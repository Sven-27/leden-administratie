<?php
// start the session to retrieve and view the session variables
session_start();
// See if the session loggedin exists and is true, if not send the user back to login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: index.php");
    die();
}

// Include the controller of member administration
include_once('mvc/controller/controller.php');
$controller = new Controller();

// Check if user can visit page by ID of the user
switch(basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php')) {
    case 'boekjaar':
        if ($_SESSION['ID'] != 2 && $_SESSION['ID'] != 1) {
            header("Location: dashboard.php");
            die();
        }
    break;
    case 'families':
        if ($_SESSION['ID'] != 3 && $_SESSION['ID'] != 1) {
            header("Location: dashboard.php");
            die();
        }
    break;
    case 'leden':
        if ($_SESSION['ID'] != 3 && $_SESSION['ID'] != 1) {
            header("Location: dashboard.php");
            die();
        }
    break;
    case 'contributies':
        if ($_SESSION['ID'] != 2 && $_SESSION['ID'] != 1) {
            header("Location: dashboard.php");
            die();
        }
    break;
    case 'abonnementen':
        if ($_SESSION['ID'] != 2 && $_SESSION['ID'] != 1) {
            header("Location: dashboard.php");
            die();
        }
    break;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Leden administratie</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" id="fontawesome-icons" href="./inc/libraries/fontawesome/css/all.min.css">
    <link rel="stylesheet" id="main-stylesheet" href="./assets/css/main.min.css">

    <!-- Scripts -->
    <script id="dashboard-scripts" src="./assets/js/dashboard.min.js"></script>
</head>
<body>
    <header>
        <section>
          <h1><?php echo basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php'); ?> - Leden-Administratie Paneel</h1>
        </section>
    </header>
    <main>
        <nav>
            <ul>
                <li>
                    <a href="./dashboard.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'dashboard') echo 'class="active"'; ?>><i class="fa-solid fa-house"></i> Dashboard</a>
                </li>
                <?php if ($_SESSION['ID'] === 2 || $_SESSION['ID'] === 1) { ?>
                <li>
                    <a href="./boekjaar.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'boekjaar') echo 'class="active"'; ?>><i class="fa-solid fa-calender"></i> Boekja(a)r(en)</a>
                </li>
                <?php } ?>
                <?php if ($_SESSION['ID'] === 3 || $_SESSION['ID'] === 1) { ?>
                <li>
                    <a href="./families.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'families') echo 'class="active"'; ?>><i class="fa-solid fa-people-group"></i> Familie(s)</a>
                </li>
                <?php } ?>
                <?php if ($_SESSION['ID'] === 3 || $_SESSION['ID'] === 1) { ?>
                <li>
                    <a href="./leden.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'leden') echo 'class="active"'; ?>><i class="fa-solid fa-people-group"></i> Leden</a>
                </li>
                <?php } ?>
                <?php if ($_SESSION['ID'] === 2 || $_SESSION['ID'] === 1) { ?>
                <li>
                    <a href="./contributies.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'contributies') echo 'class="active"'; ?>><i class="fa-solid fa-coins"></i> Contributie(s)</a>
                </li>
                <?php } ?>
                <?php if ($_SESSION['ID'] === 2 || $_SESSION['ID'] === 1) { ?>
                <li>
                    <a href="./abonnementen.php" <?php if (basename(substr($_SERVER['SCRIPT_NAME'], 1), '.php') === 'abonnementen') echo 'class="active"'; ?>><i class="fa-solid fa-coins"></i> Abonnement(en)</a>
                </li>
                <?php } ?>
                <li>
                    <a href="./logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Uitloggen</a>
                </li>
            </ul>
        </nav>
