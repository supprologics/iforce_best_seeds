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

       // create the autocomplete
       var autocomplete = $ ("#items").tablecomplete ({
          minLength: 1,
          source: "<?php echo Yii::app()->createUrl('items/loadlist/' . $model->suppliers_id); ?>",
          response: function (event, ui) {
             // ui.content is the array that's about to be sent to the response callback.
             if (ui.content.length === 0) {
                showError ("No Available Stock or Invalid SKU Code");
             }
          },
          focus: _doFocusStuff,
          select: function (event, ui) {
             $ ("#items").val (ui.item.value);
             $ ("#selling").val (ui.item.mrp);
             $ ("#cost").val (ui.item.cost);
             $ ("#qty").focus();
          },
       });
       $ (document).on ("change", "#selectall", function (e) {
          e.preventDefault ();
          $ (".chk").prop ("checked", this.checked);
       });
       $ ("#PoItems-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#PoItems-form").validate ({
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
             $ ("#PoItems-form").resetForm ();
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
       $ (document).on ("click", "#Po-print", function () {
          var id = $ (this).attr ("data-id");
          window.open ("<?php echo Yii::app()->createUrl('po/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
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
       inner_data.push ({name: "remarks", value: $ ("#remarks").val ()});
       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("PoItems/updateAll/" . $model->id) ?>",
          data: inner_data,
          type: "POST",
          success: showResponse,
          error: showResponse
       }).done (function (data) {
          search ();
       });
    }


    $ (document).on ("click", "#Po-delete", function (e) {
       e.preventDefault ();

       setLoading ($ (this));

       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to Revoke this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Po/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             window.location.href = "<?php echo Yii::app()->createUrl('Po') ?>";
          });
       } else {
          reactive ($ (this), "Revoke PO");
       }
    });
    function search () {
       $.fn.yiiListView.update ('PoItems-list', {
          complete: function () {
             loadMainInputs ();
          }
       });
    }

    function loadMainInputs () {
       var id = "<?php echo $model->id; ?>";
       $.getJSON ("<?php echo Yii::app()->createUrl('po/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {
             $ ("#" + i).val (item);
          });
       });
    }


    $ (document).on ("click", "#PoItems-remove", function (e) {
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
             url: "<?php echo Yii::app()->createUrl('PoItems/delete') ?>/" + val[i],
             success: showResponse,
             type: "POST",
             async: false,
             error: showResponse,
          });
          i++;
       } while (i < val.length)
       search ();
    });
    $ (document).on ("click", "#Po-complete", function (e) {
       e.preventDefault ();

       setLoading ($ (this));

       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Complete This GRN ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Po/PoConfirm') ?>/" + id,
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
                reactive ($ (this), "Complete PO");
             }
          });
          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('Po') ?>";
             window.open ("<?php echo Yii::app()->createUrl('Po/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       } else {
          reactive ($ (this), "Complete PO");
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


    $(document).on("change","#items_id",function(){
        var cost = $('option:selected', this).attr('data-cost');
        $("#cost").val(cost);
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

        <h2>Purchasing Order - <?php echo $model->code; ?></h2>
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
                        <input type="text" data-date-container="#form_body" name="eff_date" id="eff_date" value="<?php echo $model->eff_date; ?>" class="form-control datepicker form-control-sm" />
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
        <div id="inline_manu">
            <div class="row">
                <div class="col-sm-10">
                    <form action="<?php echo Yii::app()->createUrl('PoItems/create') ?>" method="post" id="PoItems-form" >
                        <input type="hidden" name="po_id" id="po_id" value="<?php echo $model->id; ?>" />
                        <input type="hidden" name="selling" id="selling" value="0" />
                        
                        <div class="form-row">
                            <div class="col-5">  
                                <label >SKU Code</label>

                                
                                <select id="items_id" name="items_id" class="custom-select custom-select-sm select_search">
                                    <option value="">Select The Product</option>
                                    <?php
                                    $list = Items::model()->findAllByAttributes(array("suppliers_id" => $model->suppliers_id));
                                    foreach ($list as $value) {
                                        echo "<option data-cost='". $value->cost ."' value='" . $value->id . "'>" . $value->item_name . "</option>";
                                    }
                                    ?>
                                </select>



                            </div>



                            <div class="col-1">
                                <label>COST</label>
                                <input type="text" min="0" id="cost" name="cost" class="form-control form-control-sm" placeholder="COST">
                            </div>
                            <div class="col-1">
                                <label>Qty</label>
                                <input type="text" required="" min="0" id="qty" name="qty" class="form-control form-control-sm" placeholder="Qty">
                            </div>
                            <div class="col-2">
                                <label>Remarks</label>
                                <input type="text" id="notes" name="notes" class="form-control form-control-sm" placeholder="Remarks">
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

        <div id="quick_nav">
            <button id="save" class="btn btn-block btn-sm btn-warning"><span class="oi oi-command"></span></button>
            <button id="PoItems-remove" class="btn btn-block btn-sm btn-danger"><span class="fas fa-times-circle text-danger"></span></button>
            <button id="Po-print" data-id="<?php echo $model->id; ?>" class="btn btn-block btn-sm btn-primary"><span class="oi oi-print"></span></button>
        </div>

        <div style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_poItems',
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
            <textarea class="form-control" id="remarks" rows="2" placeholder="Additional Remarks & Notes"></textarea>
        </div>

        <div id="btn_bar" class="mt-2 text-right">
            <div class="row">
                <div class="col">
                    <button id="Po-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke PO <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Po-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Complete PO</button>
                </div>
            </div>
        </div>

    </div>
</div>
