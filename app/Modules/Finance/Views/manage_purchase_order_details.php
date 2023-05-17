<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				
				<div class="row">
					<div class="col-md-3">
						<h3 class="box-title pull-left">PO Details</h3>		
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="">Change PO Status</label>
							<input type="hidden" value="<?php echo $purchase_order_id; ?>" id="PurchaseOrderID">
							<?php $disabled_purchase_order_status = (in_array($purchase_order_data['PurchaseOrderStatus'],['Received','Canceled']))?'disabled':''; ?>
							<select name="PurchaseOrderStatusID" id="ChangePurchaseOrderStatus" class="form-control" <?php echo $disabled_purchase_order_status; ?>>
								<option value="">Select Status</option>
								<?php for($i=0;$i<count($purchase_order_status);$i++){ ?>
								<option value="<?php echo $purchase_order_status[$i]['PurchaseOrderStatusID']; ?>" <?php echo($purchase_order_status[$i]['PurchaseOrderStatusID'] == $purchase_order_data['PurchaseOrderStatusID'])?'selected':''; ?> ><?php echo $purchase_order_status[$i]['PurchaseOrderStatus']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="pull-right">
							<a href="<?php echo base_url('download-purchase-order/'.$purchase_order_id); ?>" class="btn btn-warning" data-loader="false" >Download Purchase Order</a>
							<a href="<?php echo base_url('manage-purchase-orders'); ?>" class="btn btn-success" >Manage Purchase Orders</a>
						</div>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
	                <div class="col-md-6 col-xs-6">
	                    <h5><strong><?php echo $purchase_order_data['CompanyName'].' '.$purchase_order_data['CompFirmType']; ?></strong></h5>
	                    <p><?php echo $purchase_order_data['CompanyAddress']; ?></p>
	                    <p><?php echo $purchase_order_data['CompanyContactNumber']; ?></p>
	                </div>

	                <div class="col-md-6 col-xs-6">
	                    <div class="pull-right">
	                        <h5><strong>Purchase Order</strong></h5>
	                        <div class="clearfix"></div>
	                        <p>
	                            <b>PO No:</b> <?php echo $purchase_order_data['PurchaseOrderNo']; ?> <br>
	                            <?php if(!empty($purchase_order_data['CompanyServiceTaxType'])){ ?>
	                            <b><?php echo $purchase_order_data['CompanyServiceTaxType']; ?>:</b> <?php echo $purchase_order_data['CompanyServiceTaxIdentificationNumber']; ?>
	                        	<?php } ?><br>
	                            <b>Date:</b> <?php echo $purchase_order_data['DeliveryDate']; ?>
	                        </p>
	                    </div>
	                </div>
	            </div>
	            <hr>
	            <div class="row bg-comp-primary pd-t-1">
                    <div class="col-md-6 col-xs-6">
                    	<p><b>Vendor:</b></p>
                        <p>
                            <?php echo $purchase_order_data['VendorName'].' '.$purchase_order_data['FirmType']; ?>,
                            <br>
                            <?php echo $purchase_order_data['VendorBillingAddress']; ?>
                            <br>
                            <?php echo $purchase_order_data['VendorContactNo']; ?>
                        </p>
                    </div>
                    <?php if(!empty($purchase_order_data['ShippingAddress'])){ ?>
                    <div class="col-md-6 col-xs-6">
                        <div class="pull-right">
                        	<p><b>Ship To:</b></p>
	                        <p>
	                            <?php echo $purchase_order_data['CompanyName']; ?>,
	                            <br>
	                            <?php echo $purchase_order_data['ShippingAddress']; ?>,
	                            <br>
	                            <?php echo $purchase_order_data['CompanyContactNumber']; ?>
	                        </p>
                        </div>
                    </div>
                	<?php } ?>
                </div>
                <div class="row">
                	<div class="col-md-12">
                		<table class="table table-bordered table-striped text-center table-responsive">
                			<tr>
                				<th>Delivery Date</th>
                				<th>Shipping Terms</th>
                				<th>Payment Terms</th>
                			</tr>
                			<tr>
	                			<td><?php echo $purchase_order_data['DeliveryDate']; ?></td>
	                			<td><?php echo $purchase_order_data['ShippingTermsAndConditions']; ?></td>
	                			<td><?php echo $purchase_order_data['PaymentTerms']; ?></td>
                			</tr>
                		</table>
                	</div>
                </div>

				<div class="row">
                	<div class="col-md-12">
                		<table class="table table-bordered table-striped text-center table-responsive">
                			<tr>
                				<th>Sr</th>
                				<th>Particular</th>
                				<th>HSN</th>
                				<th>Price Per Unit</th>
                				<th>Qty</th>
                				<th>Total</th>
                			</tr>
                			<?php for ($i=0; $i <count($purchase_order_details) ; $i++) { ?>
            				<tr>
	                			<td><?php echo $i+1; ?></td>
	                			<td><?php echo $purchase_order_details[$i]['Particular']; ?></td>
	                			<td><?php echo $purchase_order_details[$i]['HSN']; ?></td>
	                			<td><?php echo $purchase_order_details[$i]['PricePerUnit']; ?></td>
	                			<td><?php echo $purchase_order_details[$i]['Quantity']; ?></td>
	                			<td><?php echo round($purchase_order_details[$i]['PricePerUnit']*$purchase_order_details[$i]['Quantity'], 2); ?></td>
                			</tr>
                			<?php } ?>
                			<tr>
                				<td colspan="5"><span class="pull-right">Subtotal</span></td>
                				<td><b><?php echo $purchase_order_data['TotalAmount']; ?></b></td>
                			</tr>
                		</table>
                	</div>
                </div>
                <?php if(!empty($purchase_order_data['CancelationRemark'])){ ?>
                <div class="row">
                	<div class="col-md-4">
                		<label for="CancelationRemark" class="text-danger">Cancelation Remark</label> <br>
                		<?php echo $purchase_order_data['CancelationRemark']; ?>
                	</div>
                </div>
                <?php } ?>               
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="purchaseOrderStatusModal" class="modal fade">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cancelation remark</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
      		<div class="col-md-12">
      			<div class="form-group">
      				<label for="CancelationRemark">Cancelation Remark <span class="text-danger">*</span></label>
      				<textarea name="CancelationRemark" id="CancelationRemark" cols="30" rows="5" class="form-control"></textarea>
      			</div>
      		</div>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" id="save-cancelation-remark" class="btn btn-danger">Save</button>
      </div>
    </div>

  </div>
</div>