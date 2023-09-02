<?php

require_once("includes_update_schema.php");

printVariablesFromFile('flags.php');
printVariablesFromFile('parameters.php');

$conn = connectToServer($servername, $username, $password);

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
}

// ignore databases
if ($UPDATE_ALL_DATABASES) {
// only if we update ALL the databases we check about missing ignore databases

    $db = databaseNamePrefixId($ignoreDatabase);
    $missingDatabases = databaseCheckMissing($conn, $db);

    if (!empty($missingDatabases)) {
        die("Missing ignore databases: " . implode(", ", $missingDatabases));
    } else {
        echo "All ignore databases are present\n";
    }
}

//alter  query
if(!$ALLOW_DROP_COLUMN ) {
//if we are not allowed to drop column, check whether it is present
    if (stringContains($alterQuery, 'DROP COLUMN')) {
        die("Settings do not allow DROP COLUMN");
    }
}
// *** END initial checks ***

if (!$UPDATE_ALL_DATABASES && !empty($targetDatabase)) {
    $db = databaseNamePrefixId($targetDatabase);
    updateDatabases($db, $alterQuery, $conn, $servername,  $username, $password, $test);
}


 if ($listDatabases)  {
    listDatabases($databasePrefix, $conn);
 }

$conn->close();
?>