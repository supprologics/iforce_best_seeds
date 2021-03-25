<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" >

        <thead>
            <tr class="table-active">

                <th><input type="checkbox" id="selectall"></th>
                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th class="text-right">Qty</th>
                <th class="text-right">COST</th>
                <th class="text-right">TOTAL</th>                
        </thead>
        <tbody class="lineitems" >
            <?php
            
            $po = Grn::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;

            $list = Yii::app()->db->createCommand("SELECT id FROM grn_items WHERE grn_id = '" . $data['id'] . "'")->queryAll();
foreach ($list as $itemArray) {

    $item = GrnItems::model()->findByPk($itemArray['id']);
                ?>
            <tr data-id="<?php echo $item->id; ?>" >

                    <td><input id="line_<?php echo $item->id; ?>" type="checkbox" class="chk" value="<?php echo $item->id; ?>" /></td>
                    <td class="selitem"><?php echo $num; ?></td>
                    <td class="selitem"><?php echo $item->items->code; ?></td>
                    <td class="selitem"><?php echo $item->items->item_name; ?></td>
                    <td width="7%"  class="text-right p-0"><input type="text" name="qty[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo $item->qty; ?>" /></td>
                    <td width="7%" class="text-right p-0"><input type="text" name="cost[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo sprintf('%0.2f',$item->cost); ?>" /></td>
                    <td class="selitem text-right"><?php echo number_format($item->cost * $item->qty,2); ?></td>
                    
                </tr>
                <?php
                $num += 1;
                $total += $item->cost * $item->qty;
            }
            ?>
        </tbody>
        
        <tr>
            <td colspan="6">Total</td>
            <td class="text-right"><?php echo number_format($total,2); ?></td>
        </tr>
    </table>
</form>