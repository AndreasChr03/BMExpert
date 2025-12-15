<?php
session_start();
include_once __DIR__ . '/../../config/config.php';
require ('fpdf186/fpdf.php');
$loginError = '';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role_id"]!== 2 )) {
    header("location: ../../index.php");
    exit;
}

$role_id = $_SESSION["role_id"];
// Get the user_id from the session
$owner_id = $_SESSION["user_id"];

$tenant_id = $_GET['tenant_id'];
$property_id = $_GET['property_id'];


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $tenant_id = $_POST['tenant_id'];
    $property_id = $_POST['property_id'];
    $rent_amount = $_POST['rent_amount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $ownerIDcard = $_POST['ownerIDcard'];
    $tenantIDcard = $_POST['tenantIDcard'];
    $depositAmount = $_POST['depositAmount'];

    // Validate form data
    if (empty($property_id) || empty($rent_amount) || empty($start_date) || empty($end_date) || empty($ownerIDcard) || empty($tenantIDcard) || empty($depositAmount)) {
        echo "Error: Please fill in all required fields.";
        exit;
    }

    // Fetch property details
    $property_sql = "SELECT p.*, u.name AS owner_name, u.surname AS owner_surname, u.phone_1 AS owner_phone
                     FROM property p
                     JOIN users u ON p.owner_id = u.user_id
                     WHERE p.property_id = ?";
    $property_stmt = $mysqli->prepare($property_sql);
    $property_stmt->bind_param("i", $property_id);
    $property_stmt->execute();
    $property_result = $property_stmt->get_result();
    $property_row = $property_result->fetch_assoc();

    //Fetch tenant details
    $tenant_sql = "SELECT * FROM users WHERE user_id = ?";
    $tenant_stmt = $mysqli->prepare($tenant_sql);
    $tenant_stmt->bind_param("i", $tenant_id);
    $tenant_stmt->execute();
    $tenant_result = $tenant_stmt->get_result();
    $tenant_row = $tenant_result->fetch_assoc();

    //Fetch building details
    $building_sql = "SELECT * FROM building WHERE building_id = ?";
    $building_stmt = $mysqli->prepare($building_sql);
    $building_stmt->bind_param("i", $property_row['building_id']);
    $building_stmt->execute();
    $building_result = $building_stmt->get_result();
    $building_row = $building_result->fetch_assoc();


    if (!$property_row) {
        echo "Error: Property not found.";
        exit;
    }
    if ($property_row['pet'] == 'y')
        $pet_allowed = 'Pets are allowed in the house, provided that the Tenant is responsible for any damages caused by the pets.';
    else
        $pet_allowed = 'Pets or other animals are not allowed in the house.';

    // Check if contract already exists for the property
    $check_contract_sql = "SELECT * FROM contracts WHERE property_id = ?";
    $check_contract_stmt = $mysqli->prepare($check_contract_sql);
    $check_contract_stmt->bind_param("i", $property_id);
    $check_contract_stmt->execute();
    $check_contract_result = $check_contract_stmt->get_result();

    if ($check_contract_result->num_rows > 0) {
        // Contract already exists, display alert message using JavaScript
        echo "<script>alert('Contract already exists for this property.')</script>";
        header("Location: ../../app/Views/users/dashboard_role/dashboard_owner/dashboard_owner_tenants.php");
        exit;
    }

    // Create a new PDF document
    $pdf = new FPDF();
    $pdf->AddPage();

    //Print a 1st page with Logo

    $imageFile = '../../public/img/full-black-logo.png';
    $pdf->Image($imageFile, 45, 20, 130);
    $pdf->Ln(150);
    $pdf->SetFont('Arial', 'B', 30);
    $pdf->Cell(0, 10, 'Tenancy Agreement', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Ln(30);
    $pdf->Cell(0, 10, 'PROPERTY', 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->Cell(0, 10, $property_row['number'], 0, 1, 'C');
    $pdf->AddPage();



    // Set the font and font size
    $pdf->SetFont('Arial', 'B', 16);
    // Add the rental agreement title
    $pdf->Cell(0, 10, 'TENANCY AGREEMENT', 0, 1, 'C');
    // Add a line break
    $pdf->Ln(10);

    // Set the font and font size for the agreement details
    $pdf->SetFont('Arial', '', 12);
    // Add the agreement details
    $pdf->MultiCell(0, 10, 'AGREEMENT made on ' . date('jS \o\f F Y'), 0, 1);
    $pdf->MultiCell(0, 10, 'BETWEEN:', 0, 1);

// Set the font and font size for the owner name

$pdf->MultiCell(0, 10, $property_row['owner_name'] . ' ' . $property_row['owner_surname'] . ' (I.D. No.' . $ownerIDcard . ' ), (Hereinafter referred to as "The Landlord") of the one part, with address ' . $building_row['address'] . $building_row['city'], 0, 1);

$pdf->MultiCell(0, 10, 'AND', 0, 1);

// Set the font and font size for the tenant name
$pdf->MultiCell(0, 10, $tenant_row['name'] . ' ' . $tenant_row['surname'] . ' (I.D. No.' . $tenantIDcard . '), (Hereinafter referred to as "The Tenant") of the other party.', 0, 1);


    // Add the property details
    $pdf->MultiCell(0, 10, 'WHEREAS the Landlord is the owner of the property situated at ' . $building_row['name'] . ', ' . $building_row['address'] . $building_row['city'] . ', Cyprus, and has agreed to let the same to the Tenant.', 0, 1);

    // Calculate expiry date based on contract amount and start date
    $expire_date = $end_date;
    // Calculate contract amount in days
    $contract_amount_in_days = strtotime($expire_date) - strtotime($start_date);

    // Convert contract amount to years
    $contract_amount_in_years = $contract_amount_in_days / (60 * 60 * 24 * 365); // Number of seconds in a year
    $contract_amount = round($contract_amount_in_years, 1); // Round to one decimal place


    // Check if contract amount is a valid number
    if (!is_numeric($contract_amount)) {
        // Handle the case where $contract_amount is not a valid number
        // For example, output an error message or set a default value
        $contract_amount = 0;
    }

    //Line break
    $pdf->Ln(10);
    // Add the term of tenancy to the PDF
    $pdf->SetFont('Arial', 'B', 12); // Bold font
    $pdf->MultiCell(0, 10, 'The term of this tenancy shall be for a period of ' . $contract_amount . ' year(s), starting on ' . date('d/m/Y', strtotime($start_date)) . ' until ' . date('d/m/Y', strtotime($expire_date)) . '.', 0, 1);

    // Add the rent amount to the PDF
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'RENT:', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, 'The Tenant agrees to pay the Landlord the sum of ' . $rent_amount . ' Euro(s) per month, payable in advance on the first day of each month.', 0, 1);

    // Add the terms and conditions from the tenant agreement
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'WHEREAS:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    // Define the terms and conditions
    $terms_and_conditions = [
        "The Landlord agrees to keep the house in a good state of structural repair during the duration of the tenancy.",
        "The Tenant will be liable for all damages to the rented house and must return the house in the same condition as they were assumed at the day of first entry to the house, except for damages due to fair wear and tear.",
        "The Tenant shall be responsible to pay for damages caused through his own negligence, including any damage that may be caused by fire, or flooding, or accidents caused by negligence.",
        "The Tenant is obliged to keep the house in a good condition and allow the Landlord or his representative to enter and inspect the condition of the house, at any decent time pre-agreed by both parties.",
        "The Landlord is responsible to repair any damages to the house, provided that no deliberate misuse has occurred by the Tenant.",
        "The house cannot be sub-let without written authorisation from the Landlord and must only be used for accommodation by the two Tenants signed on this agreement.",
        "No additional family members or temporary guest are allowed to stay in the house without written or verbal authorisation by the Landlord.",
        "Alterations are not allowed to be made without written authority from the Landlord, and in case the Tenant proceeds upon any such alterations without such written authority, the Tenant is responsible for restoring the house to their original state as at the time when he took the house over, unless the Landlord accepts the alterations at the time of handover as hereinabove provided.",
        "The Tenant will pay the electricity bills, water bills, telephone bills, internet bills and once per year, the cost related to garbage in respect of the rented house during the period of the tenancy.",
        "Either party may give a written notice of his intention to terminate the tenancy agreement provided that such notice is given not later than TWO Months before the expiry date of the rental period, otherwise the rental agreement will automatically be extended for One more Year, with the same Tenancy Agreement terms. If the Tenant wishes to leave, the house before the expiry date of this agreement, the Tenant shall notify the Landlord two months before and lose the deposit of" . $depositAmount . " (" . numToWords($depositAmount) . " Euro).",
        "In case of termination of this agreement, the Tenant must allow the Landlord or his Representative to visit and show the house to interested Tenants at any time pre-arranged by both parties.",
        "Failure by the Tenant to pay any rent within seven (7) days of the date on which it became payable, will entitle the Landlord to terminate the Tenancy Agreement and require immediate evacuation and delivery of the rented house and also claim compensation for any loss or damage suffered.",
        "The Deposit of " . $depositAmount . " (" . numToWords($depositAmount) . " Euro received by the Landlord upon signature of this agreement shall be refundable after deducting any damages caused by the Tenant at the end of the rental period, or any unpaid bills relating to the above-mentioned premises.",
        $pet_allowed,
        "The Landlord will deliver to the Tenant the keys, which shall be returned by the Tenant at the expiry of this agreement.",
        "Breach by the Tenant of any of the above conditions, gives permission to the Landlord to cancel the above agreement and demand the evacuation of the property. It should be understood that the Tenant will be responsible for any delayed rents and compensations for breach of any of the above conditions. All the disputable matters with respect to the execution of this agreement shall be settled by and between the two parties in the course of negotiations, or, if they fail to achieve an agreement in those negotiations then the matter will be settled by the court in accordance with the current laws of Cyprus.",
        "This agreement shall be made in two copies having the same legal force, one copy for each party.",
        "All terms of this agreement shall be of the essence.",
    ];

    // Add each term to the PDF
    foreach ($terms_and_conditions as $index => $term) {
        $pdf->MultiCell(0, 10, ($index + 1) . '. ' . $term, 0, 'J');
    }

    // Add signature section
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'IN WITNESS WHEREOF the parties here to have set their hands on the day and year above written.', 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Witness by:', 0, 1);
    $pdf->Cell(0, 10, $property_row['owner_name'] . ' ' . $property_row['owner_surname'] .  ' Landlord', 0, 1);
    $pdf->Cell(0, 10, '(I.D. No. '. $ownerIDcard.')', 0, 1);
    $pdf->Cell(0, 10, $tenant_row['name'] . ' ' . $tenant_row['surname'] . ' Tenant', 0, 1);
    $pdf->Cell(0, 10, '(I.D. No. '. $tenantIDcard.')', 0, 1);


    

    // Save the PDF file to a temporary location
    $temp_file = tempnam(sys_get_temp_dir(), 'rental_agreement_');
    $pdf->Output('F', $temp_file);

    // Define the file path to save the PDF file
    $pdf_file_path = '../../public/img/uploads/rental_agreement_' . $property_id . '_' . uniqid() . '.pdf';

    // Save the PDF file to the specified file path
    $pdf->Output('F', $pdf_file_path);

    // Read the PDF file contents
    $pdf_data = file_get_contents($temp_file);

    // Delete the temporary file
    unlink($temp_file);

    // Insert the contract details into the contracts table
    $created_by = $owner_id;
    $updated_by = $owner_id;
    $created = date('Y-m-d H:i:s');
    $updated = date('Y-m-d H:i:s');

    $insert_sql = "INSERT INTO contracts (property_id, contract, CREATED, CREATED_BY, UPDATED, UPDATED_BY, start_date, expire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $mysqli->prepare($insert_sql);
    $insert_stmt->bind_param("isssssss", $property_id, $pdf_file_path, $created, $created_by, $updated, $updated_by, $start_date, $end_date);

    if ($insert_stmt->execute()) {
        // Display success alert using JavaScript
        $_SESSION['success'] = "Rental agreement generated successfully.";

        // echo "<script>alert('Rental agreement generated successfully.\\nContract details inserted into the contracts table.');</script>";
        // Redirect to dashboard_owner_tenants.php after 3 seconds
        echo "<script>setTimeout(function(){ window.location.href = '../Views/users/dashboard_role/dashboard_owner/dashboard_owner_tenants.php'; }, 1500);</script>";
    } else {
        // Display error alert using JavaScript
        $_SESSION['error'] = "Error inserting contract details: " . $insert_stmt->error;
        // echo "<script>alert('Error inserting contract details: " . $insert_stmt->error . "');</script>";
    }



    $insert_stmt->close();
}


// Function to convert numbers to words
function numToWords($num)
{
    $ones = array(
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen'
    );
    $tens = array(
        2 => 'twenty',
        3 => 'thirty',
        4 => 'forty',
        5 => 'fifty',
        6 => 'sixty',
        7 => 'seventy',
        8 => 'eighty',
        9 => 'ninety'
    );
    $word = '';
    if ($num < 20) {
        $word = $ones[$num];
    } elseif ($num < 100) {
        $tens_digit = floor($num / 10);
        $ones_digit = $num % 10;
        $word = $tens[$tens_digit];
        if ($ones_digit > 0) {
            $word .= '-' . $ones[$ones_digit];
        }
    } else {
        $word = numToWords(floor($num / 100)) . ' hundred';
        $remaining = $num % 100;
        if ($remaining > 0) {
            $word .= ' and ' . numToWords($remaining);
        }
    }
    return ucwords($word);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Agreement</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../public/img/logo.png">
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../public/css/landing_page.css">
    <link rel="stylesheet" href="../../public/css/dashboard_admin_users.css">
    <link rel="stylesheet" href="../../public/css/dashboard_role.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Flatpickr-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>
<style>
    white-background!important{
        background-color: #fff !important;
    }
</style>

<body>


    <body id="page-top" style="background-color: #eeeeee">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container" style="padding-top: 20px;">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-2">
                                    <div class="card-header">
                                        <h3 class="text-center font-weight-light my-4">Rental Agreement Creation</h3>
                                        <span id="message"></span>
                                    </div>
                                    <div class="card-body">
                                        <!-- Displaying Success Messages -->
                                        <?php if (!empty($_SESSION['success'])): ?>
                                            <div class='alert alert-success'><strong><?= $_SESSION['success']; ?></strong>
                                            </div>
                                            <?php unset($_SESSION['success']); ?>
                                        <?php endif; ?>
                                        <!-- Close (X) Icon -->
                                        <a href="../Views/users/dashboard_role/dashboard_owner/dashboard_owner_tenants.php"
                                            class="btn btn-light" style="position: absolute; right: 10px; top: 10px;">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                        <form class="row g-3 needs-validation" method="post"
                                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?property_id=' . urlencode($property_id) . '&tenant_id=' . urlencode($tenant_id); ?>"
                                            novalidate>
                                            <!-- Hidden input field to pass property_id -->
                                            <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                                            <input type="hidden" name="tenant_id" value="<?php echo $tenant_id; ?>">
                                            <div class="col-md-6">
                                                <label for="ownerIDcard" class="form-label">Owner ID Card</label>
                                                <input type="text" class="form-control" id="ownerIDcard"
                                                    name="ownerIDcard" required pattern="[0-9]{6,7}">
                                                <div class="invalid-feedback">Please enter a valid owner's ID card
                                                    number (6-7 digits).</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="tenantIDcard" class="form-label">Tenant ID Card</label>
                                                <input type="text" class="form-control" id="tenantIDcard"
                                                    name="tenantIDcard" required pattern="[0-9]{6,7}">
                                                <div class="invalid-feedback">Please enter a valid tenant's ID card
                                                    number (6-7 digits).</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="depositAmount" class="form-label
                                                    ">Deposit Amount </label><span> &#8364;</span>
                                                <input type="text" class="form-control" id="depositAmount"
                                                    name="depositAmount" required>
                                                <div class="invalid-feedback">Please enter the deposit amount.</div>
                                            </div>

                                            <div class="col-md-12">
                                                <label for="rent_amount" class="form-label">Rent Amount
                                                    (Monthly) </label><span> &#8364;</span>
                                                <input type="text" class="form-control" id="rent_amount"
                                                    name="rent_amount" required>
                                                <div class="invalid-feedback">Please enter the rent amount.</div>
                                            </div>
                                            <div class="col-md-6">
  <label for="start_date" class="form-label white-background" >Start Date</label>
  <input type="text" class="form-control white-background" id="start_date" name="start_date" required>
  <div class="invalid-feedback">Please enter the start date.</div>
</div>
<div class="col-md-6" >
  <label for="end_date" class="form-label">End Date</label>
  <input type="text" class="form-control white-background" id="end_date" name="end_date" required>
  <div class="invalid-feedback">Please enter the end date.</div>
</div>


                                            <div class="col-12">
                                                <div
                                                    class="d-flex align-items-center justify-content-center mt-4 mb-0 w-100">
                                                    <button class="btn btn-primary w-100" id="registerSubmit"
                                                        name="submit" type="submit">Create</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </main>
        </div>
        </div>

        <script>
            // Disable form submissions if there are invalid fields
            (function () {
                'use strict';
                window.addEventListener('load', function () {
                    // Get the forms we want to add validation styles to
                    var forms = document.getElementsByClassName('needs-validation');
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function (form) {
                        form.addEventListener('submit', function (event) {
                            // Perform custom validation
                            var startDate = new Date(document.getElementById('start_date').value).getTime();
                            var endDate = new Date(document.getElementById('end_date').value).getTime();

                            if (startDate > endDate) {
                                alert('Start date cannot be later than end date');
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            var ownerIDcardInput = document.getElementById('ownerIDcard');
                            var tenantIDcardInput = document.getElementById('tenantIDcard');

                            ownerIDcardInput.addEventListener('input', function () {
                                this.value = this.value.replace(/\D/g, '');
                            });

                            tenantIDcardInput.addEventListener('input', function () {
                                this.value = this.value.replace(/\D/g, '');
                            });

                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();
        </script>
        
        <script>
document.addEventListener('DOMContentLoaded', function () {
  flatpickr("#start_date", {
    altInput: true,
    altFormat: "d-m-y"
  });
  flatpickr("#end_date", {
    altInput: true,
    altFormat: "d-m-y"
  });
});


        </script>


    </body>

</html>