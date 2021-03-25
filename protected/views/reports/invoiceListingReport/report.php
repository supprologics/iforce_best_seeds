<?php
$ctype_id = $_POST['customer_types_id'];

$datefrom = $_POST['date_from'];
$hourfrom = $_POST['hour_from'];

$dateto = $_POST['date_to'];
$hourto = $_POST['hour_to'];

$fromTime = $datefrom . " $hourfrom:00:00";
$toTime = $dateto . " $hourto:00:00";
?>
<html id="body">
    <head>        
        <title>REPORT - Invoice Listing Report</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
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

        <div class = "report_body" >
            <div class = "row" style = "margin-bottom: 8px;">
                <div class = "col">
                    <h2 class = "report_header">INVOICE LISTING REPORT</h2>
                    <table width = "100%" class = "table">
                        <tr>
                            <td style = "font-weight: bold; width:200px;" >Report Period</td>
                            <td><?php echo $fromTime . " to " . $toTime;?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Type</td>
                            <td><?php echo!empty($ctype_id) ? CustomerTypes::model()->findByPk($ctype_id)->name : "ALL"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="popularity_s">
                <table class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <th></th>
                        <th>DEVICE</th>
                        <th>INVOICE</th>
                        <th>AREA</th>
                        <th>TYPE</th>
                        <th>CUSTOMER</th>
                        <th>DATE</th>
                        <th style="text-align: right;">SALES</th>
                        <th style="text-align: right;">DISCOUNTS</th>
                        <th style="text-align: right;">ADJUSTMENTS</th>
                        <th style="text-align: right;">RETURNS</th>
                        <th style="text-align: right;">TOTAL</th>
                    </tr>

                    <?php
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


                    $num = 1;
                    $list = Yii::app()->db->createCommand("SELECT "
                                    . "invoice.id as ids,"
                                    . "invoice.code AS cod,"
                                    . "invoice.device_id as devid,"
                                    . "invoice.customers_id AS cusid,"
                                    . "invoice_total,invoice_discount,invoice_other_discount,invoice_net_total,invoice_return_total "
                                    . "FROM invoice,customers "
                                    . "WHERE customers.id = invoice.customers_id AND "
                                    . "invoice.eff_date >= '$fromTime' AND "
                                    . "invoice.eff_date <= '$toTime' $dev $customer_types_id "
                                    . "ORDER BY invoice.eff_date ASC ")->queryAll();

                    $salesTot = 0;
                    $discountTot = 0;
                    $otherDiscountTot = 0;
                    $returnTot = 0;
                    $tot = 0;
                    foreach ($list as $value) {


                        $att = Invoice::model()->findByPk($value['ids']);
                        ?>

                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $att->device->code; ?></td>
                            <td><a href="<?php echo Yii::app()->createUrl("invoice/print/" . $att->id); ?>" target="_blank" >INV<?php echo $att->code; ?></a></td>
                            <td><?php echo $att->customers->areas->name; ?></td>
                            <td><?php echo $att->customers->customerTypes->name; ?></td>
                            <td><?php echo $att->customers->name; ?></td>
                            <td><?php echo $att->eff_date; ?></td>
                            <td style="text-align: right;"><?php echo number_format($value['invoice_net_total'] + $value['invoice_discount'], 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format(0 - $value['invoice_discount'], 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format(0 - $value['invoice_other_discount'], 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format(0 - $value['invoice_return_total'], 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($value['invoice_total'], 2); ?></td>
                        </tr>
                        <?php
                        $num += 1;
                        $salesTot += $value['invoice_net_total'] + $value['invoice_discount'];
                        $discountTot += $value['invoice_discount'];
                        $otherDiscountTot += $value['invoice_other_discount'];
                        $returnTot += $value['invoice_return_total'];
                        $tot += $value['invoice_total'];
                    }
                    ?>

                    <tr>
                        <td colspan="7">Total</td>
                        <td style="text-align: right;"><?php echo number_format($salesTot, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format(0 - $discountTot, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format(0 - $otherDiscountTot, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format(0 - $returnTot, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($tot, 2); ?></td>
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