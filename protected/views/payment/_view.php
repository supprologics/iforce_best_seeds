<?php
/* @var $this PaymentController */
/* @var $data Payment */


$dataobj = Payment::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters payment_<?php echo $data['online']; ?>" data-id="<?php echo $data['id']; ?>">

    <div class='col-2 cells px-1'>
        <?php echo $dataobj->customers->name; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['eff_date']; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $data['pay_type']; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $data['cheque_no']; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $data['bank_name']; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $data['branch_name']; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['pd_date']; ?>
    </div>
    <div class='col-1 cells px-1 text-right'>
        <?php echo number_format($data['amount'],2); ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <?php if($data['online'] < 2){ ?>
        <a class="Payment-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Payment" controler="PaymentController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
        <?php } ?>
    </div>


</div>
