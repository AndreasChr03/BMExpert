<?php
session_start();
include_once '../../../../../config/config.php';

// Initialize session messages
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = array();
}
if (!isset($_SESSION['success'])) {
    $_SESSION['success'] = '';
}

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"] !== 1)) {
    header("location: ../../../../../index.php");
    exit;
}

$role_id = $_SESSION["role_id"];

//Declaration of upload directory --> public/img/uploads
$upload_dir = "../../../../../public/img/uploads/";
//Allowed file types
$allowed_types = array('jpg', 'jpeg', 'png', 'gif');

// Handling building addition
if (isset($_POST['addBuilding'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $postalCode = $_POST['postal_code'];
    $city = $_POST['city'];
    $county = $_POST['county'];
    $numFloors = $_POST['num_floors'];
    $numProperties = $_POST['num_properties'];
    $comment = $_POST['comment'];
    $photo = $_FILES['photo'] ?? null;
    $photoPath = ''; // Initialize with an empty string

    $photo = $_FILES['photo'] ?? null;

    // Check if a file was uploaded and validate the file type
    if ($photo && $photo['error'] === UPLOAD_ERR_OK && in_array(pathinfo($photo['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $photoName = uniqid('build_', true) . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
        $photoPath = $upload_dir . $photoName;

        if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
            $_SESSION['success'] = "File uploaded successfully and data stored.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded file.";
            $photoPath = ''; // Reset photoPath as the upload failed
        }
    }

    // Prepare statement for inserting new building data
    $stmt = $mysqli->prepare("INSERT INTO building (name, address, postal_code, city, county, num_floors, num_properties, comment, building_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiiss", $name, $address, $postalCode, $city, $county, $numFloors, $numProperties, $comment, $photoPath);

    // Execute and check for errors
    if ($stmt->execute()) {
        $_SESSION['success'] = "New building added successfully.";
    } else {
        $_SESSION['errors'][] = "Error adding new building: " . $stmt->error;
    }
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handling building deletion
if (isset($_POST['deleteBuilding'])) {
    $buildingId = $_POST['building_id'];

    try {
        $stmt = $mysqli->prepare("DELETE FROM building  WHERE building_id = ?");
        $stmt->bind_param("i", $buildingId);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Building  deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Error deleting user.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['errors'][] = "Cannot delete this Building  because it referenced in other records.";
        } else {
            $_SESSION['errors'][] = "Error deleting Building : " . $e->getMessage();
        }
    } finally {
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    }
}

// Handling building update
if (isset($_POST['updateBuilding'])) {
    $buildingId = $_POST['building_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $postalCode = $_POST['postal_code'];
    $city = $_POST['city'];
    $county = $_POST['county'];
    $numFloors = $_POST['num_floors'];
    $numProperties = $_POST['num_properties'];
    $comment = $_POST['comment'];
    $existingPhoto = $_POST['existing_photo'];

    $photo = $_FILES['photo'] ?? null;
    $photoPath = $existingPhoto;

    // Check if a new file was uploaded and validate the file type
    if ($photo && $photo['error'] === UPLOAD_ERR_OK && in_array(pathinfo($photo['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $photoName = uniqid('build_', true) . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
        $photoPath = $upload_dir . $photoName;

        if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
            $_SESSION['success'] = "File uploaded and building data updated successfully.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded file.";
        }
    }

    // Prepare the update statement
    $stmt = $mysqli->prepare("UPDATE building SET name = ?, address = ?, postal_code = ?, city = ?, county = ?, num_floors = ?, num_properties = ?, comment = ?, building_photo = ? WHERE building_id = ?");
    $stmt->bind_param("sssssiissi", $name, $address, $postalCode, $city, $county, $numFloors, $numProperties, $comment, $photoPath, $buildingId);

    // Execute and check for errors
    if ($stmt->execute()) {
        $_SESSION['success'] = "Building updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    } else {
        $_SESSION['errors'][] = "Error updating building: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch buildings for display
$sql = "SELECT building_id, name, address, postal_code, city, county, num_floors, num_properties, comment, building_photo FROM building";
$result = $mysqli->query($sql);

// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

mysqli_close($mysqli);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Settings</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_settings.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
</head>

<body>
    <!-- Header -->
    <?php include_once '../dashboard_modules_header.php'; ?>


    <!-- Displaying Error Messages -->
    <?php if (!empty($_SESSION['errors'])) : ?>
        <?php foreach ($_SESSION['errors'] as $error) : ?>
            <div class='alert alert-danger'><strong><?= $error ?></strong></div>
        <?php endforeach;
        unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <!-- Displaying Success Messages -->
    <?php if (!empty($_SESSION['success'])) : ?>
        <div class='alert alert-success'><strong><?= $_SESSION['success']; ?></strong></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])) : ?>
        <div class='alert alert-success'><strong><?= $_SESSION['success']; ?></strong></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Building Table Display -->
    <div class="main">
        <div class="user-header d-flex justify-content-between align-items-center button-container">
            <h2 style="margin-left: 14px;">Settings</h2>
            <div>
                <!-- Centered button for backup purposes -->
                <button class="btn btn-success mx-auto" style="background-color: #086972;" onclick="performBackup();">
                    <i class="fas fa-database"></i> Backup
                </button>
                <button id="displayBackupFiles" class="btn btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#filesModal">
                    <i class="fas fa-list"></i>
                </button>
            </div>
            <button class="btn btn-success" data-toggle="modal" data-target="#addBuildingModal" style="background-color: #086972; margin-right: 20px;">
                <i class="fas fa-plus"></i> Add Building
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="buildingTable">
                <thead>
                    <tr>
                        <th>A/A</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Postal Code</th>
                        <th>City</th>
                        <th>County</th>
                        <th>Floors</th>
                        <th>Properties</th>
                        <th>Comment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowNumber = 1;
                    while ($row = $result->fetch_assoc()) :
                        // Παίρνουμε το σχόλιο και το καθαρίζουμε από ειδικούς χαρακτήρες
                        $full_comment = htmlspecialchars($row['comment'] ?? '-');
                        // Μετατρέπουμε το σχόλιο σε πίνακα λέξεων
                        $words = explode(' ', $full_comment);
                        // Ελέγχουμε αν το σχόλιο έχει περισσότερες από 5 λέξεις
                        if (count($words) > 5) {
                            // Παίρνουμε τις πρώτες 5 λέξεις και προσθέτουμε ...
                            $comment = implode(' ', array_slice($words, 0, 5)) . '...';
                        } else {
                            // Αλλιώς εμφανίζουμε όλο το κείμενο
                            $comment = $full_comment;
                        }
                    ?>
                        <tr>
                            <td><?= $rowNumber++ ?></td>
                            <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['address'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['postal_code'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['city'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['county'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['num_floors'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['num_properties'] ?? '-') ?></td>
                            <td><?= $comment ?></td>
                            <td>
                                <!-- Κουμπιά επεξεργασίας και άλλες ενέργειες -->
                                <button class="btn btn-primary edit-btn" data-toggle="modal" data-target="#editBuildingModal" data-buildingid="<?= htmlspecialchars($row['building_id']) ?>" data-name="<?= htmlspecialchars($row['name']) ?>" data-address="<?= htmlspecialchars($row['address']) ?>" data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>" data-city="<?= htmlspecialchars($row['city']) ?>" data-county="<?= htmlspecialchars($row['county']) ?>" data-num_floors="<?= htmlspecialchars($row['num_floors']) ?>" data-num_properties="<?= htmlspecialchars($row['num_properties']) ?>" data-comment="<?= $full_comment ?>" data-photo="<?= htmlspecialchars($row['building_photo']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Trigger Modal Instead of Direct Delete -->
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteConfirmationModal" onclick="setDeleteBuildingId(<?= $row['building_id'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modals for Add and Edit Building -->
    <!-- Add Building Modal -->
    <div class="modal fade" id="addBuildingModal" tabindex="-1" role="dialog" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBuildingModalLabel">Add New Building</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="building_id" id="building_id">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_name">Building Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback">
                                    Please enter a valid building name.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="edit_address">Address</label>
                                <input type="text" class="form-control" id="edit_address" name="address" required>
                                <div class="invalid-feedback">
                                    Please enter a valid address.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="edit_postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required onkeyup="validatePostalCode(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid postal code.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_city">City</label>
                                <input type="text" class="form-control" id="edit_city" name="city" required onkeyup="validateLetters(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid city.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_county">County</label>
                                <input type="text" class="form-control" id="edit_county" name="county" required onkeyup="validateLetters(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid county.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_num_floors">Number of Floors</label>
                                <input type="number" class="form-control" id="edit_num_floors" name="num_floors" required>
                                <div class="invalid-feedback">
                                    Please enter a valid number of floors.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_num_properties">Number of Properties</label>
                                <input type="number" class="form-control" id="edit_num_properties" name="num_properties" required>
                                <div class="invalid-feedback">
                                    Please enter a valid number of properties.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="photo">Upload a Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="file" style="display: none;" id="edit-photo" name="photo" onchange="fileName()">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="edit-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_comment">Comment</label>
                                <textarea class="form-control" id="edit_comment" name="comment"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateBuilding">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Building Modal -->
    <div class="modal fade" id="editBuildingModal" tabindex="-1" role="dialog" aria-labelledby="editBuildingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBuildingModalLabel">Edit Building</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="building_id" id="building_id">
                    <div class="modal-body">
                        <!-- Form fields for editing building -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_name">Building Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback">
                                    Please enter a valid building name.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="edit_address">Address</label>
                                <input type="text" class="form-control" id="edit_address" name="address" required>
                                <div class="invalid-feedback">
                                    Please enter a valid address.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="edit_postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required onkeyup="validatePostalCode(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid postal code.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_city">City</label>
                                <input type="text" class="form-control" id="edit_city" name="city" required onkeyup="validateLetters(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid city.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_county">County</label>
                                <input type="text" class="form-control" id="edit_county" name="county" required onkeyup="validateLetters(this)">
                                <div class="invalid-feedback">
                                    Please enter a valid county.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_num_floors">Number of Floors</label>
                                <input type="number" class="form-control" id="edit_num_floors" name="num_floors" required>
                                <div class="invalid-feedback">
                                    Please enter a valid number of floors.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_num_properties">Number of Properties</label>
                                <input type="number" class="form-control" id="edit_num_properties" name="num_properties" required>
                                <div class="invalid-feedback">
                                    Please enter a valid number of properties.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="photo">Upload a Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="file" style="display: none;" id="edit-photo" name="photo" onchange="fileName()">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="edit-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_comment">Comment</label>
                                <textarea class="form-control" id="edit_comment" name="comment"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateBuilding">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this building? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Backup Status -->
    <div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backupModalLabel">Backup Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Status message will be displayed here -->

                    <script>
                        function performBackup() {
                            $.ajax({
                                url: 'backup.php', // Path to the backup script
                                type: 'GET',
                                success: function(response) {
                                    showBackupStatus(response);
                                },
                                error: function() {
                                    showBackupStatus('Failed to perform backup. Please try again.');
                                }
                            });
                        }

                        function showBackupStatus(message) {
                            $(".modal-body").text(message);
                            $("#backupModal").modal("show");

                            setTimeout(function() {
                                $("#backupModal").modal("hide");

                                // Refresh the page 1 second after the modal is hidden to give a smooth user experience
                                setTimeout(function() {
                                    window.location.reload(); // This will reload the page
                                }, 1000);
                            }, 3000); // Modal is displayed for 3 seconds
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal for displaying database backups -->
    <div class="modal fade" id="filesModal" tabindex="-1" aria-labelledby="filesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filesModalLabel">Backup Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="filesList" class="list-group">
                        <!-- Files will be listed here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- Bootstrap Bundle with Popper (includes jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../../../public/js/dashboard_admin_users.js"></script>
    <script src="../../../../../public/js/dashboard_admin_settings.js"></script>

    <script>
        (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
            })
        })()

        $(document).ready(function() {
            // Listen for the 'hidden.bs.modal' event
            $('#addBuildingModal').on('hidden.bs.modal', function (e) {
                // Reset the form inside the modal
                $(this).find('form')[0].reset();

                // Clear validation classes
                $(this).find('.form-control').removeClass('is-invalid').removeClass('is-valid');

                // Reset the form validation state
                $(this).find('.needs-validation').removeClass('was-validated');
            });
        });
    </script>
    
</body>

</html>