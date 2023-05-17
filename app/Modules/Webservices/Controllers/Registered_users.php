<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Registered_users extends ResourceController {
    function __construct()
	{
		
        $this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data)){
            return $this->respond([
                'status' => 401,
                'msg' => 'Invalid User!',
                'data' => [],
            ]);
        }

        $this->form_validation = \Config\Services::validation();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->user_model = new \Modules\Layouts\Models\User_model();
	}

    public function get_registered_users(){
        
        $offset = $this->request->getGet('iDisplayStart');
        $filter = [];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){
            $filter['search_txt'] = $this->request->getGet('sSearch');
            $limit = $this->request->getGet('iDisplayLength');
        }else{
            $limit = 10;
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'RU.Name';
                break;
            case '2':
                $sort_by = 'RU.Gender';
                break;
            case '3':
                $sort_by = 'RU.EmailID';
                break;
            case '4':
                $sort_by = 'RUAM.SubscriptionEndDate';
                break;
            case '5':
                $sort_by = 'RU.Status';
                break;
            
            default:
                // code...
                break;
        }


        $app = env('app');
        $app_id = getenv('app_id');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->registered_user_model->fetchUserList($this->user_data['CompID'],$app_id,$subscription_end_date,0,0,$filter,true);
        $users_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->registered_user_model->fetchUserList($this->user_data['CompID'],$app_id,$subscription_end_date,$limit,$offset,$filter,false,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($users_list, 200);
    }

    public function save_reminder(){

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding reminders.',
                'error' => []
            ],403);
        }

        $this->form_validation->setRule('Task', 'Task', 'required');

        if(!empty($_POST['ReminderDate']) && date('YmdHi', strtotime($_POST['ReminderDate'])) <= date('YmdHi')){
            $this->form_validation->setRule('ReminderDate', 'Reminder Date', 'valid_email',['valid_email' => 'Reminder Date should be greater than today.']);
        }else{
            $this->form_validation->setRule('ReminderDate', 'Reminder Date', 'required');
        }

        if (!$this->form_validation->withRequest($this->request)->run()){
            return $this->respond([
                'status' => false,
                'msg' => 'Validation error',
                'error' => json_decode(json_encode($this->form_validation->getErrors()),true)
            ],501);
        }else{
            $reminder_data = [
                'Task' => $this->request->getPost('Task'),
                'ReminderDate' => $this->request->getPost('ReminderDate'),
                'AddedBy' => $this->user_data['ID'],
                'AddedDate' => date('Y-m-d H:i:s')
            ];

            $reminder_id = $this->registered_user_model->saveReminder($reminder_data);

            return $this->respond([
                'status' => true,
                'msg' => 'Reminder saved successfully!',
                'data' => [
                    'ReminderID' => $reminder_id
                ]
            ],200);
        }
    }

    public function get_reminders(){
        return $this->respond([
            'status' => true,
            'msg' => 'Following are the reminders!',
            'data' => $this->registered_user_model->fetchReminders($this->user_data['ID'])
        ],200);
    }

    public function get_all_reminders(){
        
        $offset = $this->request->getGet('iDisplayStart');
        $subscription_time_left = subscription_time_left();

        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){
            $filter['search_txt'] = $this->request->getGet('sSearch');
            $limit = 10;
        }else{
            $limit = $this->request->getGet('iDisplayLength');
        }

        $total_records = $this->registered_user_model->fetchAllReminders($this->user_data['ID'],0,0,$filter,true);
        $reminders_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->registered_user_model->fetchAllReminders($this->user_data['ID'],$limit,$offset,$filter)
        ];
        
        return $this->respond($reminders_list, 200);
    }

    public function update_notification_token(){
        $registered_user_id = $this->user_data['ID'];

        $this->form_validation->setRule('DeviceID', 'Device ID', 'required');
        $this->form_validation->setRule('Token', 'Token', 'required');

        if (!$this->form_validation->withRequest($this->request)->run()){
            return $this->respond([
                'status' => false,
                'msg' => 'Validation error',
                'error' => validation_errors()
            ],501);
        }else{
            $existing_device_id = $this->request->getPost('DeviceID');
            $existing_token = (!empty($this->user_data['push_token']))?$this->user_data['push_token']:'';
            $token = $this->request->getPost('Token');

            if($token != $existing_token){
                $registered_user_token_data = [
                    'RegisteredUserID' => $this->user_data['ID'],
                    'DeviceID' => $this->request->getPost('DeviceID'),
                    'Token' => $this->request->getPost('Token'),
                ];

                $this->registered_user_model->saveRegisteredUserNotificationToken($registered_user_token_data);
                
                $user_session_data = $this->user_data;
                $user_session_data['push_token'] = $registered_user_token_data['Token'];

                $this->session->set('user_data',$user_session_data);
                return $this->respond([
                    'status' => true,
                    'msg' => 'Token Updated Successfully!',
                    'data' => []
                ],200);
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'Token already exists!',
                    'error' => ''
                ],501);
            }
        }
    }

    public function get_referral_details(){
        $comp_id = $this->user_data['CompID'];
        $offset = $this->request->getGet('iDisplayStart');
        $limit = $this->request->getGet('iDisplayLength');

        $user_details = $this->registered_user_model->fetchUserBasicDetails($this->user_data['ID'], $comp_id);

        $total_records = $this->registered_user_model->fetchReferralDetails($this->user_data['ID'],$user_details['CommissionPercentage'],0,0,true);
        $referral_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->registered_user_model->fetchReferralDetails($this->user_data['ID'],$limit,$offset)
        ];
        
        return $this->respond($referral_list, 200);        
    }
}