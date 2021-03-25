<script>
    
    $ (document).on ("click", "#Po-print", function () {
       var id = $ (this).attr ("data-id");
       window.open ("<?php echo Yii::app()->createUrl('po/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
    });

    
    $ (document).on ("click", "#Po-delete", function (e) {
       e.preventDefault ();
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
       }
    });
    
    $ (document).on ("click", "#Po-complete", function (e) {
       e.preventDefault ();
       
       
       setLoading($(this));

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var grn_id = "";
       var confirmdata = confirm ("Are you sure, you want to Approve ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('po/PoConfirm') ?>/" + id,
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
             window.location.href = "<?php echo Yii::app()->createUrl('po') ?>/" + grn_id;
          }
       }else{
            reactive($(this),"Approve");
       }
    });


    function exportTableToExcel(tableID, filename = '') {
        
       var downloadLink;
       var dataType = 'application/vnd.ms-excel';
       var tableSelect = document.getElementById (tableID);
       var tableHTML = tableSelect.outerHTML.replace (/ /g, '%20');
       filename = filename ? filename + '.xls' : 'report.xls';
       downloadLink = document.createElement ("a");
       document.body.appendChild (downloadLink);

       if (navigator.msSaveOrOpenBlob) {
          var blob = new Blob (['\ufeff', tableHTML], {
             type: dataType
          });
          navigator.msSaveOrOpenBlob (blob, filename);
       } else {
          // Create a link to the file
          downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
          downloadLink.download = filename;
          downloadLink.click ();
       }
    }


</script>

<style>
    /***** Special Autocomplete CSS Overiden Part *********/
    .ui-autocomplete{
        width: 80% !important;
    }

</style>

<div id="form_body">
    <div class="container-fluid">

        <?php
        if ($model->online == 9) {
            ?>
            <div class="bg-danger p-3">Canceled Purchasing Order</div>
            <?php
        }
        ?>

        <h2>Purchasing Order Approval Window - <?php echo $model->code; ?></h2>
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
                'itemView' => '_poItemsApprovals',
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

        <div id="btn_bar" class="mt-2 ">
            <div class="row">
                <div class="col">
                    <button class="btn btn-sm btn-success d-print-none" onclick="exportTableToExcel ('excel_export')">Export to Excel</button>
                </div>
                <div class="col text-right">

                    <?php if ($model->online != 9) { ?>
                        <button id="Po-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke PO <span class="fas fa-times-circle text-danger"></span></button>
                        <button id="Po-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Approve</button>
                    <?php } ?>
                    <button id="Po-print" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-warning"> Print PO <span class="oi oi-print"></span></button>

                </div>
            </div>
        </div>

    </div>
</div>
