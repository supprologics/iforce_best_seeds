<?php
$start = $_POST['date_from'];
$date = $_POST['date_to'];
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
                    <h2 class="report_header">Overall Sales Report</h2>
                    <table width="100%" class="table">
                        <tr>
                            <td style="font-weight: bold;">Report Period</td>
                            <td><?php echo $start . " to " . $date; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="popularity_div">
                <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                    <?php
                    if (count($_POST['devices_id']) <= 0) {
                        $deviceID = "0";
                    } else {
                        $deviceID = "";
                        foreach ($_POST['devices_id'] as $value) {
                            $deviceID .= $value . ",";
                        }
                        $deviceID = rtrim($deviceID, ",");
                    }

                    $list = Yii::app()->db->createCommand("SELECT * FROM device WHERE id IN ($deviceID)")->queryAll();
                    ?>

                    <tr>
                        <th rowspan="2">Code</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Rep-Name</th>
                        <?php
                        $listCustomerTypes = CustomerTypes::model()->findAll();
                        foreach ($listCustomerTypes as $value) {
                            echo "<th class='text-center' colspan='2'>" . $value->name . "</th>";
                        }
                        ?>
                        <th rowspan="2" class="text-right">Calls</th>
                        <th rowspan="2" class="text-right">Target</th>
                        <th rowspan="2" class="text-right">Total Sales</th>
                        <th rowspan="2" class="text-right">Achieved %</th>
                    </tr>
                    <tr>
                        <?php
                        $listCustomerTypes = CustomerTypes::model()->findAll();
                        foreach ($listCustomerTypes as $value) {
                            echo "<th class='text-right'>No's Of Calls</th>";
                            echo "<th class='text-right'>Sale Value</th>";
                            $tot[$value->id] = 0;
                            $totSales[$value->id] = 0;
                        }
                        ?>
                    </tr>
                    <?php
                    $totd = 0;
                    $totmtdToral = 0;
                    $totcnt = 0;
                    
                    $totTargets = 0;
                    
                    foreach ($list as $valueDevice) {

                        $device_id = $valueDevice['id'];

                        $month = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice WHERE device_id = '$device_id' AND "
                                        . "DATE(eff_date) <= '$date' AND "
                                        . "DATE(eff_date) >= '$start'")->queryAll();

                        $totmtdTH = 0;
                        foreach ($month as $val) {
                            $totmtdTH += $val['total'];
                        }
                        $totmtdTH = floatval($totmtdTH);
                        ?>
                        <tr>
                            <td><?php echo $valueDevice['code']; ?></td>
                            <td><?php echo $valueDevice['name']; ?></td>
                            <td><?php echo $valueDevice['rep_name']; ?></td>
                            <?php
                            $tots = 0;
                            foreach ($listCustomerTypes as $value) {

                                $ctid = $value->id;
                                $todayCntg = Yii::app()->db->createCommand("SELECT COUNT(invoice.id) tot,SUM(invoice.invoice_total) as totSale FROM invoice,customers "
                                                . "WHERE customers.id = invoice.customers_id AND "
                                                . "customers.customer_types_id = '$ctid' AND "
                                                . "DATE(invoice.eff_date) <= '$date' AND "
                                                . "DATE(invoice.eff_date) >= '$start' AND "
                                                . "invoice.device_id = '$device_id' ")->queryRow();



                                echo "<td class='text-right'>" . $todayCntg['tot'] . "</td>";
                                echo "<td class='text-right'>" . number_format($todayCntg['totSale'], 2) . "</td>";
                                $tot[$ctid] += $todayCntg['tot'];
                                $tots += $todayCntg['tot'];
                                $totSales[$value->id] += $todayCntg['totSale'];
                            }
                            ?>
                            <td class="text-right"><?php echo $tots; ?></td>
                            <td class="text-right"><?php echo number_format($valueDevice['target'],2); ?></td>
                            <td class="text-right"><?php echo number_format($totmtdTH, 2); ?></td>
                            <td class="text-right"><?php echo number_format($totmtdTH/$valueDevice['target'] * 100, 2); ?>%</td>
                        </tr>

                        <?php
                        $totmtdToral += $totmtdTH;
                        $totTargets +=$valueDevice['target'];
                    }
                    ?>

                    <tr>
                        <td colspan="3">Total</td>
                        <?php
                        $alltot = 0;
                        foreach ($listCustomerTypes as $value) {
                            $ctid = $value->id;
                            echo "<td class='text-right'>" . $tot[$ctid] . "</td>";
                            echo "<td class='text-right'>" . number_format($totSales[$ctid], 2) . "</td>";
                            $alltot += $tot[$ctid];
                        }
                        ?>
                        <td class="text-right"><?php echo $alltot; ?></td>
                        <td class="text-right"><?php echo number_format($totTargets,2); ?></td>
                        <td class="text-right"><?php echo number_format($totmtdToral, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totmtdToral/$totTargets * 100, 2); ?>%</td>
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

