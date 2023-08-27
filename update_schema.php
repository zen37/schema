<?php
require_once 'connection.php';
require_once 'parameters.php';
require_once 'functions.php';

$conn = connectToDatabase($servername, $username, $password);

listDatabases($databasePrefix, $conn);

if ($performUpdates && $targetDatabase === "ALL") {
    // Get a list of databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePrefix'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();

        //initialize a flag to track the success of updates
        $allUpdatesSuccessful = true;

        foreach ($databaseNames as $database) {
            $dbName = $database[0];

            // Connect to the specific database
            $dbConn = new mysqli($servername, $username, $password, $dbName);

            if ($dbConn->connect_error) {
                die("Connection failed: " . $dbConn->connect_error);
            }

            // Begin a transaction
            $dbConn->autocommit(false);

            if ($dbConn->query($alterQuery) === TRUE) {
                echo "SUCCESS|" .  $dbName . "|" . $alterQuery . "\n";
            } else {
                echo "FAIL|" .  $dbName . "|" . $alterQuery . "|" . $dbConn->error . "\n";
                $allUpdatesSuccessful = false;
            }

            // Commit or rollback based on overall success
            if ($allUpdatesSuccessful) {
                if (!$test) {
                    $dbConn->commit();
                }
            } else {
                $dbConn->rollback();
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