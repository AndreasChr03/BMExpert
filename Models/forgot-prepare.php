<?php

session_start();

include "../../../config/config.php";

$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $senderEmail = trim($_POST['email']);
    $email = $senderEmail; // Assign submitted email to $email

    // Prepare a select statement to look up the email
    $sql = "SELECT email FROM users WHERE email = ?";
        
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $senderEmail;
            
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
                
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Email exists, redirect to OTP sending and handling
                $_SESSION['email'] = $senderEmail;
                include "otp-send.php";
                    
                if (isset($emailSent) && $emailSent === true) {
                    // Success, redirect to your OTP verification page or show a success message
                    header("Location: ../../Models/otp-verify.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>Could not send OTP code.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>There is not an account with this email.</div>";
            }
        } else {
            echo "Something went wrong.";
        }
            
    // Close statement
    mysqli_stmt_close($stmt);
    
    }
}

?>