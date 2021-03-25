<?php
/* @var $this CostingController */
/* @var $data Costing */


$dataobj = Costing::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col-1 cells px-1'>
        <?php echo $dataobj->rm->code; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $dataobj->rm->item_name; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['qty']; ?>
    </div>
    <div class='col-1 cells px-1 text-right'>
        <?php echo $data['is_ceil'] == 0 ? "NO" : "YES"; ?>
    </div>

    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Costing-update" href="#" data-id="<?php echo $data['id']; ?>" model="Costing" controler="CostingController" data-toggle="tooltip" data-placement="top" title="Update">Update<span class="fas fa-cog"></span></a>
        <a class="Costing-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Costing" controler="CostingController" data-toggle="tooltip" data-placement="top" title="Delete">Delete<span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
