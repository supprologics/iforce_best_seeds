<?php
/* @var $this CustomersController */
/* @var $data Customers */


$dataobj = Customers::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>">

    <div class='col-1 cells px-1'>
        <?php echo $data['code']; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $dataobj->customerTypes->name; ?>
    </div>
    <div class='col cells px-1'>
        <?php echo $dataobj->areas->name; ?>
    </div>
    
    <div class='col cells px-1'>
        <?php echo $data['name']; ?>
    </div>
    <div class='col-3 cells px-1'>
        <?php echo $data['address_no']; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['mobile']; ?>
    </div>
    <div class='col-1 cells px-1'>
        <?php echo $data['landline']; ?>
    </div>
    
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Customers-update" href="#" data-id="<?php echo $data['id']; ?>" model="Customers" controler="CustomersController" data-toggle="tooltip" data-placement="top" title="Update"><span class="fas fa-cog"></span></a>
        <a class="Customers-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Customers" controler="CustomersController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>


</div>
