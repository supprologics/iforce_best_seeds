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
                    <h2 class="report_header">Agency Wise Inventory Report</h2>
                    <table width="100%" class="table">
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td><?php echo $start; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="popularity_div">
                <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">

                    <tr>
                        <th></th>
                        <th>Location</th>
                        <th>Code</th>
                        <th>Agency</th>
                        <th class="text-right">Sellable</th>
                        <th class="text-right">Non-sellable</th>
                        <th class="text-right">Total</th>
                    </tr>

                    <?php
                    $num = 1;
                    
                    $totAll = 0;
                    $totAllNs = 0;
                    
                    
                    foreach ($_POST['devices_id'] as $value) {

                        $device = Device::model()->findByPk($value);
                        
                        ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $device->locations; ?></td>
                            <td><?php echo $device->code; ?></td>
                            <td><?php echo $device->name; ?></td>
                            <td class="text-right">
                                <?php
                                
                                $list = Yii::app()->db->createCommand("SELECT SUM(qty * cost) as tot FROM stock WHERE stock_lot = 1 AND device_id = '". $device->id ."' ")->queryRow();
                                echo number_format($list['tot'],2);
                                
                                ?>
                            </td>
                            <td class="text-right">
                                <?php
                                
                                $listNS = Yii::app()->db->createCommand("SELECT SUM(qty_ns * cost) as tot FROM stock WHERE stock_lot = 1 AND device_id = '". $device->id ."' ")->queryRow();
                                echo number_format($listNS['tot'],2);
                                
                                ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($listNS['tot'] + $list['tot'],2); ?>
                            </td>
                        </tr>

                        <?php
                        $num += 1;
                        $totAll += $list['tot'];
                        $totAllNs += $listNS['tot'];
                    }
                    ?>
                        
                        <tr>
                            <td colspan="4">Total</td>
                            <td class="text-right"><?php echo number_format($totAll,2); ?></td>
                            <td class="text-right"><?php echo number_format($totAllNs,2); ?></td>
                            <td class="text-right"><?php echo number_format($totAll + $totAllNs,2); ?></td>
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

