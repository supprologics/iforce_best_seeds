<?php
/* @var $this SrItemsController */
/* @var $data SrItems */


$dataobj = SrItems::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['sr_id']; ?>
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
        <a class="SrItems-update" href="#" data-id="<?php echo $data['id']; ?>" model="SrItems" controler="SrItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="SrItems-delete" href="#" data-id="<?php echo $data['id']; ?>" model="SrItems" controler="SrItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
