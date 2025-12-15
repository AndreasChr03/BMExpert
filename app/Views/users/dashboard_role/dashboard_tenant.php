<?php
session_start();
$user_id = $_SESSION['user_id'];
include_once '../../../../config/config.php';

// Check if the user is not logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../../../index.php");
    exit;
}


$loginError = '';
if ($_SESSION["loggedin"] == false) {
    header("Location: index.php");
    exit();
}
$role_id = $_SESSION["role_id"];

// Handle redirect for other functions.
if($role_id == 1) {
    header('Location: '. BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php');
    exit;
} else if($role_id == 2) {
    header('Location: '. BASE_URL . 'app/Views/users/dashboard_role/dashboard_owner.php');
    exit;
} //end if

mysqli_close($mysqli);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tab Icon -->
    <link rel="icon" href="../../../../public/img/logo.png">
     <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<link rel="stylesheet" href="../../../../public/css/landing_page.css">
	<link rel="stylesheet" href="../../../../public/css/dashboard.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet"> 
</head>

<body>
    <!-- Header -->
    <?php include_once 'dashboard_header.php'; ?>
    
<main class="container">
<!--  Section Title -->
<div class="container dashboard-title" data-aos="fade-up">
	<h2>Tenant Dashboard</h2>
</div><!-- End Section Title -->

<!-- Leitourgies of Dashboard Section - Home Page -->

<?php 
	$stmt = $mysqli->prepare("SELECT * FROM property WHERE tenant_id = ?"); 
	$stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
?>
<section id="dashboard" class="dashboard">
	<div class="container">
		<div class="row gy-4">
		
		<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
		<a href="dashboard_tenant\dashboard_tenant_property.php?property_id=<?php echo $row['property_id'];?>">
					<div class="dashboard-item d-flex">
						<div class="icon flex-shrink-0"><i class="bi bi-house"></i></div>
						<div>
							<h4 class="title">Property Details</h4>
							<p class="description">View Property and Rental Info</p>
						</div>
					</div>
				</a>
			</div>

			<div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
				<a href="dashboard_tenant\dashboard_tenant_bills.php">
					<div class="dashboard-item d-flex">
						<div class="icon flex-shrink-0"><i class="bi bi-receipt"></i></div>
						<div>
							<h4 class="title">Bills Management</h4>
							<p class="description">View/Pay Bills</p>
						</div>
					</div>
				</a>
			</div>

			<!-- <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
				<a href="dashboard_tenant\dashboard_tenant_requests.php">
					<div class="dashboard-item d-flex">
						<div class="icon flex-shrink-0"><i class="bi bi-question-square"></i></div>
						<div>
							<h4 class="title">Requests</h4>
							<p class="description">Submit Requests to Owner</p>
						</div>
					</div>
				</a>
			</div> -->

			<div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
				<a href="dashboard_tenant\dashboard_tenant_reports_monthly.php">
					<div class="dashboard-item d-flex">
						<div class="icon flex-shrink-0"><i class="bi bi-bar-chart-line"></i></div>
						<div>
							<h4 class="title">Reports</h4>
							<p class="description">View Rental Payment & Bill Reports</p>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-6 " data-aos="fade-up" data-aos-delay="300">
						<a href="dashboard_tenant\dashboard_tenant_contract.php">
							<div class="dashboard-item d-flex">
								<div class="icon flex-shrink-0"><i class="bi bi-person-gear"></i></div>
								<div>
									<h4 class="title">Tenant Management</h4>
									<p class="description">View Contract Info </p>
								</div>
							</div>
						</a>
					</div><!-- End dashboard Item -->

                    <?php include_once 'calendar/calendar.php'; ?>

    </div>

	</div>

</section><!-- End Leitourgies Section -->
</main>

</body>
</html>
