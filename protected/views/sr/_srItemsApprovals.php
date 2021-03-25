<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" >

        <thead>
            <tr class="table-active">

                
                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th>BATCH</th>
                <th>EXPIRE</th>
                <th class="text-right">Qty</th>
                <th class="text-right">COST</th>
                <th class="text-right">TOTAL</th>                
        </thead>
        <tbody class="lineitems" >
            <?php
            $po = Sr::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;
            
            
            if($po->sr_type == 'NS'){
                $key = "qty_ns";
            }else{
                $key = "qty";
            }

            $list = Yii::app()->db->createCommand("SELECT id FROM sr_items WHERE sr_id = '" . $data['id'] . "'")->queryAll();
            foreach ($list as $itemArray) {

                $item = SrItems::model()->findByPk($itemArray['id']);
                
                $qty = Yii::app()->db->createCommand("SELECT SUM($key) as tot FROM stock WHERE items_id = '" . $item->items_id . "' AND "
                                . "batch_no ='" . $item->batch_no . "' AND "
                                . "cost = '" . $item->cost . "' AND "
                                . "expire_date = '" . $item->expire_date . "'")->queryAll();
                
                
                $maxQty = $item->{$key} + $qty[0]['tot'];
                
                
                ?>
                <tr data-id="<?php echo $item->id; ?>" >

                    <td class="selitem"><?php echo $num; ?></td>
                    <td class="selitem"><?php echo $item->items->code; ?></td>
                    <td class="selitem"><?php echo $item->items->item_name; ?></td>
                    <td width="7%" ><?php echo $item->batch_no; ?></td>
                    <td width="7%" ><?php echo $item->expire_date; ?></td>
                    <td width="7%" class="text-right"><?php echo $item->{$key}; ?></td>
                    <td width="7%" class="text-right"><?php echo sprintf('%0.2f', $item->cost); ?></td>
                    <td width="7%" class="selitem text-right"><?php echo number_format($item->cost * $item->{$key}, 2); ?></td>

                </tr>
                <?php
                $num += 1;
                $total += $item->cost * $item->{$key};
            }
            ?>
        </tbody>

        <tr>
            <td colspan="7">Total</td>
            <td class="text-right"><?php echo number_format($total, 2); ?></td>
        </tr>
    </table>
</form>