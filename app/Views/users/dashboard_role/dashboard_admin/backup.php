<?php
session_start();
include_once '../../../../../config/config.php';

// Setting the backup file location and name
$backup_file = './backUp/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql';

// Building the mysqldump command with the full path to mysqldump for locally
// $command = "\"C:/xampp/mysql/bin/mysqldump\" --user={$username} --password={$password} --host={$servername} {$dbname} > \"{$backup_file}\"";

// For Online Server
$command = "mysqldump --user={$username} --password={$password} --host={$servername} {$dbname} > \"{$backup_file}\"";

// Executing the command and capturing the output
$output = null;
$result = exec($command, $output, $return_value);

// Check the output status and handle the response appropriately
if ($return_value === 0) {
    echo "Backup created successfully.";
    // Inform the user or log this event
} else {
    echo "Error creating backup. Please contact the system administrator.";
    // Inform the user or log this event
}

?>