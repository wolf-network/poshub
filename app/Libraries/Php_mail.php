<?php 
	namespace App\Libraries;

	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Php_mail{
    	public function php_send_mail($mailer_data = []){
    		 // Instantiation and passing `true` enables exceptions
           $mail = new PHPMailer(true);

           try {
               //Server settings
            //    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
               $mail->SMTPDebug = FALSE;
              $mail->isSMTP();                                            // Send using SMTP
               $mail->Host       = (!empty($mailer_data['smtp_settings']['host']))?$mailer_data['smtp_settings']['host']:'';                    // Set the SMTP server to send through
               $mail->SMTPAuth   = (!empty($mailer_data['smtp_settings']['smtp_auth']))?$mailer_data['smtp_settings']['smtp_auth']:true;                                   // Enable SMTP authentication
               $mail->Username   = (!empty($mailer_data['smtp_settings']['username']))?$mailer_data['smtp_settings']['username']:'';                     // SMTP username
               $mail->Password   = (!empty($mailer_data['smtp_settings']['password']))?$mailer_data['smtp_settings']['password']:'';                               // SMTP password
               $mail->SMTPSecure = (!empty($mailer_data['smtp_settings']['smtp_secure']) && $mailer_data['smtp_settings']['smtp_secure'] == false)?'':PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
               $mail->Port = (!empty($mailer_data['smtp_settings']['port']))?$mailer_data['smtp_settings']['port']:465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

               $mail_from = (!empty($mailer_data['smtp_settings']['mail_from']))?$mailer_data['smtp_settings']['mail_from']:'';
               $mail->setFrom($mail_from);

               if(!empty($mailer_data['recipents'])){   
                    //Recipients
                    if(is_array($mailer_data['recipents'])){
                        for($i=0;$i<count($mailer_data['recipents']);$i++){
                            $mail->addAddress($mailer_data['recipents'][$i]);
                        }
                    }else{
                        $mail->addAddress($mailer_data['recipents']);
                    }
                }else{
                    echo "Please add recipents";
                    exit;
                }

                if(!empty($mailer_data['cc'])){   
                    //Recipients
                    if(is_array($mailer_data['cc'])){
                        for($i=0;$i<count($mailer_data['cc']);$i++){
                            $mail->addCC($mailer_data['cc'][$i]);
                        }
                    }else{
                        $mail->addCC($mailer_data['cc']);
                    }
                }

                if(!empty($mailer_data['bcc'])){   
                    //Recipients
                    if(is_array($mailer_data['bcc'])){
                        for($i=0;$i<count($mailer_data['bcc']);$i++){
                            $mail->addBCC($mailer_data['bcc'][$i]);
                        }
                    }else{
                        $mail->addBCC($mailer_data['bcc']);
                    }
                }

               // Content
               $mail->isHTML(true);                                  // Set email format to HTML

               if(!empty($mailer_data['string_attachment'])){
                $string_attachment = $mailer_data['string_attachment'];
                $mail->AddStringAttachment($string_attachment['attachment'], $string_attachment['filename'], $string_attachment['format'], $string_attachment['file_type']);
               }

               $mail->Subject = (!empty($mailer_data['subject']))?$mailer_data['subject']:'No Subject';
               $mail->Body    = (!empty($mailer_data['content']))?$mailer_data['content']:'No Content';
            //    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

               if($mail->send()){
                   return true;
                //   echo "Mail Sent";
               }else{
                   return false;
                // echo "Mail not Sent";
               }
               
           } catch (Exception $e) {
               echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
           }
    	}
    }
?>