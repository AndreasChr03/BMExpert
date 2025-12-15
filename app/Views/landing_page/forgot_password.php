<?php
include '../../../config/config.php';
$email = "";
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BMExpert</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/landing_page.css" />
  <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo.png" />
</head>

<body id="page-top" style="background-color: #eeeeee">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container" style="padding-top: 120px;">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Password Recovery</h3>
                                    <span id="message"><?php include ("../../Models/forgot-prepare.php")?></span>
                                </div>
                                <div class="card-body">
                                    <div id="instructions" class="small mb-3 text-muted">We will send an email to you with instructions
                                        on how to reset your password.
                                    </div>
                                    <form class="row g-3 needs-validation" method="post" novalidate>
                                    <div class="col-md-12">
                                        <label for="validationCustomUsername" class="form-label">Email</label>
                                        <div class="input-group has-validation">
                                          <span class="input-group-text" id="inputGroupPrepend">@</span>
                                          <input class="form-control" id="validationCustomUsername03" aria-describedby="inputGroupPrepend" name="email" type="email" value="<?php echo $email;?>" required>
                                          <div class="invalid-feedback">Please enter email address.</div>
                                        </div>
                                    </div>
                                    <div id="btnContainer">
                                        <button name="submit" type="submit" class="btn btn-primary" style="width: 100%;">Send Link</button>
                                    </div>
                                        <div class="d-flex justify-content-center my-3">
                                            <span class="me-2">Return to</span>
                                            <a href="../../../index.php" class="small" style="color: #086972;">Login</a>
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

    <!-- Bootstrap's Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
    </script>

</body>

</html>