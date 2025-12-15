<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>BMExpert</title>
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
  </head>

<body class="aa-price-range">  
    <style>
      body {
        font-family: "Open Sans", sans-serif !important;
        font-size: 18px
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
    a { text-decoration: none; }
  </style>

  <!-- Header -->
  <header class="header">
    <nav class="navbar navbar-expand-md navbar-light bg-light navmenu">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a href="../../../../../index.php" class="logo d-flex align-items-center">
                <img src="../../../../../public/img/logo.png" width="50" height="50" class="d-inline-block align-top"
                    alt="Logo of BMExpert">
                <h1 class="navbar-text" style="font-weight: 600; font-size: 24px">BMExpert</h1>
            </a>

            <!-- Navbar Hamburger -->
            <div class="d-flex align-items-center">
                <button style="order: 3;" class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="d-md-none" style="order: 2; margin: 5px;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                </div>
            </div>

            <!-- Navbar Desktop -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav" style="font-size: 16px; font-family: Open Sans, sans-serif;">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../../../../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php#for_rent">For Rent</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div class="d-none d-md-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
            </div>
        </div>
    </nav>
  </header>
  <!-- End menu section -->

<!-- Property Details Page -->
<div id="property-details" class="property-container" style="padding-top: 50px;">
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
        <form id="contactForm" class="needs-validation" novalidate action="../../../../Models/sendEmail.php" method="post">
            <div class="mb-3">
                <label for="yourName" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="yourName" name="yourName" required>
                <div class="invalid-feedback">
                    Please enter your name.
                </div>
            </div>
            <div class="mb-3">
                <label for="yourEmail" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="yourEmail" name="yourEmail" required>
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
            </div>
            <div class="mb-3">
                <label for="yourMessage" class="form-label">Your Message</label>
                <textarea class="form-control" id="yourMessage" name="yourMessage" rows="3" required></textarea>
                <div class="invalid-feedback">
                    Please enter a message.
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 flex-grow-1">Send Message</button>
        </form>
        </div>
      </div>
    </div>
  </div>

<!-- Footer -->
<footer id="footer" class="footer bg-light" style="padding-top: 50px;">
    <div class="container footer-top">

        <div class="row gy-3">
            <div class="col-lg-6 col-md-12 footer-about">
            <a href="#" class="logo d-flex align-items-center" style="text-decoration: none;">
                <img src="../../../../../public/img/BlackLogoTransparent.png" width="100" class="d-inline-block align-top" alt="Logo of BMExpert">
                <span class="footer-logo" style="margin-left: 10px; font-weight: 600; font-size: 24px;">BMExpert</span>
            </a>
                <p>The Leading Edge in Property Management. Our innovative software empowers owners, managers, and
                    tenants to streamline processes and elevate the rental experience.</p>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 footer-links">
                <h4>Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Terms of Use</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 footer-address ">
                <h4>Address</h4>
                <p>Christou Kkeli 55, Emba</p>
                <p>Paphos, 8250</p>
                <p>Cyprus</p>
            </div>
        </div>
    </div>
  </footer>

<script>
$(document).ready(function () {
    $('#contactForm').submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: formData,
            success: function (response) {
                if (response.indexOf('Message has been sent') !== -1) {
                    $('#successAlert').show(); // Show success alert
                    $('#errorAlert').hide(); // Ensure error alert is hidden
                    $('#contactForm')[0].reset(); // Reset the form fields
                    $('.was-validated').removeClass('was-validated'); // Remove validation states

                    setTimeout(function() {
                        $('#contactOwnerModal').modal('hide'); // Hide the modal after 3 seconds
                        $('#successAlert').hide(); // Also hide the success alert when closing the modal
                    }, 3000); // Delay of 5000 milliseconds (3 seconds)

                } else {
                    $('#errorAlert').show().delay(3000).fadeOut(); // Show error alert, then hide after 3 seconds
                    $('#successAlert').hide(); // Ensure success alert is hidden
                }
            },
            error: function () {
                $('#errorAlert').text('Error sending message. Please check your network connection and try again.')
                               .show().delay(3000).fadeOut();
                $('#successAlert').hide(); // Ensure success alert is hidden
            }
        });
    });
});
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
    // Extract the property ID from URL parameters
    let urlParams = new URLSearchParams(window.location.search);
    let propertyId = urlParams.get('property_id');

    if (!propertyId) {
        console.error("No property ID provided.");
        alert("No property ID specified.");
        return;
    }

    // Function to fetch and display property details
    function fetchPropertyDetails() {
        $.ajax({
            url: '../../../../Models/fetchPropertyPage.php', // Adjust path as necessary
            type: 'GET',
            data: { property_id: propertyId },
            dataType: 'json',
            success: function (property) {
                if (property.error) {
                    console.error("Failed to fetch property: ", property.error);
                    alert("Failed to fetch property details.");
                    return;
                }

                populatePropertyDetails(property);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching property details: ", error);
                alert("Error loading property details. Please try again.");
            }
        });
    }

    // Populate property details into the HTML
    function populatePropertyDetails(property) {
        let carouselInner = $('#image-carousel .carousel-inner');
        carouselInner.empty(); // Clear existing carousel items

        // Populate the carousel with property images
        property.photos.forEach((photo, index) => {
            let itemClass = index === 0 ? 'carousel-item active' : 'carousel-item';
            let itemHTML = `<div class="${itemClass}">
                                <img src="${photo}" class="d-block w-100" alt="Property Image">
                            </div>`;
            carouselInner.append(itemHTML);
        });

        // Update property information text
        let infoHTML = `
            <h1>Building ${property.building_id} - Unit ${property.number}</h1>
            <p>Rooms: ${property.rooms}</p>
            <p>Bathrooms: ${property.bathrooms}</p>
            <p>Floor: ${property.floor}</p>
            <p>Area: ${property.area} sqft</p>
            <p>Pets Allowed: ${property.pet ? 'Yes' : 'No'}</p>
            <p>Furnished: ${property.furnished ? 'Yes' : 'No'}</p>
            <p>Parking: ${property.parking}</p>
            <p>Description: ${property.details}</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactOwnerModal">Contact Owner</button>
        `;
        $('.property-info').html(infoHTML); // Display the property information
    }

    // Initial fetch of property details
    fetchPropertyDetails();
});

</script>

</body>
</html>