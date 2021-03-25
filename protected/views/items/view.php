<?php
/* @var $this CostingController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    function select2search () {
       $ ('.select_search').select2 ({
          dropdownParent: $ ('#Costing-addmodel'),
          width: '100%'
       });
    }

    $ (document).ready (function () {


       select2search();

       $ ("#Costing-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Costing-form").validate ({
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

       $ ('#Costing-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Costing-add") {
             $ ("#Costing-form").resetForm ();
             $ ("#Costing-form").attr ("action", "<?php echo Yii::app()->createUrl('Costing/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('Costing') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Costing-form").submit ();
    });


    $ (document).on ("click", ".Costing-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Costing-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Costing/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Costing-form #" + i).is ("[type='checkbox']")) {
                $ ("#Costing-form #" + i).prop ('checked', item);
             } else if ($ ("#Costing-form #" + i).is ("[type='radio']")) {
                $ ("#Costing-form #" + i).prop ('checked', item);
             } else {
                $ ("#Costing-form #" + i).val (item);
             }


          });

          select2search();
          $ ("#Costing-form").attr ("action", "<?php echo Yii::app()->createUrl('Costing/update') ?>/" + id);
       });

       $ ("#Costing-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Costing-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Costing/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Costing-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Costing-search", function () {
       search ();
    });

    $ (document).on ("change", "#Costing-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Costing-list', {
          data: {
             val: $ ("#Costing-search").val (),
             pages: $ ("#Costing-pages").val ()
          },
          complete: function () {
             //CODE GOES HERE
          }
       });
    }


</script>
<!-- //END SCRIPT -->

<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Costing Sheet for <?php echo $model->code . "  " . $model->item_name; ?> ( <?php echo $model->des; ?> )</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Costing-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Costing Sheet - Form</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Costing/create') ?>" method="post" id="Costing-form">

                        <input type="hidden" name="items_id" value="<?php echo $model->id; ?>" />
                        <div class="form-row mb-2">
                            <label for="rm_id" class="col-sm-4 control-label">Raw Material *</label>
                            <div class="col-sm-8">
                                <select id="rm_id" name="rm_id" class="custom-select custom-select-sm select_search">
                                    <?php
                                    $list = Items::model()->findAllByAttributes(array("item_type" => 'RM'));
                                    foreach ($list as $value) {
                                        echo "<option value='" . $value->id . "'>" . $value->item_name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="is_ceil" class="col-sm-4 control-label">Round Up ?</label>
                            <div class="col-sm-8">
                                <select id="is_ceil" name="is_ceil" class="custom-select custom-select-sm select_search">
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <label for="qty" class="col-sm-4 control-label">QTY</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" id="qty" name="qty" placeholder="Consumption">
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
    <div class="row justify-content-between">

        <div class="col-4">
            <div class="input-group">
                <div class="input-group-append">
                    <button id="Costing-add" data-toggle="modal" data-target="#Costing-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Costing-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Costing-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Costing-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col-1 headerdiv'>CODE</div>
            <div class='col headerdiv'>ITEM NAME</div>
            <div class='col-1 headerdiv'>QTY</div>
            <div class='col-1 headerdiv text-right'>ROUND UP</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '/costing/_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Costing-list',
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
