<?php
/* @var $this BrandsController */
/* @var $data Brands */


$dataobj = Brands::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col cells px-1 clickable'>
        <?php echo $data['name']; ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo count($dataobj->sub_categories); ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->is_dashbaord == 1 ? "YES" : "NO"; ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Brands-update" href="#" data-id="<?php echo $data['id']; ?>" model="Brands" controler="BrandsController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Brands-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Brands" controler="BrandsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
