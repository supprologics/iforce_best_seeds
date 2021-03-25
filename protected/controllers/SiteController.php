<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actiontargetview($id) {
        $this->renderPartial("dashboard", array('id' => $id));
    }

    public function actionsms() {


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://richcommunication.dialog.lk/api/sms/inline/send.php?destination=777553808&q=15796085623551&message=TEST+OK+ENGLISH+සිංහල+டமில்");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
    }

    public function actionCosting() {
        //AND item_name LIKE '% 100g%'
        $list = Yii::app()->db->createCommand("SELECt * FROM items WHERE brands_id IN ( 3,4)  ")->queryAll();

        $num = 1;
        foreach ($list as $value) {


            //$cnt = strlen($value['item_name']);
            //$nCnt = intval($cnt - 6);
            //$rm = Yii::app()->db->createCommand("SELECT item_name,id FROM items WHERE item_type = 'RM' AND suppliers_id = '10' AND item_name LIKE '%" . substr($value['item_name'], 0, $nCnt) . "%' LIMIT 1")->queryRow();

            //if (!empty($rm['id'])) {
                echo $num ." ".$value['item_name'] ."<br/>";
                //echo $num . " " . $value['item_name'] . " -  " . $rm['item_name'] . " $cnt " . substr($value['item_name'], 0, $nCnt) . "<br/>";

                $cost1 = new Costing();
                $cost1->items_id = $value['id'];
                $cost1->rm_id = $value['id'];
                $cost1->qty = 1;
                $cost1->is_ceil = 1;
                //$cost1->save();     
                $num += 1;
            //}
        }
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        if (!Yii::app()->user->isGuest) {
            $this->render('index');
        } else {
            $this->redirect('site/login');
        }
    }

    public function actiontestIds() {
        $list = Yii::app()->db->createCommand("SELECT MAX(id) as id,invoice_code,customers_id,items_id,total,item_type,eff_date,device_id,COUNT(id) as cnt FROM `invoice_items` WHERE eff_date >= '2020-06-01' GROUP BY invoice_item_id,invoice_code,customers_id,items_id,total,item_type,device_id HAVING cnt > 1")->queryAll();
        //echo "<table>";
        foreach ($list as $value) {

            echo $value['id'] . ",";
        }
        //echo "</table>";
    }

    public function actionareaslist($id) {
        echo "<option value=''>Select ALL</option>";
        $list = Yii::app()->db->createCommand("SELECT * FROM areas WHERE device_id = '$id'")->queryAll();
        foreach ($list as $value) {
            echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
        }
    }

    function actionpogenerate() {
        $list = Yii::app()->db->createCommand("SELECT * FROM device WHERE po_date = '" . date("w") . "'")->queryAll();
        foreach ($list as $value) {


            //CHECK FOR OPEN DOCS
            $device_id = $value['id'];
            $ts = date("Y-m-d H:i:s");

            $pos = Yii::app()->db->createCommand("UPDATE po SET online = 3,remarks = CONCAT( remarks,' [ Closed By System @ $ts ] ') WHERE device_id = '$device_id' AND online < 3")->execute();
            $grn = Yii::app()->db->createCommand("UPDATE grn SET online = 3,remarks = CONCAT( remarks,' [ Closed By System @ $ts ] ') WHERE device_id = '$device_id' AND online < 3")->execute();
            $sr = Yii::app()->db->createCommand("UPDATE sr SET online = 3,remarks = CONCAT( remarks,' [ Closed By System @ $ts ] ') WHERE device_id = '$device_id' AND online < 3")->execute();
            $adju = Yii::app()->db->createCommand("UPDATE adj SET online = 3,remarks = CONCAT( remarks,' [ Closed By System @ $ts ] ') WHERE device_id = '$device_id' AND online < 3")->execute();

            $model = new Po;
            $model->device_id = $device_id;
            $model->eff_date = date("Y-m-d");
            $model->created = date("Y-m-d H:i:s");
            $model->code = $this->returnCode("po", "PO-" . $model->device->code . "-");
            $model->users_id = 1;

            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
            }

            //LOAD Buffer Stock
            $bufferStock = Yii::app()->db->createCommand("SELECT * FROM bufer_stock WHERE device_id = '" . $model->device_id . "' AND qty > 0")->queryAll();
            foreach ($bufferStock as $value) {

                $item = Items::model()->findByPk($value['items_id']);
                $stocqty = Yii::app()->db->createCommand("SELECT SUM(qty) as tot FROM stock WHERE items_id = '" . $value['items_id'] . "' ")->queryAll();
                if ($stocqty[0]['tot'] >= $value['qty']) {
                    continue;
                }
                $bal = $value['qty'] - $stocqty[0]['tot'];

                $poi = new PoItems();
                $poi->po_id = $model->id;
                $poi->items_id = $value['items_id'];
                $poi->qty = $bal;
                $poi->selling = $item->mrp;

                if (!$poi->save()) {

                    $er = $poi->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $poi->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                }
            }
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                        "Reply-To: {$model->email}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-Type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {

        $this->layout = "//layouts/login";
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}
