<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [Solaris] svc Check, checks by SMF command if a service is online
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Fabrizio Cardarello <hunternet@users.sourceforge.net>
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
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    $result = 0;

    //Get the params required by plugin from argv
    
    $user = $parameters['solaris_zpool_check_user'];
    $port = $parameters['solaris_zpool_check_port'];
    
    // zpool name
    $zpool_name = $parameters['solaris_zpool_check_zpool_name'];
    // threshold
    $threshold = $parameters['solaris_zpool_check_threshold'];
    
    $script_parameters = array($zpool_name, $threshold);
    
    $ip = $job_infos['ip'];
    
    $ssh    = new SSHUtil();
    $path   = dirname($argv[0]);
    $script = $path . '/zpool-check.ksh';
    $local_parameters = implode(' ', $script_parameters);
    
    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $script, true, $local_parameters);
    
    $stdout    = $sshCommandResult['stdout'];
    $exit_code = $sshCommandResult['exit_code'];
    
    if ($exit_code == '0') {
        $result = '1';
    } else {
        $result = '0';
    }
    
    $attachments['txt']  = implode("\n", $stdout);
    $attachments['html'] = implode("<br/>", $stdout);
    
    $loggerPlugin->trace('DEBUG', 'id_job=' . $id_job . "\n" .
                                  'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . $attachments['txt']);
    
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