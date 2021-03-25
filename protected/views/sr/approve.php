<script>


    $ (document).on ("click", "#Sr-print", function () {
       var id = $ (this).attr ("data-id");
       window.open ("<?php echo Yii::app()->createUrl('sr/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
    });


    $ (document).on ("click", "#Sr-delete", function (e) {
       e.preventDefault ();

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Complete This GRN ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('sr/update') ?>/" + id,
             type: "POST",
             data: {
                online: 1
             },
             async: false,
             success: function (data) {
                var result = JSON.parse (data);
                sts = result.sts;
                showResponse (data);
             },
             error: showResponse
          });

          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('sr') ?>";
          }
       }
    });
    
    $ (document).on ("click", "#Sr-complete", function (e) {
       e.preventDefault ();
       
       
       setLoading($(this));

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Approve This ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('sr/update') ?>/" + id,
             type: "POST",
             data: {
                online: 3
             },
             async: false,
             success: function (data) {
                var result = JSON.parse (data);
                sts = result.sts;
                showResponse (data);
             },
             error: showResponse
          });

          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('sr') ?>";
             window.open ("<?php echo Yii::app()->createUrl('sr/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       } else {
            reactive($(this),"Approve");
       }
    });





</script>

<style>
    /***** Special Autocomplete CSS Overiden Part *********/
    .ui-autocomplete{
        width: 80% !important;
    }
</style>

<div id="form_body">
    <div class="container-fluid">

        <h2>Supplier Return Note Approval Window - <?php echo $model->code; ?></h2>
        <div class="row" id="row_cont">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Location</label>
                    <div class="col-sm-6">
                        <?php echo $model->device->code . " - " . $model->device->name; ?>
                    </div>
                </div>  
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Return Type</label>
                    <div class="col-sm-6">
                        <?php echo $this->returnSRTypes($model->sr_type); ?>
                    </div>
                </div> 
                <div class="form-group row">
                    <label for="eff_date" class="col-sm-3 control-label">Date</label>
                    <div class="col-sm-5">
                        <?php echo $model->eff_date; ?>
                        <input type="hidden" name="eff_date" id="eff_date" value="<?php echo $model->eff_date; ?>" />
                    </div> 
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 control-label">Created</label>
                    <div class="col-sm-6">
                        <?php echo $model->created; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>


        <hr />

        <div id="quick_nav">
            <button id="Sr-print" data-id="<?php echo $model->id; ?>" class="btn btn-block btn-sm btn-primary"><span class="oi oi-print"></span></button>
        </div>

        <div id="sdsdsd" style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_srItemsApprovals',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'SrItems-list',
                'emptyTagName' => 'p',
                'emptyText' => '<span class="glyphicon glyphicon-file"></span> No Records  ',
                'itemsTagName' => 'div',
                'itemsCssClass' => 'ss',
                'pagerCssClass' => 'pagination-div',
                'pager' => array(
                    "header" => "",
                    "htmlOptions" => array(
                        "class" => "pagination pagination-sm"
                    ),
                    'selectedPageCssClass' => 'active',
                    'nextPageLabel' => 'Next',
                    'lastPageLabel' => 'Last',
                    'prevPageLabel' => 'Previous',
                    'firstPageLabel' => 'First',
                    'maxButtonCount' => 10
                ),
            ));
            ?>
        </div>

        <div>
            Additional Remarks / Notes
            <hr/>
            <?php echo $model->remarks; ?>
        </div>

        <div id="btn_bar" class="mt-2 text-right">
            <div class="row">
                <div class="col">
                    <button id="Sr-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Sr-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Approve</button>
                </div>
            </div>
        </div>

    </div>
</div>
