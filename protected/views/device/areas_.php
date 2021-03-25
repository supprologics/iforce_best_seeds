<!--- Script -->
<script>

    $(document).ready(function () {

        $("#Areas-form").ajaxForm({
            beforeSend: function () {

                return $("#Areas-form").validate({
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

        $('#Areas-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "Areas-add") {
                $("#Areas-form").resetForm();
                $("#Areas-form").attr("action", "<?php echo Yii::app()->createUrl('Areas/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        window.location.href = "<?php echo Yii::app()->createUrl('Areas') ?>/" + id;
    });

    $(document).on("click", "#btn-submit", function () {
        $("#Areas-form").submit();
    });


    $(document).on("click", ".Areas-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#Areas-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Areas/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Areas-form #" + i).is("[type='checkbox']")) {
                    $("#Areas-form #" + i).prop('checked', item);
                } else if ($("#Areas-form #" + i).is("[type='radio']")) {
                    $("#Areas-form #" + i).prop('checked', item);
                } else {
                    $("#Areas-form #" + i).val(item);
                }
            });
            $("#Areas-form").attr("action", "<?php echo Yii::app()->createUrl('Areas/update') ?>/" + id);
        });

        $("#Areas-addmodel").modal('show');
    });

    $(document).on("click", ".Areas-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('Areas/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#Areas-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#Areas-search", function () {
        search();
    });

    $(document).on("change", "#Areas-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('Areas-list', {
            data: {
                val: $("#Areas-search").val(),
                pages: $("#Areas-pages").val()
            },
            complete: function () {
                //CODE GOES HERE
            }
        });
    }


</script>
<!-- //END SCRIPT -->

<!-- Submit Form BY model -->
<div class="modal fade" id="Areas-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">AREA - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Areas/create') ?>" method="post" id="Areas-form">

                        <input type="hidden" name="device_id" id="device_id" value="<?php echo $model->id; ?>" />
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="is_dir" class="col-sm-4 control-label">Sales Mode</label>
                            <div class="col-sm-4">
                                <select id="is_dir" name="is_dir" class="custom-select custom-select-sm">
                                    <option value="0">Non-Direct</option>
                                    <option value="1">Direct</option>
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

        <div class="col-5">
            <div class="input-group">
                <div class="input-group-append">
                    <button id="Areas-add" data-toggle="modal" data-target="#Areas-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Areas-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Areas-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Areas-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col headerdiv'>LAST VISITED DATE</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_viewAreas',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Areas-list',
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