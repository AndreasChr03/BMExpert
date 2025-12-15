<?php

include __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $propertyId = $_POST['propertyId'];
    $name = $_POST['yourName'];
    $surname = $_POST['yourSurname'];
    $phone = $_POST['yourPhone'];
    $email = $_POST['yourEmail'];
    $comment = $_POST['yourMessage'];

    $stmt = $mysqli->prepare("INSERT INTO guest (property_id, name, surname, phone, email, comment) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $propertyId, $name, $surname, $phone, $email, $comment);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Message has been sent']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to send message. Please try again.']);
    }

    $stmt->close();
    $mysqli->close();
}

?>