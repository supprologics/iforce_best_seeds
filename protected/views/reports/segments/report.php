<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css">
        <title>REPORT - Customer Segment Report</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
    </head>
    <body>

        <header class="d-print-none d-block">
            <h3>Report Window</h3>
        </header>

        <div class="report_body" >
            <div class="row" style="margin-bottom: 8px;">
                <div class="col">
                    <h2 class="report_header">Customer Segment Report</h2>
                </div>
            </div>


            <table id="popularity" class="table data reports_table table-sm table-bordered" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <th></th>
                    <th>DIST</th>
                    <th>REP</th>
                    <th>ROUTE</th>
                    <th>CODE</th>
                    <th>NAME</th>
                    <th>ADDRESS</th>
                    <th>MOBILE</th>
                    <th>PHONE</th>
                    <th class="text-right">P/CALLS</th>
                    <th class="text-right">AMOUNT</th>
                </tr>

                <?php
                //DEVICE
                $devices_id = "";
                foreach ($_POST['device_id'] as $value) {
                    $devices_id .= "$value,";
                }
                $devices_id = rtrim($devices_id, ",");


                //ITEMS
                $items_id = "";
                foreach ($_POST['items_id'] as $value) {
                    $items_id .= "$value,";
                }
                $items_id = rtrim($items_id, ",");

                //DATES
                $start = $_POST['date_from'];
                $date = $_POST['date_to'];

                //SALES TYPE
                $item_type = $_POST['sale_type'];

                //CONDITIONS
                $selval = !empty($_POST['sale_val']) ? $_POST['sale_val'] : 0;
                switch ($_POST['sale_cond']) {
                    case "E":
                        $having = " HAVING tot = '$selval' ";
                        break;
                    case "G":
                        $having = " HAVING tot > '$selval' ";
                        break;
                    case "L":
                        $having = " HAVING tot < '$selval' ";
                        break;
                    default:
                        $having = "";
                        break;
                }


                //ORDER BY
                if ($_POST['order_by'] == 1) {
                    $orderBy = " ORDER BY tot ASC";
                } else {
                    $orderBy = " ORDER BY tot DESC";
                }

                $list = Yii::app()->db->createCommand("SELECT SUM(invoice_items.total) as tot,customers_id,device_id FROM invoice_items WHERE "
                                . "device_id IN ($devices_id) AND "
                                . "items_id IN ($items_id) AND "
                                . "eff_date >= '$start' AND "
                                . "eff_date <= '$date' AND "
                                . "item_type = '$item_type' "
                                . "GROUP BY customers_id $having $orderBy")->queryAll();


                $num = 1;
                $tot = 0;
                $totcalls = 0;
                foreach ($list as $value) {

                    $att = Customers::model()->findByPk($value['customers_id']);
                    $device = Device::model()->findByPk($value['device_id']);

                    $calls = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE "
                                    . "device_id IN ($devices_id) AND "
                                    . "items_id IN ($items_id) AND "
                                    . "eff_date >= '$start' AND "
                                    . "eff_date <= '$date' AND "
                                    . "item_type = '$item_type' AND "
                                    . "customers_id = '" . $value['customers_id'] . "' "
                                    . "GROUP BY invoice_code")->query()->count();
                    ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $device->code; ?></td>
                        <td><?php echo $device->rep_name; ?></td>
                        <td><?php echo $att->areas->name; ?></td>
                        <td><?php echo $att->id; ?></td>
                        <td><?php echo $att->name; ?></td>
                        <td><?php echo $att->address_no; ?>,<?php echo $att->address_1; ?>,<?php echo $att->address_2; ?></td>
                        <td><?php echo $att->mobile; ?></td>
                        <td><?php echo $att->landline; ?></td>
                        <td class="text-right"><?php echo number_format($calls); ?></td>
                        <td class="text-right">
                            <?php
                            if ($item_type == 1) {
                                echo number_format($value['tot'], 2);
                            } else {
                                echo number_format(0 - $value['tot'], 2);
                            }
                            ?>

                        </td>
                    </tr>
                    <?php
                    $num += 1;
                    $totcalls += $calls;
                    $tot += $value['tot'];
                }
                ?>

                <tr>
                    <td colspan="9">Total</td>
                    <td class="text-right"><?php echo number_format($totcalls); ?></td>
                    <td class="text-right">
                        <?php
                        if ($item_type == 1) {
                            echo number_format($tot, 2);
                        } else {
                            echo number_format(0 - $tot, 2);
                        }
                        ?>
                    </td>
                </tr>

            </table>

        </div>
    </body>
</html>

