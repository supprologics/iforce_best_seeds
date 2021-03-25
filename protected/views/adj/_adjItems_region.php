<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" >

        <thead>
            <tr class="table-active">

                <th><input type="checkbox" id="selectall"></th>
                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>                
                <th class="text-right">SELLING</th>
                <th class="text-right">Qty</th>
                <th class="text-right">+/-</th>

                <th class="text-right">VARIANCE</th>                
        </thead>
        <tbody class="lineitems" >
            <?php
            $po = Adj::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;


            if ($po->adj_type == 'NS') {
                $key = "qty_ns";
                $key_var = "variance_ns";
            } else {
                $key = "qty";
                $key_var = "variance";
            }


            $list = Yii::app()->db->createCommand("SELECT id FROM adj_items WHERE adj_id = '" . $data['id'] . "'")->queryAll();
            foreach ($list as $itemArray) {

                $item = AdjItems::model()->findByPk($itemArray['id']);
                ?>
                <tr data-id="<?php echo $item->id; ?>" >

                    <td><input id="line_<?php echo $item->id; ?>" type="checkbox" class="chk" value="<?php echo $item->id; ?>" /></td>
                    <td class="selitem"><?php echo $num; ?></td>
                    <td class="selitem"><?php echo $item->items->code; ?></td>
                    <td class="selitem"><?php echo $item->items->item_name; ?></td>
                    <td class="text-right selitem"><?php echo sprintf('%0.2f', $item->selling); ?></td>                    
                    <td width="7%" class="text-right p-0"><input type="text" min="0" required="true" name="<?php echo $key; ?>[<?php echo $item->id ?>]" autocomplete="off" class="nonNegative form-control text-right form-control-sm" value="<?php echo $item->{$key}; ?>" /></td>
                    <td class="text-right selitem"><?php echo $item->{$key_var}; ?></td>

                    <td class="selitem text-right"><?php echo number_format($item->selling * $item->{$key_var}, 2); ?></td>

                </tr>
                <?php
                $num += 1;
                $total += $item->selling * $item->{$key_var};
            }
            ?>
        </tbody>

        <tr>
            <td colspan="7">Total</td>
            <td class="text-right"><?php echo number_format($total, 2); ?></td>
        </tr>
    </table>
</form>