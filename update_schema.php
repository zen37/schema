<?php
// Create connection
$conn = new mysqli($servername, $username, $password);

// Get a list of databases starting with 'infawork_company'
$sql = "SHOW DATABASES LIKE 'infawork_company%'";
$result = $conn->query($sql);

if ($result) {
    $databaseNames = $result->fetch_all();
    foreach ($databaseNames as $database) {
        echo $database[0] . "\n";
    }
} else {
    echo "Query failed: " . $conn->error;
}

$conn->close();
?>
