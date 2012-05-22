<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File System Check Plugin, allows to check if the specified filesystems 
 * are greater than a specified threshold.
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

try {

    //---- Start plugin code -----
    
    include_once 'sshUtil.php';
    include_once 'Pear/HTML/Table.php';
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    $result = 0;
    
    //Get the params required by plugin from argv
    
    $user           = $parameters['file_system_check_user'];
    $port           = $parameters['file_system_check_port'];
    $dynamic_fields = $parameters['dyn_params'];
    
    $ip = $job_infos['ip'];
    
    $ssh    = new SSHUtil();
    $path   = dirname($argv[0]);
    $script = $path . '/script.sh';
    
    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $script, true);
    
    $stdout    = $sshCommandResult['stdout'];
    $exit_code = $sshCommandResult['exit_code'];
    
    $attachments['txt'] = 'Filesystem'. '  --->  Capacity' . "\n";
    
    $table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';
    
    $table = new HTML_Table($table_attributes);
    $table->addRow(array('Filesystem', 
                         'MountPoint', 
                         'Capacity',
                         'Threshold (%)'), '', 'TH', false);
    $fs_alert = array();    
    if ($exit_code == '0') {
        $result = '1';
        for ($index = 0; $index < sizeof($stdout); $index++) {
            if (preg_match('/^\/.*%$/', $stdout[$index]) == 1) {
                $fs_values = explode(' ', $stdout[$index]);
                if (array_key_exists($fs_values[0], $dynamic_fields)) {
                    $perc = substr($fs_values[2], 0, strlen($fs_values[2])-1);
                    $loggerPlugin->trace('DEBUG', $perc . 
                                                  '>=' . 
                                                  $dynamic_fields[$fs_values[0]]);
                    if ($perc >= $dynamic_fields[$fs_values[0]]) {
                        $alert = true;
                        array_push($fs_alert, $fs_values[0]);
                    }
                    $table->addRow(array($fs_values[0],
                                         $fs_values[1],
                                         $fs_values[2],
                                         $dynamic_fields[$fs_values[0]] . '%'),
                                         '', 'TD', false);
                    $attachments['txt'] .= $fs_values[0] . "  --->  " . 
                                           $fs_values[2] . "\n";
                }
            }
        }
        
        $attachments['html'] = $table->toHTML();
        
        if ($alert) {
            $result = '0';
            $msg    = 'The following filesystem have exceeded ' . 
                      'the specified threshold:';
            
            $attachments['txt']  = $msg . "\n" . implode(' ', $fs_alert) . 
                                   "\n\n" . $attachments['txt'];
            $attachments['html'] = $msg.  "<br/>" . implode(' ', $fs_alert) . 
                                   "<br/><br/>" . $attachments['html'];
        }
        $loggerPlugin->trace('DEBUG', 'id_job=' . $id_job . "\n" .
                                      'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . $attachments['txt']);
    } else {
        $attachments['txt'] = implode("\n", $stdout);
        $loggerPlugin->trace('ERROR', 'id_job=' . $id_job . "\n" .
                                      'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . $attachments['txt']);
        
        $attachments['html'] = implode("<br/>", $stdout);
        
        $result = '0';
    }
    $msg_exec = $attachments['txt'];
    $loggerPlugin->trace('INFO', 'Job ' . $plugin_name . 
                                 ' with id=' . $id_job . ' done.');
    //---- End plugin code -----

} catch (Exception $e) {
    $cronUtil->traceError($plugin_name, $e);
    $msg_exec = "Plugin exception occourred: " . $e->getMessage() . "\n" .
                "Please contact system administrator";
    
}
//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######
?>