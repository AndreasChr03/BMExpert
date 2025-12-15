<?php
// Include the database configuration file from a specified path
include_once '../../../../../config/config.php';
include_once 'helper_functions.php';

// Check if there is a connection error with the database and handle it
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
// Check if the 'property_id' has been sent via POST request
if (isset($_POST['property_id'])) {
    $propertyId = $_POST['property_id'];
// Prepare an SQL statement to fetch property and related user details
    $stmt = $mysqli->prepare("SELECT
        p.floor,
        p.number,
        p.status,
        t.name AS tenant_name,
        t.surname AS tenant_surname,
        t.phone_1 AS tenant_phone,
        t.email AS tenant_email,
        o.name AS owner_name,
        o.surname AS owner_surname,
        o.phone_1 AS owner_phone,
        o.email AS owner_email,
        p.area,
        p.rooms,
        p.bathrooms,
        p.furnished,
        p.pet,
        p.parking,
        p.details,
        p.comment
    FROM
        property p
        LEFT JOIN users t ON p.tenant_id = t.user_id
        LEFT JOIN users o ON p.owner_id = o.user_id
    WHERE property_id = ?");

    $stmt->bind_param("i", $propertyId);
    // Execute statement and handle potential errors
    if (!$stmt->execute()) {
        echo "<p>Error executing statement: " . htmlspecialchars($stmt->error) . "</p>";
        $stmt->close();
        $mysqli->close();
        exit;
    }
    $result = $stmt->get_result();

    // Function to display each information set
    function displayInfoSet($label, $value)
    {
        echo "<div style='flex-basis: calc(50% - 10px);'>"; // Flex item for each set
        echo "<div style='margin-bottom: 5px;'>$label</div>";
        echo "<div style='background: white; border: 1px solid #054d56; padding: 10px; border-radius: 8px;'>" . htmlspecialchars($value) . "</div>";
        echo "</div>";
    }
    function displayInfoSetWithSpace($label, $value)
    {
        echo "<div style='flex-basis: calc(33.333% - 20px); margin-right: 10px;'>"; // Flex item with space
        echo "<div style='margin-bottom: 5px;'>$label</div>";
        echo "<div style='background: white; border: 1px solid #054d56; padding: 10px; border-radius: 8px;'>" . htmlspecialchars($value) . "</div>";
        echo "</div>";
    }
    function displayLabelAboveTextArea($label, $value) {
        echo "<div style='margin-bottom: 20px;'>"; // Container for the label and value
        echo "<label style='display: block; margin-bottom: 5px;'>$label</label>"; // Label displayed as block
        echo "<div style='min-height: 70px; width: 100%; padding: 10px; border: 1px solid #054d56; background-color: white; resize: none; overflow-y: auto; border-radius: 8px;'>" . nl2br(htmlspecialchars($value)) . "</div>";
        echo "</div>";
    }
        
    echo "<head>";
    echo ' <link rel="stylesheet" href="../../../../../public/css/fetchPropertyDetails.css">';
    echo "</head>";

    // Check if there are results and output them
    if ($result && $row = $result->fetch_assoc()) {
        echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>"; // Flex container for the entire set of information

        // Display Floor, Number, and Status in the same line
        echo "<div style='display: flex; width: 100%; justify-content: space-between;'>"; // Container for Floor, Number, and Status
        displayInfoSetWithSpace('Floor', $row['floor'] ?? '-');
        displayInfoSetWithSpace('Number', $row['number'] ?? '-');
        displayInfoSetWithSpace('Status', translateStatus($row['status'] ?? '-'));
        echo "</div>"; // Close the container for Floor, Number, and Status


        // Display Owner Name and Surname
        displayInfoSet('Owner Name', $row['owner_name'] ?? '-');
        displayInfoSet('Owner Surname', $row['owner_surname'] ?? '-');

        // Display Owner Phone and Email
        displayInfoSet('Owner Phone', $row['owner_phone'] ?? '-');
        displayInfoSet('Owner Email', $row['owner_email'] ?? '-');

        // Display Tenant Name and Surname
        displayInfoSet('Tenant Name', $row['tenant_name'] ?? '-');
        displayInfoSet('Tenant Surname', $row['tenant_surname'] ?? '-');

        // Display Tenant Phone and Email
        displayInfoSet('Tenant Phone', $row['tenant_phone'] ?? '-');
        displayInfoSet('Tenant Email', $row['tenant_email'] ?? '-');

        // Display Area, Rooms, and Bathrooms in the same line with space between
        echo "<div style='display: flex; width: 100%; justify-content: space-between;'>"; // Container for Area, Rooms, and Bathrooms
        displayInfoSetWithSpace('Area', $row['area'] ?? '-');
        displayInfoSetWithSpace('Rooms', $row['rooms'] ?? '-');
        displayInfoSetWithSpace('Bathrooms', $row['bathrooms'] ?? '-');
        echo "</div>"; // Close the container for Area, Rooms, and Bathrooms


        // Display Furnished, Parking, and Pet Friendly in the same line with space between
        echo "<div style='display: flex; width: 100%; justify-content: space-between;'>"; // Container for Area, Rooms, and Bathrooms
        displayInfoSetWithSpace('Furnished', translateYesNo($row['furnished'] ?? '-'));
        displayInfoSetWithSpace('Parking', translateParking($row['parking'] ?? '-'));
        displayInfoSetWithSpace('Pet Friendly', translateYesNo($row['pet'] ?? '-'));
        echo "</div>"; // Close the container for Area, Rooms, and Bathrooms

        // Display Details and Comments
        echo "<div style='width: 100%;'>"; // Full width for details and comments
        displayLabelAboveTextArea('Details', $row['details'] ?? '-');
        displayLabelAboveTextArea('Comments', $row['comment'] ?? '-');
        echo "</div>";

        echo "</div>"; // Close flex container
    } else {
        echo "<p>No details found for this property.</p>";
    }

    // Close the statement and connection to free up resources
    $stmt->close();
    $mysqli->close();
} else {
    echo "<p>Invalid property ID provided.</p>"; // Error message if property ID is not provided
}
