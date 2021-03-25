


<div>
    <div style="padding: 10px; background: #efefef;">

        <div style="background: #ffffff; margin: 15px; border: 1px solid #dedede; padding: 10px; ">


            <h2 style="font-family: arial;">Daily Sales Achievement Summery</h2>

            <div style="overflow-x:auto;">
                <table  cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                    
                    <tr>
                        <th rowspan="2" style="padding: 10px;border: 1px solid #dedede; text-align: left;"></th>
                        <th rowspan="2" style="padding: 10px;border: 1px solid #dedede; text-align: left;">Location</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: center;" colspan="5">Today</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: center;" colspan="3">Month to date</th>

                    </tr>
                    
                    <tr>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">Calls</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">Gross Total</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">Day Target</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">Balance to Target</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">%</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">MTD Target</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">MTD Sales</th>
                        <th style="padding: 10px;border: 1px solid #dedede; text-align: right;">MTD Bal.</th>

                    </tr>


                    <?php
                    $list = UsersDevices::model()->findAllByAttributes(array("users_id" => $users_id, "online" => 1));


                    $grossTotal = 0;
                    $dayTargetTotal = 0;
                    $balanceToTargetTotal = 0;
                    $callsTotal = 0;
                    $num = 1;


                    $MtdtargetTotal = 0;
                    $MtdSell = 0;


                    foreach ($list as $value) {

                        $device_id = $value->device_id;
                        $device = Device::model()->findByPk($device_id);
                        $data = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot,count(id) as calls FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) = '" . date("Y-m-d") . "' ")->queryRow();

                        $dataMTD = Yii::app()->db->createCommand("SELECT SUM(invoice_total) as tot,count(id) as calls FROM invoice WHERE device_id = '$device_id' AND DATE(eff_date) <= '" . date("Y-m-d") . "' AND DATE(eff_date) >= '" . date("Y-m-1") . "'")->queryRow();


                        $d_target = round($device->target / 25, 2);
                        $baltotarget = $d_target - $data['tot'];

                        if ($baltotarget > 0) {
                            $bg = "background: #ffabc1;";
                        } else {
                            $bg = "background: #e9ffcf;";
                        }

                        $p = $data['tot'] / $d_target * 100;
                        $color = "";
                        if ($p <= 25 && $p > 0) {
                            $bg = 'background:#ff90ad;';
                        }
                        if ($p > 25 && $p <= 50) {
                            $bg = 'background:#ffe483;';
                        }
                        if ($p > 50 && $p <= 75) {
                            $bg = 'background:#c6f696;';
                        }
                        if ($p > 75) {
                            $bg = 'background:#6dd144;';
                        }
                        ?>
                        <tr>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: left;"><?php echo $num; ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: left;"><?php echo $device->name; ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right;"><?php echo $data['calls']; ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right; <?php echo $bg; ?>"><?php echo number_format($data['tot'], 2); ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right; <?php echo $bg; ?>"><?php echo number_format($d_target, 2); ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right; <?php echo $bg; ?>">
                                <?php
                                $baltotarget = $d_target - $data['tot'];
                                if ($baltotarget < 0) {
                                    echo "ACHIEVED";
                                } else {
                                    echo number_format($d_target - $data['tot'], 2);
                                }
                                ?>
                            </td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right; <?php echo $bg; ?>"><?php echo round($data['tot'] / $d_target * 100, 2); ?>%</td>

                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right;"><?php echo $this->byMillions($device->target); ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right;"><?php echo $this->byMillions($dataMTD['tot']); ?></td>
                            <td style="padding: 10px;border: 1px solid #dedede; text-align: right;"><?php echo $this->byMillions($device->target - $dataMTD['tot']); ?></td>
                        </tr>
                        <?php
                        $grossTotal += $data['tot'];
                        $dayTargetTotal += $d_target;
                        $balanceToTargetTotal += $d_target - $data['tot'];
                        $callsTotal += $data['calls'];
                        $num += 1;

                        $MtdtargetTotal += $device->target;
                        $MtdSell += $dataMTD['tot'];
                    }
                    ?>

                    <tr>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: left; background: #eeeeee"></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: left; background: #eeeeee">Total</td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo $callsTotal; ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo number_format($grossTotal, 2); ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo number_format($dayTargetTotal, 2); ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo number_format($balanceToTargetTotal, 2); ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo round($grossTotal / $dayTargetTotal * 100, 2); ?>%</td>

                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo $this->byMillions($MtdtargetTotal); ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo $this->byMillions($MtdSell); ?></td>
                        <td style="padding: 10px;border: 1px solid #dedede; text-align: right; background: #eeeeee"><?php echo $this->byMillions($MtdtargetTotal - $MtdSell); ?></td>

                    </tr>

                </table>
            </div>
            <p style="font-family: arial; font-size: 12px; color: #b6b6b6;">
                This Report Generated by Prologics iForce Email Notification Module @ <?php echo date("Y-m-d H:i:s"); ?>
            </p>




        </div>


    </div>
</div>