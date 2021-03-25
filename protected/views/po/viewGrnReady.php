<script>


    $ (document).on ("click", "#Po-print", function () {
       var id = $ (this).attr ("data-id");
       window.open ("<?php echo Yii::app()->createUrl('po/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
    });

    
    $ (document).on ("click", "#Po-delete", function (e) {
       e.preventDefault ();
       setLoading($(this));
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to Revoke this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Po/update') ?>/" + id,
             type: "POST",
             data: {
                online: 9
             },
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             window.location.href = "<?php echo Yii::app()->createUrl('Po') ?>";
          });
       }else{
            reactive($(this),"Revoke PO");
       }
    });    

    $ (document).on ("click", "#Po-complete", function (e) {
       e.preventDefault ();
       
       setLoading($(this));

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var grn_id = "";
       var confirmdata = confirm ("Are you sure, you want to Convert to a GRN ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('grn/create') ?>/",
             type: "POST",
             data: {
                po_id: id
             },
             async: false,
             success: function (data) {
                var result = JSON.parse (data);
                sts = result.sts;
                if (result.id != null) {
                   grn_id = result.id;
                }
                showResponse (data);
             },
             error: showResponse
          });

          if (grn_id > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('grn') ?>/" + grn_id;
          }
       }else{
            reactive($(this),"Convert To GRN");
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

        <h2>Purchasing Order - <?php echo $model->code; ?> [ GRN PENDING ]</h2>
        <div class="row" id="row_cont">
            <div class="col-sm-6">
                
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Supplier</label>
                    <div class="col-sm-6">
                        <?php echo $model->suppliers->name; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Location</label>
                    <div class="col-sm-6">
                        <?php echo $model->device->code . " - " . $model->device->name; ?>
                    </div>
                </div>                             
                <div class="form-group row">
                    <label for="eff_date" class="col-sm-3 control-label">Date</label>
                    <div class="col-sm-5">
                        <?php echo $model->eff_date; ?>
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

        <div style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_poItemsGrnReady',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'PoItems-list',
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
            <p>
                <?php echo nl2br($model->remarks); ?>
            </p>
        </div>

        <div id="btn_bar" class="mt-2">
            <div class="row">
                <div class="col">
                    <button id="Po-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke PO <span class="fas fa-times-circle text-danger"></span></button>

                </div>
                <div class="col  text-right">
                    <button id="Po-print" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-warning"> Print PO <span class="oi oi-print"></span></button>
                    <button id="Po-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Convert To GRN</button>

                </div>
            </div>
        </div>

    </div>
</div>
