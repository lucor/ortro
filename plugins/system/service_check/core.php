<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Service Check Plugin.
 * to check if a service on the specified port is a alive.
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
 * @author   Marcello Sessa <hunternet@users.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

require_once realpath(dirname($argv[0])) . '/../../init.inc.php';
require_once 'cronUtil.php';
require_once 'services.php';

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

    //Get the params required by plugin from argv
    $default_ports = $parameters['service_check_default_ports'];
    $custom_ports  = explode(',', $parameters['service_check_custom_ports']);

    $timeout = $parameters['service_check_timeout'];
    $ip      = $job_infos['ip'];
    $result  = 1;

    if (!is_int($timeout)) {
        $timeout = 20;
    }

    $to_check_ports = array_merge($default_ports, $custom_ports);

    if (count($to_check_ports) == 0) {
        $msg_exec      = 'No port to check was specified. Nothing to check';
        $msg_exec_html = $msg_exec;
        $result        = 0;
    } else {
        foreach ($to_check_ports as $key => $value) {
            //remove eventual space from custom port values.
            $port = trim($value); 
            //create message prefix.
            if (array_key_exists($port, $services)) {
                $msg_prefix = $services[$port] . ' -> ';
            } else {
                //custom port
                $msg_prefix = 'Custom:' . $port . ' -> ';
            }
            try {
                if (isset($port) && $port != '') {
                    $fs             = fsockopen($ip, intval($port), $errno, $errstr, $timeout);
                    $msg_exec      .= $msg_prefix . 'Service Alive' . "\n";
                    $msg_exec_html .= $msg_prefix . '<font color="blue">Service Alive<br /></font>';
                }
            } catch (Exception $e) {
                $result         = 0;
                $msg_exec      .= $msg_prefix . 'Service Unreachable' . "\n";
                $msg_exec_html .= $msg_prefix . '<font color="red">Service Unreachable<br /></font>';
            }
        }
    }

    $attachments['txt']  = $msg_exec;
    $attachments['html'] = $msg_exec_html;

    $msg_exec = $msg_exec_html;

    //---- Archive job result ----
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['service_check_retention'];
    //---- End plugin code -----

} catch (Exception $e) {
    $cronUtil->traceError($plugin_name, $e);
    $msg_exec_html = "Plugin exception occourred: " . $e->getMessage() . "\n" .
                     "Please contact system administrator";
    
    $msg_exec = $msg_exec_html;
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
