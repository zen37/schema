<?php

require_once("includes_update_schema.php");

printVariablesFromFile('flags.php');
printVariablesFromFile('parameters.php');

$conn = connectToServer($servername, $username, $password);

if ($listDatabases)  {
    listDatabases($databasePrefix, $conn);
 }

// *** START initial checks ***

// target databases
if (!$UPDATE_ALL_DATABASES) {
// only if we do NOT update all the databases we check about missing target databases

    $db = databaseNamePrefixId($targetDatabase);
    $missingDatabases = databaseCheckMissing($conn, $db);

    if (!empty($missingDatabases)) {
        die("Missing target databases: " . implode(", ", $missingDatabases));
    } else {
        echo "All target databases are present\n";
    }
    echo "\n";
}

// ignore databases
if ($UPDATE_ALL_DATABASES) {
// only if we update ALL the databases we check about missing ignore databases

    $db = databaseNamePrefixId($ignoreDatabase, $databasePrefix);
    $missingDatabases = databaseCheckMissing($conn, $db);

    if (!empty($missingDatabases)) {
        die("Missing ignore databases: " . implode(", ", $missingDatabases));
    } else {
        echo "All ignore databases are present\n";
    }
    echo "\n";
}

//alter  query
if (!isset($alterQuery)) {
    die("Missing ALTER query");
}

if(!$ALLOW_DROP_COLUMN ) {
//if we are not allowed to drop column, check whether it is present
    if (stringContains($alterQuery, 'DROP COLUMN')) {
        logMsg("ERROR|Settings do not allow DROP COLUMN", $conn);
        die("Settings do not allow DROP COLUMN");
    }
}

if(!$ALLOW_DROP_TABLE ) {
    //if we are not allowed to drop table, check whether it is present
        if (stringContains($alterQuery, 'DROP TABLE')) {
            die("Settings do not allow DROP TABLE");
        }
}
// *** END initial checks ***

if (!$UPDATE_ALL_DATABASES && !empty($targetDatabase)) {
// we update target tables
    $db = databaseNamePrefixId($targetDatabase);
    updateDatabases($db, $alterQuery, $conn, $servername,  $username, $password, $test);
}

if ($UPDATE_ALL_DATABASES && !empty($ignoreDatabase)) {
// we update ALL tables except ignore tables
        $matchingDatabases = getDatabasesWithPrefix($conn);
        $ignoreDatabases = databaseNamePrefixId($ignoreDatabase);
        $notInIgnore = array_diff($matchingDatabases, $ignoreDatabases);
        //echo  implode(", ", $notInIgnore);
        updateDatabases($notInIgnore, $alterQuery, $conn, $servername,  $username, $password, $test);
 }

$conn->close();
?>