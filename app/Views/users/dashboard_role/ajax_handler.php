<?php

include_once '../../../../config/config.php';
include('calendar/smtp/PHPMailerAutoload.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['action'])) {
    if ($_GET['action'] == "sendEmailsForUpcomingEvents") {

        sendEmailsForUpcomingEvents();
    }
} else {
    if ($_POST['action'] == "insertEvent") {
        insertEvent($_POST['eventName'], $_POST['startDate'], $_POST['endDate'], $_POST['userID'] ?? null, $_POST['email'] ?? null, $_POST['emailMsg'] ?? null);

        // smtp_mailer($_POST['email'], "No Reply", $_POST['emailMsg']);
    }

    if ($_POST['action'] == "getEventByMonthAndYear") {
        getEventByMonthAndYear($_POST['month'], $_POST['year']);
    }

    if ($_POST['action'] == "deleteEvent") {
        deleteEvent($_POST['event_id']);
    }

    if ($_POST['action'] == "updateEvent") {

        updateEvent($_POST['eventName'], $_POST['startDate'], $_POST['endDate'], $_POST['event_id'], $_POST['userID'] ?? null, $_POST['email'] ?? null, $_POST['emailMsg'] ?? null);

        // smtp_mailer($_POST['email'], "No Reply", $_POST['emailMsg']);
    }

    if ($_POST['action'] == "upComingEvent") {
        upComingEvent();
    }


    if ($_POST['action'] == "login") {
        login($_POST['user_email'], $_POST['user_password']);
    }

    if ($_POST['action'] == "getAllUsers") {
        getAllUsers();
    }


    if ($_POST['action'] == "sendEmail") {
        smtp_mailer($_POST['email'], "No Reply", $_POST['emailMsg']);
    }

    if ($_POST['action'] == "getEventByID") {
        getEventByID($_POST['event_id']);
    }
}




function sendEmailsForUpcomingEvents()
{
    global $conn;

    // Get all calendar events
    $sql = "SELECT * FROM calendarevent";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            if($row['endDate']) {

                // Check if the end date is within 24 hours from now
                $endDate = new DateTime($row['endDate']);
                $now = new DateTime();
                $interval = $endDate->diff($now);

                if ($interval->h <= 24) {
                    if ((string)$row['email'] != "") {
                        smtp_mailer((string)$row['email'], "No Reply", (string)$row['emailMsg']);
                    }
                }

            }

        }
    }
    echo "All the emails has been sent.";
}
function getAllUsers()
{

    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * from users";


    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // Get the result set
        $result = $stmt->get_result();

        // Initialize an array to store events
        $events = array();

        // Fetch rows one by one
        while ($row = $result->fetch_assoc()) {
            // Add each row to the events array
            $events[] = $row;
        }

        // Free the result set
        $result->free();

        // Close the statement
        $stmt->close();

        // Return the fetched events
        echo json_encode($events);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();

    return []; // Return an empty array if there's an error

}


