<script>
    $ (function () {

       $ (".datepicker").datepicker ({
          format: "yyyy-mm-dd",
          autoclose: true,
          todayHighlight: true
       });
    });

</script>


<div class="row">
    <div id="datec" class="col">
        <h4 style="margin-bottom: 15px; font-size: 18px;">
            <span class="oi oi-paperclip"></span> 
            <?php echo $title; ?>
        </h4>

        <form target="_blank" class="form-horizontal" action="<?php echo Yii::app()->createUrl('reports/loadreport/') ?>" method="post" >
            <input type="hidden" name="report" value="<?php echo $report; ?>" />


            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">DISTRIBUTION</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">PRODUCTS</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" >
                    <select name="device_id[]" class="custom-select custom-select-sm" id="device_id" multiple="" style="min-height: 250px;">
                        <?php $this->returnDeviceOptions(false,false); ?>
                    </select>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <select name="items_id[]" class="custom-select custom-select-sm" id="items_id" multiple="" style="min-height: 250px;">
                        <?php
                        $itemlist = Items::model()->findAll(array("order" => "item_name ASC"));
                        foreach ($itemlist as $value) {
                            echo "<option value='" . $value->id . "'>" . $value->item_name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row pt-2">
                <label for="plies" class="col-sm-1 control-label">Condition</label>
                <div class="col-sm-2">
                    <select name="sale_type" id="sale_type" class="custom-select custom-select-sm" >
                        <option value="1">SALES</option>
                        <option value="2">RETURNS</option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <select name="sale_cond" id="sale_cond" class="custom-select custom-select-sm" >
                        <option value="G">></option>
                        <option value="L"><</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" name="sale_val" />
                </div>
            </div>
            
            

            <div  class="form-row form-group pt-2">
                <label for="name" class="col-sm-1 control-label">Date Period</label>
                <div class="col-sm-2">
                    <input type="text" required="true" data-date-container="#datec" class="form-control form-control-sm datepicker" value="<?php echo date("Y-m-01"); ?>" id="date_from" name="date_from" placeholder="Start Date">
                </div>
                <div class="col-sm-2">
                    <input type="text" required="true" data-date-container="#datec" class="form-control form-control-sm datepicker" value="<?php echo date("Y-m-d"); ?>" id="date_to" name="date_to" placeholder="End Date">
                </div>
            </div>
            
            <div class="form-row pt-2">
                <label for="plies" class="col-sm-1 control-label">Order</label>
                <div class="col-sm-2">
                    <select name="order_by" id="order_by" class="custom-select custom-select-sm" >
                        <option value="1">Ascending By Total</option>
                        <option value="2">Descending By Total</option>
                    </select>
                </div>
            </div>

            <div class="form-row pt-2">
                <label for="plies" class="col-sm-1 control-label"></label>
                <div class="col-sm-8">
                    <button class="btn btn-primary">View Report</button>
                </div>
            </div>


        </form>
    </div>
</div>