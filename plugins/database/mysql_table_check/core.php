<?php

    /* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

    /**
    * Mysql Check Tables
    * 
    * Runs a 'CHECK TABLE' command against each table in the database.
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
    require_once 'cronUtil.php';

    $plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
    $id_job       = $argv[1];// Get the job id
    $request_type = $argv[2];// Get the type of request

    $cronUtil   = new CronUtil($request_type);
    $job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
    $parameters = $job_infos['parameters'];
    set_error_handler("errorHandler");

    //###### End required core code ######

    try
    {
    
        //---- Start plugin code -----
        include_once 'dbUtil.php';
        
        $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
        $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                     ' with id=' . $id_job);
        
        $result = 0;
    
        //---- Start plugin code -----
        
        //Get the params required by plugin from argv
        $user  = $job_infos['identity']['username'];
        $pwd   = $job_infos['identity']['password'];
        $query = $parameters['db_check_mysql_tables_query'];
        
        $ip   = $job_infos['ip'];
        $dbms = $job_infos['dbms'];
        $sid  = $job_infos['sid'];
        $port = $job_infos['port'];
        
        try {
            $dbUtil = new DbUtil();
            $dbh    = $dbUtil->dbOpenConn($dbms, $ip, $port, $sid, $user, $pwd);
            $query  = 'SHOW TABLES';
            $tables = $dbUtil->dbQuery($dbh, $query);

            $msg_exec = '';
            $result   = 1;
            foreach ($tables as $table) {
                $loggerPlugin->trace('DEBUG', 'About to check table ' . $table[0]);    
                $query       = "check table " . $table[0];    
                $checkResult = $dbUtil->dbQuery($dbh, $query);
                if (($checkResult[0][2] == 'error') || ($checkResult[0][2] == 'warning')) {
                   $msg_exec .=  'Problem Found on With table ' . $table[0] . 
                                 ' status ' . $checkResult[0][2] .
                                 ' Message: ' . $checkResult[0][3] . 
                                 " in Database: $sid Host: $ip\n"; 
                   $loggerPlugin->trace('ERROR', 'Found Problem with table :' . $table[0] . 
                                                 ' status is :' . $checkResult[0][2]); 
                   $result = 0;     
                }
            }

           
            $dbh    = $dbUtil->dbCloseConn($dbh);
            unset($dbh);      

        } catch (Exception $e) {
            $result   = 0;
            $msg_exec = $e->getMessage();
            $loggerPlugin->trace('ERROR', $msg_exec);
        }
        
        $attachments['txt']  = $msg_exec;            
        $attachments['html'] = $msg_exec;
        //---- Archive job result ----
        $retention_data['archive_mode'] = 'DB';
        $retention_data['retention']    = $parameters['db_check_mysql_tables_retention'];
        
        
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