<?php

class ItemsController extends Controller {

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
        $access = Useraccess::model()->findByAttributes(array('users_id' => $user_id, 'access_id' => 9));

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

    public function actionloadlist($id) {
        

        $term = $_GET['term'];
        $list = Yii::app()->db->createCommand("SELECT id FROM items WHERE item_type = 'FG' AND ( code LIKE '%$term%' OR item_name LIKE '%$term%' OR des LIKE '%$term%' ) ORDER BY code ASC ")->queryAll();

        foreach ($list as $value) {

            $sku = Items::model()->findByPk($value['id']);
            $items_id = $sku->id;
            
            $avlstock = Yii::app()->db->createCommand("SELECT SUM(qty) as tot FROM stock WHERE device_id = $id AND items_id = '$items_id' ")->queryRow();
            if(empty($avlstock['tot'])){
                $avl = 0;
            }else{
                $avl = $avlstock['tot'];
            }
            
            $json[] = array(
                'id' => $sku->id,
                'code' => $sku->code,
                'value' => $sku->item_name,
                'description' => $sku->des,
                'item_name' => $sku->item_name,
                'mrp' => $sku->mrp,
                'distval' => $sku->discount,
                'cost' => $sku->cost,
                'avl' => $avl
            );
        }
        echo json_encode($json);
    }
    
    public function actionloadlistForAdj($id) {

        $adj = Adj::model()->findByPk($id);
        $term = $_GET['term'];
        
        if($adj->data_type == 'M'){
            $q = " item_type = 'RM' AND ";
        }else{
            $q = " item_type = 'FG' AND ";
        }
        
        $list = Yii::app()->db->createCommand("SELECT id FROM items WHERE $q ( code LIKE '%$term%' OR item_name LIKE '%$term%' OR des LIKE '%$term%' )  ORDER BY code ASC ")->queryAll();

        foreach ($list as $value) {

            $sku = Items::model()->findByPk($value['id']);
            $json[] = array(
                'id' => $sku->id,
                'value' => $sku->code,
                'description' => $sku->des,
                'item_name' => $sku->item_name,
                'mrp' => $sku->mrp,
                'cost' => $sku->cost
            );
        }
        echo json_encode($json);
    }

    public function actionloadlistForSr($id) {

        $term = $_GET['term'];
        $sr = Sr::model()->findByPk($id);

        $having = " HAVING s > 0 ";
        $key = "s";
        if ($sr->sr_type == 'EX') {
            $ex = " AND expire_date <= '" . date("Y-m-d") . "' AND expire_date != 0 ";
        } else {
            $ex = "";
        }

        if ($sr->sr_type == 'NS') {
            $key = "ns";
            $having = " HAVING ns > 0 ";
        }


        $device_id = $sr->device_id;
        $stock_lot = 1;

        
        
        

        $list = Yii::app()->db->createCommand("SELECT items_id,stock.cost,batch_no,expire_date,SUM(qty) as s,SUM(qty_ns) AS ns FROM stock,items WHERE "
                        . "items.id = stock.items_id AND stock.suppliers_id = '". $sr->suppliers_id ."' AND "
                        . " stock_lot = 1 AND device_id = '$device_id' AND stock_lot = '$stock_lot' AND "
                        . "( items.code LIKE '%" . $term . "%' OR items.item_name LIKE '%" . $term . "%' OR items.des LIKE '%" . $term . "%' ) $ex  "
                        . "GROUP BY items_id,stock.cost,stock.batch_no,stock.expire_date $having ORDER BY code,item_name ASC ")->queryAll();


        foreach ($list as $value) {

            $sku = Items::model()->findByPk($value['items_id']);
            $json[] = array(
                'id' => $sku->id,
                'value' => $sku->code,
                'description' => $sku->item_name,
                'batch_no' => $value['batch_no'],
                'expire_date' => $value['expire_date'],
                'cost' => $value['cost'],
                'qty' => $value[$key],
            );
        }
        echo json_encode($json);
    }
    
    
    public function actionloadlistForTn($id) {

        $term = $_GET['term'];
        $list = Yii::app()->db->createCommand("SELECT id FROM items WHERE  item_type = 'FG' AND (items.code LIKE '%" . $term . "%' OR items.item_name LIKE '%" . $term . "%' OR items.des LIKE '%" . $term . "%' )ORDER BY code,item_name ASC ")->queryAll();
        foreach ($list as $value) {

            $sku = Items::model()->findByPk($value['id']);
            $json[] = array(
                'id' => $sku->id,
                'value' => $sku->code,
                'description' => $sku->des,
                'selling' => $sku->mrp,
            );
        }
        echo json_encode($json);
    }
    
