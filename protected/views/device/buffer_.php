
<!--- Script -->
<script>

    $ (document).ready (function () {

       $ ("#BuferStock-form").ajaxForm ({
          beforeSend: function () {

             return $ ("#BuferStock-form").validate ({
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
             search_buffer ();
          }
       });

       $ ('#BuferStock-addmodel').on ('show.bs.modal', function (event) {
          var button = $ (event.relatedTarget);
          if (button.attr ("id") === "BuferStock-add") {
             $ ("#BuferStock-form").resetForm ();
             $ ("#BuferStock-form").attr ("action", "<?php echo Yii::app()->createUrl('BuferStock/create') ?>/");
             $ (".hideonupdate").show ();
          } else {
             $ (".hideonupdate").hide ();
          }
       });

    });


    $ (document).on ("click", ".clickable", function () {
       var id = $ (this).parents ("div.row").attr ("data-id");
       window.location.href = "<?php echo Yii::app()->createUrl('BuferStock') ?>/" + id;
    });

    $ (document).on ("click", "#btn-submit", function () {
       $ ("#BuferStock-form").submit ();
    });


    $ (document).on ("click", ".BuferStock-update", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       $ ("#BuferStock-form").resetForm ();
       //Handle JSON DATA to Update FORM
       $.getJSON ("<?php echo Yii::app()->createUrl('BuferStock/jsondata') ?>/" + id).done (function (data) {
          $.each (data, function (i, item) {

             if ($ ("#BuferStock-form #" + i).is ("[type='checkbox']")) {
                $ ("#BuferStock-form #" + i).prop ('checked', item);
             } else if ($ ("#BuferStock-form #" + i).is ("[type='radio']")) {
                $ ("#BuferStock-form #" + i).prop ('checked', item);
             } else {
                $ ("#BuferStock-form #" + i).val (item);
             }
          });
          $ ("#BuferStock-form").attr ("action", "<?php echo Yii::app()->createUrl('BuferStock/update') ?>/" + id);
       });

       $ ("#BuferStock-addmodel").modal ('show');
    });

    $ (document).on ("click", ".BuferStock-delete", function (e) {
       e.preventDefault ();
       var id = $ (this).attr ("data-id");
       var confirmdata = confirm ("Are you sure, you want to delete this record ?");
       if (confirmdata == true) {
          $.ajax ({
             url: "<?php echo Yii::app()->createUrl('BuferStock/delete') ?>/" + id,
             type: "POST",
             success: showResponse,
             error: showResponse
          }).done (function (data) {
             search_buffer ();
          });
       }
    });

    $ (document).on ("click", "#BuferStock-search_bufferbtn", function () {
       search_buffer ();
    });

    $ (document).on ("keyup", "#BuferStock-search_buffer", function () {
       search_buffer ();
    });

    $ (document).on ("change", "#BuferStock-pages", function () {
       search_buffer ();
    });

    function search_buffer () {
       $.fn.yiiListView.update ('BuferStock-list', {
          data: {
             val: $ ("#BuferStock-search_buffer").val (),
             pages: $ ("#BuferStock-pages").val ()
          },
          complete: function () {
             //CODE GOES HERE
          }
       });
    }

    $ (document).on ("click", "#save", function (e) {
       e.preventDefault ();
       save ();
    });

    function save () {

       var inner_data = $ ("form#inner_table").serializeArray ();

       $.ajax ({
          url: "<?php echo Yii::app()->createUrl("buferStock/updateAll/" . $model->id) ?>",
          data: inner_data,
          type: "POST",
          success: showResponse,
          error: showResponse
       }).done (function (data) {
          search_buffer ();
       });
    }


</script>

<style>

    tr.red th {
        background: #426997;
        color: white;
        border: 1px solid #c8c8c8;
    }
    tr.green th {
        background: green;
        color: white;
        border: 1px solid #c8c8c8;
    }
    tr.purple th {
        background: purple;
        color: white;
        border: 1px solid #c8c8c8;
    }
    th {
        position: sticky;
        top: 0px;
        border: 1px solid #c8c8c8;
    }

</style>

<!-- //END SCRIPT -->
<form action="#" method="post" id="inner_table">
    <div style="height: 400px; overflow: auto;">

        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider' => $dataProviderBuffer,
            'itemView' => '_viewBuffer',
            'enablePagination' => true,
            'summaryText' => '{page}/{pages} pages',
            'id' => 'BuferStock-list',
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
    <div class="text-right">
        <button id="save" class="btn btn-success btn-sm">Update</button>
    </div>
</form>
