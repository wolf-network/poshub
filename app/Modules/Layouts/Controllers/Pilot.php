<?php 

namespace Modules\Layouts\Controllers;

use Config\Services;
use App\Libraries\Php_mail;

class Pilot extends \CodeIgniter\Controller{

	function __construct()
	{
		$this->session = \Config\Services::session();
		$this->form_validation = \Config\Services::validation();
		$this->user_model = new \Modules\Layouts\Models\User_model();
        $this->company_model = new \Modules\Company\Models\Company_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->php_mail = new Php_mail();
	}

	public function index(){
		$app_id = getenv('app_id');
		$finance_model = new \Modules\Finance\Models\Finance_model();

		$data = [
			'subscription_plans' => $finance_model->fetchSubscriptionPlans($app_id)
		];

		return view('Modules\Layouts\Views\landing_page', $data);
	}

	public function login(){
        if($this->session->get('user_data')){
            return redirect()->to(base_url('dashboard'));
        }

        
        if(!empty($_POST)){
	        $this->form_validation->setRule('CompName', 'Organization Name', 'required|validateCompany');
	        $this->form_validation->setRule('Username', 'Username', 'required');
	        $this->form_validation->setRule('Password', 'Password', 'required');
	        
	        if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$comp_name = $this->request->getPost('CompName'); 

	            $company_details = $this->user_model->fetchCompDetailsViaName($comp_name);

	            $username = $this->request->getPost('Username');
	            $password = md5($this->request->getPost('Password'));
	            $app_id = getenv('app_id');
	            $device_id = $this->request->getPost('DeviceID');

	            $user_data = $this->user_model->fetchUserData($username,$password, $company_details['CompID'],$device_id);
	            
	            if(!empty($user_data)){
	                $this->session->set('user_data',$user_data);
	                return redirect()->to(base_url('dashboard'));
	            }else{
	                $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Credentials provided by you do not match, Please try again.']);
	            }
	            
	        }
        }

        
        return view('Modules\Layouts\Views\login');
    }

    public function register(){
         if($this->session->get('user_data')){
            return redirect()->to(base_url('dashboard'));
        }

        $firm_type_id = $this->request->getPost('FirmTypeID');
        
        if(!empty($_POST)){

            $this->form_validation->setRule('CompName', 'Organization Name', 'required|validateDuplicateCompany');
            $this->form_validation->setRule('FirmTypeID', 'Firm Type', 'required');
            $this->form_validation->setRule('Name', 'Full Name', 'required');
            $this->form_validation->setRule('EmailID', 'Email ID', 'required|valid_email');
            $this->form_validation->setRule('Password', 'Password', 'required');
            $this->form_validation->setRule('Password', 'Password', 'required');
            $this->form_validation->setRule('ConfirmPassword', 'Confirm Password', 'required|matches[Password]');
            
            $captcha_response = google_recaptcha();
            
            if (empty($captcha_response->success)) {
                $this->form_validation->setRule('g-recaptcha', 'g-recaptcha', 'required');
                $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Error in Google reCAPTACHA']);
            }
            
            if ($this->form_validation->withRequest($this->request)->run())
            {
                $app = env('app');
                $app_id = getenv('app_id');
                $comp_data = [
                    'CompName' => $this->request->getPost('CompName'),
                    'EmailID' => strtolower($this->request->getPost('EmailID')),
                    'FirmTypeID' => $this->request->getPost('FirmTypeID'),
                    'CompToken' => md5(date('Ymdhis')),
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                $comp_id = $this->company_model->saveCompDetails($comp_data);

                $user_data = [
                    'Name' => $this->request->getPost('Name'),
                    'ReferralCode' => bin2hex(random_bytes(5)),
                    'CommissionPercentage' => '10',
                    'CompID' => $comp_id,
                    'EmailID' => strtolower($this->request->getPost('EmailID')),
                    'Password' => md5($this->request->getPost('Password')),
                    'PrivilegeID' => 2, /* Make this dynamic in future if required */
                    'InsertedDate' => date('Y-m-d H:i:s')
                ];

                if($this->request->getPost('ReferralCode')){
                   $fetched_registered_user_id = $this->user_model->fetchUserIDViaRefferalCode($this->request->getPost('ReferralCode'));
                   $user_data['ReferredBy'] = $fetched_registered_user_id;
                }

                $registered_user_id = $this->registered_user_model->saveUser($user_data);

                $updated_comp_data = [
                    'AddedBy' => $registered_user_id
                ];

                $this->company_model->saveCompDetails($updated_comp_data,$comp_id);

                $finance_model = new \Modules\Finance\Models\Finance_model();

                $trial_plan_details = $finance_model->fetchPlanDetailsViaPlanName($app_id,'Trial');
                $subscription_end_date = date('Y-m-d H:i:s', strtotime('+'.$trial_plan_details['Duration'].' '.$trial_plan_details['DurationType']));

                $registered_user_app_mapper = [
                    'AppID' => $app_id, /* Needs to be made dynamic in future */
                    'RegisteredUserID' => $registered_user_id,
                    'SubscriptionPlanID' => $trial_plan_details['SubscriptionPlanID'],
                    'SubscribedDate' => date('Y-m-d H:i:s'),
                    'SubscriptionStartDate' => date('Y-m-d H:i:s'),
                    'SubscriptionEndDate' => $subscription_end_date
                ];

                $this->registered_user_model->saveUserApps($registered_user_app_mapper, false);

                $user_subscription_log_data = [
                    'RegisteredUserID' => $registered_user_id,
                    'CompID' => $comp_id,
                    'App' => $app, /* Needs to be made dynamic in future */
                    'SubscriptionPlanID' => $trial_plan_details['SubscriptionPlanID'],
                    'SubscribedDate' => date('Y-m-d H:i:s'),
                    'SubscriptionStartDate' => date('Y-m-d H:i:s'),
                    'SubscriptionEndDate' => $subscription_end_date,
                    'AmountPaid' => 0,
                    'AmountPaidBy' => $registered_user_id
                ];

                $this->registered_user_model->saveUserSubscriptionLogs($user_subscription_log_data);

                $content_data = [
                    'name' => $this->request->getPost('Name')
                ];

                $mailer_data = [
                    'recipents' => $user_data['EmailID'],
                    'subject' => 'Getting started with Wolf CRM.',
                    'content' => view('\Modules\Layouts\Views\registration_mail',$content_data)
                ];
                $this->php_mail->php_send_mail($mailer_data);

                $user_data = $this->user_model->fetchUserData($user_data['EmailID'],$user_data['Password'], $comp_id,$app_id);

                $this->session->set('user_data',$user_data);
                return redirect()->to(base_url('dashboard'));
            }
        }


        $data = [
            'firm_types' => $this->user_model->fetchFirmTypes()
        ];

        return view('\Modules\Layouts\Views\register',$data);
    }

    public function logout(){
        $this->session->remove('user_data');
        $this->session->destroy();

        return redirect()->to(base_url('login'));
    }

    public function forgotPassword(){

        if(!empty($_POST)){
            $this->form_validation->setRule('CompName', 'Organization Name', 'required|validateCompany');
            $this->form_validation->setRule('Username', 'Username', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $comp_name = $this->request->getPost('CompName'); 

                $company_details = $this->user_model->fetchCompDetailsViaName($comp_name);
                $username = $this->request->getPost('Username');

                $user_data = $this->user_model->fetchUserDataViaUserName($username,$company_details['CompID']);
                
                if(empty($user_data)){
                    $this->session->setFlashdata('flashmsg',['status' => false, 'msg' => 'Username not found in company database']);
                }else{
                    $password = rand(11111,99999);
                    $user_save_data = [
                        'Password' => md5($password)
                    ];
                    $this->registered_user_model->saveUser($user_save_data,$user_data['ID']);

                    $mailer_data = [
                        'recipents' => $user_data['EmailID'],
                        'subject' => 'Wolf CRM New Password.',
                        'content' => '<b>'.$password.'</b> is your new password, You may change it after logging in.'
                    ];
                    $this->php_mail->php_send_mail($mailer_data);

                    $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'New Password has been sent on your registered email ID.']);
                    redirect()->to(base_url('login'));
                }
            }
        }

        return view('Modules\Layouts\Views\forgot_password');
    }

    public function thankYou(){
        return view('\Modules\Layouts\Views\thank_you');
    }
}

?>