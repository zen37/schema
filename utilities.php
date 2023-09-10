<?php
function printVariablesFromFile($filePath) {
    echo 'PARAMETERS for ... ';
    if (file_exists($filePath)) {
        include($filePath);

        foreach (get_defined_vars() as $name => $value) {
            echo "$name: ";
            if (is_bool($value)) {
                echo $value ? 'true' : 'false'; // handle boolean values
            } elseif (is_array($value)) {
                echo implode(', ', $value); // print array values as a comma-separated list
            } else {
                echo $value; // print other types as-is
            }
            echo "\n";
        }
    } else {
        echo "File not found: $filePath\n";
    }
    echo "\n";
}


function logMsg($msg, $conn) {

    error_log($msg);

    /*
    $query = "CALL sys.syslog('LOG', '$msg')";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error writing to MySQL error log: " . mysqli_error($conn);
    } else {
        echo "Custom log message added to MySQL error log.";
    }
    */
}


?>
