<?php
ini_set('max_execution_time', 120);
$date = date("Y-m-d");
$start = date("Y-m-1");
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

    .clk_tr{
        cursor: pointer;
    }
</style>

<script>

//    function timedRefresh(timeoutPeriod) {
//        setTimeout("location.reload(true);", timeoutPeriod);
//    }
//
//    window.onload = timedRefresh(50000);

    $ (document).on ("click", ".clk_tr", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#body_" + id).fadeToggle ();

    });

    $ (document).on ("click", ".clk_tr_spl", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#spl_body_" + id).fadeToggle ();

    });
</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>



<div class="row align-items-center">
    <div class="col-sm-12 col-md-3 col-xs-12">
        <div class="box" style="min-height: 255px;">
            <h2>Overall Sales</h2>

            <div class="row" style="margin-top:0px;">
                <div class="col-xs-12 col-sm-6">

                    <script type="text/javascript">
    google.charts.load ('current', {'packages': ['gauge']});
    google.charts.setOnLoadCallback (drawChart);

    function drawChart () {

<?php
$device_ids = $this->returnDevice();

if(empty($device_ids)){
    $this->redirect("device");
}

$dev = " AND device_id IN ($device_ids) ";
$devTabel = " AND device.id IN ($device_ids) ";

//DEVICE ID 5 = TEST
$maxDevTargets = Yii::app()->db->createCommand("SELECT SUM(target) as tot FROM device WHERE id In ($device_ids)")->queryRow();
$MTD_target = floatval($maxDevTargets['tot']);
$D_target = $MTD_target / 25;



$today = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice WHERE DATE(eff_date) = '$date' $dev")->queryRow();
$monthtodate = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice WHERE DATE(eff_date) >= '$start' AND DATE(eff_date) <= '$date' $dev")->queryRow();

$today_val = round($today['total'], 0);
$mtd_val = round($monthtodate['total'], 0);


?>

       var data = google.visualization.arrayToDataTable ([
          ['Label', 'Value'],
          ['TODAY', <?php echo $today_val; ?>]
       ]);

       var options = {
          max: <?php echo $D_target; ?>,
          width: '100%', height: 170,
          redFrom: 0, redTo: <?php echo $D_target / 2; ?>,
          yellowFrom: <?php echo $D_target * 0.5 + 1; ?>, yellowTo: <?php echo $D_target * 0.75; ?>,
          greenFrom: <?php echo $D_target * 0.75 + 1; ?>, greenTo: <?php echo $D_target; ?>,
          minorTicks: 5
       };

       var chart = new google.visualization.Gauge (document.getElementById ('chart_dixv'));
       chart.draw (data, options);
    }
                    </script>
                    <div id="chart_dixv" style="display: block; width:170px; margin:0 auto; "></div>

                    <h4 style="margin-top: 0px; text-align: center;">
                        Rs <?php echo $this->byMillions($today_val); ?>
                    </h4>
                </div>
                <div class="col-xs-12 col-sm-6">

                    <script type="text/javascript">
                        google.charts.load ('current', {'packages': ['gauge']});
                        google.charts.setOnLoadCallback (drawChart);

                        function drawChart () {
                           var data = google.visualization.arrayToDataTable ([
                              ['Label', 'Value'],
                              ['MTD', <?php echo $mtd_val; ?>]
                           ]);

                           var options = {
                              max: <?php echo $MTD_target; ?>,
                              width: '100%', height: 170,
                              redFrom: 0, redTo: <?php echo $MTD_target * 0.5; ?>,
                              yellowFrom: <?php echo $MTD_target * 0.5 + 1; ?>, yellowTo: <?php echo $MTD_target * 0.75; ?>,
                              greenFrom: <?php echo $MTD_target * 0.75 + 1; ?>, greenTo: <?php echo $MTD_target; ?>,
                              minorTicks: 5
                           };

                           var chart = new google.visualization.Gauge (document.getElementById ('chart_dix2'));
                           chart.draw (data, options);
                        }
                    </script>
                    <div id="chart_dix2" style="display: block; width:170px; margin:0 auto; "></div>

                    <h4 style="margin-top: 0px; text-align: center;">
                        Rs <?php echo $this->byMillions($mtd_val); ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-3 col-xs-12">
        <div class="row ">
            <div class="col-sm-6 col-xs-12">
                <div class="box" style="width: 100%; text-align: center;">
                    <h3>Day Target Achievement</h3>
                    <h2><?php echo round($today_val / $D_target * 100, 2) ?> %</h2>
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="box" style="width: 100%; text-align: center; ">
                    <h3>Month Target Achievement</h3>
                    <h2><?php echo round($mtd_val / $MTD_target * 100, 2) ?> %</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <div class="box" style="width: 100%; text-align: center;">
                    <h3>Primary Sales MTD</h3>
                    <h2>
                        <?php
                        $dataPry = Yii::app()->db->createCommand("SELECT SUM(qty * cost) as tot FROM `grn`,grn_items WHERE DATE(eff_date) >= '$start' AND DATE(eff_date) <= '$date' AND grn.id = grn_items.grn_id AND grn.online = 3 $dev")->queryRow();
                        echo $this->byMillions($dataPry['tot']);
                        ?>
                    </h2>
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="box" style="width: 100%; text-align: center;">
                    <h3>Stock In Hand</h3>
                    <h2>
                        <?php
                        $dataPry = Yii::app()->db->createCommand("SELECT SUM(qty * cost) as tot FROM stock WHERE online > 0 $dev")->queryRow();
                        echo $this->byMillions($dataPry['tot']);
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-xs-12">
        <div style="background: white; padding: 15px;">

            <script>

                google.charts.load ('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback (drawVisualization);


                function drawVisualization () {

                   var data = google.visualization.arrayToDataTable ([
                      ['Location', 'MTD', 'TARGET'],
<?php
$list = Yii::app()->db->createCommand("SELECT * FROM device WHERE online = 1 $devTabel")->queryAll();
$data = "";


foreach ($list as $value) {

    $device_id = $value['id'];
    $target = floatval($value['target']);

    //MTD
    $month = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot FROM invoice "
                    . "WHERE DATE(eff_date) <= '$date' AND "
                    . "DATE(eff_date) >= '$start' AND "
                    . "invoice.device_id = '$device_id'  ")->queryAll();

    $totmtd = intval($month[0]['tot']);
    $data .= "['" . $value['code'] . "',$totmtd,$target],";
}
echo rtrim($data, ",");
?>
                   ]);


                   var options = {
                      title: 'Location Wise Sales Details',
                      vAxis: {title: 'Targets'},
                      hAxis: {title: 'Locations'},
                      seriesType: 'area',
                      series: {1: {type: 'line'}}};

                   var chart = new google.visualization.ComboChart (document.getElementById ('chart_div'));
                   chart.draw (data, options);

                }
            </script>

            <div id="chart_div" style="height:250px;"></div>


        </div>
    </div>
