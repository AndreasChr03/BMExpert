<?php
include '../../../config/config.php'; // Ensure your DB connection settings are correct
session_start();

// Initialize your building information array
$building = [
  'name' => '',
  'num_floors' => '',
  'num_properties' => '',
  'comment' => '',
  'address' => '',
  'postal_code' => '',
  'city' => '',
  'county' => ''
];

// Specify the building ID for which to fetch data
$buildingId = 1; // Example ID

// Prepare and execute the SQL query
$stmt = $mysqli->prepare("SELECT name, address, postal_code, city, county, num_floors, num_properties, comment FROM building WHERE building_id = ?");
$stmt->bind_param("i", $buildingId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Split the comment into lines and wrap each line in a `<p>` tag
  $lines = explode("\n", $row['comment']);
  $formattedComment = "";
  foreach ($lines as $line) {
    $formattedComment .= '<p class="minimal-spacing">' . htmlspecialchars($line) . '</p>';
  }

  // Populate the building array with fetched and formatted data
  $building = [
    'name' => $row['name'],
    'num_floors' => $row['num_floors'],
    'num_properties' => $row['num_properties'],
    'comment' => $formattedComment,
    'address' => $row['address'],
    'postal_code' => $row['postal_code'],
    'city' => $row['city'],
    'county' => $row['county']
  ];
}

// Assume connection to the database is established and relevant data is fetched
$address = $row['address'];
$city = $row['city'];
$postal_code = $row['postal_code'];
$full_address = $address . ', ' . $city . ', ' . $postal_code;
$encoded_address = urlencode($full_address);
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BMExpert</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Tab Icon -->
    <link rel="icon" href="../../../public/img/logo.png">
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../public/css/landing_page.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> <!--Roboto-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<style>
    .form-group {
        position: relative;
        margin-bottom: 32px;
    }
    .form-control {
        width: 100%;
        display: block;
    }
    .invalid-feedback-form {
        position: absolute;
        width: 100%;
        top: 100%; /* Place it right below the input */
        left: 0;
        display: none; /* Hide by default */
        text-align: left;
    }
    .form-control.is-invalid ~ .invalid-feedback-form {
        display: block; /* Show only when the input is invalid */
    }
    .alert {
        opacity: 1;  /* Start fully visible */
        transition: opacity 0.5s ease-out; /* Smooth transition for the opacity */
    }
    .hidden {
        display: none !important; /* Important to override Bootstrap's display */
        opacity: 0 !important; /* Important to ensure it fades out */
    }
    .invalid-feedback-form {
        color: #f85149;
        font-weight: 450;  /* Semi-bold, lighter than bold */
    }
</style>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <?php include 'header.php'; ?>

    <!-- Contact Section -->
    <div class="container text-light p p-lg-0 pt-lg-5 text-center" style="margin-top: 20px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="pb-3" style="color: black;">Contact Us</h2>
                <p style="color: black;">Get in touch with us for any questions.</p>
            </div>
            <!-- Form Column -->
            <div class="col-lg-6 mx-auto">
                <div id="feedbackAlert" class="alert" style="display: none;"></div>
                <form action="../../Models/send-email.php" method="post" id="contactForm" class="needs-validation" novalidate>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                        <div class="invalid-feedback-form">
                            Please enter your name.
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="surname" placeholder="Surname" required>
                        <div class="invalid-feedback-form">
                            Please enter your surname.
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                        <div class="invalid-feedback-form">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" name="message" placeholder="Message" required></textarea>
                        <div class="invalid-feedback-form">
                            Please enter a message.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send</button>
                </form>
            </div>
            <!-- Map Column -->
            <div class="col-lg-6 mx-auto">
                <div class="container-fluid">
                    <iframe src="https://www.google.com/maps?q=<?= htmlspecialchars($encoded_address) ?>&output=embed" 
                        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    
    form.addEventListener('submit', function (event) {
        // Prevent the default form submit if it's invalid
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Loop over each form control to check validity
            Array.from(form.getElementsByClassName('form-control')).forEach(function(input) {
                const feedbackElement = input.nextElementSibling;
                // Check if the next sibling is an invalid feedback element
                if (feedbackElement && feedbackElement.classList.contains('invalid-feedback-form')) {
                    if (!input.validity.valid) {
                        feedbackElement.style.display = 'block'; // Show the feedback message
                        input.classList.add('is-invalid'); // Add 'is-invalid' class
                    } else {
                        feedbackElement.style.display = 'none'; // Hide the feedback message
                        input.classList.remove('is-invalid'); // Remove 'is-invalid' class
                    }
                }
            });
        }

        form.classList.add('was-validated');
    });

    // Remove the invalid feedback when the user corrects the input
    Array.from(form.getElementsByClassName('form-control')).forEach(function(input) {
        input.addEventListener('input', function() {
            const feedbackElement = input.nextElementSibling;
            if (feedbackElement && feedbackElement.classList.contains('invalid-feedback-form')) {
                if (input.validity.valid) {
                    feedbackElement.style.display = 'none'; // Hide the feedback message
                    input.classList.remove('is-invalid'); // Remove 'is-invalid' class
                } else {
                    feedbackElement.style.display = 'block'; // Show the feedback message
                    input.classList.add('is-invalid'); // Add 'is-invalid' class
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            const alert = document.getElementById('feedbackAlert');
            if (data.includes('Message has been sent')) {
                updateAlert(alert, 'Message sent successfully!', 'alert-success');
                form.reset();  // Reset the form after successful submission
                form.classList.remove('was-validated'); // Remove validation states
            } else {
                updateAlert(alert, 'Failed to send message. Please try again.', 'alert-danger');
            }
        })
        .catch(error => {
            updateAlert(document.getElementById('feedbackAlert'), 'Error sending message.', 'alert-danger');
        });
    });

    function updateAlert(alert, message, type) {
        alert.className = 'alert ' + type; // Update the class to change alert type
        alert.textContent = message; // Update the text content
        alert.style.opacity = 1; // Ensure the alert is fully visible
        alert.style.display = 'block'; // Show the alert

        setTimeout(() => {
            alert.style.opacity = 0; // Start the fade out
            setTimeout(() => {
                alert.classList.add('hidden'); // Hide the alert after fade completes
            }, 500); // Wait for the fade to complete
        }, 3000); // Display the alert for 3 seconds before starting fade
    }
});
</script>

</body>
</html>

<?php include 'footer.php'; ?>
<?php include 'login_modal.php'; ?>