<?php

define("DDL_SEP", ';;');
define("DB_PREFIX", 'infawork_company_');

$ALLOW_DROP = true;

$disallowedStatements = [
    'DROP TABLE',
    'DROP DATABASE',
    'DROP VIEW',
    'DROP PROCEDURE',
   // 'DROP FUNCTION',
    'DROP TRIGGER',
    'DROP EVENT',
   // 'DROP COLUMN',
    'DROP USER',      
    'DROP INDEX',      
    'DROP FOREIGN',    
    'DROP PRIMARY',    
    'DROP KEY',        
    'DROP CONSTRAINT', 
    'DROP UNIQUE',     
    'DROP FULLTEXT',   
    'DROP SPATIAL',    
    'DROP TEMPORARY', 
    'TRUNCATE',
    'DELETE',
    'MODIFY COLUMN'
];


$UPDATE_ALL_DATABASES = false;

?>