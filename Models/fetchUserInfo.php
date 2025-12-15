<?php

include __DIR__ . '/../../config/config.php';  // Include config with $mysqli setup

if(isset($_SESSION["email"])) {
    $emailID = $_SESSION["email"];
    $sql = "SELECT name, surname, email, phone_1, phone_2 FROM users WHERE email = ? LIMIT 1";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    $stmt->bind_param("s", $emailID);
    if ($stmt->execute()) {
        $stmt->bind_result($name, $surname, $email, $phone_1, $phone_2);
        if ($stmt->fetch()) {
            $userInfo = [
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'phone_1' => $phone_1,
                'phone_2' => $phone_2
            ];
        } else {
            echo "No user found with that email.";
        }
    } else {
        echo "Execute error: " . $stmt->error;
    }

    $stmt->free_result();
    $stmt->close();

} else {
    echo "Email ID is not set in session.";
}

?>