<div class="row">
    <div class="col-md-2">&nbsp;</div>
    <div class="col-md-4">
        <div class="box box-widget widget-user-2">
            <div class="widget-user-header bg-green text-center">
                <h3>Pricing Plans</h3>
                <p><b>Enjoy unlimited usage of Wolf Network CRM</b></p>
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
                    <?php for($i=0;$i<count($subscription_plans);$i++){ ?>
                    <div class="col-sm-6 col-xs-6 border-right">
                        <div class="description-block">
                            <?php 
                                $tax_amount = $subscription_plans[$i]['TotalAmount'] * $subscription_plans[$i]['TaxPercentage'] / 100;

                                $plan_amount = $subscription_plans[$i]['TotalAmount'] + $tax_amount;
                            ?>
                            <h5 class="description-header"> <i class="fa fa-inr"></i> <?php echo $plan_amount; ?> Per User</h5>
                            <span class="description-text">
                                <?php echo $subscription_plans[$i]['PlanName']; ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-widget widget-user-2">
            <div class="widget-user-header bg-green text-center">
                <h3>Need Custom Solution?</h3>
                <p><b>Contact Us Via</b></p>
                <p>&nbsp;</p>
            </div>
            <div class="box-footer">
                <ul class="nav nav-stacked">
                    <li>
                        <a href="mailto:info@wolfnetwork.in" data-loader="false">
                            info@wolfnetwork.in
                            <span class="pull-right text-green">
                                <i class="fa fa-send" aria-hidden="true"></i>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="tel:+91 9137166653" data-loader="false">
                            +91 9137166653
                            <span class="pull-right text-green">
                                <i class="fa fa-mobile" aria-hidden="true"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>