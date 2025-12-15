<?php

session_start();
    
include "../../config/config.php";

$code = "";
$code_err = "";

$emailSubject = $_SESSION['email'] ?? '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["code"]))) {
        $code_err = "Please insert the code.";
    } else {
        $code = trim($_POST["code"]);
        
        if(isset($_SESSION['otp'], $_SESSION['otp_expiry']) && $code == $_SESSION['otp'] && time() < $_SESSION['otp_expiry']) {
            // OTP is correct and not expired
            echo "<div class='alert alert-success'>OTP code verification successful. You can now reset your password.</div>";

            // Unset OTP and expiry from the session after successful verification
            unset($_SESSION['otp'], $_SESSION['otp_expiry']);
            header("Location: ../Views/landing_page/new_password.php");
            // exit;
        } else {
            // Either the code is incorrect, expired, or not set
            $code_err = "The verification code is incorrect or expired.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/landing_page.css" />
  <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo.png" />
</head>

<body id="page-top" style="background-color: #eeeeee">
    <div id="layoutAuthentication" style="padding-top: 120px;">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">OTP Verification</h3>
                                    <span id="message"></span>
                                </div>
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                                    <p class="mb-2">We have sent a verification code to your email: <strong><?php echo htmlspecialchars($emailSubject); ?></strong>.
                                    <div class="col-md-12">
                                        <label for="code" class="form-label">Verification Code</label>
                                        <input type="text" id="code" name="code" class="form-control <?php echo (!empty($code_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $code; ?>" placeholder="Enter Code">
                                        <span class="invalid-feedback"><?php echo $code_err; ?></span>
                                    </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" name="submit" type="submit" style="width: 100%;">Verify Code</button>
                                        </div>
                                    </form>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>