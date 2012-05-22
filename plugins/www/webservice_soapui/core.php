<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WebService SoapUI Plugin.
 * Runs specified TestCases and reports/exports results as configured in SoapUI
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
@require_once ORTRO_CONF_PLUGINS . 'www_webservice_soapui.php';
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

//Get the params required by plugin
$testcase = ORTRO_ATTACHMENTS . $id_job . DS . $parameters['webservice_soapui_testcase'];

$cmdLine = $conf['webservice_soapui']['soapui_path'] . 
           DS . 'bin' . DS . 'testrunner.sh -r ' .
           $testcase . ' 2>&1';

exec($cmdLine, $stdout, $exit_code);

$attachments['txt']  = implode("\n", $stdout);
$attachments['html'] = implode("<br/>", $stdout);

if ($exit_code != '0') {
    $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . implode("\n", $stdout));
    $result = '0';
} else {
    $result = '1';
}

$msg_exec = $attachments['txt'];
//---- End plugin code -----

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######
?>