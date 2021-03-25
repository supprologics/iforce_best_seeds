<?php
/* @var $this TnController */
/* @var $data Tn */


$dataobj = Tn::model()->findByPk($data['id']);


$user_id = Yii::app()->user->getState("userid");
if($dataobj->users_id == $user_id && $dataobj->online == 2){
    $stsValue = 3;
}else{
    $stsValue = $dataobj->online;
}


?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>" data-sts="<?php echo $stsValue; ?>">

    <div class='col-2 cells px-1 clickable'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->device->name; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['eff_date']; ?>
    </div>
    <div class='col cells px-1 clickable'>
        <?php echo $data['remarks']; ?>
    </div>
    <div class='col-2 cells px-1 clickable sts_<?php echo $data['online']; ?>'>
        <?php echo $this->returnStatus($data['online']); ?>
    </div>


</div>
