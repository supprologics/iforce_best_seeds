<?php

class TnItemsController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 6));

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
        $data = TnItems::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionCreate() {
        try {

            $model = new TnItems;

            $model->attributes = $_POST;
            $model->qty = $_POST['qty'];
            $model->expire_date = !empty($_POST['expire_date']) ? $_POST['expire_date'] : '';
            $model->batch_no = !empty($_POST['batch_no']) ? $_POST['batch_no'] : '';
            $model->cost = $model->selling;

            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $this->bomUpdate($model->id);

            $this->returnMsg("Successfully Updated", 1, 0);
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

    public function bomUpdate($id) {
        try {

            //LOAD ENTRY
            $tnItems = TnItems::model()->findByPk($id);

            //GET THE RM STOCK ID
            $devices_id = Param::model()->findByPk(2)->val;

            //LOAD RM COSTING SHEET
            $costing = Yii::app()->db->createCommand("SELECT * FROM costing WHERE items_id = '" . $tnItems->items_id . "'")->queryAll();
            foreach ($costing as $value) {

                $bom = Bom::model()->findByAttributes(array("tn_items_id" => $id, "items_id" => $value['rm_id']));
                if ($bom == null) {
                    $bom = new Bom();
                }

                $bom->tn_items_id = $id;
                $bom->items_id = $value['rm_id'];
                $bom->cost = $bom->items->cost;
                $bom->batch_no = '';
                $bom->expire_date = '';

                if ($value['is_ceil'] == 1) {
                    $bom->qty = ceil($value['qty'] * $tnItems->qty);
                } else {
                    $bom->qty = $value['qty'] * $tnItems->qty;
                }

                if (!$bom->save()) {

                    $er = $bom->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $bom->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function actionupdateAll($id) {
        try {

            $model = Tn::model()->findByPk($id);

            $model->eff_date = $_POST['eff_date'];
            $model->remarks = $_POST['remarks'];


            if (!$model->save()) {
                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            //Update PO Items
            $erros = array();
            foreach ($_POST['qty'] as $key => $value) {

                $poitems = TnItems::model()->findByAttributes(array("id" => $key));
                $poitems->qty = $value;
                $poitems->batch_no = $_POST["batch_no"][$key];
                $poitems->expire_date = $_POST["expire_date"][$key];
               

                if (!$poitems->save()) {
                    $er = $poitems->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $poitems->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
                
                $this->bomUpdate($key);
            }

            $this->returnMsg("Successfully Updated", 1, 0);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionDelete($id) {
        try {

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


        $sql = "SELECT * FROM tn_items WHERE online = 1 $searchtxt ORDER BY id DESC ";
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
        $model = TnItems::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'tn-items-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
