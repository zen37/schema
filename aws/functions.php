<?php

function connectToDatabase($servername, $username, $password, $dbName) {
    $dbConn = new mysqli($servername, $username, $password, $dbName);

    if ($dbConn->connect_error) {
        die("Connection failed: " . $dbConn->connect_error);
    }
    return $dbConn;
}

function updateDatabases2($dblist, $alterQuery) {
    $user = $_SESSION['adminUserFullName'];

    foreach ($dblist as $db) {
        echo "Database: $db<br>"; // Debug: Output the current database

        $ddllist = customSqlSplitter($alterQuery);

        foreach ($ddllist as $ddl) {
            $ddl = trim($ddl);
            echo "DDL: " . htmlspecialchars($ddl) . '<br>'; // Debug: Output the DDL statement
        }
    }
}


function databaseCheckMissing($databases, $databasePrefix, $conn) {
    $matchingDatabases = getDatabasesWithPrefix($databasePrefix, $conn);
    $missingDatabases = array_diff($databases, $matchingDatabases);
    return $missingDatabases;
}

function getDatabasesWithPrefix($databasePrefix, $conn) {
    
    $sql = "SHOW DATABASES LIKE '$databasePrefix%'";
    $result = $conn->query($sql);

    if ($result) {
        $databaseNames = $result->fetch_all();
        $listdb = [];
            foreach ($databaseNames as $database) {
                $listdb[] = $database[0];
            }
        return $listdb;
    } else {
        return null;
    }
}


function getInputDatabases($input, $databasePrefix) {
    
    $list = explode(",", $input);
    $dblist = array();
    
    foreach ($list as $id) {
        $dblist[] = $databasePrefix . trim($id);
    }
    
    return $dblist;
}

function getDatabasesNotIgnore($ignore, $databasePrefix, $conn) {
    
    $matchingDatabases  = getDatabasesWithPrefix($databasePrefix, $conn);
    $ignoreDatabases    = getInputDatabases($ignore, $databasePrefix);
    $notInIgnore        = array_diff($matchingDatabases, $ignoreDatabases);
    
    return $notInIgnore;
}


function printDatabasesWithPrefix($databasePrefix, $conn) {

    $res = getDatabasesWithPrefix($databasePrefix, $conn);
    
    if (!empty($res)) {
        foreach ($res as $val) {
            $id = getCompanyID($val, $databasePrefix);
            echo $val . ": " . getCompanyInfo($id, $conn) . "<br>";
        }
    } else {
        echo "No databases found with prefix " .$databasePrefix;
    }
}


function getCompanyID($input, $prefix) {
    // Check if the input string starts with the specified prefix
    if (strpos($input, $prefix) === 0) {
        return substr($input, strlen($prefix));
    } else {
        return $input;
    }
}

function getCompanyName($id, $conn) {

    $query = "SELECT companyName FROM infawork_website.infaweb_companies WHERE IWCompanyId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $stmt->bind_result($companyName);

    if ($stmt->fetch()) {
        $stmt->close();
        return $companyName;
    } else {
        return 'name not found';
    }
}

function getCompanyInfo($id, $conn) {
    $query = "SELECT companyName, companyCity, companyState FROM infawork_website.infaweb_companies WHERE companyId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $stmt->bind_result($companyName, $companyCity, $companyState);

    if ($stmt->fetch()) {
        $stmt->close();
        $result = $companyName . ', ' . $companyCity . ', ' . $companyState;
        return $result;
    } else {
        return 'Value not found';
    }
}


function printConfig($filePath) {
    
    if (file_exists($filePath)) {
        
        include($filePath);

        foreach (get_defined_vars() as $name => $value) {
            echo "$name: ";
            if (is_bool($value)) {
                echo $value ? 'true' : 'false'; // handle boolean values
                echo "<br>";
            } elseif (is_array($value)) {
                echo implode(', ', $value); // print array values as a comma-separated list
                echo "<br>";
            } else {
                echo $value; // print other types as-is
            }
            echo "<br>";
        }
        
        echo "<br>";

        $fileConstants = getConstantsFromFile($filePath);
    
        foreach ($fileConstants as $name => $value) {
            echo "$name: $value<br>";
        }
        
    } else {
        echo "File not found: $filePath\n";
    }
    echo "<br>";
}

function getConstantsFromFile($filePath) {
    $constants = [];
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);

        // Match constant definitions in the file
        if (preg_match_all('/define\(["\']([^"\']+)["\'],\s*(["\'][^"\']+["\']|[^,]+)\)/', $fileContent, $matches)) {
            foreach ($matches[1] as $index => $constantName) {
                $constants[$constantName] = eval('return ' . $matches[2][$index] . ';');
            }
        }
    }
    return $constants;
}


function updateDatabases($dblist, $alterQuery) {
    $user = $_SESSION['adminUserFullName'];

    // Save the SQL script to a temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'sql_script');
    file_put_contents($tempFile, $alterQuery);

    // Split SQL statements using a semicolon as the delimiter
    $statements = explode(';', $alterQuery);

    foreach ($dblist as $db) {
        $timestamp = time();

        // Construct the command to execute the script using MySQL command-line tool
        $command = sprintf(
            "mysql -h %s -u %s -p'%s' %s < %s",
            _OIT_WEB_DB_HOST,
            _OIT_WEB_DB_USER,
            _OIT_WEB_DB_PASS,
            $db,
            $tempFile
        );

        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w")   // stderr
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]); // Close stdin
            $output = stream_get_contents($pipes[1]); // Read stdout
            fclose($pipes[1]); // Close stdout
            $error = stream_get_contents($pipes[2]); // Read stderr
            fclose($pipes[2]); // Close stderr

            $returnCode = proc_close($process); // Close process and get the return code

            foreach ($statements as $stmt) {
                $ddl = trim($stmt);
                if (!empty($ddl)) {
                    if ($returnCode === 0) {
                        echo '<span style="color: green;">SUCCESS</span> | ' . "$db | $ddl | $user | $timestamp<br>";
                    } else {
                        echo '<span style="color: red;">FAIL</span> | ' . "$db | $ddl | " . $error . " | $user | $timestamp<br>";
                        break; // Stop processing on the first error
                    }
                }
            }
        }
    }

    // Delete the temporary file
    unlink($tempFile);
}