</div>


<div class="row" style="margin-top: 8px;">
    <div class="col-sm-12">
        <div style="background: white; padding: 15px;">
            <h3 style="font-size: 12px; font-weight: bold;">Outlet Wise Sales Summery</h3>

            <table class="table table-responsive-xl">
                <tr>
                    <th>Location</th>
                    <th class="text-right">D/Calls</th>
                    <th class="text-right">D/Sales</th>
                    <th class="text-right">D/Target</th>
                    <th class="text-right">D/Balance</th>

                    <?php
                    $listCustomerTypes = CustomerTypes::model()->findAll();
                    foreach ($listCustomerTypes as $value) {
                        echo "<th class='text-right'>M/" . $value->name . "</th>";
                        $grandtot[$value->id] = 0;
                    }
                    ?>
                    <th class="text-right">M/Calls</th>
                    <th class="text-right">M/NP Calls</th>

                    <th class="text-right">M/Total</th>
                    <th class="text-right">M/Target</th>
                    <th class="text-right">M/Balance</th>
                </tr>
                <?php
                $regions_list = Region::model()->findAll();

//GRAND TOTAL KEYS
                $AllTargetTot = 0;
                $AllDayTarget = 0;
                $Alltotcnt = 0;
                $Alltotd = 0;
                $AlltotmtdToral = 0;
                $ALLNP = 0;


                $precentageArray = array();
                $precentageArrayMonth = array();

                foreach ($regions_list as $region) {

                    $reg_id = $region->id;

                    foreach ($listCustomerTypes as $value) {
                        $tot[$reg_id][$value->id] = 0;
                    }

                    $totd[$reg_id] = 0;
                    $totdNP[$reg_id] = 0;
                    $totmtdToral[$reg_id] = 0;
                    $totcnt[$reg_id] = 0;
                    $ld[$reg_id] = 0;


                    $list = Yii::app()->db->createCommand("SELECT * FROM device WHERE region_id = '$reg_id' AND online = 1 $devTabel")->queryAll();

                    echo "<tbody id='body_$reg_id' style='display:none;'>";


                    $daytargettot[$reg_id] = 0;
                    $targettot[$reg_id] = 0;



                    foreach ($list as $value) {

                        $device_id = $value['id'];
                        $target = $value['target'];
                        $daytarget = round($target / 25, 2);

                        $last_day = date("Y-m-d", strtotime($date . " -1 day"));
                        $last_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -1 day"));



                        $lastday = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice "
                                        . "WHERE device_id = '$device_id' AND "
                                        . "invoice.created <= '$last_time' AND "
                                        . "DATE(invoice.eff_date) = '$last_day' ")->queryAll();

                        $today = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice "
                                        . "WHERE device_id = '$device_id' AND "
                                        . "DATE(eff_date) = '$date' ")->queryAll();


                        $tottod = 0;
                        foreach ($today as $val) {
                            $tottod += $val['total'];
                        }


                        $month = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total FROM invoice "
                                        . "WHERE device_id = '$device_id' AND "
                                        . "DATE(eff_date) <= '$date' AND "
                                        . "DATE(eff_date) >= '$start'")->queryAll();


                        $totmtdTH = 0;
                        foreach ($month as $val) {
                            $totmtdTH += $val['total'];
                        }

                        $totmtdTH = floatval($totmtdTH);

                        $todayCnt = Yii::app()->db->createCommand("SELECT id FROM invoice WHERE DATE(created) = '$date' AND device_id = '$device_id' GROUP BY customers_id ")->queryAll();
                        $dcnt = count($todayCnt);
                        ?>
                        <tr class="table-info" >
                            <td><?php echo $value['code']; ?> - <?php echo $value['name']; ?></td>
                            <td class="text-right"><?php echo number_format($dcnt); ?></td>
                            <td class="text-right">
                                <?php
                                if (!empty($daytarget)) {
                                    $hour = $daytarget / 9;
                                } else {
                                    $hour = 0;
                                }
                                $curHour = date("H");

                                if ($curHour >= 17) {
                                    $curHour = 9;
                                } else {
                                    $curHour = $curHour - 8;
                                }

                                $targetToBe = $hour * $curHour;
                                $curSale = $tottod;


                                if ($targetToBe < $curSale) {
                                    echo '<span class="d-inline oi oi-arrow-top text-success">' . number_format($curSale) . ' </span>';
                                } else {
                                    echo '<span class="d-inline oi oi-arrow-bottom text-danger">' . number_format($curSale) . '</span>';
                                }


                                if (!empty($daytarget)) {
                                    $presentage = round($tottod / $daytarget * 100, 2);
                                } else {
                                    $presentage = 0;
                                }

                                $precentageArray[] = array('device_id' => $device_id, "presentage" => $presentage);
                                ?>
                            </td>
                            <td class="text-right"><?php echo number_format($daytarget); ?></td>
                            <td class="text-right"><?php echo number_format($daytarget - $tottod); ?></td>
                            <?php
                            $tots = 0;
                            foreach ($listCustomerTypes as $value) {

                                $ctid = $value->id;
                                $todayCntg = Yii::app()->db->createCommand("SELECT invoice.id FROM invoice,customers "
                                                . "WHERE customers.id = invoice.customers_id AND "
                                                . "customers.customer_types_id = '$ctid' AND "
                                                . "DATE(invoice.created) <= '$date' AND "
                                                . "DATE(invoice.created) >= '$start' AND "
                                                . "invoice.device_id = '$device_id' ")->queryAll();

                                $dcntg = count($todayCntg);

                                echo "<td class='text-right'>$dcntg</td>";
                                $tot[$reg_id][$ctid] += $dcntg;
                                $tots += $dcntg;
                            }
                            ?>
                            <td class="text-right"><?php echo $tots; ?></td>
                            <td class="text-right">
                                <?php 
                                
                                $npqty = Yii::app()->db->createCommand("SELECT count(id) as count FROM `productivecalls` WHERE DATE(created) >= '$start' AND DATE(created) <= '$date' AND device_id = $device_id")->queryRow();
                                echo $npqty['count']; 
                                
                                ?>
                            
                            </td>
                            <td class="text-right">
                                <?php
                                $dayToBeTarget = $target / 25;
                                $curDay = date("d");

                                if ($curDay >= 25) {
                                    $curDay = 25;
                                }

                                $targetToBe = $dayToBeTarget * $curDay;
                                $curSale = $totmtdTH;


                                if ($targetToBe < $curSale) {
                                    echo '<span class="d-inline oi oi-arrow-top text-success">' . number_format($curSale) . ' </span>';
                                } else {
                                    echo '<span class="d-inline oi oi-arrow-bottom text-danger">' . number_format($curSale) . '</span>';
                                }

                                if (!empty($target)) {
                                    $presentageMonth = round($totmtdTH / $target * 100, 2);
                                } else {
                                    $presentageMonth = 0;
                                }

                                $precentageArrayMonth[] = array('device_id' => $device_id, "presentage" => $presentageMonth);
                                ?>
                            </td>
                            <td class="text-right"><?php echo number_format($target); ?></td>
                            <td class="text-right"><?php echo number_format($target - $totmtdTH); ?></td>
                        </tr>

                        <?php
                        
                        $totdNP[$reg_id] += $npqty['count'];
                        $totd[$reg_id] += $tottod;
                        $totmtdToral[$reg_id] += $totmtdTH;
                        $totcnt[$reg_id] += $dcnt;
                        $ld[$reg_id] += $lastday[0]['total'];

                        $daytargettot[$reg_id] += $daytarget;
                        $targettot[$reg_id] += $target;
                    }
                    echo "</tbody>";
                    ?>

                    <tr data-id="<?php echo $reg_id; ?>" class="clk_tr">
                        <td><?php echo $region->name; ?></td>
                        <td class="text-right"><?php echo number_format($totcnt[$reg_id]); ?></td>

                        <td class="text-right" width="150px">
                            <?php
                            $hour = $daytargettot[$reg_id] / 9;
                            $curHour = date("H");

                            if ($curHour >= 17) {
                                $curHour = 9;
                            } else {
                                $curHour = $curHour - 8;
                            }

                            $targetToBe = $hour * $curHour;
                            $curSale = $totd[$reg_id];


                            if ($targetToBe < $curSale) {
                                echo '<span class="d-inline oi oi-arrow-top text-success">' . number_format($curSale) . ' </span>';
                            } else {
                                echo '<span class="d-inline oi oi-arrow-bottom text-danger">' . number_format($curSale) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="text-right"><?php echo number_format($daytargettot[$reg_id]); ?></td>
                        <td class="text-right"><?php echo number_format($daytargettot[$reg_id] - $totd[$reg_id]); ?></td>
                        <?php
                        $alltot = 0;
                        foreach ($listCustomerTypes as $value) {
                            $ctid = $value->id;
                            echo "<td class='text-right'>" . $tot[$reg_id][$ctid] . "</td>";
                            $alltot += $tot[$reg_id][$ctid];
                            $grandtot[$ctid] += $tot[$reg_id][$ctid];
                        }
                        ?>
                        <td class="text-right"><?php echo $alltot; ?></td>
                        <td class="text-right"><?php echo $totdNP[$reg_id]; ?></td>
                        <td class="text-right">
                            <?php
                            $dayToBeTarget = $targettot[$reg_id] / 25;
                            $curDay = date("d");

                            if ($curDay >= 25) {
                                $curDay = 25;
                            }

                            $targetToBe = $dayToBeTarget * $curDay;
                            $curSale = $totmtdToral[$reg_id];


                            if ($targetToBe < $curSale) {
                                echo '<span class="d-inline oi oi-arrow-top text-success">' . number_format($curSale) . ' </span>';
                            } else {
                                echo '<span class="d-inline oi oi-arrow-bottom text-danger">' . number_format($curSale) . '</span>';
                            }
                            ?>
                        </td>

                        <td class="text-right"><?php echo number_format($targettot[$reg_id]); ?></td>
                        <td class="text-right"><?php echo number_format($targettot[$reg_id] - $totmtdToral[$reg_id]); ?></td>
                    </tr>

                    <?php
                    
                    $ALLNP += $totdNP[$reg_id];
                    $Alltotcnt += $totcnt[$reg_id];
                    $Alltotd += $totd[$reg_id];
                    $AlltotmtdToral += $totmtdToral[$reg_id];
                    $AllTargetTot += $targettot[$reg_id];
                    $AllDayTarget += $daytargettot[$reg_id];
                }
                ?>

                <tr class="table-active">
                    <td>All Total</td>
                    <td class="text-right"><?php echo number_format($Alltotcnt); ?></td>
                    <td class="text-right"><?php echo number_format($Alltotd); ?></td>
                    <td class="text-right"><?php echo number_format($AllDayTarget); ?></td>
                    <td class="text-right"><?php echo number_format($AllDayTarget - $Alltotd); ?></td>
                    <?php
                    $alltot = 0;
                    foreach ($listCustomerTypes as $value) {
                        $ctid = $value->id;
                        echo "<td class='text-right'>" . $grandtot[$ctid] . "</td>";
                        $alltot += $grandtot[$ctid];
                    }
                    ?>
                    <td class="text-right"><?php echo $alltot; ?></td>
                    <td class="text-right"><?php echo $ALLNP; ?></td>
                    <td class="text-right">
                        <?php echo number_format($AlltotmtdToral); ?>
                    </td>
                    <td class="text-right"><?php echo number_format($AllTargetTot); ?></td>
                    <td class="text-right"><?php echo number_format($AllTargetTot - $AlltotmtdToral); ?></td>
                </tr>  

            </table>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-6">
        <div class="box text-center" style="border: 1px solid #cccccc;">
            <h3>Today - Best Performer</h3>
            <h2>
                <?php
                $presentage = array_column($precentageArray, 'presentage');
                array_multisort($presentage, SORT_DESC, $precentageArray);

                $devObj = Device::model()->findByPk($precentageArray[0]['device_id']);
                echo $devObj->code . " -  " . $devObj->rep_name . " (" . $precentageArray[0]['presentage'] . "%)";
                ?>
            </h2>
        </div>
    </div>
    <div class="col-6">
        <div class="box text-center" style="border: 1px solid #cccccc;">
            <h3>Month to Date - Best Performer</h3>
            <h2>
                <?php
                $presentageMonth = array_column($precentageArrayMonth, 'presentage');
                array_multisort($presentageMonth, SORT_DESC, $precentageArrayMonth);

                $devObj = Device::model()->findByPk($precentageArrayMonth[0]['device_id']);
                echo $devObj->code . " -  " . $devObj->rep_name . " (" . $precentageArrayMonth[0]['presentage'] . "%)";
                ?>
            </h2>
        </div>
    </div>
