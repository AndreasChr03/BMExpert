<?php
include "../../../../../config/config.php";
session_start();
$email = $_SESSION['email'];
// $servername = 'localhost';
// $username = 'root';
// $password = '';
// $dbname = 'reports';


// try 
// {
// 	$conn = mysqli_connect($servername, $username, $password, $dbname);
// } 
// catch (Exception $e) 
// {
// 	echo "Service unavailable";
// 	exit();

// }
//$id = $_SESSION['id']; 
$month = $_POST['month'] ?? '01';  // Default to January if not specified
$currentYear = date('Y');
$yearsData = [];
$labels = [];

for ($i = 0; $i < 6; $i++) {
    $year = $currentYear - $i;
    $stmt = $mysqli->prepare("SELECT SUM(cost) as sum FROM bills b
    JOIN property p ON b.property_id = p.property_id 
    JOIN users u ON u.user_id = p.tenant_id WHERE MONTH(b.CREATED) = ? AND YEAR(b.CREATED) = ? AND u.email = ? ");//
    $stmt->bind_param("iis", $month, $year,$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $yearsData[] = $row['sum'];
    $labels[] = $year;
}

$stmt->close();
$mysqli->close();

echo json_encode(['data' => $yearsData, 'labels' => $labels]);
?>
