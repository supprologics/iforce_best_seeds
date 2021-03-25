<?php
/* @var $this DeviceController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $(document).ready(function () {

        $("#Device-form").ajaxForm({
            beforeSend: function () {

                return $("#Device-form").validate({
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

        $('#Device-addmodel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.attr("id") === "Device-add") {
                $("#Device-form").resetForm();
                $("#Device-form").attr("action", "<?php echo Yii::app()->createUrl('Device/create') ?>/");
                $(".hideonupdate").show();
            } else {
                $(".hideonupdate").hide();
            }
        });

    });


    $(document).on("click", ".clickable", function () {
        var id = $(this).parents("div.row").attr("data-id");
        window.location.href = "<?php echo Yii::app()->createUrl('Device') ?>/" + id;
    });

    $(document).on("click", "#btn-submit", function () {
        $("#Device-form").submit();
    });


    $(document).on("click", ".Device-update", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#Device-form").resetForm();
        //Handle JSON DATA to Update FORM
        $.getJSON("<?php echo Yii::app()->createUrl('Device/jsondata') ?>/" + id).done(function (data) {
            $.each(data, function (i, item) {

                if ($("#Device-form #" + i).is("[type='checkbox']")) {
                    $("#Device-form #" + i).prop('checked', item);
                } else if ($("#Device-form #" + i).is("[type='radio']")) {
                    $("#Device-form #" + i).prop('checked', item);
                } else {
                    $("#Device-form #" + i).val(item);
                }
            });
            $("#Device-form").attr("action", "<?php echo Yii::app()->createUrl('Device/update') ?>/" + id);
        });

        $("#Device-addmodel").modal('show');
    });

    $(document).on("click", ".Device-delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var confirmdata = confirm("Are you sure, you want to delete this record ?");
        if (confirmdata == true) {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('Device/delete') ?>/" + id,
                type: "POST",
                success: showResponse,
                error: showResponse
            }).done(function (data) {
                search();
            });
        }
    });

    $(document).on("click", "#Device-searchbtn", function () {
        search();
    });

    $(document).on("keyup", "#Device-search", function () {
        search();
    });

    $(document).on("change", "#Device-pages", function () {
        search();
    });

    function search() {
        $.fn.yiiListView.update('Device-list', {
            data: {
                val: $("#Device-search").val(),
                pages: $("#Device-pages").val()
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
            <h1>Devices Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Device-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Device - FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Device/create') ?>" method="post" id="Device-form">

                        <div class="form-group row">
                            <label for="region_id" class="col-sm-4 control-label">Region</label>
                            <div class="col-sm-6">
                                <select id="region_id" name="region_id" class="custom-select custom-select-sm">
                                    <?php
                                    $list = Region::model()->findAll();
                                    foreach ($list as $value) {
                                        echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="locations" class="col-sm-4 control-label">Territory</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="60" required="true" class="form-control form-control-sm" id="locations" name="locations" placeholder="Territory">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="map_area" class="col-sm-4 control-label">Province</label>
                            <div class="col-sm-6">
                                <select id="map_area" name="map_area" class="custom-select custom-select-sm">
                                    <option value="LK-1">Western province</option>
                                    <option value="LK-2">Central Province</option>
                                    <option value="LK-3">Southern Province</option>
                                    <option value="LK-4">Northern Province</option>
                                    <option value="LK-5">Eastern Province</option>
                                    <option value="LK-6">North-western Province</option>
                                    <option value="LK-7">North-Central Province</option>
                                    <option value="LK-8">Uva Province</option>
                                    <option value="LK-9">Sabaragamuwa Province</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="dtype" class="col-sm-4 control-label">Mode</label>
                            <div class="col-sm-6">
                                <select id="dtype" name="dtype" class="custom-select custom-select-sm">
                                    <option value="REP">SALES-REP</option>
                                    <option value="SUP">SUPERVISOR</option>
                                    <option value="ASM">AREA SALES MANAGER</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="code" class="col-sm-4 control-label">Code</label>
                            <div class="col-sm-4">
                                <input type="text" maxlength="4" class="form-control form-control-sm" id="code" name="code" placeholder="Code">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 control-label">Login PIN</label>
                            <div class="col-sm-4">
                                <input type="text"  required="true" class="form-control form-control-sm" id="pin" name="pin" placeholder="Login PIN">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mac_id" class="col-sm-4 control-label">MAC ID#</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="mac_id" name="mac_id" placeholder="MAC ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="target" class="col-sm-4 control-label">Sales Target ( Rs.)</label>
                            <div class="col-sm-6">
                                <input type="text" min="0" class="form-control form-control-sm" id="target" name="target" placeholder="Rs 0.00">
                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="address_line1" class="col-sm-4 control-label">Address Line-01</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="60" required="true" class="form-control form-control-sm" id="address_line1" name="address_line1" placeholder="Address Line-01">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address_line2" class="col-sm-4 control-label">Address Line-02</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="60" required="true" class="form-control form-control-sm" id="address_line2" name="address_line2" placeholder="Address Line-02">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tel_no" class="col-sm-4 control-label">Telephone-01</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="10" required="true" class="form-control form-control-sm" id="tel_no" name="tel_no" placeholder="Telephone Number 01">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tel_no_2" class="col-sm-4 control-label">Telephone-02</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="10" required="true" class="form-control form-control-sm" id="tel_no_2" name="tel_no_2" placeholder="Telephone Number 02">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rep_name" class="col-sm-4 control-label">Rep name</label>
                            <div class="col-sm-8">
                                <input type="text" maxlength="60" required="true" class="form-control form-control-sm" id="rep_name" name="rep_name" placeholder="Rep name">
                            </div>
                        </div>
                        
                        
                        
                        <div class="form-group row">
                            <label for="po_date" class="col-sm-4 control-label">PO Date</label>
                            <div class="col-sm-6">
                                <select id="po_date" name="po_date" class="custom-select custom-select-sm">
                                    <option value="0">SUNDAY</option>
                                    <option value="1">MONDAY</option>
                                    <option value="2">TUESDAY</option>
                                    <option value="3">WEDNESDAY</option>
                                    <option value="4">THURSDAY</option>
                                    <option value="5">FRIDAY</option>
                                    <option value="6">SATURDAY</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="stock_lot" class="col-sm-4 control-label">Application Stock</label>
                            <div class="col-sm-5">
                                <select id="stock_lot" name="stock_lot" class="custom-select custom-select-sm">
                                    <option value="0">NO-LIMITS</option>
                                    <option value="1">MAIN STOCK</option>
                                    <option value="2">VEHICLE</option>                                    
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
                    <button id="Device-add" data-toggle="modal" data-target="#Device-addmodel" class="btn btn-secondary btn-block btn-sm" >
                        Add <span class="oi oi-plus"></span>
                    </button>
                </div>
                <input type="text" id="Device-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Device-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-auto">
            <div class="input-group">
                <select id="Device-pages" name="pages" class="custom-select custom-select-sm">
                    <option>10 Pages</option>
                    <option selected="selected">50 Pages</option>
                    <option>100 Pages</option>
                </select>
            </div>
        </div>

    </div>
</div>




<div style="margin-bottom: 50px;">
    <div class="table-box">

        <div class="row no-gutters">
            <div class='col-1 headerdiv'>REGION</div>
            <div class='col-1 headerdiv'>MODE</div>
            <div class='col-1 headerdiv'>TERRITORY</div>
            <div class='col-1 headerdiv'>CODE</div>
            <div class='col-2 headerdiv'>NAME</div>
            
            <div class='col-2 headerdiv'>ADDRESS</div>
            <div class='col-1 headerdiv'>TEL</div>
            <div class='col-1 headerdiv'>REP</div>
            <div class='col  text-right headerdiv'>TARGET</div>
            <div class='col headerdiv text-right'>PIN</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Device-list',
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
