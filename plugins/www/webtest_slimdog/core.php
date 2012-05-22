<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WebTest Slimdog Plugin.
 * allows to check your webapps using the Slimdog libraries
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
@require_once ORTRO_CONF_PLUGINS . 'www_webtest_slimdog.php';
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
$loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                             ' with id=' . $id_job);

//Get the params required by plugin from argv
$user = $job_infos['identity']['username'];
$pwd  = $job_infos['identity']['password'];

$input_script = $parameters['webtest_input_script'];

//Replace username e password in the input_script code
$input_script = str_replace('${USERNAME}', $user, $input_script);
$input_script = str_replace('${PASSWORD}', $pwd, $input_script);

$path     = dirname($argv[0]);    
$tempFile = 'webtest_' . time() . rand();
$tempDir  = $path . DIRECTORY_SEPARATOR;

$fh = fopen($tempDir . $tempFile, 'w+');
fwrite($fh, $input_script);
fclose($fh);
chmod($tempDir . $tempFile, 0777);

$cmdLine = $path . '/script.sh "' . $conf['webtest']['java_path'] . '" "' . 
           $conf['webtest']['slimdog_path'] . '" -f "' . 
           $tempDir . $tempFile . '" 2>&1';
            
$loggerPlugin->trace('ERROR', $cmdLine);
exec($cmdLine, $stdout, $exit_code);

@unlink($tempDir . $tempFile);

$attachments['txt']  = implode("\n", $stdout);
$attachments['html'] = implode("<br/>", $stdout);

if ($exit_code != '0') {
    $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . implode("\n", $stdout));
    $result = '0';
} else {
    if ((strpos($attachments['txt'], 'Failed') !== false)) {
        $result = '1';
    } else {
        $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . implode("\n", $stdout));
        $result = '0';
    }
    
}

$msg_exec = $attachments['txt'];
//---- End plugin code -----

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######
?>