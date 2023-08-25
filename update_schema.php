<?php
require_once 'connection.php';
require_once 'parameters.php';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($listDatabases) {
    // List databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePattern'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();
        foreach ($databaseNames as $database) {
            echo $database[0] . "\n";
        }
    } else {
        echo "Query failed: " . $conn->error;
    }
}

if ($performUpdates && $targetDatabase === "ALL") {
    // Get a list of databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePattern'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();
        foreach ($databaseNames as $database) {
            $dbName = $database[0];

            // Connect to the specific database
            $dbConn = new mysqli($servername, $username, $password, $dbName);

            if ($dbConn->connect_error) {
                die("Connection failed: " . $dbConn->connect_error);
            }

            // Add a new field to the table 'infa_accounting_ap_automation'
          //  $alterQuery = "ALTER TABLE infa_accounting_ap_automation ADD COLUMN new_field CHAR(1)";

            if ($dbConn->query($alterQuery) === TRUE) {
                echo "$dbName: $alterQuery\n";
            } else {
                echo $dbConn->error . ". $alterQuery\n";
            }


            $dbConn->close();
        }
    } else {
        echo "Query failed: " . $conn->error;
    }
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