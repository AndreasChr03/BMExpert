<?php
session_start();

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 2 )) {
    header("location: ../../../../../index.php");
    exit;
}
include_once '../../../../../config/config.php';
include_once '../../../../../app/Controllers/property_controller.php';
$loginError = '';
if ($_SESSION["loggedin"] == false) {
    header("Location: index.php");
    exit();
}
$role_id = $_SESSION["role_id"];



?>
<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <link rel="stylesheet" href="../../../../../public/css/dashboard.css">
    <link href="../../../../../public/css/dashboard_owner_properties.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<body>
    <!-- Header -->
    <?php include __DIR__ . '/../dashboard_modules_header.php'; ?>

    <!-- Displaying Error Messages -->
    <?php if (!empty($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class='alert alert-danger'><strong><?= $error ?></strong></div>
        <?php endforeach;
        unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <!-- Displaying Success Messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class='alert alert-success'><strong><?= $_SESSION['success']; ?></strong></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <!-- Headder that displays available or not properties and a button to add a property-->
    <div class="main">
        <div class="user-header d-flex justify-content-between align-items-center">
            <?php if (!$property_available) {
                echo "<div class='container mt-5'style='margin-left:0px;'>";
                echo "<h1>Not available properties</h1>";
                echo "<h3>Please add a property</h3>";
                echo "</div>";
            } else {
                echo "<div class='container mt-5' style='margin-left:0px;'>";
                echo "<h1 style='margin-left: 14px;'>Available properties</h1>";
                echo "</div>";

            } ?>
            <button style="background-color: #086972; margin-right: 20px;" class="btn btn-success"
                data-bs-toggle="modal" data-bs-target="#propertyDetailsModal"
                onclick="loadPropertyDetails(<?= htmlspecialchars($row['property_id']) ?>)">
                <i style='margin-right:10px;' class="fas fa-plus"></i>Add Property
            </button>
        </div>
    </div>



    <!-- Property Cards Container -->
    <div class="container mt-5" style="padding-bottom: 20px; font-size: 11px;">
        <div class="row" id="propertyCardsContainer"></div>
        <div class="row mt-4">
            <div id="paginationControls" class="col-12"></div>
        </div>
    </div>

    <!-- Main  -->


    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyDetailsModalLabel">Property Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Dynamic content will be loaded here by JavaScript -->

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts gia to Modal Window -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../../../../public/js/dashboard_owner_properties.js?v=1.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
 
    
    <script>

        // Call the function to load the form when the modal is shown
        $('#propertyDetailsModal').on('shown.bs.modal', function () {
            loadPropertyForm(); // Assuming loadPropertyForm() is the function that loads the form content
        });

        // Function to load the form into the modal
        function loadPropertyForm() {
            $.ajax({
                url: "../../../../Controllers/addPropertyForm.php", // Path to your PHP file that generates the form
                type: "GET",
                success: function (response) {
                    $('#modalContent').html(response);
                },
                error: function () {
                    $('#modalContent').html('<p>Error loading form. Please try again.</p>');
                }
            });
        }
        // Submit form via AJAX
        $(document).on('submit', '#propertyForm', function (e) {
            e.preventDefault();
            $.ajax({
                url: "../../../Models/addProperties.php", // Path to your PHP file that handles form submission
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    // Handle success response if needed
                    console.log("Form submitted successfully.");
                    // Close the modal if needed
                    $('#propertyDetailsModal').modal('hide');
                },
                error: function () {
                    // Handle error response if needed
                    console.log("Error submitting form.");
                }
            });
        });
        //Javascript gia fetcingProperties
        $(document).ready(function () {
            var currentPage = 1; // Default to first page
            fetchProperties(currentPage);

            function fetchProperties(page) {
                currentPage = page; // Update current page
                $.ajax({
                    url: '../../../../Models/fetchOwnerProperties.php',
                    type: 'GET',
                    data: { page: page, limit: 4 },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Fetched data:", response);
                        var propertyCardsContainer = $('#propertyCardsContainer');
                        propertyCardsContainer.empty();
                        if (response.data && response.data.length) {
                            response.data.forEach(function (property) {
                                translateStatus(property.status, function (translatedStatus) {
                                    var statusColor = translatedStatus === 'Available' ? 'bg-success' : 'bg-danger';
                                    var BASE_URL = '<?php echo BASE_URL; ?>';
                                    var imagePath = BASE_URL + 'public/img/uploads/' + (property.photo_path);
                                    var propertyCard = $('<div class="col-sm-12 col-md-6 mb-4">').append(
                                        $('<a>', {
                                            href: `../../../../Controllers/fetchOwnerEachProperty.php?property_id=${property.property_id}`,
                                            class: 'text-decoration-none',
                                            'data-property-id': property.property_id, // Storing property ID
                                            click: function () { // Adding a click event handler directly
                                                var propertyId = $(this).data('property-id');
                                                window.location.href = `../../../../Controllers/fetchOwnerEachProperty.php?property_id=${propertyId}`;
                                            }
                                        }).append(
                                            $('<div class="card">').append(
                                                $('<img>', {
                                                    class: "card-img-top img-fluid",
                                                    src: imagePath,
                                                    alt: "Property Image",
                                                    style: "height: 400px; object-fit: cover;"
                                                }),
                                                $('<div class="card-body" style="background-color: ' + statusColor + ';">').append(
                                                    $('<h5 class="card-title">').text("Building " + property.building_id + " - Unit " + property.number),
                                                    $('<p class="card-text">').text(property.details)
                                                ),
                                                $('<div class="card-footer bg-transparent border-top-0">').append(
                                                    $('<small class="text-muted">').text(property.area + "  m²")
                                                )
                                            )
                                        )
                                    );
                                    propertyCardsContainer.append(propertyCard);
                                });
                            });
                            createPagination(response.totalPages, currentPage);
                        } else {
                            propertyCardsContainer.append("<h2>Please add a property.</h2>");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching properties:", status, error);
                        propertyCardsContainer.append("<p>Error fetching properties. Please try again.</p>");
                    }
                });
            }

            function createPagination(totalPages, currentPage) {
                let paginationControls = $('#paginationControls');
                paginationControls.empty(); // Clear existing pagination buttons

                let flexContainer = $('<div class="d-flex justify-content-center"></div>');
                let paginationUl = $('<ul class="pagination pagination-lg"></ul>');

                // Previous button
                let prevLi = $('<li class="page-item ' + (currentPage > 1 ? '' : 'disabled') + '"></li>');
                let prevLink = $('<a class="page-link" href="#">Previous</a>').on('click', function (e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        fetchProperties(currentPage - 1);
                    }
                });
                prevLi.append(prevLink);
                paginationUl.append(prevLi);

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    let pageLi = $('<li class="page-item ' + (i === currentPage ? 'active' : '') + '"></li>');
                    let pageLink = $('<a class="page-link" href="#">' + i + '</a>').on('click', function (e) {
                        e.preventDefault();
                        fetchProperties(i);
                    });
                    pageLi.append(pageLink);
                    paginationUl.append(pageLi);
                }

                // Next button
                let nextLi = $('<li class="page-item ' + (currentPage < totalPages ? '' : 'disabled') + '"></li>');
                let nextLink = $('<a class="page-link" href="#">Next</a>').on('click', function (e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        fetchProperties(currentPage + 1);
                    }
                });
                nextLi.append(nextLink);
                paginationUl.append(nextLi);

                flexContainer.append(paginationUl);
                paginationControls.append(flexContainer);
            }
        });

        function translateStatus(status, callback) {
            $.get('../dashboard_admin/helper_functions.php', { translate: 'status', value: status }, function (response) {
                callback(response.trim());
            });
        }

        
    </script>



</body>
<?php
mysqli_close($mysqli);
?>

</html>