<?php
$date = date("Y-m-d");
?>
<style>

    .box{
        background: white;
        width: 100%;
        min-height: 50px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .box h2{
        font-size: 18px; font-weight: bold;
    }
    .box h3{
        font-size: 14px; font-weight: normal;
    }
    .box h4{
        font-size: 18px; font-weight: bold;
        margin: 1px 0;
    }
</style>

<script>

//    function timedRefresh(timeoutPeriod) {
//        setTimeout("location.reload(true);", timeoutPeriod);
//    }
//
//    window.onload = timedRefresh(50000);


</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<header style="background: #760465; font-weight: bold; font-size: 18px; padding: 12px 0; color: white;">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                EWM DASHBOARD
            </div>
        </div>
    </div>
</header>

<div class="container-fluid" style="background: #d9d9d9;">
    <div class="row">
        <div class="col-sm-4 col-xs-12">
            <div class="box" style="min-height: 255px;">
                <h2>Overall Sales</h2>

                <div class="row" style="margin-top:0px;">
                    <div class="col-xs-12 col-sm-6">

                        <script type="text/javascript">
    google.charts.load('current', {'packages': ['gauge']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

<?php
$today = Yii::app()->db->createCommand("SELECT total FROM invoice WHERE DATE(created) = '$date' AND device_id != 5 GROUP BY created ")->queryAll();
$tottodval = 0;
foreach($today as $value){
    $tottodval += $value['total'];
}


$month = Yii::app()->db->createCommand("SELECT total FROM invoice WHERE "
                . "DATE(created) <= '$date' AND DATE(created) >= '" . date("Y-m-1") . "' AND device_id != 5 GROUP BY created ")->queryAll();
                
$totmtdAll = 0;
foreach($month as $value){
    $totmtdAll += $value['total'];
}                


$tottod = floatval($tottodval);
$totmtdAll = floatval($totmtdAll);
?>

        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['TODAY', <?php echo $tottod; ?>]
        ]);

        var options = {
            max: 150000,
            width: '100%', height: 170,
            redFrom: 0, redTo: 20000,
            yellowFrom: 20000, yellowTo: 45000,
            greenFrom: 45000, greenTo: 150000,
            minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_dixv'));
        chart.draw(data, options);
    }
                        </script>
                        <div id="chart_dixv" style="display: block; width:170px; margin:0 auto; "></div>

                        <h4 style="margin-top: 0px; text-align: center;">
                            Rs <?php echo number_format($tottod, 2); ?>
                        </h4>
                    </div>
                    <div class="col-xs-12 col-sm-6">

                        <script type="text/javascript">
                            google.charts.load('current', {'packages': ['gauge']});
                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {
                                var data = google.visualization.arrayToDataTable([
                                    ['Label', 'Value'],
                                    ['MTD', <?php echo $totmtdAll; ?>]
                                ]);

                                var options = {
                                    max: 800000,
                                    width: '100%', height: 170,
                                    redFrom: 0, redTo: 100000,
                                    yellowFrom: 100000, yellowTo: 450000,
                                    greenFrom: 450000, greenTo: 800000,
                                    minorTicks: 5
                                };

                                var chart = new google.visualization.Gauge(document.getElementById('chart_dix2'));
                                chart.draw(data, options);
                            }
                        </script>
                        <div id="chart_dix2" style="display: block; width:170px; margin:0 auto; "></div>


                        <h4 style="margin-top: 0px; text-align: center;">
                            Rs <?php echo number_format($totmtdAll, 2); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div style="background: white; padding: 15px;">
                <h2 style="font-size: 14px;">Location Wise Sales Details - Today</h2>

                <script>

                    google.charts.load('current', {packages: ['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawMaterial);

                    function drawMaterial() {
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Location');
                        data.addColumn('number', 'Today');

                        data.addRows([
<?php
$list = Yii::app()->db->createCommand("SELECT * FROM user WHERE id != 6")->queryAll();
$data = "";
foreach ($list as $value) {

    $device_id = $value['device_id'];

    $today = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM invoice WHERE DATE(created) = '$date' AND device_id = '$device_id' ")->queryAll();


    $tottoday = floatval($today[0]['tot']);

    $data .= "['" . $value['username'] . "', $tottoday],";
}
echo rtrim($data, ",");
?>
                        ]);

                        var options = {
                            title: 'Location Wise Sales Details',
                            hAxis: {
                                title: 'Locations'
                            },
                            legend: {position: 'none'},
                            vAxis: {
                                title: 'Sales'
                            }
                        };

                        var materialChart = new google.charts.Bar(document.getElementById('chart_div'));
                        materialChart.draw(data, options);
                    }
                </script>
                <div id="chart_div"></div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div style="background: white; padding: 15px;">
                <h2 style="font-size: 14px;">Location Wise Sales Details - Month to Date</h2>
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script>

                    google.charts.load('current', {packages: ['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawMaterial);

                    function drawMaterial() {
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Location');
                        data.addColumn('number', 'MTD');

                        data.addRows([
<?php
$list = Yii::app()->db->createCommand("SELECT * FROM user WHERE id != 6")->queryAll();
$data = "";
foreach ($list as $value) {

    $device_id = $value['device_id'];

    $month = Yii::app()->db->createCommand("SELECT SUM(total) as tot FROM invoice WHERE "
                    . "DATE(created) <= '$date' AND DATE(created) >= '" . date("Y-m-01") . "'  AND device_id = '$device_id'  ")->queryAll();


    $totmtd = floatval($month[0]['tot']);

    $data .= "['" . $value['username'] . "',$totmtd],";
}
echo rtrim($data, ",");
?>
                        ]);

                        var options = {
                            title: 'Location Wise Sales Details',
                            hAxis: {
                                title: "Locations",
                                direction: -1,
                                slantedText: true,
                                slantedTextAngle: 90
                            },
                            legend: {position: 'none'},
                            seriesType: 'bars',
                            vAxis: {
                                title: 'Sales'
                            }
                        };

                        var materialChart = new google.charts.Bar(document.getElementById('chart_div_mtd'));
                        materialChart.draw(data, options);
                    }
                </script>
                <div id="chart_div_mtd"></div>
            </div>
        </div>

    </div>
    <div class="row" style="margin-top: 8px;">
        <div class="col-sm-6">
            <div style="background: white; padding: 15px;">
                <h3 style="font-size: 12px; font-weight: bold;">Outlet Wise Sales Summery</h3>

                <table class="table table-responsive-xl">
                    <tr>
                        <th>Location</th>
                        <th class="text-right">D/Calls</th>
                        <th class="text-right">Daily</th>
                        <th class="text-right">MTD</th>
                    </tr>
                    <?php
                    $totd = 0;
                    $totmtdToral = 0;
                    $totcnt = 0;
                    $list = Yii::app()->db->createCommand("SELECT * FROM user WHERE id != 6")->queryAll();
                    foreach ($list as $value) {

                        $device_id = $value['device_id'];
                        $today = Yii::app()->db->createCommand("SELECT total FROM `invoice` where device_id = '$device_id' AND DATE(eff_date) = '$date' GROUP BY created")->queryAll();
                        $tottod = 0;
                        foreach($today as $val){
                            $tottod += $val['total'];
                        }
                        
                        
                        $month = Yii::app()->db->createCommand("SELECT total FROM invoice WHERE "
                                        . "DATE(created) <= '$date' AND DATE(created) >= '" . date("Y-m-1") . "' AND device_id = '$device_id' GROUP BY created ")->queryAll();
                        $totmtdTH = 0;
                        foreach($month as $val){
                            $totmtdTH += $val['total'];
                        }

                        $totmtdTH = floatval($totmtdTH);
                        
                        
                        $todayCnt = Yii::app()->db->createCommand("SELECT id FROM invoice WHERE DATE(created) = '$date' AND device_id = '$device_id' GROUP BY customers_id ")->queryAll();
                        $dcnt = count($todayCnt);
                        
                        ?>
                        <tr>
                            <td><?php echo $value['username']; ?></td>
                            <td class="text-right"><?php echo number_format($dcnt, 2); ?></td>
                            <td class="text-right"><?php echo number_format($tottod, 2); ?></td>
                            <td class="text-right"><?php echo number_format($totmtdTH, 2); ?></td>
                        </tr>

                        <?php
                        $totd += $tottod;
                        $totmtdToral += $totmtdTH;
                        $totcnt += $dcnt;
                    }
                    ?>

                    <tr>
                        <td>Total</td>
                        <td class="text-right"><?php echo number_format($totcnt, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totd, 2); ?></td>
                        <td class="text-right"><?php echo number_format($totmtdToral, 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6">
            <div id="map" style="width:100%; height: 60vh; margin-top: 0px;"></div>

            <script>

                $(function () {

                    var myVar = setInterval(initMap, 300000);

                });

                // Initialize and add the map
                function initMap() {
                    // The location of Uluru



                    // The map, centered at Uluru
                    var km = 0;
                    var bounds = new google.maps.LatLngBounds();
                    var map = new google.maps.Map(
                            document.getElementById('map'));

<?php
$list = Yii::app()->db->createCommand("SELECT invoice.*,user.username FROM `invoice`,user WHERE user.device_id = invoice.device_id AND latitude != 0 group BY device_id ORDER BY eff_date,id DESC")->queryAll();
$num = 1;
foreach ($list as $value) {

    $lat = floatval($value['latitude']);
    $lng = floatval($value['longitude']);
    $username = $value['username'];
    $sync = $value['sync_time'];

    echo "var infowindow$num = new google.maps.InfoWindow({
                                        content: '<span>$username</span><br/><small>$sync</small>'
                                    });";

    echo "var marker$num = new google.maps.Marker({position: {lat: $lat, lng: $lng}, map: map,title:'Active',icon: {
      url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
}});
    loc = new google.maps.LatLng(marker$num.position.lat(), marker$num.position.lng());
    bounds.extend(loc);";

    echo "google.maps.event.addListener(marker$num, 'click', function () {
                                                        infowindow$num.open(map, marker$num);
                                                    });";

    $num += 1;
}
?>

                    map.fitBounds(bounds);
                    map.panToBounds(bounds);
                }
            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJz9rO99_oSy4PmgjuzN_-85oUMeWrD8c&callback=initMap&libraries=geometry">
            </script>
        </div>
    </div>
</div>
