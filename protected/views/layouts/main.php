<?php /* @var $this Controller */ ?>
<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="language" content="en">

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/open-iconic-bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/template.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/alertify.css">


        <title><?php echo CHtml::encode($this->pageTitle); ?></title>

        <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
        <?php Yii::app()->clientScript->registerCoreScript('jquery.ui'); ?>  

        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.bundle.min.js" ></script>
        <?php Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css'); ?>

        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.form.js'); ?>
        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.validate.js'); ?>
        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/alertify.min.js'); ?>


        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-datepicker.css">
        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/bootstrap-datepicker.js'); ?>

        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.inputmask.bundle.js'); ?>


        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

        
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/all.css" rel="stylesheet">
        

        <script type="text/javascript">

            $ (function () {


               $ (".datepicker").datepicker ({
                  format: "yyyy-mm-dd",
                  autoclose: true,
                  todayHighlight: true
               });
               $ (".timemask").inputmask ("99:99:99", {
                  "placeholder": "HH:MM:SS"
               });
            });

            function dateload () {
               $ (".datepicker").datepicker ({
                  format: "yyyy-mm-dd",
                  autoclose: true,
                  todayHighlight: true
               });
            }

            function showError (responseText) {
               alertify.success (responseText).dismissOthers ();
            }

            function showResponse (responseText, statusText, xhr, $form) {
               $ ("#err").html ("");

               if (responseText.status != null) {
                  alertify.success (responseText.responseText).dismissOthers ();

                  if (typeof value !== "undefined") {
                     $ (".modal form").resetForm ();
                     $ (".modal").modal ('hide');
                  }
               } else {
                  if (typeof (responseText) != 'object') {
                     var responseText = JSON.parse (responseText);
                  }
                  if (responseText.sts == '1' && responseText.hide == '0' && typeof value !== "undefined" || !responseText.hide) {
                     $ (".modal form").resetForm ();
                     $ (".modal").modal ('hide');
                  }
                  alertify.success (responseText.msg).dismissOthers ();
               }
            }

            function setLoading (obj) {
               obj.html ("Loading....");
               obj.attr ("disabled", true);
            }

            function reactive (obj, txt) {
               obj.attr ("disabled", false);
               obj.html (txt);
            }



        </script>
    </head>

    <body class="bg-white">
        <?php echo $content; ?>
    </body>
</html>
