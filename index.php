<?php
session_start();
include 'config/config.php';

$sql = "SELECT 
            p.property_id, 
            p.number, 
            p.details, 
            (
                SELECT photo_path 
                FROM property_photos 
                WHERE property_id = p.property_id 
                LIMIT 1
            ) AS photo_path
        FROM 
            property p
        WHERE 
            p.status = 'a' -- Only select properties with status 'a'
        ORDER BY 
            p.property_id ASC 
        LIMIT 3";

$result = $mysqli->query($sql);

$properties = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Modify photo_path to include only the filename
        $row['photo_path'] = basename($row['photo_path']) ?: 'unavailable.jpg'; // Set default image if photo_path is empty
        $properties[] = $row;
    }
} else {
    // No properties found, add a default entry
    $properties[] = [
        'property_id' => 0,
        'number' => 'N/A',
        'details' => 'No available properties at the moment.',
        'photo_path' => 'unavailable.jpg'
    ];
}
$mysqli->close();

function truncateText($text, $limit = 35)
{
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        $text = implode(' ', array_slice($words, 0, $limit)) . '...';
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMExpert</title>
    <!-- Tab Icon -->
    <link rel="icon" href="public/img/logo.png">

    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="public/css/landing_page.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
    <style>
        .carousel-indicators {
            display: none;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <?php include 'app/Views/landing_page/header.php'; ?>

    <!-- Main  -->
    <main>
        <section id="home" class="home">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>Apartments available for Rent.</h2>
                    </div>
                </div>
            </div>
        </section>
        <section id="for_rent" class="for_rent section-title">

            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($properties as $index => $property) : ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <a href="app/Views/for_rent/property_page.php?property_id=<?php echo $property['property_id']; ?>">
                                            <img src="public/img/uploads/<?php echo $property['photo_path']; ?>" class="d-block w-100" alt="Building <?php echo $property['number']; ?>">
                                            <div class="carousel-caption">
                                                <h5>Apartment <?php echo $property['number']; ?></h5>
                                                <p><?php echo truncateText($property['details']); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>



        </section>
        <section id="about" class="about">
        </section>
        <section id="contact" class="contact">
        </section>
    </main>

    <?php include 'app/Views/landing_page/footer.php'; ?>
    <!-- Include the Modal Login Form -->
    <?php include 'app/Views/landing_page/login_modal.php'; ?>

    <!-- Include Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- <script src="modal-script.js"></script> -->

</body>

</html>