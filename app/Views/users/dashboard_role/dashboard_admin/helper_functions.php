<?php
function translateStatus($status) {
    if ($status === 'a') return 'Available';
    if ($status === 'r') return 'Rented';
    return 'Unknown'; // Fallback for any undefined status
}

function translateYesNo($value) {
    if ($value === 'y') return 'Yes';
    if ($value === 'n') return 'No';
    return 'Unknown'; // Fallback for any undefined value
}

function translateParking($value) {
    if ($value === 'c') return 'Covered';
    if ($value === 'u') return 'Uncovered';
    return 'Unknown'; // Fallback for any undefined value
}

// Handling the AJAX request
if (isset($_GET['translate'])) {
    $value = $_GET['value'];
    $type = $_GET['translate'];
    if ($type == 'yesno') {
        echo translateYesNo($value);
    } elseif ($type == 'parking') {
        echo translateParking($value);
    } elseif ($type == 'status') {
        echo translateStatus($value);
    }
}
?>