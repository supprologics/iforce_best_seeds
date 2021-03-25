<?php
/* @var $this SrController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ('.select_search').select2 ({
          dropdownParent: $ ('#Sr-addmodel'),
          width: '100%'
       });

       $ ("#Sr-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Sr-form").validate ({
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
                window.location.href = "<?php echo Yii::app()->createUrl("sr"); ?>/" + result.id;
             }
             showResponse (data);
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Sr-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Sr-add") {
             $ ("#Sr-form").resetForm ();
             $ ("#Sr-form").attr ("action", "<?php echo Yii::app()->createUrl('Sr/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       var sts = $ (this).parents ("div.row").attr ("data-sts");
       if (sts < 3) {
          window.location.href = "<?php echo Yii::app()->createUrl('Sr') ?>/" + id;
       } else {
          window.open ("<?php echo Yii::app()->createUrl('sr/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       }
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Sr-form").submit ();
    });


    $ (document).on ("click", ".Sr-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Sr-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Sr/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Sr-form #" + i).is ("[type='checkbox']")) {
                $ ("#Sr-form #" + i).prop ('checked', item);
             } else if ($ ("#Sr-form #" + i).is ("[type='radio']")) {
                $ ("#Sr-form #" + i).prop ('checked', item);
             } else {
                $ ("#Sr-form #" + i).val (item);
             }
          });
          $ ("#Sr-form").attr ("action", "<?php echo Yii::app()->createUrl('Sr/update') ?>/" + id);
       });

       $ ("#Sr-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Sr-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Sr/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Sr-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Sr-search", function () {
       search ();
    });

    $ (document).on ("change", "#Sr-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Sr-list', {
          data: {
             val: $ ("#Sr-search").val (),
             pages: $ ("#Sr-pages").val ()
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
            <h1>Supplier Return Note Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Sr-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier Return - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Sr/create') ?>" method="post" id="Sr-form">

                        <div class="form-row mb-2">
                            <label for="suppliers_id" class="col-sm-4 control-label">Supplier *</label>
                            <div class="col-sm-8">
                                <select id="suppliers_id" name="suppliers_id" class="custom-select custom-select-sm select_search">
                                    <?php
                                    $list = Suppliers::model()->findAllByAttributes(array("online" => 1));
                                    foreach ($list as $value) {
                                        echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row mb-2">
                            <label for="device_id" class="col-sm-4 control-label">Location</label>
                            <div class="col-sm-8">
                                <select id="device_id" name="device_id" class="custom-select custom-select-sm">
                                    <?php
                                    $list = Device::model()->findAllByAttributes(array("device_type" => 1));
                                    foreach ($list as $value) {
                                        echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row mb-2">
                            <label for="eff_date" class="col-sm-4 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" value="<?php echo date("Y-m-d"); ?>" data-date-container="#Sr-addmodel" class="form-control datepicker form-control-sm" id="eff_date" name="eff_date" placeholder="Date">
                            </div>
                        </div>                       
                        <div class="form-row mb-2">
                            <label for="remarks" class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8">
                                <textarea name="remarks" id="remarks" rows="2" class="form-control form-control-sm"></textarea>
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
                    <button id="Sr-add" data-toggle="modal" data-target="#Sr-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Sr-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Sr-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Sr-pages" name="pages" class="custom-select custom-select-sm">
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

            <div class='col-2 headerdiv'>CODE</div>
            <div class='col-2 headerdiv'>DEVICE</div>
            <div class='col-2 headerdiv'>DATE</div>
            <div class='col headerdiv'>REMARKS</div>
            <div class='col-2 headerdiv'>STATUS</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Sr-list',
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
