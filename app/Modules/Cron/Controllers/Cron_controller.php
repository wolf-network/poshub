<?php

namespace Modules\Cron\Controllers;

use \App\Libraries\Php_mail;

class Cron_controller extends \CodeIgniter\Controller {
    function __construct()
	{
        $this->stock_model = new \Modules\Inventory\Models\Stock_model();
        $this->php_mail = new Php_mail();
	}

    public function stockExpiryReminderMail(){
        $expiry_reminder_date = date('Y-m-d');

        $expiring_item_reminder_contact_details = $this->stock_model->fetchExpiringItemsRemindersContactDetails($expiry_reminder_date);

        if(!empty($expiring_item_reminder_contact_details)){
            for ($i=0; $i <count($expiring_item_reminder_contact_details) ; $i++) {

                $mailing_comp_id = $expiring_item_reminder_contact_details[$i]['CompID'];

                $data = [
                    'comp_name' => $expiring_item_reminder_contact_details[$i]['CompName'],
                    'comp_email' => $expiring_item_reminder_contact_details[$i]['EmailID'],
                    'stock_details' => $expiring_item_reminder_contact_details[$i]['stocks']
                ];

                $stock_inward_log_data = [
                    'NextReminderDate' => date('Y-m-d', strtotime("+7 day",strtotime($expiry_reminder_date)))
                ];

                $this->stock_model->UpdateInwardLogNextExpiryDate($mailing_comp_id,$stock_inward_log_data,$expiry_reminder_date);

                $mailer_data = [
                    'recipents' => $expiring_item_reminder_contact_details[$i]['EmailID'],
                    'subject' => 'Urgent: Items in Your Inventory Are About to Expire.',
                    'content' => view('\Modules\Cron\Views\stock_expiry_reminder_mailer',$data)
                ];



                $this->php_mail->php_send_mail($mailer_data);
            }
        }

    }
}