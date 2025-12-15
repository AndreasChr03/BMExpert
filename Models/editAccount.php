<?php

session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $query = "SELECT name, surname, phone_1, phone_2, email FROM users WHERE email = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $userDetails = $result->fetch_assoc();
                echo json_encode(['status' => 'success', 'user' => $userDetails]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No user found with that email.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to execute query: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed: ' . $mysqli->error]);
    }
    exit();
}

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $newEmail = trim($_POST['email'] ?? $email);
    $phone1 = $_POST['phone_1'] ?? null; 
    $phone2 = $_POST['phone_2'] ?? null;
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $oldPassword = $_POST['oldPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $newPasswordRepeat = $_POST['newPasswordRepeat'] ?? '';

    $mysqli->begin_transaction();
    try {
        $changesMade = false;

        // Check for email uniqueness if changed
        if ($newEmail !== $email) {
            $checkEmailSql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
            $stmt = $mysqli->prepare($checkEmailSql);
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $mysqli->error);
            }
            $stmt->bind_param("s", $newEmail);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                throw new Exception("Email is already in use.");
            }
            $stmt->close();
        }

        // Check for phone_1 uniqueness if changed
        if ($phone1) {
            $checkPhone1Sql = "SELECT COUNT(*) as count FROM users WHERE phone_1 = ? AND email != ?";
            $stmt = $mysqli->prepare($checkPhone1Sql);
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $mysqli->error);
            }
            $stmt->bind_param("ss", $phone1, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                throw new Exception("Phone 1 is already in use.");
            }
            $stmt->close();
        }

        // Check for phone_2 uniqueness if changed
        if ($phone2) {
            $checkPhone2Sql = "SELECT COUNT(*) as count FROM users WHERE phone_2 = ? AND email != ?";
            $stmt = $mysqli->prepare($checkPhone2Sql);
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $mysqli->error);
            }
            $stmt->bind_param("ss", $phone2, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                throw new Exception("Phone 2 is already in use.");
            }
            $stmt->close();
        }

        // Update email, name, surname, and phones if provided
        $updateSql = "UPDATE users SET email = ?, name = ?, surname = ?, phone_1 = ?, phone_2 = ? WHERE email = ?";
        $stmt = $mysqli->prepare($updateSql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $mysqli->error);
        }
        $stmt->bind_param("ssssss", $newEmail, $name, $surname, $phone1, $phone2, $email);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $changesMade = true;
        }
        $stmt->close();

        // Update password if needed
        if (!empty($newPassword) && !empty($newPasswordRepeat) && !empty($oldPassword)) {
            if ($newPassword !== $newPasswordRepeat) {
                throw new Exception("New passwords do not match.");
            }
            $passwordCheckSql = "SELECT password FROM users WHERE email = ?";
            $stmt = $mysqli->prepare($passwordCheckSql);
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($user = $result->fetch_assoc()) {
                    if (!password_verify($oldPassword, $user['password'])) {
                        throw new Exception("Old password is incorrect.");
                    }
                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePasswordSql = "UPDATE users SET password = ? WHERE email = ?";
                    $stmt = $mysqli->prepare($updatePasswordSql);
                    $stmt->bind_param("ss", $newPasswordHash, $newEmail);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $changesMade = true;
                    }
                }
                $stmt->close();
            } else {
                throw new Exception("Failed to fetch user for password update: " . $stmt->error);
            }
        }

        if ($changesMade) {
            $mysqli->commit();
            $response = ['status' => 'success', 'message' => 'User details updated successfully.'];
        } else {
            $mysqli->rollback();
            throw new Exception("No changes were made to the user details.");
        }
    } catch (Exception $e) {
        $mysqli->rollback();
        $response['message'] = 'Failed to update user details. ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request or not logged in.';
}

echo json_encode($response);
$mysqli->close();

?>