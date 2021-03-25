<div style="background: white; padding: 15px;">

            <script>

                google.charts.load ('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback (drawVisualization);


                function drawVisualization () {

                   var data = google.visualization.arrayToDataTable ([
                      ['Location', 'Today', 'MTD', 'TARGET'],
<?php
$list = Yii::app()->db->createCommand("SELECT * FROM device WHERE online = 1 $dev")->queryAll();
$data = "";
foreach ($list as $value) {

    $device_id = $value['id'];
    $target = floatval($value['target']);
    $today = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM invoice_items,invoice "
                    . "WHERE invoice.code= invoice_items.invoice_code AND "
                    . "invoice.customers_id = invoice_items.customers_id AND "
                    . "DATE(invoice.created) = '$date' AND "
                    . "invoice_items.device_id = '$device_id' ")->queryAll();

    $tottoday = intval($today[0]['tot']);
    //MTD
    $month = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM invoice_items,invoice "
                    . "WHERE invoice.code= invoice_items.invoice_code AND "
                    . "invoice.customers_id = invoice_items.customers_id AND "
                    . "DATE(invoice.created) <= '$date' AND "
                    . "DATE(invoice.created) >= '$start' AND "
                    . "invoice_items.device_id = '$device_id'  ")->queryAll();

    $totmtd = intval($month[0]['tot']);
    $data .= "['" . $value['code'] . "', $tottoday,$totmtd,$target],";
}
echo rtrim($data, ",");
?>
                   ]);


                   var options = {
                      title: 'Location Wise Sales Details',
                      vAxis: {title: 'Targets'},
                      hAxis: {title: 'Locations'},
                      seriesType: 'area',
                      series: {2: {type: 'line'}}};

                   var chart = new google.visualization.ComboChart (document.getElementById ('chart_div'));
                   chart.draw (data, options);

                }
            </script>

            <div id="chart_div" style="height:250px;"></div>


        </div>