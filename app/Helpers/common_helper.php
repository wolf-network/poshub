<?php 
    use sngrl\PhpFirebaseCloudMessaging\Client;
    use sngrl\PhpFirebaseCloudMessaging\Message;
    use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
    use sngrl\PhpFirebaseCloudMessaging\Notification;

    function substrwords($text, $maxchar, $end='...') {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);      
            $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } 
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } 
        else {
            $output = $text;
        }
        return $output;
    }

    function common_pagination($base_url,$total_rows,$per_page,$custom_template = array()){
        $CI =& get_instance();

        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;

        if (empty($custom_template)) {
            /* Pagination Templating Starts */
            $config['full_tag_open'] = "<ul class='pagination pagination-sm no-margin pull-right'>";
            $config['full_tag_close'] ="</ul>";
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
            $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
            $config['next_tag_open'] = "<li>";
            $config['next_tagl_close'] = "</li>";
            $config['prev_tag_open'] = "<li>";
            $config['prev_tagl_close'] = "</li>";
            $config['first_tag_open'] = "<li>";
            $config['first_tagl_close'] = "</li>";
            $config['last_tag_open'] = "<li>";
            $config['last_tagl_close'] = "</li>";
            /* Pagination Templating Ends */
        }
        else{
            $config = $custom_template;
        }

        $CI->pagination->initialize($config);

        return $CI->pagination->create_links();
    }

    function sortBy($field, &$array, $direction = 'asc')
    {
        if(!empty($array)){
            usort($array, create_function('$a, $b, $direction = "asc"', '
                $a = $a["' . $field . '"];
                $b = $b["' . $field . '"];
    
                if ($a == $b) return 0;
    
                $direction = strtolower(trim($direction));
    
                return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
            '));
    
            return true;   
        }
        else{
            return false;
        }
    }

    
    function media_server($link = ''){
        return getenv('media_server').$link;
    }

    function upload_file($bucket = '',$files = []){
        $app_key = getenv('media_server_app_key');
        $app_secret = getenv('media_server_app_secret');

        if(empty($app_key)){
            $data = [
                'status' => false,
                'msg' => 'Please provide API Key.'
            ];

            return $data;
        }

        if(empty($bucket)){
            $data = [
                'status' => false,
                'msg' => 'Please Provide a bucket to upload file.'
            ];

            return $data;
        }

        if(empty($files)){
            $data = [
                'status' => false,
                'msg' => 'Please Provide files to upload.'
            ];

            return $data;
        }

        $post_fields = [
            'app_key' => $app_key,
            'app_secret' => $app_secret,
            'bucket' => $bucket
        ];

        if(is_array($files['name'])){
            for($i=0;$i<count($files['name']);$i++){
                $post_fields['files['.$i.']'] = new CURLFILE($files['tmp_name'][$i],$files['type'][$i],$files['name'][$i]);
            }
        }else{
            $post_fields['files'] = new CURLFILE($files['tmp_name'],$files['type'],$files['name']);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => media_server('media'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $post_fields
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    function change_date_format($date,$required_format = 'Y-m-d'){
        if(!empty($date)){
            return date($required_format, strtotime(str_replace('/','-',$date)));
        }
    }

    function curl_request($url, $post_fields = [])
    {
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        if(!empty($post_fields)){
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post_fields);   
        }
        
        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }

    function subscription_time_left($subscription_end_date = ''){
        if(empty($subscription_end_date)){
            if(empty(session()->get('user_data')['ID'])){
                header('location:'.base_url().'login');
            }
            $app = env('app');
            $app_details = session()->get('user_data')['apps'][$app];
            $subscription_end_date = date_create($app_details['SubscriptionEndDate']);
        }else{
            $subscription_end_date = $subscription_end_date;
        }

        $current_date = date_create(date('Y-m-d H:i:s'));
        $subscription_time_diff = date_diff($current_date,$subscription_end_date);
        $subscription_time_left = [
            'years' => $subscription_time_diff->format('%r%Y'),
            'months' => $subscription_time_diff->format('%r%m'),
            'days' => $subscription_time_diff->format('%r%d'),
            'hours' => $subscription_time_diff->format('%r%h'),
            'minutes' => $subscription_time_diff->format('%r%i'),
            'seconds' => $subscription_time_diff->format('%r%s'),
        ];

        return $subscription_time_left;
    }

    function numberToWords($number){
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => 'zero', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
         $divider = ($i == 2) ? 10 : 100;
         $number = floor($no % $divider);
         $no = floor($no / $divider);
         $i += ($divider == 10) ? 1 : 2;
         if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
         } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?" point " . $words[$point / 10] . " " . $words[$point = $point % 10] : '';
        return $result . $points;
    }
    
    function google_recaptcha(){
        // Storing google recaptcha response
        // in $recaptcha variable
        $recaptcha = $_POST['g-recaptcha-response'];
        $secret_key = RECAPTCHA_SECRET_KEY;
        
        // Hitting request to the URL, Google will
        // respond with success or error scenario
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='. $secret_key . '&response=' . $recaptcha;
        
        // Making request to verify captcha
        $response = file_get_contents($url);
        
        // Response return by google is in
        // JSON format, so we have to parse
        // that json
        $response = json_decode($response);
        return $response;
    }

     function sendNotification($receivers_token,$notification_data){
        $url ="https://fcm.googleapis.com/fcm/send";
        
        $title = $notification_data['title'];
        $body = $notification_data['body'];
        $click_action = $notification_data['redirect_url'];

        $fields=array(
            "notification"=>array(
                "title"=> $title,
                "body"=> $body,
                "icon"=> base_url('assets/img/wolf-symbol-mini.jpg'),
                "click_action"=> $click_action
            )
        );
        
        if(!is_array($receivers_token)){
            $fields['to'] = $receivers_token;   
        }else{
            $fields['registration_ids'] = $receivers_token;
        }
    
        $headers=array(
            'Authorization: key='.FIREBASE_KEY,
            'Content-Type:application/json'
        );
    
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $result=curl_exec($ch);
        // print_r($result);
        curl_close($ch);
    }

    function default_view($view, $data=array())
    {
        $session = \Config\Services::session();
        $app = env('app');
        $app_id = getenv('app_id');
        $user_data = $session->get('user_data');

        if(empty($user_data)){
            redirect()->to(base_url('login'));
        }
        else{
            $user_model = new \Modules\Layouts\Models\User_model();

            $filter = [
                'NotificationReadDate' => 'null'
            ];
            $subscription_time_left = subscription_time_left();
            $global_data = [
                'view' => $view,
                'user_data' => $user_data,
                'plan_name' => $user_data['apps'][$app]['PlanName'],
                'subscription_time_left' => $subscription_time_left,
                'app_id' => $app_id,
                'apps' => $user_model->fetchAllApps()
            ];


            if( ($subscription_time_left['days'] <= 15 && $subscription_time_left['seconds'] >= 0) || $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0 ){
                if(session()->get('user_data')['Privilege'] == 'Admin'){
                    $finance_model = new \Modules\Finance\Models\Finance_model();
                    $global_data['subscription_plans'] = $finance_model->fetchSubscriptionPlans($app_id);
                }
            }
            
            $data = array_merge($data,$global_data);

            return view('\Modules\Layouts\Views\default_layout',$data);
        }
    }

?>