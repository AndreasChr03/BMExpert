<?php

session_start();
include "../../../../../config/config.php";

$email = $_SESSION['email'];

$currentYear = $_POST['year'] ?? date('Y'); // Use POST to get the year or default to the current year

$monthlyData = array_fill(0, 12, 0); // Initialize an array with 0 for each month
$labels = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'); // Month labels

// Check the user's role
$roleCheckStmt = $mysqli->prepare("SELECT role_id FROM users WHERE email = ?");
$roleCheckStmt->bind_param("s", $email);
$roleCheckStmt->execute();
$roleResult = $roleCheckStmt->get_result();
$roleRow = $roleResult->fetch_assoc();
$roleCheckStmt->close();

if ($roleRow['role_id'] == 1) {
    // User is admin
    // SQL query to fetch sum of cost for each month in the selected year where building_id = 1
    $query = "SELECT MONTH(b.CREATED) as month, SUM(cost) as sum FROM bills b
              WHERE YEAR(b.CREATED) = ? AND b.building_id = 1
              GROUP BY MONTH(b.CREATED)";

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => "Prepare failed: " . $mysqli->error]);
        $mysqli->close();
        exit;
    }

    $stmt->bind_param("i", $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $monthlyData[$row['month'] - 1] = (int)$row['sum']; // Update the corresponding month with the sum
    }
} else {
    // User is not admin, perhaps do something else or nothing
    echo json_encode(['error' => "Unauthorized access"]);
    exit;
}

$stmt->close();
$mysqli->close();

echo json_encode(['data' => $monthlyData, 'labels' => $labels]);
?>
