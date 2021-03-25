<?php
/* @var $this InvoiceItemsController */
/* @var $dataProvider CActiveDataProvider */

?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#InvoiceItems-form").ajaxForm({
            beforeSend: function () {

                return $("#InvoiceItems-form").validate({
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

        $('#InvoiceItems-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "InvoiceItems-add") {
                $("#InvoiceItems-form").resetForm();
                $("#InvoiceItems-form").attr("action", "<?php echo Yii::app()->createUrl('InvoiceItems/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        window.location.href = "<?php echo Yii::app()->createUrl('InvoiceItems') ?>/"+id;
    });

    $(document).on("click", "#btn-submit", function () {
        $("#InvoiceItems-form").submit();
    });


    $(document).on("click", ".InvoiceItems-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#InvoiceItems-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('InvoiceItems/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#InvoiceItems-form #" + i).is("[type='checkbox']")) {
                    $("#InvoiceItems-form #" + i).prop('checked', item);
                } else if ($("#InvoiceItems-form #" + i).is("[type='radio']")) {
                    $("#InvoiceItems-form #" + i).prop('checked', item);
                } else {
                    $("#InvoiceItems-form #" + i).val(item);
                }
            });
            $("#InvoiceItems-form").attr("action", "<?php echo Yii::app()->createUrl('InvoiceItems/update') ?>/" + id);
        });

        $("#InvoiceItems-addmodel").modal('show');
    });

    $(document).on("click", ".InvoiceItems-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('InvoiceItems/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#InvoiceItems-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#InvoiceItems-search", function () {
        search();
    });

    $(document).on("change", "#InvoiceItems-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('InvoiceItems-list', {
            data: {
                val: $("#InvoiceItems-search").val(),
                pages: $("#InvoiceItems-pages").val()
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
            <h1>Invoice Items</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="InvoiceItems-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('InvoiceItems/create') ?>" method="post" id="InvoiceItems-form">

                        <div class="form-group row">
                            <label for="code" class="col-sm-4 control-label">Code</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" id="code" name="code" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <textarea name="description" id="description" rows="2" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select" class="col-sm-4 control-label">Types</label>
                            <div class="col-sm-4">
                                <select id="select" name="select" class="custom-select custom-select-sm">
                                    <option>Select the Value</option>
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
                    <button id="InvoiceItems-add" data-toggle="modal" data-target="#InvoiceItems-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="InvoiceItems-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="InvoiceItems-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="InvoiceItems-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col headerdiv'>invoice_item_id</div>
<div class='col headerdiv'>invoice_code</div>
<div class='col headerdiv'>customers_id</div>
<div class='col headerdiv'>items_id</div>
<div class='col headerdiv'>item_name</div>
<div class='col headerdiv'>qty_selable</div>
<div class='col headerdiv'>qty_nonselable</div>
<div class='col headerdiv'>mrp</div>
<div class='col headerdiv'>discount</div>
<div class='col headerdiv'>discount_type</div>
<div class='col headerdiv'>discount_amount</div>
<div class='col headerdiv'>is_manual_dis</div>
<div class='col headerdiv'>total</div>
<div class='col headerdiv'>device_id</div>
<div class='col headerdiv'>eff_date</div>
<div class='col headerdiv'>item_type</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataProvider,
            'itemView'=>'_view',
            'enablePagination' => true,
            'summaryText' => '{page}/{pages} pages',
            'id' => 'InvoiceItems-list',
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
