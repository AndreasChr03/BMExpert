<?php
session_start();
include_once '../../../../../config/config.php';

// Check permissions
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"] !== 1)) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the file name from the request
$file = $_POST['file'] ?? null;
if ($file) {
    $file = basename($file); // Prevent directory traversal
    $backupFilePath = './backUp/' . $file;

    if (file_exists($backupFilePath)) {
        if (unlink($backupFilePath)) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Backup file deleted successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to delete backup file']);
        }
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
