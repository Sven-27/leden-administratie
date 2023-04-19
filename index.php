<?php 
// Start the session to retrieve the login session
session_start();
// Check that the loggedin key exists in the session and that the value is true
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    // If so, redirect the user to the dashboard
    header("Location: dashboard.php");
    die();
}

// Add the file with the connection to the database
include './inc/process/connect.php';

// See if a POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If no login field is provided or its value is not Login, declare the error variable with the error message
    if (!isset($_POST['login']) || $_POST['login'] !== 'Inloggen') $error = 'Foutief formulier verstuurd.';

    // If no email field is included, its value is not filled in or it is not a valid email, declare the error variable with the error message
    if (!isset($_POST['login_email']) || empty($_POST['login_email']) || !filter_var($_POST['login_email'], FILTER_VALIDATE_EMAIL)) $error_email = 'Geen valide e-mail adres ingevoerd.';
    
    //  If no password field is included and its value is not entered, declare the error variable with the error message
    if (!isset($_POST['login_password']) || empty($_POST['login_password'])) $error_password = 'Geen wachtwoord ingevoerd.';

    // Replace the spaces in the login field with nothing as they are not allowed in a password
    $_POST['login_password'] = preg_replace('/\s+/', '', $_POST['login_password']);

    // We implement a try catch here, for if an error comes up then we can catch it
    try {
        // We create a select of the users containing the entered email address
        $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE Email=:email");

        // bind the parameters
        $stmt->bindValue(":email", $_POST['login_email']);

        // Execute the query
        $stmt->execute();
            
        // Check whether even results were found
        if ($stmt->rowCount() > 0) { 
            // Go through the found results
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Check that the password entered matches the one in the database. This is encrypted so we use password_verify
                if (password_verify($_POST['login_password'], $row['Wachtwoord'])) {
                    // If the password matches, we have a match and set the loggedin session to true and redirect the user to the dashboard
                    $_SESSION['loggedin'] = true;
                    $_SESSION['ID'] = intval($row['ID']);
                    header("Location: dashboard.php");
                    die();
                } else {
                    // The password did not match the user we found so we declare the error message in the error variable
                    $error_password = "Wachtwoord is incorrect.";
                }
            }
        } else {
            // No user was found with entered e-mail address so we declare the error variable with the error message
            $error_email = "Er is geen account gevonden met dit email-adres.";
        }
    
    } catch(PDOException $e) {
        // Something went wrong during the try so we need to declare a general error variable stating that something went wrong
        $error = "Er ging iets fout bij het ophalen van de gebruikers.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leden administratie</title>

    <link rel="stylesheet" href="./inc/libraries/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/main.min.css">
</head>
<body>
    <header>
        <section>
          <h1>Leden-Administratie Paneel</h1>
        </section>
    </header>
    <main class="login">
        <section id="login">
            <form id="login" method="post">
                <h2>Inloggen</h2>
                <label for="login_email">
                    E-mail <?php if (isset($error_email)) echo '<span class="error">' . $error_email . '</span>'; ?>
                </label>
                <input type="login_email" name="login_email" id="login_email" placeholder="voorbeeld@google.com" required>
                <label for="login_password">
                    Wachtwoord <?php if (isset($error_password)) echo '<span class="error">' . $error_password . '</span>'; ?>
                </label>
                <input type="password" name="login_password" id="login_password" required placeholder="&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;">
                <input type="submit" value="Inloggen" name="login" id="login_submit">
            </form>
        </section>
    <?php include './inc/templates/footer.php'; ?>
</body>
</html>
