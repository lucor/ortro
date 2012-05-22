<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WebTest Selenium Plugin, allows to check your webapps 
 * using the Selenium RC libraries
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
@require_once ORTRO_CONF_PLUGINS . 'www_webtest_selenium.php';
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

$program_language = $parameters['webtest_selenium_programming_language'];
$input_script     = $parameters['webtest_selenium_input_script'];

//Replace username e password in the input_script code
$input_script = str_replace('${USERNAME}', $user, $input_script);
$input_script = str_replace('${PASSWORD}', $pwd, $input_script);

$path     = dirname($argv[0]) . DIRECTORY_SEPARATOR;
$tempFile = '';
$tempFile = $path . 'webtest_selenium_' . 
            $id_job . '_' . time() . rand();

$fh = fopen($tempFile, 'w+');
fwrite($fh, $input_script);
fclose($fh);
chmod($tempFile, 0777);

switch ($program_language) {
case 'python':
    $python_path          = $conf['webtest_selenium']['python_path'];
    $selenium_python_path = $conf['webtest_selenium']['selenium_path'];
    $export_path          = 'export PYTHONPATH=$PYTHONPATH:' . 
                            $selenium_python_path . ';';
    $cmdLine              = $export_path . $python_path . 
                            $program_language . ' "' . $tempFile . '" 2>&1';        
    break;
}
 
exec($cmdLine, $stdout, $exit_code);

@unlink($tempFile);

str_replace($password, '******', $stdout);//hide the password
$attachments['txt']  = implode("\n", $stdout);
$attachments['html'] = implode("<br/>", $stdout);

if ($exit_code != '0') {
    $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . implode("\n", $stdout));
    $result = '0';
} else {
    $result = '1';
}

$attachments['txt']  = implode("\n", $stdout);
$attachments['html'] = implode("<br/>", $stdout);
$msg_exec            = $attachments['html'];
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