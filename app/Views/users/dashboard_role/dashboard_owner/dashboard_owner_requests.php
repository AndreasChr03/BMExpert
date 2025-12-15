<?php
session_start();
require_once '../../../../../config/config.php';

// Consolidate session checks
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role_id"] !== 2) {
    header("location: ../../../../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handling request deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteGuest'])) {
    $guestId = $_POST['guest_id'];

    $mysqli->begin_transaction();
    try {
        $stmt = $mysqli->prepare("DELETE FROM guest WHERE guest_id = ?");
        $stmt->bind_param("i", $guestId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Request deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Error deleting request.";
        }
        $stmt->close();
        $mysqli->commit();
    } catch (mysqli_sql_exception $e) {
        $mysqli->rollback();
        $_SESSION['errors'][] = "Error deleting request: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
    exit();
}

// Fetch requests for display
$sql = "SELECT guest_id, property_id, name, surname, phone, email, comment FROM guest";
$result = $mysqli->query($sql);

// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");


//fetch request from tenants of owner with userid =session
$stmt_tenant = $mysqli->prepare("SELECT r.*, p.property_id FROM request r JOIN property p ON r.property_id = p.property_id WHERE p.owner_id = ?");
$stmt_tenant->bind_param("i", $user_id);
$stmt_tenant->execute();
$result_tenants = $stmt_tenant->get_result();
$stmt_tenant->close();




// Close MySQL connection
mysqli_close($mysqli);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include_once '../dashboard_modules_header.php'; ?>

    <!-- Displaying Error Messages -->
    <?php if (!empty($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class='alert alert-danger'><strong><?= $error ?></strong></div>
        <?php endforeach;
        unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <!-- Displaying Success Messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class='alert alert-success'><strong><?= $_SESSION['success']; ?></strong></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="main">
        <div class="user-header d-flex justify-content-between align-items-center">
            <h1 style="margin-left: 14px;">Requests</h1>
        </div>
    </div>

    <!-- User Table Display -->
    <div class="table-responsive">
        <table class="table table-bordered" id="userTable">
            <thead>
                <tr>
                    <th class="center-text">A/A</th>
                    <th>Property Number</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Title</th>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rowNumber = 1;
                while ($row = $result->fetch_assoc()):
                    // Fetch property number for each guest
                    $propertyId = $row['property_id'];
                    $sql2 = "SELECT number FROM property WHERE property_id = ?";
                    $stmt = $mysqli->prepare($sql2);
                    $stmt->bind_param("i", $propertyId);
                    $stmt->execute();
                    $result2 = $stmt->get_result();
                    $stmt->close();
                    $row2 = $result2->fetch_assoc();

                    ?>
                    <tr>
                        <td class="text-center"><?= $rowNumber++ ?></td>
                        <td><?= htmlspecialchars($row2['number'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['surname'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                        <td>Guest Request</td> <!--display null-->
                        <td><?= htmlspecialchars($row['comment'] ?? '-') ?></td>
                        <td>
                            <!-- Trigger Modal for Email Reply -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#emailReplyModal"
                                onclick="setReplyUserEmail('<?= $row['email'] ?>')">
                                <i class="bi bi-reply-fill"></i>
                            </button>
                            <!-- Trigger Modal Instead of Direct Delete -->
                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#deleteConfirmationModal" onclick="setDeleteUserId(<?= $row['guest_id'] ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile;

                //Fetch property number for each tenant
                while ($row_tenants = $result_tenants->fetch_assoc()):

                    $propertyId = $row_tenants['property_id'];
                    $sql_tenant = "SELECT number FROM property WHERE property_id = ?";
                    $stmt_tenant = $mysqli->prepare($sql_tenant);
                    $stmt_tenant->bind_param("i", $propertyId);
                    $stmt_tenant->execute();
                    $result_number_tenant = $stmt_tenant->get_result();
                    $stmt_tenant->close();
                    $row_number_tenant = $result_number_tenant->fetch_assoc();

                    //fetch teannt name and surname and email and phone from table users where u.tenant_id=p.tenant_id from table property
                    $sql_tenant = "SELECT u.name, u.surname, u.email, u.phone_1 FROM users u JOIN property p ON u.user_id = p.tenant_id WHERE p.property_id = ?";
                    $stmt_tenant = $mysqli->prepare($sql_tenant);
                    $stmt_tenant->bind_param("i", $propertyId);
                    $stmt_tenant->execute();
                    $result_tenant = $stmt_tenant->get_result();
                    $stmt_tenant->close();
                    $row_tenant = $result_tenant->fetch_assoc();

                    ?>
                    <tr>
                        <td class="text-center"><?= $rowNumber++ ?></td>

                        <td><?= htmlspecialchars($row_number_tenant['number'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenant['name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenant['surname'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenant['phone_1'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenant['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenants['subject'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row_tenants['details'] ?? '-') ?></td>
                        <td>
                             <!-- If the status is 'p' then the owner can reply to the tenant -->
                             <?php if ($row_tenants['status'] == 'p'): ?>
                                <!-- Trigger Modal for Email Reply -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#tenantReplyModal" data-name="<?= $row_tenant['name'] ?>"
                                data-surname="<?= $row_tenant['surname'] ?>"
                                data-propertyNum="<?= $row_number_tenant['number'] ?>"
                                data-requestId="<?= $row_tenants['request_id'] ?>">
                                <i class="bi bi-reply-fill"></i>
                            </button>
                            <?php endif; ?>


                        </td>
                    </tr>
                <?php endwhile;


                ?>

            </tbody>
        </table>
    </div>

    <!-- Email Reply Modal -->
    <div class="modal fade" id="emailReplyModal" tabindex="-1" role="dialog" aria-labelledby="emailReplyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailReplyModalLabel">Reply to Email</h5>
                    <button type="button" class="close" data-dismiss="modal" id="btn-close-reload" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Alert Container inside the modal -->
                <div id="alertContainer" class="mx-3 my-2"></div>
                <div class="modal-body pt-0">
                    <form id="emailReplyForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <div class="form-group">
                            <label for="guestEmail">To:</label>
                            <input type="email" class="form-control" id="guestEmail" name="guestEmail" readonly>
                        </div>
                        <div class="form-group">
                            <label for="emailSubject">Subject:</label>
                            <input type="text" class="form-control" id="emailSubject" name="emailSubject" required>
                            <div class="invalid-feedback">
                                Please enter a subject.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailMessage">Message:</label>
                            <textarea class="form-control" id="emailMessage" name="emailMessage" rows="4"
                                required></textarea>
                            <div class="invalid-feedback">
                                Please enter a message.
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendEmail">Send Email</button>
                </div>
            </div>
        </div>
    </div>

    <!--Reply to tenant Modal-->
    <div class="modal fade" id="tenantReplyModal" tabindex="-1" role="dialog" aria-labelledby="tenantReplyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenantReplyModalLabel">Reply to Tenant</h5>

                    <button type="button" class="close" data-dismiss="modal"  id="btn-close-reload" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Alert Container inside the modal -->
                <div id="alertContainer" class="mx-3 my-2"></div>

                <div class="modal-body pt-0">
                    <form id="tenantReplyForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <div class="form-group">
                            <label for="tenantFullName">To:</label>
                            <input type="text" class="form-control" id="tenantFullName" name="tenantFullName" readonly>
                        </div>
                        <div class="form-group">
                            <label for="propertyNum">Property Number:</label>
                            <input type="text" class="form-control" id="propertyNum" name="propertyNum" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tenantSubject">Subject:</label>
                            <input type="text" class="form-control" id="tenantSubject" name="tenantSubject" required>
                            <div class="invalid-feedback">
                                Please enter a subject.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tenantMessage">Message:</label>
                            <textarea class="form-control" id="tenantMessage" name="tenantMessage" rows="4"
                                required></textarea>
                            <div class="invalid-feedback">
                                Please enter a message.
                            </div>
                        </div>
                        <!-- Request ID to be sent to the server -->
                        <!-- <input type="hidden" id="requestId" name="requestId" value=""> -->

                        <div class="form-group">
                            <!-- <label for="requestId">Request ID:</label> -->
                            <input type="hidden" class="form-control" id="requestId" name="requestId" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendReply">Reply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this request? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="../../../../../public/js/dashboard_admin_users.js"></script>

    <script>
    // Function to reload the page when the close button is pressed
    document.getElementById('btn-close-reload').addEventListener('click', function () {
        window.location.reload();
    });
</script>

    <script>
        var deleteGuestId; // Variable to hold the user ID to delete

        function setDeleteUserId(guestId) {
            deleteGuestId = guestId; // Set the global user ID
        }

        function setReplyUserEmail(email) {
            document.getElementById('guestEmail').value = email;
        }

        $('#confirmDelete').click(function () {
            // Submit the form programmatically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Specify the action if needed

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'guest_id';
            idInput.value = deleteGuestId;
            form.appendChild(idInput);

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'deleteGuest';
            actionInput.value = '1'; // Value that triggers the delete operation in your PHP
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        });

        $(document).ready(function () {
            // Add custom validation for the send email button
            $('#sendEmail').click(function (event) {
                var form = document.getElementById('emailReplyForm');
                if (form.checkValidity()) {
                    // Prevent the default form submission
                    event.preventDefault();

                    // Use AJAX to submit form data
                    $.ajax({
                        url: '../../../../Models/request-reply.php', // Adjust the path as necessary
                        type: 'POST',
                        data: new FormData(form),
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            // Create a Bootstrap alert dynamically
                            var alertDiv = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                'Email sent successfully!' + '</div>');

                            // Append the alert to a container, e.g., a div with id="alertContainer"
                            $('#alertContainer').append(alertDiv);

                            // Hide the modal after 2 seconds
                            setTimeout(function () {
                                $('#emailReplyModal').modal('hide');
                                alertDiv.alert('close'); // Close the alert
                            }, 3000);

                            form.reset(); // Reset the form
                        },
                        error: function (xhr, status, error) {
                            // Create a Bootstrap alert dynamically
                            var alertDiv = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Failed to send email: ' + error + '</div>');

                            // Append the alert to a container, e.g., a div with id="alertContainer"
                            $('#alertContainer').append(alertDiv);

                            // Hide the modal after 2 seconds
                            setTimeout(function () {
                                $('#emailReplyModal').modal('hide');
                                alertDiv.alert('close'); // Close the alert
                            }, 3000);
                        }
                    });
                } else {
                    form.classList.add('was-validated');
                }
            });

            // Reset form when modal is closed
            $('#emailReplyModal').on('hidden.bs.modal', function (e) {
                $(this).find('form')[0].reset();
                $(this).find('.form-control').removeClass('is-invalid').removeClass('is-valid');
                $(this).find('.needs-validation').removeClass('was-validated');
                // Clear alerts when the modal is closed
                $('#alertContainer').empty();
            });
        });
    </script>

    <script>
        function setReplyTenant(name, surname, propertyNum, requestId) {
            document.getElementById('tenantFullName').value = name + ' ' + surname;
            document.getElementById('propertyNum').value = propertyNum;
            document.getElementById('requestId').value = requestId;
        }

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#tenantReplyModal').on('shown.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var name = button.data('name'); // Extract info from data-* attributes
                var surname = button.data('surname');
                var propertyNum = button.attr('data-propertyNum');
                var requestId = button.attr('data-requestId');

                setReplyTenant(name, surname, propertyNum, requestId);
            });
        });

        //Handle tenant reply
        $(document).ready(function () {
            // Add custom validation for the send email button
            $('#sendReply').click(function (event) {
                var form = document.getElementById('tenantReplyForm');
                if (form.checkValidity()) {
                    // Prevent the default form submission
                    event.preventDefault();

                    // Get the request ID value
                    var requestId = document.getElementById('requestId').value;

                    // Get the form data
                    var formData = new FormData(form);

                    // Append the requestId to the form data
                    formData.append('requestId', requestId);
                    // Use AJAX to submit form data
                    $.ajax({
                        url: '../../../../Models/tenant-request-reply.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            // Create a Bootstrap alert dynamically
                            var alertDiv = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                'Reply sent successfully!' + '</div>');

                            // Append the alert to a container, e.g., a div with id="alertContainer"
                            $('#alertContainer').append(alertDiv);

                            // Hide the modal
                            $('#tenantReplyModal').modal('hide');

                            // Close the alert after 2 seconds
                            setTimeout(function () {
                                alertDiv.alert('close');
                            }, 3000);

                            form.reset(); // Reset the form
                        },

                        error: function (xhr, status, error) {
                            // Create a Bootstrap alert dynamically
                            var alertDiv = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Failed to send reply: ' + error + '</div>');

                            // Append the alert to a container, e.g., a div with id="alertContainer"
                            $('#alertContainer').append(alertDiv);

                            // Hide the modal after 2 seconds
                            setTimeout(function () {
                                $('#tenantReplyModal').modal('hide');
                                alertDiv.alert('close'); // Close the alert
                            }, 3000);
                        }
                    });
                } else {
                    form.classList.add('was-validated');
                }
            });
        });
    </script>

</body>

</html>