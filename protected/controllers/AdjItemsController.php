<?php

class AdjItemsController extends Controller {

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
        $data = AdjItems::model()->findByPk($id);
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


            $model = AdjItems::model()->findByAttributes(array(
                'adj_id' => $_POST['adj_id'],
                'items_id' => $_POST['items_id'],
                'cost' => $_POST['cost'],
                'selling' => $_POST['selling']
            ));
            
            
            if ($model == null) {
                
                $model = new AdjItems;
                $model->attributes = $_POST;                
                
                if($model->adj->adj_type == "NS"){
                    $key = "qty_ns";
                    $var_key = "variance_ns";
                    $model->qty = 0;
                    $model->variance = 0;
                }else{
                    $key = "qty";
                    $var_key = "variance";
                }
                
                $model->{$key} = $_POST['qty'];               
                                
                $stock_lot = $model->adj->lot_no;
                $qty = Yii::app()->db->createCommand("SELECT SUM($key) as tot FROM stock WHERE items_id = '" . $model->items_id . "' AND "
                                . "device_id = '". $model->adj->device_id ."' AND cost = '". $_POST['cost'] ."' AND selling = '". $_POST['selling'] ."' AND "
                                . " stock_lot = $stock_lot ")->queryAll(); 
                
                
                $model->{$var_key} = $_POST['qty'] - $qty[0]['tot'];
                
                
            }else{                
                //throw new Exception("Already Entered to the List");                
                //RETURN ERROR VALUES
                if($model->adj->adj_type == "NS"){                    
                    $key = "qty_ns";
                    $var_key = "variance_ns";                    
                    $model->qty = 0;
                    $model->variance = 0;                    
                }else{                    
                    $key = "qty";
                    $var_key = "variance";  
                    $model->qty_ns = 0;
                    $model->variance_ns = 0; 
                } 
                
                //VARIANCE GOING TO BE CHNAGED
                $old_qty = $model->{$key};
                $old_var = $model->{$var_key}; 
                                
                $diff =  $_POST['qty'] - $old_qty;                 
                
                $model->{$key} = $_POST['qty'];                
                $model->{$var_key} = $old_var + $diff;    
                $model->selling = $model->cost;
            }               
                        

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
    
    public function actionupdateAll($id) {
        try {

            $model = Adj::model()->findByPk($id);

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
            
            
            if ($model->adj_type == 'NS') {
                $keySelector = "qty_ns";
                $keySelector_var = "variance_ns";
            } else {
                $keySelector = "qty";
                $keySelector_var = "variance";
            } 

            //Update PO Items
            foreach ($_POST[$keySelector] as $key => $value) {

                $poitems = AdjItems::model()->findByAttributes(array("id" => $key));

                //Asign Prev Values
                $old_qty = $poitems->{$keySelector};
                $old_var = $poitems->{$keySelector_var};  
                
                $diff =   $old_qty - $value;         
                $poitems->{$keySelector} = $value;
                $poitems->{$keySelector_var} = $old_var - $diff;                

                if (!$poitems->save()) {
                    $er = $poitems->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $poitems->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
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


        $sql = "SELECT * FROM adj_items WHERE online = 1 $searchtxt ORDER BY id DESC ";
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
        $model = AdjItems::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'adj-items-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
