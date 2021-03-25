<?php
/* @var $this ItemsController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#Items-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Items-form").validate ({
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

       $ ('#Items-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Items-add") {
             $ ("#Items-form").resetForm ();
             $ ("#Items-form").attr ("action", "<?php echo Yii::app()->createUrl('Items/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('Items') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Items-form").submit ();
    });


    $ (document).on ("click", ".Items-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Items-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Items/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Items-form #" + i).is ("[type='checkbox']")) {
                $ ("#Items-form #" + i).prop ('checked', item);
             } else if ($ ("#Items-form #" + i).is ("[type='radio']")) {
                $ ("#Items-form #" + i).prop ('checked', item);
             } else {
                $ ("#Items-form #" + i).val (item);
             }
          });
          $ ("#Items-form").attr ("action", "<?php echo Yii::app()->createUrl('Items/update') ?>/" + id);
       });

       $ ("#Items-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Items-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Items/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Items-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Items-search", function () {
       search ();
    });

    $ (document).on ("change", "#Items-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Items-list', {
          data: {
             val: $ ("#Items-search").val (),
             pages: $ ("#Items-pages").val (),
             brands_id : $("#brands_online").val()
          }
       });
    }


</script>
<!-- //END SCRIPT -->

<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Product Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Items-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Finish Good Product - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Items/create') ?>" method="post" id="Items-form">

                        
                        <?php
                        
                        //ASSIGN MAIN SUPPLIER FOR FINISHGOODS
                        $suplier_id = Param::model()->findByPk(1)->val;
                        
                        ?>
                        
                        <input type="hidden" name="item_type" value="FG" />
                        <input type="hidden" name="suppliers_id" id="suppliers_id" value="<?php echo $suplier_id; ?>" />
                        <div class="form-row mb-2">
                            <label for="brands_id" class="col-sm-4 control-label">Category *</label>
                            <div class="col-sm-4">
                                <select id="brands_id" name="brands_id" class="custom-select custom-select-sm">
                                    <?php
                                    $list = Brands::model()->findAllByAttributes(array("is_dashbaord" => 1));
                                    foreach ($list as $value) {
                                        echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="sub_category_id" class="col-sm-4 control-label">Sub Category *</label>
                            <div class="col-sm-4">
                                <select id="sub_category_id" name="sub_category_id" class="custom-select custom-select-sm">
                                    <option value="0" disabled selected>Select Sub Category*</option>
                                    <script>

                                        //for default option
                                        document.cookie = "brand_id_jq = 1";
                                        $('#brands_id').on('change', function() {
                                            $('#sub_category_id').empty();
                                            
                                            $.ajax({
                                                url: "<?php echo Yii::app()->createUrl('Items/SubCategory') ?>/" + this.value,
                                                type: "GET",
                                                success: function (data) {
                                                    var data = JSON.parse(data);
                                                    if (typeof data[0]['id'] !== 'undefined') {
                                                        $('#sub_category_id').append(`<option value="0" disabled selected>Select Sub Category*</option>`);
                                                        data.forEach(element => {
                                                            $('#sub_category_id').append(`<option value="${element['id']}">${element['name']}</option>`);
                                                        });
                                                    }
                                                    else{
                                                        $('#sub_category_id').append(`<option value="0" disabled selected>No Sub Category*</option>`);
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="code" class="col-sm-4 control-label">Code *</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm required" id="code" name="code" placeholder="Code">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="item_name" class="col-sm-4 control-label">Product Name *</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm required" id="item_name" name="item_name" placeholder="Product Name">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="des" class="col-sm-4 control-label">Product Description</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="des" name="des" placeholder="Description">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="short_des" class="col-sm-4 control-label">Short Description</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="short_des" name="short_des" placeholder="Short Description">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="cost" class="col-sm-4 control-label">Cost (Rs.)</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control form-control-sm" id="cost" name="cost" placeholder="Cost Rs.">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="mrp" class="col-sm-4 control-label">MRP (Rs.) *</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control form-control-sm required" id="mrp" name="mrp" placeholder="MRP Rs.">
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="discount" class="col-sm-4 control-label">Discount</label>
                            <div class="col-sm-3">
                                <label>Value</label>
                                <input type="text" class="form-control form-control-sm" id="discount" name="discount" placeholder="Name">
                            </div>
                            <div class="col-sm-4">
                                <label>Type</label>
                                <select id="discount_type" name="discount_type" class="custom-select custom-select-sm">
                                    <option value="1">By Presentage</option>
                                    <option value="0">By Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="volume" class="col-sm-4 control-label">Volume & Units</label>
                            <div class="col-sm-3">
                                <label>Volume</label>
                                <input type="text" class="form-control form-control-sm" id="volume" name="volume" placeholder="Volume">
                            </div>
                            <div class="col-sm-4">
                                <label>Select Measure Unit</label>
                                <select id="unit_type" name="unit_type" class="custom-select custom-select-sm">
                                    <option value="kg">By Kg</option>
                                    <option value="lb">By lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="online" class="col-sm-4 control-label">Import Stock ?</label>
                            <div class="col-sm-4">
                                <select id="online" name="online" class="custom-select custom-select-sm">
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="is_stock" class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-4">
                                <select id="is_stock" name="is_stock" class="custom-select custom-select-sm">
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                </select>
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
            <button id="Items-add" data-toggle="modal" data-target="#Items-addmodel" class="btn btn-secondary btn-block btn-sm" >
                Add <span class="oi oi-plus"></span>
            </button>
        </div>
        <div class="col-2">
            <label>Category</label>
            <select name="brands_online" id="brands_online" class="custom-select custom-select-sm">
                <option value="">Select ALL</option>
                <option value="1">Local Seeds</option>
                <option value="3">High Value</option>
                <option value="4">Low Value</option>
            </select>
        </div>
        <div class="col-3">
            <label>Search</label>
            <div class="input-group">
                <input type="text" id="Items-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Items-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-5"></div>
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


<div style="margin-bottom: 150px;">
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col headerdiv'>CODE</div>
            <div class='col-2 headerdiv'>CATEGORY</div>
            <div class='col-2 headerdiv'>ITEM NAME</div>
            <div class='col-2 headerdiv'>DESCRIPTION</div>
            <div class='col headerdiv'>COST</div>
            <div class='col headerdiv'>MRP</div>
            <div class='col headerdiv'>DISCOUNT</div>
            <div class='col headerdiv'>DIST/TYPE</div>
            <div class='col headerdiv'>DIST/AMOUNT</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Items-list',
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
