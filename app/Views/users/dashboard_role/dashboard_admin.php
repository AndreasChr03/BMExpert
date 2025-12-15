<?php
session_start();

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
if($role_id == 2) {
    header('Location: '. BASE_URL . 'app/Views/users/dashboard_role/dashboard_owner.php');
    exit;
} else if($role_id == 3) {
    header('Location: '. BASE_URL . 'app/Views/users/dashboard_role/dashboard_tenant.php');
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
			<h2>Admin Dashboard</h2>
		</div><!-- End Section Title -->

		<!-- Leitourgies of Dashboard Section - Home Page -->
		<section id="dashboard" class="dashboard">
			<div class="container">
				<div class="row gy-4">

					<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
						<a href="dashboard_admin\dashboard_admin_users.php">
							<div class="dashboard-item d-flex">
								<div class="icon flex-shrink-0"><i class="bi bi-people"></i></div>
								<div>
									<h4 class="title">Users Management</h4>
									<p class="description">Users and Roles Management</p>
								</div>
							</div>
						</a>
					</div><!-- End dashboard Item -->

					<div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
						<a href="dashboard_admin\dashboard_admin_properties.php">
							<div class="dashboard-item d-flex">
								<div class="icon flex-shrink-0"><i class="bi bi-houses"></i></div>
								<div>
									<h4 class="title">Properties</h4>
									<p class="description">Overview of all properties of building</p>
								</div>
							</div>
						</a>
					</div><!-- End dashboard Item -->

					<div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
						<a href="dashboard_admin\dashboard_admin_settings.php">
							<div class="dashboard-item d-flex">
								<div class="icon flex-shrink-0"><i class="bi bi-gear"></i></div>
								<div>
									<h4 class="title">Settings</h4>
									<p class="description">System and Building Settings</p>
								</div>
							</div>
						</a>
					</div><!-- End dashboard Item -->

					<div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
						<a href="dashboard_admin\dashboard_admin_reports.php">
							<div class="dashboard-item d-flex">
								<div class="icon flex-shrink-0"><i class="bi bi-bar-chart-line"></i></div>
								<div>
									<h4 class="title">Reports</h4>
									<p class="description">Reports and Statistics</p>
								</div>
							</div>
						</a>
					</div><!-- End dashboard Item -->

				</div>

                <div>
                    <?php include_once 'calendar/calendar.php'; ?>
                </div>


			</div>

		</section><!-- End Leitourgies Section -->
	</main>

</body>

</html>
