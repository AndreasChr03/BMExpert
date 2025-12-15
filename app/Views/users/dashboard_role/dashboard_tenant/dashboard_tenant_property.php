<?php 
session_start();

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 3 )) {
    header("location: ../../../../../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
include __DIR__. '../../../../../../config/config.php';
$property_id = isset($_GET['property_id']) ? filter_var($_GET['property_id'], FILTER_SANITIZE_NUMBER_INT) : null;
$building_id = '';
$owner_id = '';
$tenant_id = '';
$address = '';
$area = '';
$rooms = '';
$floor = '';
$number = '';
$bathrooms = '';
$parking = '';
$details = '';
$status = '';
$pet = '';
$furnished = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')	{
	if (isset($_POST['property_id'])){
			// Assign variables from POST values, using the null coalescing operator to keep default value if not set
            
			$building_id = $_POST['building_id'];
			$owner_id = $_POST['owner_id'];
			$tenant_id = $_POST['tenant_id'];
			$address = $_POST['address'];
			$area = $_POST['area'];
			$rooms = $_POST['rooms'];
			$floor = $_POST['floor'];
			$number = $_POST['number'];
			$bathrooms = $_POST['bathrooms'];
			$parking = $_POST['parking'];
			$details = $_POST['details'];
			$status = $_POST['status'];
			$pet = $_POST['pet'];
			$furnished = $_POST['furnished'];
		}
		$errors = [];
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Users</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
</head>

<body>
<?php include "../dashboard_modules_header.php"?>
    <!-- Header -->
    <div id="property-details" class="property-container" style="font-size: 18px; position: relative;">

    <!-- Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div id="successMessage" class="alert alert-success message-box" role="alert" style="display: none;">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div id="errorMessage" class="alert alert-danger message-box" role="alert" style="display: none;">
            <?php echo $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to show a message div, wait for 3 seconds, then hide it
        function showMessage(divId) {
            const messageDiv = document.getElementById(divId);
            if (messageDiv.innerText.trim() !== "") {
                messageDiv.style.display = "block"; // Show the div
                setTimeout(function() {
                    messageDiv.style.display = "none"; // Hide after 3 seconds
                }, 3000);
            }
        }

        // Show success message if it exists
        showMessage('successMessage');

        // Show error message if it exists
        showMessage('errorMessage');
    });
</script>
<style>
body {
        font-family: "Open Sans", sans-serif !important;
    }

    .btn-primary {
        background-color: #086972 !important;
        color: #ffffff !important;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 4px;
        transition: 0.3s;
        line-height: 1;
    }

    /* Center align text in the message box */
    .message-content {
        text-align: center;
    }

    /* Style for the message box */
    .message-box {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 0.9);
        color: #000;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        display: none; /* Initially hidden */
    }
      body {
        font-family: "Open Sans", sans-serif !important;
      }
      .btn-primary {
        background-color: #086972 !important;
        color: #ffffff !important;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 4px;
        transition: 0.3s;
        line-height: 1;
      }
      .navbar-expand-md .navbar-nav .nav-link {
          padding-right: 8px;
          padding-left: 8px;
      }
      .property-container {
        display: flex;
        width: 100%; /* Make the container take full width */
        margin: 0 auto; /* Remove horizontal margin to use full width */
        background: #f9f9f9; 
        padding-left: 20px;
    }
    .property-images {
        flex: 1 1 50%; /* Takes up half of the flex container */
        position: relative;
    }
    .carousel {
        width: 100%;
        height: 100vh; /* Make the carousel take full viewport height */
        overflow: hidden;
        position: relative;
    }
    .carousel img {
        width: 100%;
        height: auto;
        display: none; /* Initially hide all images */
        object-fit: cover; /* Ensures images cover the area without distortion */
    }
    .carousel-control-prev,
    .carousel-control-next {
        top: 35%; /* Position halfway down */
        transform: translateY(-50%); /* Offset the position by half the height of the element */
    }
    .property-info {
        flex: 1 1 50%; /* Takes up the other half of the flex container */
        padding: 15px;
        border-radius: 0 8px 8px 0; /* Rounded corners on the right side */
    }
    .property-info h1, .property-info p { margin: 0 0 10px 0; }
    a { 
        text-decoration: none; 
    }
    #yourPhone::-webkit-outer-spin-button,
    #yourPhone::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* #yourPhone[type=number] {
        -moz-appearance: textfield;
    } */
    @media (max-width: 767px) {
    .property-container {
        flex-direction: column; /* Stack the child elements vertically */
    }
    .property-images {
        flex: 0 0 40vh; /* Set to your preferred height */
        position: relative; /* Ensures proper positioning context for arrows */
    }
    .property-info {
        transform: translateY(-400px);
    }
    .carousel img {
        width: 100%;
        height: 40vh; /* Adjusted to your specified perfect height */
        object-fit: cover;
        object-position: center; /* Ensures the image is centered within its element */
        display: block;
    }
    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
}.modal-custom {
    max-width: 90%; /* Set to 90% of the screen width, adjust as necessary */
}

