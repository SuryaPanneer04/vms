<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body){
    global $con;
    
    // Fetch config from DB
    $config = $con->query("SELECT * FROM mail_settings LIMIT 1")->fetch();
    
    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'] ?? '';
        $mail->Password = $config['smtp_pass'] ?? '';
        $mail->SMTPSecure = $config['smtp_secure'] ?? 'tls';
        $mail->Port = $config['smtp_port'] ?? 587;

        $mail->setFrom($config['from_email'] ?? $config['smtp_user'], $config['from_name'] ?? 'VMS');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body . ($config['mail_footer'] ? "<br><br>" . $config['mail_footer'] : "");

        $mail->send();
        return true;
    } catch (Exception $e){
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendTemplateMail($to, $template_key, $placeholders = []){
    global $con;

    try {
        $stmt = $con->prepare("SELECT * FROM mail_templates WHERE template_key = ?");
        $stmt->execute([$template_key]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            return "Template '$template_key' not found.";
        }

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        // Handle CC if present
        $cc = $template['cc_email'] ?? '';
        
        // Handle From override
        $from_override = !empty($template['from_email']) ? $template['from_email'] : null;
        
        // Fetch config from DB for mailer
        $config = $con->query("SELECT * FROM mail_settings LIMIT 1")->fetch();
        
        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_user'] ?? '';
            $mail->Password = $config['smtp_pass'] ?? '';
            $mail->SMTPSecure = $config['smtp_secure'] ?? 'tls';
            $mail->Port = $config['smtp_port'] ?? 587;

            $sender_email = $from_override ?? ($config['from_email'] ?? $config['smtp_user']);
            $mail->setFrom($sender_email, $config['from_name'] ?? 'VMS');
            $mail->addAddress($to);
            
            if(!empty($cc)){
                $cc_list = explode(',', $cc);
                foreach($cc_list as $email){
                    $mail->addCC(trim($email));
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            
            $final_footer = !empty($template['mail_footer']) ? $template['mail_footer'] : ($config['mail_footer'] ?? '');
            $mail->Body = $body . ($final_footer ? "<br><br>" . $final_footer : "");

            $mail->send();
            return true;
        } catch (Exception $e){
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } catch (PDOException $e) {
        return "Database error: " . $e->getMessage();
    }
}
?>