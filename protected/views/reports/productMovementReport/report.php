<?php
$ctype_id = $_POST['customer_types_id'];

$datefrom = $_POST['date_from'];
$dateto = $_POST['date_to'];

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Sales Movement Report By Products</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">

        <script>

            function exportTableToExcel(tableID, filename = '') {
               var downloadLink;
               var dataType = 'application/vnd.ms-excel';
               var tableSelect = document.getElementById (tableID);
               var tableHTML = tableSelect.outerHTML.replace (/ /g, '%20');
               filename = filename ? filename + '.xls' : 'report.xls';
               downloadLink = document.createElement ("a");
               document.body.appendChild (downloadLink);

               if (navigator.msSaveOrOpenBlob) {
                  var blob = new Blob (['\ufeff', tableHTML], {
                     type: dataType
                  });
                  navigator.msSaveOrOpenBlob (blob, filename);
               } else {
                  // Create a link to the file
                  downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                  downloadLink.download = filename;
                  downloadLink.click ();
               }
            }

        </script>

    </head>
    <body>

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" style="margin-bottom: 100px;" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Product Movement Report by Products</h2>
                    <table width="100%" class="table">
                        <tr>
                            <td style="font-weight: bold;">Report Period</td>
                            <td><?php echo $datefrom . " to " . $dateto; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Type</td>
                            <td><?php echo!empty($ctype_id) ? CustomerTypes::model()->findByPk($ctype_id)->name : "ALL"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div  id="popularity_div" >
                <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">

                    <tr>
                        <th></th>
                        <th>CODE</th>
                        <th>ITEM</th>
                        <th class="text-right">MRP</th>
                        <th class="text-right" width="8%">SALE</th>
                        <th class="text-right" width="8%">FREE</th>
                        <th class="text-right" width="8%">SAMPLE</th>
                        <th class="text-right" width="8%">RETURN - SELABLE</th>
                        <th class="text-right" width="8%">RETURN - N/SELABLE</th>
                        <th class="text-right" width="8%">DISCOUNTS</th>
                        <th class="text-right" width="8%">GROSS SALE</th>
                    </tr>

                    <?php
                    
                    //FILTERATIONS
                    if (count($_POST['devices_id']) <= 0) {
                        $dev = "";
                    } else {
                        $deviceID = "";
                        foreach ($_POST['devices_id'] as $value) {
                            $deviceID .= $value . ",";
                        }
                        $deviceID = rtrim($deviceID, ",");
                        $dev = " AND device_id IN ($deviceID) ";
                    }
                    
                    

                    if (!empty($_POST['customer_types_id'])) {
                        $customer_types_id = " AND customer_types_id = '" . $_POST['customer_types_id'] . "' ";
                    } else {
                        $customer_types_id = "";
                    }
                                        
                    
                    $list = Yii::app()->db->createCommand("SELECT * FROM items WHERE online >= 0 ORDER BY item_name ASC")->queryAll();

                    $allp = 0;
                    $freetot = 0;
                    $sampletot = 0;

                    $stot = 0;
                    $nstot = 0;

                    $netttot = 0;
                    $disttot = 0;
                    $alltot = 0;
                    $alltotDist = 0;

                    $num = 1;
                    foreach ($list as $value) {

                        $item_id = $value['id'];


                        $qtyTot = Yii::app()->db->createCommand("SELECT SUM(total) as tot,SUM(discount_amount) as distTotal FROM invoice_items WHERE  items_id = '$item_id' $dev $customer_types_id AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' AND item_type = 1 ")->queryRow();
                        $qtyReturnsTot = Yii::app()->db->createCommand("SELECT SUM(total) as tot,SUM(discount_amount) as distTotal FROM invoice_items WHERE items_id = '$item_id' $dev $customer_types_id AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' AND item_type = 2 ")->queryRow();

                        $amount = $qtyTot['tot'] - $qtyReturnsTot['tot'];
                        $amountDist = $qtyTot['distTotal'] - $qtyReturnsTot['distTotal'];

                        if ($amount > 0) {
                            ?>

                            <tr>
                                <td><?php echo $num; ?></td>
                                <td><?php echo $value['code']; ?></td>
                                <td><?php echo $value['item_name']; ?></td>
                                <td class="text-right"><?php echo number_format($value['mrp'],2); ?></td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    $qty = Yii::app()->db->createCommand("SELECT SUM(qty_selable) as tot FROM invoice_items WHERE items_id = '$item_id' AND item_type = 1 AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' $dev $customer_types_id ")->queryRow();
                                    echo $qty['tot'];

                                    $allp += $qty['tot'];
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    $qty = Yii::app()->db->createCommand("SELECT SUM(qty_selable) as tot FROM invoice_items WHERE items_id = '$item_id' AND item_type = 3 AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' $dev $customer_types_id ")->queryRow();
                                    echo $qty['tot'];

                                    $freetot += $qty['tot'];
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    $qty = Yii::app()->db->createCommand("SELECT SUM(qty_selable) as tot FROM invoice_items WHERE items_id = '$item_id' AND item_type = 4 AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' $dev $customer_types_id ")->queryRow();
                                    echo $qty['tot'];
                                    $sampletot += $qty['tot'];
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    $qty = Yii::app()->db->createCommand("SELECT SUM(qty_selable) as tot FROM invoice_items WHERE item_type = 2 AND items_id = '$item_id' $dev $customer_types_id AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' ")->queryRow();
                                    echo !empty(0 - $qty['tot']) ? 0 - $qty['tot'] : "";

                                    $stot += $qty['tot'];
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    $qty = Yii::app()->db->createCommand("SELECT SUM(qty_nonselable) as tot FROM invoice_items WHERE item_type = 2 AND items_id = '$item_id' $dev $customer_types_id AND invoice_items.eff_date >= '$datefrom' AND invoice_items.eff_date <= '$dateto' ")->queryRow();
                                    echo !empty(0 - $qty['tot']) ? 0 - $qty['tot'] : "";

                                    $nstot += $qty['tot'];
                                    ?>
                                </td>    
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    echo number_format($amountDist, 2);
                                    $alltotDist += $amountDist;
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    //QTY
                                    echo number_format($amount, 2);
                                    $alltot += $amount;
                                    ?>
                                </td>
                            </tr>

                            <?php
                            $num += 1;
                        }
                    }
                    ?>


                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right"><?php echo $allp; ?></td>
                        <td class="text-right"><?php echo $freetot; ?></td>
                        <td class="text-right"><?php echo $sampletot; ?></td>
                        <td class="text-right"><?php echo  0 -$stot; ?></td>
                        <td class="text-right"><?php echo 0 - $nstot; ?></td>
                        <td class="text-right"><?php echo number_format($alltotDist, 2); ?></td>
                        <td class="text-right"><?php echo number_format($alltot, 2); ?></td>
                    </tr>

                </table>
            </div>
            <div class="row d-print-non">
                <div class="col text-right">
                    <button class="btn btn-sm btn-success d-print-none" onclick="window.print ()">Print <span class="oi oi-print"></span></button>
                    <button class="btn btn-sm btn-warning d-print-none" onclick="exportTableToExcel ('popularity_div')">Export to Excel</button>
                </div>
            </div>
        </div>


    </body>
</html>

