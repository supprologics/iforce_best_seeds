<script>

    $ (function () {

       $ ('.select_search').select2 ({
          width: '100%'
       });

       loadMainInputs ();
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
             $ ("<th>").html ("Code").appendTo ($row);
             $ ("<th>").html ("Item Name").appendTo ($row);
             $ ("<th>").html ("Description").appendTo ($row);
             $ ("<th>").html ("COST (Rs.)").appendTo ($row);
             $ ("<th>").html ("MRP (Rs.)").appendTo ($row);
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
             $ ("<td>").html (item.value).appendTo ($row);
             $ ("<td>").html (item.item_name).appendTo ($row);
             $ ("<td>").html (item.description).appendTo ($row);
             $ ("<td>").html (item.cost).appendTo ($row);
             $ ("<td>").html (item.mrp).appendTo ($row);
             return $row.appendTo (table);
          }
       });
       function _doFocusStuff (event, ui) {
          if (ui.item) {
             var item = ui.item;
             $ ("#items_id").val (item.id);
          }
          jQuery (this).val (ui.item.suggestion);
          return false;
       }

       $.ui.autocomplete.prototype._renderItem = function (table, item) {
          return $ ("<li>")
                    .data ("item.autocomplete", item)
                    .append (
                        "<div class='listCode'>" + item.code + "</div>" + 
                        "<div class='listName'>" + item.item_name + "</div>" + 
                        "<div class='listMrp'>" + item.mrp + "</div>" +
                        "<div class='listAvl'>" + item.avl + "</div>"
                    )
                    .appendTo (table);
       };

       // create the autocomplete
       var autocomplete = $ ("#items").autocomplete ({
          minLength: 1,
          source: "<?php echo Yii::app()->createUrl('items/loadlist/'. $model->device_id); ?>",
          response: function (event, ui) {
             // ui.content is the array that's about to be sent to the response callback.
             if (ui.content.length === 0) {
                showError ("No Available Stock or Invalid SKU Code");
             }
          },
          select: function (event, ui) {
             $ ("#items_id").val (ui.item.id);
             $ ("#items").val (ui.item.item_name);
             $ ("#mrp").val (ui.item.mrp);
             $ ("#dist_val").val (ui.item.distval);
             $ ("#qty_selable").focus ();
          },
       });
       
       
       
       $ (document).on ("change", "#selectall", function (e) {
          e.preventDefault ();
          $ (".chk").prop ("checked", this.checked);
       });
       $ ("#InvoiceItems-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#InvoiceItems-form").validate ({
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
             $ ("#InvoiceItems-form").resetForm ();
             var result = JSON.parse (data);
             if (result.sts == 1) {
                $ ("#items").focus ();
             }
             showResponse (data);
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });
       $ (document).on ("click", "#Invoice-print", function () {
          var id = $ (this).attr ("data-id");
          window.open ("<?php echo Yii::app()->createUrl('invoice/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       });
    });
    $ (document).on ("click", "#save", function (e) {
       e.preventDefault ();
       save ();
    });
    function save () {

       var inner_data = $ ("form#inner_table").serializeArray ();
       //ADD Other Details
       inner_data.push ({name: "eff_date", value: $ ("#eff_date").val ()});
       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("InvoiceItems/updateAll/" . $model->id) ?>",
          data: inner_data,
          type: "POST",
          success: showResponse,
          error: showResponse
       }).done (function (data) {
          search ();
       });
    }


    $ (document).on ("click", "#Invoice-delete", function (e) {
       e.preventDefault ();

       setLoading ($ (this));

       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to Revoke this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('invoice/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             window.location.href = "<?php echo Yii::app()->createUrl('invoice/credit') ?>";
          });
       } else {
          reactive ($ (this), "Revoke INVOICE");
       }
    });
    function search () {
       $.fn.yiiListView.update ('InvoiceItems-list', {
          complete: function () {
             loadMainInputs ();
          }
       });
    }

    function loadMainInputs () {
       var id = "<?php echo $model->id; ?>";
       $.getJSON ("<?php echo Yii::app()->createUrl('invoice/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {
             $ ("#" + i).val (item);
          });
       });
    }


    $ (document).on ("click", "#InvoiceItems-remove", function (e) {
       e.preventDefault ();
       var getConfirmation = confirm ("Are You Sure, You want Remove Selected Lines");
       if (getConfirmation == false) {
          return;
       }

       var val = [];
       $ ('.chk:checkbox:checked').each (function (i) {
          val[i] = $ (this).val ();
       });
       var i = 0;
       do {

          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('InvoiceItems/delete') ?>/" + val[i],
             success: showResponse,
             type: "POST",
             async: false,
             error: showResponse,
          });
          i++;
       } while (i < val.length)
       search ();
    });
    $ (document).on ("click", "#Invoice-complete", function (e) {
       e.preventDefault ();

       setLoading ($ (this));

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Complete This INVOICE ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('invoice/InvConfirm') ?>/" + id,
             type: "POST",
             data: {
                online: 2
             },
             async: false,
             success: function (data) {
                var result = JSON.parse (data);
                sts = result.sts;
                showResponse (data);
             },
             error: showResponse,
             complete: function () {
                reactive ($ (this), "Complete INVOICE");
             }
          });
          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->request->urlReferrer; ?>";
             window.open ("<?php echo Yii::app()->createUrl('invoice/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       } else {
          reactive ($ (this), "Complete INVOICE");
       }
    });




    $ (document).on ("click", ".selitem", function (e) {
       var id = $ (this).parents ("tr").attr ("data-id");
       var checkBoxes = $ ("#line_" + id);
       checkBoxes.prop ("checked", !checkBoxes.prop ("checked"));
       if (checkBoxes.is (":checked")) {
          $ (this).parents ("tr").addClass ("table-danger");
       } else {
          $ (this).parents ("tr").removeClass ("table-danger");
       }
    });


    $ (document).on ("change", "#items_id", function () {
       var mrp = $ ('option:selected', this).attr ('data-mrp');
       var dist = $ ('option:selected', this).attr ('data-dist');
       $ ("#mrp").val (mrp);
       $ ("#dist_val").val (dist);
       $("#qty_selable").focus();
    });




