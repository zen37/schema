<?php
function printVariablesFromFile($filePath) {
    if (file_exists($filePath)) {
        include($filePath);

        foreach (get_defined_vars() as $name => $value) {
            echo "$name: ";
            if (is_bool($value)) {
                echo $value ? 'true' : 'false'; // Handle boolean values
            } elseif (is_array($value)) {
                echo implode(', ', $value); // Print array values as a comma-separated list
            } else {
                echo $value; // Print other types as-is
            }
            echo "\n";
        }
    } else {
        echo "File not found: $filePath\n";
    }
    echo "\n";
}
?>