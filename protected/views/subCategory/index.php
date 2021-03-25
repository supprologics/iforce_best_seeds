<?php
/* @var $this SubCategoryController */
/* @var $dataProvider CActiveDataProvider */

?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#SubCategory-form").ajaxForm({
            beforeSend: function () {

                return $("#SubCategory-form").validate({
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
                }).form();

            },
            success: showResponse,
            error: showResponse,
            complete: function () {
                search();
            }
        });

        $('#SubCategory-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "SubCategory-add") {
                $("#SubCategory-form").resetForm();
                $("#SubCategory-form").attr("action", "<?php echo Yii::app()->createUrl('SubCategory/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        window.location.href = "<?php echo Yii::app()->createUrl('SubCategory') ?>/"+id;
    });

    $(document).on("click", "#btn-submit", function () {
        $("#SubCategory-form").submit();
    });


    $(document).on("click", ".SubCategory-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#SubCategory-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('SubCategory/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#SubCategory-form #" + i).is("[type='checkbox']")) {
                    $("#SubCategory-form #" + i).prop('checked', item);
                } else if ($("#SubCategory-form #" + i).is("[type='radio']")) {
                    $("#SubCategory-form #" + i).prop('checked', item);
                } else {
                    $("#SubCategory-form #" + i).val(item);
                }
            });
            $("#SubCategory-form").attr("action", "<?php echo Yii::app()->createUrl('SubCategory/update') ?>/" + id);
        });

        $("#SubCategory-addmodel").modal('show');
    });

    $(document).on("click", ".SubCategory-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('SubCategory/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#SubCategory-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#SubCategory-search", function () {
        search();
    });

    $(document).on("change", "#SubCategory-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('SubCategory-list', {
            data: {
                val: $("#SubCategory-search").val(),
                pages: $("#SubCategory-pages").val()
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
            <h1>Sub Categories for - <?php echo $model->name; ?></h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="SubCategory-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('SubCategory/create') ?>" method="post" id="SubCategory-form">
                        <input type="hidden" name="brands_id" id="brands_id" value="<?php echo $model->id; ?>">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                            </div>
                        </div>   
                        <div class="form-group row">
                            <label for="is_dashboard" class="col-sm-4 control-label">Selling Products ?</label>
                            <div class="col-sm-4">
                                <select id="is_dashboard" name="is_dashboard" class="custom-select custom-select-sm">
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
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
    <div class="row justify-content-between">

        <div class="col-4">
            <div class="input-group">
                <div class="input-group-append">
                    <button id="SubCategory-add" data-toggle="modal" data-target="#SubCategory-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="SubCategory-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="SubCategory-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="SubCategory-pages" name="pages" class="custom-select custom-select-sm">
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
<div class='col headerdiv'>NAME</div>
<div class='col headerdiv'>SELLING</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataProvider,
            'itemView'=>'_view',
            'enablePagination' => true,
            'summaryText' => '{page}/{pages} pages',
            'id' => 'SubCategory-list',
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
            )); ?>
        </div>


    </div>
</div>
