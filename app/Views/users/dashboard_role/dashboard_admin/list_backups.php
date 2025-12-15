<?php
$backupPath = './backUp/'; // Adjust the path to where your backups are stored

$files = array_diff(scandir($backupPath, SCANDIR_SORT_DESCENDING), array('..', '.'));

$result = [];

foreach ($files as $file) {
    if (is_file($backupPath . $file)) {
        // Extract the backup file ID from the file name
        $fileNameParts = explode('_', $file);
        $backupFileId = end($fileNameParts);
        $backupFileId = rtrim($backupFileId, '.sql'); // Remove the .sql extension

        $result[] = [
            'id' => $backupFileId, // Backup file ID
            'name' => $file,
            'date' => date("F d Y H:i:s.", filemtime($backupPath . $file)),
            'url' => "download.php?file=" . urlencode($file) // Safer download script
        ];
    }
}

// Sort files by modification time
usort($result, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

header('Content-Type: application/json');
echo json_encode($result);
?>