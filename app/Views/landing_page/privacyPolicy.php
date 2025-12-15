<?php
include '../../../config/config.php'; // Ensure your DB connection settings are correct
session_start();

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy</title>
  <!-- Tab Icon -->
  <link rel="icon" href="../../../public/img/logo.png">

  <!-- CSS Files -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../../public/css/landing_page.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
  <style>
    .br-minimal {
      margin: 0 !important;
      line-height: 0.4 !important;
      /* This forces the style to take priority */
    }
    .text-privacy {
      color: black;
      font-size: 13px;
    }
  </style>

</head>

<body>

  <?php include 'header.php'; ?>
  <!-- Main  -->
  <main>
    <!-- Contact Section -->
    <div class="container text-light p p-lg-0 pt-lg-5 text-center" style="margin-top: 20px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1 class="pb-3" style="color: black;">Privacy Policy</h1>
            </div>
        </div>

        <br>
        <div class="col-md-8 offset-md-2 text-privacy">
    <!-- Data Collection -->
    <h4>Data Collection</h4>
    <p>We collect various types of personal data from users to provide and improve our services. This may include information such as ID number, Emails, Phone numbers and nationality. The data is collected for purposes such as account registration, rental agreement creation and communication with users.</p>
    <br>
    <!-- Data Security -->
    <h4>Data Security</h4>
    <p>We take the security of user data seriously and have implemented various measures to protect it. These measures include encryption of sensitive information, strict access controls, and regular security audits. We ensure that user data is stored securely and protected from unauthorized access or disclosure.</p>
    <br>
    <!-- User Rights -->
    <h4>User Rights</h4>
    <p>Users have the right to access, correct, or delete their personal information held by us. To exercise these rights, users can submit a data access or deletion request through our designated channels. We will respond to such requests within the applicable timeframes and may require verification of identity to process them. There are no fees associated with exercising these rights, and users can contact us for assistance.</p>
    <br>
        <!-- Third-party Sharing -->
        <h4>Third-party Sharing</h4>
    <p>We prioritize the privacy and security of user data. We do not share user data with third-party sites or services. Your information remains confidential and is used solely for the purposes outlined in our Privacy Policy. You can trust that your data is safe with us.</p>
    <br>  
    <!-- Cookies -->
    <h4>Cookies</h4>
    <p>We value your privacy and do not track cookies or use similar tracking technologies on our website. Your browsing experience is not monitored, and no personal information is collected through cookies. You can browse our website with confidence knowing that your privacy is protected.</p>
</div>



        

  </main>

  <?php include 'footer.php'; ?>
  <?php include 'login_modal.php'; ?>

  <!-- Include Bootstrap JS for modal functionality -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <!-- <script src="modal-script.js"></script> -->


</body>

</html>
