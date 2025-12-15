<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../../vendor/autoload.php';

// Generate OTP
$otp = rand(100000, 999999);

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bmexpert.enterprise@gmail.com';   // SMTP username
    $mail->Password   = 'mgns ssgh cjjv foql';   // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587; // TCP port to connect to

    // Sender info
    $mail->setFrom('bmexpert.enterprise@gmail.com', 'BMExpert');
    $mail->addAddress($senderEmail);
    $mail->isHTML(true);
    $mail->Subject = '[BMExpert] Reset Password OTP';
    $mail->Body = "<div style='margin: 0; margin-top: 70px; padding: 92px 30px 115px; border-radius: 30px; text-align: center; font-family: 'Questrial', sans-serif;'>
                   <div style='width: 100%; max-width: 489px; margin: 0 auto;'>
                   <h1 style=' margin: 0; font-size: 24px; font-weight: 500; color: #1f1f1f;'>Your OTP</h1>
                   <p style=' margin: 0; margin-top: 17px; font-size: 16px; font-weight: 500;'>Hello there,</p>
                   <p style=' margin: 0; margin-top: 17px; font-weight: 500; letter-spacing: 0.56px;'>
                    Thank you for choosing BMExpert. Use the following OTP to complete the procedure to change your password. OTP is valid for
                   <span style='font-weight: 600; color: #1f1f1f;'>5 minutes</span>. Do not share this code with others.</p>
                   <p style=' margin: 0; margin-top: 60px; font-size: 40px; font-weight: 600; letter-spacing: 25px; color: #086972;'>$otp</p>
                   </div>
                   </div>";
    $mail->send();
    echo 'Message has been sent';

    // Store OTP and expiry in session if mail was successfully sent
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes
    $emailSent = true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $emailSent = false;
    }

?>