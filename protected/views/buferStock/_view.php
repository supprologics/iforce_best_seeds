<?php
/* @var $this BuferStockController */
/* @var $data BuferStock */


$dataobj = BuferStock::model()->findByPk($data['id']);

?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col cells px-1 clickable'>
	<?php echo $data['device_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['items_id']; ?>
</div>
<div class='col cells px-1 clickable'>
	<?php echo $data['qty']; ?>
</div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="BuferStock-update" href="#" data-id="<?php echo $data['id']; ?>" model="BuferStock" controler="BuferStockController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="BuferStock-delete" href="#" data-id="<?php echo $data['id']; ?>" model="BuferStock" controler="BuferStockController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

    
</div>
