


<div>
    <div style="padding: 10px; background: #efefef;">

        <div style="background: #ffffff; margin: 15px; border: 1px solid #dedede; padding: 10px; ">


            <h2 style="font-family: arial;">Target Achivement Analysis: Up to Last Week Report</h2>

            <div style="overflow-x:auto;">
                <table  cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                    <tr>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;"></th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;">Location</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;">Name</th>

                        <?php
                        $today = date("Y-m-d", strtotime($today . " -1 days"));
                        //$biginDate = date("Y-m-d", strtotime($today . " -6 days"));
                        
                        $biginDate = date("Y-m-1");
                        
                        $begin = new DateTime($biginDate);
                        $end = new DateTime($today);

                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                            echo '<th style="padding: 10px;border: 1px solid #dedede; text-align: center;">' . $i->format("d") . '</th>';
                        }
                        
                        ?>

                    </tr>
                    <tr>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;"></th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;"></th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: left;"></th>

                        <?php
                        $begin = new DateTime($biginDate);
                        $end = new DateTime($today);
                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                            echo '<th style="padding: 10px;border: 1px solid #dedede; text-align: center;">' . $i->format("D") . '</th>';
                        }
                        
                        ?>

                    </tr>


                    <?php
                    $list = UsersDevices::model()->findAllByAttributes(array("users_id" => $users_id, "online" => 1));


                    $grossTotal = 0;
                    $dayTargetTotal = 0;
                    $balanceToTargetTotal = 0;
                    $callsTotal = 0;
                    $num = 1;

                    foreach ($list as $value) {

                        $device_id = $value->device_id;
                        $device = Device::model()->findByPk($device_id);
                        $d_target = round($device->target / 25, 2);
                        ?>
                        <tr >
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: left;"><?php echo $num; ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: left;"><?php echo $device->name; ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: left;"><?php echo $device->rep_name; ?></td>
                            <?php
                            
                            
                            $begin = new DateTime($biginDate);
                            $end = new DateTime($today);
                            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                                
                                
                                $date = $i->format("Y-m-d");
                                $data = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot,count(id) as calls FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) = '$date' ")->queryRow();
                                
                                $p = $data['tot']/$d_target * 100;
                                
                                $color = "";
                                if($p <= 25 && $p> 0){
                                    $color = 'background:#ff90ad;';
                                }
                                if($p > 25 && $p <= 50){
                                    $color = 'background:#ffe483;';
                                }
                                if($p > 50 && $p <= 75){
                                    $color = 'background:#c6f696;';
                                }
                                if($p > 75){
                                    $color = 'background:#6dd144;';
                                }
                                
                                
                                
                                echo "<td style='padding: 10px;border: 1px solid #dedede; text-align: center;$color'>";
                                echo !empty($p) ? number_format($data['tot']/$d_target * 100)."%" : "";                                
                                echo "</td>";
                                
                            }
                            ?>
                        </tr>
                            <?php
                            $num += 1;
                        }
                        ?>


                </table>
            </div>
            <p style="font-family: arial; font-size: 12px; color: #b6b6b6;">
                This Report Generated by Prologics iForce Email Notification Module @ <?php echo date("Y-m-d H:i:s"); ?>
            </p>




        </div>


    </div>
</div>