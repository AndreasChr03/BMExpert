<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $servername = 'localhost';
// $username = 'cei326omada5user';
// $password = 'e3BB5nM@g!!1kBAZ';
// $dbname = 'cei326omada5';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cei326omada5";

if (!defined('BASE_URL')) {
	// define('BASE_URL', 'https://cei326-omada5.cut.ac.cy/'); 
    define('BASE_URL', 'http://localhost/BMExpert/');
}

try 
{
	$mysqli = mysqli_connect($servername, $username, $password, $dbname);
} 
catch (Exception $e) 
{
	echo "Service unavailable";
	exit();

}
?>