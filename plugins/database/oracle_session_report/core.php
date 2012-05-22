<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Oracle Session Report plugin.
 * Generate session reports.
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
 * @author   Giuseppe De Santis <desantis.g@gmail.com>
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

try {

    //---- Start plugin code -----
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    $result = 0;
    
    include_once 'ioUtil.php';
    include_once 'dbUtil.php';
    
    //Get the params required by plugin from argv
        
    $user             = $job_infos['identity']['username'];
    $pwd              = $job_infos['identity']['password'];
    $tablespaces      = trim($parameters['db_oracle_session_report_tablespaces']);
    $attach_result_as = $parameters['db_oracle_session_report_attach_result'];
    $filename         = $parameters['db_oracle_session_report_file_name'];
    
    if ($filename == '') {
        $filename = 'file';
    }
    
    $ip        = $job_infos['ip'];
    $dbms      = $job_infos['dbms'];
    $sid       = $job_infos['sid'];
    $port      = $job_infos['port'];
    $id_system = $job_infos['id_system'];
    
    $query = 'select RESOURCE_NAME, CURRENT_UTILIZATION, '. 
             'MAX_UTILIZATION, LIMIT_VALUE ' .
             ' from v$resource_limit ' .
             " where resource_name in ('processes','sessions')";
    
    $dbUtil = new DbUtil();
    $dbh    = $dbUtil->dbOpenConn($dbms, $ip, $port, $sid, $user, $pwd);
    $rows   = $dbUtil->dbQuery($dbh, $query, MDB2_FETCHMODE_ASSOC);
    $dbh    = $dbUtil->dbCloseConn($dbh);
    unset($dbh);
    
    if (count($rows)>0) {
        
        $reports_storing_path = ORTRO_REPORTS . $id_system . DIRECTORY_SEPARATOR . 
                                $id_job . DIRECTORY_SEPARATOR;
        
        switch ($attach_result_as) {
        case 'html':
            $th_class = 'class="background-color : #eee; ' .
                        'border-bottom : 1px #bbb solid; ' .
                        'border-right : 1px #bbb solid;' .
                        'text-align : left;' .
                        'font-family : verdana;' .
                        'font-size : 8pt;' .
                        'font-weight : bold;"';
                                  
            $td_class = 'class="font-family : arial, helvetica, sans-serif; ' .
                        'font-size : 10pt;"';
                                  
            include_once 'Pear/HTML/Table.php';
            $table       = new HTML_Table('cellpadding=4 cellspacing=0 border=1');
            $column_name = array_keys($rows[0]);
            $table->addRow($column_name, $th_class, 'TH');
            foreach ($rows as $row) {
                $table_row = array();
                foreach ($column_name as $name) {
                    array_push($table_row, $row[$name]);
                }
                $table->addRow($table_row, '', 'TD', false);
            }
                
            $attachments['html'] = $table->toHTML();
            createHTMLFile($attachments['html'], $reports_storing_path, $filename);
            break;
        
        case 'txt':
            $temp_file = createFileByQuery($rows, 
                                           $reports_storing_path, 
                                           $filename, 
                                           'txt', 
                                           "\t", 
                                           "\n");
                                           
            $attachments['file'] = array($temp_file);
            break;
                
        case 'csv':
            $temp_file = createFileByQuery($rows, 
                                           $reports_storing_path, 
                                           $filename, 
                                           'csv', 
                                           ';', 
                                           "\n");
                                           
            $attachments['file'] = array($temp_file);
            break;
        }
    
        $result   = 1;
        $msg_exec = $result;
        
        //---- Archive job result ----
        $retention_data['archive_mode'] = 'FILESYSTEM';
        $retention_data['retention']    = $parameters['db_oracle_session_report_retention'];
        $retention_data['path']         = $reports_storing_path;
        $retention_data['filename']     = $filename;
        
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