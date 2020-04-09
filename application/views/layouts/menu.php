<?php define('CONTROLLER_NAME',$this->uri->segment(1));?>
<?php define('METHOD_NAME',$this->uri->segment(2));?>
<?php define('PARAM_NAME',$this->uri->segment(4));?>
<aside class="sidebar" data-trigger="scrollbar">
    <div class="sidebar--profile">
        <div class="profile--img">
            <a href="profile.html">
                <img src="<?php echo IMAGE_PATH;?>unknown.png" alt="" class="rounded-circle">
            </a>
        </div>
        <div class="profile--name">
            <a href="<?php echo URI_ROOT."site/profile/" ?>" class="btn-link"><?php echo $this->session->userdata('name');?></a>
        </div>
        <div class="profile--nav">
            <ul class="nav">
                <li class="nav-item">
                    <a href="<?php echo URI_ROOT."site/logout/" ?>" class="nav-link" title="Logout">
                        <i class="fa fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar--nav">
        <ul>
            <li>
                <ul>
                    <li class="active">
                        <a href="<?php echo URI_ROOT.'site/purchase/'; ?>">
                            <i class="fa fa-user"></i>
                            <span>Purchase</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
<main class="main--container">