<?php
$listDatabases = false; // Set to true to list databases
//$performUpdates = true; // Set to true to perform updates

$alterQuery = "ALTER TABLE infa_accounting_ap_automation ADD COLUMN new_field CHAR(1)";
//$alterQuery = "ALTER TABLE infa_accounting_ap_automation DROP COLUMN new_field";

$targetDatabase = [70, 71, 69];
$ignoreDatabase = [64, 689];
?>