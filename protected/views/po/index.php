<?php
/* @var $this PoController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {


       $ ('.select_search').select2 ({
          dropdownParent: $ ('#Po-addmodel'),
          width: '100%'
       });

       $ ("#Po-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Po-form").validate ({
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
                window.location.href = "<?php echo Yii::app()->createUrl("po"); ?>/" + result.id;
             }
             showResponse (data);
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Po-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Po-add") {
             $ ("#Po-form").resetForm ();
             $ ("#Po-form").attr ("action", "<?php echo Yii::app()->createUrl('Po/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       var sts = $ (this).parents ("div.row").attr ("data-sts");
       if (sts <= 3) {
          window.location.href = "<?php echo Yii::app()->createUrl('Po') ?>/" + id;
       } else {
          window.open ("<?php echo Yii::app()->createUrl('Po/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       }
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Po-form").submit ();
    });


    $ (document).on ("click", ".Po-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Po-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Po/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Po-form #" + i).is ("[type='checkbox']")) {
                $ ("#Po-form #" + i).prop ('checked', item);
             } else if ($ ("#Po-form #" + i).is ("[type='radio']")) {
                $ ("#Po-form #" + i).prop ('checked', item);
             } else {
                $ ("#Po-form #" + i).val (item);
             }
          });
          $ ("#Po-form").attr ("action", "<?php echo Yii::app()->createUrl('Po/update') ?>/" + id);
       });

       $ ("#Po-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Po-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Po/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Po-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Po-search", function () {
       search ();
    });

    $ (document).on ("change", "#Po-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Po-list', {
          data: {
             val: $ ("#Po-search").val (),
             pages: $ ("#Po-pages").val ()
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
            <h1>Purchasing Orders Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Po-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Purchasing Order - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Po/create') ?>" method="post" id="Po-form">

                        
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
                            <div class="col-sm-6">
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
                                <input data-date-container="#Po-addmodel" type="text" value="<?php echo date("Y-m-d"); ?>" class="form-control datepicker form-control-sm" id="eff_date" name="eff_date" placeholder="Date">
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
                    <button id="Po-add" data-toggle="modal" data-target="#Po-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Po-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Po-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Po-pages" name="pages" class="custom-select custom-select-sm">
                    <option>10 Pages</option>
                    <option selected="selected">50 Pages</option>
                    <option>100 Pages</option>
                </select>
            </div>
        </div>

    </div>
</div>




<div  style="margin-bottom: 50px;">
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col-2 headerdiv'>CODE</div>
            <div class='col-2 headerdiv'>DEVICE</div>

            <div class='col-2 headerdiv'>DATE</div>
            <div class='col headerdiv'>REMARKS</div>
            <div class='col-1 headerdiv'>STATUS</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Po-list',
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
