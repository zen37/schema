<?php

function connectToServer($servername, $username, $password) {

    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function connectToDatabase($servername, $username, $password, $dbName) {

    $dbConn = new mysqli($servername, $username, $password, $dbName);

    if ($dbConn->connect_error) {
        die("Connection failed: " . $dbConn->connect_error);
    }

    return $dbConn;

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

            $dbConn=connectToDatabase($servername, $username, $password, $dbName);
            $dbConn->autocommit(false);

            if ($dbConn->query($alterQuery) === TRUE) {
                echo "SUCCESS|$dbName|$alterQuery\n";
            } else {
                echo "FAIL|$dbName|$alterQuery|" . $dbConn->error . "\n";
                $allUpdatesSuccessful = false;
            }

            if ($allUpdatesSuccessful && !$test) {
                echo "commiting\n";
                $dbConn->commit();
            } else {
                echo "rollback\n";
                $dbConn->rollback();
            }
               $dbConn->close();
        }
    } else {
        echo "Query failed: " . $conn->error;
    }
}
?>
