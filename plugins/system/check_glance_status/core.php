<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Check Glance Status Plugin, allows to verify the status of an HP/UX machine.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Marcello Sessa <zodd81@users.sourceforge.net>
 * @author   Danilo Alfano <ph4ntom@users.sourceforge.net> 
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
    
    $user = $parameters['check_glance_status_user'];
    $port = $parameters['check_glance_status_port'];
    
    // The absolute path of glance command
    $dir_path = '"' . $parameters['check_glance_status_dir_path'] . '"';
    // The parameter to pass to the glance command
    $type = '"' . $parameters['check_glance_type'] . '"';
    // The parameter to pass for the number of checks
    $number = '"' . $parameters['check_glance_status_number'] . '"';

    $script_parameters = array($dir_path, $type, $number);
    
    $ip = $job_infos['ip'];
    
    $ssh    = new SSHUtil();
    $path   = dirname($argv[0]);
    $script = $path . '/glance.sh';
    
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

    //---- Archive job result ----
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['check_glance_retention'];
    var_dump($retention_data);         

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