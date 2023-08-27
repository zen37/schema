<?php
// Define parameters
$test = true;
$databasePrefix = 'infawork_company_7';
$targetDatabase = array(1, 5, 6);
$listDatabases = true; // Set to true to list databases
$performUpdates = true; // Set to true to perform updates
$targetDatabase = "ALL"; // Set to "ALL" to update all databases
//$alterQuery = "ALTER TABLE infa_accounting_ap_automation ADD COLUMN new_field CHAR(1)";
$alterQuery = "ALTER TABLE infa_accounting_ap_automation DROP COLUMN new_field";
?>