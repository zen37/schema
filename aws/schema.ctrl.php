<?php
require_once('mustincludes.php');
require_once('schema/functions.php');
require_once('schema/config.php');

$user=$_SESSION['adminUserFullName'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    // check if the "listDatabases" checkbox is checked
    if (isset($_POST["listDatabases"])) {
        $listDatabasesChecked = true;
    } else {
        $listDatabasesChecked = false;
    }

    if ($listDatabasesChecked) {
        printDatabasesWithPrefix(DB_PREFIX, $infwwebdb);
    } 
    
    $alterQuery = $_POST["alterQuery"];
    
    // *** START initial checks ***
    
    //alter  query
    if ((empty($alterQuery)) && !(isset($_POST["listDatabases"]))){
        die("Missing DDL statement");
    }
    
    // check for existence of DROP
    if (!$ALLOW_DROP && preg_match('/\bDROP\b/i', $alterQuery)) {
        die("Settings do not allow DROP");
    }
    
    foreach ($disallowedStatements as $statement) {
        $pattern = '/\b' . preg_quote($statement, '/') . '\b/i';
        if (preg_match($pattern, preg_replace('/\s+/', ' ', $alterQuery))) {
            die("Settings do not allow $statement statements");
        }
    }
    
    if ($UPDATE_ALL_DATABASES && isset($_POST["targetDatabase"]) && trim($_POST["targetDatabase"]) !== "") {
        die("Cannot have target databases if UPDATE_ALL_DATABASES=true");        
    }
    
    if($UPDATE_ALL_DATABASES) {
        
        if (isset($_POST["ignoreDatabase"]) && trim($_POST["ignoreDatabase"]) !== "") {
            
            $ignore = $_POST["ignoreDatabase"];
            
            $dblist = getInputDatabases($ignore, DB_PREFIX);
            //check if ignore databases exist
            $missingDatabases = databaseCheckMissing($dblist, DB_PREFIX, $infwwebdb);
            
            if (!empty($missingDatabases)) {
                die("Missing ignore databases: " . implode(", ", $missingDatabases));
            }
        }
    }  else {
        
        if (isset($_POST["targetDatabase"]) && trim($_POST["targetDatabase"]) !== "") {
            
            $target = $_POST["targetDatabase"];
            
            $dblist = getInputDatabases($target, DB_PREFIX);
            //check if target databases exist
            $missingDatabases = databaseCheckMissing($dblist, DB_PREFIX, $infwwebdb);
        
            if (!empty($missingDatabases)) {
                die("Missing target databases: " . implode(", ", $missingDatabases));
            } 
        }
    }
    
    // *** END initial checks ***
    
    if($UPDATE_ALL_DATABASES) {
        
        if (isset($_POST["ignoreDatabase"]) && trim($_POST["ignoreDatabase"]) !== "") {
            
            $ignore = $_POST["ignoreDatabase"];
            
            $dblist = getDatabasesNotIgnore($ignore, DB_PREFIX, $infwwebdb);
            //print_r($dblist);
        updateDatabases($dblist, $alterQuery);
            
            
        } else {
            if (!empty($alterQuery)) {
             //   die("No target databases entered");
            }            
        }
    }
    
    if(!$UPDATE_ALL_DATABASES) {
    
        if (isset($_POST["targetDatabase"]) && trim($_POST["targetDatabase"]) !== "") {
            
            $target = $_POST["targetDatabase"];
            
            $dblist = getInputDatabases($target, DB_PREFIX);
            updateDatabases($dblist, $alterQuery);
   
        } else {
            if (!empty($alterQuery)) {
                die("No target databases entered");
            }            
        }
    }
    
}

?>