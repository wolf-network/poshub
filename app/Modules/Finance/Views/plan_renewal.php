<div class="row">
                    <div class="col-md-4">&nbsp;</div>
                    <div class="col-md-4">

                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-green text-center">
                                <h3>Renewal Plans</h3>
                                <p><b>Renew to Enjoy unlimited usage of Wolf CRM</b></p>
                                <p><b>Prices are in INR</b></p>
                            </div>
                            <div class="box-footer">
                                <ul class="nav nav-stacked">
                                    <li>
                                        <a href="#">
                                            Receive continous updates 
                                            <span class="pull-right text-green">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Manage Clients & Vendors
                                            <span class="pull-right text-green">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Store Unlimited contacts & leads 
                                            <span class="pull-right text-green">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Create and store Unlimited invoices 
                                            <span class="pull-right text-green">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="row">
                                    <?php for($i=0;$i<count($pricing_plans);$i++){ 

                                        $tax_amount = $pricing_plans[$i]['TotalAmount'] * $pricing_plans[$i]['TaxPercentage'] / 100;

                                        $plan_amount = $pricing_plans[$i]['TotalAmount'] + $tax_amount;
                                    ?>
                                    <div class="col-sm-6 col-xs-6 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header"> <i class="fa fa-inr"></i> <?php echo $plan_amount; ?> Per User</h5>
                                            <span class="description-text"><?php echo $pricing_plans[$i]['PlanName']; ?></span>
                                            <br><br>
                                            <a href="user-subscription/<?php echo $user_data['ID']; ?>?plan=<?php echo $pricing_plans[$i]['SubscriptionPlanID']; ?>" class="btn bg-olive btn-block buy-subscription">Buy now</a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>