</div>

<!--
<div class="row no-gutters" style="margin-top: 8px; margin-bottom: 40px;">
    <div class="col-sm-6" >
        <div style="background: white; padding: 15px;">
            <h3 style="font-size: 12px; font-weight: bold;">Sri Lanka HEAT-MAP</h3>
        </div>
        <script type="text/javascript">
            google.charts.load ('current', {
               'packages': ['geochart'],
               // Note: you will need to get a mapsApiKey for your project.
               // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
               'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
            });
            google.charts.setOnLoadCallback (drawRegionsMap);

            function drawRegionsMap () {
               var data = google.visualization.arrayToDataTable ([
                  ['provinces', 'MTD Total Sales'],
                <?php
                $monthAreaWise = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as total,device.map_area FROM invoice,device "
                                . "WHERE device.id = invoice.device_id AND "
                                . "DATE(invoice.eff_date) <= '$date' AND "
                                . "DATE(invoice.eff_date) >= '$start' "
                                . " GROUP BY device.map_area")->queryAll();


                $data = "";
                foreach ($monthAreaWise as $value) {
                    $data .= "['" . $value['map_area'] . "'," . intval($value['total']) . "],";
                }
                echo rtrim($data, ",");
                ?>
               ]);

               var options = {
                  region: 'LK', // Africa
                  resolution: 'provinces',
                  displayMode: 'auto',
                  keepAspectRatio: false
               };

               var chart = new google.visualization.GeoChart (document.getElementById ('regions_div'));

               chart.draw (data, options);
            }
        </script>

        <div id="regions_div" style="height: 600px; margin-top: 0px; border: 1px solid #cccccc;"></div>

    </div>
    <div class="col-sm-6">
        <div style="background: white; padding: 15px;">
            <h3 style="font-size: 12px; font-weight: bold;">REP Location MAP</h3>
        </div>
        <div id="map" style="width:100%; height: 600px; margin-top: 0px;"></div>

        <script>

            $ (function () {

               var myVar = setInterval (initMap, 300000);

            });

            // Initialize and add the map
            function initMap () {
               // The location of Uluru



               // The map, centered at Uluru
               var km = 0;
               var bounds = new google.maps.LatLngBounds ();
               var map = new google.maps.Map (
                         document.getElementById ('map'));

                <?php




                $list = Yii::app()->db->createCommand("SELECT device_id,device.code as code FROM `invoice`,device WHERE device.id = invoice.device_id AND DATE(invoice.created) = '$date' $dev GROUP BY invoice.device_id")->queryAll();
                $num = 1;
                foreach ($list as $value) {

                    $device_id = floatval($value['device_id']);
                    $lastrec = Yii::app()->db->createCommand("SELECT * FROM invoice WHERE device_id = '$device_id' AND DATE(created) = '$date' AND latitude != 0 AND longitude != 0 ORDER BY sync_time DESC LIMIT 1 ")->queryAll();


                    if (count($lastrec) <= 0) {
                        continue;
                    }

                    $lat = floatval($lastrec[0]['latitude']);
                    $lng = floatval($lastrec[0]['longitude']);
                    $username = $value['code'];
                    $sync = $lastrec[0]['sync_time'];

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

               map.fitBounds (bounds);
               map.panToBounds (bounds);
            }
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJz9rO99_oSy4PmgjuzN_-85oUMeWrD8c&callback=initMap&libraries=geometry">
        </script>




    </div>
</div>
-->




