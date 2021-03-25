<form action="#" method="post" id="inner_table">
    <div id="inner_table_box">
    <table class="table table-sm table-hover table-sp" >

        <thead>
            <tr class="table-active">

                <th></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th>BATCH</th>
                <th>EXPIRE</th>
                <th class="text-right">QTY</th>
                <th class="text-right">MRP Rs.</th>
                <th class="text-right">TOTAL</th>                
        </thead>
        <tbody id="inner_table_d" class="lineitems" >
            <?php
            $po = Tn::model()->findByPk($data['id']);
            $total = 0;
            $num = 1;

            $stock_lot = 1;
            $list = Yii::app()->db->createCommand("SELECT id FROM tn_items WHERE tn_id = '" . $data['id'] . "'")->queryAll();
            foreach ($list as $itemArray) {

                $item = TnItems::model()->findByPk($itemArray['id']);

                $qty = Yii::app()->db->createCommand("SELECT SUM(qty) as tot FROM stock WHERE items_id = '" . $item->items_id . "' AND "
                                . "batch_no ='" . $item->batch_no . "' AND "
                                . "selling = '" . $item->selling . "' AND "
                                . "expire_date = '" . $item->expire_date . "' AND "
                                . "stock_lot = $stock_lot ")->queryAll();

                $maxQty = $item->qty + $qty[0]['tot'];
                ?>
            <tr  data-id="<?php echo $item->id; ?>" >

                    <td class=""><?php echo $num; ?></td>
                    <td class=""><?php echo $item->items->code; ?></td>
                    <td class=""><?php echo $item->items->item_name; ?></td>
                    <td width="7%" class="" >
                        <?php echo $item->batch_no; ?>
                    </td>
                    <td width="7%" class="" >
                        <?php echo $item->expire_date; ?>
                    </td>
                    <td  width="7%" class="text-right ">
                        <?php echo $item->qty; ?>
                    </td>
                    <td width="7%" class="text-right"><?php echo sprintf('%0.2f', $item->selling); ?></td>
                    <td class=" text-right"><?php echo number_format($item->selling * $item->qty, 2); ?></td>

                </tr>
                <?php
                $num += 1;
                $total += $item->selling * $item->qty;
            }
            ?>
        </tbody>

        <tr>
            <td colspan="7">Total</td>
            <td class="text-right"><?php echo number_format($total, 2); ?></td>
        </tr>
    </table>
    </div>
</form>