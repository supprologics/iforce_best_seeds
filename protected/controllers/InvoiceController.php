<?php

class InvoiceController extends Controller {

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
        $data = Invoice::model()->findByPk($id);

        $dataArray = $data->attributes;
        $dataArray['eff_date'] = date("Y-m-d", strtotime($data->eff_date));
        $output = CJSON::encode($dataArray);


        echo $output;
    }

    public function actionInvConfirm($id) {
        try {

            $model = $this->loadModel($id);
            $model->online = $_POST['online'];


            $invcode = $model->code;
            $cus_id = $model->customers_id;
            $device_id = $model->device_id;

            //CAL totals
            $sales = Yii::app()->db->createCommand("SELECT SUM(total) as tot,SUM(mrp * qty_selable) as nett, SUM(discount_amount) as distotal from invoice_items WHERE invoice_code = '$invcode' AND customers_id = '$cus_id' AND device_id = '$device_id' AND item_type = 1 ")->queryRow();
            $retuens = Yii::app()->db->createCommand("SELECT SUM(total) as tot,SUM(mrp * qty_selable) as nett, SUM(discount_amount) as distotal from invoice_items WHERE invoice_code = '$invcode' AND customers_id = '$cus_id' AND device_id = '$device_id' AND item_type = 2 ")->queryRow();

            $model->invoice_discount_total = abs($sales['distotal'] - $retuens['distotal']);
            $model->invoice_discount = abs($sales['distotal'] - $retuens['distotal']);
            $model->invoice_total = $sales['tot'] - $retuens['tot'];
            $model->invoice_return_total = $retuens['tot'];
            $model->invoice_net_total = $sales['nett'] + $retuens['nett'];


            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }
            
            
            //UPDATE LEDGER
            
            $ledger = new Ledger();
            $ledger->customers_id = $model->customers_id;
            $ledger->code = $model->code;
            
            
            if($model->invoice_total >= 0){
                $ledger->l_type = "INVOICE";
                $ledger->cr = abs($model->invoice_total);
            }else{
                $ledger->l_type = "CREDIT";
                $ledger->dr = abs($model->invoice_total);
            }
            
            $ledger->ref = "INVOICE ENTRY";           
            
            $ledger->created = date("Y-m-d H:i:s");
            $ledger->invoice_id = $model->id;

            if (!$ledger->save()) {

                $er = $ledger->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $ledger->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            $this->returnMsg("Successfully Updated", 1, 0);
        } catch (Exception $ex) {
            $this->returnMsg($ex->getMessage(), 0, 1);
        }
    }

    public function actionPrint($id) {
        $this->renderPartial('print', array(
            'model' => $this->loadModel($id)
        ));
    }

    public function actionView($id) {


        $sql = "SELECT id FROM invoice WHERE id = $id ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => 1
            ),
                )
        );

        $model = $this->loadModel($id);
        if ($model->pay_type == "1") {
            $this->render('view', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
            ));
        } else {
            $this->render('view_credit', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
            ));
        }
    }

    public function actionCreate() {
        try {

            $model = new Invoice;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->code = $this->returnInvCode();
            $model->sync_time = $model->created;

            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
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

            $inv = Invoice::model()->findByPk($id);

            $inv_cod = $inv->code;
            $cus_id = $inv->customers_id;
            $dev_id = $inv->device_id;

            Yii::app()->db->createCommand("DELETE FROM invoice_items WHERE invoice_code = '$inv_cod' AND customers_id = '$cus_id' AND device_id = '$dev_id' ")->execute();
            Yii::app()->db->createCommand("DELETE FROM ledger WHERE code = '$inv_cod' AND customers_id = '$cus_id' AND l_type = 'INVOICE' ")->execute();

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

    public function actionCredit() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND ( invoice.code LIKE '%" . $_GET['val'] . "%' OR invoice.bill_bookcode LIKE '" . $_GET['val'] . "%' ) ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }

        if (empty($_GET['device_id'])) {
            $device_id = "";
        } else {
            $device_id = " AND invoice.device_id = '" . $_GET['device_id'] . "' ";
        }

        if (empty($_GET['cdate'])) {
            $cdate = "";
        } else {
            $cdate = " AND DATE(eff_date) = '" . $_GET['cdate'] . "' ";
        }

        $device = $this->returnDevice();

        $sql = "SELECT invoice.* FROM invoice,customers WHERE invoice.device_id IN ($device) AND customers.id = invoice.customers_id AND pay_type = 2 $searchtxt $device_id $cdate ORDER BY invoice.id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => $pages
            ),
                )
        );

        $this->render('index_credit', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionIndex() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND ( invoice.code LIKE '%" . $_GET['val'] . "%' OR invoice.bill_bookcode LIKE '" . $_GET['val'] . "%' ) ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }
        

        if (empty($_GET['device_id'])) {
            $device_id = "";
        } else {
            $device_id = " AND invoice.device_id = '" . $_GET['device_id'] . "' ";
        }

        if (empty($_GET['cdate'])) {
            $cdate = "";
        } else {
            $cdate = " AND DATE(eff_date) = '" . $_GET['cdate'] . "' ";
        }

        $device = $this->returnDevice();

        $sql = "SELECT invoice.* FROM invoice,customers WHERE invoice.device_id IN ($device) AND customers.id = invoice.customers_id AND pay_type = 1 $searchtxt $device_id $cdate ORDER BY invoice.eff_date DESC,invoice.bill_bookcode DESC ";
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
        $model = Invoice::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'invoice-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
