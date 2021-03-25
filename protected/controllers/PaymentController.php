<?php

class PaymentController extends Controller {

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
        $data = Payment::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {
        
    }

    public function actionCreate() {
        try {

            $model = new Payment;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->code = $this->returnCode("payment", "PAY-" . $model->customers->areas->device->code . "-");
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

            $ledger = new Ledger();
            $ledger->customers_id = $model->customers_id;
            $ledger->code = $model->code;
            $ledger->invoice_id = $model->invoice_id;
            $ledger->l_type = "PAYMENT";
            $ledger->dr = abs($model->amount);
            $ledger->ref = $model->remarks;
            $ledger->users_id = $model->users_id;
            $ledger->created = date("Y-m-d H:i:s");

            if (!$ledger->save()) {

                $er = $ledger->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $ledger->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            $this->returnMsg("Successfully Updated", 0, 1);
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

            $payment = $this->loadModel($id);
            $payment->online = 2;
            $payment->save();

            $ledger = new Ledger();
            $ledger->customers_id = $payment->customers_id;
            $ledger->code = $payment->code;
            $ledger->l_type = $_POST['l_type'];
            $ledger->invoice_id = $payment->invoice_id;
            $ledger->cr = abs($payment->amount);
            $ledger->ref = $_POST['ref'];
            $ledger->users_id = Yii::app()->user->getId();
            $ledger->created = date("Y-m-d H:i:s");

            if (!$ledger->save()) {

                $er = $ledger->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $ledger->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $this->returnMsg("Successfully canceled", 1, 0);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionIndex() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND customers.name LIKE '%" . $_GET['val'] . "%' ";
        }
        
        if (empty($_GET['chq_no'])) {
            $chq_no = "";
        } else {
            $chq_no = " AND payment.cheque_no LIKE '%" . $_GET['chq_no'] . "%' ";
        }
        
        if (empty($_GET['pd'])) {
            $chq_pd = "";
        } else {
            $chq_pd = " AND payment.pd_date = '" . $_GET['pd'] . "' ";
        }
        
        if (empty($_GET['online'])) {
            $online = "";
        } else {
            $online = " AND payment.online = '" . $_GET['online'] . "' ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }


        $sql = "SELECT payment.* FROM payment,customers WHERE customers.id = payment.customers_id $searchtxt $chq_no $chq_pd $online ORDER BY payment.id DESC ";
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
        $model = Payment::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'payment-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
