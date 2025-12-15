<?php
// Include the database configuration file from a specified path
include_once __DIR__ . '/../../config/config.php';
// Function to translate the status of a property
include_once '../Views/users/dashboard_role/dashboard_admin/helper_functions.php';


// Check if there is a connection error with the database and handle it
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
// Check if the 'property_id' has been sent via POST request
if (isset($_POST['property_id'])) {
    $propertyId = $_POST['property_id'];
    // Prepare an SQL statement to fetch property and related user details
    $stmt = $mysqli->prepare("SELECT
        p.floor,
        p.number,
        p.status,
        t.name AS tenant_name,
        t.surname AS tenant_surname,
        t.phone_1 AS tenant_phone,
        t.email AS tenant_email,
        o.name AS owner_name,
        o.surname AS owner_surname,
        o.phone_1 AS owner_phone,
        o.email AS owner_email,
        p.area,
        p.rooms,
        p.bathrooms,
        p.furnished,
        p.pet,
        p.parking,
        p.details,
        p.comment
    FROM
        property p
        LEFT JOIN users t ON p.tenant_id = t.user_id
        LEFT JOIN users o ON p.owner_id = o.user_id
    WHERE property_id = ?");

    $stmt->bind_param("i", $propertyId);
    // Execute statement and handle potential errors
    if (!$stmt->execute()) {
        echo "<p>Error executing statement: " . htmlspecialchars($stmt->error) . "</p>";
        $stmt->close();
        $mysqli->close();
        exit;
    }
    $result = mysqli_stmt_get_result($stmt);


    //Function to display Available or Rented in value of form
    function displayStatus($status)
    {
        if ($status == 'a') {
            echo "<option value='a' selected>Available</option>";
            echo "<option value='r'>Rented</option>";
        } else {
            echo "<option value='a'>Available</option>";
            echo "<option value='r' selected>Rented</option>";
        }
    }

    // Function to display options for parking
    function displayParking($selectedValue = '')
    {
        $options = '<option value="c">Yes</option>';
        $options .= '<option value="u">No</option>';

        // Add 'selected' attribute to the option that matches the selected value
        if ($selectedValue === 'c') {
            $options = str_replace('value="c"', 'value="c" selected', $options);
        } elseif ($selectedValue === 'u') {
            $options = str_replace('value="u"', 'value="u" selected', $options);
        }

        return $options;
    }

    // Function to display options for yes or no
    function displayYesNo($selectedValue = '')
    {
        $options = '<option value="y">Yes</option>';
        $options .= '<option value="n">No</option>';

        // Add 'selected' attribute to the option that matches the selected value
        if ($selectedValue === 'y') {
            $options = str_replace('value="y"', 'value="y" selected', $options);
        } elseif ($selectedValue === 'n') {
            $options = str_replace('value="n"', 'value="n" selected', $options);
        }

        return $options;
    }
    ?>

    <!doctype html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../../public/css/fetchPropertyDetails.css">

    </head>

    <body>

        <div id="message"></div>
        
        <?php


        // Check if there are results and output them
        if ($result && $row = $result->fetch_assoc()) { ?>

            <div class="container">
                <div class="row gutters">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <form class="needs-validation" id="propertyform"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="row gutters">

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="floor">Floor</label>
                                        <input required type="text" class="form-control" id="floor" name="floor"
                                            placeholder="Enter floor number"
                                            value="<?php echo isset($row['floor']) ? htmlspecialchars($row['floor']) : ''; ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid floor number.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="number">Number</label>
                                        <input required type="text" class="form-control" id="number" name="number"
                                            placeholder="Enter property number"
                                            value="<?php echo isset($row['number']) ? htmlspecialchars($row['number']) : ''; ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid property number.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php displayStatus($row['status'] ?? ''); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid status.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="area">Area</label>
                                        <input required type="text" class="form-control" id="area" name="area"
                                            placeholder="Enter property area"
                                            value="<?php echo isset($row['area']) ? htmlspecialchars($row['area']) : ''; ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid area.
                                        </div>

                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="rooms">Rooms</label>
                                        <input trequired ype="text" class="form-control" id="rooms" name="rooms"
                                            placeholder="Enter number of rooms"
                                            value="<?php echo isset($row['rooms']) ? htmlspecialchars($row['rooms']) : ''; ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid number of rooms.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="bathrooms">Bathrooms</label>
                                        <input required type="text" class="form-control" id="bathrooms" name="bathrooms"
                                            placeholder="Enter number of bathrooms"
                                            value="<?php echo isset($row['bathrooms']) ? htmlspecialchars($row['bathrooms']) : ''; ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid number of bathrooms.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="furnished">Furnished</label>
                                        <select class="form-control" id="furnished" name="furnished">
                                            <?php echo displayYesNo($row['furnished'] ?? ''); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid option.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="pet">Pet Allowed</label>
                                        <select class="form-control" id="pet" name="pet">
                                            <?php echo displayYesNo($row['pet'] ?? ''); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid option.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="parking">Parking</label>
                                        <select class="form-control" id="parking" name="parking">
                                            <?php echo displayParking($row['parking'] ?? ''); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid option.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="details">Details</label>
                                        <textarea class="form-control" id="details" name="details" rows="3"
                                            placeholder="Enter property details"><?php echo isset($row['details']) ? htmlspecialchars($row['details']) : ''; ?></textarea>
                                        <div class="invalid-feedback">
                                            Please enter valid details.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="comment">Comments</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="3"
                                            placeholder="Enter any additional comments"><?php echo isset($row['comment']) ? htmlspecialchars($row['comment']) : ''; ?></textarea>
                                        <div class="invalid-feedback">
                                            Please enter valid comments.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col -sm-12 col-12">
                                    <div class="text-right">
                                        <button type="button" id="submit" name="submit" class="btn btn-primary"
                                            disabled>Update</button>
                                    </div>
                                </div>
                                <input type="hidden" id="property_id" name="property_id"
                                    value="<?php echo htmlspecialchars($propertyId); ?>">

                            </div>
                        </form>
                    </div>
                </div>
            </div>


        <?php
        } else {
            echo "<p>No details found for this property.</p>";
        }

        // Close the statement and connection to free up resources
        $stmt->close();
} else {
    echo "<p>Invalid property ID provided.</p>"; // Error message if property ID is not provided
} ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


    <script>
        // Get the submit button
        const submitBtn = document.getElementById('submit');

        // Function to enable the submit button
        function enableSubmitButton() {
            submitBtn.disabled = false;
        }

        // Attach an event listener to all input and textarea fields within the form
        document.querySelectorAll('#propertyform input, #propertyform textarea, #propertyform select').forEach(input => {
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
                    url: '../Models/updateProperty.php', // Change the URL to match your endpoint for updating property details
                    type: 'POST',
                    data: $('#propertyform').serialize(),
                    dataType: 'json',
                    success: function (response) {
                        console.log('POST Response:', response);
                        $('#message').html('<div class="alert alert-' + (response.status === 'success' ? 'success' : 'danger') + '">' + response.message + '</div>');
                        // Hide the message after 10 seconds
                        setTimeout(function () {
                            $('#message > div').fadeOut();
                            if (response.status === 'success') {
                                // Reset the form
                                // $('#propertyform')[0].reset();/\
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
            $('#submit').on('click', function () {
                submitForm();
            });

            // // Form submission for updating property details on pressing Enter key
            $('#propertyform').on('keypress', function (e) {
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