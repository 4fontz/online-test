    			<footer class="main--footer main--footer-light">

            <p>Copyright &copy; <a href="#">DAdmin</a>. All Rights Reserved.</p>

        </footer>

    </main>

</div>

    <script src="<?php echo JS_PATH;?>perfect-scrollbar.min.js"></script>

    <script src="<?php echo JS_PATH;?>raphael.min.js"></script>

    <script src="<?php echo JS_PATH;?>morris.min.js"></script>

    <script src="<?php echo JS_PATH;?>select2.min.js"></script>
    
    <script src="<?php echo JS_PATH;?>jquery.steps.min.js"></script>

    <script src="<?php echo JS_PATH;?>jquery-jvectormap.min.js"></script>

    <script src="<?php echo JS_PATH;?>jquery-jvectormap-world-mill.min.js"></script>

    <script src="<?php echo JS_PATH;?>horizontal-timeline.min.js"></script>

    <script src="<?php echo JS_PATH;?>jquery.validate.min.js"></script>

    <script src="<?php echo JS_PATH;?>bootstrap-notify.js"></script>

    <script src="<?php echo JS_PATH;?>bootstrap-notify.min.js"></script>

    <script src="<?php echo JS_PATH;?>sweetalert.min.js"></script>

    <script src="<?php echo JS_PATH;?>sweetalert-init.js"></script>

    <script src="<?php echo JS_PATH;?>highlight.js"></script>

    <script src="<?php echo JS_PATH;?>bootstrap-switch.js"></script>

    <script type="text/javascript">

        $(document).ready(function(){

        	<?php if ($this->session->flashdata('alert_type')) { ?>

                $.notify({

                	icon: "<?php echo $this->session->flashdata('alert_icon');?>",

                	message: "<?php echo $this->session->flashdata('alert_info');?>"

                },{

                	type: "<?php echo $this->session->flashdata('alert_type');?>"

                });

            <?php }?>

        });

    </script>

</body>
<div class="se-pre-con"></div>
</html>