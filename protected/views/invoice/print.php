<?php
$mPDF1 = new mpdf('', 'A4', 0, 'Arial', 20, 5, 70, 20, 4, 4, 'P');
$model = Invoice::model()->findByPk($model->id);
$barcode = $model->code;
//HEADER CONTENT
ob_start();
?>



<div>
    <div style="border-bottom: 1px solid #e5e5e5; padding: 10px 0;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="150px">
                    <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" width="120px" />
                </td>
                <td>

                    <table cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td style="font-size: 12px; padding-right: 30px; vertical-align: top;" >
                                <h3 style="text-align: left; padding-bottom: 25px;">Best Seeds Co. (Pvt) Ltd</h3>
                                <p>
                                    No. 378/5,Sinha Predesa,Kalapaluwawa<br/>Rajagiriya,Sri Lanka<br/>
                                    Tel: 011 2793857 Fax: 0112 793858<br/>
                                    bestseed@sltnet.lk
                                </p>
                            </td>
                            <td style="font-size: 10px; text-align: center; vertical-align: top;">
                        <barcode code="<?php echo $barcode; ?>" type="C39" text="<?php echo $barcode; ?>" height="1.2" />
                        <h2><?php echo $model->code; ?></h2>
                </td>
            </tr>
        </table>
        </td>
        </tr>
        </table>
    </div>
    <h3 style="text-align: center;">INVOICE - WEB PRINT</h3>

    <div>
        <table cellspacing="0" cellpadding="0" width="100%" class="borderd">
            <tr>
                <td width="50%">

                    <table cellspacing="0" cellpadding="0" width="100%" class="no-border">
                        <tr>
                            <td>Code</td>
                            <td><?php echo $model->code; ?></td>
                        </tr>
                        <tr>
                            <td>Customer</td>
                            <td><?php echo $model->customers->name; ?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>
                                <?php echo!empty($model->customers->address_no) ? $model->customers->address_no . "," : ""; ?>
                                <?php echo!empty($model->customers->address_1) ? $model->customers->address_1 . "," : ""; ?>
                                <?php echo!empty($model->customers->address_2) ? $model->customers->address_2 . "," : ""; ?>
                                <?php echo!empty($model->customers->street) ? $model->customers->street : ""; ?>
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellspacing="0" cellpadding="0" width="100%" class="no-border">
                        <tr>
                            <td>Date</td>
                            <td><?php echo $model->eff_date; ?></td>
                        </tr>
                        <tr>
                            <td>Sync Time</td>
                            <td><?php echo $model->sync_time; ?></td>
                        </tr>
                        <tr>
                            <td>Pages</td>
                            <td>{PAGENO}/{nbpg}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
$header = ob_get_contents();
ob_end_clean();
//HEADER CONTENT END--
//BODY CONTENT
ob_start();
?>

<style>
    .no-border td{
        border: none !important;
        padding: 3px;
    }

    .borderd{
        border-collapse: collapse;
        table-layout: fixed;
    }
    .borderd th{
        padding: 5px;
        font-size: 11px;
        background: #e5e5e5;
        text-align: left;
        font-weight: normal;
    }
    .borderd td{
        padding: 5px;
        font-size: 12px;
        border: 1px solid #e5e5e5;
        vertical-align: top;
    }

    .right{
        text-align: right !important;
    }

    .morepad td{
        padding: 10px 5px;
    }


</style>

