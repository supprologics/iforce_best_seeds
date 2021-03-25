<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">


<div>
    <h5 class="text-center">Performance Score Board</h5>
    <table cellspacing="0" cellpadding="0" width="100%" class="table table-sm" >
        
        <tr>
            <th>#</th>
            <th>Location</th>
            <th>Name</th>
        </tr>

        <?php
        $start_date = date("Y-m-1");
        $today = date("Y-m-d");

        $list = Yii::app()->db->createCommand("SELECT device_id as deviceId, round((IFNULL((SELECT SUM(total) FROM invoice_items WHERE device_id = deviceId AND eff_date >= '$start_date' AND eff_date <= '$today' AND item_type = 1) ,0) - IFNULL((SELECT SUM(total) FROM invoice_items WHERE device_id = deviceId AND eff_date >= '$start_date' AND eff_date <= '$today' AND item_type = 2),0))/device.target * 100,2) as totalCalc FROM `invoice_items`,device WHERE device.id = invoice_items.device_id AND invoice_items.eff_date >= '2021-01-01' GROUP BY invoice_items.device_id ORDER BY totalCalc DESC")->queryAll();

        $num = 1;
        foreach ($list as $value) {
            $device = Device::model()->findByPk($value['deviceId']);

            if ($num > 3) {
                $cls = "d-none";
            } else {
                $cls = "";
            }
            
            if($device->id == $id){
                $cls = "table-warning";
                $rep_name = "You";
            }else{
                $rep_name = $device->rep_name;
            }
            
            ?>

            <tr class="<?php echo $cls; ?>">
                <td><?php echo $num; ?></td>
                <td><?php echo $device->name; ?></td>
                <td><?php echo $rep_name; ?></td>
            </tr>

            <?php
            $num += 1;
        }
        ?>


    </table>
</div>

<div>
    <h5 class="text-center">Product Targets</h5>
    <table cellspacing="0" cellpadding="0" width="100%" class="table table-sm" >
        <tr>
            <th>Product</th>
            <th class="text-right">Target</th>
            <th class="text-right">Sale</th>
            <th class="text-right">Balance</th>
        </tr>
        <?php
        $list = Items::model()->findAllByAttributes(array("online" => 1), array("order" => 'item_name ASC'));
        foreach ($list as $value) {

            $items_id = $value->id;
            $buffer = Yii::app()->db->createCommand("SELECT qty FROM bufer_stock WHERE items_id = '$items_id' AND device_id = '$id' ")->queryRow();

            $invoice = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM `invoice_items` where device_id = $id AND eff_date >= '$start_date' AND eff_date <= '$today' AND item_type = 1 AND items_id = '$items_id'")->queryRow();
            $returns = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM `invoice_items` where device_id = $id AND eff_date >= '$start_date' AND eff_date <= '$today' AND item_type = 2 AND items_id = '$items_id'")->queryRow();

            $target = $value->mrp * $buffer['qty'];
            $sale = $invoice['tot'] - $returns['tot'];
            $balnce = $target - $sale;

            $ratio = round($balnce / $target * 100);

            $cls = "table-success";

            if ($ratio >= 75) {
                $cls = "table-danger";
            }

            if ($ratio > 0 && $ratio < 75) {
                $cls = "table-warning";
            }

            if ($ratio <= 0) {
                $cls = "table-success";
            }
            
            if($target > 0){
            
            ?>
            <tr class="<?php echo $cls; ?>">
                <td><?php echo $value->item_name; ?></td>
                <td class="text-right"><?php echo number_format($target); ?></td>
                <td class="text-right"><?php echo number_format($sale); ?></td>
                <td class="text-right"><?php echo number_format($balnce); ?></td>
            </tr>

            <?php
            }
        }
        ?>
    </table>
</div>
