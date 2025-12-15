<?php

session_start();

include "../../../config/config.php";

if (isset($_POST['submit'])) {
    $password = isset($_POST['password']) ? trim(htmlspecialchars($_POST['password'])) : null;
    $repeatpassword = isset($_POST['repeatpassword']) ? trim(htmlspecialchars($_POST['repeatpassword'])) : null;

    $errors = array();

    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $repeatpassword) {
        array_push($errors, "Passwords do not match");
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        $sql = "UPDATE users SET password = ? WHERE email = ?";

        if($stmt = mysqli_prepare($mysqli, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $passwordHash, $email);

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
            
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                echo "<div class='alert alert-success'>You have successfully reset your password. You will be redirected to the home page in 3 seconds.</div>";
                echo "<script>setTimeout(function() {window.location.href = '../../../index.php'; }, 3000);</script>";
                // header("Refresh:3; url=../view/login.php");
            } else{
                echo "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
            }

            mysqli_stmt_close($stmt);
        }
    }
}

?>