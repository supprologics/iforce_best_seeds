<?php
$ctype_id = $_POST['customer_types_id'];
$datefrom = $_POST['date_from'];
$dateto = $_POST['date_to'];
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Sales Movement Report</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
    </head>
    <body>

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Total Sales Movement Report</h2>
                    <table width="100%">
                        <tr>
                            <td style="font-weight: bold;">Report Period</td>
                            <td><?php echo $datefrom . " to " . $dateto; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Type</td>
                            <td><?php echo!empty($ctype_id) ? CustomerTypes::model()->findByPk($ctype_id)->name : "ALL"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>


            <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">

                <tr>
                    <th></th>
                    <th>CODE</th>
                    <th>ITEM</th>

                    <?php
                    $list = Device::model()->findAllByAttributes(array("online" => 1));
                    foreach ($list as $value) {
                        ?>
                        <th class = "text-right"><?php echo $value->code; ?></th>
                        <?php
                    }
                    ?>

                    <th  class="text-right">Total</th>
                </tr>

                <?php
                if (!empty($_POST['customer_types_id'])) {
                    $customer_types_id = " AND customer_types_id = '" . $_POST['customer_types_id'] . "' ";
                } else {
                    $customer_types_id = "";
                }



                $list = Yii::app()->db->createCommand("SELECT * FROM items WHERE online = 1 ORDER BY item_name ASC")->queryAll();

                $allp = 0;
                $freetot = 0;
                $sampletot = 0;

                $stot = 0;
                $nstot = 0;

                $netttot = 0;
                $disttot = 0;
                $alltot = 0;

                $num = 1;
                foreach ($list as $value) {

                    $item_id = $value['id'];
                    ?>

                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $value['code']; ?></td>
                        <td><?php echo $value['item_name']; ?></td>

                        <?php
                        $list = Device::model()->findAllByAttributes(array("online" => 1));
                        $tot = 0;
                        foreach ($list as $value) {
                            ?>
                            <td class = "text-right">
                                <?php
                                $dev_id = $value->id;
                                $qty = Yii::app()->db->createCommand("SELECT SUM(qty_selable + qty_nonselable) as tot FROM invoice_items,customers WHERE customers.id = invoice_items.customers_id AND device_id = '$dev_id' AND DATE(invoice_items.eff_date) >= '$datefrom' AND DATE(invoice_items.eff_date) <= '$dateto' AND items_id = '$item_id' $customer_types_id")->queryAll();
                                echo $qty[0]['tot'];
                                $tot += $qty[0]['tot'];
                                ?>
                            </td>
                            <?php
                        }
                        ?>

                        <td class = "text-right"><?php echo $tot; ?></td>

                    </tr>

                    <?php
                    $num += 1;
                }
                ?>

            </table>


        </div>
    </body>
</html>

