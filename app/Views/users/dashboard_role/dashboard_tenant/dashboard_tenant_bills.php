<?php
session_start();
include_once '../../../../../config/config.php';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 3 )) {
    header("location: ../../../../../index.php");
    exit;
}

    // Further processing here
    

$user_id = $_SESSION['user_id'];
$sql = "SELECT *FROM bills";
$result = $mysqli->query($sql);

$upload_dir = "../../../../../public/img/uploads/";
//Allowed file types
$allowed_types = array('jpg', 'jpeg', 'png', 'gif');
$property_id = 18;
// Handling bills addition
if (isset($_POST['addBill'])) {
    $buildingId = 0;  // Set directly to 1 as required
    // Collect the bill-related information from the form
    $billTitle = $_POST['bill_title'];
    $cost = $_POST['cost'];
    $billComment = $_POST['comment'];
    $expireDate = $_POST['expire_date'];

    // Initialize photo paths with existing values from hidden inputs or set to default empty if not present
    $billPhotoPath = isset($_POST['existing_bill_photo']) ? $_POST['existing_bill_photo'] : '';
    $receiptPhotoPath = isset($_POST['existing_receipt_photo']) ? $_POST['existing_receipt_photo'] : '';

    // Get file data from form
    $billPhoto = $_FILES['bill_photo'] ?? null;
    $receiptPhoto = $_FILES['receipt_photo'] ?? null;


    // Check if a bill photo was uploaded and validate the file type
    if ($billPhoto && $billPhoto['error'] === UPLOAD_ERR_OK && in_array(pathinfo($billPhoto['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $billPhotoName = uniqid('bill_', true) . '.' . pathinfo($billPhoto['name'], PATHINFO_EXTENSION);
        $billPhotoPath = $upload_dir . $billPhotoName;

        if (move_uploaded_file($billPhoto['tmp_name'], $billPhotoPath)) {
            $_SESSION['success'] .= " Bill file uploaded successfully.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded bill file.";
            // Do not reset $billPhotoPath as it should retain the existing path if the move fails
        }
    }
    // If no file uploaded and no existing path, this stays as the default or existing path

    // Check if a receipt photo was uploaded and validate the file type
    if ($receiptPhoto && $receiptPhoto['error'] === UPLOAD_ERR_OK && in_array(pathinfo($receiptPhoto['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $receiptPhotoName = uniqid('receipt_', true) . '.' . pathinfo($receiptPhoto['name'], PATHINFO_EXTENSION);
        $receiptPhotoPath = $upload_dir . $receiptPhotoName;

        if (move_uploaded_file($receiptPhoto['tmp_name'], $receiptPhotoPath)) {
            $_SESSION['success'] .= " Receipt file uploaded successfully.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded receipt file.";
            // Do not reset $receiptPhotoPath as it should retain the existing path if the move fails
        }
    }
    
    $property_id = 0;
$sql1 = "SELECT * FROM users u JOIN property p ON u.user_id = p.tenant_id WHERE p.tenant_id = ?";
$stmt1 = $mysqli->prepare($sql1);
$stmt1->bind_param("i", $user_id);
$stmt1->execute();

$result = $stmt1->get_result();

// Check if there are results and then loop through to display them
if ($result->num_rows > 0) {
    
    while ($row1 = $result->fetch_assoc()) {
        $property_id = $row1['property_id'];
        
    }
}

$stmt1->close();

    // Prepare SQL statement to insert bill data
    $sql = "INSERT INTO bills (building_id, property_id, bill_title, cost, comment, bill, receipt, expire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisdssss",  $buildingId, $property_id, $billTitle, $cost, $billComment, $billPhotoPath, $receiptPhotoPath, $expireDate);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Bill added successfully.";
        } else {
            $_SESSION['errors'][] = "Error adding bill: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['errors'][] = "Failed to prepare the bill insertion statement.";
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Handling bill deletion
if (isset($_POST['deleteBill'])) {
    $billId = $_POST['bill_id'];

    try {
        $stmt = $mysqli->prepare("DELETE FROM bills WHERE bill_id = ?");
        $stmt->bind_param("i", $billId);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Bill deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Error deleting bill.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['errors'][] = "Cannot delete this bill because it is referenced in other records.";
        } else {
            $_SESSION['errors'][] = "Error deleting bill: " . $e->getMessage();
        }
    } finally {
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    }
}
// Handling bill update
if (isset($_POST['updateBill'])) {
    $billId = $_POST['bill_id'];
    $billTitle = $_POST['bill_title'];
    $cost = $_POST['cost'];
    $billComment = $_POST['comment'];
    $expireDate = $_POST['expire_date'];

    $existingBillPhoto = $_POST['existing_bill_photo'];
    $existingReceiptPhoto = $_POST['existing_receipt_photo'];

    $billPhotoPath = (!empty($_FILES['bill_photo']['name'])) ? $upload_dir . uniqid('bill_', true) . '.' . pathinfo($_FILES['bill_photo']['name'], PATHINFO_EXTENSION) : $existingBillPhoto;
    $receiptPhotoPath = (!empty($_FILES['receipt_photo']['name'])) ? $upload_dir . uniqid('receipt_', true) . '.' . pathinfo($_FILES['receipt_photo']['name'], PATHINFO_EXTENSION) : $existingReceiptPhoto;

    $billPhoto = $_FILES['bill_photo'] ?? null;
    $receiptPhoto = $_FILES['receipt_photo'] ?? null;
    $billPhotoPath = $existingBillPhoto;
    $receiptPhotoPath = $existingReceiptPhoto;

    // Check if a new bill photo was uploaded and validate the file type
    if ($billPhoto && $billPhoto['error'] === UPLOAD_ERR_OK && in_array(pathinfo($billPhoto['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $billPhotoName = uniqid('bill_', true) . '.' . pathinfo($billPhoto['name'], PATHINFO_EXTENSION);
        $billPhotoPath = $upload_dir . $billPhotoName;

        if (move_uploaded_file($billPhoto['tmp_name'], $billPhotoPath)) {
            $_SESSION['success'] = "Bill file uploaded successfully.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded bill file.";
        }
    }

    // Check if a new receipt photo was uploaded and validate the file type
    if ($receiptPhoto && $receiptPhoto['error'] === UPLOAD_ERR_OK && in_array(pathinfo($receiptPhoto['name'], PATHINFO_EXTENSION), $allowed_types)) {
        $receiptPhotoName = uniqid('receipt_', true) . '.' . pathinfo($receiptPhoto['name'], PATHINFO_EXTENSION);
        $receiptPhotoPath = $upload_dir . $receiptPhotoName;

        if (move_uploaded_file($receiptPhoto['tmp_name'], $receiptPhotoPath)) {
            $_SESSION['success'] = "Receipt file uploaded successfully.";
        } else {
            $_SESSION['errors'][] = "Failed to move uploaded receipt file.";
        }
    }

    // Prepare the update statement
    $stmt = $mysqli->prepare("UPDATE bills SET bill_title = ?, cost = ?, comment = ?, bill = ?, receipt = ? , expire_date = ? WHERE bill_id = ?");
    $stmt->bind_param("sdssssi", $billTitle, $cost, $billComment, $billPhotoPath, $receiptPhotoPath, $expireDate , $billId );

    // Execute and check for errors
    if ($stmt->execute()) {
        $_SESSION['success'] = "Bill updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    } else {
        $_SESSION['errors'][] = "Error updating bill: " . $stmt->error;
    }
    $stmt->close();
}
$property_id = 0;
$sql1 = "SELECT * FROM users u JOIN property p ON u.user_id = p.tenant_id WHERE p.tenant_id = ?";
$stmt1 = $mysqli->prepare($sql1);
$stmt1->bind_param("i", $user_id);
$stmt1->execute();

$result = $stmt1->get_result();

// Check if there are results and then loop through to display them
if ($result->num_rows > 0) {
    
    while ($row1 = $result->fetch_assoc()) {
        $property_id = $row1['property_id'];
        
    }
}

$stmt1->close();

$sql = "SELECT bill_id, bill_title, cost, comment, bill, receipt, CREATED, expire_date FROM bills WHERE property_id = ? order by expire_date ";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $property_id);
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
    <title>Reports</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_role.css">
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

  <!-- Bill Table Display -->
<div class="main">
    <div class="user-header d-flex justify-content-between align-items-center">
        <h2 style="margin-left: 14px;">Bills</h2>
        
        
        <button class="btn btn-success" data-toggle="modal" data-target="#addBillModal" style="background-color: #086972; margin-right: 20px;">
            <i class="fas fa-plus"></i> Add Bill
        </button>
    </div>
</div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="billTable">
                <thead>
                <tr>
                    
                    <th>Bill type</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Expire Date</th>
                    <th>Cost</th>
                    <th>Bill Photo</th>
                    <th>Receipt Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            
            
                <?php
                
                
                // Display the uploaded files and download links
                if ($result->num_rows > 0) {
                    $rowNumber = 1;
                    while ($row = $result->fetch_assoc()) {
                        $formattedDate = (new DateTime($row['CREATED']))->format('d/m/Y');

                        $full_comment = htmlspecialchars($row['comment'] ?? '-');
                        $words = explode(' ', $full_comment);
                        if (count($words) > 5) {
                            $comment = implode(' ', array_slice($words, 0, 5)) . '...';
                        } else {
                            $comment = $full_comment;
                        }
                        ?>
                        
                        <tr>
                            <?php if(empty($row['receipt'])) {
                                $update_sql = "UPDATE bills SET status = 'f' WHERE bill_id = ?";
                                $update_stmt = $mysqli->prepare($update_sql);
                                $update_stmt->bind_param("i", $row['bill_id']);
                                $update_stmt->execute();
                    
                                // Reflect the update in the current output data without refetching from the database
                                $row['status'] = 'f';
                            
                            }
                            else {
                                $row['status'] = 'p';
                            }
                                ?>
                            <td><?php echo $row['bill_title']; ?></td>
                            <td><?php if($row['status'] == 'p') {
                                    echo'The bill is paid';
                                }
                                else {
                                    echo 'pending';
                                }?></td>
                                <td><?php echo $row['comment'];?></td>
                                <td><?php echo $row['expire_date'];?></td>
                                <td><?php echo $row['cost'];?></td>
                            
                            
                            <td>
                            <?php if (!empty($row['bill'])): ?>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#imagePreviewModal" data-image-url="<?= htmlspecialchars($row['bill']) ?>">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                                <?php else: ?>
                                <button class="btn btn-primary btn-sm" disabled><i class="bi bi-eye"></i> Preview</button>
                                    <?php endif; ?>
                                    </td>
                                    <td>
                                    <?php if(!empty($row['receipt'])): ?>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#imagePreviewModal" data-image-url="<?= htmlspecialchars($row['receipt']) ?>">
                                            <i class="bi bi-eye"></i> Preview
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-primary btn-sm" disabled><i class="bi bi-eye"></i> Preview
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary edit-btn" data-toggle="modal" data-target="#editBillModal" data-billid="<?= htmlspecialchars($row['bill_id']) ?>" data-title="<?= htmlspecialchars($row['bill_title']) ?>" data-cost="<?= htmlspecialchars($row['cost']) ?>" data-comment="<?= $full_comment ?>" data-bill="<?php echo $row['bill']; ?>" data-receipt="<?php echo $row['receipt']; ?>" data-edit_date="<?php echo $row['expire_date'];?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteConfirmationModal" onclick="setDeleteBillId(<?= $row['bill_id'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                            </tr>                       
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4">No files uploaded yet.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        
    </div>
</div>

    <div class="modal fade" id="addBillModal" tabindex="-1" role="dialog" aria-labelledby="addBillModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBillModalLabel">Add New Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <div class="modal-body">
                        <!-- Form fields for adding bill -->
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="bill_title">Bill Title</label>
                                <input type="text" class="form-control" id="bill_title" name="bill_title" required>
                                <div class="invalid-feedback">
                                    Please enter a valid bill title.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="cost">Cost</label>
                                <input type="number" class="form-control" id="cost" name="cost" required step="0.01">
                                <div class="invalid-feedback">
                                    Please enter a valid cost.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bill_photo">Upload Bill Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="file" style="display: none;" id="bill_photo" name="bill_photo" onchange="updateFileName('bill_photo', 'bill-file-name')">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="bill-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="receipt_photo">Upload Receipt Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="file" style="display: none;" id="receipt_photo" name="receipt_photo" onchange="updateFileName('receipt_photo', 'receipt-file-name')">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="receipt-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="comment">Comment</label>
                                <textarea class="form-control" id="comment" name="comment"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="expire_date">Expire Date</label>
                                <input type="date" class="form-control" id="expire_date" name="expire_date" required></input>
                                <div class="invalid-feedback">
                                    Please select expire date.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="addBill">Add Bill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Bill Modal -->
    <div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBillModalLabel">Edit Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" enctype="multipart/form-data">
                    <input type="hidden" name="bill_id" id="bill_id">
                    <div class="modal-body">
                        <!-- Form fields for editing bill -->
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="edit_bill_title">Bill Title</label>
                                <input type="text" class="form-control" id="edit_bill_title" name="bill_title" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="edit_cost">Cost</label>
                                <input type="number" class="form-control" id="edit_cost" name="cost" step="0.01" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_bill_photo">Upload Bill Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn btn btn-primary">
                                        Browse&hellip; <input type="file" style="display: none;" id="edit_bill_photo" name="bill_photo" onchange="updateFileName('edit_bill_photo', 'edit-bill-file-name')">
                                    </label>
                                    <input type="text" class="form-control" id="edit-bill-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_receipt_photo">Upload Receipt Photo</label>
                                <div class="input-group">
                                    <label class="input-group-btn btn btn-primary">
                                        Browse&hellip; <input type="file" style="display: none;" id="edit_receipt_photo" name="receipt_photo" onchange="updateFileName('edit_receipt_photo', 'edit-receipt-file-name')">
                                    </label>
                                    <input type="text" class="form-control" id="edit-receipt-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for existing photo paths -->
                        <input type="hidden" name="existing_bill_photo" id="existing_bill_photo" value="">
                        <input type="hidden" name="existing_receipt_photo" id="existing_receipt_photo" value="">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_comment">Comment</label>
                                <textarea class="form-control" id="edit_comment" name="comment"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_date">Expire Date</label>
                                <input type="date" class="form-control" id="edit_date" name="expire_date"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateBill">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal for Bill -->
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
                    Are you sure you want to delete this bill? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBill">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="previewImage" src="" alt="Preview Image" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a id="downloadLink" href="#" class="btn btn-primary" download>Download</a>
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
            $('#addBillModal').on('hidden.bs.modal', function (e) {
                // Reset the form inside the modal
                $(this).find('form')[0].reset();

                // Clear validation classes
                $(this).find('.form-control').removeClass('is-invalid').removeClass('is-valid');

                // Reset the form validation state
                $(this).find('.needs-validation').removeClass('was-validated');
            });
        });
        
        var deleteBillId; // Variable to hold the bill ID to delete

        function setDeleteBillId(billId) {
            deleteBillId = billId; // Set the global bill ID
        }

        $('#confirmDeleteBill').click(function() {
            // Submit the form programmatically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Specify the action if needed

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'bill_id';
            idInput.value = deleteBillId;
            form.appendChild(idInput);

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'deleteBill';
            actionInput.value = '1'; // Value that triggers the delete operation in your PHP
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        });
    </script>

    <script type="text/javascript">
        function updateFileName(inputId, outputId) {
            var input = document.getElementById(inputId);
            var fileName = input.files[0].name; // Get the file name
            document.getElementById(outputId).value = fileName; // Set the file name in the text input
        }

        $(document).ready(function() {
            // Function to adjust textarea height
            function adjustTextareaHeight(textarea) {
                textarea.style.height = 'auto'; // Reset height to calculate new scroll height
                textarea.style.height = textarea.scrollHeight + 'px'; // Set new height based on scroll height
            }

            // When the edit modal is shown, adjust the textarea height
            $('#editBillModal').on('shown.bs.modal', function() {
                adjustTextareaHeight(document.getElementById('edit_comment'));
            });

            // Also adjust on input in case you need it to expand as the user types
            $('#edit_comment').on('input', function() {
                adjustTextareaHeight(this);
            });

            // This function is triggered when an edit button is clicked
            $('#billTable').on('click', '.edit-btn', function() {
                var billId = $(this).data('billid');
                var billTitle = $(this).data('title');
                var cost = $(this).data('cost');
                var comment = $(this).data('comment');
                var billPhoto = $(this).data('bill');
                var receiptPhoto = $(this).data('receipt');
                var expireDate = $(this).data('edit_date');

                // Ensure that the hidden fields for existing photos are set
                $('#editBillModal #existing_bill_photo').val(billPhoto);
                $('#editBillModal #existing_receipt_photo').val(receiptPhoto);

                $('#editBillModal #bill_id').val(billId);
                $('#editBillModal #edit_bill_title').val(billTitle);
                $('#editBillModal #edit_cost').val(cost);
                $('#editBillModal #edit_comment').val(comment.replace(/<br\s*[\/]?>/gi, "\n"));
                $('#editBillModal #edit-bill-file-name').val(billPhoto.split('/').pop());
                $('#editBillModal #edit-receipt-file-name').val(receiptPhoto.split('/').pop());
                $('#editBillModal #edit_date').val(expireDate);
            });

        });

        $(document).ready(function() {
            $('#imagePreviewModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var imageUrl = button.data('image-url'); // Get the image URL from data attribute

                var modal = $(this);
                modal.find('#previewImage').attr('src', imageUrl);
                modal.find('#downloadLink').attr('href', imageUrl);
            });
        });

        $(document).ready(function() {
            // Select all Bootstrap alerts
            $('.alert').each(function() {
                // Set a timeout to fade out the alert
                $(this).delay(3000).fadeOut('slow', function() {
                    $(this).remove(); // Optional: remove the element after fading out
                });
            });
        });
    </script>

</body>

</html>
