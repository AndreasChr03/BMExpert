<?php
session_start();
include '../../config/config.php';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 2 )) {
  header("location: ../../index.php");
  exit;
}

$name = $surname = $email = $nationality = $phone_1 = $phone_2 = "";
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/landing_page.css" />
  <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo.png" />
  
  <title>BMExpert</title>
</head>

<body id="page-top" style="background-color: #eeeeee">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container" style="padding-bottom: 10px;">
          <div class="row justify-content-center">
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                  <h3 class="text-center font-weight-light my-4">Tenant Register</h3>
                  <!-- <h2 class="text-center font-weight-light my-4"><?php echo "USER id". $_SESSION['user_id']?> </h2> -->
                  <span id="message"><?php include ("../Models/tenantRegisterValid.php")?></span>
                </div>
                <div class="card-body">
                  <!-- Close (X) Icon -->
                  <a href="javascript:history.back()" class="btn btn-light" style="position: absolute; right: 10px; top: 10px;">
                    <i class="bi bi-x-lg"></i>
                  </a>
                  <form class="row g-3 needs-validation" method="post"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                    <div class="col-md-12 form-group">
                      <label for="role">Role</label>
                      <select class="form-select" id="role" name="role" disabled required>
                          <?php
                          if (isset($_SESSION['role_id'])) {
                              // The role_id the current user can create (assuming it's always the next id)
                              $role_to_create_id = $_SESSION['role_id'] + 1;

                              // Prepare the SQL query to get the role name based on the role_to_create_id
                              $sql = "SELECT role_id, role_name FROM roles WHERE role_id = ?";
                              $stmt = $mysqli->prepare($sql);
                              $stmt->bind_param("i", $role_to_create_id);
                              $stmt->execute();
                              $result = $stmt->get_result();
             
                              
                              if ($result && $result->num_rows > 0) {
                                  // Fetch the role the user can create an account for
                                  $row = $result->fetch_assoc();
                                  echo "<option value='" . htmlspecialchars($row['role_id']) . "'>" . htmlspecialchars($row['role_name']) . "</option>";

                              } else {
                                  echo "<option>No roles available for account creation.</option>";
                              }

                              $stmt->close();
                          } else {
                              echo "<option>You are not authorized to view this page.</option>";
                          }
                          ?>
                      </select>
                      
                      <?php 
                              //Prepare SQL query to get the available propertys to be rented based on the user_id of the owner. 
                              //Present them as a dropdown list for the owner to select the property to be rented.
                              //Assign the selected property to the tenant.
          
                              $query = "SELECT property_id, number, status FROM property WHERE owner_id = ?";
                              $stmtProperties = $mysqli->prepare($query);
                              $stmtProperties->bind_param("i", $_SESSION['user_id']);
                              $stmtProperties->execute();
                              $resultProperties = $stmtProperties->get_result();
                              if ($resultProperties && $resultProperties->num_rows > 0) {
                                  // Output the property select dropdown
                                  echo "<div class='col-md-12 form-group'>";
                                  echo "<label for='property_rented' style='margin-bottom: 10px; margin-top: 10px;'>Unit for rent</label>";
                                  echo "<select class='form-select' id='property_rented' name='property_rented' required>";
                                  echo "<option value='' disabled selected>Select property:</option>";

                                  while ($rowProperty = $resultProperties->fetch_assoc()) {
                                      if ($rowProperty['status'] == 'a') {
                                          echo "<option value='" . htmlspecialchars($rowProperty['property_id']) . "'>" . htmlspecialchars($rowProperty['number']) . "</option>";
                                      }
                                  }
                                  echo "</select>";
                                  echo "<div class='invalid-feedback'>Please select a property from the list.</div>";
                                  echo "</div>"; // Close div after the select
                              } else {
                                  echo "<div class='col-md-12 form-group'>";
                                  echo "<label for='property_rented' style='margin-bottom: 10px; margin-top: 10px;'>Unit for rent</label>";
                                  echo "<select class='form-select' id='property_rented' name='property_rented' required>";
                                  echo "<option value='' disabled>No properties available for renting. Please add a property.</option>";
                                  echo "</select>";
                                  echo "</div>"; // Close div after the select
                              }
                            
                            $stmtProperties->close();
                      
                      if (isset($row)): ?>
                        <input type="hidden" name="role_id" value="<?php echo isset($row['role_id']) ? $row['role_id'] : ''; ?>" />
                      <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                      <label for="validationCustom01" class="form-label">First name</label>
                      <input type="text" class="form-control" id="validationCustom01" name="name"
                        value="<?php echo $name; ?>" required>
                      <div class="invalid-feedback">Please enter your first name.</div>
                    </div>
                    <div class="col-md-6">
                      <label for="validationCustom01" class="form-label">Last name</label>
                      <input type="text" class="form-control" id="validationCustom02" name="surname"
                        value="<?php echo $surname; ?>" required>
                      <div class="invalid-feedback">Please enter your last name.</div>
                    </div>
                    <div class="col-md-4 mb-3">
  <!-- Nationality Dropdown -->
  <label for="nationality" class="form-label">Nationality</label>
  <select class="form-select" id="nationality" name="nationality" required onchange="handleNationalityChange(this)">
    <option selected disabled value="">Choose...</option>
    <?php
      $options = [
          "Afghan", "Albanian", "Algerian", "American", "Andorran", "Angolan", "Antiguans",
          "Argentinean", "Armenian", "Australian", "Austrian", "Azerbaijani", "Bahamian",
          "Bahraini", "Bangladeshi", "Barbadian", "Barbudans", "Batswana", "Belarusian",
          "Belgian", "Belizean", "Beninese", "Bhutanese", "Bolivian", "Bosnian", "Brazilian",
          "British", "Bruneian", "Bulgarian", "Burkinabe", "Burmese", "Burundian", "Cambodian",
          "Cameroonian", "Canadian", "Cape Verdean", "Central African", "Chadian", "Chilean",
          "Chinese", "Colombian", "Comoran", "Congolese", "Costa Rican", "Croatian", "Cuban",
          "Cypriot", "Czech", "Danish", "Djibouti", "Dominican", "Dutch", "East Timorese",
          "Ecuadorean", "Egyptian", "Emirian", "Equatorial Guinean", "Eritrean", "Estonian",
          "Ethiopian", "Fijian", "Filipino", "Finnish", "French", "Gabonese", "Gambian",
          "Georgian", "German", "Ghanaian", "Greek", "Grenadian", "Guatemalan", "Guinea-Bissauan",
          "Guinean", "Guyanese", "Haitian", "Herzegovinian", "Honduran", "Hungarian", "I-Kiribati",
          "Icelander", "Indian", "Indonesian", "Iranian", "Iraqi", "Irish", "Israeli", "Italian",
          "Ivorian", "Jamaican", "Japanese", "Jordanian", "Kazakhstani", "Kenyan", "Kittian and Nevisian",
          "Kuwaiti", "Kyrgyz", "Laotian", "Latvian", "Lebanese", "Liberian", "Libyan", "Liechtensteiner",
          "Lithuanian", "Luxembourger", "Macedonian", "Malagasy", "Malawian", "Malaysian", "Maldivian",
          "Malian", "Maltese", "Marshallese", "Mauritanian", "Mauritian", "Mexican", "Micronesian",
          "Moldovan", "Monacan", "Mongolian", "Moroccan", "Mosotho", "Motswana", "Mozambican", "Namibian",
          "Nauruan", "Nepalese", "New Zealander", "Ni-Vanuatu", "Nicaraguan", "Nigerian", "Nigerien",
          "North Korean", "Northern Irish", "Norwegian", "Omani", "Pakistani", "Palauan", "Panamanian",
          "Papua New Guinean", "Paraguayan", "Peruvian", "Polish", "Portuguese", "Qatari", "Romanian",
          "Russian", "Rwandan", "Saint Lucian", "Salvadoran", "Samoan", "San Marinese", "Sao Tomean",
          "Saudi", "Scottish", "Senegalese", "Serbian", "Seychellois", "Sierra Leonean", "Singaporean",
          "Slovakian", "Slovenian", "Solomon Islander", "Somali", "South African", "South Korean",
          "Spanish", "Sri Lankan", "Sudanese", "Surinamer", "Swazi", "Swedish", "Swiss", "Syrian",
          "Taiwanese", "Tajik", "Tanzanian", "Thai", "Togolese", "Tongan", "Tobagonian", "Tunisian",
          "Turkish", "Tuvaluan", "Ugandan", "Ukrainian", "Uruguayan", "Uzbekistani", "Venezuelan",
          "Vietnamese", "Welsh", "Yemenite", "Zambian", "Zimbabwean"
      ];  
      foreach ($options as $option) {
          echo "<option value='$option'" . ($nationality === $option ? " selected" : "") . ">$option</option>";
      }
    ?>
    <option value="Other">Other</option>
  </select>
  <div class="invalid-feedback">Please select your nationality.</div>
