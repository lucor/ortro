<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * check_uri_response_code Plugin, 
 * allows to check the response code of a http request to a web page.
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

try {
    //---- Start plugin code -----
    
    include_once 'HTTP/Request.php';
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    //Get the params required by plugin from argv
    $ip = $job_infos['ip'];
        
    $protocol = $parameters['check_uri_response_code_protocol'];
    $port     = $parameters['check_uri_response_code_port'];
    $url      = $parameters['check_uri_response_code_url'];
    
    //Generate the uri
    $uri = $protocol . '://' . $ip . ':' . $port . '/' .  $url;
    
    //Perform the request
    $req =& new HTTP_Request("");
    $req->setURL($uri);
    $req->sendRequest();
    
    //Get the response code
    $code = $req->getResponseCode();
    
    //Status Code Definitions see RFC 2616 for details
    $status_code_details = array(
        '100'=>'Continue',
        '101'=>'Switching Protocols',
        '200'=>'OK',
        '201'=>'Created',
        '202'=>'Accepted',
        '203'=>'Non-Authoritative Information',
        '204'=>'No Content',
        '205'=>'Reset Content',
        '206'=>'Partial Content',
        '300'=>'Multiple Choices',
        '301'=>'Moved Permanently',
        '302'=>'Found',
        '303'=>'See Other',
        '304'=>'Not Modified',
        '305'=>'Use Proxy',
        '306'=>'(Unused)',
        '307'=>'Temporary Redirect',
        '400'=>'Bad Request',
        '401'=>'Unauthorized',
        '402'=>'Payment Required',
        '403'=>'Forbidden',
        '404'=>'Not Found',
        '405'=>'Method Not Allowed',
        '406'=>'Not Acceptable',
        '407'=>'Proxy Authentication Required',
        '408'=>'Request Timeout',
        '409'=>'Conflict',
        '410'=>'Gone',
        '411'=>'Length Required',
        '412'=>'Precondition Failed',
        '413'=>'Request Entity Too Large',
        '414'=>'Request-URI Too Long',
        '415'=>'Unsupported Media Type',
        '416'=>'Requested Range Not Satisfiable',
        '417'=>'Expectation Failed',
        '500'=>'Internal Server Error',
        '501'=>'Not Implemented',
        '502'=>'Bad Gateway',
        '503'=>'Service Unavailable',
        '504'=>'Gateway Timeout',
        '505'=>'HTTP Version Not Supported');
    
    $attachments['txt'] = $code . ' -> ' . $status_code_details[$code];
    
    if ($code == '200') {
        $result = '1';
        $loggerPlugin->trace('INFO', 'result for id_job=' . 
                                     $id_job . ' -> ' . $attachments['txt']);
    } else {
        $result = '0';
        $loggerPlugin->trace('ERROR', 'result for id_job=' . 
                                      $id_job . ' -> ' . $attachments['txt']);
    }
    
    $msg_exec = $attachments['txt'];
    
} catch (Exception $e) {
    $result   = 0;
    $msg_exec = $e->getMessage();
    $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
}
//---- End plugin code -----

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######
?>