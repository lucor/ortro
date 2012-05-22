<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * http_download_file Plugin, 
 * download a file via http and make the content of the file available 
 * as input for an incoming job step in a workflow.
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
set_include_path(realpath(dirname($argv[0])) . "/lib/Pear/:" . 
                 ini_get("include_path"));
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

require_once 'HTTP/Request.php';

$loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
$log_prefix   = '[job: ' . $id_job . '] ';
$loggerPlugin->trace('INFO', $log_prefix . 'Executing job');

$result = 0;

//Get the params required by plugin from argv
$user      = $job_infos['identity']['username'];
$password  = $job_infos['identity']['password'];
$id_system = $job_infos['id_system'];
$ip        = $job_infos['ip'];
    
$protocol = $parameters['http_download_file_protocol'];
$port     = $parameters['http_download_file_port'];
$url      = $parameters['http_download_file_url'];
$save_as  = trim($parameters['http_download_file_save_as']);

if ($save_as == '') {
    $save_as = basename($url);
}

$path_save = ORTRO_INCOMING . $id_system . DS;
//check for existing folder system otherwise create it
if (!is_dir($path_save)) {
    @mkdir($path_save, 0700, true);
}

//Generate the uri
$uri = $protocol . '://' . $ip . ':' . $port . '/' .  $url;

//Perform the request
$req =& new HTTP_Request("");
$req->setURL($uri);
if (isset($user) && $user != '') {
    $req->setBasicAuth($user, $password);
}

$response = $req->sendRequest();

if (PEAR::isError($response)) {
    $result   = '0';
    $msg_exec = $response->getMessage();
    $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
} else {
    $fh = fopen($path_save . $save_as, 'w+');
    fwrite($fh, $req->getResponseBody());
    fclose($fh);
    $output   = $path_save . $save_as;
    $result   = '1';
    $msg_exec = '';
}

//---- End plugin code -----

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, 
                       $msg_exec, $attachments, $output);
//###### End required core code ######
?>