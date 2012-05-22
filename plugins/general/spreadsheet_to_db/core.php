<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Spreadsheet2Database.
 * Imports the contents of a spreadsheet into a database.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

require_once realpath(dirname($argv[0])) . '/../../init.inc.php';
set_include_path(realpath(dirname($argv[0])) . "/lib/:".ini_get("include_path"));
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil   = new CronUtil($request_type);
$job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
$parameters = $job_infos['parameters'];
set_error_handler("errorHandler");

//###### End required core code ######

try {

    //---- Start plugin code -----
    include_once 'dbUtil.php';
    include_once 'Pear/MDB2/Schema.php';
    
    $loggerPlugin = new LogUtil($plugin_name,
                                ORTRO_LOG_PLUGINS . $plugin_name);
    $log_prefix   = '[job: ' . $id_job . '] ';
    $loggerPlugin->trace('INFO', $log_prefix . 'Executing job');
    
    $result = 0;

    //---- Start plugin code -----
    
    //Get the params required by plugin from argv
    $user      = $job_infos['identity']['username'];
    $pwd       = $job_infos['identity']['password'];
    $id_system = $job_infos['id_system'];
    
    $query            = $parameters['db_custom_query_query'];
    $table_name       = $parameters['db_spreadsheet_to_db_table_name'];
    $table_definition = '<?xml version="1.0" encoding="UTF-8"?>
                         <database><name><variable>db_name</variable></name>
                           <table><name><variable>table_name</variable></name>
                             <declaration>' .
                             $parameters['db_spreadsheet_to_db_table_definition'] .
                             '</declaration>
                           </table>
                        </database>';
    
    $spreadsheet_filename = $parameters['db_spreadsheet_to_db_spreadsheet_filename'];
    
    if ($parameters['febo_spreadsheet_to_db_field_with_quote'] == 'escape') {
        $replace_quote_with = "\'";
    } else {
        $replace_quote_with = '';
    }
    
    $ip   = $job_infos['ip'];
    $dbms = $job_infos['dbms'];
    $sid  = $job_infos['sid'];
    $port = $job_infos['port'];
    
    //Generate the xml database schema file
    $path             = dirname($argv[0]);    
    $temp_file_prefix = $path . DS . $id_job . time() . rand();
    $temp_file_xml    = $temp_file_prefix . '.xml';
    
    try {
    
        //Generate the database schema file
        $fh = fopen($temp_file_xml, 'w+');
        fwrite($fh, $table_definition);
        fclose($fh);

        //check for existing file        
        $spreadsheet_full_path = ORTRO_INCOMING . $id_system . DS . 
                                 $spreadsheet_filename;
        if (!file_exists($spreadsheet_full_path)) {
            $error_msg = 'The file "' . 
                         $spreadsheet_full_path . 
                         '" does not exist...';
            throw new Exception($error_msg);
        }
        $loggerPlugin->trace('DEBUG', $log_prefix . 
                             'File found: ' . 
                             $spreadsheet_full_path . 
                             ' ...');
    
        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConn($dbms, $ip, $port, $sid, $user, $pwd);
        
        $query_drop_if_exists_table = 'drop table ' . $table_name;
        
        $loggerPlugin->trace('DEBUG', $log_prefix . 
                                      'Trying to drop table ' . 
                                      $table_name . ' ...');
        $result = $dbh->exec($query_drop_if_exists_table);
        
        if (PEAR::isError($result)) {
            $loggerPlugin->trace('DEBUG', $log_prefix . 
                                          'Table ' . 
                                          $table_name . 
                                          ' does not exist continue...');
        } else {
            $loggerPlugin->trace('DEBUG', $log_prefix . 
                                          'Table ' . 
                                          $table_name . 
                                          ' dropped...');
        }
        
        $options = array('quote_identifier' => true);
        $schema  =& MDB2_Schema::factory($dbUtil->getDSN(), $options);
        if (PEAR::isError($schema)) {
            $loggerPlugin->trace('ERROR', $log_prefix . 
                                          $schema->getMessage() . '\n' . 
                                          $schema->getDebugInfo());
        }
        
        $variables = array('db_name'=> $sid, 'table_name'=> $table_name);
                   
           $result = $schema->updateDatabase($temp_file_xml, false, $variables);
        if (PEAR::isError($result)) {
            $loggerPlugin->trace('ERROR', $log_prefix . 
                                          $result->getMessage() . '\n' . 
                                          $result->getDebugInfo());
        } else {
            $loggerPlugin->trace('DEBUG', $log_prefix .  
                                          'Table ' . 
                                          $table_name . 
                                          ' created...');
        }
        //remove xml schema
        @unlink($temp_file_xml);
        
        include_once 'Excel/reader.php';
        // ExcelFile($filename, $encoding);
        $data = new Spreadsheet_Excel_Reader();
        // Set output Encoding.
        $data->setOutputEncoding('CP1251');
        $data->read($spreadsheet_full_path);
        
        $rows  = $data->sheets[0]['numRows'];
        $cols  = $data->sheets[0]['numCols'];
        $cells = $data->sheets[0]['cells'];
        $loggerPlugin->trace('DEBUG', $log_prefix . 
                                      'rows: ' . 
                                      $rows . 
                                      ' cols: ' . 
                                      $cols);
        for ($i = 1; $i <= $rows; $i++) {
            $line = '';
            for ($j = 1; $j < $cols; $j++) {
                $line .= str_replace("'", 
                                     $replace_quote_with, 
                                     $cells[$i][$j]) . '","';
            }
            $query_insert_row = 'insert into ' . 
                                $table_name . 
                                ' values("' . $line . '")';
            $dbUtil->dbExec($dbh, $query_insert_row);
        }
        
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        $result = 1;
        
        $msg_exec = 'OK';
    } catch (Exception $e) {
        $result   = 0;
        $msg_exec = $e->getMessage();
        $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
    }
    
    //---- End plugin code -----

} catch (Exception $e) {
    $cronUtil->traceError($plugin_name, $e);
    $msg_exec = "Plugin exception occourred: " . $e->getMessage() . "\n" .
                "Please contact system administrator";
    
}

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
if ($retention_data['retention'] > 0 && is_numeric($retention_data['retention'])) {
    //apply retention policy
    $cronUtil->archiveJobResult($id_job, $retention_data);
}
//###### End required core code ######
?>