@media (min-width: 576px) {
    .modal-custom {
        max-width: 800px; /* Adjust this value to suit your design */
    }
}
#propertiesTable td {
    word-wrap: break-word; /* Older CSS3 property for wrapping long words */
    overflow-wrap: break-word; /* Standard property to break long words */
    max-width: 200px; /* Set maximum width of the cell, adjust as needed */
}

    /* Custom styles for full-screen modal */


</style>

<!-- Property Details Page -->
<div id="property-details" class="property-container" style="padding-top: 50px; font-size: 18px;">
    <div class="property-images">
        <div id="image-carousel" class="carousel slide" data-bs-ride="carousel" >
        <!-- Carousel Inner -->
        <div class="carousel-inner" role="listbox"></div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#image-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#image-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>
    </div>
    <div class="property-info"></div>
</div>

<!-- Contact Owner Modal -->
<div class="modal fade" id="contactOwnerModal" tabindex="-1" aria-labelledby="contactOwnerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactOwnerModalLabel">Contact Owner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
        <div id="successAlert" class="alert alert-success" role="alert" style="display:none;">
            Message sent successfully!
        </div>
        <div id="errorAlert" class="alert alert-danger" role="alert" style="display:none;">
            Failed to send message. Please try again.
        </div>
        <form id="contactForm" class="needs-validation" action="process_form.php?property_id=<?php echo $property_id?>" method="post" novalidate>
            <!-- Hidden field for property_id -->
            <input type="hidden" id="propertyId" name="propertyId" value="">
            
                <!-- First Name Field -->
                <div class="md-4">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                    <div class="invalid-feedback">
                        Please enter a subject.
                    </div>
            </div>
            <div class="mb-3">
                <label for="yourMessage" class="form-label">Message</label>
                <textarea class="form-control" id="yourMessage" name="details" rows="3" required></textarea>
                <div class="invalid-feedback">
                    Please enter a message.
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Message</button>
        </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-custom">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="propertyDetailsModalLabel">My Requests</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="main">
        <div class="user-header d-flex justify-content-between align-items-center">
            <h2 style="margin-left: 20px;">Requests</h2>
        </div>
        <!-- Properties Table Display -->
        <div class="table-responsive">
            <table class="table table-bordered .table-hover" id="propertiesTable">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Details</th>
                        <th>Date Send</th>
                        <th>Status</th>
                        <th>Date Answer</th>
                        <th>Reply Details</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT r.* FROM request r JOIN property p ON r.property_id = p.property_id WHERE p.tenant_id = ?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0) : ?>
                        <?php while ($rows = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $rows['subject']; ?></td>
                                <td><?php echo $rows['details']; ?></td>
                                <td><?php echo $rows['start_date']; ?></td>
                                <?php if ($rows['status'] == 'p'): ?>
                                <td>Pending</td>
                                <?php else : ?>
                                <td>Finished</td>
                                <?php endif; ?>
                                <td><?php echo $rows['finish_date']; ?></td>
                                <td><?php echo $rows['replyDetails'];?> </td>
                                
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9">No properties found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
$.ajax({
    type: "POST",
    url: "process_form.php",
    data: formData,
    success: function (response) {
        if (response.message === 'Message has been sent') {
            // Handle success
        } else {
            $('#message-display').text(response.message).show(); // Display the message
            setTimeout(function() {
                $('#message-display').hide(); // Hide the message after some time
            }, 5000); // Adjust the time as needed
        }
    },
    error: function (xhr, status, error) {
        console.error("AJAX Error: ", status, error);
        $('#message-display').text('Error sending message. Check your network connection and try again.').show(); // Display error message
    }
});

// $(document).ready(function () {
//     $('#contactForm').submit(function (event) {
//         event.preventDefault(); // Always prevent the default submit to handle with AJAX

//         // Check form validity
//         if (!this.checkValidity()) {
//             event.stopPropagation(); // Stop the form from being submitted
//             $(this).addClass('was-validated'); // Add validation class to show feedback
//             return; // Stop the function here if the form is invalid
//         }

    //     var formData = $(this).serialize(); // Serialize the form data

    //     $.ajax({
    //         type: "POST",
    //         url: "process_form.php",
    //         data: formData,
    //         success: function (response) {
    //             if (response.message === 'Message has been sent') {
    //                 $('#successAlert').show(); // Show success alert
    //                 $('#errorAlert').hide(); // Hide error alert
    //                 $('#contactForm')[0].reset(); // Reset the form fields
    //                 $('#contactForm').removeClass('was-validated'); // Reset validation state
    //                 setTimeout(function() {
    //                     $('#contactOwnerModal').modal('hide'); // Use Bootstrap's method to hide the modal
    //                     $('body').removeClass('modal-open'); // Remove the class that prevents scrolling
    //                     $('.modal-backdrop').remove(); // Manually remove the backdrop
    //                 }, 3000);
    //             } else {
    //                 $('#errorAlert').text(response.message).show().delay(3000).fadeOut(); // Show and hide error alert
    //                 $('#successAlert').hide(); // Hide success alert
    //             }
    //         },
    //         error: function (xhr, status, error) {
    //             console.error("AJAX Error: ", status, error);
    //             $('#errorAlert').text('Error sending message. Check your network connection and try again. Details: ' + xhr.responseText).show();
    //         }
    //     });
    // });

    // Clear form validation state when the modal is hidden
//     $('#contactOwnerModal').on('hidden.bs.modal', function () {
//         $('body').removeClass('modal-open'); // Ensure scrolling is re-enabled
//         $('.modal-backdrop').remove(); // Ensure the backdrop is removed
//         $('#contactForm').trigger("reset");
//         $('#contactForm').removeClass('was-validated');
//         $('.alert').hide(); // Ensure alerts are hidden
//     });
// });
</script>

</script>

<script>
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
    })()

    $(document).ready(function () {
        $('#contactOwnerModal').on('hidden.bs.modal', function () {
            var form = $(this).find('form')[0];
            if (form) {
                form.reset();  // Reset form data
                form.classList.remove('was-validated'); // Reset validation state
            }
        });
    });

    $(document).ready(function () {
    let urlParams = new URLSearchParams(window.location.search);
    let propertyId = urlParams.get('property_id');

    // AJAX call to fetch property details from the server
    $.ajax({
        url: '../../../../Models/fetchPropertyPage.php',
        type: 'GET',
        data: { property_id: propertyId },
        dataType: 'json',
        success: function (property) {
            if (property.error) {
                alert("Failed to fetch property: " + property.error);
                return;
            }
            $('#propertyId').val(propertyId);
            let carouselInner = $('#image-carousel .carousel-inner');
            carouselInner.empty(); // Clear existing items if any

            // Populate the carousel with images from the property
            property.photos.forEach((photo, index) => {
                let itemClass = index === 0 ? 'carousel-item active' : 'carousel-item';
                let itemHTML = `<div class="${itemClass}">
                                    <img src="<?php echo BASE_URL; ?>public/img/uploads/${photo}" class="d-block w-100" alt="Property Image">
                                </div>`;
                carouselInner.append(itemHTML);
            });

            // Update property information
            let title = `Building ${property.building_id} - Unit ${property.number}`;
            translateValue('yesno', property.pet, function(translatedPet) {
                translateValue('parking', property.parking, function(translatedParking) {
                    let propertyHTML = `
                        <h1>${title}</h1>
                        <p>Rooms: ${property.rooms}</p>
                        <p>Bathrooms: ${property.bathrooms}</p>
                        <p>Floor: ${property.floor}</p>
                        <p>Area: ${property.area}  m<sup>2</sup></p>
                        <p>Pets Allowed: ${translatedPet}</p>
                        <p>Furnished: ${property.furnished ? 'Yes' : 'No'}</p>
                        <p>Parking: ${translatedParking}</p>
                        <p>Description: ${property.details}</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactOwnerModal">Contact Owner</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#propertyDetailsModal">View Details</button>
                    `;
                    $('.property-info').html(propertyHTML); // Ensure property info is updated
                });
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching property details: ", error);
            alert("Error loading property details. Please try again.");
        }
    });
});

function translateValue(type, value, callback) {
    $.get('../dashboard_admin/helper_functions.php', { translate: type, value: value }, callback);
}

document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('yourPhone');

    phoneInput.addEventListener('input', restrictPhoneInput);
    phoneInput.addEventListener('blur', sanitizePhoneInput);

    function restrictPhoneInput(e) {
      e.target.value = e.target.value.replace(/[^0-9]/g, '');
    }

    function sanitizePhoneInput(e) {
      e.target.value = e.target.value.replace(/[^0-9]/g, '');
    }
  });
</script>

</body>
</html>
