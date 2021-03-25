<?php

class GrnController extends Controller {

    public $layout = '//layouts/default';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl - login', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        $deny = array();
        $accessArray = array("all");
        $denyarray = array();


        $user_id = Yii::app()->user->getState("userid");
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 5));

        if (isset($access)) {

            if ($access->view_ == 1) {
                $accessArray[] = 'index';
                $accessArray[] = 'view';
            }

            if ($access->create_ == 1) {
                $accessArray[] = 'create';
            }

            if ($access->update_ == 1) {
                $accessArray[] = 'update';
            }

            if ($access->delete_ == 1) {
                $accessArray[] = 'delete';
            }


            $access = array('allow',
                'actions' => $accessArray,
                'users' => array('@')
            );
        }

        return array(
            $access,
            array('deny',
                'actions' => array('create', 'update', 'delete', 'index', 'view'),
                'users' => array('*')
            )
        );
    }

    public function actionPrint($id) {
        $this->renderPartial('print', array(
            'model' => $this->loadModel($id)
        ));
    }

    public function returnMsg($msg, $status, $hide = 0, $id = "") {
        $result = array(
            'msg' => $msg,
            'sts' => $status,
            'hide' => $hide,
            'id' => $id
        );
        echo json_encode($result);
    }

    public function actionjsondata($id) {
        $data = Grn::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {

        $sql = "SELECT id FROM grn WHERE id = $id ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => 1
            ),
                )
        );

        $model = $this->loadModel($id);
        $model = $this->loadModel($id);
        $this->render('view', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionCreate() {
        try {

            //CHECK FOR OPEN DOCS
            //Load PO
            $po = Po::model()->findByPk($_POST['po_id']);

            $device_id = $po->device_id;
            //CHECK any open Stock process
            $this->checkStockAvl($device_id);

            $model = new Grn;

            $model->device_id = $po->device_id;
            $model->po_id = $po->id;
            $model->code = $this->returnCode("grn", "GRN-" . $po->device->code . "-");
            $model->eff_date = date("Y-m-d");
            $model->created = date("Y-m-d H:i:s");
            $model->users_id = Yii::app()->user->getId();
            $model->suppliers_id = $po->suppliers_id;


            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            //Update Items
            foreach ($po->poItems as $value) {

                $poQty = $value->qty;
                $alreadyPoQty = Yii::app()->db->createCommand("SELECT SUM(qty) AS tot FROM grn_items WHERE po_items_id = '" . $value->id . "'")->queryRow();

                if ($alreadyPoQty['tot'] >= $poQty) {
                    $avlqty = 0;
                } else {
                    $avlqty = $poQty - $alreadyPoQty['tot'];
                }

                if ($avlqty > 0) {

                    $items = new GrnItems();
                    $items->grn_id = $model->id;
                    $items->po_items_id = $value->id;
                    $items->items_id = $value->items_id;
                    $items->qty = $avlqty;
                    $items->selling = $value->selling;
                    $items->cost = $value->cost;
                    $items->expire_date = "";


                    if (!$items->save()) {

                        $er = $items->getErrors();
                        $err_txt = "";
                        foreach ($er as $key => $value) {
                            $lebel = $items->getAttributeLabel($key);
                            $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                        }
                        throw new Exception($err_txt);
                    }
                }
            }

            $po->online = 3;
            $po->save();

            $this->returnMsg("Successfully Updated", 1, 0, $model->id);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionUpdate($id) {
        try {

            $model = $this->loadModel($id);

            $model->attributes = $_POST;

            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            $this->returnMsg("Successfully Updated", 1, 0);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionDelete($id) {
        try {

            Yii::app()->db->createCommand("DELETE FROM grn_items WHERE grn_id = '$id'")->execute();

            if ($this->loadModel($id)->delete()) {
                $this->returnMsg("Successfully Deleted", 1, 0);
            } else {
                $this->returnMsg("Error Occured", 0, 1);
            }
        } catch (CDbException $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionIndex() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND name LIKE '%" . $_GET['val'] . "%' ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }


        $device = $this->returnDevice();

        $sql = "SELECT * FROM grn WHERE online >= 1 AND device_id IN ($device) $searchtxt ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => $pages
            ),
                )
        );

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function loadModel($id) {
        $model = Grn::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'grn-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
