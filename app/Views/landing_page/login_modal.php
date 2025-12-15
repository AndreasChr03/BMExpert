<?php

include __DIR__ . '/../../../config/config.php';

$loginError = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $sql = "SELECT email, password, role_id, user_id FROM users WHERE email = ?";
  if ($stmt = mysqli_prepare($mysqli, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fetchedemail, $hashedPassword, $role_id, $user_id);

    if (mysqli_stmt_fetch($stmt)) {
      if (password_verify($password, $hashedPassword)) {
        $_SESSION["email"] = $email;
        $_SESSION["role_id"] = $role_id;
        $_SESSION["loggedin"] = true;
        $_SESSION["user_id"]=$user_id;

        // Dynamically determine redirection based on role_id
        $redirectUrl = '';
        switch ($role_id) {
            case 1:
                $redirectUrl = BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php';
                break;
            case 2:
                $redirectUrl = BASE_URL . 'app/Views/users/dashboard_role/dashboard_owner.php';
                break;
            case 3:
                $redirectUrl = BASE_URL . 'app/Views/users/dashboard_role/dashboard_tenant.php';
                break;
            default:
                $redirectUrl = BASE_URL . 'app/Views/error404.php';
                break;
        }

        // Use JavaScript for redirection to the determined URL
        if ($redirectUrl) {
          echo "<script type='text/javascript'>window.location.href = '{$redirectUrl}';</script>";
          header("Location: {$redirectUrl}"); // Fallback in case JavaScript is disabled (optional) 
          exit(); // Ensure the script stops executing after this
        }
      } else {
        echo "<script>document.getElementById('password').classList.add('invalid');</script>";
        $loginError = "Invalid password!";
      }
    } else {
      echo "<script>document.getElementById('email').classList.add('invalid');</script>";
      $loginError = "Invalid email!";
    }

    mysqli_stmt_close($stmt);
  } else {
    $loginError = "An error occurred. Please try again later.";
    echo '<div class="error" role="alert">' . $loginError . '</div>';
  }
  mysqli_close($mysqli);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactOwnerModalLabel">Sign in</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php if (!empty($loginError)) { ?>
        <div class="alert alert-danger mx-3" role="alert" style="margin-top: 5px;">
          <?= $loginError ?>
        </div>
      <?php } ?>
      <img src="../../../public/img/full-black-logo.png" alt="Logo" class="img-fluid mx-auto d-block" style="margin-top: 10px; width: 300px; height: 170px;">
      <div class="modal-body">
        <form class="needs-validation" id="loginFormModal" method="post" novalidate>
          <!-- Form content -->
          <div class="input-group has-validation mb-3">
            <span class="input-group-text" id="inputGroupPrepend">@</span>
            <input class="form-control" id="validationCustomUsername03" aria-describedby="inputGroupPrepend" name="email" type="email" value="<?php echo $email;?>" placeholder="Email" required>
            <div class="invalid-feedback">Please enter email address.</div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" id="modalPassword" name="password" placeholder="Password" required>
            <span class="input-group-text">
              <i class="bi bi-eye-slash" onclick="toggleVisibility('modalPassword', this)" style="cursor: pointer;"></i>
            </span>
            <div class="invalid-feedback">Please enter your password.</div>
          </div>
          <div class="d-flex justify-content-between">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="rememberMeModal" name="remember">
              <label class="form-check-label" for="rememberMeModal">Remember Me</label>
            </div>
            <a href="<?php echo BASE_URL; ?>app/Views/landing_page/forgot_password.php" class="small" style="align-self: center; color: #086972;">Forgot Password?</a>
          </div>
          <button type="submit" class="btn btn-primary w-100 mt-4">Login</button>
          <div class="d-flex justify-content-center mt-2">
            <span class="me-2">Don't have an account?</span>
            <a href="<?php echo BASE_URL; ?>app/Views/landing_page/register.php" class="small" style="color: #086972;">Sign Up</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      <?php if (!empty($loginError)) : ?>
        // Assuming you are using Bootstrap's modal component
        var myModal = new bootstrap.Modal(document.getElementById('loginModal'), {
          keyboard: false
        });
        myModal.show();
      <?php endif; ?>
    });

    (function () {
        'use strict'
          var forms = document.querySelectorAll('.needs-validation')
          // Loop over them and prevent submission
          Array.prototype.slice.call(forms)
            .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
                }
                form.classList.add('was-validated')
              }, false)
            })
        })()

    const togglePassword = document.querySelector("#togglePassword");

        function toggleVisibility(inputId, icon) {
          const input = document.getElementById(inputId);
          if (input.type === "password") {
              input.type = "text";
              icon.classList.replace("bi-eye-slash", "bi-eye");
          } else {
              input.type = "password";
              icon.classList.replace("bi-eye", "bi-eye-slash");
          }
      }
  </script>
  
   <script src="<?php echo BASE_URL; ?>public/js/modal-script.js"></script>

</body>

</html>
