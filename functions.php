<?php

function connectToServer($servername, $username, $password) {
    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function listDatabases($databasePrefix, $conn) {
    // List databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePrefix%'";
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

function updateDatabases($databasePrefix, $alterQuery, $conn, $servername, $username, $password, $test) {
    // Get a list of databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePrefix%'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();

        // Initialize a flag to track the success of updates
        $allUpdatesSuccessful = true;

        foreach ($databaseNames as $database) {
            $dbName = $database[0];
            $dbConn = new mysqli($servername, $username, $password, $dbName);

            if ($dbConn->connect_error) {
                die("Connection failed: " . $dbConn->connect_error);
            }

            // Begin a transaction
            $dbConn->autocommit(false);

            if ($dbConn->query($alterQuery) === TRUE) {
                echo "SUCCESS|$dbName|$alterQuery\n";
            } else {
                echo "FAIL|$dbName|$alterQuery|" . $dbConn->error . "\n";
                $allUpdatesSuccessful = false;
            }

            // Commit or rollback based on overall success
            if ($allUpdatesSuccessful && !$test) {
                $dbConn->commit();
            } else {
                $dbConn->rollback();
            }

               $dbConn->close();
        }
    } else {
        echo "Query failed: " . $conn->error;
    }
}
?>
