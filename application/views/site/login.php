<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-outlines">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Online Test - Login</title>
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="icon" href="" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700%7CMontserrat:400,500">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>fontawesome-all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>jquery-ui.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>style.css">
</head>
<script type="text/javascript">
    var base_url = '<?php echo URI_ROOT; ?>';
</script>
<body>
    <div class="wrapper">
        <div class="m-account-w bg--img">
            <div class="m-account">
                <div class="row no-gutters">
                    <div class="col-md-6">
                        <div class="m-account--content-w">
                            <div class="m-account--content">
                                <!--<h2 class="h2">Want's to explore?</h2>
                                <p>Dive to our dashing packages</p>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="m-account--form-w">
                            <div class="m-account--form">
                                
                                <?php
                                    $attributes = array(
                                        'id' => 'login_form',
                                        'role' => 'form',
                                        'method' => 'POST'
                                    );
                                    echo form_open('site/login', $attributes);
                                    ?>
                                    <label class="m-account--title">Login to your account</label>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <i class="fas fa-user"></i>
                                            </div>
											<font style="color:red;"><?php echo $this->session->flashdata('error'); ?></font>
                                            <input type="text" name="username" placeholder="Username" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <input type="password" name="password" placeholder="Password" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="m-account--actions">
                                        <input type="submit" name="login" class="btn btn-rounded btn-info" value="Login">
                                    </div>
                                    <div class="m-account--footer">
                                        <p>&copy; 2018 <?php echo "Online Test";?></p>
                                    </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo JS_PATH;?>jquery.min.js"></script>
    <script src="<?php echo JS_PATH;?>jquery-ui.min.js"></script>
    <script src="<?php echo JS_PATH;?>bootstrap.bundle.min.js"></script>
    <script src="<?php echo JS_PATH;?>bootstrap-notify.js"></script>
    <script src="<?php echo JS_PATH;?>bootstrap-notify.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
        	<?php if ($this->session->flashdata('alert_type')) { ?>
                $.notify({
                	icon: '<?php echo $this->session->flashdata('alert_icon');?>',
                	message: '<?php echo $this->session->flashdata('alert_info');?>'
                },{
                	type: '<?php echo $this->session->flashdata('alert_type');?>'
                });
            <?php }?>
        });
    </script>
</body>
</html>
