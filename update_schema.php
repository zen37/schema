<?php
require_once 'connection.php';
require_once 'parameters.php';
require_once 'functions.php';

if ($test) {
    echo "TEST parameter is on.\n";
}

$conn = connectToServer($servername, $username, $password);

listDatabases($databasePrefix, $conn);

if ($performUpdates && $targetDatabase === "ALL") {
    updateDatabases($databasePrefix, $alterQuery, $conn, $servername,  $username, $password, $test);
} elseif ($performUpdates && $targetDatabase !== "ALL") {
    // Check if the target database exists
    $sql = "SHOW DATABASES LIKE '$targetDatabase'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $dbName = $targetDatabase;
        // Connect to the specific database
        $dbConn = new mysqli($servername, $username, $password, $dbName);

        if ($dbConn->connect_error) {
            die("Connection failed: " . $dbConn->connect_error);
        }

        // Add a new field to the table 'infa_accounting_ap_automation'

        if ($dbConn->query($alterQuery) === TRUE) {
            echo "$dbName: $alterQuery\n";
        } else {
            echo $dbConn->error . ". $alterQuery\n";
        }

        $dbConn->close();
    } else {
        echo "Database '$targetDatabase' not found.\n";
    }
}

$conn->close();
?>