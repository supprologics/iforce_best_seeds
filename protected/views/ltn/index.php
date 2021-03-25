<?php
/* @var $this LtnController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#Ltn-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Ltn-form").validate ({
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
                window.location.href = "<?php echo Yii::app()->createUrl("ltn"); ?>/" + result.id;
             }
             showResponse (data);
          },
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Ltn-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Ltn-add") {
             $ ("#Ltn-form").resetForm ();
             $ ("#Ltn-form").attr ("action", "<?php echo Yii::app()->createUrl('ltn/create') ?>/");
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
          window.location.href = "<?php echo Yii::app()->createUrl('ltn') ?>/" + id;
       } else {
          window.open ("<?php echo Yii::app()->createUrl('ltn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus ();
       }
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Ltn-form").submit ();
    });


    $ (document).on ("click", ".Ltn-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Ltn-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Ltn/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Ltn-form #" + i).is ("[type='checkbox']")) {
                $ ("#Ltn-form #" + i).prop ('checked', item);
             } else if ($ ("#Ltn-form #" + i).is ("[type='radio']")) {
                $ ("#Ltn-form #" + i).prop ('checked', item);
             } else {
                $ ("#Ltn-form #" + i).val (item);
             }
          });
          $ ("#Ltn-form").attr ("action", "<?php echo Yii::app()->createUrl('Ltn/update') ?>/" + id);
       });

       $ ("#Ltn-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Ltn-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Ltn/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Ltn-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Ltn-search", function () {
       search ();
    });

    $ (document).on ("change", "#Ltn-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Ltn-list', {
          data: {
             val: $ ("#Ltn-search").val (),
             pages: $ ("#Ltn-pages").val ()
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
            <h1>Internal Location Transfers</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Ltn-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Internal Transfer - Form</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Ltn/create') ?>" method="post" id="Ltn-form">
                        
                        <?php                        
                        $device_id = Param::model()->findByAttributes(array("key" => "MAIN_RM_STOCK"))->val;                        
                        ?>
                        
                        <input type="hidden" name="device_to" id="device_to" value="<?php echo $device_id; ?>" />
                        <div class="form-group row">
                            <label for="device_from" class="col-sm-4 control-label">Location From</label>
                            <div class="col-sm-5">
                                <select id="device_from" name="device_from" class="custom-select custom-select-sm">
                                    <?php $this->returnDeviceOptions(false); ?>
                                </select>
                            </div>
                        </div>  
                        
                        <div class="form-group row">
                            <label for="ltn_type" class="col-sm-4 control-label">Stock Type</label>
                            <div class="col-sm-4">
                                <select id="ltn_type" name="ltn_type" class="custom-select custom-select-sm">
                                    <option value="S">Sellable </option>
                                    <option value="NS">Non-Sellable ( Damage Returns )</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="eff_date" class="col-sm-4 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" data-date-container="#Ltn-addmodel" class="form-control datepicker form-control-sm" id="eff_date" name="eff_date" placeholder="Date">
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="bill_bookcode" class="col-sm-4 control-label">Ref No#</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" id="bill_bookcode" name="bill_bookcode" placeholder="Ref No#">
                            </div>
                        </div>
                        <div class="form-group row">
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
                    <button id="Ltn-add" data-toggle="modal" data-target="#Ltn-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Ltn-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Ltn-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Ltn-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col-2 headerdiv'>DEVICE-FROM</div>
            <div class='col-2 headerdiv'>DEVICE-TO</div>
            <div class='col-1 headerdiv'>DATE</div>
            <div class='col headerdiv'>REMARKS</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Ltn-list',
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
