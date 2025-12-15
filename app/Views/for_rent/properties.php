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
    .card {
        margin-right: 0;
        margin-left: auto;
        cursor: pointer;
    }
    .col-md-6 {
        padding-right: 5px;
    }
    @media (min-width: 768px) {
      .card {
        margin: 8px;
      }
    }
    @media (max-width: 767px) {
        .card {
            margin-top: 50px;
            margin: 4px;
        }
    }
    .card {
        display: flex;
        flex-direction: column;
        height: 100%; /* Ensures all cards are full height of their flex container */
    }
    .card-body {
        flex: 1; /* Allows the card body to expand to fill available space */
        display: flex;
        flex-direction: column;
    }
    .card-title, .card-text {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limits text to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-text {
        flex-grow: 1;
        overflow: hidden;
        max-height: 4em;
    }
    .card-footer {
        margin-top: auto;
    }
    a { 
        text-decoration: none; 
    }
    .navbar-nav {
        font-size: 16px; font-family: Open Sans, sans-serif;
    }
    .pagination .page-link {
        padding: 12px 16px; /* Smaller padding than Bootstrap's default */
        font-size: 16px; /* Smaller font size */
        color: #000;
    }
    .pagination .page-link:hover, .pagination .page-link:focus, .pagination .page-item.active .page-link {
        color: #ffffff;
        background-color: #086972;
        border-color: #000000;
    }
    .card-text {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp: 3; /* starting point */
        max-height: 4.5em; /* Adjust based on line height and line-clamp */
        line-height: 1.5em;
    }

    /* Adjustments for smaller screens */
    @media (max-width: 767px) {
        .card-text {
            -webkit-line-clamp: 2;
            max-height: 3em; /* Less space for text on smaller devices */
        }
    }
  </style>
  
    <!-- Start Property Header -->
    <section id="aa-property-header" style="background-image: url('img/property.jpg'); background-position: center; position: relative;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-property-header-inner" style="position: relative; z-index: 10;">
                        <h2 style="font-size: 36px; font-weight: 700; color: white;">For Rent Page</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <!-- Property Cards Container -->
   <div class="container mt-5" style="padding-bottom: 20px; font-size: 11px;">
        <div class="row" id="propertyCardsContainer"></div>
        <div class="row mt-4">
            <div id="paginationControls" class="col-12"></div>
        </div>
    </div>

<script>
        $(document).ready(function() {
        var currentPage = 1; // Default to first page
        fetchProperties(currentPage);

        function fetchProperties(page) {
            currentPage = page; // Update current page
            $.ajax({
                url: '../../Models/fetchProperties.php',
                type: 'GET',
                data: { page: page, limit: 4 },
                dataType: 'json',
                success: function(response) {
                    console.log("Fetched data:", response);
                    var propertyCardsContainer = $('#propertyCardsContainer');
                    propertyCardsContainer.empty();
                    if (response.data && response.data.length) {
                        response.data.forEach(function(property) {
                            translateStatus(property.status, function(translatedStatus) {
                                var statusColor = translatedStatus === 'Available' ? 'bg-success' : 'bg-danger';
                                var BASE_URL = '<?php echo BASE_URL; ?>';
                                var imagePath = BASE_URL + 'public/img/uploads/' + (property.photo_path || BASE_URL + 'public/img/building.jpg');
                                var propertyCard = $('<div class="col-sm-12 col-md-6 mb-4">').append(
                                    $('<a>', {
                                        href: `property_page.php?property_id=${property.property_id}`,
                                        class: 'text-decoration-none',
                                        'data-property-id': property.property_id, // Storing property ID
                                        click: function() { // Adding a click event handler directly
                                            var propertyId = $(this).data('property-id');
                                            window.location.href = `property_page.php?property_id=${propertyId}`;
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
                                                $('<p class="card-text">').text(truncateWords(property.details, 35))
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
                        propertyCardsContainer.append("<p>No properties found.</p>");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching properties:", status, error);
                    propertyCardsContainer.append("<p>Error fetching properties. Please try again.</p>");
                }
            });
        }

    function truncateWords(text, numWords) {
        return text.split(' ').slice(0, numWords).join(' ') + (text.split(' ').length > numWords ? '...' : '');
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
        $.get('../users/dashboard_role/dashboard_admin/helper_functions.php', { translate: 'status', value: status }, function(response) {
            callback(response.trim());
        });
    }
</script>

</body>
</html>

<?php include '../landing_page/footer.php'; ?>