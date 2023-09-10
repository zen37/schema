<?php

function connectToDatabase($servername, $username, $password, $dbName) {
    $dbConn = new mysqli($servername, $username, $password, $dbName);

    if ($dbConn->connect_error) {
        die("Connection failed: " . $dbConn->connect_error);
    }
    return $dbConn;
}


function updateDatabases($dblist, $alterQuery) {
    
    $user=$_SESSION['adminUserFullName'];
         
    foreach ($dblist as $db) {
                
        $timestamp = time();
        $dbConn=connectToDatabase(_OIT_WEB_DB_HOST, _OIT_WEB_DB_USER, _OIT_WEB_DB_PASS, $db);
                
        $ddllist = explode(DDL_SEP, $alterQuery);
        
            foreach ($ddllist as $ddl) {
                $ddl = trim($ddl);
                // echo "ddl: " . $ddl .'<br>';
                if (!empty($ddl)) {
                    try {
                        if ($dbConn->query($ddl) === TRUE) {
                            echo "SUCCESS|$db|$ddl|$user|$timestamp|<br>";
                        } else {
                            echo "FAIL|$db|$ddl|" . $dbConn->error . "|$user|$timestamp|<br>";
                            $exitLoop = true;
                            break;
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo "FAIL|$db|$ddl|" . $e->getMessage() . "|$user|$timestamp|<br>";
                        $exitLoop = true;
                        break;
                    }
                }
            }
    
        $dbConn->close();
            
        if ($exitLoop) {
        //first error we abort
            echo "Processing stopped";
            break;
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
            echo $val . " ... " . getCompanyInfo($id, $conn) . "<br>";
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
    $query = "SELECT companyName, companyCity, companyState FROM infawork_website.infaweb_companies WHERE IWCompanyId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $stmt->bind_result($companyName, $companyCity, $companyState);

    if ($stmt->fetch()) {
        $stmt->close();
        $result = $companyName . ' ' . $companyCity . ' ' . $companyState;
        return $result;
    } else {
        return 'Record not found';
    }
}


function printConfig($filePath) {
    
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



function stringContains($haystack, $needle) {
    return stripos($haystack, $needle) !== false;
}
?>