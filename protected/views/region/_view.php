<?php
/* @var $this RegionController */
/* @var $data Region */


$dataobj = Region::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col cells px-1'>
        <?php echo $data['name']; ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Region-update" href="#" data-id="<?php echo $data['id']; ?>" model="Region" controler="RegionController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Region-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Region" controler="RegionController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
