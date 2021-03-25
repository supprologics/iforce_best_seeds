<?php
/* @var $this SrController */
/* @var $data Sr */


$dataobj = Sr::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>" data-sts="<?php echo $data['online']; ?>">

    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->device->name; ?>
    </div>
    
    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['eff_date']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['remarks']; ?>
    </div>
    <div class='col-2 cells px-1 clickable sts_<?php echo $data['online']; ?>'>
        <?php echo $this->returnStatus($data['online']); ?>
    </div>
    


</div>
