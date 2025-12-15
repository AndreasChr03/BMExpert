<?php
    
include __DIR__ ."/../../config/config.php";
$response = ['errors' => [], 'success' => ''];

if (isset($_POST['submit'])) {
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : null;
    $surname = isset($_POST['surname']) ? trim(htmlspecialchars($_POST['surname'])) : null;
    $phone_1 = isset($_POST['phone_1']) ? trim(htmlspecialchars($_POST['phone_1'])) : null;
    $phone_2 = isset($_POST['phone_2']) ? $_POST['phone_2'] : null;
    $nationality = isset($_POST['nationality']) ? trim(htmlspecialchars($_POST['nationality'])) : null;
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $password = isset($_POST['password']) ? trim(htmlspecialchars($_POST['password'])) : null;
    $repeatpassword = isset($_POST['repeatpassword']) ? trim(htmlspecialchars($_POST['repeatpassword'])) : null;
    $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : null;
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Email is not valid";
    }
    if (strlen($password) < 8) {
        $response['errors'][] = "Password must be at least 8 characters long";
    }
    if ($password !== $repeatpassword) {
        $response['errors'][] = "Passwords do not match";
    }

    if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] + 1) !== $role_id) {
        $response['errors'][] = "You do not have permission to assign this role.";
    }
    
    // Check if the email or phone_1 already exists
    $sql = "SELECT * FROM users WHERE email = ? OR phone_1 = ?";
    if ($stmt = mysqli_prepare($mysqli, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $phone_1);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['email'] === $email) {
                    $response['errors'][] = "Cannot create an account. Email already exists.";
                }
                if ($row['phone_1'] === $phone_1) {
                    $response['errors'][] = "Cannot create an account. Phone number already exists.";
                }
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['errors'][] = "Error preparing query.";
    }

    if (empty($response['errors'])) {
        $sql = "INSERT INTO users (email, password, name, surname, phone_1, phone_2, nationality, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($mysqli, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssi", $email, $passwordHash, $name, $surname, $phone_1, $phone_2, $nationality, $role_id);
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = "Registration successful! Redirecting...";
            } else {
                $response['errors'][] = "Failed to execute the insertion.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['errors'][] = "Error preparing the insert statement.";
        }
    }

    // Display errors if there are any
    foreach ($response['errors'] as $error) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
    }

    // If there's a success message, display it and redirect based on session's role_id
    if (!empty($response['success'])) {
        echo "<div class='alert alert-success'>" . htmlspecialchars($response['success']) . "</div>";
        $redirectUrl = "../../index.php";
        $creator_role_id = $_SESSION['role_id'] ?? 0; // Use null coalescing operator for default value

        switch ($creator_role_id) {
            case 1:
                $redirectUrl = "../users/dashboard_role/dashboard_admin/dashboard_admin_users.php";
                break;
            case 2:
                $redirectUrl = "../users/dashboard_role/dashboard_owner/dashboard_owner_tenants.php";
                break;
        }
        echo "<script>setTimeout(function(){ window.location.href = '$redirectUrl'; }, 2000);</script>";
    }
}

?>