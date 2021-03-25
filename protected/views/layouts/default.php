<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

<?php require 'navigation.php'; ?>

<div class="container-fluid" style="margin-top: 56px;">
    <div class="row">
        <div class="col">
            <?php echo $content; ?>
        </div>
    </div>
</div>

<footer style="position: fixed; width: 100%; z-index: 2; background: #e3e3e3; bottom: 0px; left: 0px; font-size: 11px; border-top: 1px solid #eeeeee; padding: 8px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                Developed By PIT
            </div>
        </div>
    </div>
</footer>

<?php $this->endContent(); ?>