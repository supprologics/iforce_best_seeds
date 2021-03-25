
<script>

    $ (function () {



       // create the autocomplete

    });
</script>


<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#Payment-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Payment-form").validate ({
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
          success: function(data){
              $ ("#Payment-form").resetForm ();
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ("#Payment-form-delete").ajaxForm ({
          beforeSend: function () {

             return $ ("#Payment-form-delete").validate ({
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
          success: showResponse,
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Payment-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Payment-add") {
             $ ("#Payment-form").resetForm ();
             $ ("#Payment-form").attr ("action", "<?php echo Yii::app()->createUrl('Payment/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });
    });

    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('Payment') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Payment-form").submit ();
    });
    $ (document).on ("click", "#btn-submit-delete", function () {
       $ ("#Payment-form-delete").submit ();
    });

    $ (document).on ("click", ".Payment-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Payment-form-delete").attr ("action", "<?php echo Yii::app()->createUrl('Payment/delete') ?>/" + id);
       $ ("#Payment-returns").modal ('show');
    });


    $ (document).on ("click", "#Payment-searchbtn", function () {
       search ();
    });
    $ (document).on ("keyup", "#Payment-search", function () {
       search ();
    });
    $ (document).on ("change", "#Payment-pages", function () {
       search ();
    });
    function search () {
       $.fn.yiiListView.update ('Payment-list', {
          data: {
             val: $ ("#Payment-search").val (),
             pages: $ ("#Payment-pages").val (),
             chq_no: $ ("#search_chqno").val (),
             pd: $ ("#search_pd").val (),
             online: $ ("#search_online").val (),
          }
       });
    }

    $ (document).on ("change", "#device_id", function () {

       $.widget ("custom.tablecomplete", $.ui.autocomplete, {
          _create: function () {
             this._super ();
             this.widget ().menu ("option", "items", "> tr:not(.ui-autocomplete-header)");
          },
          _renderMenu: function (ul, items) {
             var self = this;
             //table definitions
             var $t = $ ("<table class='table table-sm table-sp'>", {
                border: 0
             }).appendTo (ul);
             $t.append ($ ("<thead>"));
             $t.find ("thead").append ($ ("<tr>"));
             var $row = $t.find ("tr");
             $ ("<th>").html ("INVOICE").appendTo ($row);
             $ ("<th>").html ("Customer Name").appendTo ($row);
             $ ("<th>").html ("Code").appendTo ($row);
             $ ("<th>").html ("Outstanding").appendTo ($row);
             $ ("<tbody>").appendTo ($t);
             $.each (items, function (index, item) {
                self._renderItemData (ul, $t.find ("tbody"), item);
             });
          },
          _renderItemData: function (ul, table, item) {
             return this._renderItem (table, item).data ("ui-autocomplete-item", item);
          },
          _renderItem: function (table, item) {
             var $row = $ ("<tr>", {
                class: "ui-menu-item",
                role: "presentation"
             })
             $ ("<td>").html (item.code).appendTo ($row);
             $ ("<td>").html (item.value).appendTo ($row);
             $ ("<td>").html (item.name).appendTo ($row);
             $ ("<td>").html (item.due).appendTo ($row);
             return $row.appendTo (table);
          }
       });
       function _doFocusStuff (event, ui) {
          if (ui.item) {
             var item = ui.item;
             $ ("#customers").val (item.name);
          }
          $ ("#customers_id").val ("");
          return false;
       }
       var autocomplete = $ ("#customers").tablecomplete ({
          minLength: 2,
          source: "<?php echo Yii::app()->createUrl('customers/loadlist/'); ?>/" + $ ("#device_id").val (),
          response: function (event, ui) {
             // ui.content is the array that's about to be sent to the resgrnnse callback.
             if (ui.content.length == 0) {
                showError ("No Customer invoice Found !");
             }
          },
          appendTo: "#Payment-addmodel",
          focus: _doFocusStuff,
          select: function (event, ui) {
             $ ("#customers_id").val (ui.item.id);
             $ ("#eff_date").focus ();
             $("#invoice_id").val(ui.item.inv_id);
             $("#amount").val(ui.item.due_val);
          }
       });

    });


</script>
<!-- //END SCRIPT -->

<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Customer Payments Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Payment-returns" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Customer Payments Status Update</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Payment/delete') ?>" method="post" id="Payment-form-delete">

                        <div class="form-group row">
                            <label for="l_type" class="col-sm-4 control-label">Return Status</label>
                            <div class="col-sm-6">
                                <select id="l_type" name="l_type" class="custom-select custom-select-sm">
                                    <option value="CHQRETURN" >Cheque Return</option>
                                    <option value="REFUND" >Payment Canceled</option>
                                    <option value="OTHER" >Other Return</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ref" class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8">
                                <textarea name="ref" id="ref" rows="2" maxlength="250" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>                      

                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button id="btn-submit-delete" type="button" class="btn btn-success btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Submit Form BY model -->

<!-- Submit Form BY model -->
<div class="modal fade" id="Payment-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Customer Payments</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Payment/create') ?>" method="post" id="Payment-form">
                        <input type="hidden" class="form-control form-control-sm" id="invoice_id" name="invoice_id" placeholder="invoice_id">
                        <div class="form-group row">
                            <label for="device_id" class="col-sm-4 control-label">Location</label>
                            <div class="col-sm-4">
                                <select id="device_id" class="custom-select custom-select-sm">
                                    <?php $this->returnDeviceOptions(false); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="customers_id" class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="customers" placeholder="Customer Name">
                                <input type="hidden" class="form-control form-control-sm" id="customers_id" name="customers_id" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="receipt_no" class="col-sm-4 control-label">Receipt No#</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm " id="receipt_no" name="receipt_no" placeholder="Receipt No#">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="eff_date" class="col-sm-4 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" data-date-container="#Payment-addmodel" class="form-control form-control-sm datepicker" id="eff_date" name="eff_date" placeholder="Date">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8">
                                <textarea name="description" id="description" rows="2" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pay_type" class="col-sm-4 control-label">Payment Mode</label>
                            <div class="col-sm-4">
                                <select id="pay_type" name="pay_type" class="custom-select custom-select-sm">
                                    <option value="BANK">BANK</option>
                                    <option value="CASH">CASH</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="amount" class="col-sm-4 control-label">Amount</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm " id="amount" name="amount" placeholder="Amount 0.00">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cheque_no" class="col-sm-4 control-label">Cheque No#</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm " id="cheque_no" name="cheque_no" placeholder="Cheque No#">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_name" class="col-sm-4 control-label">Bank</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm " id="bank_name" name="bank_name" placeholder="Bank Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="branch_name" class="col-sm-4 control-label">Branch</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm " id="branch_name" name="branch_name" placeholder="Branch Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pd_date" class="col-sm-4 control-label">Cheque Date</label>
                            <div class="col-sm-6">
                                <input type="text" data-date-container="#Payment-addmodel"  class="form-control form-control-sm datepicker" id="pd_date" name="pd_date" placeholder="Cheque Date">
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
            <button id="Payment-add" data-toggle="modal" data-target="#Payment-addmodel" class="btn btn-secondary btn-block btn-sm" >
                Add <span class="oi oi-plus"></span>
            </button>
        </div>
        <div class="col-1">
            <label>Status</label>
            <select name="search_online" id="search_online" class="custom-select custom-select-sm">
                <option value="">All Status</option>
                <option value="1">Success</option>
                <option value="2">Canceled</option>
            </select>
        </div>
        <div class="col-1">
            <label>Cheque No#</label>
            <div class="input-group">
                <input type="text" id="search_chqno" class="form-control form-control-sm" placeholder="Cheque no#">           
            </div>
        </div> 
        <div id="dc" class="col-1">
            <label>Cheque Date</label>
            <div class="input-group">
                <input type="text" data-date-container="#dc" id="search_pd" class="form-control form-control-sm datepicker" placeholder="Date">      
            </div>
        </div> 
        <div class="col-3">
            <label>Customer</label>
            <div class="input-group">
                <input type="text" id="Payment-search" class="form-control form-control-sm" placeholder="Search by Customer Name">                
                <div class="input-group-append">
                    <button id="Payment-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 

        <div class="col-4"></div>
        <div class="col-1 align-self-end">
            <label>&nbsp;</label>
            <div class="input-group">
                <select id="Payment-pages" name="pages" class="custom-select custom-select-sm">
                    <option>10 Pages</option>
                    <option selected="selected">50 Pages</option>
                    <option>100 Pages</option>
                </select>
            </div>
        </div>

    </div>
</div>



<div>
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col-2 headerdiv'>CUSTOMER</div>
            <div class='col-1 headerdiv'>CODE</div>
            <div class='col-1 headerdiv'>DATE</div>
            <div class='col headerdiv'>MODE</div>
            <div class='col headerdiv'>CHEQUE</div>
            <div class='col headerdiv'>BANK</div>
            <div class='col headerdiv'>BRANCH</div>
            <div class='col-1 headerdiv'>POST DATE</div>
            <div class='col-1 headerdiv text-right'>AMOUNT</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Payment-list',
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
