<?php
ob_start(); // Start buffering output

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['receipt'])) {
  $bill_id = $_POST['bill_id'];
  $targetDir = "images/";
  $fileName = basename($_FILES["receipt"]["name"]);
  $targetFilePath = $targetDir . $fileName;
  $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

  if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
      if ($_FILES["receipt"]["size"] > 5000000) {
        $_SESSION['message'] = "Sorry, your file is too large.<br>";
      } else {
          if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFilePath)) {
              $sql = "UPDATE bills SET receipt = ? WHERE bill_id = ?";
              $stmt = $mysqli->prepare($sql);
              if ($stmt === false) {
                  echo "Error preparing the statement: " . $mysqli->error;
              } else {
                  $stmt->bind_param("si", $targetFilePath, $bill_id);
                  if ($stmt->execute()) {
                    $_SESSION['message'] = "Upload successful!";
                    header("Location: dashboard_tenant_bills.php");
                    exit;
                  } else {
                    $_SESSION['message'] = 'Upload failed.';
                    header("Location: dashboard_tenant_bills.php");
                    exit;
                  }
              }
          }
      }
  } else {
      $_SESSION['message'] = "Invalid file type.";
  }
}

ob_end_flush(); // End buffering and flush all output
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Receipt</title>
    <!-- Correct jQuery Link -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS and CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form inside Modal -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Select image to upload:</label>
                        <input type="file" class="form-control" id="receipt" name="receipt">
                        <input type="hidden" name="bill_id" id="modalBillId" value="">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // When the modal is about to be shown
    $('#uploadModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var billId = button.data('bill-id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('#modalBillId').val(billId); // Use the extracted bill ID to set the hidden input value
    });
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>