<?php

class AdjController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 2));

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
        $data = Adj::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {
        $sql = "SELECT id FROM adj WHERE id = $id ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => 1
            ),
                )
        );

        $model = $this->loadModel($id);

        //LOAD EDIT VIEW


        $model = $this->loadModel($id);        
        $user_id = Yii::app()->user->getState("userid");
        if ($model->users_id == $user_id && $model->online == 2) {
            $stsValue = 3;
        } else {
            $stsValue = $model->online;
        }


        if ($stsValue == 1) {

            if ($model->data_type == "M") {
                $this->render('view', array(
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ));
                return;
            } else {
                $this->render('view_region', array(
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ));
                return;
            }
        }
        
        //LOAD APPROVAL WINDOW
        if ($stsValue == 2) {
            $this->render('approve', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
            ));
            return;
        }
        
        //LOAD PRINT ONLY VIEW WHEN COMPLATED OR REJECTED
        if ($stsValue >= 3) {
            $this->render('print', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
            ));
            return;
        }
        
    }

    public function actionCreate() {
        try {


            $device_id = $_POST['device_id'];
            //CHECK any open Stock process
            $this->checkStockAvl($device_id);


            $model = new Adj;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->eff_date = date("Y-m-d");
            $model->code = $this->returnCode("adj", "ADJ-" . $model->device->code . "-");
            $model->users_id = Yii::app()->user->getId();


            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            //LOAD Buffer Stock
            if (isset($_POST['bufer_stock'])) {


                if ($model->data_type == 'M') {
                    $q = " AND item_type = 'RM' ";
                } else {
                    $q = " AND item_type = 'FG' ";
                }


                $bufferStock = Yii::app()->db->createCommand("SELECT id,cost,mrp FROM items WHERE online = 1 $q")->queryAll();
                foreach ($bufferStock as $valueMaster) {

                    $items_id = $valueMaster['id'];
                    //CHECK QTYS BY BATCH

                    if ($model->adj_type == "NS") {
                        $key = "qty_ns";
                        $key_var = "variance_ns";
                    } else {
                        $key = "qty";
                        $key_var = "variance";
                    }

                    $stock_lot = $model->lot_no;
                    $device_id = $model->device_id;

                    if ($model->data_type == 'M') {
                        $group_q = ",cost";
                    } else {
                        $group_q = ",selling";
                    }

                    $bufferStockByBatch = Yii::app()->db->createCommand("SELECT SUM($key) as tot,items_id,batch_no,expire_date,selling,cost FROM stock WHERE stock_lot = $stock_lot AND device_id = '$device_id' AND stock.items_id = $items_id GROUP BY items_id $group_q ")->queryAll();

                    if (count($bufferStockByBatch) > 0) {
                        foreach ($bufferStockByBatch as $value) {
                            //CREATE BATCH WISE ENTRY
                            $poi = new AdjItems();
                            $items = Items::model()->findByPk($value['items_id']);


                            $poi->adj_id = $model->id;
                            $poi->items_id = $value['items_id'];

                            if (!empty($_POST['bufer_init'])) {
                                $poi->{$key} = 0;
                                $poi->{$key_var} = 0 - $value['tot'];
                            } else {
                                $poi->{$key} = $value['tot'];
                                $poi->{$key_var} = 0;
                            }

                            $poi->batch_no = $value['batch_no'];
                            $poi->expire_date = $value['expire_date'];
                            $poi->cost = $value['cost'];
                            $poi->selling = $value['selling'];

                            if (!$poi->save()) {

                                $er = $poi->getErrors();
                                $err_txt = "";
                                foreach ($er as $key => $value) {
                                    $lebel = $poi->getAttributeLabel($key);
                                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                                }
                                throw new Exception($err_txt);
                            }
                        }
                    }
                }
            }

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
        $sql = "SELECT * FROM adj WHERE online >= 1 AND data_type = 'M' AND device_id IN ($device) $searchtxt ORDER BY id DESC ";
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

    public function actionRegion() {

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
        $sql = "SELECT * FROM adj WHERE online >= 1 AND data_type = 'R' AND device_id IN ($device) $searchtxt ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => $pages
            ),
                )
        );

        $this->render('index_region', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function loadModel($id) {
        $model = Adj::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'adj-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
