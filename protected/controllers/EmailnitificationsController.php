<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './mails/PHPMailer.php';
require './mails/SMTP.php';
require './mails/Exception.php';

class EmailnitificationsController extends Controller {

    public function actionIndex() {
        $this->render('index');
    }

    public function loadmail() {



        try {

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'mail.prologics.lk';
            $mail->SMTPAuth = true;
            $mail->Username = 'idoc@prologics.lk';
            $mail->Password = 'X{4vZ20[}vbe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('idoc@prologics.lk', 'SFA Notifications');
            $mail->addReplyTo('idoc@prologics.lk', 'SFA Notifications');

            return $mail;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function actionTest() {
        $this->renderPartial("lastDayPerformance", array("users_id" => 1));
    }

    public function actionDailySales() {

        //LOAD ACTIVE USERS
        $list = Yii::app()->db->createCommand("SELECT id,email FROM `users` WHERE online = 1")->queryAll();
        foreach ($list as $value) {
            
            if(empty($value['email'])){
                continue;
            }
            
            $mail = $this->loadmail();
            $mail->isHTML(true);
            $mail->Subject = "Daily Sales Achievement Summery " . date("Y-m-d");

            $mail->addAddress($value['email']);
            //SEND DATA AND GET THE EMAIL BODY
            ob_start();
            $this->renderPartial("dailysales", array("users_id" => $value['id']));
            $mail->Body = ob_get_contents();
            ob_end_clean();

            $mail->send();
        }
    }

    public function actionCheques() {

        $mail = $this->loadmail();
        //LOAD ACTIVE USERS
        $list = Yii::app()->db->createCommand("SELECT id FROM `users` WHERE id = 1 ")->queryAll();
        foreach ($list as $value) {
            
            
            if(empty($value['email'])){
                continue;
            }

            $device_ids = $this->returnDeviceByUsersId($value['id']);
            $count = Yii::app()->db->createCommand("SELECT id FROM payment WHERE pd_date = '" . date("Y-m-d") . "' AND device_id in ($device_ids) AND online = 1 AND pay_type = 'BANK' ")->query()->count();

            //SEND MAIL IF AVILABLE PD CHEQUES;
            if ($count > 0) {
                $mail->addAddress("chatura@prologics.lk");
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "iForce Reminder : You have cheques to deposit: Date-" . date("Y-m-d");

                //SEND DATA AND GET THE EMAIL BODY
                ob_start();
                $this->renderPartial("dailycheques", array("users_id" => $value['id']));
                $mail->Body = ob_get_contents();
                ob_end_clean();

                //$mail->send();
            }
        }
    }

    public function actionLastDayDetails() {

        //LOAD ACTIVE USERS
        $list = Yii::app()->db->createCommand("SELECT id,email FROM `users` WHERE online = 1 AND ulevel = 10 ")->queryAll();
        foreach ($list as $value) {
            
            
            if(empty($value['email'])){
                continue;
            }
            
            
            $mail = $this->loadmail();
            $mail->isHTML(true);
            $mail->Subject = "Last Day Field Sales Performance " . date("Y-m-d");

            $mail->addAddress($value['email']);
            //SEND DATA AND GET THE EMAIL BODY
            ob_start();
            $this->renderPartial("lastDayPerformance", array("users_id" => $value['id']));
            $mail->Body = ob_get_contents();
            ob_end_clean();

            $mail->send();
        }
    }
    
    
    public function actionSlowMoving() {

        //LOAD ACTIVE USERS
        $list = Yii::app()->db->createCommand("SELECT id,email FROM `users` WHERE online = 1 ")->queryAll();
        foreach ($list as $value) {
            
            
            if(empty($value['email'])){
                continue;
            }
            
            $mail = $this->loadmail();
            $mail->isHTML(true);
            $mail->Subject = "No Moving Items Update: Weekly Update up-to " . date("Y-m-d");

            $mail->addAddress($value['email']);
            //SEND DATA AND GET THE EMAIL BODY
            ob_start();
            $this->renderPartial("nomoving", array("users_id" => $value['id']));
            $mail->Body = ob_get_contents();
            ob_end_clean();

            $mail->send();
        }
    }
    
    
    public function actionLastWeekSalesReps() {

        //LOAD ACTIVE USERS
        $list = Yii::app()->db->createCommand("SELECT id,email FROM `users` WHERE online = 1 ")->queryAll();
        foreach ($list as $value) {
            
            
            if(empty($value['email'])){
                continue;
            }
            
            $mail = $this->loadmail();
            $mail->isHTML(true);
            $mail->Subject = "Target Achivement Analysis : Weekly Update up-to " . date("Y-m-d");

            $mail->addAddress($value['email']);
            //SEND DATA AND GET THE EMAIL BODY
            ob_start();
            $this->renderPartial("salesrepweek", array("users_id" => $value['id']));
            $mail->Body = ob_get_contents();
            ob_end_clean();

            $mail->send();
        }
    }
    

}
