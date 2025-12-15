<?php
   session_start();
   include '../../Models/fetchUserInfo.php';

   // Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BMExpert</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Tab Icon -->
    <link rel="icon" href="../../../public/img/logo.png">
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../public/css/landing_page.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>
<style>
    .card-body {
        padding: 20px; /* Add padding to the container for overall internal spacing */
    }

    .form-group {
        margin-bottom: 15px; /* Add more spacing between form groups */
    }

    .gutters {
        margin-right: -15px; /* Adjust right margin for alignment */
        margin-left: -15px; /* Adjust left margin for alignment */
    }

    .col-12, .col-sm-6, .col-md-6, .col-lg-6, .col-xl-6 {
        padding-right: 15px; /* Add padding to the right of each column */
        padding-left: 15px; /* Add padding to the left of each column */
    }

    .card-body .text-right {
        display: flex;
        justify-content: flex-end; /* Aligns the button to the right */
        padding: 10px 0; /* Adds padding top and bottom for spacing around the button */
    }
    
    .btn-primary {
        margin-top: 20px; /* Adds top margin to space button away from form elements */
    }

    /* Additional styling for responsiveness */
    @media (max-width: 576px) {
        .card-body .text-right {
            justify-content: center; /* Center the button on small screens */
        }
    }
</style>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>


    <?php include 'header.php'; ?>

<div class="container" style="padding-bottom: 40px; padding-top: 40px;">
    <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="account-settings">
                        <div class="user-profile">
                            <div class="user-avatar" style="padding-bottom: 25px;">
                                <img src="../../../public/img/avatar.png">
                            </div>
                            <h6 class="mb-2"><?php echo htmlspecialchars($userInfo['name'] . ' ' . $userInfo['surname']); ?></h6>
                            <p class="mb-1"><?php echo htmlspecialchars($userInfo['phone_1']); ?></p>
                            <p><?php echo htmlspecialchars($userInfo['email']); ?></p>
                        </div>
                        <div class="about">
                            <h5 style="color: #086972; text-align: center; font-weight: bold;">About</h5>
                            <p style="text-align: center; margin-bottom: 25px;">Here you can edit your credentials. Make sure not to share your personal information with anyone.</p>
                            <p style="text-align: center;">You need to enter your password before you make any changes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div id="message"></div>
                    <form class="needs-validation" id="profileform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" novalidate>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h6 class="mb-2 text-primary" style="color: #086972 !important; font-weight: bold;">Personal Details</h6>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter first name" value="<?php echo isset($userInfo['name']) ? htmlspecialchars($userInfo['name']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="surname">Last Name</label>
                                    <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter last name" value="<?php echo isset($userInfo['surname']) ? htmlspecialchars($userInfo['surname']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email" aria-describedby="inputGroupPrepend" placeholder="Enter email address" value="<?php echo isset($userInfo['email']) ? htmlspecialchars($userInfo['email']) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="phone">Primary Phone</label>
                                    <input type="text" class="form-control" id="phone_1" name="phone_1" placeholder="Enter phone number" value="<?php echo isset($userInfo['phone_1']) ? htmlspecialchars($userInfo['phone_1']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="phone">Secondary Phone (Optional)</label>
                                    <input type="text" class="form-control" id="phone_2" name="phone_2" placeholder="Enter phone number" value="<?php echo isset($userInfo['phone_2']) ? htmlspecialchars($userInfo['phone_2']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h6 class="mt-3 mb-2 text-primary" style="color: #086972 !important; font-weight: bold;">Change Password</h6>
                            </div>
                            <!-- Old Password Field -->
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="oldPassword">Old Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="Enter old password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="bi bi-eye-slash" onclick="toggleUserPassword('oldPassword');" style="cursor: pointer;"></i>
                                            </span>
                                            <div class="invalid-feedback">Please enter password.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <!-- New Password Field -->
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="bi bi-eye-slash" onclick="toggleUserPassword('newPassword');" style="cursor: pointer;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Repeat New Password Field -->
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="newPasswordRepeat">Repeat New Password</label>
                                    <input type="password" class="form-control" id="newPasswordRepeat" name="newPasswordRepeat" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="text-right">
                                    <button type="button" id="submit" name="submit" class="btn btn-primary" disabled>Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    #submit.btn-primary:disabled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #ffffff !important;
    }
  </style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the submit button
        const submitBtn = document.getElementById('submit');

        // Function to enable the submit button
        function enableSubmitButton() {
            submitBtn.disabled = false;
        }

        // Attach an event listener to all input and textarea fields within the form
        document.querySelectorAll('#profileform input, #profileform textarea').forEach(input => {
            input.addEventListener('input', enableSubmitButton);
        });
    });
</script>

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

        function toggleUserPassword(inputId) {
            var input = document.getElementById(inputId);
            var icon = input.parentNode.querySelector('.input-group-text i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = "password";
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        }
</script>

<script>
$(document).ready(function() {
    // Fetch and display user details
    $.ajax({
        url: '../../Models/editAccount.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('GET Response:', response); // Debugging
            if (response.status === 'success') {
                // Populate user details
                $('#name').val(response.user.name);
                $('#surname').val(response.user.surname);
                $('#phone_1').val(response.user.phone_1);
                $('#phone_2').val(response.user.phone_2);
                $('#email').val(response.user.email);
            } else {
                console.error('Error fetching user details:', response.message);
                $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
            setTimeout(function() { $('#message > div').fadeOut(); }, 5000);
        },
        error: function(xhr, status, error) {
            console.error('GET AJAX error occurred:', error);
            $('#message').html('<div class="alert alert-danger">An error occurred: ' + xhr.responseText + '</div>');
            setTimeout(function() { $('#message > div').fadeOut(); }, 5000);
        }
    });

    // Form submission for updating account details
    $('#profileform').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../Models/editAccount.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('POST Response:', response);
                $('#message').html('<div class="alert alert-' + (response.status === 'success' ? 'success' : 'danger') + '">' + response.message + '</div>');
                setTimeout(function() { $('#message > div').fadeOut(); }, 10000);
            },
            error: function(xhr) {
                console.error('POST AJAX error occurred:', xhr.status, xhr.responseText);
                $('#message').html('<div class="alert alert-danger">An error occurred: ' + xhr.responseText + '</div>');
            }
        });
    });

    // Trigger form submission on "Update" button click
    $('#submit').click(function() {
        $('#profileform').submit();
    });
});
</script>

</body>
</html>