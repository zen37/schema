<?php
// Define parameters
$databasePattern = 'infawork_company_7%';
$listDatabases = false; // Set to true to list databases
$performUpdates = true; // Set to true to perform updates
$targetDatabase = "ALL"; // Set to "ALL" to update all databases
$alterQuery = "ALTER TABLE infa_accounting_ap_automation ADD COLUMN new_field CHAR(1)";
//$alterQuery = "ALTER TABLE infa_accounting_ap_automation DROP COLUMN new_field";
?>