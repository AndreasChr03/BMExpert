<?php
session_start();
include_once __DIR__ . '/../../config/config.php';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 2 )) {
    header("location: ../../index.php");
    exit;
}
$property_id = $_GET['property_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMExpert</title>
    <link rel="icon" href="../../public/img/logo.png">
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Main style sheet -->
    <link href="../../public/css/dashboard_owner_properties.css" rel="stylesheet">
    <!-- Header and Footer CSS -->
    <link href="../../public/css/landing_page.css" rel="stylesheet">
    <link href="../../public/css/dashboard.css" rel="stylesheet">
    <!-- CSS Files -->
       <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<?php include __DIR__ . '/../Views/users/dashboard_role/dashboard_modules_header.php'; 

?>


<body class="aa-price-range">
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
            width: 100%;
            /* Make the container take full width */
            min-height: 100vh;
            /* Minimum height of 100% of the viewport height */
            margin: 0 auto;
            /* Remove horizontal margin to use full width */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background: #f9f9f9;
            /* Optional: Adds a light background to the entire container */
            padding-left: 20px;
        }

        .property-images {
            flex: 1 1 50%;
            /* Takes up half of the flex container */
            position: relative;
        }

        .carousel {
            width: 100%;
            height: 100vh;
            /* Make the carousel take full viewport height */
            overflow: hidden;
            position: relative;
        }

        .carousel img {
            width: 100%;
            height: auto;
            display: none;
            /* Initially hide all images */
            object-fit: cover;
            /* Ensures images cover the area without distortion */
        }

        .carousel-control-prev,
        .carousel-control-next {
            top: 35%;
            /* Position halfway down */
            transform: translateY(-50%);
            /* Offset the position by half the height of the element */
        }

        .property-info {
            flex: 1 1 50%;
            /* Takes up the other half of the flex container */
            border-radius: 0 8px 8px 0;
            /* Rounded corners on the right side */
            margin-left: 30px;
            padding-top: 0;
        }

        .property-info h1,
        .property-info p {
            margin: 0 0 10px 0;
        }

        a {
            text-decoration: none;
        }

        .navbar-nav {
            font-size: 16px;
            font-family: Open Sans, sans-serif;
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
                flex-direction: column;
                /* Stack the child elements vertically */
            }

            .property-images {
                flex: 0 0 40vh;
                /* Set to your preferred height */
                position: relative;
                /* Ensures proper positioning context for arrows */
            }

            .property-info {
                transform: translateY(-400px);
                /* Move the property info below the carousel */
            }

            .carousel img {
                width: 100%;
                height: 40vh;
                /* Adjusted to your specified perfect height */
                object-fit: cover;
                object-position: center;
                /* Ensures the image is centered within its element */
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
    <!-- Property Details Page -->
    <div id="property-details" class="property-container"
        style="background-color:white; padding-top: 50px; font-size: 18px;">
        <div class="property-images">
            <div id="image-carousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Inner -->
                <div class="carousel-inner" role="listbox"></div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#image-carousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#image-carousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="property-info">
            <!-- This is where the property information will be displayed -->
        </div>
    </div>

  
    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyDetailsModalLabel">Property Details</h5>
                    <button type="button" class="btn-close" id="btn-close-reload" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Dynamic content will be loaded here by JavaScript -->
                    <?php
                    // Check if $propertyId is set and not empty
                    if (isset($property_id) && !empty($property_id)) {
                        echo '<button type="button" class="btn btn-primary" onclick="loadPropertyDetails(' . htmlspecialchars($property_id) . ')" data-bs-toggle="modal" data-bs-target="#propertyDetailsModal"><i class="bi bi-pencil-square"></i>Edit Property</button>';
                    } else {
                        echo '<p>No property selected.</p>';
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
        
    <script>
    // Function to reload the page when the close button is pressed
    document.getElementById('btn-close-reload').addEventListener('click', function () {
        window.location.reload();
    });
</script>

    <script>


        //Function gia na kaneis load ta data gia edit tou property
        function loadPropertyDetails(propertyId) {
            $.ajax({
                url: "fetchPropertyDetails.php",  // Make sure the path is correct relative to where the JS file is included.
                type: "POST",
                data: { property_id: propertyId },
                success: function (response) {
                    $('#modalContent').html(response);  // Assuming the server sends back formatted HTML
                },
                error: function () {
                    $('#modalContent').html('<p>Error loading details. Please try again.</p>');
                }
            });
        }

        function displayStatus(status) {
    var statusSelect = $('#status'); // Assuming the select element has id="status"
    statusSelect.empty(); // Clear existing options
    if (status == 'a') {
        statusSelect.append('<option value="1" selected>Available</option>');
        statusSelect.append('<option value="0">Rented</option>');
    } else {
        statusSelect.append('<option value="1">Available</option>');
        statusSelect.append('<option value="0" selected>Rented</option>');
    }
}

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
            let urlParams = new URLSearchParams(window.location.search);
            let propertyId = urlParams.get('property_id');

            // AJAX call to fetch property details from the server
            $.ajax({
                url: '../Models/fetchPropertyPage.php',
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
                    translateValue('status', property.status, function (translatedStatus) {
                    translateValue('yesno', property.pet, function (translatedPet) {
                        translateValue('parking', property.parking, function (translatedParking) {
                        let propertyHTML = `
                        <h1>${title}</h1>
                        
                        <p>Status: ${translatedStatus}</p>
                        <p>Rooms: ${property.rooms}</p>
                        <p>Bathrooms: ${property.bathrooms}</p>
                        <p>Floor: ${property.floor}</p>
                        <p>Area: ${property.area}  m<sup>2</sup></p>
                        <p>Pets Allowed: ${translatedPet}</p>
                        <p>Furnished: ${property.furnished ? 'Yes' : 'No'}</p>
                        <p>Parking: ${translatedParking}</p>
                        <p>Description: ${property.details}</p>
                        <button type="button" class="btn btn-primary" onclick="loadPropertyDetails(<?php echo htmlspecialchars($property_id); ?>)" data-bs-toggle="modal" data-bs-target="#propertyDetailsModal"><i class="bi bi-pencil-square"></i>   Edit Property</button>`;
                            $('.property-info').html(propertyHTML); // Ensure property info is updated
                        });
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
            $.get('../Views/users/dashboard_role/dashboard_admin/helper_functions.php', { translate: type, value: value }, callback);
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