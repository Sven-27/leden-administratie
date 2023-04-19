<?php
    $servername = "localhost"; // Hostname most of the times localhost
    $username = "root"; // Username of the admin account of the database 
    $password = ""; // Password to the database 
    $database = "ledenadministratie"; // Name of the database

    // Here a connection is attempted with a try catch. This allows your code to continue even if to goes wrong.
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password); // set the PDO error mode to exception
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        // Send back an error message. As a developer, you want to see the error so you also return the getMessage(). ?>
        <div class="message failure">
            <p><?php echo $e->getMessage(); ?></p>
        </div>
    <?php }
