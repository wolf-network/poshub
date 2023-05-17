<?php 
    
    namespace App\Libraries;

    use Razorpay\Api\Api;

    class Payment_gateways{

        public function razorPay($data){
            $key = env('razorpay_key_id');
            $secret = env('razorpay_key_secret');
            $api = new Api($key, $secret);

            $res = $api->order->create(
                array(
                    'receipt' => $data['receipt'], 
                    'amount' => $data['amount'] * 100, 
                    'currency' => 'INR', 
                    'notes'=> array(
                        'plan_name'=> '30 Days Plan'
                    )
                )
            );

            $order_id = $res->id;
            $payee_name = $data['payee_name'];
            $payee_email = (!empty($data['payee_email']))?$data['payee_email']:'';
            $payee_contact = (!empty($data['payee_contact']))?$data['payee_contact']:'';
            $callback_url = $data['callback_url'].'&order_id='.$order_id;
            $http_referer = (!empty($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:base_url('dashboard');
            $cancel_url = (!empty($data['cancel_url']))?$data['cancel_url']:$http_referer;
            $logo = base_url('assets/img/wolf-symbol-mini.jpg');

            // print_r($res->id);

            echo <<<EOD
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script>
                var options = {
                    "key": "$key", // Enter the Key ID generated from the Dashboard
                    "currency": "INR",
                    "name": "Wolf Network",
                    "image": "$logo",
                    "order_id": "$order_id", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                    "callback_url": "$callback_url",
                    "prefill": {
                        "name": "$payee_name",
                        "email": "$payee_email",
                        "contact": "$payee_contact"
                    },
                    "theme": {
                        "color": "#3c3c3c"
                    },
                    "modal":  { 
                        "escape": false, 
                        "ondismiss": function(){
                            window.location.href = "$cancel_url";
                        } 
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
                
                </script>
            EOD;
        }

        public function fetchRazorpayOrderDetails($order_id){
            $key = env('razorpay_key_id');
            $secret = env('razorpay_key_secret');
            $api = new Api($key, $secret);

            try {
                $order_details = $api->order->fetch($order_id);
                $order_details->amount_paid = $order_details->amount_paid / 100;
                $order_details->amount = $order_details->amount / 100;
                return $order_details;
            } catch (Exception $e) {
                return false;
            }
        }

    }
?>