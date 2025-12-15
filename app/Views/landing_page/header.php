<?php
include __DIR__ . '/../../../config/config.php';
$fullName = '';
$isLoggedIn = !empty($_SESSION["email"]);

if ($isLoggedIn) {
    $email = $_SESSION["email"];
    $sql = "SELECT CONCAT(name, ' ', surname) AS fullName
            FROM users
            WHERE email = ?";
  
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($fullName);
    $stmt->fetch();
    $stmt->close();
}

function getDashboardUrl($role_id) {
    switch ($role_id) {
        case 1:
            return BASE_URL . 'app/Views/users/dashboard_role/dashboard_admin.php';
        case 2:
            return BASE_URL . 'app/Views/users/dashboard_role/dashboard_owner.php';
        case 3:
            return BASE_URL . 'app/Views/users/dashboard_role/dashboard_tenant.php';
        default:
            return BASE_URL . 'app/Views/error404.php';
    }
}
$dashboardUrl = isset($_SESSION['role_id']) ? getDashboardUrl($_SESSION['role_id']) : BASE_URL . 'app/Views/error404.php';
?>

<!-- Include Bootstrap JS with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    .profile-menu .dropdown-menu {
        right: 0;
        left: auto;
    }
    .profile-pic {
        display: flex;
        align-items: center;
        white-space: nowrap;
    }
    .nav-link.dropdown-toggle {
        display: flex;
        align-items: center;
        white-space: nowrap;
    }
    @media (min-width: 768px) {
        .mobile-hidden {
            display: none;
        }
    }
</style>

<header class="header">
    <nav class="navbar navbar-expand-md navbar-light bg-light navmenu">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a style="text-decoration: none;" href="<?php echo BASE_URL ?>index.php" class="logo d-flex align-items-center">
                <img src="<?php echo BASE_URL ?>public/img/logo.png" width="50" height="50" class="d-inline-block align-top" alt="Logo of BMExpert">
                <h1 style=" text-decoration: none;" class="navbar-text">BMExpert</h1>
            </a>

            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links (Collapsible Content) -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="<?php echo BASE_URL; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>app/Views/for_rent/properties.php">For Rent</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>app/Views/landing_page/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>app/Views/landing_page/contact.php">Contact Us</a>
                    </li>
                    <!-- Mobile view Login/Profile -->
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown mobile-hidden">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $fullName; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo $dashboardUrl; ?>"><i class="fas fa-sliders-h fa-fw"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Views/landing_page/profile.php"><i class="fas fa-cog fa-fw"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Models/logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> Log Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item mobile-hidden">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Desktop Login Button / User Profile -->
            <?php if (!$isLoggedIn): ?>
                <div class="d-none d-md-block">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                </div>
            <?php else: ?>
                <div class="d-none d-md-flex justify-content-end align-items-center">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $fullName; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo $dashboardUrl; ?>"><i class="fas fa-sliders-h fa-fw"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Views/landing_page/profile.php"><i class="fas fa-cog fa-fw"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Models/logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>

<?php include 'login_modal.php'; ?>