</div>

<div class="col-md-8 mb-3"> <!-- You can add mb-3 for spacing if needed -->
  <!-- Email Input -->
  <label for="validationCustomUsername" class="form-label">Email</label>
  <div class="input-group has-validation">
    <span class="input-group-text" id="inputGroupPrepend">@</span>
    <input type="email" class="form-control" id="validationCustomUsername03"
      aria-describedby="inputGroupPrepend" name="email" value="<?php echo $email; ?>" required>
    <div class="invalid-feedback">Please enter email address.</div>
  </div>
</div>
                    <div class="col-md-6">
                      <label for="customerPhone">Primary Phone</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="phone_1" name="phone_1"
                          value="<?php echo $phone_1; ?>" required>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="customerPhone">Secondary Phone (Optional)</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="phone_2" name="phone_2"
                          value="<?php echo $phone_2; ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="inputPassword">Password</label>
                      <div class="input-group">
                        <input class="form-control" id="inputPassword" name="password" type="password" required>
                        <span class="input-group-text">
                          <i class="bi bi-eye-slash" onclick="toggleVisibility('inputPassword', this)" style="cursor: pointer;"></i>
                        </span>
                        <div class="invalid-feedback">Please enter a password.</div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="repeatPassword">Confirm Password</label>
                      <div class="input-group">
                        <input class="form-control" id="repeatPassword" name="repeatpassword" type="password" required>
                        <span class="input-group-text">
                          <i class="bi bi-eye-slash" onclick="toggleVisibility('repeatPassword', this)" style="cursor: pointer;"></i>
                        </span>
                        <div class="invalid-feedback">Please re-enter the password.</div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-4 mb-0 w-100">
                      <!-- Conditionally disable the button if not logged in or role_id is 3 -->
                      <button class="btn btn-primary w-100" id="loginSubmit" name="submit"
                              type="submit" <?php if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] == 3) echo 'disabled'; ?>>
                        Register
                      </button>
                    </div>
                    <div class="d-flex justify-content-center my-3">
                      <span class="me-2">Already have an account?</span>
                      <a href="../../../index.php" class="small" style="color: #086972;">Login now</a>
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

    document.getElementById('phone_1').addEventListener('input', function (e) {
      this.value = this.value.replace(/[^\d]/g, ''); // Keep only digits
    });
    document.getElementById('phone_2').addEventListener('input', function (e) {
      this.value = this.value.replace(/[^\d]/g, ''); // Keep only digits
    });
  </script>

  <script>
    // Wait for the DOM to fully load
    document.addEventListener("DOMContentLoaded", function () {
      // Select all elements with the class 'alert'
      var alerts = document.querySelectorAll('.alert');

      // Function to gradually decrease opacity
      function fadeOut(element) {
        var op = 1;  // initial opacity
        var timer = setInterval(function () {
          if (op <= 0.1) {
            clearInterval(timer);
            element.style.display = 'none';
          }
          element.style.opacity = op;
          element.style.filter = 'alpha(opacity=' + op * 100 + ")";
          op -= op * 0.1;
        }, 50); // Adjust speed here
      }

      // Set a timeout to fade out alerts after 5 seconds
      setTimeout(function () {
        alerts.forEach(function (alert) {
          fadeOut(alert);
        });
      }, 5000); // 5000 milliseconds = 5 seconds
    });
  </script>

  <script>
  function handleNationalityChange(selectElement) {
    if (selectElement.value === 'Other') {
      const parentDiv = selectElement.parentElement;
      const inputField = document.createElement('input');
      inputField.type = 'text';
      inputField.name = 'nationality';
      inputField.id = 'nationality';
      inputField.className = 'form-control';
      inputField.required = true;
      inputField.placeholder = 'Enter nationality';
      inputField.maxLength = 50;

      parentDiv.innerHTML = ''; // Clear the existing select element
      parentDiv.appendChild(inputField); // Add the new input field

      // Re-attach the label and feedback
      const label = document.createElement('label');
      label.className = 'form-label';
      label.htmlFor = 'nationality';
      label.textContent = 'Nationality';

      const feedback = document.createElement('div');
      feedback.className = 'invalid-feedback';
      feedback.textContent = 'Please enter your nationality.';

      parentDiv.insertBefore(label, inputField);
      parentDiv.appendChild(feedback);
    }
  }
</script>

</body>

</html>
