<!DOCTYPE html>
<html dir="ltr" lang="en" class="no-outlines">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Online Test - <?php echo isset($title)?$title:'Custom Page'?></title>
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700%7CMontserrat:400,500">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>fontawesome-all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>jquery-ui.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>perfect-scrollbar.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>select2.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>ion.rangeSlider.skinFlat.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>datatables.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>datepicker.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>style.css?v=1.1">
    <!-- For some special cases JS filed included in header -->   
    <script src="<?php echo JS_PATH;?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo JS_PATH;?>jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo JS_PATH;?>bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="<?php echo JS_PATH;?>datatables.min.js"></script>
    <script src="<?php echo JS_PATH;?>main.js?v=1.2" type="text/javascript"></script>
    <script type="text/javascript">
        var base_url = '<?php echo URI_ROOT; ?>';
        var FCPATH = '<?php echo FCPATH; ?>';
    </script>
</head>
<body>
    <div class="wrapper">
    	<header class="navbar navbar-fixed">
            <div class="navbar--header">
                <a href="<?php echo URI_ROOT; ?>" class="logo">
                    <h2>Online Test</h2>
                </a>
                <a href="#" class="navbar--btn" data-toggle="sidebar" title="Toggle Sidebar">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
            <a href="#" class="navbar--btn" data-toggle="sidebar" title="Toggle Sidebar">
                <i class="fa fa-bars"></i>
            </a>
            <div class="navbar--nav ml-auto">
                <ul class="nav">
                    <li class="nav-item dropdown nav--user online">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <img src="<?php echo IMAGE_PATH;?>unknown.png" alt="" class="rounded-circle">
                            <span><?php echo $this->session->userdata('name');?></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="<?php echo URI_ROOT."site/logout/" ?>"><i class="fa fa-power-off"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </header>