<?php
$mPDF1 = new mpdf('', 'A4', 0, 'Arial',20, 5, 70, 20, 4, 4, 'P');
$model = Adj::model()->findByPk($model->id);
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
                        <h2><?php echo $model->code; ?></h2>
                </td>
            </tr>
        </table>
        </td>
        </tr>
        </table>
    </div>
    <h3 style="text-align: center;">Adjustment Note</h3>

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
                            <td>Request Location</td>
                            <td><?php echo $model->device->name; ?></td>
                        </tr>

                        <tr>
                            <td>Pages</td>
                            <td>{PAGENO}/{nbpg}</td>
                        </tr>
                    </table>



                </td>
                <td>
                    <table cellspacing="0" cellpadding="0" width="100%" class="no-border">

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
            <th>BATCH</th>
            <th>EXPIRE</th>
            
            <th style="text-align: right">COST</th>
            <th style="text-align: right">QTY</th>
            <th style="text-align: right">+/-</th>
            <th style="text-align: right">VARIANCE</th>

        </tr>


        <?php
        $po_id = $model->id;
        $list = Yii::app()->db->createCommand("SELECT id FROM adj_items WHERE adj_id = '$po_id'")->queryAll();
        $num = 1;
        $tot = 0;
        $totamount = 0;
        
        
        if ($model->adj_type == 'NS') {
            $key = "qty_ns";
            $key_var = "variance_ns";
        } else {
            $key = "qty";
            $key_var = "variance";            
        }
        

        foreach ($list as $value) {
            $poitems = AdjItems::model()->findByPk($value['id']);
            ?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo $poitems->items->code; ?></td>
                <td><?php echo $poitems->items->item_name; ?></td>
                <td><?php echo $poitems->batch_no; ?></td>
                <td><?php echo $poitems->expire_date; ?></td>
                
                <td style="text-align: right"><?php echo number_format($poitems->cost, 2); ?></td>
                <td style="text-align: right"><?php echo $poitems->{$key}; ?></td>
                <td style="text-align: right"><?php echo $poitems->{$key_var}; ?></td>
                <td style="text-align: right"><?php echo number_format($poitems->{$key_var} * $poitems->cost, 2); ?></td>
            </tr>
            <?php
            $num += 1;
            $tot += $poitems->{$key_var} * $poitems->cost;
        }
        ?>

        <tr>
            <td colspan="8">Total</td>
            <td style="text-align: right"><?php echo number_format($tot, 2); ?></td>
        </tr>    
    </table>

    <div style="font-size: 12px; border: 1px solid #e5e5e5; padding: 10px; margin-top: 10px;">
        <h5 style="margin: 0px; padding: 0px;">Remarks / Notes</h5>
        <p style="padding: 0px; margin: 0px;">
            <?php echo nl2br($model->remarks); ?>
        </p>
    </div>

    <div style="margin-top: 50px; font-size: 10px; margin-bottom: 55px;">
        <table cellspacing="0" cellpadding="0" width="100%" >
            <tr>
                <td>............................................<br/>Prepared By</td>
                <td>............................................<br/>Authorized by By</td>
                <td>............................................<br/>Accepted by</td>
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


switch ($model->online) {
    case 1:
        $mPDF1->SetWatermarkText('DRAFT');
        break;
    case 2:
        $mPDF1->SetWatermarkText('PENDING APPROVALS');
        break;
    case 9:
        $mPDF1->SetWatermarkText('CANCELED');
        break;

    default:
        break;
}

$mPDF1->showWatermarkText = true;
$mPDF1->WriteHTML($output);
$mPDF1->Output($model->code, "I");
