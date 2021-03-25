<?php
/* @var $this InvoiceController */
/* @var $data Invoice */


$dataobj = Invoice::model()->findByPk($data['id']);
?>


<div class="row datarow no-gutters" data-id="<?php echo $data['id']; ?>" data-sts="<?php echo $data['online']; ?>">
    
    <div class='col-1 cells px-1 clickable'>
        <?php echo date("Y-m-d", strtotime($data['eff_date'])); ?>
    </div> 
    <div class='col-1 cells px-1 clickable'>
        <?php echo $data['bill_bookcode']; ?>
    </div>
    <div class='col-1 cells px-1 clickable'>
        INV<?php echo $data['code']; ?>
    </div>
    
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->customers->areas->name; ?>
    </div>
    <div class='col-3 cells px-1 clickable'>
        <?php echo $dataobj->customers->name; ?>
    </div>
    <div class='col-2 cells px-1 clickable'>
        <?php echo $dataobj->device->name; ?>
    </div>    
     
    <div class='col-1 cells text-right px-1 clickable'>
        <?php echo number_format($data['invoice_total'],2); ?>
    </div>
    <div class='col-sm-1 cells btn-cog text-right px-1'>
        <a class="Invoice-delete" href="#" data-id="<?php echo $data['id']; ?>" model="Items" controler="ItemsController" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fas fa-times-circle text-danger"></span></a>
    </div>
    
</div>
