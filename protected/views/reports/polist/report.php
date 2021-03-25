<?php
$devices_id = $_POST['devices_id'];
$datefrom = $_POST['date_from'];
$dateto = $_POST['date_to'];


?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Invoice Listing Report</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
    </head>
    <body>

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">PO Listing Report</h2>
                    <table width="100%">
                        <tr>
                            <td style="font-weight: bold;">Report Period</td>
                            <td><?php echo $datefrom ." to ".$dateto; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Device name</td>
                            <td><?php echo Device::model()->findByPk($devices_id)->name; ?></td>
                        </tr>
                    </table>
                </div>
            </div>


            <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <th></th>
                    <th>PO#</th>
                    <th>DATE</th>
                    <th>TIMESTAMP</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>

                <?php
                
               
                
                
                $num = 1;
                $list = Yii::app()->db->createCommand("SELECT id FROM po WHERE device_id = '$devices_id' AND eff_date >= '$datefrom' AND eff_date <= '$dateto' ")->queryAll();
                $tot = 0;
                foreach ($list as $value) {
                    $att = Po::model()->findByPk($value['id']);
                    $totpo = Yii::app()->db->createCommand("SELECT SUM(qty * selling) as tot FROM `po_items` where po_id = '". $value['id'] ."'")->queryAll();
                    
                    ?>

                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $att->code; ?></td>
                        <td><?php echo $att->eff_date; ?></td>
                        <td><?php echo $att->created; ?></td>
                        <td style="text-align: right;"><?php echo number_format($totpo[0]['tot'],2); ?></td>
                    </tr>
                    <?php
                    
                    $num += 1;
                    $tot += $totpo[0]['tot'];
                }
                ?>
                    
                    <tr>
                        <td colspan="4">Total</td>
                        <td style="text-align: right;"><?php echo number_format($tot,2); ?></td>
                    </tr>

            </table>
            

        </div>
    </body>
</html>

