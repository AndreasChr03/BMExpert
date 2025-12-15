<?php
session_start();
include_once '../../../../../config/config.php';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 2 )) {
    header("location: ../../../../../index.php");
    exit;
}

$loginError = '';
if ($_SESSION["loggedin"] == false) {
    header("Location: index.php");
    exit();
}
$role_id = $_SESSION["role_id"];
$owner_id = $_SESSION['user_id'];

// Handling user deletion
if (isset($_POST['deleteUser'])) {
    $userId = $_POST['user_id'];

    try {

        // Fetch property ID based on tenant ID
        $propertyIdStmt = $mysqli->prepare("SELECT property_id FROM property WHERE tenant_id = ?");
        $propertyIdStmt->bind_param("i", $userId);
        $propertyIdStmt->execute();
        $propertyIdResult = $propertyIdStmt->get_result();

        // Fetch property IDs
        $propertyIds = [];
        while ($row = $propertyIdResult->fetch_assoc()) {
            $propertyIds[] = $row['property_id'];
        }

        //When you delete a tenant, free the appartment
        $updateStmt = $mysqli->prepare("UPDATE property SET tenant_id = NULL, status = 'a' WHERE tenant_id = ?");
        $updateStmt->bind_param("i", $userId);
        $updateStmt->execute();
        $updateStmt->close();


        // Delete related entries from the "request" table
        foreach ($propertyIds as $propertyId) {
           $deleteRequestStmt = $mysqli->prepare("DELETE FROM request WHERE property_id = ?");
           $deleteRequestStmt->bind_param("i", $propertyId);
           $deleteRequestStmt->execute();
           $deleteRequestStmt->close();

           // Delete the contract and associated files
           $pdf_file_path = '../../../../../public/img/uploads/rental_agreement_' . $propertyId . '_*.pdf';
           array_map('unlink', glob($pdf_file_path));

           $deleteContractStmt = $mysqli->prepare("DELETE FROM contracts WHERE property_id = ?");
           $deleteContractStmt->bind_param("i", $propertyId);

           if ($deleteContractStmt->execute()) {
               $_SESSION['success'] = "Contract deleted successfully.";
           } else {
               $_SESSION['errors'][] = "Error deleting contract.";
           }
        }      

        $stmt = $mysqli->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Error deleting user.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['errors'][] = "Cannot delete this user because they are referenced in other records.";
        } else {
            $_SESSION['errors'][] = "Error deleting user: " . $e->getMessage();
        }
    } finally {
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    }
}
// Handling contract deletion
if (isset($_POST['deleteContract'])) {
    $propertyId = $_POST['property_id'];

    try {
        //Delete the file form the server
        $pdf_file_path = '../../../../../public/img/uploads/rental_agreement_' . $propertyId . '_*.pdf';
        array_map('unlink', glob($pdf_file_path));

        $stmt2 = $mysqli->prepare("DELETE FROM contracts WHERE property_id = ?");
        $stmt2->bind_param("i", $propertyId);

        if ($stmt2->execute()) {
            $_SESSION['success'] = "Contract deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Error deleting contract.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['errors'][] = "Cannot delete this contract because it is referenced in other records.";
        } else {
            $_SESSION['errors'][] = "Error deleting contract: " . $e->getMessage();
        }
    } finally {
        $stmt2->close();
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    }
}


