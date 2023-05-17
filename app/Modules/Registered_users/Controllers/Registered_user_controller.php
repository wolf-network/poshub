<?php

namespace Modules\Registered_users\Controllers;

use \Config\Services;
use App\Libraries\Php_spreadsheets;

class Registered_user_controller extends \CodeIgniter\Controller {
    function __construct()
	{
        $this->session = \Config\Services::session();
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
            exit();
        }

        $this->user_model = new \Modules\Layouts\Models\User_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->finance_model = new \Modules\Finance\Models\Finance_model();

        $this->form_validation = \Config\Services::validation();
	}
    
    public function dashboard(){
        $app_id = getenv('app_id');
        $app = env('app');

        if(empty($this->userdata['apps'][$app])){
            $trial_plan_details = $this->finance_model->fetchPlanDetailsViaPlanName($app_id,'Trial');
            $subscription_end_date = date('Y-m-d H:i:s', strtotime('+'.$trial_plan_details['Duration'].' '.$trial_plan_details['DurationType']));

            $registered_user_app_mapper_data = [
                'AppID' => $app_id, /* Needs to be made dynamic in future */
                'RegisteredUserID' => $this->userdata['ID'],
                'SubscriptionPlanID' => $trial_plan_details['SubscriptionPlanID'],
                'SubscribedDate' => date('Y-m-d H:i:s'),
                'SubscriptionStartDate' => date('Y-m-d H:i:s'),
                'SubscriptionEndDate' => $subscription_end_date
            ];

            $this->registered_user_model->saveUserApps($registered_user_app_mapper_data, false);

            $this->userdata['apps'][$app] = [
                'AppID' => $app_id,
                'App' => $app,
                'UserURL' => base_url(),
                'PlanName' => 'Trial',
                'SubscriptionEndDate' => $subscription_end_date
            ];

            $this->session->set('user_data', $this->userdata);
        }
        
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->userdata['CompID'];

        $stock_model = new \Modules\Inventory\Models\Stock_model();
        $item_model = new \Modules\Inventory\Models\Item_model();
        $data = [
            'basic_statistics' => $this->registered_user_model->fetchBasicStatisticsCount($comp_id),
            'items_count' => $item_model->fetchItemList($comp_id,$subscription_end_date,0,0,[],true),
            'revenue' => $this->finance_model->fetchRevenue($comp_id,date('Y-m-d')),
            'inward_outward_reports' => $stock_model->fetchInwardOutwardReports($this->userdata['CompID'],$subscription_end_date,5,0,['ReportDateTo' => date('Y-m-d')]),
            'mom_growth' => $this->finance_model->fetchMOMSalesGrowth($comp_id,date('Y')),
            'category_wise_sales' => $this->finance_model->CategoryWiseSalesReport($comp_id,date('Y')),
            'add_bel_global_js' => [base_url('assets/js/finance.js')]
        ];

        if($this->userdata['Privilege'] == 'Admin'){
            $data['registered_users'] = $this->registered_user_model->fetchAllUsers($comp_id,$app_id);
        }

        return default_view('\Modules\Registered_users\Views\dashboard',$data);
    }

    public function saveUser($registered_user_id = 0){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start Adding/Editing users.']);
            $this->response->redirect(base_url('plan-renewal'));
        }
        
        if(!empty($_POST)){
            $this->form_validation->setRule('Name', 'Full Name', 'required');
            $this->form_validation->setRule('Gender', 'Gender', 'required|in_list[Male,Female,Transgender]');
            $this->form_validation->setRule('RoleID.*', 'Role', 'required');
            $this->form_validation->setRule('EmailID', 'Email ID', 'required|valid_email|max_length[254]|duplicateUser['.$registered_user_id.']');
            $this->form_validation->setRule('PrivilegeID', 'Privilege', 'required');


            if ($this->form_validation->withRequest($this->request)->run())
            {
                $app = env('app');
                $app_id = getenv('app_id');

                $registered_user_data = [
                    'Name' => $this->request->getPost('Name'),
                    'ReferralCode' => bin2hex(random_bytes(5)),
                    'CompID' => $comp_id,
                    'Gender' => $this->request->getPost('Gender'),
                    'EmailID' => $this->request->getPost('EmailID'),
                    'PrivilegeID' => $this->request->getPost('PrivilegeID')
                ];

                if($registered_user_id == 0){
                    $registered_user_data['InsertedBy'] = $this->userdata['ID'];
                    $registered_user_data['InsertedDate'] = date('Y-m-d H:i:s');
                }else{
                    $registered_user_data['UpdatedBy'] = $this->userdata['ID'];
                    $registered_user_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $registered_user_id = $this->registered_user_model->saveUser($registered_user_data,$registered_user_id);

                if(!empty($registered_user_data['InsertedDate'])){
                    $trial_plan_details = $this->finance_model->fetchPlanDetailsViaPlanName($app_id,'Trial');
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
                        'CompID' => $this->userdata['CompID'],
                        'App' => $app, /* Needs to be made dynamic in future */
                        'SubscriptionPlanID' => $trial_plan_details['SubscriptionPlanID'],
                        'SubscribedDate' => date('Y-m-d H:i:s'),
                        'SubscriptionStartDate' => date('Y-m-d H:i:s'),
                        'SubscriptionEndDate' => $subscription_end_date,
                        'AmountPaid' => 0,
                        'AmountPaidBy' => $this->userdata['ID']
                    ];

                    $this->registered_user_model->saveUserSubscriptionLogs($user_subscription_log_data);
                }

                for($i=0;$i<count($_POST['RoleID']);$i++){
                    $employee_roles_mapper_data[] = [
                        'RegisteredUserID' => $registered_user_id,
                        'RoleID' => $_POST['RoleID'][$i]
                    ];
                }

                $this->registered_user_model->saveUserRoles($employee_roles_mapper_data,$registered_user_id);
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Saved employee data successfully!']);
                $this->response->redirect(base_url('manage-users'));
            }
        }
        
        if(!empty($registered_user_id)){
            $registered_user_details = $this->registered_user_model->fetchUserDetails($registered_user_id,$this->userdata['CompID']);

            if(empty($registered_user_details)){
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit;
            }

            if(empty($_POST['RoleID'])){
                $_POST['RoleID'] = (!empty($registered_user_details['RoleID']))?explode(',',$registered_user_details['RoleID']):[];
            }

            foreach($registered_user_details as $registered_user_details_key => $registered_user_details_value){
                if(empty($_POST[$registered_user_details_key])){
                    $_POST[$registered_user_details_key] = $registered_user_details_value;
                }
            }
        }

        $data = [
            'registered_user_id' => $registered_user_id,
            'roles' => $this->user_model->fetchRoles($comp_id),
            'privileges' => $this->user_model->fetchPrivileges(),
            'add_bel_global_js' => base_url('assets/js/registered_user.js')
        ];
        
        return default_view('\Modules\Registered_users\Views\save_user',$data);
    }

    public function manageUsers(){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('NA', 'NA', 'permit_empty');
            if(!empty($_FILES['UserExcel']['name'])){
                if($_FILES['UserExcel']['size'] == 0){
                    $this->form_validation->setRule('UserExcel', 'User Excel', 'required',['required' => 'Invalid file with 0 bytes.']);
                }
                
                if($_FILES['UserExcel']['size'] > 2097152){
                    $this->form_validation->setRule('UserExcel', 'User Excel', 'required',['required' => 'File size should be maximum of 2MB.']);
                }
            }else{
               $this->form_validation->setRule('UserExcel', 'User Excel', 'required'); 
            }

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $file_validation = $this->validate([
                    'UserExcel' => [
                        'uploaded[UserExcel]',
                        'max_size[UserExcel,2048]',
                        'ext_in[UserExcel,xlsx,xls,csv]',
                    ],
                ]);

                if (!$file_validation) {
                    $error = $this->validator->getErrors()['UserExcel'];
                    $this->session->setFlashdata('flashmsg',['status' => false, 'msg' => $error]);
                    $this->response->redirect(base_url('manage-users'));
                }else
                {
                    $file = $this->request->getFile('UserExcel');
                    $encrypted_file_name = $file->getRandomName();
                    $upload_path = 'assets/uploads/excel/';
                    $file->move($upload_path,$encrypted_file_name);

                    $user_excel_path = $upload_path.$encrypted_file_name;
                    $this->importUsers($user_excel_path);
                }
            }
        }

            
        $app_id = getenv('app_id');

        $data = [
            'add_bel_global_js' => base_url('assets/js/registered_user.js'),
            'subscription_plans' => $this->finance_model->fetchSubscriptionPlans($app_id)
        ];

        return default_view('\Modules\Registered_users\Views\manage_users',$data);
    }

    public function deleteRegisteredUser($registered_user_id){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $registered_user_details = $this->registered_user_model->fetchUserDetails($registered_user_id, $this->userdata['CompID']);

        if(empty($registered_user_details)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit;
        }else{

            if($this->userdata['InsertedBy'] == $registered_user_details['ID']){
                $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'You are not authorized to delete this user.']);
            }else{
                $registered_user_data = [
                    'Deleted' => 1,
                    'DeletedBy' => $registered_user_id,
                    'DeletedDate' => date('Y-m-d H:i:s')
                ];

                $this->registered_user_model->saveUser($registered_user_data,$registered_user_id);
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Deleted employee data successfully!']);                
            }

            $this->response->redirect(base_url('manage-users'));
        }
    }

    public function readNotification($user_notification_id){
        $notification_details = $this->registered_user_model->fetchEmployeeNotificationDetails($this->userdata['ID'],$user_notification_id);

        if(!empty($notification_details)){
            if(empty($notification_details['NotificationReadDate'])){
                $notification_data = [
                    'NotificationReadDate' => date('Y-m-d H:i:s')
                ];
                $this->registered_user_model->saveUserNotification($notification_data,$user_notification_id);
            }
            $this->response->redirect(base_url($notification_details['RedirectURL']));
        }else{
            echo "Either the notification does not exist or does not belong to you!";
        }
    }

    public function viewAllNotifications(){
        return default_view('\Modules\Registered_users\Views\view_all_notifications');
    }

    public function changeUserStatus($registered_user_id){
        
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $registered_user_details = $this->registered_user_model->fetchUserDetails($registered_user_id, $this->userdata['CompID']);

        if(empty($registered_user_details)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit;
        }else{

            if($this->userdata['InsertedBy'] == $registered_user_details['ID']){
                $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'You are not authorized to change the status of this user.']);
            }else{
                $this->registered_user_model->UpdateUserStatus($registered_user_id);
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'User status updated successfully!']);                
            }

            $this->response->redirect(base_url('manage-users'));
        }
    }

    public function resetPassword(){

        if(!empty($_POST)){
            $this->form_validation->setRule('CurrentPassword', 'Current Password', 'required|validateCurrentPassword');
            $this->form_validation->setRule('Password', 'New Password', 'required');
            $this->form_validation->setRule('ConfirmPassword', 'Confirm Password', 'required|matches[Password]');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $registered_user_data = [
                    'Password' => md5($this->request->getPost('Password'))
                ];

                $this->registered_user_model->saveUser($registered_user_data,$this->userdata['ID']);
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Password changed successfully!']);
                $this->response->redirect(base_url('reset-password'));
            }
        }

        $data = [
            'add_bel_global_js' => base_url('assets/js/registered_user.js')
        ];

        return default_view('\Modules\Registered_users\Views\reset_password', $data);       
    }

    public function manageReminders(){
        return default_view('\Modules\Registered_users\Views\manage_reminders');
    }

    public function importUsers($user_excel_path){
        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start importing users.']);
            return $this->response->redirect(base_url('plan-renewal'));
        }

        $php_spreadsheets = new Php_spreadsheets();
        $user_import_data = $php_spreadsheets->import_excel($user_excel_path);
        $worksheet_name = $user_import_data['worksheet_name'][0];
        $worksheet_header = $user_import_data['headers'][$worksheet_name]['og'];
        $worksheet_data = $user_import_data['data'][$worksheet_name];
        $error = '';
        $required_headers = ['Full Name','Gender','Email ID'];
        for($i=0;$i<count($required_headers);$i++){
            if(!in_array($required_headers[$i], $worksheet_header)){
                $error .= ' '.$required_headers[$i].',';
            }
        }

        $error_arr = [];

        if(!empty($error)){
            $excel_header_arr = [
                'title' => 'Following headers are missing in excel:',
                'msg' => rtrim($error,',')
            ];

            array_push($error_arr, $excel_header_arr);
        }

        if(empty($worksheet_data)){
            $excel_data_arr = [
                'title' => 'Data Error:',
                'msg' => 'your excel has no data'
            ];

            array_push($error_arr, $excel_data_arr);
        }

        if(!empty($error_arr)){
            unlink($user_excel_path);
            
            $error_msg = 'No users were imported, Kindly fix the following problems.';
            $this->session->setFlashdata('excel_err',['status' => false,'msg' => $error_msg,'data' => $error_arr]);
            $this->response->redirect(base_url('manage-users'));
        }else{
            $imported_users_count = 0;
            for($i=0;$i<count($worksheet_data);$i++){
                $line_no = $i+2;
                

                if(!empty($worksheet_data[$i]['FullName']) && !empty($worksheet_data[$i]['EmailID'])){

                    $existing_user = $this->registered_user_model->duplicateUser($worksheet_data[$i]['EmailID'],0,$comp_id);

                    if(empty($existing_user)){
                        $app = env('app');
                        $app_id = getenv('app_id');

                        $privilege = (!empty($worksheet_data[$i]['Permission']))?$worksheet_data[$i]['Permission']:'User';
                        $imported_users_count += 1;

                        $user_data = [
                            'Name' => $worksheet_data[$i]['FullName'],
                            'ReferralCode' => bin2hex(random_bytes(5)),
                            'CompID' => $comp_id,
                            'EmailID' => ($worksheet_data[$i]['EmailID'])?$worksheet_data[$i]['EmailID']:null,
                            'Gender' => ($worksheet_data[$i]['Gender'])?$worksheet_data[$i]['Gender']:null,
                            'PrivilegeID' => $this->user_model->fetchPrivilegeIDViaPrivelege($privilege)['PrivilegeID'],
                            'Status' => 'Active',
                            'InsertedBy' => $this->userdata['ID'],
                            'InsertedDate' => date('Y-m-d H:i:s')
                        ];

                        $registered_user_id = $this->registered_user_model->saveUser($user_data);

                        $trial_plan_details = $this->finance_model->fetchPlanDetailsViaPlanName($app_id,'Trial');
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
                            'CompID' => $this->userdata['CompID'],
                            'App' => $app, /* Needs to be made dynamic in future */
                            'SubscriptionPlanID' => $trial_plan_details['SubscriptionPlanID'],
                            'SubscribedDate' => date('Y-m-d H:i:s'),
                            'SubscriptionStartDate' => date('Y-m-d H:i:s'),
                            'SubscriptionEndDate' => $subscription_end_date,
                            'AmountPaid' => 0,
                            'AmountPaidBy' => $this->userdata['ID']
                        ];

                        $this->registered_user_model->saveUserSubscriptionLogs($user_subscription_log_data);

                        if(!empty($worksheet_data[$i]['Roles'])){
                            $received_roles_arr = explode(',',$worksheet_data[$i]['Roles']);

                            for($i=0;$i<count($received_roles_arr);$i++){
                                $role_id = $this->registered_user_model->fetchRoleIDViaRole($received_roles_arr[$i]);
                                if(!empty($role_id['RoleID'])){
                                    $roles_arr[] = $role_id['RoleID'];
                                }else{
                                    $not_found_roles[] = $received_roles_arr[$i];
                                }
                            }

                            if(!empty($not_found_roles)){
                                $imploded_roles = implode(',', $not_found_roles);
                                $plural_roles_text = (count($not_found_roles) > 1)?'roles':'role';

                                if(count($not_found_roles) > 1){
                                    $roles_err_msg = 'User roles named '.$imploded_roles.' on line no '.$line_no.' of your excel were not found in the database.';
                                }else{
                                    $roles_err_msg = 'User role named '.$imploded_roles.' on line no '.$line_no.' of your excel was not found in the database.';
                                }

                                $roles_err_arr = [
                                    'title' => 'Unfound roles',
                                    'msg' => $roles_err_msg
                                ];

                                array_push($error_arr, $roles_err_arr);
                            }

                            if(!empty($roles_arr)){
                                for($i=0;$i<count($roles_arr);$i++){
                                    $registered_user_roles_mapper_data[] = [
                                        'RegisteredUserID' => $registered_user_id,
                                        'RoleID' => $roles_arr[$i]
                                    ];
                                }

                                $this->registered_user_model->saveUserRoles($registered_user_roles_mapper_data,$registered_user_id);
                            }
                        }

                    }else{
                        $user_import_err_arr = [
                            'title' => 'Import Error',
                            'msg' => 'Email ID mentioned on line no '.$line_no.' of your excel already exists in the database.'
                        ];

                        array_push($error_arr, $user_import_err_arr);
                    }

                    
                }else{

                    $user_import_err_msg = '';

                    if(empty($worksheet_data[$i]['FullName'])){
                        $user_import_err_msg = 'Full Name is missing on line no'.$line_no;
                    } else if(empty($worksheet_data[$i]['EmailID'])){
                        $user_import_err_msg = 'Email ID is missing on line no'.$line_no;
                    } else if($user_import_err_msg){
                        $user_import_err_msg = 'Full Name and Email ID is missing on line no'.$line_no;
                    }else{}

                    $user_import_err_arr = [
                        'title' => 'Import Error:',
                        'msg' => $user_import_err_msg
                    ];

                    array_push($error_arr, $user_import_err_arr);
                }

            }

            unlink($user_excel_path);

            if(empty($error_arr)){
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Users Imported Successfully!']);
            }else{
                $error_msg = ($imported_users_count > 0)?$imported_users_count.' Users Imported Successfully but following problems need to be fixed!':'No users were imported, Kindly fix the following problems.';
                $this->session->setFlashdata('excel_err',['status' => false,'msg' => $error_msg,'data' => $error_arr]);
            }
            
            $this->response->redirect(base_url('manage-users'));
        }
    }

    public function viewReferralDetails(){
        $comp_id = $this->userdata['CompID'];
        $user_details = $this->registered_user_model->fetchUserBasicDetails($this->userdata['ID'], $comp_id);

        $data = [
            'user_bank_details' => $this->registered_user_model->fetchUserBankAccountDetails($this->userdata['ID']),
            'user_earnings' => $this->registered_user_model->fetchReferralEarnings($this->userdata['ID'],$user_details['CommissionPercentage'])
        ];

        return default_view('\Modules\Registered_users\Views\view_referral_details', $data);
    }

    public function saveRegisteredUserBankDetails(){

        $user_existing_bank_details = $this->registered_user_model->UserExistingBankAccountDetails($this->userdata['ID']);

        if(!empty($user_existing_bank_details)){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'You have already provided your back account details. Contact us if you wish to change your bank details.']);

            $this->response->redirect(base_url('view-referral-details'));
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('AccountNumber', 'Account Number', 'required');
            $this->form_validation->setRule('BankID', 'Bank Name', 'required');
            $this->form_validation->setRule('BankDetailsID', 'IFSC Code', 'required');
            $this->form_validation->setRule('AccountType', 'Account Type', 'required');
            $this->form_validation->setRule('AccountHoldersName', 'Account Holders Name', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $registered_user_bank_account_details = [
                    'RegisteredUserID' => $this->userdata['ID'],
                    'AccountNumber' => $this->request->getPost('AccountNumber'),
                    'BankID' => $this->request->getPost('BankID'),
                    'BankDetailsID' => $this->request->getPost('BankDetailsID'),
                    'AccountType' => $this->request->getPost('AccountType'),
                    'AccountHolderName' => $this->request->getPost('AccountHoldersName'),
                ];

                $this->registered_user_model->saveRegisteredUserBankDetails($registered_user_bank_account_details);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Your bank account details have been saved successfully!']);

                $this->response->redirect(base_url('view-referral-details'));
            }
        }


        $data = [
            'bank_details' => $this->user_model->fetchBanks()
        ];
        return default_view('\Modules\Registered_users\Views\save_registered_user_bank_details',$data);
    }
}