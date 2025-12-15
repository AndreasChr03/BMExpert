<?php
session_start();
include "../../../../../config/config.php"; // Adjust the path to your config file as needed

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize errors array
    $errors = [];
    $status = 'p';
    $date = date("Y-m-d H:i:s");
    // Extract form data
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $details = isset($_POST['details']) ? $_POST['details'] : '';
    $replyTitle = isset($_POST['replyTitle']) ? $_POST['replyTitle'] : 'No Title'; 

    // Sanitize the input to prevent SQL injection
    $property_id = isset($_GET['property_id']) ? filter_var($_GET['property_id'], FILTER_SANITIZE_NUMBER_INT) : null;

    // Validate message
    if (empty($details)) {
        $errors['details'] = "Message is required.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        // Prepare and bind
        $stmt = $mysqli->prepare("INSERT INTO request (property_id, subject, details, status, start_date, replyTitle) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $property_id, $subject, $details, $status, $date, $replyTitle); 

        // Execute the prepared statement
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Message has been sent";
            header("Location: dashboard_tenant_property.php?property_id=$property_id");
            exit;
        } else {
            $_SESSION['error_message'] = "Error inserting data into database";
            header("Location:dashboard_tenant_property.php?property_id=$property_id");
            exit;
        }

        // Close statement
        $stmt->close();
    } else {
        // Set error session message for validation errors
        $_SESSION['error_message'] = "Validation failed";
        $_SESSION['validation_errors'] = $errors;
        // Redirect back to the form page
        header("Location: form_page.php");
        exit;
    }

    // Close connection
    $mysqli->close();
} else {
    // Send error response for invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Method not allowed"]);
}
?>
