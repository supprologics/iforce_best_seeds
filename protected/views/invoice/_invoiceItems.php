<form action="#" method="post" id="inner_table">
    <table class="table table-sm table-hover table-sp" >



        <?php
        $model = Invoice::model()->findByPk($data['id']);

        $listCategory = Brands::model()->findAllByAttributes(array("is_dashbaord" => 1));

        $grandTotal = 0;
        foreach ($listCategory as $valueCategory) {
            ?>

            <thead>
                <tr>
                    <th colspan="9"><?php echo $valueCategory->name; ?></th>
                </tr>
            </thead>

            <thead>
                <tr class="table-active">

                    <th><input type="checkbox" id="selectall"></th>
                    <th></th>
                    <th>CODE</th>
                    <th>DESCRIPTION</th>
                    <th>TYPE</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">MRP Rs.</th>
                    <th class="text-right">DIST%</th>
                    <th class="text-right">TOTAL</th>   
                </tr>
            </thead>


            <tbody class="lineitems" >
                <?php
                $category_id = $valueCategory->id;

                $total = 0;
                $totDiscount = 0;
                $num = 1;

                $code = $model->code;
                $device_id = $model->device_id;

                $list = Yii::app()->db->createCommand("SELECT invoice_items.id FROM invoice_items,items WHERE items.id = invoice_items.items_id AND items.brands_id = $category_id AND invoice_code = '$code' AND device_id = '$device_id' ")->queryAll();
                foreach ($list as $itemArray) {

                    $item = InvoiceItems::model()->findByPk($itemArray['id']);

                    if ($item->item_type == 3) {
                        $redonly = " readonly";
                        $cls = "class='table-warning'";
                    } else {
                        $redonly = "";
                        $cls = "";
                    }
                    ?>
                    <tr data-id="<?php echo $item->id; ?>"  <?php echo $cls; ?> >

                        <td><input id="line_<?php echo $item->id; ?>" type="checkbox" class="chk" value="<?php echo $item->id; ?>" /></td>
                        <td class="selitem"><?php echo $num; ?></td>
                        <td class="selitem"><?php echo $item->items->code; ?></td>
                        <td class="selitem"><?php echo $item->items->item_name; ?></td>
                        <td class="selitem">
                            <?php
                            if ($item->item_type == 1) {
                                echo "";
                            } elseif ($item->item_type == 3) {
                                echo "FREE";

                                $item->mrp = 0;
                                $item->dist_val = 0;
                                $item->discount_amount = 0;
                            }
                            ?>
                        </td>
                        <td width="10%" class="text-right p-0"><input  type="text" name="qty[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo $item->qty_selable; ?>" /></td>
                        <td width="10%" class="text-right p-0"><input <?php echo $redonly; ?> type="text" name="mrp[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo sprintf('%0.2f', $item->mrp); ?>" /></td>
                        <td width="10%" class="text-right p-0"><input <?php echo $redonly; ?> type="text" name="dist[<?php echo $item->id ?>]" autocomplete="off" class="form-control text-right form-control-sm" value="<?php echo sprintf('%0.2f', $item->dist_val); ?>" /></td>

                        <td width="10%" class="selitem text-right"><?php echo number_format($item->mrp * $item->qty_selable, 2); ?></td>

                    </tr>
                    <?php
                    $num += 1;
                    $total += $item->mrp * $item->qty_selable;
                    $totDiscount += $item->discount_amount;
                }
                ?>


                <tr>
                    <td colspan="8" class="text-right">Gross Total</td>
                    <td width="10%" class="selitem text-right"><?php echo number_format($total, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="8" class="text-right">Discount Amount</td>
                    <td width="10%" class="selitem text-right">-<?php echo number_format($totDiscount, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="8" class="text-right">Nett Amount</td>
                    <td width="10%" class="selitem text-right"><?php echo number_format($total - $totDiscount, 2); ?></td>
                </tr>



            </tbody>

            <?php
            $grandTotal += $total - $totDiscount;
        }
        ?>


        <tr>
            <td colspan="8" class="text-right">Final Total</td>
            <td width="10%" class="selitem text-right"><?php echo number_format($grandTotal, 2); ?></td>
        </tr>
        
        
        
        <?php
        
        for($i=0;$i <= 100;$i++){
            ?>
        
            <tr>
            <td colspan="8" class="text-right">Final Total</td>
            <td width="10%" class="selitem text-right"><?php echo number_format($grandTotal, 2); ?></td>
        </tr>
            <?php
            
        }
        
        ?>
        
        


    </table>
</form>