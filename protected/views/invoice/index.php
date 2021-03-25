<?php
/* @var $this InvoiceController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {


       


       $.ui.autocomplete.prototype._renderItem = function (table, item) {
          return $ ("<li>")
                    .data ("item.autocomplete", item)
                    .append (
                              "<div class='listCode'>" + item.value + "</div>" +
                              "<div class='listName' >" + item.name + "</div>"
                              )
                    .appendTo (table);
       };



       // create the autocomplete
       var autocomplete = $ ("#customers").autocomplete ({
          minLength: 2,
          source: "<?php echo Yii::app()->createUrl('customers/loadlistNames/'); ?>",
          response: function (event, ui) {
             // ui.content is the array that's about to be sent to the response callback.
             if (ui.content.length === 0) {
                showError ("No Customer Found !");
             }
          },
          appendTo: "#Invoice-addmodel",
          select: function (event, ui) {
             $ ("#customers_id").val (ui.item.id);
          }
       });




       $ ("#Invoice-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Invoice-form").validate ({
                rules: {
                   name: {
                      required: true,
                   }
                },
                messages: {
                   name: {
                      max: "Customize Your Error"
                   }
                }
             }).form ();

          },
          success: function (data) {
             var result = JSON.parse (data);
             if (result.id != 0) {
                window.location.href = "<?php echo Yii::app()->createUrl("invoice"); ?>/" + result.id;
             }
             showResponse (data);
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Invoice-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Invoice-add") {
             $ ("#Invoice-form").resetForm ();
             $ ("#Invoice-form").attr ("action", "<?php echo Yii::app()->createUrl('Invoice/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       var sts = $ (this).parents ("div.row").attr ("data-sts");
       if (sts == 1) {
          window.location.href = "<?php echo Yii::app()->createUrl('Invoice') ?>/" + id;
       } else {
          window.open ("<?php echo Yii::app()->createUrl('Invoice/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       }
    });


    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Invoice-form").submit ();
    });


    $ (document).on ("click", ".Invoice-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Invoice-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Invoice/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Invoice-form #" + i).is ("[type='checkbox']")) {
                $ ("#Invoice-form #" + i).prop ('checked', item);
             } else if ($ ("#Invoice-form #" + i).is ("[type='radio']")) {
                $ ("#Invoice-form #" + i).prop ('checked', item);
             } else {
                $ ("#Invoice-form #" + i).val (item);
             }
          });
          $ ("#Invoice-form").attr ("action", "<?php echo Yii::app()->createUrl('Invoice/update') ?>/" + id);
       });

       $ ("#Invoice-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Invoice-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Invoice/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Invoice-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Invoice-search", function () {
       search ();
    });

    $ (document).on ("change", "#Invoice-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Invoice-list', {
          data: {
             val: $ ("#Invoice-search").val (),
             pages: $ ("#Invoice-pages").val (),
             cdate: $ ("#date_search").val (),
             device_id: $ ("#devices_search").val ()
          }
       });
    }


</script>
<!-- //END SCRIPT -->

<style>
    /***** Special Autocomplete CSS Overiden Part *********/
    
    .listCode{
        width:20%;
        display:inline-block;
    }
    .listName{
        width:80%;
        display:inline-block;
    }   


</style>

<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>CUSTOMER INVOICES</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Invoice-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">NEW INVOICE</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Invoice/create') ?>" method="post" id="Invoice-form">
                        <input type="hidden" name="pay_type" value="1" />

                        <div class="form-row mb-1">
                            <label for="customers_id" class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <input type="text" id="customers" class="form-control form-control-sm" placeholder="Search By Name / Code / Mobile">
                                <input type="hidden" id="customers_id" name="customers_id" >
                            </div>
                        </div> 

                        <div class="form-row mb-1">
                            <label for="device_id" class="col-sm-4 control-label">Location</label>
                            <div class="col-sm-5">
                                <select id="device_id" name="device_id" class="custom-select custom-select-sm">
                                    <?php $this->returnDeviceOptions(false); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <label for="eff_date" class="col-sm-4 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" data-date-container="#Invoice-addmodel" value="<?php echo date("Y-m-d"); ?>" class="form-control datepicker form-control-sm" id="eff_date" name="eff_date" placeholder="Date">
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <label for="bill_bookcode" class="col-sm-4 control-label">Invoice No#</label>
                            <div class="col-sm-4">
                                <input type="text" required="true" class="form-control form-control-sm" id="bill_bookcode" name="bill_bookcode" placeholder="Invoice No#">
                            </div>
                        </div>



                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button id="btn-submit" type="button" class="btn btn-success btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Submit Form BY model -->

<div id="title-nav" class="inputsearch">
    <div class="row justify-content-start no-gutters">
        <div class="col-1">
            <label>&nbsp;</label>
            <div class="input-group-append">
                <button id="Device-add" data-toggle="modal" data-target="#Invoice-addmodel" class="btn btn-secondary btn-block btn-sm" >
                    Add <span class="oi oi-plus"></span>
                </button>
            </div>
        </div>        
        <div class="col-2">
            <label>Device</label>
            <select name="devices_search" id="devices_search" class="custom-select custom-select-sm">
                <option value="">Select All</option>
                <?php $this->returnDeviceOptions(); ?>
            </select>
        </div>
        <div class="col-3" id="dd">
            <label>Invoice Date & Bill No#</label>
            <div class="input-group">
                <input type="text" data-date-container="#dd" id="date_search" class="form-control datepicker form-control-sm" placeholder="Date">

                <input type="text" id="Invoice-search" class="form-control form-control-sm" placeholder="Search by Bill No">
                <div class="input-group-append">
                    <button id="Invoice-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-4"></div>
        <div class="col-1 align-self-end">
            <label>&nbsp;</label>
            <div class="input-group">
                <select id="Customers-pages" name="pages" class="custom-select custom-select-sm">
                    <option>10 Pages</option>
                    <option selected="selected">50 Pages</option>
                    <option>100 Pages</option>
                </select>
            </div>
        </div>

    </div>
</div>




<div style="margin-bottom: 100px;">
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col-1 headerdiv'>DATE</div>
            <div class='col-1 headerdiv'>BILL</div>
            <div class='col-1 headerdiv'>CODE</div>            
            <div class='col-2 headerdiv'>AREA</div>
            <div class='col-3 headerdiv'>CUSTOMER</div>
            <div class='col-2 headerdiv'>DEVICE</div>            
            <div class='col-1 text-right headerdiv'>TOTAL</div>
            <div class='col-1 text-right headerdiv'></div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Invoice-list',
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


    </div>
</div>
