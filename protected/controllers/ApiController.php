<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class ApiController extends Controller {

    public function actionIndex() {
        $this->render('index');
    }

    public function returnJson($data, $message, $success) {
        $json = array(
            "data" => $data,
            "message" => $message,
            "success" => $success
        );
        http_response_code(200);
        echo json_encode($json);
    }
    
    
    

    public function actionchkversion() {
        $result = array(
            "cur_version" => 10,
            "package" => 'com.prologics.ewm'
        );
        $this->returnJson($result, "OK", true);
    }

    public function actionSyncAll() {
        try {

            $device_id = $_POST['device_id'];
            $date_from = $_POST['date_from'];
            $date_to = $_POST['date_to'];

            $list = Yii::app()->db->createCommand("SELECT * FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) >= '$date_from' AND DATE(eff_date) <= '$date_to' ")->queryAll();


            $result = array();
            $num = 0;
            foreach ($list as $value) {
                $result[$num] = $value;

                $invoice_code = $value['code'];
                $customers_id = $value['customers_id'];
                $device_id = $value['device_id'];

                //Asign Customer Name  & Route ID
                $customers = Customers::model()->findByPk($customers_id);
                $result[$num]['customer_name'] = $customers->name;
                $result[$num]['areas_id'] = $customers->areas_id;

                $invoice_items = Yii::app()->db->createCommand("SELECT * FROM invoice_items WHERE  invoice_code = '$invoice_code' AND customers_id = '$customers_id' AND device_id = '$device_id' ")->queryAll();

                $invoiceItems = array();
                foreach ($invoice_items as $valueItems) {
                    $invoiceItems[] = $valueItems;
                }
                $result[$num]['invoice_items'] = $invoiceItems;

                $num += 1;
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionSyncCustomer() {
        try {

            $device_id = $_POST['device_id'];
            $customers_id = $_POST['customers_id'];

            $list = Yii::app()->db->createCommand("SELECT * FROM invoice WHERE device_id = '$device_id' AND customers_id = '$customers_id' ")->queryAll();

            $result = array();
            $num = 0;
            foreach ($list as $value) {
                $result[$num] = $value;

                $invoice_code = $value['code'];
                $customers_id = $value['customers_id'];
                $device_id = $value['device_id'];

                $invoice_items = Yii::app()->db->createCommand("SELECT * FROM invoice_items WHERE  invoice_code = '$invoice_code' AND customers_id = '$customers_id' AND device_id = '$device_id' ")->queryAll();

                $invoiceItems = array();
                foreach ($invoice_items as $valueItems) {
                    $invoiceItems[] = $valueItems;
                }
                $result[$num]['invoice_items'] = $invoiceItems;

                $num += 1;
            }


            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionTestServer() {
        $result = array(
            "message" => "Server is Working ... !",
        );
        $this->returnJson($result, "OK", true);
    }

    public function actionSessionUpdate() {
        try {

            $session = new SessionLogins();
            $session->attributes = $_POST;
            $session->created = date("Y-m-d H:i:s");

            if (!$session->save()) {

                $er = $session->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $session->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $this->returnJson("", "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionDashboard() {

        $device_id = $_POST['device_id'];
        $device = Device::model()->findByPk($device_id);
        $target = floatval(round($device->target, 2));
        $d_target = round($target / 25, 2);

        $todaySale = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot,SUM(invoice_net_total - invoice_other_discount) as nettot,SUM(invoice_return_total) as totreturn,count(id) as cnt FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) = '" . date("Y-m-d") . "' ")->queryRow();

        $MonthSale = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot,count(id) as cnt FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) >= '" . date("Y-m-1") . "' AND DATE(eff_date) <= '" . date("Y-m-d") . "' ")->queryRow();


        $result = array(
            "today_sale" => floatval(round($todaySale['nettot'])),
            "today_returns" => floatval(round($todaySale['totreturn'])),
            "today_gross" => floatval(round($todaySale['tot'])),
            "day_target" => $d_target,
            "bal_to_target" => round($d_target - $todaySale['tot']),
            "month_total" => floatval(round($MonthSale['tot'])),
            "month_target" => $target,
            "bal_to_monthtarget" => round($target - $MonthSale['tot']),
            "calls" => floatval(round($todaySale['cnt'])),
            "month_calls" => floatval(round($MonthSale['cnt'])),
            "message" => "OK",
        );
        $this->returnJson($result, "OK", true);
    }

    public function actionCustomerDashboard() {

        $customers_id = $_POST['customers_id'];

        $due = Yii::app()->db->createCommand("SELECT SUM(cr) - SUM(dr) AS due FROM ledger WHERE customers_id = '$customers_id'")->queryRow();

        $invoice = Yii::app()->db->createCommand("SELECT COUNT(id) AS invcnt, SUM(invoice_total) as tot,SUM(invoice_return_total) as rtn FROM invoice WHERE customers_id = '$customers_id'")->queryRow();

        $lastInvoices = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot FROM invoice WHERE customers_id = '$customers_id' GROUP BY code ORDER BY eff_date ASC LIMIT 6")->queryAll();
        $data = array();
        $num = 1;
        foreach ($lastInvoices as $value) {
            $data[$num] = $value['tot'];
            $num += 1;
        }


        $sales = array();
        $num = 1;
        if (count($lastInvoices) > 0) {
            foreach ($lastInvoices as $value) {
                $sales[$num] = round($value['tot'], 0);
                $num += 1;
            }
        }


        $result = array(
            "outstanding" => floatval($due['due']),
            "due_days" => 0,
            "last_invoice_days" => 0,
            "total_sales" => floatval($invoice['tot']),
            "total_returns" => floatval($invoice['rtn']),
            "customer_days" => 0,
            "total_invoices" => intval($invoice['invcnt']),
            "chq_returns" => 0,
            "sales" => $sales
        );
        $this->returnJson($result, "OK", true);
    }

    public function actionlogin() {

        try {
            $pin = $_POST['pin'];
            $mac_id = $_POST['mac_id'];
            $version = intval($_POST['version']);


            if ($version < 10) {
                throw new Exception("Invalid Verion. Update the correct version.");
            }

            //"mac_id" => md5($mac_id)
            $data = Device::model()->findByAttributes(array("pin" => $pin, "online" => 1));
            if ($data == null) {
                throw new Exception("No device Found");
            }

            //GET THE LAST INVOICE CODE
            $invcode = Yii::app()->db->createCommand("SELECT code FROM invoice WHERE device_id = '" . $data->id . "' ORDER BY code DESC LIMIT 1")->queryRow();
            if (empty($invcode['code'])) {
                $invcode = 0;
            } else {
                $invcode = intval($invcode['code']);
            }

            $result = array(
                "device_id" => intval($data->id),
                "code" => $data->code,
                "name" => $data->name,
                "target" => $data->target,
                "inv_code" => $invcode,
                "message" => "OK",
                "distributor_name" => $data->name,
                "sales_rep" => $data->rep_name,
                "address_line1" => $data->address_line1,
                "address_line2" => $data->address_line2,
                "distributor_tel" => $data->tel_no,
                "territory" => $data->locations,
                "tech_support" => "0777553808",
                "print_footer_line_1" => "iForce by EWM",
                "print_footer_line_2" => "www.ewm.lk"
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionLastInvoiceCode() {

        try {
            $device_id = $_POST['device_id'];

            //GET THE LAST INVOICE CODE
            $invcode = Yii::app()->db->createCommand("SELECT code FROM invoice WHERE device_id = '$device_id' ORDER BY code DESC LIMIT 1")->queryRow();

            if (empty($invcode['code'])) {
                $invcode = 1;
            } else {
                $invcode = intval($invcode['code']);
            }

            $result = array(
                "inv_code" => $invcode,
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionloadStockList() {

        try {

            $device_id = $_POST['device_id'];
            $device = Device::model()->findByPk($device_id);

            if (empty($device->stock_lot)) {
                $stocklot = "";
            } else {
                $stocklot = " AND stock_lot = '" . $device->stock_lot . "' ";
            }

            $list = Yii::app()->db->createCommand("SELECT items.*,id AS itemsId,"
                            . "(SELECT SUM(qty) FROM stock WHERE items_id = itemsId AND device_id  = '$device_id' $stocklot ) as qty, "
                            . "(SELECT SUM(qty_ns) FROM stock WHERE items_id = itemsId AND device_id  = '$device_id' $stocklot ) as qty_ns "
                            . "FROM items WHERE online = 1 ")->queryAll();

            foreach ($list as $value) {

                if ($value['qty'] <= 0) {
                    $value['qty'] = 0;
                }

                if ($value['qty_ns'] <= 0) {
                    $value['qty_ns'] = 0;
                }

                //floatval($value['qty'])

                if (empty($device->stock_lot)) {
                    $qty = 1000;
                    $qty_ns = 0;
                } else {
                    $qty = $value['qty'] > 0 ? floatval($value['qty']) : 0;
                    $qty_ns = $value['qty_ns'] > 0 ? floatval($value['qty_ns']) : 0;
                }

                //MIN LIMIT
                $limit = 0.95;

                //MAX LIMIT
                $max_limit = 2;

                //floatval(round($value['mrp'] , 2))
                $result[] = array(
                    "items_id" => intval($value['id']),
                    "code" => $value['code'],
                    "item_name" => $value['item_name'],
                    "min_price" => floatval(round($value['mrp'] * $limit, 2)),
                    "max_price" => floatval(round($value['mrp'] * $max_limit, 2)),
                    "selling_price" => floatval(round($value['mrp'], 2)),
                    "qty" => $qty,
                    "qty_ns" => $qty_ns
                );
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionloadAreaList() {
        try {

            $device_id = $_POST['device_id'];
            $list = Yii::app()->db->createCommand("SELECT * FROM areas WHERE device_id = '$device_id' AND online = 1")->queryAll();

            if (count($list) <= 0) {
                throw new Exception("No Areas to Select");
            }

            foreach ($list as $value) {

                $cnt = Yii::app()->db->createCommand("SELECT COUNT(id) as cnt FROM customers WHERE areas_id = '" . $value['id'] . "' ")->queryRow();

                $result[] = array(
                    "area_id" => intval($value['id']),
                    "name" => $value['name'],
                    "last_date" => $value['last_date']
                );
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionUpdateStatus() {
        try {

            $productivecall = new Productivecalls();

            $productivecall->attributes = $_POST;
            $productivecall->created = date("Y-m-d H:i:s");
            $productivecall->online = 1;


            if (!$productivecall->save()) {

                $er = $productivecall->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $productivecall->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $result[] = array(
                "is_synced" => 1
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionUpdateCustomer() {
        try {

            $customers_id = $_POST['customers_id'];

            $cus = Customers::model()->findByPk($customers_id);
            $cus->latitude = $_POST['latitude'];
            $cus->longitude = $_POST['longitude'];

            if (!$cus->save()) {

                $er = $cus->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $cus->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $result[] = array(
                "is_synced" => 1
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionloadStatusList() {

        try {

            $list = array(1 => "CLOSED", 2 => "NO SALE", 3 => "NO STOCK");

            foreach ($list as $key => $value) {
                $result[] = array(
                    "id" => intval($key),
                    "name" => $value
                );
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionloadCustomerTypes() {

        try {

            $list = Yii::app()->db->createCommand("SELECT * FROM customer_types WHERE online = 1")->queryAll();

            foreach ($list as $value) {
                $result[] = array(
                    "customer_types_id" => intval($value['id']),
                    "name" => $value['name']
                );
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionUpdateShelf() {
        try {

            $eff_date = date("Y-m-d");
            $customers_id = $_POST['customers_id'];

            $list = json_decode($_POST['items'], false);
            foreach ($list as $value) {

                $shelf = Shelf::model()->findByAttributes(array("eff_date" => $eff_date, "customers_id" => $customers_id, "items_id" => $value->items_id));
                if ($shelf == null) {
                    $shelf = new Shelf();
                }

                $shelf->customers_id = $customers_id;
                $shelf->items_id = $value->items_id;
                $shelf->qty = $value->qty;
                $shelf->eff_date = $eff_date;
                $shelf->created = date("Y-m-d H:i:s");

                if (!$shelf->save()) {

                    $er = $shelf->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $shelf->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
            }

            $result[] = array(
                "is_synced" => 1
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionloadCustomers() {
        try {
            $areas_id = $_POST['areas_id'];
            $list = Yii::app()->db->createCommand("SELECT id FROM customers WHERE areas_id = '$areas_id' ")->queryAll();

            $data = array();
            foreach ($list as $value) {
                $model = Customers::model()->findByPk($value['id']);
                $data[] = array(
                    'id' => intval($model->id),
                    'customer_types_id' => intval($model->customer_types_id),
                    'areas_id' => intval($model->areas_id),
                    'code' => $model->code,
                    'name' => $model->name,
                    'address_no' => $model->address_no,
                    'address_1' => $model->address_1,
                    'address_2' => $model->address_2,
                    'street' => $model->street,
                    'contact_name' => $model->contact_name,
                    'mobile' => $model->mobile,
                    'landline' => $model->landline,
                    'cover_image' => NULL,
                    'latitude' => floatval($model->latitude),
                    'longitude' => floatval($model->longitude),
                    'is_synced' => intval($model->is_synced),
                    'created' => $model->created,
                    'synced' => $model->synced,
                    'is_approved' => intval($model->is_approved),
                    'discount' => floatval($model->discount),
                    'online' => intval($model->online)
                );
            }

            $this->returnJson($data, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionCreateCustomer() {
        try {


            $model = new Customers();
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


            //FILE UPLOAD
            //".". $uploadedFile->getExtensionName()
            $customer = Customers::model()->findByPk($model->id);
            if ($_FILES['imagenPerfil']['size'] > 0) {
                $uploadedFile = CUploadedFile::getInstanceByName("imagenPerfil");
                $customer->cover_image = md5($model->id) . "." . $uploadedFile->getExtensionName();
                $uploadedFile->saveAs("images/" . $customer->cover_image);
                $customer->save();
            }


            $list = array(
                'id' => intval($model->id),
                'customer_types_id' => intval($model->customer_types_id),
                'areas_id' => intval($model->areas_id),
                'code' => $model->code,
                'name' => $model->name,
                'address_no' => $model->address_no,
                'address_1' => $model->address_1,
                'address_2' => $model->address_2,
                'street' => $model->street,
                'contact_name' => $model->contact_name,
                'mobile' => $model->mobile,
                'landline' => $model->landline,
                'cover_image' => $_FILES["file"]["name"],
                'latitude' => floatval($model->latitude),
                'longitude' => floatval($model->longitude),
                'is_synced' => intval($model->is_synced),
                'created' => $model->created,
                'synced' => $model->synced,
                'is_approved' => intval($model->is_approved),
                'discount' => floatval($model->discount),
                'online' => intval($model->online)
            );


            $this->returnJson($list, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionListRoute() {
        try {

            $device_id = $_POST['device_id'];
            $date_from = $_POST['date_from'];
            $date_to = $_POST['date_to'];


            // Set timezone
            date_default_timezone_set('UTC');
            while (strtotime($date_from) <= strtotime($date_to)) {

                $sch = Schedule::model()->findByAttributes(array("eff_date" => $date_from, "device_id" => $device_id));
                if ($sch == null) {
                    $sch = new Schedule();
                    $sch->device_id = $device_id;
                    $sch->areas_id = null;
                    $sch->eff_date = $date_from;
                    $sch->online = 1;
                    $sch->created = date("Y-m-d H:i:s");
                    $sch->save();
                }

                $result[] = array(
                    "schedule_id" => $sch->id,
                    "date" => $sch->eff_date,
                    "areas_id" => $sch->areas_id,
                    "name" => !empty($sch->areas_id) ? $sch->areas->name : "N/A",
                    "day" => date("D", strtotime($sch->eff_date)),
                    "is_holiday" => 0
                );

                $date_from = date("Y-m-d", strtotime("+1 day", strtotime($date_from)));
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function logError($txt, $device_id) {
        //if ($device_id == 5) {
        $fp = fopen('testlog.txt', 'a');
        fwrite($fp, $txt . PHP_EOL);
        fclose($fp);
        //}
    }

    public function actionInvItemsSync() {
        try {

            $inv_items_id = $_POST['invoice_item_id'];
            $invoice_code = $_POST['invoice_code'];
            $device_id = $_POST['device_id'];


            $items = InvoiceItems::model()->findByAttributes(array("invoice_item_id" => $inv_items_id, "invoice_code" => $invoice_code, "device_id" => $device_id));

            if ($items == null) {
                $items = New InvoiceItems();
            }

            $items->invoice_item_id = $inv_items_id;
            $items->invoice_code = $invoice_code;
            $items->device_id = $device_id;

            $items->customers_id = $_POST['customers_id'];
            $items->items_id = $_POST['items_id'];
            $items->item_name = $_POST['items_name'];
            $items->qty_selable = $_POST['qty_selable'];
            $items->qty_nonselable = $_POST['qty_nonselable'];
            $items->mrp = $_POST['mrp'];
            $items->discount = round($_POST['discount'], 2);
            $items->discount_type = $_POST['discount_type'];
            $items->discount_amount = round($_POST['discount_amount'], 2);
            $items->is_manual_dis = $_POST['is_manual_dis'];
            $items->total = round($_POST['total'], 2);
            $items->eff_date = $_POST['eff_date'];
            $items->item_type = $_POST['item_type']; // FREE - 2/SAMPLE - 3/SELL - 1

            if (!$items->save()) {

                $er = $items->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $items->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }

                throw new Exception($err_txt);
            }

            $result[] = array(
                "is_synced" => 1
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {

            $result[] = array(
                "is_synced" => 0
            );
            $this->returnJson($result, $exc->getMessage(), false);
        }
    }

    public function actionInvSync() {
        try {

            $device_id = trim($_POST['device_id']);
            $customers_id = trim($_POST['customers_id']);
            $code = trim($_POST['code']);

            $invoice = Invoice::model()->findByAttributes(array("device_id" => $device_id, "code" => $code, "customers_id" => $customers_id));
            if ($invoice == null) {
                $invoice = new Invoice();
            }

            $invoice->device_id = $device_id;
            $invoice->customers_id = $customers_id;
            $invoice->code = $code;

            $invoice->invoice_discount_total = round($_POST['invoice_discount_total'], 2);
            $invoice->invoice_net_total = round($_POST['invoice_net_total'], 2);
            $invoice->invoice_return_total = round($_POST['invoice_return_total'], 2);
            $invoice->invoice_total = round($_POST['invoice_total'], 2);
            $invoice->invoice_other_discount = round($_POST['invoice_other_discount'], 2);

            //THESE2 should remove on new releases
            $invoice->invoice_discount = round($_POST['invoice_discount'], 2);
            $invoice->invoice_discount_type = $_POST['invoice_discount_type'];

            $invoice->eff_date = $_POST['eff_date'];
            $invoice->sync_time = date("Y-m-d H:i:s");
            $invoice->is_synced = 1;
            $invoice->battery_level = intval($_POST['battery_level']);
            $invoice->latitude = $_POST['latitude'];
            $invoice->longitude = $_POST['longitude'];
            $invoice->pay_type = $_POST['pay_type'];
            $invoice->created = date("Y-m-d H:i:s");
            $invoice->online = $_POST['online'];

            if (!$invoice->save()) {

                $er = $invoice->getErrors();
                $err_txt = "";
                foreach ($er as $key => $value) {
                    $lebel = $invoice->getAttributeLabel($key);
                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                }
                throw new Exception($err_txt);
            }

            $result[] = array(
                "is_synced" => $invoice->is_synced
            );

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $result[] = array(
                "is_synced" => 0
            );
            $this->returnJson($result, $exc->getMessage(), false);
        }
    }

    public function actiontestStock() {

        try {
            $stock = new Stock();

            $stock->device_id = 1;
            $stock->brands_id = 1;
            $stock->items_id = 1;
            $stock->qty = 5;
            $stock->qty_ns = '';
            $stock->stock_lot = 1;
            $stock->cost = 110;
            $stock->selling = 150;
            $stock->discount = 0;
            $stock->total = 1520;
            $stock->batch_no = "";
            $stock->expire_date = "";
            $stock->tbl_name = "invoice";
            $stock->p_id = 1;
            $stock->f_id = 1;
            $stock->created = '2020-10-29';
            $stock->online = 3;

//            if (!$stock->save()) {
//                $er = $stock->getErrors();
//                $err_txt = "";
//                foreach ($er as $key => $value) {
//                    $lebel = $stock->getAttributeLabel($key);
//                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
//                }
//                throw new Exception($err_txt);
//            }
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionInvoiceCreate() {
        try {

            $device_id = $_POST['device_id'];
            $customers_id = $_POST['customers_id'];
            $code = $_POST['code'];

            $invoice = Invoice::model()->findByAttributes(array("device_id" => $device_id, "code" => $code, "customers_id" => $customers_id));

            if ($invoice == null) {

                $invoice = new Invoice();

                $invoice->device_id = $device_id;
                $invoice->customers_id = $customers_id;
                $invoice->code = $code;

                $invoice->invoice_discount_total = round($_POST['invoice_discount_total'], 2);
                $invoice->invoice_net_total = round($_POST['invoice_net_total'], 2);
                $invoice->invoice_return_total = round($_POST['invoice_return_total'], 2);
                $invoice->invoice_total = round($_POST['invoice_total'], 2);
                $invoice->invoice_other_discount = round($_POST['invoice_other_discount'], 2);


                //THESE2 should remove on new releases
                $invoice->invoice_discount = round($_POST['invoice_discount'], 2);
                $invoice->invoice_discount_type = $_POST['invoice_discount_type'];

                $invoice->eff_date = $_POST['eff_date'];
                $invoice->sync_time = date("Y-m-d H:i:s");
                $invoice->is_synced = 1;
                $invoice->battery_level = intval($_POST['battery_level']);
                $invoice->latitude = $_POST['latitude'];
                $invoice->longitude = $_POST['longitude'];
                $invoice->pay_type = $_POST['pay_type'];
                $invoice->created = date("Y-m-d H:i:s");
                $invoice->online = $_POST['online'];

                if (!$invoice->save()) {

                    $er = $invoice->getErrors();
                    $err_txt = "";
                    foreach ($er as $key => $value) {
                        $lebel = $invoice->getAttributeLabel($key);
                        $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                    }
                    throw new Exception($err_txt);
                }
            } else {
                $this->logError("SUPLICATE INV TRY  " . $invoice->id . date("Y-m-d H:i:s"), $device_id);
            }


            $list = json_decode($_POST['items'], false);
            foreach ($list as $value) {

                $inv_items_id = $value->invoice_item_id;
                $invoice_code = $value->invoice_code;
                $device_id = $value->device_id;

                $itemsRow = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE "
                                . "invoice_item_id = '$inv_items_id' AND "
                                . "invoice_code = '$invoice_code' AND "
                                . "device_id = '$device_id' ")->queryRow();

                if (!empty($itemsRow['id'])) {
                    $this->logError("SUPLICATE INV-ITEM TRY  " . $itemsRow['id'] . date("Y-m-d H:i:s"), $device_id);
                    $items = InvoiceItems::model()->findByPk($itemsRow['id']);
                } else {
                    $items = New InvoiceItems();
                    $items->invoice_item_id = $inv_items_id;
                    $items->invoice_code = $invoice_code;
                    $items->customers_id = $value->customers_id;
                    $items->device_id = $device_id;
                    $items->items_id = $value->items_id;
                    $items->item_name = $value->items_name;
                    $items->qty_selable = $value->qty_selable;
                    $items->qty_nonselable = $value->qty_nonselable;
                    $items->mrp = $value->mrp;
                    $items->discount = round($value->discount, 2);
                    $items->discount_type = $value->discount_type;
                    $items->discount_amount = round($value->discount_amount, 2);
                    $items->is_manual_dis = $value->is_manual_dis;
                    $items->total = round($value->total, 2);
                    $items->eff_date = $invoice->eff_date;
                    $items->item_type = $value->item_type; // 2-return,3-FREE,4-SAMPLE,1=SELL

                    if (!$items->save()) {
                        $er = $items->getErrors();
                        $err_txt = "";
                        foreach ($er as $key => $value) {
                            $lebel = $items->getAttributeLabel($key);
                            $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                        }
                        throw new Exception($err_txt);
                    }

                    $qty_s = $value->qty_selable;
                    $qty_ns = $value->qty_nonselable;

                    if ($items->item_type == 2) {

                        $avList = Yii::app()->db->createCommand("SELECT cost,batch_no,expire_date FROM stock WHERE device_id = '" . $device_id . "' AND items_id = '" . $value->items_id . "' AND stock_lot = '" . $items->device->stock_lot . "' GROUP BY cost,batch_no,expire_date ORDER BY created DESC LIMIT 1 ")->queryRow();


                        //ASSIGn LAST COST IF EMPTY
                        if (empty($avList['cost'])) {
                            $avList['cost'] = $items->items->cost;
                        }

                        if (empty($avList['cost'])) {
                            $avList['cost'] = 1;
                        }


                        $stock = new Stock();
                        $stock->device_id = $device_id;
                        $stock->brands_id = $items->items->brands_id;
                        $stock->items_id = $items->items_id;
                        $stock->qty = $qty_s;
                        $stock->qty_ns = $qty_ns;
                        $stock->stock_lot = $items->device->stock_lot;
                        $stock->cost = $avList['cost'];
                        $stock->selling = $items->mrp;
                        $stock->discount = $items->discount;
                        $stock->total = $items->total;
                        $stock->batch_no = $avList['batch_no'];
                        $stock->expire_date = $avList['expire_date'];
                        $stock->tbl_name = "invoice";
                        $stock->p_id = $invoice->id;
                        $stock->f_id = $items->id;
                        $stock->created = $invoice->eff_date;
                        $stock->online = 3;

                        if (!$stock->save()) {
                            $er = $stock->getErrors();
                            $err_txt = "";
                            foreach ($er as $key => $value) {
                                $lebel = $stock->getAttributeLabel($key);
                                $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                            }
                            throw new Exception($err_txt);
                        }
                    } else {

                        $avList = Yii::app()->db->createCommand("SELECT SUM(qty) as qty_s,SUM(qty_ns) as qty_ns,items_id,cost,batch_no,expire_date FROM stock WHERE device_id = '" . $device_id . "' AND items_id = '" . $value->items_id . "' AND stock_lot = '" . $items->device->stock_lot . "' GROUP BY cost,batch_no,expire_date ORDER BY created ASC ")->queryAll();
                        foreach ($avList as $value) {

                            if ($qty_s <= 0) {
                                continue;
                            }

                            //SET THE BATCH QTY
                            if ($qty_s >= $value['qty_s']) {
                                $stock_s = $value['qty_s'];
                            } else {
                                $stock_s = $qty_s;
                            }



                            //RE Calculate the Requirement
                            $qty_s = $qty_s - $stock_s;

                            //ASSIGn LAST COST IF EMPTY
                            if (empty($value['cost'])) {
                                $value['cost'] = $items->items->cost;
                            }

                            if (empty($value['cost'])) {
                                $value['cost'] = 1;
                            }

                            $stock = new Stock();
                            $stock->device_id = $device_id;
                            $stock->brands_id = $items->items->brands_id;
                            $stock->items_id = $items->items_id;
                            $stock->qty = 0 - $stock_s;
                            $stock->stock_lot = $items->device->stock_lot;
                            $stock->cost = $value['cost'];
                            $stock->selling = $items->mrp;
                            $stock->discount = $items->discount;
                            $stock->total = $items->mrp * $stock_s;
                            $stock->batch_no = $value['batch_no'];
                            $stock->expire_date = $value['expire_date'];
                            $stock->tbl_name = "invoice";
                            $stock->p_id = $invoice->id;
                            $stock->f_id = $items->id;
                            $stock->created = $invoice->eff_date;
                            $stock->online = 3;


                            if (!$stock->save()) {
                                $er = $stock->getErrors();
                                $err_txt = "";
                                foreach ($er as $key => $value) {
                                    $lebel = $stock->getAttributeLabel($key);
                                    $err_txt .= $lebel . " : " . $value[0] . "<br/>";
                                }
                                throw new Exception($err_txt);
                            }
                        }
                    }
                }
            }

            $result[] = array(
                "is_synced" => $invoice->is_synced
            );
            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->logError("ERROR " . $exc->getMessage() . date("Y-m-d H:i:s"), $device_id);
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionUpdatePayment() {
        try {

            $model = new Payment;

            $model->attributes = $_POST;
            $model->created = date("Y-m-d H:i:s");
            $model->code = $this->returnCode("payment", "PAY-" . $model->customers->areas->device->code . "-");

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

            $this->returnJson("", "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

    public function actionSyncPaymentList() {
        try {

            $customers_id = $_POST['customers_id'];
            $list = Yii::app()->db->createCommand("SELECT *,DATE(created) as effDate FROM `ledger` WHERE customers_id = '$customers_id' ORDER BY created ASC ")->queryAll();

            if (count($list) < 0) {
                throw new Exception("No Payments");
            }

            foreach ($list as $value) {
                $result[] = array(
                    "id" => $value['id'],
                    "customers_id" => $value['customers_id'],
                    "code" => $value['code'],
                    "eff_date" => $value['effDate'],
                    "l_type" => $value['l_type'],
                    "dr" => $value['dr'],
                    "cr" => $value['cr'],
                    "ref" => $value['ref']
                );
            }

            $this->returnJson($result, "OK", true);
        } catch (Exception $exc) {
            $this->returnJson("", $exc->getMessage(), false);
        }
    }

}
