<?php
/* @var $this DeviceController */
/* @var $data Device */


$dataobj = Device::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col-1 cells px-1 clickable'>
        <?php echo $dataobj->region->name; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['dtype']; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['locations']; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['name']; ?><br/>
        
    </div>
    
    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['address_line1']; ?><br/>
        <?php echo $data['address_line2']; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['tel_no']; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['rep_name']; ?>
    </div>
    <div class='col text-right cells px-1 clickable'>
        <?php echo number_format($data['target'],2); ?>
    </div>
    <div class='col cells px-1 text-right clickable'>
        <?php echo $data['pin']; ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Device-update" href="#" data-id="<?php echo $data['id']; ?>" model="Device" controler="DeviceController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Device-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Device" controler="DeviceController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