// Handling user update
if (isset($_POST['updateUser'])) {
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone1 = $_POST['phone_1'];
    $phone2 = $_POST['phone_2'];
    $email = $_POST['email']; // Added email
    $nationality = $_POST['nationality'];

    // Assign the role_id value
    $role_id = 3;

    // Prepare the update statement
    $stmt = $mysqli->prepare("UPDATE users SET name = ?, surname = ?, phone_1 = ?, phone_2 = ?, email = ?, nationality = ? WHERE user_id = ?");
    $stmt->bind_param("ssssssi", $name, $surname, $phone1, $phone2, $email, $nationality, $userId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page to avoid form resubmission
        exit();
    } else {
        $_SESSION['errors'][] = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users for display along with contract information
$sql = "SELECT u.user_id, u.name, u.surname, u.phone_1, u.phone_2, u.email, u.nationality, p.number, p.property_id,
            c.contract_id, c.contract
        FROM users u
        LEFT JOIN property p ON p.tenant_id = u.user_id AND p.owner_id = ?
        LEFT JOIN contracts c ON c.property_id = p.property_id
        WHERE u.role_id = 3";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();


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
    <title>Users</title>
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
            <h1 style="margin-left: 14px;">List Of Tenants</h1>
            <a class="btn btn-success" href="../../../../Controllers/tenantRegister.php"
                style="background-color: #086972; margin-right: 20px;">
                <i class="fas fa-plus"></i> Add Tenant
            </a>
        </div>
    </div>


    <!-- User Table Display -->
    <div class="table-responsive">
        <table class="table table-bordered" id="userTable">
            <thead>
                <tr>
                    <th class="center-text">A/A</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Phone 1</th>
                    <th>Phone 2</th>
                    <th>Email</th> <!-- Added header for Email -->
                    <th>Nationality</th>
                    <th>Rented Property</th>
                    <th>Actions</th>
                    <th>Rental Agreement</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rowNumber = 1;
                while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?= $rowNumber++ ?></td>
                        <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['surname'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['phone_1'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['phone_2'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '-') ?></td> <!-- Display Email -->
                        <td><?= htmlspecialchars($row['nationality'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['number'] ?? 'Not Rented') ?></td>
                        <td>
                            <!-- Trigger Modal of edit  -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editUserModal"
                                data-userid="<?= htmlspecialchars($row['user_id']) ?>"
                                data-name="<?= htmlspecialchars($row['name'] ?? '-') ?>"
                                data-surname="<?= htmlspecialchars($row['surname'] ?? '-') ?>"
                                data-phone_1="<?= htmlspecialchars($row['phone_1'] ?? '-') ?>"
                                data-phone_2="<?= htmlspecialchars($row['phone_2'] ?? '-') ?>"
                                data-email="<?= htmlspecialchars($row['email'] ?? '-') ?>"
                                data-nationality="<?= htmlspecialchars($row['nationality'] ?? '-') ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Trigger Modal Instead of Direct Delete -->
                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#deleteConfirmationModal" onclick="setDeleteUserId(<?= $row['user_id'] ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td>
                            <!-- Trigger button for creating rental agreement with pdf -->
                            <?php if (!$row['contract_id']): ?>
                                <a href="../../../../Controllers/rentalAgreement.php?tenant_id=<?= $row['user_id'] ?>&property_id=<?= $row['property_id'] ?>"
                                class="btn btn-success">
                                <i class="fas fa-file-pdf"></i>
                            <?php endif; ?>
                            <?php if ($row['contract_id']): ?>
                                <!-- Trigger Modal Instead of Direct Delete -->
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteContract"
                                    onclick="setDeleteContract(<?= $row['property_id'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                                <!-- Button to download contract -->
                                <?php if ($row['contract_id']): ?>
                                    <a href="../../../<?=$row['contract']?>"
                                    class="btn btn-success" download>
                                    <i class="fas fa-download"></i> 
                                </a>
                            <?php endif; ?>
                            <?php if (($row['contract'])): ?>
                                    <button class="btn btn-primary btn-md print-btn" data-contract-url="../../../<?=$row['contract'] ?>">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                
                                <?php endif; ?>
                        </td>

                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>
    <iframe id="printFrame" style="display: none;"></iframe>



    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required
                                onkeyup="validateLetters(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_surname">Surname</label>
                            <input type="text" class="form-control" id="edit_surname" name="surname" required
                                onkeyup="validateLetters(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_phone_1">Phone 1</label>
                            <input type="text" class="form-control" id="edit_phone_1" name="phone_1" required
                                onkeyup="validateNumbers(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_phone_2">Phone 2</label>
                            <input type="text" class="form-control" id="edit_phone_2" name="phone_2"
                                onkeyup="validateNumbers(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required
                                onkeyup="validateEmail(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_nationality">Nationality</label>
                            <input type="text" class="form-control" id="edit_nationality" name="nationality" required
                                onkeyup="validateLetters(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateUser">Save changes</button>
                    </div>
                </form>
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
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Contract Modal -->
    <div class="modal fade" id="deleteContract" tabindex="-1" role="dialog" aria-labelledby="deleteContractLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteContractLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this contract? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteContract">Delete</button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#billTable').DataTable();

    // Function to print a specific contract
    function printContract(contractUrl) {
        var printFrame = document.getElementById('printFrame');
        printFrame.onload = function() {
            setTimeout(function() {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                printFrame.onload = null; // Prevent multiple triggers
            }, 250);
        };
        printFrame.src = contractUrl;
    }

    // Handle click on the print button
    $('.print-btn').on('click', function() {
        var contractUrl = $(this).data('contract-url');
        printContract(contractUrl);
    });

    // More event handlers here...
});
</script>
    <script>
        var propertyId; // Variable to hold the contract ID to delete
        function setDeleteContract(propertyIdParam) {
            propertyId = propertyIdParam; // Set the global contract ID
        }

        $('#confirmDeleteContract').click(function () {
            // Submit the form programmatically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Specify the action if needed

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'property_id';
            idInput.value = propertyId;
            form.appendChild(idInput);

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'deleteContract';
            actionInput.value = '1'; // Value that triggers the delete operation in your PHP
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        });
    </script>


    <script>
        var deleteUserId; // Variable to hold the user ID to delete

        function setDeleteUserId(userId) {
            deleteUserId = userId; // Set the global user ID
        }

        $('#confirmDelete').click(function () {
            // Submit the form programmatically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Specify the action if needed

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'user_id';
            idInput.value = deleteUserId;
            form.appendChild(idInput);

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'deleteUser';
            actionInput.value = '1'; // Value that triggers the delete operation in your PHP
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        });

        $(document).ready(function () {
            // Select all Bootstrap alerts
            $('.alert').each(function () {
                // Set a timeout to fade out the alert
                $(this).delay(3000).fadeOut('slow', function () {
                    $(this).remove(); // Optional: remove the element after fading out
                });
            });
        });
    </script>

</body>

</html>