<?php 
session_start();
include_once '../../../../../config/config.php';


// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 3 )) {
    header("location: ../../../../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users u JOIN property p ON u.user_id = p.tenant_id JOIN contracts c ON p.property_id = c.property_id WHERE tenant_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uploadBtn'], $_FILES["fileToUpload"])) {
    if ($_FILES["fileToUpload"]["error"] == 0) {
        $contract_id = $_POST['contract_id'];
        $target_dir = "../../../../../public/img/uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ["pdf"];
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['errors'][] = "Sorry, only PDF files are allowed.";
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to self to show error message
            exit; // It's crucial to use exit after header to stop script execution
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $update_sql = "UPDATE contracts SET contract = ? WHERE contract_id = ?";
            $stmt = $mysqli->prepare($update_sql);
            if ($stmt) {
                $stmt->bind_param("si", $target_file, $contract_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['success'] = "Contract updated successfully.";
                } else {
                    $_SESSION['errors'][] = "No changes were made or the new pdf is already in.";
                }
            } else {
                $_SESSION['errors'][] = "Error preparing statement: " . $mysqli->error;
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['errors'][] = "Error uploading file.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['errors'][] = "File upload error: " . $_FILES["fileToUpload"]["error"];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

function displayAndClearMessages($type) {
    if (!empty($_SESSION[$type])) {
        foreach ($_SESSION[$type] as $message) {
            echo "<div class='alert alert-" . ($type == 'errors' ? 'danger' : 'success') . "'><strong>$message</strong></div>";
        }
        unset($_SESSION[$type]); // Clear the messages after displaying
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Contracts</title>
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../../../../public/css/landing_page.css">
    <link rel="stylesheet" href="../../../../../public/css/dashboard_admin_users.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../../public/img/logo.png">

<style>
    /* Custom CSS to hide DataTables default sorting icons */
    table.dataTable thead .sorting::before, 
    table.dataTable thead .sorting::after,
    table.dataTable thead .sorting_asc::before,
    table.dataTable thead .sorting_asc::after,
    table.dataTable thead .sorting_desc::before,
    table.dataTable thead .sorting_desc::after {
        content: "" !important;
        background-image: none !important;
    }

    /* Custom sorting icons */
    .sorting-icon {
        font-size: 0.8rem;
        color: #333;
        margin-left: 5px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0px !important; /* Remove padding around page numbers */
        margin-left:0px !important;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding: 0px !important;
        margin-left:0px !important;
    }
</style>

</head>
<body>

<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#billTable').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": [2, 3, 4] } // Disable sorting on columns 3, 4, and 5
        ],
        "order": [[0, 'asc']], // Default ordering (ascending) on the first column
        "drawCallback": function(settings) {
            var api = this.api();
            // Clear previous sorting icons
            $('.sorting-icon').html('&#x2195;'); // Reset to default bi-directional arrow
            // Update sorting icons based on current sort order
            api.order().forEach(function(order) {
                var column = order[0];
                var dir = order[1];
                var icon = dir === 'asc' ? '&#x2191;' : '&#x2193;'; // Arrow up or down
                $(api.column(column).header()).find('.sorting-icon').html(icon);
            });
        }
    });

    // Initial setup of sorting icons based on the default order
    $('.sorting-icon').each(function(index) {
        // Set the default sorting icon direction
        $(this).html(index === 0 ? '&#x2191;' : '&#x2195;');
    });
});
</script>
    <!-- Header -->
    <?php include_once '../dashboard_modules_header.php'; ?>
    <!-- Displaying Error Messages -->

<div class="main">
    <div class="user-header d-flex justify-content-between align-items-center">
        <h2 style="margin-left: 14px;">Contract</h2>
    </div>
</div>
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

<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="billTable">
        <thead>
    <tr>
        <th>Start Date</th>
        <th>Expire Date </th>
        <th>Print Contract</th>
        <th>Upload</th>
        <th>Download</th>
    </tr>
