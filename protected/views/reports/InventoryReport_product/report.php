<?php
$start = $_POST['date_from'];
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Invoice Listing Report</title>
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
    <body style="margin-bottom: 100px;">

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Agency Wise Inventory Report By Products</h2>
                    <table width="100%" class="table">
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td><?php echo $start; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Device</td>
                            <td>
                                <?php
                                foreach ($_POST['devices_id'] as $value) {
                                    $device = Device::model()->findByPk($value);
                                    echo $device->name . ", ";
                                }
                                ?>

                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="popularity_div">
                <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">

                    <tr>
                        <th></th>
                        <th>BRAND</th>
                        <th>CODE</th>
                        <th>ITEM DESCRIPTION</th>     
                        <th class="text-right">QTY</th>
                        <th class="text-right">SELLABLE</th>
                        <th class="text-right">QTY/NS</th>
                        <th class="text-right">NON-SELLABLE</th>
                        <th class="text-right">TOTAL</th>
                    </tr>

                    <?php
                    $num = 1;

                    $totAll = 0;
                    $totAllNs = 0;
                    
                    $totQtyAll = 0;
                    $totQtyAllNs = 0;

                    $items = Items::model()->findAll();

                    $devices_id = "";
                    foreach ($_POST['devices_id'] as $value) {
                        $devices_id .= "$value,";
                    }
                    $devices_id = rtrim($devices_id, ",");

                    foreach ($items as $value) {
                        $items_id = $value->id;
                        ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $value->brands->name; ?></td>
                            <td><?php echo $value->code; ?></td>
                            <td><?php echo $value->item_name; ?></td>

                            <td class="text-right">
                                <?php
                                $list = Yii::app()->db->createCommand("SELECT SUM(qty * cost) as tot,SUM(qty) as qtytot FROM stock WHERE items_id = '$items_id' AND stock_lot = 1 AND device_id IN ($devices_id) ")->queryRow();
                                echo number_format($list['qtytot'], 2);
                                ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($list['tot'], 2);  ?>
                            </td>
                            <td class="text-right">
                                <?php
                                $listNS = Yii::app()->db->createCommand("SELECT SUM(qty_ns * cost) as tot,SUM(qty_ns) as qtytot_ns FROM stock WHERE items_id = '$items_id' AND stock_lot = 1 AND device_id IN ($devices_id) ")->queryRow();
                                echo number_format($listNS['qtytot_ns'], 2);
                                ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($listNS['tot'], 2);  ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($listNS['tot'] + $list['tot'], 2); ?>
                            </td>
                        </tr>

                        <?php
                        $num += 1;
                        $totAll += $list['tot'];
                        $totAllNs += $listNS['tot'];
                        
                        $totQtyAll += $list['qtytot'];
                        $totQtyAllNs += $listNS['qtytot_ns'];
                        
                    }
                    ?>

                    <tr>
                        <td colspan="4">Total</td>
                        <td class="text-right"><?php echo number_format($totQtyAll, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totAll, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totQtyAllNs, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totAllNs, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totAll + $totAllNs, 2); ?></td>
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

