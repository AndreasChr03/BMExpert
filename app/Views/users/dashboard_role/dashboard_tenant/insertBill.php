<?php 


$property_id = 18;

$building_id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {



	$bill_type = $_POST['bill_type'] ?? '';
	$expire_date = $_POST['expire_date'] ?? '';
	$cost = $_POST['cost'] ?? '';
	$bill_image = $_FILES['bill_image']['name'] ?? '';
	$receipt_image = $_FILES['receipt_image']['name'] ?? '';
	$comment = $_POST['comment'];
	$status = "f";
	
	if (isset($_FILES["receipt_image"]) && $_FILES["receipt_image"]["error"] == 0) {
		$target_dir = "images/"; // Change this to the desired directory for uploaded files
		$target_file = $target_dir . basename($_FILES["receipt_image"]["name"]);
		$file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// Check if the file is allowed (you can modify this to allow specific file types)
		$allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");
		if (!in_array($file_type, $allowed_types)) {
			$_SESSION['error'] = "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
		} else {
			// Move the uploaded file to the specified directory
			if (move_uploaded_file($_FILES["receipt_image"]["tmp_name"], $target_file)) {
				// File upload success, now store information in the database
				$filename = $_FILES["receipt_image"]["name"];
				$receipt_image = $target_file;
				
			}	
					
			}
		}
	


		// Check if a file was uploaded without errors
		if (isset($_FILES["bill_image"]) && $_FILES["bill_image"]["error"] == 0) {
			$target_dir = "images/"; // Change this to the desired directory for uploaded files
			$target_file = $target_dir . basename($_FILES["bill_image"]["name"]);
			$file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	
			// Check if the file is allowed (you can modify this to allow specific file types)
			$allowed_types = array("jpg", "jpeg", "png", "gif");
			if (!in_array($file_type, $allowed_types)) {
				$_SESSION['error'] = "Sorry, only JPG, JPEG and PNG files are allowed.";
				header("Location: dashboard_tenant_bills.php");
				exit;
			} else {
				// Move the uploaded file to the specified directory
				if (move_uploaded_file($_FILES["bill_image"]["tmp_name"], $target_file)) {
					// File upload success, now store information in the database
					$filename = $_FILES["bill_image"]["name"];
					$bill_image = $target_file;
					
				}	
					    
				}
			}
				
			
			if(!(empty(trim($receipt_image)))) {
				$status = "p";
			}
			
			if(empty(trim($bill_type)) || empty(trim($expire_date)) || empty(trim($cost))) {
				
			}
			else {
				$sql = "INSERT INTO bills (property_id, building_id, bill_title, bill, receipt, expire_date, cost, status, comment)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $mysqli->prepare($sql);
			if ($stmt === false) {
				$_SESSION['error'] = "Error preparing the statement: " . $mysqli->error;
			} else {
				// Update bind_param to include the correct data types and all parameters
				$stmt->bind_param("iissssdss", $property_id, $building_id, $bill_type, $bill_image, $receipt_image, $expire_date, $cost, $status, $comment);
				if ($stmt->execute()) {
					$_SESSION['message'] = "Upload successful!";
					header("Location: dashboard_tenant_bills.php");
					exit;       
				} else {
					$_SESSION['message'] = 'Upload failed: ' . $stmt->error;
					header("Location: dashboard_tenant_bills.php");
					exit;
			}
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../../../public\css\landing_page.css">
    <link rel="stylesheet" href="../../../../../public\css\dashboard_role.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle JS (includes Popper) -->

</head>
<body>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enter Bill Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="insert_bill" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="bill_type" class="form-label">Bill type</label>
                <select class="form-select" id="bill_type" name="bill_type" required>
                <option value="" selected disabled>Select Bill Type</option>
                <option value="Electricity">Electricity</option>
                <option value="Water">Water</option>
                <option value="Other">Other</option>
                <option value="Common Expenses">Common Expenses</option>
            </select>
            <div class="invalid-feedback">
        Please select the bill type.
    </div>
</div>
          <div class="mb-3">
            <label for="expire_date" class="form-label">Expire Date</label>
            <input type="date" class="form-control" id="expire_date" name="expire_date" required>
            <div class="invalid-feedback">
              Please enter the expire date.
            </div>
          </div>
          <div class="mb-3">
            <label for="cost" class="form-label">Cost</label>
            <input type="number" class="form-control" id="cost" name="cost" required>
            <div class="invalid-feedback">
              Please enter the cost.
            </div>
          </div>
          <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <input type="text" class="form-control" id="comment" name="comment">
            <div class="invalid-feedback">
              Please enter some comments.
            </div>
          </div>
          <div class="mb-3">
            <label for="bill_image" class="form-label">Upload Bill</label>
            <input type="file" class="form-control" id="bill_image" name="bill_image">
            <div class="invalid-feedback">
              Please enter the bill.
            </div>
          </div>
          <div class="mb-3">
            <label for="receipt_image" class="form-label">Upload Receipt</label>
            <input type="file" class="form-control" id="receipt_image" name="receipt_image">
            <div class="invalid-feedback">
              Please enter the receipt.
            </div>
          </div>
          </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="btn btn-primary" form="insert_bill">Save changes</button>
            </div>
        </div>
    </div>
</div>
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
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>