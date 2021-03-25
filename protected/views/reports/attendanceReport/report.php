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
                    <h2 class="report_header">Sales Team Attendance Report</h2>
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
                        <th></th>
                        <th>Code</th>
                        <th>Device</th>
                        <th>Rep-Name</th>
                        <th>LOG-IN Route</th>
                        <th>Distance</th>
                        <th class="text-center">IN</th>
                        <th>LOG-IN</th>
                        <th>LOG-OUT</th>
                        <th class="text-center">OUT</th>
                        <th>Distance</th>
                        <th>LOG-OUT Route</th>
                        <th>Remarks</th>
                    </tr>


                    <?php
                    $num = 1;
                    foreach ($list as $valueDevice) {
                        
                        $device_id = $valueDevice['id'];
                        $loginIN = Yii::app()->db->createCommand("SELECT id FROM session_logins WHERE device_id = '$device_id' AND DATE(created) = '$start' AND log_type = 1 ORDER BY created ASC LIMIT 1")->queryRow();
                        $loginOUT = Yii::app()->db->createCommand("SELECT id FROM session_logins WHERE device_id = '$device_id' AND DATE(created) = '$start' AND log_type = 2 ORDER BY created DESC LIMIT 1")->queryRow();
                        
                        
                        $sessionIn = SessionLogins::model()->findByPk($loginIN['id']);
                        $sessionOut = SessionLogins::model()->findByPk($loginOUT['id']);
                        
                        ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $valueDevice['code']; ?></td>
                            <td><?php echo $valueDevice['name']; ?></td>
                            <td><?php echo $valueDevice['rep_name']; ?></td>
                            <td><?php echo !empty($sessionIn) ? $sessionIn->areas->name : ""; ?></td>
                            <td><?php echo !empty($sessionIn) ? $this->distance($valueDevice['lat'],$valueDevice['lng'], $sessionIn->latitude, $sessionIn->longitude, "K") : ""; ?></td>
                            <td class="text-center"><?php echo !empty($sessionIn) ? "<a href='https://www.google.com/maps/dir/". $sessionIn->latitude .",". $sessionIn->longitude ."/". $valueDevice['lat'] .",". $valueDevice['lng'] ."/@". $sessionIn->latitude .",". $sessionIn->longitude .",14z/data=!3m1!4b1!4m2!4m1!3e0' target='_blank'>MAP</a>": ""; ?></td>
                            <td><?php echo !empty($sessionIn) ? $sessionIn->created : ""; ?></td>
                            <td><?php echo !empty($sessionOut) ? $sessionOut->created : ""; ?></td>
                            <td class="text-center"><?php echo !empty($sessionOut) ? "<a href='https://maps.google.com/?q=". $sessionOut->latitude .",". $sessionOut->longitude ."' target='_blank'>MAP</a>": ""; ?></td>
                            <td><?php echo !empty($sessionIn) ? $this->distance($valueDevice['lat'],$valueDevice['lng'], $sessionOut->latitude, $sessionOut->longitude, "K") : ""; ?></td>
                            
                            <td><?php echo !empty($sessionOut) ? $sessionOut->areas->name : ""; ?></td>
                            <td><?php echo !empty($sessionOut) ? $sessionOut->reason : ""; ?></td>
                        </tr>


                        <?php
                        
                        $num += 1;
                    }
                    ?>



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

