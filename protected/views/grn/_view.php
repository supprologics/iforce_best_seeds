<?php
/* @var $this GrnController */
/* @var $data Grn */


$dataobj = Grn::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>" data-sts="<?php echo $data['online']; ?>">

    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['code']; ?>        
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->device->name; ?>
    </div>    
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->po->code; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['eff_date']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['remarks']; ?>
    </div>
    <div class='col-1 cells px-1 clickable grn_<?php echo $data['online']; ?>'>
        <?php echo $this->returnStatusGRN($data['online']); ?>
    </div>
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Grn-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Items" controler="ItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>

</div>
