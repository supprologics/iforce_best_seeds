<?php

// This is the database connection configuration.
return array(
	'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
	// uncomment the following lines to use a MySQL database
	
	'connectionString' => 'mysql:host=localhost;dbname=prologics_sfa',
	'emulatePrepare' => true,
	'username' => 'prologics_sfa',
	'password' => 'G[-sr0G,?mCo',
	'charset' => 'utf8',
	
);

/***
 * 
 * G[-sr0G,?mCo
 * 
 * 
 */


        ini_set('max_execution_time', 3600);
        $list = Yii::app()->db->createCommand("SELECT * FROM `invoice` WHERE id = 48736 ")->queryAll();
        foreach ($list as $value) {
            
            $invid = $value['id'];
            
            $invoice_code = $value['code'];
            $device_id = $value['device_id'];
            $customers_id = $value['customers_id'];
            
            $list = Yii::app()->db->createCommand("SELECT SUM(qty_selable * mrp) as nettot,SUM(total) as tot, SUM(discount_amount) as dis FROM invoice_items WHERE invoice_code = '$invoice_code' AND device_id = '$device_id' AND customers_id = '$customers_id' ")->queryRow();
            $listReturn = Yii::app()->db->createCommand("SELECT SUM(total) as returntot FROM invoice_items WHERE invoice_code = '$invoice_code' AND device_id = '$device_id' AND customers_id = '$customers_id' AND item_type = 2")->queryRow();
        
            $invoice = Invoice::model()->findByPk($invid);
            $invoice->invoice_total = $list['tot'] - $listReturn['returntot'];
            $invoice->invoice_net_total = $list['nettot'];
            $invoice->invoice_discount = $list['dis'];
            $invoice->invoice_return_total = $listReturn['returntot'];
            
            $invoice->save();            
            
        }  
        
        
        
        
        
        
        //UPDATE LEDGER
            $ledger = Ledger::model()->findByAttributes(array("customers_id" => $invoice->customers_id, "code" => $invoice->code, "l_type" => "INVOICE"));
            if ($ledger == null) {
                $ledger = new Ledger();
            }

            $ledger->customers_id = $invoice->customers_id;
            $ledger->code = $invoice->code;
            $ledger->l_type = "INVOICE";
            if ($invoice->invoice_total > 0) {
                $ledger->cr = abs($invoice->invoice_total);
            } else {
                $ledger->dr = abs($invoice->invoice_total);
            }
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
            //UPDATE LEDGER END
