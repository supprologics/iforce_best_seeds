<script>
    $(function () {

        $(".datepicker").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

        /* JOB ***/
        $.widget("custom.tablecomplete", $.ui.autocomplete, {
            _create: function () {
                this._super();
                this.widget().menu("option", "items", "> tr:not(.ui-autocomplete-header)");
            },
            _renderMenu(ul, items) {
                var self = this;
                //table definitions
                var $t = $("<table class='table table-sm table-sp'>", {
                    border: 0
                }).appendTo(ul);
                $t.append($("<thead>"));
                $t.find("thead").append($("<tr>"));
                var $row = $t.find("tr");
                $("<th>").html("Code").appendTo($row);
                $("<th>").html("Style").appendTo($row);
                $("<th>").html("VPO").appendTo($row);
                $("<th>").html("Supplier").appendTo($row);
                $("<tbody>").appendTo($t);
                $.each(items, function (index, item) {
                    self._renderItemData(ul, $t.find("tbody"), item);
                });
            },
            _renderItemData(ul, table, item) {
                return this._renderItem(table, item).data("ui-autocomplete-item", item);
            },
            _renderItem(table, item) {
                var $row = $("<tr>", {
                    class: "ui-menu-item",
                    role: "presentation"
                });
                $("<td>").html(item.value).appendTo($row);
                $("<td>").html(item.docstyle).appendTo($row);
                $("<td>").html(item.vpo).appendTo($row);
                $("<td>").html(item.supplier).appendTo($row);
                return $row.appendTo(table);
            }
        });

        function _doFocusStuff(event, ui) {
            if (ui.item) {
                var $item = ui.item;
            }
            return false;
        }

        // create the autocomplete
        var autocomplete = $("#job_id").tablecomplete({
            minLength: 2,
            source: "<?php echo Yii::app()->createUrl('job/loadlist/'); ?>/",
            focus: _doFocusStuff
        });
    });
</script>


<div  class="row">
    <div id="datec" class="col">
        <h4 style="margin-bottom: 15px; font-size: 18px;">
            <span class="oi oi-paperclip"></span> 
            <?php echo $title; ?>
        </h4>

        <form target="_blank" class="form-horizontal" action="<?php echo Yii::app()->createUrl('reports/loadreport/') ?>" method="post" >
            <input type="hidden" name="report" value="<?php echo $report; ?>" />
            <div class="form-group row">
                <label for="name" class="col-sm-2 control-label">Device</label>
                <div class="col-sm-6">
                    <select name="devices_id[]" required="true" class="custom-select custom-select-sm required" id="devices_id" multiple="true" style="height: 300px;">
                        <?php $this->returnDeviceOptions(false); ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="customer_types_id" class="col-sm-2 control-label">Customer Type</label>
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
            <div  class="form-row form-group">
                <label for="name" class="col-sm-2 control-label">Period From</label>
                <div class="col-sm-2">
                    <input type="text" required="true" data-date-container="#datec" class="form-control form-control-sm datepicker" value="<?php echo date("Y-m-01"); ?>" id="date_from" name="date_from" placeholder="Start Date">
                </div>
                <div class="col-sm-2">
                    <select name="hour_from" id="hour_from" class="custom-select custom-select-sm">
                        <?php
                        for ($i = 1; $i <= 24; $i++) {

                            $nw = date("H");

                            if ($i == 7) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }

                            echo "<option $sel value='$i'>$i:00</option>";
                        }
                        ?>
                    </select>
                </div>                
            </div>
            <div  class="form-row form-group">
                <label for="name" class="col-sm-2 control-label">Period To</label>                
                <div class="col-sm-2">
                    <input type="text" required="true" data-date-container="#datec" class="form-control form-control-sm datepicker" value="<?php echo date("Y-m-d"); ?>" id="date_to" name="date_to" placeholder="End Date">
                </div>
                <div class="col-sm-2">
                    <select name="hour_to" id="hour_to" class="custom-select custom-select-sm">
                        <?php
                        for ($i = 1; $i <= 24; $i++) {

                            $nw = date("H");

                            if ($i == $nw + 1) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }

                            echo "<option $sel value='$i'>$i:00</option>";
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