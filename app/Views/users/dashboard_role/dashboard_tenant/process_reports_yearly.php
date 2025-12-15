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
  // Default to January if not specified
  $currentYear = $_POST['year'] ?? date('Y'); // Use POST to get the year or default to the current year

  $monthlyData = array_fill(0, 12, 0); // Initialize an array with 0 for each month
  $labels = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'); // Month labels
  
  // SQL query to fetch sum of cost for each month in the selected year
  $query = "SELECT MONTH(b.CREATED) as month, SUM(cost) as sum FROM bills b
            JOIN property p ON b.property_id = p.property_id
            JOIN users u ON u.user_id = p.tenant_id
            WHERE YEAR(b.CREATED) = ? AND u.email = ?
            GROUP BY MONTH(b.CREATED)";
  
  $stmt = $mysqli->prepare($query);
  if (!$stmt) {
      echo json_encode(['error' => "Prepare failed: " . $mysqli->error]);
      $mysqli->close();
      exit;
  }
  
  $stmt->bind_param("is", $currentYear,$email);
  $stmt->execute();
  $result = $stmt->get_result();
  
  while ($row = $result->fetch_assoc()) {
      $monthlyData[$row['month'] - 1] = (int)$row['sum']; // Update the corresponding month with the sum
  }
  
  $stmt->close();
  $mysqli->close();
  
  echo json_encode(['data' => $monthlyData, 'labels' => $labels]);
  ?>