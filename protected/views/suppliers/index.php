<?php
/* @var $this SuppliersController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#Suppliers-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Suppliers-form").validate ({
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

       $ ('#Suppliers-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Suppliers-add") {
             $ ("#Suppliers-form").resetForm ();
             $ ("#Suppliers-form").attr ("action", "<?php echo Yii::app()->createUrl('Suppliers/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('Suppliers') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Suppliers-form").submit ();
    });


    $ (document).on ("click", ".Suppliers-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Suppliers-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('Suppliers/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#Suppliers-form #" + i).is ("[type='checkbox']")) {
                $ ("#Suppliers-form #" + i).prop ('checked', item);
             } else if ($ ("#Suppliers-form #" + i).is ("[type='radio']")) {
                $ ("#Suppliers-form #" + i).prop ('checked', item);
             } else {
                $ ("#Suppliers-form #" + i).val (item);
             }
          });
          $ ("#Suppliers-form").attr ("action", "<?php echo Yii::app()->createUrl('Suppliers/update') ?>/" + id);
       });

       $ ("#Suppliers-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Suppliers-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Suppliers/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Suppliers-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Suppliers-search", function () {
       search ();
    });

    $ (document).on ("change", "#Suppliers-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Suppliers-list', {
          data: {
             val: $ ("#Suppliers-search").val (),
             pages: $ ("#Suppliers-pages").val ()
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
            <h1>Supplier Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Suppliers-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier - Form</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Suppliers/create') ?>" method="post" id="Suppliers-form">
                        
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Supplier Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 control-label">address</label>
                            <div class="col-sm-8">
                                <textarea name="address" id="address" rows="2" class="form-control form-control-sm" placeholder="Postal Address"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm" id="mobile" name="mobile" placeholder="Mobile Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fax" class="col-sm-4 control-label">Fax</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm" id="fax" name="fax" placeholder="Fax Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-4 control-label">Email</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="Email Address">
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
                    <button id="Suppliers-add" data-toggle="modal" data-target="#Suppliers-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Suppliers-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Suppliers-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Suppliers-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col-2 headerdiv'>NAME</div>
            <div class='col-4 headerdiv'>ADDRESS</div>
            <div class='col headerdiv'>MOBILE</div>
            <div class='col headerdiv'>FAX</div>
            <div class='col headerdiv'>EMAIL</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Suppliers-list',
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
