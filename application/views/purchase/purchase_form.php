<section class="page--header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="page--title h5">Purchase</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URI_ROOT.'site/purchase';?>">Purchase</a></li>
                    <li class="breadcrumb-item active"><span><?php echo $key;?></span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="main--content">
    <div class="row gutter-20">
    	<div class="col-md-12">
    	<?php if(validation_errors()!=NULL){?>
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Error List - <span class="text-lightergray">You must clear below error(s) before further proceedings</span></h3>
                </div>
                <div class="panel-content">
                    <?php echo validation_errors(); ?>
                </div>
            </div>
        <?php }?>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $key;?> Purchase</h3>
            </div>
            <div class="panel-content">
            	<?php echo form_open('site/purchase_create',array('id'=>'formWizard','class'=>'form--wizard','method'=>'post','enctype'=>'multipart/form-data')); ?>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Name *</span>
                        <div class="col-md-9">
                            <input type="text" name="purchase_name" id="purchase_name" placeholder="Name" class="form-control" autocomplete="off" required value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_name:'';?>">
                        	<?php if(isset($unique_error)){?>
                            	<h6 class="page--title"><?php echo $unique_error;?></h6>
                            <?php }?>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Unit *</span>
                        <div class="col-md-9">
                            <select id="purchase_unit" name="purchase_unit" class="form-control">
                                <option value="">Select Unit</option>
                                    <option value="Kg" <?php echo (@$existing_purchase->purchase_unit=="Kg")?'selected':''; ?>>Kilo Gram</option>
                                    <option value="Nos" <?php echo (@$existing_purchase->purchase_unit=="Nos")?'selected':''; ?>>Numbers</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Quantity *</span>
                        <div class="col-md-9">
                            <input type="number" min="1" name="purchase_quantity" id="purchase_quantity" placeholder="Quantity" class="form-control" autocomplete="off" required value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_quantity:'1';?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Net Purchase Rate *</span>
                        <div class="col-md-9">
                            <input type="number" min="1" name="purchase_net_purchase_rate" id="purchase_net_purchase_rate" placeholder="Net Purchase Rate" class="form-control" autocomplete="off" required value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_net_purchase_rate:'0';?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Unit Price *</span>
                        <div class="col-md-9">
                            <input type="number" min="1" name="purchase_per_kg_piece" id="purchase_per_kg_piece" readonly placeholder="Unit Price" class="form-control" autocomplete="off" required value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_per_kg_piece:'';?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Markup (%) </span>
                        <div class="col-md-9">
                            <input type="number" name="purchase_markup" id="purchase_markup" placeholder="Markup" class="form-control" autocomplete="off" value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_markup:'0';?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right">Sales Price *</span>
                        <div class="col-md-9">
                            <input type="number" min="1" name="purchase_sales_price" readonly id="purchase_sales_price" placeholder="Markup" class="form-control" autocomplete="off" required value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_sales_price:'';?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <span class="label-text col-md-3 col-form-label text-md-right"></span>
                        <div class="col-md-9">
                        	<input type="hidden" name="purchase_id" id="purchase_id" value="<?php echo (isset($existing_purchase))?$existing_purchase->purchase_id:'0';?>">
                            <input type="submit" class="btn btn-success form_submit_btn" value="Submit">
                        	<input type="reset" class="btn btn-default" value="Reset">
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
     </div>
  </div> 
</section>