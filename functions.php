<?php
function listDatabases($databasePrefix, $conn) {
    // List databases based on the provided pattern
    $sql = "SHOW DATABASES LIKE '$databasePrefix'";
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
?>