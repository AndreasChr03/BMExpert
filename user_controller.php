<?php
session_start();
$loginError = '';
include_once '../../config/config.php';
include_once '../Models/User.php';


$user = new User($mysqli);

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // On the Module If the user is already logged in, redirect them to the home URL when they press the home button on header
    if (isset($_POST['homeButtonClicked'])) {
        // Call determineHomeURL function to get the home URL based on the user's role
        $homeURL = $user->determineHomeURL($_SESSION["role_id"]);
        // Redirect the user
        header("Location: $homeURL");
    }


    //Registration
    if (isset($_POST['submit'])) {
        $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : null;
        $surname = isset($_POST['surname']) ? trim(htmlspecialchars($_POST['surname'])) : null;
        $phone_1 = isset($_POST['phone_1']) ? trim(htmlspecialchars($_POST['phone_1'])) : null;
        $phone_2 = isset($_POST['phone_2']) ? $_POST['phone_2'] : null;
        $nationality = isset($_POST['nationality']) ? trim(htmlspecialchars($_POST['nationality'])) : null;
        $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
        $password = isset($_POST['password']) ? trim(htmlspecialchars($_POST['password'])) : null;
        $repeatpassword = isset($_POST['repeatpassword']) ? trim(htmlspecialchars($_POST['repeatpassword'])) : null;

        // Call the register method from the User model
        $registrationResponse[] = $user->register($name, $surname, $phone_1, $phone_2, $nationality, $email, $password, $repeatpassword);

        // Process the response from the registration method
        if (!empty($registrationResponse['errors'])) {
            foreach ($registrationResponse['errors'] as $error) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
            }
        }

        if (!empty($registrationResponse['success'])) {
            echo "<div class='alert alert-success'>" . ($registrationResponse['success']) . "</div>";
        }
    }

}

// If the user is already logged in, redirect them to the home URL when they press the home button on header

