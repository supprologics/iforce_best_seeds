<?php
/* @var $this AdjController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#Adj-form").ajaxForm({
            beforeSend: function () {

                return $("#Adj-form").validate({
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
             success: function (data) {
                var result = JSON.parse(data);
                if (result.id != 0) {
                    window.location.href = "<?php echo Yii::app()->createUrl("adj"); ?>/" + result.id;
                }
                showResponse(data);
            },
            error: showResponse,
            complete: function () {
                search();
            }
        });

        $('#Adj-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "Adj-add") {
                $("#Adj-form").resetForm();
                $("#Adj-form").attr("action", "<?php echo Yii::app()->createUrl('Adj/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        var sts = $(this).parents("div.row").attr("data-sts");
        if (sts < 3) {
            window.location.href = "<?php echo Yii::app()->createUrl('Adj') ?>/" + id;
        } else {  
            window.open("<?php echo Yii::app()->createUrl('adj/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus();
        }
    });

    $(document).on("click", "#btn-submit", function () {
        $("#Adj-form").submit();
    });


    $(document).on("click", ".Adj-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#Adj-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Adj/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Adj-form #" + i).is("[type='checkbox']")) {
                    $("#Adj-form #" + i).prop('checked', item);
                } else if ($("#Adj-form #" + i).is("[type='radio']")) {
                    $("#Adj-form #" + i).prop('checked', item);
                } else {
                    $("#Adj-form #" + i).val(item);
                }
            });
            $("#Adj-form").attr("action", "<?php echo Yii::app()->createUrl('Adj/update') ?>/" + id);
        });

        $("#Adj-addmodel").modal('show');
    });

    $(document).on("click", ".Adj-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('Adj/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#Adj-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#Adj-search", function () {
        search();
    });

    $(document).on("change", "#Adj-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('Adj-list', {
            data: {
                val: $("#Adj-search").val(),
                pages: $("#Adj-pages").val()
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
            <h1>Stock Adjustments & Manual Stock Entry View For Regional Stock</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Adj-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Stock Adjustments - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Adj/create') ?>" method="post" id="Adj-form">
                        <input type="hidden" id="data_type" name="data_type" value="R">
                        <div class="form-group row">
                            <label for="device_id" class="col-sm-4 control-label">Location</label>
                            <div class="col-sm-8">
                                <select id="device_id" name="device_id" class="custom-select custom-select-sm">
                                    <?php $this->returnDeviceOptions(false); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="lot_to" class="col-sm-4 control-label">Stock Lot</label>
                            <div class="col-sm-5">
                                <select id="lot_no" name="lot_no" class="custom-select custom-select-sm">
                                    <option value="1">MAIN STOCK</option>                                  
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="adj_type" class="col-sm-4 control-label">Stock Type</label>
                            <div class="col-sm-4">
                                <select id="adj_type" name="adj_type" class="custom-select custom-select-sm">
                                    <option value="S">Sellable </option>
                                    <option value="NS">Non-Sellable </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="eff_date" class="col-sm-4 control-label">Date</label>
                            <div class="col-sm-4">
                                <input type="text" data-date-container="#Adj-addmodel" value='<?php echo date("Y-m-d"); ?>' class="form-control datepicker form-control-sm" id="eff_date" name="eff_date" placeholder="Date">
                            </div>
                        </div>  
                        
                        
                        <div class="form-group row">
                            <label for="remarks" class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8">
                                <textarea name="remarks" id="remarks" rows="2" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="eff_date" class="col-sm-4 control-label"></label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input checked="" type="checkbox" class="form-check-input" id="bufer_stock" name="bufer_stock" value="1">
                                    <label  class="form-check-label" for="exampleCheck1">Load All Products Sheet</label>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row mt-2">
                            <label for="bufer_init" class="col-sm-4 control-label"></label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="bufer_init" name="bufer_init" value="1" >
                                    <label  class="form-check-label" for="exampleCheck1">ZERO Initialization</label>
                                </div>
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
                    <button id="Adj-add" data-toggle="modal" data-target="#Adj-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Adj-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Adj-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Adj-pages" name="pages" class="custom-select custom-select-sm">
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
            
            <div class='col-2 headerdiv'>ADJUSTMENT</div>
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
                'id' => 'Adj-list',
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
