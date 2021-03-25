<script>
    $(function () {



    });

    $(document).on("change", "#device_id", function (e) {
        e.preventDefault();
        var location_id = $(this).val();
        if (location_id == "") {
            $("#areas_id").html("<option value=''>Select All</option>");
        } else {
            loadDevices($(this).val());
        }
    });
    

    function loadDevices(id) {
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("site/areaslist"); ?>/" + id,
            success: function (data) {
                $("#areas_id").html(data);
            },
            error: showResponse
        });
    }

</script>


<div class="row">
    <div class="col">
        <h4 style="margin-bottom: 15px; font-size: 18px;">
            <span class="oi oi-paperclip"></span> 
            <?php echo $title; ?>
        </h4>

        <form target="_blank" class="form-horizontal" action="<?php echo Yii::app()->createUrl('reports/loadreport/') ?>" method="post" >
            <input type="hidden" name="report" value="<?php echo $report; ?>" />
            <div class="form-group row">
                <label for="device_id" class="col-sm-2 control-label">Sales Rep</label>
                <div class="col-sm-6">
                    <select name="device_id" class="custom-select custom-select-sm" id="device_id">
                        <?php $this->returnDeviceOptions(); ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="areas_id" class="col-sm-2 control-label">Route</label>
                <div class="col-sm-6">
                    <select name="areas_id" class="custom-select custom-select-sm" id="areas_id">
                        <option value="">Select All</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="customer_types_id" class="col-sm-2 control-label">Type</label>
                <div class="col-sm-5">
                    <select name="customer_types_id" class="custom-select custom-select-sm" id="customer_types_id">
                        <option value="">Select ALL</option>
                        <?php
                        $list = CustomerTypes::model()->findAll();
                        foreach ($list as $value) {
                            echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="plies" class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <button class="btn btn-primary">View Report</button>
                </div>
            </div>


        </form>
    </div>
</div>