</script>

<style>
    /***** Special Autocomplete CSS Overiden Part *********/
    .ui-autocomplete{
        width: 40% !important;
    }

    .listCode{
        width:10%;
        display:inline-block;
    }
    .listName{
        width:70%;
        display:inline-block;
    }
    .listMrp{
        width:10%;
        display:inline-block;
    }
    
    .listAvl{
        width:10%;
        display:inline-block;
    }
</style>

<div id="form_body">
    <div class="container-fluid">

        <h2>CEDIT NOTE - <?php echo $model->code; ?></h2>
        <div class="row" id="row_cont">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Customer</label>
                    <div class="col-sm-6">
                        <?php echo $model->customers->name; ?>
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
                        <input type="text" data-date-container="#form_body" name="eff_date" id="eff_date" value="<?php echo date("Y-m-d",  strtotime($model->eff_date)); ?>" class="form-control datepicker form-control-sm" />
                    </div>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>

        <div id="quick_nav">
            <button id="save" class="btn btn-block btn-sm btn-warning"><span class="oi oi-command"></span></button>
            <button id="InvoiceItems-remove" class="btn btn-block btn-sm btn-danger"><span class="fas fa-times-circle text-danger"></span></button>
        </div>


        <hr/>
        <div id="inline_manu">
            <div class="row">
                <div class="col-sm-10">
                    <form action="<?php echo Yii::app()->createUrl('InvoiceItems/create') ?>" method="post" id="InvoiceItems-form" >
                        <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $model->id; ?>" />
                        <input type="hidden" name="selling" id="selling" value="0" />

                        <div class="form-row">
                            <div class="col-3">  
                                <label >SKU Code</label>

                                <input type="text" required="" id="items" class="form-control form-control-sm" placeholder="Search By SKU Code">
                                <input type="hidden" id="items_id" name="items_id" >


                            </div>

                            <div class="col-1">
                                <label>Selling Price</label>
                                <input type="text" min="0" id="mrp" name="mrp" class="form-control form-control-sm" placeholder="Rs.">
                            </div>
                            <div class="col-1">
                                <label>Discount %</label>
                                <input type="text" required="" min="0" id="dist_val" name="dist_val" class="form-control form-control-sm" placeholder="%">
                            </div>
                            <div class="col-1">  
                                <label >Type</label>
                                <select id="item_type" name="item_type" class="custom-select custom-select-sm">
                                    <option value="2">RETURN</option>
                                </select>

                            </div>
                            <div class="col-1">
                                <label>Selable Qty</label>
                                <input type="text"  min="0" id="qty_selable" name="qty_selable" class="form-control form-control-sm" placeholder="#">
                            </div>
                            <div class="col-1">
                                <label>Non Saleable Qty</label>
                                <input type="text"  min="0" id="qty_nonselable" name="qty_nonselable" class="form-control form-control-sm" placeholder="#">
                            </div>
                            <div class="col">
                                <label>&nbsp;</label>
                                <button id="btn-submit-add" class="btn btn-primary btn-sm">Add <span class="oi oi-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_invoiceItems',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'InvoiceItems-list',
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


        <div id="btn_bar" class="mt-2 text-right">
            <div class="row">
                <div class="col">
                    <button id="Invoice-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke INVOICE <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Invoice-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Complete INVOICE</button>
                </div>
            </div>
        </div>

    </div>
</div>
