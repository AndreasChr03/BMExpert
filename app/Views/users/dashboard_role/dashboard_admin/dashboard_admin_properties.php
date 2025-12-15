<?php
session_start();

include_once '../../../../../config/config.php';
include_once 'helper_functions.php';

// Initialize session messages
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = array();
}
if (!isset($_SESSION['success'])) {
    $_SESSION['success'] = '';
}

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 1 )) {
    header("location: ../../../../../index.php");
    exit;
}

$role_id = $_SESSION["role_id"];

// Fetch minimal data for initial table display
$query = "SELECT
    p.property_id,
    p.floor,
    p.number,
    p.status,
    t.name AS tenant_name,
    t.surname AS tenant_surname,
    t.phone_1 AS tenant_phone,
    t.email AS tenant_email
FROM
    property p
    LEFT JOIN users t ON p.tenant_id = t.user_id";

$result = $mysqli->query($query);
$rowNumber = 1; // Initialize row number before the loop

// Function to fetch detailed data for modal dialog
function fetchDetails($propertyId)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT
        p.property_id,
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
    WHERE p.id = ?");
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

mysqli_close($mysqli);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties</title>
    <link rel="icon" href="../../../../../public/img/logo.png">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:wght@300&display=swap" rel="stylesheet">

</head>

<body>
  <?php include_once '../dashboard_modules_header.php'; ?>

    <div class="main">
        <div class="user-header d-flex justify-content-between align-items-center">
            <h2 style="margin-left: 20px;">Properties</h2>
        </div>
        <!-- Properties Table Display -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover " id="propertiesTable">
                <thead>
                    <tr>
                        <th>A/A</th>
                        <th>Floor</th>
                        <th>Number</th>
                        <th>Status</th>
                        <th>Tenant Name</th>
                        <th>Tenant Surname</th>
                        <th>Tenant Phone</th>
                        <th>Tenant Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($rowNumber++) ?></td>
                                <td><?= htmlspecialchars($row['floor'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['number'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(translateStatus($row['status'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars($row['tenant_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['tenant_surname'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['tenant_phone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['tenant_email'] ?? '-') ?></td>
                                <td>
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#propertyDetailsModal" onclick="loadPropertyDetails(<?= htmlspecialchars($row['property_id']) ?>)">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </td>
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
        </main>
        <!-- Property Details Modal -->
        <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
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

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
        <script src="../../../../../public/js/dashboard_admin_users.js?v=1.1"></script>

</body>

</html>