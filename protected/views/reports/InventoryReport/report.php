<?php
$devices_id = $_POST['devices_id'];
$dateto = $_POST['date_to'];
?>

<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Inventory Report</title>
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

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Inventory Report</h2>
                    <table width="100%">
                        <tr>
                            <td width="20%" style="font-weight: bold;">Effected Date</td>
                            <td><?php echo $dateto; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Device name</td>
                            <td><?php echo Device::model()->findByPk($devices_id)->name; ?></td>
                        </tr>

                    </table>
                </div>
            </div>

            <div id="popularity_s">
                <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <th></th>
                        <th>SUPPLIER</th>
                        <th>BRAND</th>
                        <th>SKU CODE</th>
                        <th>SKU DESCRIPTION</th>
                        <th>AMOUNT</th>
                        <th class="text-right">AVL NON-SELL</th>
                        <th class="text-right">NS VALUATION</th>
                        <th class="text-right">AVL SELL</th>
                        <th class="text-right">S VALUATION</th>

                    </tr>    

                    <?php
                    $num = 1;

                    $stock_lot = $_POST['stocklot'];
                    $tot = 0;
                    $totns = 0;
                    $list = Yii::app()->db->createCommand("SELECT SUM(stock.qty) as tot,SUM(stock.qty_ns) as tot_ns,stock.items_id,stock.cost,stock.suppliers_id,stock.batch_no,stock.expire_date FROM `stock`,items WHERE items.id = stock.items_id AND items.online = 1 AND device_id = $devices_id AND stock_lot = '$stock_lot' AND DATE(stock.created) <= '$dateto'  GROUP BY stock.items_id,suppliers_id ")->queryAll();
                    foreach ($list as $value) {

                        $item = Items::model()->findByPk($value['items_id']);
                        $sup = Suppliers::model()->findByPk($value['suppliers_id']);
                        ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $sup->name; ?></td>
                            <td><?php echo $item->brands->name; ?></td>
                            <td><?php echo $item->code; ?></td>
                            <td><?php echo $item->item_name; ?></td>
                            <td><?php echo $value['cost']; ?></td>
                            <td class="text-right"><?php echo $value['tot_ns']; ?></td>
                            <td class="text-right"><?php echo number_format($value['tot_ns'] * $value['cost'], 2); ?></td>
                            <td class="text-right"><?php echo $value['tot']; ?></td>
                            <td class="text-right"><?php echo number_format($value['tot'] * $value['cost'], 2); ?></td>
                        </tr>

                        <?php
                        $num += 1;
                        $tot += $value['tot'] * $value['cost'];
                        $totns += $value['tot_ns'] * $value['cost'];
                    }
                    ?>

                    <tr>
                        <td colspan="7">Total Valuations</td>
                        <td class="text-right"><?php echo number_format($totns, 2); ?></td>
                        <td></td>
                        <td class="text-right"><?php echo number_format($tot, 2); ?></td>
                    </tr>
                </table>
            </div>

            <div class="row d-print-non">
                <div class="col text-right">
                    <button class="btn btn-sm btn-success d-print-none" onclick="window.print ()">Print <span class="oi oi-print"></span></button>
                    <button class="btn btn-sm btn-warning d-print-none" onclick="exportTableToExcel ('popularity_s')">Export to Excel</button>
                </div>
            </div>

        </div>
    </body>
</html>