</thead>
            <tbody>
                <?php
                // Display the uploaded files and download links
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['start_date'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($row['expire_date'] ?? 'N/A'); ?></td>
                            <td>
                                <!-- Print Contract Button -->
                                <?php if (!empty($row['contract'])): ?>
                                    <button class="btn btn-primary btn-md" onclick="printContractWithCustomFavicon('../../../<?=$row['contract'] ?>').print();">

                                        <i class="bi bi-printer"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-sm" disabled><i class="bi bi-printer"></i></button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editBillModal" data-contractid="<?php echo htmlspecialchars($row['contract_id']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                            <td>
                               
                                <!-- Button to download contract -->
                                <?php if ($row['contract_id']): ?>
                                    <a href="../../../<?=$row['contract']?>"
                                    class="btn btn-success" download>
                                    <i class="fas fa-download"></i> 
                                </a>
                            <?php endif; ?>
                            
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<iframe id="printFrame" style="display: none;"></iframe>

    <!-- Modal for Editing Bills -->
<div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBillModalLabel">Upload Signed Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group col-md-12">
                        <label for="edit_receipt_photo">Upload Contract Photo</label>
                        <div class="input-group">
                            <label class="input-group-btn btn btn-primary">
                                Browse&hellip; <input type="file" style="display: none;" id="fileToUpload" name="fileToUpload">
                            </label>
                            <input type="hidden" name="contract_id" id="modalContractId" value="">
                            <input type="text" class="form-control" id="edit-receipt-file-name" placeholder="No file chosen" readonly style="height: 38px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="uploadBtn">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- jQuery, DataTables, and Bootstrap scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready( function () {
    $('#billTable').DataTable({
        // You can specify which columns to allow sorting on, e.g.:
        "columnDefs": [
           
            { "orderable": true, "targets": [0, 1] },
            { "orderable": false, "targets": [2, 3, 4] }
        ]
    });
});
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var fileInput = document.getElementById('fileToUpload');
            var fileNameInput = document.getElementById('edit-receipt-file-name');

            fileInput.addEventListener('change', function() {
                var files = fileInput.files;
                if (files.length > 0) {
                    fileNameInput.value = files[0].name;
                } else {
                    fileNameInput.value = "No file chosen";
                }
            });
        });

        $(document).ready(function() {
            // Initialize DataTables
            $('#billTable').DataTable();

            // Pass 'contract_id' to the modal when the edit button is clicked
            $('.edit-btn').on('click', function() {
                var contractId = $(this).data('contractid');
                $('#modalContractId').val(contractId);
            });
        });
    </script>
<!-- this is to print the specific page -->
    <script>
        function printContractWithCustomFavicon(contractUrl) {
            var printFrame = document.getElementById('printFrame');

            // Handle the load event of the iframe
            printFrame.onload = function() {
                try {
                    // Wait for the content to be loaded fully
                    setTimeout(function() {
                        // Try to access the iframe's window and print its content
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    }, 500); // Adjust timeout as needed
                } catch (e) {
                    console.error("Error printing the contract: ", e);
                }
            };

            // Set the source of the iframe which triggers the load event
            printFrame.src = contractUrl;
        }
    </script>



    <script>
    
    // $(document).ready(function() {
    //         // Select all Bootstrap alerts
    //         $('.alert').each(function() {
    //             // Set a timeout to fade out the alert
    //             $(this).delay(3000).fadeOut('slow', function() {
    //                 $(this).remove(); // Optional: remove the element after fading out
    //                 window.location();
    //             });
    //         });
    //     });
   
    $(document).ready(function() {
            $('#billTable').DataTable();

            $('.edit-btn').on('click', function() {
                var contractId = $(this).data('contractid');
                $('#modalContractId').val(contractId);
            });

            $('.print-btn').on('click', function() {
                var contractPath = $(this).data('contract-path');
                printContractWithCustomFavicon(contractPath);
            });
    if ($('.alert').length > 0) {
                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() {
                        // After fade out complete
                        $(this).remove();

                        // Check if all alerts are processed
                        if ($('.alert').length === 0) {
                            setTimeout(function() {
                                window.location.reload();  // Reload the page
                            }, 500); // Wait for 500ms after the last alert is removed
                        }
                    });
                }, 3000); // Start fading out the alerts after 3000ms (3 seconds)
            }
        });


</script>
</body>
</html>