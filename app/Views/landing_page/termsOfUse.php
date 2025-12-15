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
  <meta charset="UTF-10">
  <meta name="viewport" content="width=device-width, initial-scale=2.0">
  <title>Terms Of Use</title>
  <!-- Tab Icon -->
  <link rel="icon" href="../../../public/img/logo.png">

  <!-- CSS Files -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@2.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../../public/css/landing_page.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
  <style>
    .br-minimal {
      margin: 0 !important;
      line-height: 0.2 !important;
      /* This forces the style to take priority */
    }
  </style>

</head>

<body>

  <?php include 'header.php'; ?>
  <!-- Main  -->
  <main>
    <!-- Contact Section -->
    <div class="container text-light p p-lg-0 pt-lg-5 text-left" style="margin-top: 20px; margin-bottom: 50px;">
    <div class="row align-items-center justify-content-center">

    <div class="col-md-12 text-center mb-2">
        <h1 class="pb-3" style="color: black;">Terms and Policies</h1>
        <p style="color: black;">Find below the relevant legislation</p>
    </div>
</div>
<br>  
<br>



        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
            <h4 class="pb-3" style="color: black;">Apartment Building Legislation</h4>
          </div>
          <div class="col-md-2">
            <a href="../../../public/policies/Administrative-Committee.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>

        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
          <h4 class="pb-3" style="color: black;">Apartment Building Insurance Legislation</h4>
          </div>
          <div class="col-md-2">
          <a href="../../../public/policies/Apartment-Building-Insurance-Legislation.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>

        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
            <h4 class="pb-3" style="color: black;">Commonly Owned Buildings Legislation</h4>
          </div>
          <div class="col-md-2">
            <a href="../../../public/policies/Commonly-owned-buildings.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>

        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
          <h4 class="pb-3" style="color: black;">Assessement and Management of Enviromental Noise</h4>
          </div>
          <div class="col-md-2">
          <a href="../../../public/policies/On-Assessment-and-Management-of-Environmental-Noise.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>

        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
          <h4 class="pb-3" style="color: black;">Tenancy Laws 1983-1995</h4>
          </div>
          <div class="col-md-2">
          <a href="../../../public/policies/The-about-tenancy-Laws-1983-1995.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>

        <div class="row align-items-left justify-content-left">
          <div class="col-md-10">
          <h4 class="pb-3" style="color: black;">Dog Legislation</h4>
          </div>
          <div class="col-md-2">
          <a href="../../../public/policies/The-dog-law.pdf" target="_blank"
              class="btn btn-primary" style="width: 100%;">View</a>
          </div>
        </div>


  </main>

  <?php include 'footer.php'; ?>
  <?php include 'login_modal.php'; ?>

  <!-- Include Bootstrap JS for modal functionality -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <!-- <script src="modal-script.js"></script> -->


</body>

</html>