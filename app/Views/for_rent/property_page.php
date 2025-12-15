<?php
session_start();
include '../landing_page/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>BMExpert</title>
    <link rel="icon" href="../../../public/img/logo.png">
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Main style sheet -->
    <link href="css/style.css" rel="stylesheet"> 
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>

<body>  
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
      .navbar-expand-md .navbar-nav .nav-link {
          padding-right: 8px;
          padding-left: 8px;
      }
      .property-container {
        display: flex;
        width: 100%; /* Make the container take full width */
        min-height: 100vh; /* Minimum height of 100% of the viewport height */
        margin: 0 auto; /* Remove horizontal margin to use full width */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background: #f9f9f9; /* Optional: Adds a light background to the entire container */
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
    .navbar-nav {
        font-size: 16px; font-family: Open Sans, sans-serif;
    }
    #yourPhone::-webkit-outer-spin-button,
    #yourPhone::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    #yourPhone[type=number] {
        -moz-appearance: textfield;
    }
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
}
</style>

<!-- Property Details Page -->
<div id="property-details" class="property-container" style="padding-top: 50px; font-size: 18px;">
    <div class="property-images">
        <div id="image-carousel" class="carousel slide" data-bs-ride="carousel">
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
        <form id="contactForm" class="needs-validation" action="../../Models/storeGuest.php" method="post" novalidate>
            <!-- Hidden field for property_id -->
            <input type="hidden" id="propertyId" name="propertyId" value="">
            <div class="row g-3">
                <!-- First Name Field -->
                <div class="col-md-6">
                    <label for="yourName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="yourName" name="yourName" required>
                    <div class="invalid-feedback">
                        Please enter your first name.
                    </div>
                </div>
                <!-- Last Name Field -->
                <div class="col-md-6">
                    <label for="yourSurname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="yourSurname" name="yourSurname" required>
                    <div class="invalid-feedback">
                        Please enter your last name.
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label for="yourPhone" class="form-label mt-3">Phone Number</label>
                <input type="number" class="form-control" id="yourPhone" name="yourPhone" required>
                <div class="invalid-feedback">
                    Please enter a valid phone number.
                </div>
            </div>
            <div class="mb-3">
                <label for="yourEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="yourEmail" name="yourEmail" required>
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
            </div>
            <div class="mb-3">
                <label for="yourMessage" class="form-label">Message</label>
                <textarea class="form-control" id="yourMessage" name="yourMessage" rows="3" required></textarea>
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

<script>
$(document).ready(function () {
    $('#contactForm').submit(function (event) {
        event.preventDefault(); // Always prevent the default submit to handle with AJAX

        // Check form validity
        if (!this.checkValidity()) {
            event.stopPropagation(); // Stop the form from being submitted
            $(this).addClass('was-validated'); // Add validation class to show feedback
            return; // Stop the function here if the form is invalid
        }

        var formData = $(this).serialize(); // Serialize the form data

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: formData,
            success: function (response) {
                if (response.message === 'Message has been sent') {
                    $('#successAlert').show(); // Show success alert
                    $('#errorAlert').hide(); // Hide error alert
                    $('#contactForm')[0].reset(); // Reset the form fields
                    $('#contactForm').removeClass('was-validated'); // Reset validation state
                    setTimeout(function() {
                        $('#contactOwnerModal').modal('hide'); // Use Bootstrap's method to hide the modal
                        $('body').removeClass('modal-open'); // Remove the class that prevents scrolling
                        $('.modal-backdrop').remove(); // Manually remove the backdrop
                    }, 3000);
                } else {
                    $('#errorAlert').text(response.message).show().delay(3000).fadeOut(); // Show and hide error alert
                    $('#successAlert').hide(); // Hide success alert
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                $('#errorAlert').text('Error sending message. Check your network connection and try again. Details: ' + xhr.responseText).show();
            }
        });
    });

    // Clear form validation state when the modal is hidden
    $('#contactOwnerModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open'); // Ensure scrolling is re-enabled
        $('.modal-backdrop').remove(); // Ensure the backdrop is removed
        $('#contactForm').trigger("reset");
        $('#contactForm').removeClass('was-validated');
        $('.alert').hide(); // Ensure alerts are hidden
    });
});
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
        url: '../../Models/fetchPropertyPage.php',
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
                        <p>Area: ${property.area} m<sup>2</sup></p>
                        <p>Pets Allowed: ${translatedPet}</p>
                        <p>Furnished: ${property.furnished ? 'Yes' : 'No'}</p>
                        <p>Parking: ${translatedParking}</p>
                        <p>Description: ${property.details}</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactOwnerModal">Contact Owner</button>
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
    $.get('../users/dashboard_role/dashboard_admin/helper_functions.php', { translate: type, value: value }, callback);
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

<?php include '../landing_page/footer.php'; ?>