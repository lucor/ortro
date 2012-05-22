<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Ping Plugin, to check if a host is alive.
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
set_include_path(realpath(dirname($argv[0])) . "/lib/:" . 
                 ini_get("include_path"));
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil  = new CronUtil($request_type);
$job_infos = $cronUtil->startJobEvent($plugin_name, $id_job);

set_error_handler("errorHandler");

//###### End required core code ######

try {
    
    //---- Start plugin code -----
    
    include_once 'Pear/Net/Ping.php';
    
    $result = 0;
    
    //Get the params required by plugin from argv
    $ip = $job_infos['ip'];
    
    // create object
    $ping = Net_Ping::factory();

    // ping host and display response
    if (!PEAR::isError($ping)) {
        $response = $ping->ping($ip);
    }
    
    if (!PEAR::isError($response)) {
        if ($response->getReceived() > 0) {
            $msg_exec = 'Alive';
            $result   = 1;
        } else {
            $msg_exec = 'Host Unreachable';
        }
    } else {
        $msg_exec = $response->getMessage();
    }
    
    $attachments['html'] = $msg_exec;
    
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