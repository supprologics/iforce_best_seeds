<?php
/* @var $this PaymentItemsController */
/* @var $data PaymentItems */


$dataobj = PaymentItems::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['payment_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['invoice_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['amount']; ?>
</div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="PaymentItems-update" href="#" data-id="<?php echo $data['id']; ?>" model="PaymentItems" controler="PaymentItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="PaymentItems-delete" href="#" data-id="<?php echo $data['id']; ?>" model="PaymentItems" controler="PaymentItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
