<?php
/* @var $this UsersController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#Users-form").ajaxForm({
            beforeSend: function () {

                return $("#Users-form").validate({
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
                    window.location.href = "<?php echo Yii::app()->createUrl("Users"); ?>/" + result.id;
                }
                showResponse(data);
            },
            error: showResponse,
            complete: function () {
                search();
            }
        });

        $('#Users-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "Users-add") {
                $("#Users-form").resetForm();
                $("#Users-form").attr("action", "<?php echo Yii::app()->createUrl('Users/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        window.location.href = "<?php echo Yii::app()->createUrl('Users') ?>/" + id;
    });

    $(document).on("click", "#btn-submit", function () {
        $("#Users-form").submit();
    });


    $(document).on("click", ".Users-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#Users-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Users/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Users-form #" + i).is("[type='checkbox']")) {
                    $("#Users-form #" + i).prop('checked', item);
                } else if ($("#Users-form #" + i).is("[type='radio']")) {
                    $("#Users-form #" + i).prop('checked', item);
                } else {
                    $("#Users-form #" + i).val(item);
                }
            });
            $("#Users-form").attr("action", "<?php echo Yii::app()->createUrl('Users/update') ?>/" + id);
        });

        $("#Users-addmodel").modal('show');
    });

    $(document).on("click", ".Users-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('Users/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#Users-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#Users-search", function () {
        search();
    });

    $(document).on("change", "#Users-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('Users-list', {
            data: {
                val: $("#Users-search").val(),
                pages: $("#Users-pages").val()
            },
            complete: function () {
                int();
            }
        });
    }


</script>
<!-- //END SCRIPT -->

<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Users Registration Section</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Users-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">New User Adding Form</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Users/create') ?>" method="post" id="Users-form">

                        <div class="form-group row">
                            <label for="fullname" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" required class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                            </div>
                        </div>
                         <div class="form-group row">
                            <label for="email" class="col-sm-4 control-label">Email Address</label>
                            <div class="col-sm-8">
                                <input type="text" required="true" class="form-control form-control-sm" id="email" name="email" placeholder="Email Address @">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="level" class="col-sm-4 control-label">Level</label>
                            <div class="col-sm-4">
                                <select name="level" id="level" class="custom-select custom-select-sm">
                                    <?php
                                    
                                    for($i=1;$i <= 10; $i++){
                                        echo "<option value='$i'>Level $i</option>";
                                    }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-sm-4 control-label">Username</label>
                            <div class="col-sm-4">
                                <input type="text" required class="form-control form-control-sm" id="username" name="username" placeholder="Username">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-4">
                                <input type="text" required class="form-control form-control-sm" id="password" name="password" placeholder="Password">
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
                    <button id="Users-add" data-toggle="modal" data-target="#Users-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Users-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Users-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Users-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col-4 headerdiv'>Full Name</div>
            <div class='col-3 headerdiv'>Email</div>
            <div class='col-2 headerdiv'>Username</div>
            <div class='col headerdiv'>Member Since</div>
            <div class='col headerdiv'>Status</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Users-list',
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
