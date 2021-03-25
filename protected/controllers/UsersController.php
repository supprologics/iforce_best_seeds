<?php

class UsersController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 7));

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
    
    public function actionpsw($id) {
        try {

            $model = $this->loadModel($id);

            
            $model->password = md5($_POST['password']);

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

    function returncheckBoxStatus($val) {
        return !empty($val) ? TRUE : FALSE;
    }

    public function actionjsondata($id) {
        $data = Users::model()->findByPk($id);
        $dataArray = $data->attributes;

        $list = Useraccess::model()->findAllByAttributes(array("users_id" => $id));
        foreach ($list as $value) {
            $dataArray['view_' . $value->access_id] = $this->returncheckBoxStatus($value->view_);
            $dataArray['update_' . $value->access_id] = $this->returncheckBoxStatus($value->update_);
            $dataArray['create_' . $value->access_id] = $this->returncheckBoxStatus($value->create_);
            $dataArray['delete_' . $value->access_id] = $this->returncheckBoxStatus($value->delete_);
            $dataArray['approve_' . $value->access_id] = $this->returncheckBoxStatus($value->approve_);
        }
        
        $list = UsersDevices::model()->findAllByAttributes(array("users_id" => $id,'online' => 1));
        foreach ($list as $value) {
            $dataArray['device_id_' . $value->device_id] = $this->returncheckBoxStatus($value->online);
        }
        
        $output = CJSON::encode($dataArray);
        echo $output;
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionCreate() {
        try {

            $model = new Users;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->password = md5($model->password);

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

            if (!$model->save()) {

                $er = $model->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $model->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }


            //UPDATE USER ACCESS;
            foreach ($_POST['access_id'] as $value) {
                $ua = Useraccess::model()->findByAttributes(array("users_id" => $id, "access_id" => $value));
                if ($ua == null) {
                    $ua = new Useraccess();
                    $ua->users_id = $id;
                    $ua->access_id = $value;
                }
                $ua->view_ = isset($_POST['view'][$value]) ? 1 : 0;
                $ua->update_ = isset($_POST['update'][$value]) ? 1 : 0;
                $ua->create_ = isset($_POST['create'][$value]) ? 1 : 0;
                $ua->delete_ = isset($_POST['delete'][$value]) ? 1 : 0;
                $ua->approve_ = isset($_POST['approve'][$value]) ? 1 : 0;
                if (!$ua->save()) {

                    $er = $ua->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $ua->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
            }

            //UPDATE LOCATIONS
            Yii::app()->db->createCommand("UPDATE users_devices SET online = 0 WHERE users_id = '$id'")->execute();
            foreach ($_POST['device_id'] as $key => $value) {
                $ua = UsersDevices::model()->findByAttributes(array("users_id" => $id, "device_id" => $key));
                if ($ua == null) {
                    $ua = new UsersDevices();
                    $ua->users_id = $id;
                    $ua->device_id = $key;
                }

                $ua->online = isset($_POST['device_id'][$key]) ? 1 : 0;
                if (!$ua->save()) {

                    $er = $ua->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $ua->getAttributeLabel($key);
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


        $sql = "SELECT * FROM users WHERE online = 1 $searchtxt ORDER BY id DESC ";
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
        $model = Users::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'users-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
