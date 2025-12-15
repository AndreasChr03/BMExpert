<?php
session_start();  // Start the session to access session variables

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';  // Adjust the path as necessary

$mail = new PHPMailer(true);

$response = [];

// Suppress error output to ensure JSON response is clean
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Verify necessary session data exists
    if (!isset($_SESSION['email'])) {
        throw new Exception('Owner email not set in session.');
    }

    // Fetching session data
    $ownerEmail = $_SESSION['email'];
    $ownerName = (isset($_SESSION['name']) ? $_SESSION['name'] : 'Owner') . ' ' . (isset($_SESSION['surname']) ? $_SESSION['surname'] : '');

    // Checking if necessary POST variables are set
    if (!isset($_POST['guestEmail'], $_POST['emailSubject'], $_POST['emailMessage'])) {
        throw new Exception('One or more required fields are missing.');
    }

    // Server settings
    $mail->SMTPDebug = 0;  // Disable debug output for production
    $mail->isSMTP();  
    $mail->Host = 'smtp.gmail.com';  
    $mail->SMTPAuth = true;  
    $mail->Username = 'bmexpert.enterprise@gmail.com'; 
    $mail->Password = 'mgns ssgh cjjv foql'; 
    $mail->SMTPSecure = 'tls';  
    $mail->Port = 587;  

    // Sender
    $mail->setFrom($ownerEmail, $ownerName);  
    $mail->addReplyTo($ownerEmail, $ownerName);  

    // Recipient
    $recipientEmail = $_POST['guestEmail'];
    $mail->addAddress($recipientEmail);  

    // Content
    $mail->isHTML(true);  
    $mail->Subject = $_POST['emailSubject'];
    $mail->Body    = '
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
            <h2>Property Request Reply</h2>
            <p><label>From:</label> ' . htmlspecialchars($ownerName) . '</p>
            <p><label>Message:</label> ' . nl2br(htmlspecialchars($_POST['emailMessage'])) . '</p>
          </div>
        </body>
        </html>';
    $mail->AltBody = 'From: ' . $ownerName . "\n" .
                     'Message: ' . $_POST['emailMessage'];

    $mail->send();
    $response['status'] = 'success';
    $response['message'] = 'Email sent successfully.';
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = "Mailer Error: {$mail->ErrorInfo}";
}

header('Content-Type: application/json');
echo json_encode($response);
?>