<script>
    $ (function () {

       loadCustomers ($ ("#devices_id").val ());
       $ (".datepicker").datepicker ({
          format: "yyyy-mm-dd",
          autoclose: true,
          todayHighlight: true
       });

       $ ('a[data-toggle="tab"]').on ('show.bs.tab', function (e) {
           $(".inputs").val("");           
       });

    });



    $ (document).on ("change", "#devices_id", function () {
       loadCustomers ($ ("#devices_id").val ());
    });


    function loadCustomers (id) {
       $.widget ("custom.tablecomplete", $.ui.autocomplete, {
          _create: function () {
             this._super ();
             this.widget ().menu ("option", "items", "> tr:not(.ui-autocomplete-header)");
          },
          _renderMenu: function (ul, items) {
             var self = this;
             //table definitions
             var $t = $ ("<table class='table table-sm table-sp'>", {
                border: 0
             }).appendTo (ul);
             $t.append ($ ("<thead>"));
             $t.find ("thead").append ($ ("<tr>"));
             var $row = $t.find ("tr");
             $ ("<th>").html ("Customer Name").appendTo ($row);
             $ ("<th>").html ("Code").appendTo ($row);
             $ ("<th>").html ("Outstanding").appendTo ($row);
             $ ("<tbody>").appendTo ($t);
             $.each (items, function (index, item) {
                self._renderItemData (ul, $t.find ("tbody"), item);
             });
          },
          _renderItemData: function (ul, table, item) {
             return this._renderItem (table, item).data ("ui-autocomplete-item", item);
          },
          _renderItem: function (table, item) {
             var $row = $ ("<tr>", {
                class: "ui-menu-item",
                role: "presentation"
             })
             $ ("<td>").html (item.value).appendTo ($row);
             $ ("<td>").html (item.name).appendTo ($row);
             $ ("<td>").html (item.due).appendTo ($row);
             return $row.appendTo (table);
          }
       });
       function _doFocusStuff (event, ui) {
          if (ui.item) {
             var item = ui.item;
             $ ("#customers").val (item.name);
          }
          $ ("#customers_id").val ("");
          return false;
       }
       var autocomplete = $ ("#customers").tablecomplete ({
          minLength: 2,
          source: "<?php echo Yii::app()->createUrl('customers/loadlist/'); ?>/" + id,
          response: function (event, ui) {
             if (ui.content.length == 0) {
                showError ("No Customer Code Found !");
             }
          },
          focus: _doFocusStuff,
          select: function (event, ui) {
             $ ("#customers_id").val (ui.item.id);
             $ ("#date_from").focus ();
          }
       });
    }


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
                    <select name="devices_id[]" required="true" class="custom-select custom-select-sm" id="devices_id" multiple="true" style="height: 300px;">
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
            </div>
            <div  class="form-row form-group">
                <label for="name" class="col-sm-2 control-label">Period To</label>                
                <div class="col-sm-2">
                    <input type="text" required="true" data-date-container="#datec" class="form-control form-control-sm datepicker" value="<?php echo date("Y-m-d"); ?>" id="date_to" name="date_to" placeholder="End Date">
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