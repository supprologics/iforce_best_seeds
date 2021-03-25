<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" >

        <thead>
            <tr class="table-active">

                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th>REMARKS</th>
                <th class="text-right">PO</th>
                <th class="text-right">GRN</th>
                <th class="text-right">AVL</th>
                <th class="text-right">MRP</th>
                <th class="text-right">COST</th>
                <th class="text-right">TOTAL</th>                
        </thead>
        <tbody class="lineitems" >
            <?php
            
            $po = Po::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;

            $list = Yii::app()->db->createCommand("SELECT id FROM po_items WHERE po_id = '" . $data['id'] . "'")->queryAll();
            foreach ($list as $itemArray) {

                $item = PoItems::model()->findByPk($itemArray['id']);
                ?>
                <tr data-id="<?php echo $item->id; ?>" >

                    <td class="selitem"><?php echo $num; ?></td>
                    <td class="selitem"><?php echo $item->items->code; ?></td>
                    <td class="selitem"><?php echo $item->items->item_name; ?></td>
                    <td class="selitem"><?php echo $item->notes; ?></td>
                    <td width="10%" class="text-right"><?php echo $item->qty; ?></td>
                    <td width="10%" class="text-right">
                        <?php 
                        $qty = Yii::app()->db->createCommand("SELECT SUM(qty) AS tot FROM grn_items WHERE po_items_id = '". $item->id ."'")->queryRow();
                        echo !empty($qty['tot']) ? $qty['tot'] : 0; 
                        ?>
                    </td>
                    <td width="10%" class="text-right"><?php echo $item->qty - $qty['tot']; ?></td>
                    <td width="10%" class="text-right"><?php echo sprintf('%0.2f',$item->selling); ?></td>
                    <td width="10%" class="text-right"><?php echo sprintf('%0.2f',$item->cost); ?></td>
                    <td width="10%" class="selitem text-right"><?php echo number_format($item->cost * $item->qty,2); ?></td>
                    
                </tr>
                <?php
                $num += 1;
                $total += $item->cost * $item->qty;
            }
            ?>
        </tbody>
        
        <tr>
            <td colspan="9">Total</td>
            <td class="text-right"><?php echo number_format($total,2); ?></td>
        </tr>
    </table>
</form>