<?php
/* @var $this AreasController */
/* @var $dataProvider CActiveDataProvider */
?>


<script>
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


    $ (document).on ("click", "#update_map", function (e) {
       e.preventDefault ();
       var lat = $ ("#latitude").val ();
       var lng = $ ("#longitude").val ();

       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("device/update/" . $model->id) ?>",
          type: "POST",
          data: {
              lat : lat,
              lng : lng
          },
          success: function(data){
              showResponse(data);
          },
          error: showResponse
       });

    });
</script>


<div class="row" style="margin-bottom: 50px;">
    <div class="col-sm-3 col-xs-12  ">
        <div style="padding: 10px;" class="bg-light p-4 border-dark ">
            <h2 style="margin: 2px 0; border-bottom: 1px solid #bebebe; padding-bottom: 10px;"><?php echo $model->code; ?></h2>
            <h3><?php echo $model->name; ?></h3>
            <p><?php echo $model->region->name; ?></p>
        </div>

        <h6 class="pt-2">Base Location</h6>
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
            <label for="latitude" class="col-sm-4 control-label">Latitude</label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" id="latitude" value="<?php echo $model->lat; ?>" name="latitude" placeholder="Latitude">
            </div>
        </div>
        <div class="form-row mb-1">
            <label for="longitude" class="col-sm-4 control-label">Longitude</label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" id="longitude" value="<?php echo $model->lng; ?>" name="longitude" placeholder="Longitude">
            </div>
        </div>
        <div class="form-row mb-1">
            <label for="update_map" class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                <button class="btn btn-success" id="update_map">Update Location</button>
            </div>
        </div>
    </div>
    <div class="col-sm-9 col-xs-12">

        <div style="margin-top: 10px;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Areas Registry</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="buffer-tab" data-toggle="tab" href="#buffer" role="tab" aria-controls="buffer" aria-selected="true">Buffer Stock Limits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="route-tab" data-toggle="tab" href="#route" role="tab" aria-controls="route" aria-selected="true">Route Planer</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <?php require 'areas_.php'; ?>
                </div>
                <div class="tab-pane show" id="buffer" role="tabpanel" aria-labelledby="buffer-tab">
                    <?php require 'buffer_.php'; ?>
                </div>
                <div class="tab-pane show" id="route" role="tabpanel" aria-labelledby="route-tab">
                    <?php require 'route_.php'; ?>
                </div>
            </div>
        </div>



    </div>
</div>