    public function actionloadlistForLtn($id) {

        $term = $_GET['term'];
        $sr = Ltn::model()->findByPk($id);

        if ($sr->ltn_type == 'NS') {
            $key = "qty_ns";
        } else {
            $key = "qty";
        }
        
        $device_id = $sr->device_from;        
        
        $list = Yii::app()->db->createCommand("SELECT items_id,stock.selling,batch_no,expire_date,SUM($key) as tot FROM stock,items WHERE items.id = stock.items_id AND "
                        . " stock_lot = 1 AND device_id = '$device_id' AND ( items.code LIKE '%" . $term . "%' OR items.item_name LIKE '%" . $term . "%' OR items.des LIKE '%" . $term . "%' ) GROUP BY stock.items_id,stock.selling HAVING tot > 0 ORDER BY code,item_name ASC ")->queryAll();
        
        
        foreach ($list as $value) {

            $sku = Items::model()->findByPk($value['items_id']);
            $json[] = array(
                'id' => $sku->id,
                'value' => $sku->code,
                'description' => $sku->des,
                'selling' => $value['selling'],
                'qty' => $value['tot'],
            );
        }
        
        echo json_encode($json);
    }

    public function actionjsondata($id) {
        $data = Items::model()->findByPk($id);
        $output = CJSON::encode($data);
        echo $output;
    }

    public function actionView($id) {


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


        $sql = "SELECT * FROM costing WHERE items_id = $id $searchtxt ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => $pages
            ),
                )
        );


        $this->render('view', array(
            'model' => $this->loadModel($id),
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionCreate() {
        try {

            $model = new Items;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");



            //MANAGE DISCOUNT
            if (!empty($model->discount)) {
                if ($model->discount_type == 1) {
                    $model->discount_amount = round($model->mrp * $model->discount / 100, 2);
                } else {
                    $model->discount_amount = $model->discount;
                }
            } else {
                $model->discount_amount = 0;
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

    public function actionUpdate($id) {
        try {

            $model = $this->loadModel($id);

            $model->attributes = $_POST;

            //MANAGE DISCOUNT
            if (!empty($model->discount)) {
                if ($model->discount_type == 1) {
                    $model->discount_amount = round($model->mrp * $model->discount / 100, 2);
                } else {
                    $model->discount_amount = $model->discount;
                }
            } else {
                $model->discount_amount = 0;
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

    public function actionDelete($id) {
        try {
            if ($this->loadModel($id)->delete()) {
                $this->returnMsg("Successfully Deleted", 1, 0);
            } else {
                $this->returnMsg("Error Occured", 0, 1);
            }
        } catch (CDbException $exc) {
            $this->returnMsg("Invalid Action! cant delete this record", 0, 1);
        } catch (Exception $exc) {
            $this->returnMsg($exc->getMessage(), 0, 1);
        }
    }

    public function actionIndex() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND ( item_name LIKE '%" . $_GET['val'] . "%' OR des LIKE '%" . $_GET['val'] . "%' OR code LIKE '%" . $_GET['val'] . "%' )";
        }

        if (!empty($_GET['brands_id'])) {
            $brands_id = " AND brands_id = '" . $_GET['brands_id'] . "' ";
        } else {
            $brands_id = " ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }


        $sql = "SELECT * FROM items WHERE  item_type = 'FG' $brands_id $searchtxt ORDER BY id DESC ";
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

    public function actionRm() {

        //Handle Search Values
        if (empty($_GET['val'])) {
            $searchtxt = "";
        } else {
            $searchtxt = " AND ( item_name LIKE '%" . $_GET['val'] . "%' OR des LIKE '%" . $_GET['val'] . "%' OR code LIKE '%" . $_GET['val'] . "%' )";
        }

        if (isset($_GET['search_online'])) {
            $online = " AND online = '" . $_GET['search_online'] . "' ";
        } else {
            $online = " AND online = '1' ";
        }

        if (empty($_GET['pages'])) {
            $pages = 50;
        } else {
            $pages = $_GET['pages'];
        }


        $sql = "SELECT * FROM items WHERE item_type = 'RM' $online $searchtxt ORDER BY id DESC ";
        $count = Yii::app()->db->createCommand($sql)->query()->rowCount;
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'pagination' => array(
                'pageSize' => $pages
            ),
                )
        );

        $this->render('index_rm', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function loadModel($id) {
        $model = Items::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'items-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    
    public function actionSubCategory($id) {

        $list = Yii::app()->db->createCommand("SELECT id,name FROM sub_categories WHERE  brands_id = $id AND is_dashboard = '1' ")->queryAll();
        
        $i=0;
        $json[]='';
        foreach ($list as $value) {
            $sub_cat = SubCategory::model()->findByPk($value['id']);

            $json[$i]['id']=$sub_cat->id;
            $json[$i]['name']=$sub_cat->name;
            $i++;
        }
        

        echo json_encode($json);
    }

}
