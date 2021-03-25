<?php
/* @var $this LtnItemsController */
/* @var $data LtnItems */


$dataobj = LtnItems::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['ltn_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['items_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['cost']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['selling']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['batch_no']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['expire_date']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['qty']; ?>
</div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="LtnItems-update" href="#" data-id="<?php echo $data['id']; ?>" model="LtnItems" controler="LtnItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="LtnItems-delete" href="#" data-id="<?php echo $data['id']; ?>" model="LtnItems" controler="LtnItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
