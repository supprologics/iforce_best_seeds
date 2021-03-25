<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Users Access Control Window</h1>
        </div>
    </div>
</div>

<script>
    $(function () {
        loaddata();
        $("#Users-update").ajaxForm({
            success: showResponse,
            error: showResponse,
            complete: function () {
                loaddata();
            }
        });
        $("#Users-psw").ajaxForm({
            beforeSend: function () {

                return $("#Users-psw").validate({
                    rules: {
                        password: {
                            required: true,
                        }
                    },
                    messages: {
                        password: {
                            required: "Please Enter a Valid Password"
                        }
                    }
                }).form();

            },
            success: showResponse,
            error: showResponse,
            complete: function () {
                loaddata();
            }
        });
    });

    $(document).on("click", "#btn-update", function (e) {
        e.preventDefault();
        $("#Users-update").submit();
    });

    $(document).on("click", "#btn-psw", function (e) {
        e.preventDefault();
        if (!$("#Users-psw").validate().form()) {
            $("#password").focus();
            return;
        }

        var chk = confirm("Are you sure, You want to RE-SET this User's Password Now ?")
        if (chk == true) {
            $("#Users-psw").submit();
        }
    });

    $(document).on("click", ".sellall", function (e) {
        var dataclass = $(this).attr("data-att");
        if (this.checked) {
            $('.' + dataclass).each(function () {
                this.checked = true;
            });
        } else {
            $('.' + dataclass).each(function () {
                this.checked = false;
            });
        }
    });


    function loaddata() {
        
        $("#Users-form").resetForm();
        var id = "<?php echo $model->id; ?>";
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Users/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Users-update #" + i).is("[type='checkbox']")) {
                    $("#Users-update #" + i).prop('checked', item);
                } else if ($("#Users-update #" + i).is("[type='radio']")) {
                    $("#Users-update #" + i).prop('checked', item);
                } else {
                    $("#Users-update #" + i).val(item);
                }
            });
        });
    }
</script>


<div>
    <div class="row ">
        <div class="col-sm-4">
            <div class="pt-2">
                <table class="table table-bordered table-sm">
                    <tr>
                        <td>Status</td>
                        <td><?php echo!empty($model->online) ? "ACTIVE" : "NO"; ?></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?php echo $model->name; ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><?php echo $model->username; ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $model->email; ?></td>
                    </tr>
                    <tr>
                        <td>SUPER USER</td>
                        <td><?php echo!empty($model->is_supper) ? "YES" : "NO"; ?></td>
                    </tr>
                    <tr>
                        <td>created @</td>
                        <td><?php echo $model->created; ?></td>
                    </tr>
                </table>
            </div>

            <div class="pt-2">
                <h5>Reset User password</h5>
                <form action="<?php echo Yii::app()->createUrl("users/psw/" . $model->id); ?>" method="post" id="Users-psw" class="form-horizontal">
                    <div class="form-group row">
                        <label class="control-label col-sm-4">New Password</label>
                        <div class="col-sm-6">
                            <input type="password"  id="password" name="password" class="form-control required form-control-sm" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-4"></label>
                        <div class="col-sm-6">
                            <button id="btn-psw" class="btn btn-sm btn-warning">RE-SET <span class="oi oi-lock-locked"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="pt-2" style="margin-bottom: 150px;">

                <form action="<?php echo Yii::app()->createUrl("users/update/" . $model->id); ?>" method="post" id="Users-update">


                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Access Permissions</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Monitoring Devices</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Role Based Access Control</th>
                                        <th class="text-center">View / Access</th>
                                        <th class="text-center">Update</th>
                                        <th class="text-center">Create</th>
                                        <th class="text-center">Delete</th>
                                        <th class="text-center">Approvals</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="view" /></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="update" /></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="create" /></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="delete" /></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="approve" /></td>
                                </tr>
                                <?php
                                $list = Access::model()->findAll(array(), array("ORDER" => "name ASC"));
                                foreach ($list as $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value->name; ?><input  type="hidden" name="access_id[]" value="<?php echo $value->id; ?>" /></td>
                                        <td class="text-center"><input type="checkbox" class="view" id="view_<?php echo $value->id; ?>" name="view[<?php echo $value->id; ?>]" /></td>
                                        <td class="text-center"><input type="checkbox" class="update" id="update_<?php echo $value->id; ?>" name="update[<?php echo $value->id; ?>]" /></td>
                                        <td class="text-center"><input type="checkbox" class="create" id="create_<?php echo $value->id; ?>" name="create[<?php echo $value->id; ?>]" /></td>
                                        <td class="text-center"><input type="checkbox" class="delete" id="delete_<?php echo $value->id; ?>" name="delete[<?php echo $value->id; ?>]" /></td>
                                        <td class="text-center"><input type="checkbox" class="approve" id="approve_<?php echo $value->id; ?>" name="approve[<?php echo $value->id; ?>]" /></td>
                                    </tr>
                                    <?php
                                }
                                ?>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                            <table class="table table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Region</th>
                                        <th>Device</th>
                                        <th class="text-center">Access</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"><input type="checkbox" class="sellall" data-att="device_id" /></td>
                                </tr>
                                <?php
                                $list = Device::model()->findAll(array(), array("ORDER" => "name ASC"));
                                foreach ($list as $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value->region->name; ?></td>
                                        <td><?php echo $value->code." - ". $value->name; ?></td>
                                        <td class="text-center"><input type="checkbox" class="device_id" id="device_id_<?php echo $value->id; ?>" name="device_id[<?php echo $value->id; ?>]" /></td>
                                    </tr>
                                    <?php
                                }
                                ?>

                            </table>

                        </div>
                    </div>



                    <div class="row">
                        <div class="col-2">Email</div>
                        <div class="col-6">
                            <input type="text" name="email" id="email" class="form-control form-control-sm" />
                        </div>
                        <div class="col-3">
                            <select class="custom-select custom-select-sm" id="online" name="online">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row pt-1">
                        <div class="col-2">User Level</div>
                        <div class="col-3">
                            <select name="ulevel" id="ulevel" class="custom-select custom-select-sm">
                                <?php
                                for ($i = 1; $i <= 10; $i++) {
                                    echo "<option value='$i'>Level $i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="pt-2">

                        Please Sign-In again for take effect the access permisions.
                        <button id="btn-update" class="btn btn-success btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>