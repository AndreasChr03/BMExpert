<?php
// Start session and include necessary files
session_start();
include_once '../../../../../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit();
}

// Check if user_id is provided in the POST request
if (!isset($_POST['tenant_id'])) {
    echo json_encode(array('success' => false, 'message' => 'User ID not provided.'));
    exit();
}

// Get the user ID from the POST request
$user_id = $_POST['tenant_id'];

// Prepare SQL query to get the properties associated with the selected tenant_id
$queryProperties = "SELECT property_id, number, status FROM property WHERE tenant_id = ?";
$stmtProperties = $mysqli->prepare($queryProperties);
$stmtProperties->bind_param("i", $user_id);

// Execute the prepared statement to fetch properties associated with the selected tenant
if ($stmtProperties->execute()) {
    // Get the result set
    $resultProperties = $stmtProperties->get_result();

    // Fetch properties associated with the selected tenant into an array
    $properties = array();
    while ($rowProperty = $resultProperties->fetch_assoc()) {
        $properties[] = $rowProperty;
    }

    // Close the prepared statement
    $stmtProperties->close();
} else {
    // Error occurred while executing the query
    echo json_encode(array('success' => false, 'message' => 'Error fetching properties associated with the selected tenant.'));
    exit();
}

// Prepare SQL query to get all available properties with status 'a'
$queryAllProperties = "SELECT property_id, number, status FROM property WHERE status = 'a'";
$stmtAllProperties = $mysqli->prepare($queryAllProperties);

// Execute the prepared statement to fetch all available properties
if ($stmtAllProperties->execute()) {
    // Get the result set
    $resultAllProperties = $stmtAllProperties->get_result();

    // Fetch all available properties into the same array
    while ($rowProperty = $resultAllProperties->fetch_assoc()) {
        $properties[] = $rowProperty;
    }

    // Close the prepared statement
    $stmtAllProperties->close();
} else {
    // Error occurred while executing the query
    echo json_encode(array('success' => false, 'message' => 'Error fetching all available properties.'));
    exit();
}

//Prepare SQL query to get the user details
$queryUser = "SELECT name, surname, phone_1, phone_2, email FROM users WHERE user_id = ?";
$stmtUser = $mysqli->prepare($queryUser);
$stmtUser->bind_param("i", $user_id);

// Execute the prepared statement to fetch user details
if ($stmtUser->execute()) {
    // Get the result set
    $resultUser = $stmtUser->get_result();

    // Fetch user details
    $user = $resultUser->fetch_assoc();

    // Close the prepared statement
    $stmtUser->close();
} else {
    // Error occurred while executing the query
    echo json_encode(array('success' => false, 'message' => 'Error fetching user details.'));
    exit();
}

// Return JSON response with user details and properties data
// echo json_encode(array('success' => true, 'user' => $user, 'properties' => $properties));
$result = array('success' => true, 'user' => $user, 'properties' => $properties);

// exit();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</head>

<body>
    <!-- <div id="message"></div> -->

    <div class="container">
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <form class="needs-validation" id="tenantForm"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row gutters">

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="property_rented">Unit To be Rented</label>
                                <select class="form-select" id="property_rented" name="property_rented" required>
                                    <?php foreach ($result['properties'] as $property): ?>
                                        <option value="<?php echo $property['property_id']; ?>">
                                            <?php echo $property['number']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a property.
                                </div>
                            </div>
                            <input type="hidden" id="selected_property_number" name="selected_property_number"
                                value="<?php echo $property['number']; ?>">
                            <input type="hidden" id="tenant_id" name="tenant_id" value="<?php echo $user_id; ?>">
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_name" class="form-label">First name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required
                                    onkeyup="validateLetters(this)"
                                    value="<?php echo htmlspecialchars($result['user']['name']); ?>">
                                <div class="invalid-feedback">Please enter your first name.</div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_surname" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="edit_surname" name="surname" required
                                    onkeyup="validateLetters(this)"
                                    value="<?php echo htmlspecialchars($result['user']['surname']); ?>">
                                <div class="invalid-feedback">Please enter your last name.</div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_phone_1">Primary Phone</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_phone_1" name="phone_1" required
                                        onkeyup="validateNumbers(this)"
                                        value="<?php echo htmlspecialchars($result['user']['phone_1']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_phone_2">Secondary Phone (Optional)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_phone_2" name="phone_2"
                                        onkeyup="validateNumbers(this)"
                                        value="<?php echo htmlspecialchars($result['user']['phone_2']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_email" class="form-label">Email</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="email" class="form-control" id="edit_email" name="email" required
                                        onkeyup="validateEmail(this)"
                                        value="<?php echo htmlspecialchars($result['user']['email']); ?>">
                                    <div class="invalid-feedback">Please enter email address.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="edit_nationality" class="form-label">Nationality</label>
                                <select class="form-select" id="edit_nationality" name="nationality" required>
                                    <?php
                                    $options = ["Cypriot", "Greek", "British", "Armenian", "Russian", "Bulgarian", "Ukranian"];
                                    foreach ($options as $option) {
                                        echo "<option value='$option'" . ($result['user']['nationality'] === $option ? " selected" : "") . ">$option</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Please select your nationality.</div>
                            </div>
                        </div>



                        <div class="col-xl-12 col-lg-12 col-md-12 col -sm-12 col-12">
                            <div class="text-right">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" id="saveTenantButton" class="btn btn-primary"
                                        name="updateUser">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>


    <script>
        // Get the submit button
        const submitBtn = document.getElementById('saveTenantButton');

        // Function to enable the submit button
        function enableSubmitButton() {
            submitBtn.disabled = false;
        }

        // Attach an event listener to all input and textarea fields within the form
        document.querySelectorAll('#tenantForm input, #tenantForm textarea, #tenantForm select').forEach(input => {
            input.addEventListener('input', enableSubmitButton);
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
        })();
    </script>

    <script>
        $(document).ready(function () {
            // Function to handle form submission
            function submitForm() {
                $.ajax({
                    url: 'updateTenant.php', // Change the URL to match your endpoint for updating property details
                    type: 'POST',
                    data: $('#tenantForm').serialize(),
                    dataType: 'json',
                    success: function (response) {
                        console.log('POST Response:', response);
                        $('#message').html('<div class="alert alert-' + (response.status === 'success' ? 'success' : 'danger') + '">' + response.message + '</div>');
                        // Hide the message after 10 seconds
                        setTimeout(function () {
                            $('#message > div').fadeOut();
                            if (response.status === 'success') {
                                // Reset the form
                                // $('#tenantForm')[0].reset();
                                // Optionally reload the page
                                // location.reload();
                            }
                        }, 10000);

                    },
                    error: function (xhr) {
                        console.error('POST AJAX error occurred:', xhr.status, xhr.responseText);
                        $('#message').html('<div class="alert alert-danger">An error occurred: ' + xhr.responseText + '</div>');
                    }
                });
            }

            // Attach click event listener to the "Update" button
            $('#saveTenantButton').on('click', function () {
                submitForm();
            });

            // // Form submission for updating property details on pressing Enter key
            $('#tenantForm').on('keypress', function (e) {
                if (e.which === 13) { // Check if Enter key is pressed
                    e.preventDefault(); // Prevent default form submission
                    submitForm(); // Submit the form
                }
            });
        });

        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
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
        })();

    </script>


</body>

</html>