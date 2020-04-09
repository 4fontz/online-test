<section class="page--header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="page--title h5">Purchase</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URI_ROOT;?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span>Purchase</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="main--content">
    <div class="panel">
        <div class="records--header">
            <div class="title fa-wrench">
                <h3 class="h3">Purchase List </h3>
                <p>Found Total <?php echo count($PurchaseList);?> Purchase</p>
            </div>
            <div class="actions">
                <a href="<?php echo URI_ROOT."site/purchase_create/"?>" class="btn btn-success">Add New</a>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="records--list" data-title="Purchase Listing">
            <table id="recordsListView">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Markup</th>
                        <th>Unit Price</th>
                        <th>Sales price</th>
                        <th>Created Date</th>
                        <th class="not-sortable">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($PurchaseList)>0){
                        $i=1;foreach($PurchaseList as $purchase){?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $purchase->purchase_name;?></td>
                                <td><?php echo $purchase->purchase_quantity.' '.$purchase->purchase_unit;?></td>
                                <td><?php echo number_format($purchase->purchase_net_purchase_rate,2);?></td>
                                <td><?php echo $purchase->purchase_markup;?>%</td>
                                <td><?php echo number_format($purchase->purchase_per_kg_piece,2);?></td>
                                <td><?php echo number_format($purchase->purchase_sales_price,2);?></td>
                                <td><?php echo date("Y-M-d", strtotime($purchase->purchase_created_at));  ?></td>
                                <td>
                                    <div class="dropleft">
                                        <a href="#" class="btn-link" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <a href="<?php echo URI_ROOT.'site/purchase_edit/'.$purchase->purchase_id; ?>" class="dropdown-item">Edit</a>
                                            <a href="<?php echo URI_ROOT.'site/purchase_view/'.$purchase->purchase_id; ?>" class="dropdown-item">View</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++;}
                    }?>
                </tbody>
			</table>
        </div>
    </div>
</section>