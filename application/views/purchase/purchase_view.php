<section class="page--header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="page--title h5">Purchase</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URI_ROOT;?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URI_ROOT.'site/Purchase';?>">Purchase</a></li>
                    <li class="breadcrumb-item active"><span><?php echo "View";?></span></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="main--content">
    <div class="panel">
        <div class="records--header">
            <div class="title fa-wrench">
                <h3 class="h3">Purchase View </h3>
            </div>
        </div>
    </div>
                
    <div class="panel">
    <div class="records--body">
        <div class="title">
            <h6 class="h6">Purchase - <span class="text-lightergray"> <?php echo $existing_purchase->purchase_name;?></span></h6>
        </div>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#tab01" data-toggle="tab" class="nav-link active">Overview</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab01">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="subtitle">Purchase Information</h4>
                        <table class="table table-simple">
                            <tbody>
                                <tr>
                                    <td>Name:</td>
                                    <th><?php echo $existing_purchase->purchase_name;?></th>
                                </tr>
                                <tr>
                                    <td>Unit:</td>
                                    <th><?php echo $existing_purchase->purchase_unit;?></th>
                                </tr>
                                <tr>
                                    <td>Quantity:</td>
                                    <th><?php echo $existing_purchase->purchase_quantity;?></th>
                                </tr>
                                <tr>
                                    <td>Purchase Rate:</td>
                                    <th><?php echo number_format($existing_purchase->purchase_net_purchase_rate,2);?></th>
                                </tr>
                                <tr>
                                    <td>Unit Price:</td>
                                    <th><?php echo number_format($existing_purchase->purchase_per_kg_piece,2);?></th>
                                </tr>
                                <tr>
                                    <td>Markup:</td>
                                    <?php $percentage_amount = ($existing_purchase->purchase_per_kg_piece/100)*$existing_purchase->purchase_markup;?>
                                    <th><?php echo number_format($percentage_amount,2);?> (<?php echo $existing_purchase->purchase_markup.'% of '.$existing_purchase->purchase_per_kg_piece?> )</th>
                                </tr>
                                <tr>
                                    <td>Sales Price:</td>
                                    <th><?php echo number_format($existing_purchase->purchase_sales_price,2);?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4 class="subtitle">Other Information</h4>
                        <table class="table table-simple">
                            <tbody>
                            	<tr>
                                    <td>Created At :</td>
                                    <th><?php echo $existing_purchase->purchase_created_at;?></th>
                                </tr>
                                <?php if($existing_purchase->purchase_updated_at!=NULL){?>
                                    <tr>
                                        <td>Updated At :</td>
                                        <th><?php echo $existing_purchase->purchase_updated_at;?></th>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>