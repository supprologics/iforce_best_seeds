<?php
/* @var $this GrnItemsController */
/* @var $data GrnItems */


$dataobj = GrnItems::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['grn_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['items_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['qty']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['selling']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['discount']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['total']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['remarks']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['batch_no']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['expire_date']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['sub_location']; ?>
</div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="GrnItems-update" href="#" data-id="<?php echo $data['id']; ?>" model="GrnItems" controler="GrnItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="GrnItems-delete" href="#" data-id="<?php echo $data['id']; ?>" model="GrnItems" controler="GrnItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