<div>
    <table cellspacing="0" cellpadding="0" width="100%" class="borderd" style="overflow: wrap;">

        <tr>

            <th></th>
            <th>CODE</th>
            <th>DESCRIPTION</th>
            <th style="text-align: right">QTY</th>
            <th style="text-align: right">QTY N/S</th>
            <th style="text-align: right">MRP</th>
            <th style="text-align: right">DIST</th>
            <th style="text-align: right">TOTAL</th>

        </tr>

        <tr>
            <td colspan="8">SALES</td>
        </tr>


        <?php
        $code = $model->code;
        $device_id = $model->device_id;
        $list = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE invoice_code = '$code' AND device_id = '$device_id' AND item_type = 1")->queryAll();
        $num = 1;
        $tot = 0;
        $totamount = 0;

        foreach ($list as $value) {
            $poitems = InvoiceItems::model()->findByPk($value['id']);
            ?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo $poitems->items->code; ?></td>
                <td><?php echo $poitems->items->item_name; ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_nonselable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->mrp, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable * $poitems->discount, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->total, 2); ?></td>
            </tr>
            <?php
            $num += 1;
            $tot += $poitems->total;
        }
        ?>

        <tr>
            <td colspan="8">RETURNS</td>
        </tr>


        <?php
        $code = $model->code;
        $device_id = $model->device_id;
        $list = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE invoice_code = '$code' AND device_id = '$device_id' AND item_type = 2")->queryAll();
        $num = 1;
        $tot = 0;
        $totamount = 0;

        foreach ($list as $value) {
            $poitems = InvoiceItems::model()->findByPk($value['id']);
            ?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo $poitems->items->code; ?></td>
                <td><?php echo $poitems->items->item_name; ?></td>
                <td style="text-align: right"><?php echo number_format(0 - $poitems->qty_selable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format(0 - $poitems->qty_nonselable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->mrp, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable * $poitems->discount, 2); ?></td>
                <td style="text-align: right"><?php echo number_format(0 - $poitems->total, 2); ?></td>
            </tr>
            <?php
            $num += 1;
            $tot += $poitems->total;
        }
        ?>

        <tr>
            <td colspan="8">FREE-ISSUES</td>
        </tr>


        <?php
        $code = $model->code;
        $device_id = $model->device_id;
        $list = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE invoice_code = '$code' AND device_id = '$device_id' AND item_type = 3")->queryAll();
        $num = 1;
        $tot = 0;
        $totamount = 0;

        foreach ($list as $value) {
            $poitems = InvoiceItems::model()->findByPk($value['id']);
            ?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo $poitems->items->code; ?></td>
                <td><?php echo $poitems->items->item_name; ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_nonselable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->mrp, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable * $poitems->discount, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->total, 2); ?></td>
            </tr>
            <?php
            $num += 1;
            $tot += $poitems->total;
        }
        ?>

        <tr>
            <td colspan="8">SAMPLE</td>
        </tr>


        <?php
        $code = $model->code;
        $device_id = $model->device_id;
        $list = Yii::app()->db->createCommand("SELECT id FROM invoice_items WHERE invoice_code = '$code' AND device_id = '$device_id' AND item_type = 4")->queryAll();
        $num = 1;
        $tot = 0;
        $totamount = 0;

        foreach ($list as $value) {
            $poitems = InvoiceItems::model()->findByPk($value['id']);
            ?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo $poitems->items->code; ?></td>
                <td><?php echo $poitems->items->item_name; ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_nonselable, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->mrp, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->qty_selable * $poitems->discount, 2); ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->total, 2); ?></td>
            </tr>
            <?php
            $num += 1;
            $tot += $poitems->total;
        }
        ?>

        <tr>
            <td colspan="7" style="text-align: right;">Net Total</td>
            <td style="text-align: right"><?php echo number_format($model->invoice_net_total, 2); ?></td>
        </tr> 
        <tr>
            <td colspan="7" style="text-align: right;">Discounts</td>
            <td style="text-align: right"><?php echo number_format(0 - $model->invoice_discount, 2); ?></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right;">Other Discounts</td>
            <td style="text-align: right"><?php echo number_format(0 - $model->invoice_other_discount, 2); ?></td>
        </tr> 
        <tr>
            <td colspan="7" style="text-align: right;">Returns</td>
            <td style="text-align: right"><?php echo number_format(0 - $model->invoice_return_total, 2); ?></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right;">Gross Total</td>
            <td style="text-align: right"><?php echo number_format($model->invoice_total, 2); ?></td>
        </tr>
    </table>


    <div style="margin-top: 50px; font-size: 10px; margin-bottom: 55px;">
        <table cellspacing="0" cellpadding="0" width="100%" >
            <tr>
                <td>Prepared By : <?php echo $model->device->name; ?></td>
            </tr>
        </table>
    </div>


</div>

<?php
$output = ob_get_contents();
ob_end_clean();
//BODY CONTENT END
//FOOTER CONTENT
ob_start();
?>


<div style=" padding: 15px; font-size: 8px; text-align: center;">
    Printed @ <?php echo date("Y-m-d H:i:s"); ?> By PIT iForce System.
</div>


<?php
$footer = ob_get_contents();
ob_end_clean();
//FOOTER CONTENT END


$mPDF1->SetHTMLHeader($header);
$mPDF1->SetHTMLFooter($footer);
$mPDF1->allow_output_buffering = true;

$mPDF1->showWatermarkText = true;
$mPDF1->WriteHTML($output);
$mPDF1->Output($model->code, "I");
