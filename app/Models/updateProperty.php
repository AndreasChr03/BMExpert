<?php

session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/config.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['email'])) {
    $propertyId = $_POST['property_id'] ?? null;
    if (!$propertyId) {
        $response['message'] = 'Property ID is missing.';
        echo json_encode($response);
        exit;
    }

    $floor = $_POST['floor'] ?? '';
    $number = $_POST['number'] ?? '';
    $status = $_POST['status'] ?? '';
    $area = $_POST['area'] ?? '';
    $rooms = $_POST['rooms'] ?? '';
    $bathrooms = $_POST['bathrooms'] ?? '';
    $furnished = $_POST['furnished'] ?? '';
    $pet = $_POST['pet'] ?? '';
    $parking = $_POST['parking'] ?? '';
    $details = $_POST['details'] ?? '';
    $comment = $_POST['comment'] ?? '';

    // Perform validation here if needed

    $mysqli->begin_transaction();
    try {
        // Update property details
        $query = "UPDATE property SET floor=?, number=?, status=?, pet=?, furnished=?, rooms=?, bathrooms=?, parking=?, area=?, details=?, comment=? WHERE property_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iisssiisissi", $floor, $number, $status, $pet, $furnished, $rooms, $bathrooms, $parking, $area, $details, $comment, $propertyId);
       
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $mysqli->commit();
            $response = ['status' => 'success', 'message' => 'Property details updated successfully.'];
        } else {
            $mysqli->rollback();
            throw new Exception("No changes were made to the property details.");
        }
    } catch (Exception $e) {
        $mysqli->rollback();
        $response['message'] = 'Failed to update property details. ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request or not logged in.';
}
echo json_encode($response);
$mysqli->close();

?>
