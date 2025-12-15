<?php
session_start();
include_once __DIR__ . '/../../config/config.php';
// Assuming you have already connected to the database and retrieved the form data

$loginError = '';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = $_POST['requestId'];
    $tenantFullName = $_POST['tenantFullName'];
    $propertyNum = $_POST['propertyNum'];
    $tenantSubject = $_POST['tenantSubject'];
    $tenantMessage = $_POST['tenantMessage'];
    //Reply date is the current date
    $replyDate = date("Y-m-d");


// Update the request with request id = $requestId with the reply and set the status to 'f'
$sql = "UPDATE request SET replyTitle = ?, replyDetails = ?, status = 'f', finish_date = ? WHERE request_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssi", $tenantSubject, $tenantMessage, $replyDate, $requestId);


    if ($stmt->execute()) {
        $_SESSION['success'] = "Reply sent successfully.";
        http_response_code(200); // Success
        $stmt->close();
        exit(json_encode(["message" => "Request updated successfully."]));
    } else {
        http_response_code(500); // Internal Server Error
        $_SESSION['error'] = "Error inserting  details: " . $stmt->error;
        $stmt->close();
        exit(json_encode(["error" => "Error updating request: " . $stmt->error]));
    }
}
?>
