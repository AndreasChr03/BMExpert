<?php include '../../../config/config.php'; ?>

<!DOCTYPE html>
<html>
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
                                    <h3 class="text-center font-weight-light my-4">Reset Password</h3>
                                    <span id="message"><?php include ("../../Models/new-password-submit.php")?></span>
                                </div>
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                                    <div class="col-md-12">
                                            <label for="inputPassword">Password</label>
                                            <div class="input-group">
                                                <input class="form-control" id="inputPassword" name="password" type="password" required>
                                                <span class="input-group-text">
                                                    <i class="bi bi-eye-slash" onclick="toggleVisibility('inputPassword', this)" style="cursor: pointer;"></i>
                                                </span>
                                                <div class="invalid-feedback">Please enter password.</div>
                                            </div>
                                          </div>
                                        <div class="col-md-12">
                                            <label for="repeatPassword">Confirm Password</label>
                                            <div class="input-group">
                                                <input class="form-control" id="repeatPassword" name="repeatpassword" type="password" required>
                                                <span class="input-group-text">
                                                    <i class="bi bi-eye-slash" onclick="toggleVisibility('repeatPassword', this)" style="cursor: pointer;"></i>
                                                </span>
                                                <div class="invalid-feedback">Please re-enter password.</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" id="reset-password-submit" name="submit" type="submit" style="width: 100%;">Change password</button>
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

    <script>
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
        const passwordInput = document.querySelector("#repeatPassword");

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

</body>

</html>