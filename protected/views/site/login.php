

<div class="container">
    <div class="row justify-content-md-center no-gutters" style="margin-top: 12%;">
        
        <div class="col-sm-4">
            <div class="login-box">
                
                <!-- /.login-logo -->
                <div class="login-box-body">
                    
                    <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" class="img-fluid" />
                    
                    <p class="login-box-msg">Sign in to start your session</p>

                    <form action="<?php echo Yii::app()->createUrl("site/login") ?>" method="post">
                        <div class="form-group has-feedback">
                            <input type="text" required="true" name="LoginForm[username]" class="form-control" placeholder="Username">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <?php echo $model->getError("username"); ?>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="LoginForm[password]" required="true" class="form-control" placeholder="Password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            <?php echo $model->getError("password"); ?>
                        </div>
                        <div  class="form-group has-feedback">
                            <div class="col-xs-4">
                                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                            </div>
                            <!-- /.col -->
                        </div>

                    </form>


                </div>
                <!-- /.login-box-body -->
            </div>
        </div>
    </div>
</div>