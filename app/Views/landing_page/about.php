<?php
include '../../../config/config.php'; // Ensure your DB connection settings are correct
session_start();

// Initialize your building information array
$building = [
  'name' => '',
  'num_floors' => '',
  'num_properties' => '',
  'comment' => '',
  'address' => '',
  'postal_code' => '',
  'city' => '',
  'county' => ''
];

// Specify the building ID for which to fetch data
$buildingId = 1; // Example ID

// Prepare and execute the SQL query
$stmt = $mysqli->prepare("SELECT name, address, postal_code, city, county, num_floors, num_properties, comment FROM building WHERE building_id = ?");
$stmt->bind_param("i", $buildingId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Split the comment into lines and wrap each line in a `<p>` tag
  $lines = explode("\n", $row['comment']);
  $formattedComment = "";
  foreach ($lines as $line) {
    $formattedComment .= '<p class="minimal-spacing">' . htmlspecialchars($line) . '</p>';
  }

  // Populate the building array with fetched and formatted data
  $building = [
    'name' => $row['name'],
    'num_floors' => $row['num_floors'],
    'num_properties' => $row['num_properties'],
    'comment' => $formattedComment,
    'address' => $row['address'],
    'postal_code' => $row['postal_code'],
    'city' => $row['city'],
    'county' => $row['county']
  ];
}

// Assume connection to the database is established and relevant data is fetched
$address = $row['address'];
$city = $row['city'];
$postal_code = $row['postal_code'];
$full_address = $address . ', ' . $city . ', ' . $postal_code;
$encoded_address = urlencode($full_address);


// Ετοιμάστε και εκτελέστε την ερώτηση SQL
$stmt = $mysqli->prepare("SELECT building_photo FROM building WHERE building_id = ?");
$stmt->bind_param("i", $buildingId);
$stmt->execute();
$result = $stmt->get_result();
$imagePath = ''; // Default empty
if ($row = $result->fetch_assoc()) {
    $imagePath = $row['building_photo'] ?? '';  // Retrieve the path from the database or set as empty if not present
}

// Correctly form the full image path by ensuring there are no double slashes or missing slashes
$fullImagePath = BASE_URL . 'public/img/uploads/' . basename($imagePath);

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$stmt->close();
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BMExpert</title>
  <!-- Tab Icon -->
  <link rel="icon" href="../../../public/img/logo.png">

  <!-- CSS Files -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../../public/css/landing_page.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
  <style>
    .br-minimal {
      margin: 0 !important;
      line-height: 0.4 !important;
      /* This forces the style to take priority */
    }
  </style>

</head>

<body>

  <?php include 'header.php'; ?>
  <!-- Main  -->
  <main>
    <section id="about" class="about">
    <div class="image_container">
    <?php if (!empty($imagePath)): ?>
        <img src="<?= htmlspecialchars($fullImagePath) ?>" class="building_img" alt="The apartment building">
    <?php else: ?>
        <p>Image not available.</p>
    <?php endif; ?>
    <div class="text-container">
        <h2>Fifty-One Washington</h2>
    </div>
</div>

      </div>

      <div class="container">
        <div class="row gy-4 align-items-top">
          <div class="col-lg-6">
            <h3><?= htmlspecialchars($building['name']) ?></h3>
            <p>Number of Floors: <?= htmlspecialchars($building['num_floors']) ?></p>
            <p>Number of Units: <?= htmlspecialchars($building['num_properties']) ?></p>
            <?= $building['comment'] ?> <!-- Display the formatted comment -->
            <h4>Address</h4>
            <p><?= htmlspecialchars($building['address']) ?></p>
            <p><?= htmlspecialchars($building['city']) ?>, <?= htmlspecialchars($building['postal_code']) ?></p>
            <p><?= htmlspecialchars($building['county']) ?></p>
          </div>

          <div class="col-lg-6 order-2 order-lg-2 d-flex align-items-center">
            <iframe src="https://www.google.com/maps?q=<?= $encoded_address ?>&output=embed" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>
  <?php include 'login_modal.php'; ?>

  <!-- Include Bootstrap JS for modal functionality -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <!-- <script src="modal-script.js"></script> -->


</body>

</html>
