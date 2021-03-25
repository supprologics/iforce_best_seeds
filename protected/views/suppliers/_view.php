<?php
/* @var $this SuppliersController */
/* @var $data Suppliers */


$dataobj = Suppliers::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['name']; ?>
    </div>
    <div class='col-4 cells px-1 clickable'>
        <?php echo $data['address']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['mobile']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['fax']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['email']; ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Suppliers-update" href="#" data-id="<?php echo $data['id']; ?>" model="Suppliers" controler="SuppliersController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Suppliers-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Suppliers" controler="SuppliersController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
