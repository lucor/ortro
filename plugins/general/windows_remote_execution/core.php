<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Windows Remote Execution allows you to execute arbitrary command 
 * on a remote Windows machine using winexe command on samba protocol
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
 * @author   Danilo Alfano <ph4ntom@user.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

$current_path = realpath(dirname($argv[0]));
require_once $current_path . '/../../init.inc.php';
@require_once ORTRO_CONF_PLUGINS . 'general_windows_remote_execution.php';
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil   = new CronUtil($request_type);
$job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
$parameters = $job_infos['parameters'];
set_error_handler("errorHandler");

//###### End required core code ######

//---- Start plugin code -----

$loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
$loggerPlugin->trace('INFO', 
                     'Executing job ' . $plugin_name . ' with id=' . $id_job);

//Get the params required by plugin from argv
$user = $job_infos['identity']['username'];
$pwd  = $job_infos['identity']['password'];

$domain  = $parameters['windows_remote_execution_domain'];
$command = $parameters['windows_remote_execution_command'];

$ip          = $job_infos['ip'];
$winexe_path = $conf['windows_remote_execution']['winexe_path'];

// winexe -U DOMAIN/USER%PASSWORD //IP "command"
$cmdLine = $winexe_path . ' -U ' . 
           $domain . '/' . 
           $user . '%' . 
           $pwd . ' //' . 
           $ip . ' "' . 'cmd /C ' . 
           $command . '" 2>&1';
 
exec($cmdLine, $stdout, $exit_code);

if (strpos($stdout[0], 'EPOLL_CTL_ADD') !== false) {
    //remove not useful error message
    array_shift($stdout);
}

$attachments['txt']  = implode("\n", $stdout);
$attachments['html'] = implode("<br/>", $stdout);

if ($exit_code != '0') {
    $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . implode("\n", $stdout));
    
    $result = '0';
} else {
    $result = '1';
}

$msg_exec = $attachments['html'];
$loggerPlugin->trace('INFO', 'cmd='.$cmdLine);
$loggerPlugin->trace('INFO', 'id=' . $id_job . 
                             ' output: ' . $attachments['txt']);
$loggerPlugin->trace('INFO', 'id=' . $id_job . 
                             ' exit_code: ' . $exit_code);
$loggerPlugin->trace('INFO', 'Job ' . $plugin_name . 
                             ' with id=' . $id_job . ' done.');
//---- End plugin code -----

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######
?>