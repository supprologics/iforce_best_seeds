<?php
$devices_id = $_POST['devices_id'];
$datefrom = $_POST['date_from'];
$dateto = $_POST['date_to'];

$device = Device::model()->findByPk($devices_id);
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Daily Movement Report By Sales REP</title>
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
                    <h2 class="report_header">Daily Calls Analyze By Sales REP</h2>
                    <table width="100%">
                        <tr>
                            <td width="10%" style="font-weight: bold;">Device</td>
                            <td>   <?php echo $device->code; ?>  <?php echo $device->name; ?></td>
                        </tr>
                        <tr>
                            <td width="10%" style="font-weight: bold;">Sales REP</td>
                            <td><?php echo $device->rep_name; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Report Period</td>
                            <td><?php echo $datefrom . " to " . $dateto; ?></td>
                        </tr>
                    </table>
                </div>
            </div>


            <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">

                <tr>
                    <th colspan="9">Productive Calls</th>
                </tr>

                <tr>
                    <th></th>
                    <th>CUSTOMER</th>
                    <th>AREA</th>
                    <th style="text-align: right;">LATITUDE</th>
                    <th style="text-align: right;">LONGITUDE</th>
                    <th style="text-align: right;">BAT%</th>
                    <th style="text-align: right;">TIME</th>
                    <th style="text-align: right;">REMARKS</th>
                    <th style="text-align: right;">SALES</th>
                </tr>

                <?php
                $num = 1;

                $list = Yii::app()->db->createCommand("SELECT id FROM invoice WHERE device_id = '$devices_id' AND DATE(created) >= '$datefrom' AND DATE(created) <= '$dateto' ORDER BY created ASC ")->queryAll();

                $marker = "";
                $tot = 0;
                foreach ($list as $value) {


                    $att = Invoice::model()->findByPk($value['id']);
                    ?>

                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $att->customers->name; ?></td>
                        <td><?php echo $att->customers->areas->name; ?></td>
                        <td style="text-align: right;"><?php echo $att->latitude; ?></td>
                        <td style="text-align: right;"><?php echo $att->longitude; ?></td>
                        <td style="text-align: right;"><?php echo $att->battery_level; ?>%</td>
                        <td style="text-align: right;"><?php echo $att->eff_date; ?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"><?php echo number_format($att->invoice_total, 2); ?></td>
                    </tr>
                    <?php
                    $num += 1;
                    $tot += $att->invoice_total;
                    
                    $lat = floatval($att->latitude);
                    $lng = floatval($att->longitude);
//                    if($att->amount > 0){
//                        $colour = 'green';
//                    }else{
//                        $colour = "red";
//                    }                    
                    $colour = "green";
                    if (!empty($lat) && !empty($lng)) {
                        $marker .= "&markers=size:mid%7Ccolor:$colour%7Clabel:$num%7C$lat,$lng";

                        $num += 1;
                    }
                    
                }
                ?>


                <tr>
                    <td colspan="8" style="text-align: right;">TOTAL</td>
                    <td style="text-align: right;"><?php echo number_format($tot, 2); ?></td>
                </tr>    


                <tr>
                    <th colspan="9">Non-Productive Calls</th>
                </tr>

                <?php
                $listCalls = Yii::app()->db->createCommand("SELECt id FROM productivecalls WHERE device_id = '$devices_id' AND DATE(created) >= '$datefrom' AND DATE(created) <= '$dateto' ORDER BY created ASC ")->queryAll();
                foreach ($listCalls as $value) {
                    $call = Productivecalls::model()->findByPk($value['id']);
                    ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $call->customers->name; ?></td>
                        <td><?php echo $call->customers->areas->name; ?></td>
                        <td style="text-align: right;"><?php echo $call->latitude; ?></td>
                        <td style="text-align: right;"><?php echo $call->longitude; ?></td>
                        <td style="text-align: right;"><?php echo $call->bat_level; ?>%</td>
                        <td style="text-align: right;"><?php echo $call->created; ?></td>
                        <td style="text-align: right;"><?php echo $call->remarks; ?></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <?php
                    $lat = floatval($call->latitude);
                    $lng = floatval($call->longitude);
                    
                    $colour = "red";
                    if (!empty($lat) && !empty($lng)) {
                        $marker .= "&markers=size:mid%7Ccolor:$colour%7Clabel:$num%7C$lat,$lng";
                        $num += 1;
                    }
                }
                ?>




            </table>

            <div>
                <img src="https://maps.googleapis.com/maps/api/staticmap?size=1400x512&scale=2&zoom=12&<?php echo $marker; ?>&key=AIzaSyDJz9rO99_oSy4PmgjuzN_-85oUMeWrD8c" style="width: 100%;" />
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

