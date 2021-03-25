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
                <th class="text-right">MRP</th>
                <th class="text-right">TOTAL</th>                
        </thead>
        <tbody class="lineitems" >
            <?php
            
            $po = Adj::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;

            $list = Yii::app()->db->createCommand("SELECT id FROM adj_items WHERE adj_id = '" . $data['id'] . "'")->queryAll();
            foreach ($list as $itemArray) {

                $item = AdjItems::model()->findByPk($itemArray['id']);
                ?>
            <tr data-id="<?php echo $item->id; ?>" >

                    <td class="selitem"><?php echo $num; ?></td>
                    <td class="selitem"><?php echo $item->items->code; ?></td>
                    <td class="selitem"><?php echo $item->items->item_name; ?></td>
                    <td><?php echo $item->batch_no; ?></td>
                    <td width="7%"><?php echo $item->expire_date; ?></td>                    
                    <td class="text-right"><?php echo $item->qty; ?></td>
                    <td class="text-right"><?php echo sprintf('%0.2f',$item->selling); ?></td>
                    <td class="selitem text-right"><?php echo number_format($item->selling * $item->qty,2); ?></td>
                    
                </tr>
                <?php
                $num += 1;
                $total += $item->selling * $item->qty;
            }
            ?>
        </tbody>
        
        <tr>
            <td colspan="7">Total</td>
            <td class="text-right"><?php echo number_format($total,2); ?></td>
        </tr>
    </table>
</form>