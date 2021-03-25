
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


       var inner_data = $ ("form#inner_table").serializeArray ();
       //ADD Other Details
       inner_data.push ({name: "reamrks_approval", value: $ ("#reamrks_approval").val ()});

       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("tn/update/" . $model->id) ?>",
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
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Reject This Record ?");
       if (confirmdata == true) {

          save ();
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Tn/update') ?>/" + id,
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
             window.location.href = "<?php echo Yii::app()->createUrl('Tn') ?>";
          }
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


       setLoading ($ (this));

       save ();
       var id = $ (this).attr ("data-id");
       var sts = 0;
       var confirmdata = confirm ("Are you sure, you want to Process This ?");
       if (confirmdata == true) {

          save ();
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Tn/update') ?>/" + id,
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
             window.location.href = "<?php echo Yii::app()->createUrl('Tn') ?>";
             window.open ("<?php echo Yii::app()->createUrl('Tn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       } else {
          reactive ($ (this), "Complete");
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
                        <?php echo $model->bill_bookcode; ?>
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

        <div id="sdsdsd" style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_tnItems_approvals',
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

        <div style="margin-bottom: 15px;">
            Originator Remarks / Notes
            <p>
                <?php echo nl2br($model->remarks); ?>
            </p>
        </div>

        <div>
            Accept / Reject
            <textarea class="form-control" id="reamrks_approval" rows="2" placeholder="Remarks & Notes"></textarea>
        </div>

        <div id="btn_bar" class="mt-2 text-right">
            <div class="row">
                <div class="col">
                    <button id="Tn-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Reject <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Tn-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Accept </button>
                </div>
            </div>
        </div>

    </div>
</div>
