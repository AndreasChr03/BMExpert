<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0; // Disable debug output for production
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'bmexpert.enterprise@gmail.com'; // SMTP username
    $mail->Password = 'mgns ssgh cjjv foql'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to

    // Sender
    $mail->setFrom('noreply@yourdomain.com', 'Web Contact'); // Use an email you control
    $mail->addReplyTo($_POST['email'], $_POST['name']); // Add a reply-to address

    // Recipient
    $mail->addAddress('bmexpert.enterprise@gmail.com', 'BMExpert'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'New Message from Contact Form';
    $mail->Body = '
        <html>
        <head>
          <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .email-content { margin: 20px; padding: 10px; background-color: #f4f4f4; border-radius: 5px; }
            .email-content p { margin: 10px 0; }
            .email-content label { font-weight: bold; }
          </style>
        </head>
        <body>
          <div class="email-content">
            <h2>New Contact Form Submission</h2>
            <p><label>Name:</label> ' . htmlspecialchars($_POST['name']) . ' ' . htmlspecialchars($_POST['surname']) . '</p>
            <p><label>Email:</label> ' . htmlspecialchars($_POST['email']) . '</p>
            <p><label>Message:</label> ' . nl2br(htmlspecialchars($_POST['message'])) . '</p>
          </div>
        </body>
        </html>';

    $mail->AltBody = 'Name: ' . $_POST['name'] . ' ' . $_POST['surname'] . "\n" .
                     'Email: ' . $_POST['email'] . "\n" .
                     'Message: ' . $_POST['message'];

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>