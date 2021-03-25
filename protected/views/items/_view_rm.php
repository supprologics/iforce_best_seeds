<?php
/* @var $this ItemsController */
/* @var $data Items */


$dataobj = Items::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">
    
    <div class='col-1 cells px-1'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $dataobj->suppliers->name; ?>
    </div>
    <div class='col-3 cells px-1'>
        <?php echo $data['item_name']; ?>
    </div>
    <div class='col-3 cells px-1'>
        <?php echo $data['des']; ?>
    </div>
     <div class='col-1 text-right cells px-1'>
        <?php echo number_format($data['cost'],2); ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Items-update" href="#" data-id="<?php echo $data['id']; ?>" model="Items" controler="ItemsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Items-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Items" controler="ItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
