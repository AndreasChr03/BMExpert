<?php
session_start();
include "../../../../../config/config.php";

// Initialize variables
$month = $_POST['month'] ?? '01';  // Default to January if not specified
$email = $_SESSION['email'];
$currentYear = date('Y');
$yearsData = [];
$labels = [];

// Check the user's role
$roleCheckStmt = $mysqli->prepare("SELECT role_id FROM users WHERE email = ?");
$roleCheckStmt->bind_param("s", $email);
$roleCheckStmt->execute();
$roleResult = $roleCheckStmt->get_result();
$roleRow = $roleResult->fetch_assoc();
$roleCheckStmt->close();

if ($roleRow['role_id'] == 1) {
    // User is admin
    for ($i = 0; $i < 6; $i++) {
        $year = $currentYear - $i;
        // Adjusted SQL query for admin where building_id = 1
        $stmt = $mysqli->prepare("SELECT SUM(cost) as sum FROM bills 
        WHERE building_id = 1 AND MONTH(CREATED) = ? AND YEAR(CREATED) = ?");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $yearsData[] = $row['sum'];
        $labels[] = $year;
    }
} else {
    // User is not admin, do something else or nothing
}

$stmt->close();
$mysqli->close();

echo json_encode(['data' => $yearsData, 'labels' => $labels]);
?>
