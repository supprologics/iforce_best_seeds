
<table class="table table-sm table-hover table-sp" >
    <tr class="table-active red">
        <th></th>
        <th>CODE</th>
        <th>BRAND</th>
        <th>DESCRIPTION</th>
        <th class="text-right">Target</th>  
        <th class="text-right">Qty</th>
    </tr>
    <tbody class="lineitems">
        <?php
        $device_id = $data['id'];
        $num = 1;
        $list = Yii::app()->db->createCommand("SELECT id FROM items ORDER BY item_name ASC")->queryAll();
        foreach ($list as $itemArray) {

            $item = Items::model()->findByPk($itemArray['id']);
            $buffer = BuferStock::model()->findByAttributes(array("device_id" => $device_id, "items_id" => $item->id));
            $bqty = isset($buffer) ? $buffer->qty : 0;
            $target_sale = isset($buffer) ? $buffer->target_sale : 0;
            ?>
            <tr data-id="<?php echo $item->id; ?>" >
                <td width="5%" class="selitem"><?php echo $num; ?></td>
                <td width="15%" class="selitem"><?php echo $item->code; ?></td>
                <td width="25%" class="selitem"><?php echo $item->brands->name; ?></td>
                <td width="45%" class="selitem"><?php echo $item->item_name; ?></td>
                <td class="text-right p-0"><input type="text" name="target_sale[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo $target_sale; ?>" /></td>
                <td class="text-right p-0"><input type="text" name="qty[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo $bqty; ?>" /></td>
            </tr>
            <?php
            $num += 1;
        }
        ?>
    </tbody>
</table>
