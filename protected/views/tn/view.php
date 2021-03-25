
<script>

    $ (function () {

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
             $ ("<th>").html ("Description").appendTo ($row);
             $ ("<th>").html ("Batch").appendTo ($row);
             $ ("<th>").html ("Expire Date").appendTo ($row);
             $ ("<th class='text-right'>").html ("COST (Rs.)").appendTo ($row);
             $ ("<th class='text-right'>").html ("AVL QTY").appendTo ($row);
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
             $ ("<td>").html (item.description).appendTo ($row);
             $ ("<td>").html (item.batch_no).appendTo ($row);
             $ ("<td>").html (item.expire_date).appendTo ($row);
             $ ("<td class='text-right'>").html (item.selling).appendTo ($row);
             $ ("<td class='text-right'>").html (item.qty).appendTo ($row);
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
          minLength: 2,
          source: "<?php echo Yii::app()->createUrl('items/loadlistForTn/' . $model->id); ?>",
          ressrnse: function (event, ui) {
             // ui.content is the array that's about to be sent to the ressrnse callback.
             if (ui.content.length === 0) {
                showError ("No Available Stock or Invalid SKU Code");
             }
          },
          focus: _doFocusStuff,
          select: function (event, ui) {
             $ ("#items").val (ui.item.value);
             $ ("#batch_no").val (ui.item.batch_no);
             $ ("#expire_date").val (ui.item.expire_date);
             $ ("#selling").val (ui.item.selling);
             $ ("#qty").attr ("max", ui.item.qty);
             $ ("#qty").focus ();
          }
       });
       $ (document).on ("change", "#selectall", function (e) {
          e.preventDefault ();
          $ (".chk").prop ("checked", this.checked);
       });
       $ ("#TnItems-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#TnItems-form").validate ({
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
             $ ("#TnItems-form").resetForm ();
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
       $ (document).on ("click", "#Tn-print", function () {
          save ();
          var id = $ (this).attr ("data-id");
          window.open ("<?php echo Yii::app()->createUrl('tn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       });
    });
    $ (document).on ("click", "#save", function (e) {
       e.preventDefault ();
       save ();
    });
    
    
    
    function save () {


       $.each ($ (".chkqty"), function (i, item) {

          var max = parseFloat ($ (item).attr ("max"));
          var thisval = parseFloat ($ (item).val ());
          if (thisval > max) {
             $ (item).addClass ("error");
          } else {
             $ (item).removeClass ("error");
          }
       });
       var errors = $ ('.error').length
       if (errors > 0) {
          showError ("Please Check QTY, You have Exceed the MAX Qty");
          return false;
       }


       var inner_data = $ ("form#inner_table").serializeArray ();
       //ADD Other Details
       inner_data.push ({name: "eff_date", value: $ ("#eff_date").val ()});
       inner_data.push ({name: "remarks", value: $ ("#remarks").val ()});
       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("TnItems/updateAll/" . $model->id) ?>",
          data: inner_data,
          type: "POST",
          success: showResponse,
          error: showResponse
       }).done (function (data) {
          search ();
       });
    }


    $ (document).on ("click", "#Tn-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to Revoke this record ?");
       if (confirmdata == true) {
           save();
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Tn/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             window.location.href = "<?php echo Yii::app()->createUrl('Tn') ?>";
          });
       }
    });
    
    function search () {
       $.fn.yiiListView.update ('TnItems-list', {
          complete: function () {
             loadMainInputs ();
             dateload ();
          }
       });
    }

    function loadMainInputs () {
       var id = "<?php echo $model->id; ?>";
       $.getJSON ("<?php echo Yii::app()->createUrl('Tn/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {
             $ ("#" + i).val (item);
          });
       });
    }


    $ (document).on ("click", "#TnItems-remove", function (e) {
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
             url: "<?php echo Yii::app()->createUrl('TnItems/delete') ?>/" + val[i],
             success: showResponse,
             type: "POST",
             async: false,
             error: showResponse,
          });
          i++;
       } while (i < val.length)
       search ();
    });
    $ (document).on ("click", "#Tn-complete", function (e) {
       e.preventDefault ();
       
       
       setLoading($(this));

       save ();
       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Process This ?");
       if (confirmdata == true) {
           save();
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Tn/update') ?>/" + id,
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
             error: showResponse
          });
          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('Tn') ?>";
             window.open ("<?php echo Yii::app()->createUrl('Tn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       }else {
            reactive($(this),"Complete");
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




</script>

<style>
    /***** Special Autocomplete CSS Overiden Part *********/
    .ui-autocomplete{
        width: 80% !important;
    }
</style>

<div id="form_body">
    <div class="container-fluid">

        <h2>Stock Transfer Note - <?php echo $model->code; ?></h2>
        <div class="row" id="row_cont">
            <div class="col-sm-6">
                
                <div class="form-group row">
                    <label for="bill_bookcode" class="col-sm-3 control-label">BIL Ref No#</label>
                    <div class="col-sm-5">
                        <input type="text" name="bill_bookcode" id="bill_bookcode" value="<?php echo $model->bill_bookcode; ?>" class="form-control form-control-sm" />
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="container_no" class="col-sm-3 control-label">Request Location</label>
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
                    <form action="<?php echo Yii::app()->createUrl('TnItems/create') ?>" method="post" id="TnItems-form" >
                        <input type="hidden" name="tn_id" id="tn_id" value="<?php echo $model->id; ?>" />
                        <div class="form-row">
                            <div class="col-3">
                                <label >SKU Code</label>
                                <input type="text" required="" id="items" class="form-control form-control-sm" placeholder="Search By SKU Code">
                                <input type="hidden" id="items_id" name="items_id" >
                            </div>
                            <div class="col-1">
                                <label>BATCH</label>
                                <input type="text"  id="batch_no" name="batch_no" class="form-control form-control-sm" placeholder="Batch">
                            </div>
                            <div class="col-1" id="con_d">
                                <label>EXPIRE</label>
                                <input type="text" data-date-container="#con_d" id="expire_date" name="expire_date" class="form-control datepicker form-control-sm" placeholder="Expire">
                            </div>
                            <div class="col-1">
                                <label>MRP</label>
                                <input type="text" required="" min="0" id="selling" name="selling" class="form-control form-control-sm" placeholder="MRP Rs.">
                            </div>
                            <div class="col-1">
                                <label>Qty</label>
                                <input type="text" required="" min="0" id="qty" name="qty" class="form-control form-control-sm" placeholder="Qty">
                            </div>
                            <div class="col-4">
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
            <button id="TnItems-remove" class="btn btn-block btn-sm btn-danger"><span class="fas fa-times-circle text-danger"></span></button>
            <button id="Tn-print" data-id="<?php echo $model->id; ?>" class="btn btn-block btn-sm btn-primary"><span class="oi oi-print"></span></button>
        </div>

        <div id="sdsdsd" style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_tnItems',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'TnItems-list',
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
        
        <?php if(count($model->remarks) > 0){ ?>
        <div style="margin-bottom: 15px;">
            Reject Remarks / Notes
            <p>
            <?php echo nl2br($model->reamrks_approval); ?>
            </p>
        </div>
        <?php } ?>

        <div>
            Additional Remarks / Notes
            <textarea class="form-control" id="remarks" rows="2" placeholder="Additional Remarks & Notes"></textarea>
        </div>

        <div id="btn_bar" class="mt-2 text-right">
            <div class="row">
                <div class="col">
                    <button id="Tn-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Tn-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Process</button>
                </div>
            </div>
        </div>

    </div>
</div>
