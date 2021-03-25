<?php
/* @var $this InvoiceItemsController */
/* @var $data InvoiceItems */


$dataobj = InvoiceItems::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['invoice_item_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['invoice_code']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['customers_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['items_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['item_name']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['qty_selable']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['qty_nonselable']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['mrp']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['discount']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['discount_type']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['discount_amount']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['is_manual_dis']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['total']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['device_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['eff_date']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['item_type']; ?>
</div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="InvoiceItems-update" href="#" data-id="<?php echo $data['id']; ?>" model="InvoiceItems" controler="InvoiceItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="InvoiceItems-delete" href="#" data-id="<?php echo $data['id']; ?>" model="InvoiceItems" controler="InvoiceItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
