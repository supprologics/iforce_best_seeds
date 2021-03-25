<?php
/* @var $this CustomersController */
/* @var $dataProvider CActiveDataProvider */
?>



<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#Customers-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#Customers-form").validate ({
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
             }).form ();

          },
          success: showResponse,
          error: showResponse,
          complete: function () {
             search ();
          }
       });

       $ ('#Customers-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "Customers-add") {
             $ ("#Customers-form").resetForm ();
             $ ("#Customers-form").attr ("action", "<?php echo Yii::app()->createUrl('Customers/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('Customers') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#Customers-form").submit ();
    });

    function setGMap () {

       var latData = parseFloat ($ ("#latitude").val ());
       var longData = parseFloat ($ ("#longitude").val ());

       if ($ ("#latitude").val () != "" && $ ("#longitude").val () != "") {
          var myLatLng = {lat: latData, lng: longData};
       } else {
          var myLatLng = {lat: 6.930444, lng: 79.852601};
       }

       map = new google.maps.Map (document.getElementById ('map'), {
          center: myLatLng,
          zoom: 12,
          streetViewControl: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
       });


       var marker = new google.maps.Marker ({
          position: myLatLng,
          map: map,
          title: 'Geolocation of the project'
       });

       map.addListener ('click', function (event) {
          $ ("#latitude").val (event.latLng.lat ());
          $ ("#longitude").val (event.latLng.lng ());
          var newLatLang = {lat: event.latLng.lat (), lng: event.latLng.lng ()};
          marker.setPosition (newLatLang);
       });

       google.maps.event.trigger (map, 'resize');
    }


    $ (document).on ("click", ".Customers-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#Customers-form").resetForm ();
       //Handle JSON DATA to Update FORM

       $.ajax ({
          url: "<?php echo Yii::app()->createUrl('Customers/jsondata') ?>/" + id,
          async: false,
          dataType: 'json'
       }).done (function (data) {
          $.each (data, function (i, item) {
             if ($ ("#Customers-form #" + i).is ("[type='checkbox']")) {
                $ ("#Customers-form #" + i).prop ('checked', item);
             } else if ($ ("#Customers-form #" + i).is ("[type='radio']")) {
                $ ("#Customers-form #" + i).prop ('checked', item);
             } else {
                $ ("#Customers-form #" + i).val (item);
             }

             if (i == 'cover_image') {
                $ ("#cover_img").attr ("src", "<?php echo Yii::app()->request->baseUrl; ?>/images/" + item);
             }


          });
          $ ("#Customers-form").attr ("action", "<?php echo Yii::app()->createUrl('Customers/update') ?>/" + id);
       });

       setGMap ();
       $ ("#Customers-addmodel").modal ('show');
    });

    $ (document).on ("click", ".Customers-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('Customers/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search ();
          });
       }
    });

    $ (document).on ("click", "#Customers-searchbtn", function () {
       search ();
    });

    $ (document).on ("keyup", "#Customers-search", function () {
       search ();
    });

    $ (document).on ("change", "#Customers-pages", function () {
       search ();
    });

    function search () {
       $.fn.yiiListView.update ('Customers-list', {
          data: {
             val: $ ("#Customers-search").val (),
             pages: $ ("#Customers-pages").val (),
             ctype: $ ("#type_search").val (),
             device_id: $ ("#devices_search").val ()
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
            <h1>Customers Registry</h1>
        </div>
    </div>
</div>

<!-- Submit Form BY model -->
<div class="modal fade" id="Customers-addmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Customer Update FORM</h4>
            </div>
            <div class="modal-body">

                <div class="cus-form">
                    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('Customers/create') ?>" method="post" id="Customers-form">

                        <div class="form-row">

                            <div class="col-sm-6">

                                <div class="form-row mb-1">
                                    <label for="customer_types_id" class="col-sm-4 control-label">District</label>
                                    <div class="col-sm-8">
                                        <select id="areas_id" required="true" name="areas_id" class="custom-select custom-select-sm">
                                            <option value="">Select a District Area</option>
                                            <?php
                                            
                                            //GET THE USER ACCESS DEVICE IDS
                                            $devlist = $this->returnDevice();
                                            $list = Yii::app()->db->createCommand("SELECT * FROM areas WHERE device_id IN ($devlist)")->queryAll();
                                            foreach ($list as $value) {
                                                echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <label for="customer_types_id" class="col-sm-4 control-label">Type</label>
                                    <div class="col-sm-4">
                                        <select id="customer_types_id" name="customer_types_id" class="custom-select custom-select-sm">
                                            <?php
                                            $list = CustomerTypes::model()->findAll();
                                            foreach ($list as $value) {
                                                echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <label for="name" class="col-sm-4 control-label">Shop Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" required="true" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <label for="brn" class="col-sm-4 control-label">BRN</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control form-control-sm" id="brn" name="brn" placeholder="BRN">
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <label for="name" class="col-sm-4 control-label">Address</label>
                                    <div class="col-sm-8">
                                        <textarea required="true" id="address_no" name="address_no"  class="form-control form-control-sm" placeholder="Address"></textarea>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-row mb-1">
                                    <label for="street" class="col-sm-4 control-label">Street</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" id="street" name="street" placeholder="Street">
                                    </div>
                                </div>
                                
                                <hr/>

                                <div class="form-row mb-1">
                                    <label for="contact_name" class="col-sm-4 control-label">Contact Person</label>
                                    <div class="col-sm-8">
                                        <input type="text" required="true" class="form-control form-control-sm" id="contact_name" name="contact_name" placeholder="Contact Person">
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                                    <div class="col-sm-8">
                                        <input type="text" required="true" class="form-control form-control-sm" id="mobile" name="mobile" placeholder="Mobile NUmber">
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <label for="landline" class="col-sm-4 control-label">Phone</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" id="landline" name="landline" placeholder="Land-line">
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <label for="nic" class="col-sm-4 control-label">NIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" required="true" class="form-control form-control-sm" id="nic" name="nic" placeholder="NIC">
                                    </div>
                                </div>




                            </div>
                            <div class="col-sm-6">

                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">GEO-Location</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="form-group mb-1">
                                            <div class="row">
                                                <div class="col">

                                                    <script>
                                                        function initialize () {
                                                        }
                                                    </script>
                                                    <script async defer
                                                            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJz9rO99_oSy4PmgjuzN_-85oUMeWrD8c&callback=setGMap">
                                                    </script>
                                                    <div id="map" style="height: 200px; width: 100%; z-index: 3000;" ></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row mb-1">
                                            <label for="latitude" class="col-sm-5 control-label">Latitude</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm" id="latitude" name="latitude" placeholder="Latitude">
                                            </div>
                                        </div>
                                        <div class="form-row mb-1">
                                            <label for="longitude" class="col-sm-5 control-label">Longitude</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm" id="longitude" name="longitude" placeholder="Longitude">
                                            </div>
                                        </div>
                                        <div class="form-row mb-1">
                                            <label for="seed_act" class="col-sm-5 control-label">Seed Act Reg No:</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control form-control-sm" id="seed_act" name="seed_act" placeholder="Seed Act Reg No">
                                            </div>
                                        </div>
                                        <div class="form-row mb-1">
                                            <label for="credit_limit" class="col-sm-5 control-label">Credit Limit Rs.</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control form-control-sm" id="credit_limit" name="credit_limit" placeholder="Rs.">
                                            </div>
                                        </div>
                                    </div>
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
    <div class="row justify-content-start no-gutters">
        <div class="col-1">
            <label>&nbsp;</label>
            <div class="input-group-append">
                <button id="Device-add" data-toggle="modal" data-target="#Customers-addmodel" class="btn btn-secondary btn-block btn-sm" >
                    Add <span class="oi oi-plus"></span>
                </button>
            </div>
        </div>
        <div class="col-1">

            <label>Type</label>
            <select name="type_search" id="type_search" class="custom-select custom-select-sm">
                <option value="">Select All</option>
                <?php
                $list = CustomerTypes::model()->findAll();
                foreach ($list as $value) {
                    echo "<option value='" . $value->id . "'>" . $value->name . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-2">
            <label>Device</label>
            <select name="devices_search" id="devices_search" class="custom-select custom-select-sm">
                <option value="">Select All</option>
                <?php $this->returnDeviceOptions(); ?>
            </select>
        </div>
        <div class="col-3">
            <label>Customer Search</label>
            <div class="input-group">

                <input type="text" id="Customers-search" class="form-control form-control-sm" placeholder="Search by Name/Code etc.">
                <div class="input-group-append">
                    <button id="Customers-searchbtn" class="btn btn-secondary btn-sm" >Search <span class="oi oi-magnifying-glass"></span> <span class="glyphicon glyphicon-search"></span></button>
                </div>
            </div>
        </div> 
        <div class="col-4"></div>
        <div class="col-1 align-self-end">
            <label>&nbsp;</label>
            <div class="input-group">
                <select id="Customers-pages" name="pages" class="custom-select custom-select-sm">
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
            <div class='col-1 headerdiv'>CODE</div>
            <div class='col-1 headerdiv'>TYPE</div>
            <div class='col headerdiv'>AREA</div>
            
            <div class='col headerdiv'>NAME</div>
            <div class='col-3 headerdiv'>ADDRESS</div>
            <div class='col-1 headerdiv'>MOBILE</div>
            <div class='col-1 headerdiv'>LANDLINE</div>
            <div class="col-sm-1 headerdiv">&nbsp;</div>
        </div>


        <div class="row no-gutters">
            <?php
            $this->widget('zii.widgets.CListView', array(
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'enablePagination' => true,
                'summaryText' => '{page}/{pages} pages',
                'id' => 'Customers-list',
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
