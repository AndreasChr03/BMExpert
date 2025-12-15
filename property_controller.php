<?php
// session_start();
include_once __DIR__ . '/../../config/config.php';
include_once __DIR__ . '/../Models/Property.php';

$loginError = '';


// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit();
}

// Initialize Property object
$property = new Property($mysqli);

// Get user email from session
$email = $_SESSION["email"];

// Call getUserId from Property.php from database
$user_id = $property->getUserId($email);

// Check if the owner has any properties
$property_available = $property->hasProperties($user_id);
$_SESSION["property_available"] = $property_available;

// Show Property
// $property_id = $property->getPropertyId();
// $property_details = $property->showProperty($property_id);

//All properties of User
$all_properties = $property->showAllPropertiesofUser($user_id);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Populate property data
    $owner_id = $user_id;
    $building_id = 1; // Hardcoded for now
    if (isset($_POST["tenantId"])) {
        $tenant_id = $_POST["tenantId"];
    } else {
        $tenant_id = null;
    }
    $floor = $_POST["floor"];
    $number = $_POST["num"];
    $status = $_POST["status"];
    $pet_allowed = $_POST["pet"];
    $furnished = $_POST["furnished"];
    $rooms = $_POST["rooms"];
    $bathrooms = $_POST["bathrooms"];
    $parking = isset($_POST["parking"]) ? $_POST["parking"] : null;
    $area = isset($_POST["area"]) ? $_POST["area"] : null;
    $details = $_POST["details"];
    //$property_videos = $_POST["propertyVideos"]; 
    $comment = $_POST["comment"];

    // Set property data in the model
    $property->setBuildingId($building_id);
    $property->setOwnerId($owner_id);
    $property->setTenantId($tenant_id);
    $property->setFloor($floor);
    $property->setNumber($number);
    $property->setStatus($status);
    $property->setPet($pet_allowed);
    $property->setFurnished($furnished);
    $property->setRooms($rooms);
    $property->setBathrooms($bathrooms);
    $property->setParking($parking);
    $property->setArea($area);
    $property->setDetails($details);
    $property->setComment($comment);


    // Create property
    if ($property->insertProperty()) {
        $_SESSION["property_added"] = true;
        // Upload property photos
        $property_id = mysqli_insert_id($mysqli);
        $property->addPhoto($property_id, $_FILES);
        header("Location: dashboard_owner_properties.php");
        exit();
    } else {
        echo "Error adding property";
    }


}



