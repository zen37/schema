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

function getDatabasesWithPrefix($conn) {
    global $databasePrefix;
    $sql = "SHOW DATABASES LIKE '$databasePrefix%'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();
        $databases = [];
        foreach ($databaseNames as $database) {
            $databases[] = $database[0];
        }
        return $databases;
    } else {
        return null;
    }
}

function databaseCheckMissing($conn, $databases) {
    $matchingDatabases = getDatabasesWithPrefix($conn);
    $missingDatabases = array_diff($databases, $matchingDatabases);
    return $missingDatabases;
}

function databaseNamePrefixId($databaseIDs) {
    global $databasePrefix;
    $db = [];
    foreach ($databaseIDs as $id) {
        $db[] = $databasePrefix . $id;
    }
    return $db;
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

function updateDatabases($databases, $alterQuery, $conn, $servername, $username, $password, $test) {
    foreach ($databases as $db) {
        $dbConn=connectToDatabase($servername, $username, $password, $db);

        if ($dbConn->query($alterQuery) === TRUE) {
            echo "SUCCESS|$db|$alterQuery\n";
        } else {
            echo "FAIL|$db|$alterQuery|" . $dbConn->error . "\n";
            die('stopping here');
        }
        $dbConn->close();
    }
}

function stringContains($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
}

?>
