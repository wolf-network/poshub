<!-- Save Item Modal -->
<div id="saveItemModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
  	<form action="javascript:void(0)" id="itemForm" class="item-form">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add Item</h4>
	      </div>
	      <div class="modal-body">
	        <div class="row">
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="Item">Item <span class="text-red">*</span> </label>
	        			<input type="text" name="Item" value="" id="Item-field" class="form-control">
	        		</div>
	        	</div>
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="BuyingPrice">Buying Price Per Unit</label>
	        			<input type="text" name="BuyingPrice" value="" id="BuyingPrice-field" class="form-control">
	        		</div>
	        	</div>
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="Price">Selling Price Per Unit<span class="text-red">*</span></label>
	        			<input type="text" name="Price" value="" id="Price-field" class="form-control">
	        		</div>
	        	</div>
	        </div>
	        <div class="row">
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="">Type <span class="text-red">*</span></label> <br>
	        			<input type="radio" name="ItemType" id="Good" value="Good"> Good
	        			<input type="radio" name="ItemType" id="Service" value="Service"> Service <br>
	        			<span id="ItemType-field"></span>
	        		</div>
	        	</div>
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="HSN">HSN <span class="text-red">*</span></label>
	        			<input type="text" name="HSN" value="" id="HSN-field" class="form-control">
	        		</div>
	        	</div>
	        	<div class="col-md-4">
	        		<div class="form-group">
	        			<label for="">Barcode No</label>
                        <input type="text" name="BarcodeNo" value="" id="BarcodeNo-field" class="form-control">
	        		</div>
	        	</div>
	        </div>
	        <div class="row">
	        	<div class="col-md-5">
	                <div class="panel panel-default">
	                    <div class="panel-heading">
	                        <h4 class="pull-left">Taxes (On Selling)</h4>
	                        <button type="button" class="btn bg-olive pull-right add-item-tax">Add</button>
	                        <div class="clearfix"></div>
	                    </div>
	                    <div class="panel-body">
	                        <div class="row">
	                            <div class="col-md-5 col-xs-4">
	                                <div class="form-group">
	                                    <label for="">Tax Name <span class="text-red">*</span></label>
	                                    <input type="text" name="Tax[]" value="" class="form-control tax-field" placeholder="E.g: GST/VAT">
	                                    
	                                </div>
	                            </div>
	                            <div class="col-md-5 col-xs-6">
	                                <label for="">Tax Rate (%) <span class="text-red">*</span></label>
	                                <div class="form-group">
	                                    <div class="input-group">
	                                        <input type="text" name="TaxPercentage[]" value="" class="form-control tax-percentage-field">
	                                        <span class="input-group-addon">%</span>
	                                    </div>  
	                                </div>
	                            </div>
	                            <div class="col-md-2 col-xs-1">
	                                <div class="form-group remove-item-tax-btn-container">
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-success save-item"> Save </button>
	      </div>
	    </div>
	</form>
  </div>
</div>