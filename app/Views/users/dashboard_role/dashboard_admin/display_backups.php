<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-3">
        <h1 class="mb-3">Backup Files</h1>
        <ul class="list-group">
            <?php
            $backupPath = 'backUp/';  // Ensure this path is correct
            $files = scandir($backupPath);

            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $filePath = $backupPath . $file;
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    echo $file . ' - Last modified: ' . date("F d Y H:i:s.", filemtime($filePath));
                    echo ' <a href="' . $filePath . '" class="btn btn-success btn-sm" download>Download</a>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
        <a href="dashboard_admin_settings.php" class="btn btn-primary mt-3">Go Back</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
