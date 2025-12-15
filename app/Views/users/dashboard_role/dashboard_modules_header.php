<?php
include __DIR__ . '/../../../../config/config.php';
$fullName = '';
if (!empty($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $sql = "SELECT CONCAT(name, ' ', surname) AS fullName FROM users WHERE email = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($fullName);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BMExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
    </style>
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-light navmenu">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <a href="<?php echo BASE_URL ?>index.php" style="text-decoration:none;" class="logo d-flex align-items-center">
                    <img src="<?php echo BASE_URL ?>public/img/logo.png" width="50" height="50" alt="Logo of BMExpert">
                    <h1 class="navbar-text">BMExpert</h1>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form id="homeForm" action="<?php echo BASE_URL; ?>app/Controllers/user_controller.php" method="POST">
                                <a class="nav-link active" aria-current="page" href="#" onclick="document.getElementById('homeForm').submit();">Dashboard</a>
                                <input type="hidden" name="homeButtonClicked" value="true">
                            </form>
                        </li>
                        <!-- Mobile view Profile -->
                        <?php if (!empty($fullName)): ?>
                        <li class="nav-item dropdown d-md-none">
                            <a class="nav-link dropdown-toggle" href="#" id="mobileNavbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $fullName; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['role_id'] != 3): ?>
                                    <?php if ($_SESSION['role_id'] == 1): ?>
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>app/Views/landing_page/register.php"><i
                                                    class="fas fa-sliders-h fa-fw"></i> Create Account</a></li>
                                    <?php elseif ($_SESSION['role_id'] == 2): ?>
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>app/Controllers/tenantRegister.php"><i
                                                    class="fas fa-sliders-h fa-fw"></i> Create Account</a></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <li><a class="dropdown-item"
                                        href="<?php echo BASE_URL; ?>app/Views/landing_page/profile.php"><i
                                            class="fas fa-cog fa-fw"></i> Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Models/logout.php"><i
                                            class="fas fa-sign-out-alt fa-fw"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Desktop Profile Dropdown -->
                <?php if (!empty($fullName)): ?>
                <div class="d-none d-md-flex justify-content-end align-items-center">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="desktopNavbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $fullName; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['role_id'] != 3): ?>
                                    <?php if ($_SESSION['role_id'] == 1): ?>
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>app/Views/landing_page/register.php"><i
                                                    class="fas fa-sliders-h fa-fw"></i> Create Account</a></li>
                                    <?php elseif ($_SESSION['role_id'] == 2): ?>
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>app/Controllers/tenantRegister.php"><i
                                                    class="fas fa-sliders-h fa-fw"></i> Create Account</a></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <li><a class="dropdown-item"
                                        href="<?php echo BASE_URL; ?>app/Views/landing_page/profile.php"><i
                                            class="fas fa-cog fa-fw"></i> Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>app/Models/logout.php"><i
                                            class="fas fa-sign-out-alt fa-fw"></i> Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
