<?php

class CustomersController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 10));

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
    
    public function actionloadlistNames() {

        $term = $_GET['term'];
        $list = Yii::app()->db->createCommand("SELECT id FROM customers WHERE ( "
                . "code LIKE '%$term%' OR "
                . "name LIKE '%$term%' OR "
                . "mobile LIKE '%$term%' OR "
                . "nic LIKE '%$term%' "
                . ") ORDER BY code ASC ")->queryAll();
        
        foreach ($list as $value) {
            $data = Customers::model()->findByPk($value['id']);        
            $json[] = array(
                'id' => $data->id,
                'value' => $data->code,
                'name' => $data->name,
            );
        }
        echo json_encode($json);
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
        $data = Customers::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionloadlist($id) {
        $term = $_GET['term'];
        $list = Yii::app()->db->createCommand("SELECT customers.id,invoice.id as invid,invoice.code,invoice.invoice_total,inv_due(invoice.id) as balance FROM customers,areas,invoice WHERE invoice.customers_id = customers.id AND areas.id = customers.areas_id AND areas.device_id = '$id' AND ( customers.code LIKE '%$term%' OR customers.name LIKE '%$term%' ) GROUP BY invoice.code HAVING balance > 0 ORDER BY customers.name ASC LIMIT 25 ")->queryAll();
        
        foreach ($list as $value) {

            $sku = Customers::model()->findByPk($value['id']);            
            $json[] = array(
                'id' => $sku->id,
                'value' => $sku->name,
                'name' => $sku->code,
                'inv_id' => $value['invid'],
                'code' => $value['code'],
                'due_val' => round($value['balance'],2),
                'due' => number_format($value['balance'],2)
            );
        }
        echo json_encode($json);
    }    
    

    public function actionCreate() {
        try {

            $model = new Customers;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->code = $this->returnCode("customers", "C");
            
            $model->latitude = round($_POST['latitude'],6);
            $model->longitude = round($_POST['longitude'],6);
            $model->synced = $model->created;


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

    public function actionUpdate($id) {
        try {

            $model = $this->loadModel($id);

            $model->attributes = $_POST;
            $model->latitude = round($model->latitude, 6);
            $model->longitude = round($model->longitude, 6);

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

        if (empty($_GET['ctype'])) {
            $ctype = "";
        } else {
            $ctype = " AND customer_types_id = '" . $_GET['ctype'] . "' ";
        }

        if (empty($_GET['device_id'])) {
            $device_id = "";
        } else {
            $device_id = " AND areas.device_id = '" . $_GET['device_id'] . "' ";
        }

        $device = $this->returnDevice();

        $sql = "SELECT customers.* FROM customers,areas "
                . "WHERE customers.areas_id = areas.id AND "
                . "customers.online = 1 AND areas.device_id IN ($device) $device_id $searchtxt $ctype "
                . "ORDER BY customers.id DESC ";

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
        $model = Customers::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'customers-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
