<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" id="excel_export" >

        <thead>
            <tr class="table-active">

                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th>REMARKS</th>
                <th class="text-right">Buffer Lvl.</th>
                <th class="text-right">Avl Stocks.</th>
                <th class="text-right">Diff</th>
                <th class="text-right">Order Qty</th>
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
                    <td width="10%" class="text-right">
                        <?php
                        $stock = BuferStock::model()->findByAttributes(array("items_id" => $item->items->id, "device_id" => $po->device_id));
                        if ($stock != null) {
                            echo $stock->qty;
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td width="10%" class="text-right">
                        <?php
                        $stockavl = Yii::app()->db->createCommand("SELECT SUM(qty) as tot FROM stock WHERE items_id = '" . $item->items->id . "' AND online > 0 ")->queryAll();
                        echo $stockavl[0]['tot'];
                        ?>
                    </td>
                    <td width="10%" class="text-right">
                        <?php
                        if ($stock != null) {
                            echo $stock->qty - $stockavl[0]['tot'];
                        } else {
                            echo "0";
                        }
                        ?>                   
                    </td>
                    <td width="10%" class="text-right"><?php echo $item->qty; ?></td>
                    <td width="10%" class="text-right"><?php echo sprintf('%0.2f', $item->cost); ?></td>
                    <td width="10%" class="selitem text-right"><?php echo number_format($item->cost * $item->qty, 2); ?></td>

                </tr>
    <?php
    $num += 1;
    $total += $item->cost * $item->qty;
}
?>
        </tbody>

        <tr>
            <td colspan="9">Total</td>
            <td class="text-right"><?php echo number_format($total, 2); ?></td>
        </tr>
    </table>
</form>