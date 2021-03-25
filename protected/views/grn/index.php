<?php
/* @var $this GrnController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#Grn-form").ajaxForm({
            beforeSend: function () {

                return $("#Grn-form").validate({
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

        $('#Grn-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "Grn-add") {
                $("#Grn-form").resetForm();
                $("#Grn-form").attr("action", "<?php echo Yii::app()->createUrl('Grn/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        var sts = $(this).parents("div.row").attr("data-sts");
        if (sts == 1) {
            window.location.href = "<?php echo Yii::app()->createUrl('Grn') ?>/" + id;
        } else {
             window.open("<?php echo Yii::app()->createUrl('grn/print/') ?>/" + id, "mywindow", "location=1,status=1,scrollbars=1, width=800,height=600").focus();
        }
    });

    $(document).on("click", "#btn-submit", function () {
        $("#Grn-form").submit();
    });


    $(document).on("click", ".Grn-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#Grn-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Grn/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Grn-form #" + i).is("[type='checkbox']")) {
                    $("#Grn-form #" + i).prop('checked', item);
                } else if ($("#Grn-form #" + i).is("[type='radio']")) {
                    $("#Grn-form #" + i).prop('checked', item);
                } else {
                    $("#Grn-form #" + i).val(item);
                }
            });
            $("#Grn-form").attr("action", "<?php echo Yii::app()->createUrl('Grn/update') ?>/" + id);
        });

        $("#Grn-addmodel").modal('show');
    });

    $(document).on("click", ".Grn-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('Grn/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#Grn-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#Grn-search", function () {
        search();
    });

    $(document).on("change", "#Grn-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('Grn-list', {
            data: {
                val: $("#Grn-search").val(),
                pages: $("#Grn-pages").val()
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
            <h1>Goods Receiving Note Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Grn-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Grn/create') ?>" method="post" id="Grn-form">

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
                
                <input type="text" id="Grn-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Grn-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Grn-pages" name="pages" class="custom-select custom-select-sm">
                    <option>10 Pages</option>
                    <option selected="selected">50 Pages</option>
                    <option>100 Pages</option>
                </select>
            </div>
        </div>

    </div>
</div>




<div style="margin-bottom: 100px;">
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col-2 headerdiv'>CODE</div>
            <div class='col-2 headerdiv'>DEVICE</div>
            <div class='col-2 headerdiv'>PO</div>            
            <div class='col-2 headerdiv'>DATE</div>
            <div class='col headerdiv'>REMARKS</div>
            <div class='col-1 headerdiv'>STATUS</div>
            <div class='col-1 headerdiv'></div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Grn-list',
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
