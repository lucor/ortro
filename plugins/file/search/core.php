<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File Search.
 * Enables searching for files by pattern and 
 * checks for an expected number of matches.
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
    
    $loggerPlugin = new LogUtil($plugin_name,
                                ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 
                         'Executing job ' . $plugin_name . ' with id=' . $id_job);
    
    $result = 0;

    //Get the params required by plugin from argv
    
    $user = $parameters['file_search_user'];
    $port = $parameters['file_search_port'];
    
    // The absolute path of folder to apply retention policy
    $dir_path = '"' . $parameters['file_search_dir_path'] . '"';
    // The search pattern
    $search_for = '"' . 
                  str_replace('*',
                              '\*',
                              $parameters['file_search_search_for']) . 
                  '"';
    // Is it a recursive search? (0 = false, 1 = true)
    $recursive = $parameters['file_search_recursive'];
    // The boolean operator
    $operator = '"' . $parameters['file_search_operator'] . '"'; 
    // The number of occourrence expected
    $expected_occurence = $parameters['file_search_expected_occurrence'];
    // If test is true set job result as success or error
    $on_true_set = $parameters['file_search_is_error'];
    
    $script_parameters = array($dir_path, 
                               $search_for, 
                               $recursive, 
                               $operator, 
                               $expected_occurence);
    
    $ip = $job_infos['ip'];
    
    $ssh    = new SSHUtil();
    $path   = dirname($argv[0]);
    $script = $path . '/script.sh';

    $local_parameters = implode(' ', $script_parameters);
    
    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $script, true, $local_parameters);
    
    $stdout    = $sshCommandResult['stdout'];
    $exit_code = $sshCommandResult['exit_code'];
    
    $attachments['txt']  = implode("\n", $stdout);
    $attachments['html'] = implode("<br/>", $stdout);
    
    $loggerPlugin->trace('DEBUG', 'id_job=' . $id_job . "\n" .
                                  'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . $attachments['txt']);
    
    if ($exit_code == '0') {
        $result = '1';
        if ($on_true_set == 'error') {
            $result = '0';
        }
    } else {
        $result = '0';
        if ($on_true_set == 'error') {
            $result = '1';
        }
    }
    
    
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