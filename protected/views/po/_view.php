<?php
/* @var $this PoController */
/* @var $data Po */


$dataobj = Po::model()->findByPk($data['id']);


$poQTY = Yii::app()->db->createCommand("SELECT SUM(qty) as po_qty FROM `po_items` where po_id = '". $data['id'] ."' ")->queryRow();
$grnQTY = Yii::app()->db->createCommand("SELECT SUM(grn_items.qty) as grn_qty FROM `grn_items`,grn WHERE grn.id = grn_items.grn_id AND grn.po_id = '". $data['id'] ."'  ")->queryRow();

if($grnQTY['grn_qty'] >= $poQTY['po_qty']){
    $data['online'] = 5;
}

if(empty($poQTY['po_qty'])){
    $data['online'] = $dataobj->online;
}


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
    <div class='col-1 cells px-1 clickable posts_<?php echo $data['online']; ?>'>
        <?php echo $this->returnStatus($data['online']); ?>
    </div>



</div>