// echo smtp_mailer('bmexpert.enterprise@gmail.com','Test Email','Test Email');
function smtp_mailer($to, $subject, $msg)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    //$mail->SMTPDebug = 2; 
    $mail->Username = "bmexpert.enterprise@gmail.com";
    $mail->Password = "mgns ssgh cjjv foql";
    $mail->SetFrom("bmexpert.enterprise@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}

/*function login($user_email, $user_password)
{
    global $conn;

    // Prepare SQL statement
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("s", $user_email);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the result set
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Fetch the hashed password from the result
            $row = $result->fetch_assoc();
            $dbPassword = $row['password'];

            // Verify the provided password against the hashed password from the database
            if (password_verify($user_password, $dbPassword)) {

                // Store the User object in the session
                $_SESSION['name'] = $row["name"];
                $_SESSION['email'] = $row["email"];
                if ($row['role_id'] == 1) {
                    $role_name = "Admin";
                } else if ($row['role_id'] == 2) {
                    $role_name = "Owner";
                } else {
                    $role_name = "Tenant";
                }

                $_SESSION["role_name"] = $role_name;




                echo "true";
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }
    } else {
        // Error executing the statement
        echo "Error: " . $sql . "<br>" . $conn->error;
        echo "false";
    }
    $conn->close();
}
*/


function upComingEvent()
{


    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement with filtering by month and year
    // $sql = "SELECT * FROM calendarevent WHERE MONTH(startDate) = ? AND YEAR(startDate) = ?";
    $sql = "SELECT 
    event_id, 
    CASE 
        WHEN isPrimary = 'false' 
        THEN 
            CONCAT(
                name, 
                IF(
                    TIMESTAMPDIFF(SECOND, startDate, endDate) >= 3600, 
                    CONCAT(
                        '\n', 
                        TIMESTAMPDIFF(HOUR, startDate, endDate), 
                        IF(
                            TIMESTAMPDIFF(HOUR, startDate, endDate) > 1, 
                            ' Hours ', 
                            ' Hour '
                        )
                    ), 
                    ''
                ), 
                IF(
                    (TIMESTAMPDIFF(SECOND, startDate, endDate) % 3600) >= 60, 
                    CONCAT(
                        '\n', 
                        TIMESTAMPDIFF(MINUTE, startDate, endDate) % 60, 
                        IF(
                            (TIMESTAMPDIFF(MINUTE, startDate, endDate) % 60) > 1, 
                            ' Minutes ', 
                            ' Minute '
                        )
                    ), 
                    ''
                )
            )
        ELSE 
            name 
    END AS name, 
    isPrimary, 
    startDate, 
    endDate 
FROM 
    (SELECT * FROM calendarevent WHERE startDate > CURDATE() AND startDate != CURDATE()) AS ce;";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param("i",  $year); // Assuming $month and $year are integers

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // Get the result set
        $result = $stmt->get_result();

        // Initialize an array to store events
        $events = array();

        // Fetch rows one by one
        while ($row = $result->fetch_assoc()) {
            // Add each row to the events array
            $events[] = $row;
        }

        // Free the result set
        $result->free();

        // Close the statement
        $stmt->close();

        // Return the fetched events
        echo json_encode($events);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();

    return []; // Return an empty array if there's an error
}
function deleteEvent($event_id)
{
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $sql = "DELETE FROM calendarevent WHERE event_id = ?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $event_id);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        echo $stmt->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }


    // Close the statement and connection
    $stmt->close();
}

function getEventByID($event_id)
{
    global $conn;

    // Prepare SQL statement
    $sql = "SELECT * FROM calendarevent WHERE event_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("s", $event_id);

    // Execute the statement
    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // Get the result set
        $result = $stmt->get_result();

        // Initialize an array to store events
        $events = array();

        // Fetch rows one by one
        while ($row = $result->fetch_assoc()) {
            // Add each row to the events array
            $events[] = $row;
        }

        // Free the result set
        $result->free();

        // Close the statement
        $stmt->close();

        // Return the fetched events
        echo json_encode($events);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();

    return [];
}



function updateEvent($eventName, $startDate, $endDate, $event_id, $userID, $email, $emailMsg)
{
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $sql = "UPDATE calendarevent SET  userID = '$userID', email = '$email', emailMsg = '$emailMsg', name = '$eventName', startDate = '$startDate' , endDate = '$endDate' WHERE event_id = '$event_id'";

    // Execute the statement
    if ($conn->query($sql) === TRUE) {
        echo "Event updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();
}

function insertEvent($eventName, $startDate, $endDate, $userID, $email, $emailMsg)
{
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $sql = "INSERT INTO calendarevent (name, startDate, endDate, isPrimary, userID, email, emailMsg) VALUES (?, ?, ?, false, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $eventName, $startDate, $endDate, $userID, $email, $emailMsg);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        echo $stmt->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }


    // Close the statement and connection
    $stmt->close();
}

function getEventByMonthAndYear($month, $year)
{
    session_start();
    $role_id = $_SESSION["role_id"];
    $user_id = $_SESSION["user_id"];

    if($role_id == 1) {
        $where = '';
    } else {
        $where = 'WHERE userID = ' . $user_id . ' OR userID IS NULL';
    } //end if

    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement with filtering by month and year
    // $sql = "SELECT * FROM calendarevent WHERE MONTH(startDate) = ? AND YEAR(startDate) = ?";
    $sql = "SELECT 
    event_id, 
    CASE 
        WHEN isPrimary = 'false' 
        THEN 
            CONCAT(
                name, 
                IF(
                    TIMESTAMPDIFF(SECOND, startDate, endDate) >= 3600, 
                    CONCAT(
                        '\n', 
                        TIMESTAMPDIFF(HOUR, startDate, endDate), 
                        IF(
                            TIMESTAMPDIFF(HOUR, startDate, endDate) > 1, 
                            ' Hours ', 
                            ' Hour '
                        )
                    ), 
                    ''
                ), 
                IF(
                    (TIMESTAMPDIFF(SECOND, startDate, endDate) % 3600) >= 60, 
                    CONCAT(
                        '\n', 
                        TIMESTAMPDIFF(MINUTE, startDate, endDate) % 60, 
                        IF(
                            (TIMESTAMPDIFF(MINUTE, startDate, endDate) % 60) > 1, 
                            ' Minutes ', 
                            ' Minute '
                        )
                    ), 
                    ''
                )
            )
        ELSE 
            name 
    END AS name, 
    isPrimary, 
    startDate, 
    endDate 
FROM 
    calendarevent
$where";


    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param("i",  $year); // Assuming $month and $year are integers

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // Get the result set
        $result = $stmt->get_result();

        // Initialize an array to store events
        $events = array();

        // Fetch rows one by one
        while ($row = $result->fetch_assoc()) {
            // Add each row to the events array
            $events[] = $row;
        }

        // Free the result set
        $result->free();

        // Close the statement
        $stmt->close();

        // Return the fetched events
        echo json_encode($events);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();

    return []; // Return an empty array if there's an error
}



// Example usage
// $eventName = "Meeting";
// $startDate = "2024-04-14"; 
// insertEvent($eventName, $startDate);
