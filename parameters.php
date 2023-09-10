<?php
$listDatabases = true; // Set to true to list databases

//$alterQuery = "ALTER TABLE infa_accounting_ap_automation ADD COLUMN new_field CHAR(1)";
$alterQuery = "ALTER TABLE infa_accounting_ap_automation DROP COLUMN new_field";
//$alterQuery = "DROP TABLE _test";

$targetDatabase = [70];
$ignoreDatabase = [68, 71, 64, 63, 67, 73, 65, 69];
?>