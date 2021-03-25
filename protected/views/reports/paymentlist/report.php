<?php

$device_id = $_POST['device_id'];
$areas_id = $_POST['areas_id'];

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Customer Listing Report</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
    </head>
    <body>

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Customer Listing Report</h2>
                    <table width="100%">
                        
                        <tr>
                            <td width="25%" style="font-weight: bold;">Sales Rep</td>
                            <td><?php echo !empty($device_id) ? Device::model()->findByPk($device_id)->name : "Select ALL"; ?></td>
                        </tr>
                        <tr>
                            <td width="25%" style="font-weight: bold;">Area</td>
                            <td><?php echo !empty($areas_id) ? Areas::model()->findByPk($areas_id)->name : "Select ALL"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>


            <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <th></th>
                    <th width="95px;">TYPE</th>
                    <th>ROUTE</th>
                    <th>CODE</th>
                    <th>NAME</th>
                    <th>ADDRESS</th>
                    <th>STREET</th>
                    <th>CONTACT</th>
                    <th>MOBILE</th>
                    <th>PHONE</th>
                    <th class="text-right">Due</th>
                </tr>

                <?php
                $num = 1;
                
                                
                if(!empty($areas_id)){
                    $area = " AND areas_id = '$areas_id' ";
                }else{
                    $area = "";
                }
                
                if(!empty($device_id)){
                    $devices = " AND device.id = '$device_id' ";
                }else{
                    $devices = "";
                }
                
                if(!empty($_POST['customer_types_id'])){
                    $customer_types_id = " AND customer_types_id = '". $_POST['customer_types_id'] ."' ";
                }else{
                    $customer_types_id = "";
                }
                
                
                $list = Yii::app()->db->createCommand("SELECT customers.id FROM customers,areas,device WHERE "
                        . "customers.areas_id = areas.id AND "
                        . "areas.device_id = device.id $area $devices $customer_types_id ORDER BY CONVERT(customers.code, SIGNED INTEGER) ASC")->queryAll();
                
                
                $tot = 0;
                foreach ($list as $value) {


                    $att = Customers::model()->findByPk($value['id']);
                    $due = Yii::app()->db->createCommand("SELECT SUM(cr) - SUM(dr) AS due FROM ledger WHERE customers_id = '". $value['id'] ."'")->queryRow();
                    ?>

                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $att->customerTypes->name; ?></td>
                        <td><?php echo $att->areas->name; ?></td>
                        <td><?php echo $att->code; ?></td>
                        <td><?php echo $att->name; ?></td>
                        <td><?php echo $att->address_no; ?>,<?php echo $att->address_1; ?>,<?php echo $att->address_2; ?></td>
                        <td><?php echo $att->street; ?></td>
                        <td><?php echo $att->contact_name; ?></td>
                        <td><?php echo $att->mobile; ?></td>
                        <td><?php echo $att->landline; ?></td>
                        <td class="text-right"><?php echo number_format($due['due'],2) ?></td>
                    </tr>
                    <?php
                    $num += 1;
                    $tot += $due['due'];
                    
                }
                ?>
                    
                    <tr>
                        <td colspan="10">Total Due</td>
                        <td class="text-right"><?php echo number_format($tot,2) ?></td>
                    </tr>

            </table>

        </div>
    </body>
</html>

