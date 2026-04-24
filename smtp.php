<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body){
    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'suryapanneer04@gmail.com'; // Replace with your email
        $mail->Password = 'mkmu yfqt tjsn fkpr'; // Replace with your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('suryapanneer04@gmail.com', 'VMS'); // Replace with your email and name
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e){
        return $mail->ErrorInfo;
    }
}
?>