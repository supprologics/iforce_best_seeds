<script>

    $ (function () {

       loadMainInputs ();

       $ (document).on ("change", "#selectall", function (e) {
          e.preventDefault ();
          $ (".chk").prop ("checked", this.checked);
       });

       $ ("#GrnItems-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#GrnItems-form").validate ({
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
       $ (document).on ("click", "#Grn-print", function () {
          var id = $ (this).attr ("data-id");
          save();
          window.open ("<?php echo Yii::app()->createUrl('grn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
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
          url: "<?php echo Yii::app()->createUrl("GrnItems/updateAll/" . $model->id) ?>",
          data: inner_data,
          type: "POST",
          success: showResponse,
          error: showResponse
       }).done (function (data) {
          search ();
       });
    }


    $ (document).on ("click", "#Grn-delete", function (e) {
       e.preventDefault ();
       
       setLoading($(this));
       
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to Revoke this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Grn/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             window.location.href = "<?php echo Yii::app()->createUrl('Grn') ?>";
          });
       }else{
            reactive($(this),"Revoke GRN");
       }
    });
    function search () {
       $.fn.yiiListView.update ('GrnItems-list', {
          complete: function () {
             loadMainInputs ();
             dateload ();
          }
       });
    }

    function loadMainInputs () {
       var id = "<?php echo $model->id; ?>";
       $.getJSON ("<?php echo Yii::app()->createUrl('grn/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {
             $ ("#" + i).val (item);
          });
       });
    }


    $ (document).on ("click", "#GrnItems-remove", function (e) {
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
             url: "<?php echo Yii::app()->createUrl('GrnItems/delete') ?>/" + val[i],
             success: showResponse,
             type: "POST",
             async: false,
             error: showResponse,
          });
          i++;
       } while (i < val.length)
       search ();
    });
    
    $ (document).on ("click", "#Grn-complete", function (e) {
       e.preventDefault ();
       save();
       var id = $ (this).attr ("data-id");
       var sts = 0;

       setLoading($(this));

       var confirmdata = confirm ("Are you sure, you want to Complete This GRN ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('grn/update') ?>/" + id,
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
             error: showResponse,
             complete: function () {
                reactive($(this),"Complete GRN");
             }
          });
          if (sts > 0) {
             window.location.href = "<?php echo Yii::app()->createUrl('grn') ?>";
             window.open ("<?php echo Yii::app()->createUrl('grn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600");
          }
       } else {
            reactive($(this),"Complete GRN");
       }
    });
    
    
    
    
    $(document).on("change","#po_items_id",function(e){
       e.preventDefault();
       var cost = $('option:selected', this).attr('data-cost');
       var mrp = $('option:selected', this).attr('data-mrp');
       
       $("#cost").val(cost);
       $("#selling").val(mrp);
       $("#qty").focus();
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

        <h2>Goods Receiving Note - <?php echo $model->code; ?></h2>
        <div class="row" id="row_cont">
            <div class="col-sm-6">
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
                    <form action="<?php echo Yii::app()->createUrl('GrnItems/create') ?>" method="post" id="GrnItems-form" >
                        <input type="hidden" name="grn_id" id="grn_id" value="<?php echo $model->id; ?>" />
                        <div class="form-row">
                            <div class="col-3">  
                                <label >Item Code</label>                                
                                <select name="po_items_id" class="custom-select custom-select-sm select_search" id="po_items_id" >
                                    <option value="">Select The Item</option>
                                    <?php
                                    
                                    $list = PoItems::model()->findAllByAttributes(array("po_id" => $model->po_id));
                                    foreach ($list as $value) {
                                        echo "<option data-cost='". $value->items->cost ."' data-mrp='". $value->items->mrp ."' value='". $value->id ."'>". $value->items->code ." - ". $value->items->item_name ."</option>";
                                    }
                                    
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-1">
                                <label>COST</label>
                                <input type="text" required="true" min="0" id="cost" name="cost" class="form-control form-control-sm" placeholder="COST">
                            </div>
                            <div class="col-1">
                                <label>Qty</label>
                                <input type="text" required="" min="0" id="qty" name="qty" class="form-control form-control-sm" placeholder="Qty">
                            </div>
                            <div class="col-3">
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
            <button id="GrnItems-remove" class="btn btn-block btn-sm btn-danger"><span class="fas fa-times-circle text-danger"></span></button>
            <button id="Grn-print" data-id="<?php echo $model->id; ?>" class="btn btn-block btn-sm btn-primary"><span class="oi oi-print"></span></button>
        </div>

        <div id="sdsdsd" style="overflow: auto;" >
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_grnItems',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'GrnItems-list',
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
                    <button id="Grn-delete" data-id="<?php echo $model->id; ?>" class="btn btn-sm btn-danger"> Revoke GRN <span class="fas fa-times-circle text-danger"></span></button>
                    <button id="Grn-complete" data-id="<?php echo $model->id; ?>" class="btn  btn-sm btn-success">Complete GRN</button>
                </div>
            </div>
        </div>

    </div>
</div>
