<?php

class ReportsController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 11));

        if (isset($access)) {

            if ($access->view_ == 1) {
                $accessArray[] = 'index';
                $accessArray[] = 'view';
                $accessArray[] = 'print';
            }

            if ($access->create_ == 1) {
                $accessArray[] = 'create';
                $accessArray[] = 'createall';
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
                'actions' => array('create', 'createall', 'update', 'delete', 'index', 'view'),
                'users' => array('*')
            )
        );
    }

    public function actionLoadui() {
        $page = $_POST['report'];
        $this->renderPartial($page . "/ui", array(
            'report' => $page,
            'title' => $_POST['title']
        ));
    }

    public function actionloadreport() {
        $report = $_POST['report'];
        $this->renderPartial($report . "/report", array(
            'post' => $_POST
        ));
    }

    public function actionIndex() {
        $this->render('index');
    }
    
    public function actioninventory() {
        $this->render('inventory');
    }
    
    public function actioncustomer() {
        $this->render('customer');
    }

}
