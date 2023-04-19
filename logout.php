<?php
// Start session to view session variable
session_start();

// unset the login in session, so that the user is sent to the index
unset($_SESSION['loggedin']);

// To be on the safe side, we destroy the entire session so that all variables are unset
session_destroy();

// Redirect the user to the index to log in again
header("Location: index.php");
die();
?>