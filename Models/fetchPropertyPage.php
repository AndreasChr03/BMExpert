<?php

include "../../config/config.php";
header('Content-Type: application/json');

// Check if there is a database connection error
if ($mysqli->connect_error) {
    error_log("Connection failed: " . $mysqli->connect_error);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// Validate the property_id input
if (isset($_GET['property_id']) && ctype_digit($_GET['property_id'])) {
    $propertyID = intval($_GET['property_id']);

    $sql = "SELECT p.*, GROUP_CONCAT(pp.photo_path SEPARATOR ', ') AS photos
            FROM property p
            LEFT JOIN property_photos pp ON p.property_id = pp.property_id
            WHERE p.property_id = ?
            GROUP BY p.property_id;";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $propertyID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $propertyDetails = $result->fetch_assoc();
            $propertyDetails['photos'] = explode(', ', $propertyDetails['photos'] ?? '');
            echo json_encode($propertyDetails);
        } else {
            echo json_encode(['error' => 'Property not found.']);
        }

        $stmt->close();
    } else {
        error_log("SQL Error: " . $mysqli->error);
        echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
    }
} else {
    echo json_encode(['error' => 'Invalid property ID.']);
}

$mysqli->close();

?>