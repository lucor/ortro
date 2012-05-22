<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File Watch.
 * Enables monitoring of the specified file for lines containing a match to the given PATTERN.  
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

$current_path = realpath(dirname($argv[0]));
require_once $current_path . '/../../init.inc.php';
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
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 
                         'Executing job ' . $plugin_name . ' with id=' . $id_job);
    
    $result = 0;

    //Get the params required by plugin from argv
    
    $user = $parameters['file_watch_user'];
    $port = $parameters['file_watch_port'];
    $match_is_error = $parameters['file_watch_is_error'];
    
    $init_size = 0;
    
    if (is_file(ORTRO_TEMP . $id_job)) {
        //get the file size at the last check
        $init_size = file_get_contents(ORTRO_TEMP . $id_job);
    
    }
    
    $loggerPlugin->trace('DEBUG', 'Script parameters: ' . print_r($job_infos, true));
    
    // The absolute path of the file to watch
    $log_file_path = '"' . $parameters['file_watch_path'] . '"';
    
    // The search pattern
    $pattern = '\'' . $parameters['file_watch_pattern'] . '\'';
    
    $script_parameters = array($log_file_path, 
                               $pattern, 
                               $init_size);
    
    $ip = $job_infos['ip'];
    
    $ssh    = new SSHUtil();
    $path   = dirname($argv[0]);
    $script = $path . '/script.sh';
    
    $local_parameters = implode(' ', $script_parameters);
    
    $loggerPlugin->trace('DEBUG', 'Script parameters: ' . print_r($script_parameters, true));

    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $script, true, $local_parameters);
    
    $stdout    = $sshCommandResult['stdout'];
    $exit_code = $sshCommandResult['exit_code'];
    
    
    if ($exit_code == 0) {//an occurence with the pattern search was found
        //store the actual size of the monitored file.
        $actual_size = array_shift($stdout); 
        file_put_contents(ORTRO_TEMP . $id_job, $actual_size);
        
        if ($match_is_error == 1) {//pattern match is an error
            $exit_code = 1;    
        }
    }
    
    if ($exit_code == 2) {//an occurence with the pattern search was not found
        //store the actual size of the monitored file.
        $actual_size = array_shift($stdout); 
        file_put_contents(ORTRO_TEMP . $id_job, $actual_size);
        
        if ($match_is_error == 1) {//pattern match is an error
            $exit_code = 0;
        } else {
            $exit_code = 1;
        }
    }
    
    $loggerPlugin->trace('DEBUG', 'init_size: ' . $init_size);
    $loggerPlugin->trace('DEBUG', 'actual_size: ' . $actual_size);
    
    $attachments['txt'] = implode("\n", $stdout);
    $loggerPlugin->trace('DEBUG', 'id_job=' . $id_job . "\n" .
                                  'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . $attachments['txt']);
    
    if ($exit_code == '0') {
        $result = '1';
    } else {
        $result = '0';
    }
    
    $attachments['html'] = implode("<br/>", $stdout);
    
    $msg_exec = $attachments['txt'];
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