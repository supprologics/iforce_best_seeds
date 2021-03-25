<script>

    $(function () {
        loadcalender();
        $("#schedule-form").ajaxForm({
            beforeSend: function () {
                return $("#schedule-form").validate({
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
                loadcalender();
            }
        });

    });

    $(document).on("click", "#btn-submit-schedule", function () {
        $("#schedule-form").submit();
    });

    $(document).on("click", "#calander-searchbtn", function () {
        loadcalender();
    });

    $(document).on("click", ".cngShift", function () {
        var effdate = $(this).attr("data-date");
        var device_id = $(this).attr("data-device");
        loadRoutes(effdate);
        $("#date-title").html(effdate);
        $("#eff_date").val(effdate);
        $("#schedule-addmodel").modal("show");
    });

    function loadRoutes(effdate) {
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("device/loadroute") ?>",
            async: false,
            type: 'post',
            async: false,
            data: {
                effdate: effdate,
                device_id: <?php echo $model->id; ?>
            }
        }).done(function (data) {
            $("#areas_id").html(data);
        });
    }

    function loadcalender() {
       $.ajax({
            url: "<?php echo Yii::app()->createUrl("device/calender") ?>",
            async: false,
            type: 'post',
            data: {
                month: $("#month_grid").val(),
                year: $("#year_grid").val(),
                device_id: <?php echo $model->id; ?>
            }
        }).done(function (data) {
            $("#calender").html(data);
        });
    }

    $(document).on("click", "#approve_all", function (e) {
        e.preventDefault();
        var conf = confirm("Are You Sure, You want to Approve All Days ?");
        if (conf == false) {
            return;
        }
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("device/UpdateAllSchedules") ?>",
            async: false,
            type: 'post',
            data: {
                month: $("#month_grid").val(),
                year: $("#year_grid").val(),
                device_id: <?php echo $model->id; ?>
            },
            error: showResponse,
            success: function (data) {
                loadcalender();
                showResponse(data);
            }
        });

    });

</script>

<!-- Submit Form BY model -->
<div class="modal fade" id="schedule-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Route for <span id="date-title"></span></h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('device/scheduleUpdate') ?>" method="post" id="schedule-form">
                        <input type="hidden" name="device_id" id="device_id" value="<?php echo $model->id; ?>" />
                        <input type="hidden" name="eff_date" id="eff_date"  />
                        <div class="form-group row">
                            <label for="areas_id" class="col-sm-3 control-label">Routes</label>
                            <div class="col-sm-9">
                                <select id="areas_id" name="areas_id" class="custom-select custom-select-sm">

                                </select>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button id="btn-submit-schedule" type="button" class="btn btn-success btn-sm">Approve</button>
            </div>
        </div>
    </div>
</div>
<!-- Submit Form BY model -->

<div id="title-nav" class="inputsearch">
    <div class="row justify-content-between">

        <div class="col-5">
            <div class="input-group">
                <select id="year_grid" name="year_grid" class="custom-select-sm custom-select">
                    <?php
                    $lastyear = intval(date("Y")) - 1;
                    $nextyear = intval(date("Y"));
                    for ($y = $lastyear; $y <= $nextyear + 1; $y++) {

                        if (date("Y") == $y) {
                            $sel = "selected='selected'";
                        } else {
                            $sel = "";
                        }

                        echo "<option value='$y' $sel>$y</option>";
                    }
                    ?>
                </select>
                <select id="month_grid" name="month_grid" class="custom-select-sm custom-select">
                    <?php
                    for ($y = 1; $y <= 12; $y++) {

                        if (date("m") == $y) {
                            $sel = "selected='selected'";
                        } else {
                            $sel = "";
                        }

                        echo "<option value='$y' $sel>$y</option>";
                    }
                    ?>
                </select>

                <div class="input-group-append">
                    <button id="calander-searchbtn" class="btn btn-secondary btn-sm" >Load <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 

    </div>
</div>

<div id="calender"></div>
<div class="text-right mt-2">
    <button id="approve_all" class="btn btn-success btn-sm">Approve All</button>
</div>



