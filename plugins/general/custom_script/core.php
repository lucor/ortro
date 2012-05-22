<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Custom Script.
 * Execute a custom script or program using ssh.
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

    $bg_execution = false;
    $local        = false;
    
    //Get the params required by plugin from argv
    
    $path_script = $parameters['custom_script_path'];
    $user        = $parameters['custom_script_user'];
    $port        = $parameters['custom_script_port'];

    if ($parameters['custom_script_location'] == 'local') {
        $local = true;
    }
    $ip = $job_infos['ip'];

    $ssh              = new SSHUtil();
    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $path_script, $local);
    $stdout           = $sshCommandResult['stdout'];
    $exit_code        = $sshCommandResult['exit_code'];
        
    $attachments['txt']  = implode("\n", $stdout);
    $attachments['html'] = implode("<br/>", $stdout);
    
    if ($exit_code == '0') {
        $result = '1';
    } else {
        $loggerPlugin->trace('ERROR', 'id_job=' . $id_job . "\n" .
                                      'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . $attachments['txt']);
        $result = '0';
    }
    
    $msg_exec = $attachments['txt'];
    
    //---- Archive job result ----
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['custom_script_retention'];
    
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
