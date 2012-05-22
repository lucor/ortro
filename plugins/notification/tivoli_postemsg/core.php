<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Posts an event to the event server using non-Tivoli communication.
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
 
require_once 'logUtil.php';

global $conf;
require_once ORTRO_CONF_PLUGINS . 'notification_tivoli_postemsg.php';

/**
 * Posts an event to the event server using non-Tivoli communication
 *
 * @param array $elements    The user form values 
 * @param array $attachments The files to attach
 * 
 * @return void
 */
function tivoli_postemsgNotify($elements,$attachments)
{
    $logger = new LogUtil('tivoli_postemsg_notification');
    
    $logger->trace('DEBUG', 'Sending tivoli postemsg');
    $data_message = $elements['tivoli_postemsg_message'];
    if ($elements['tivoli_postemsg_attach_result'] == '1' && 
        array_key_exists('txt', $attachments)) {
        $data_message .= $attachments['txt'];
    }
    $cmdLine =  $GLOBALS['conf']['tivoli_postemsg']['path'] .
                ' -S ' . $GLOBALS['conf']['tivoli_postemsg']['host'] .
                ' -r ' . $elements['tivoli_postemsg_severity'] .
                ' -m "' . $data_message . '" ' .
                $elements['tivoli_postemsg_attribute'] .
                ' "' . $elements['tivoli_postemsg_class'] . '" ' .
                ' "' . $elements['tivoli_postemsg_source'] . '" ' .
                ' 2>&1 &';
                   
    exec($cmdLine, $stdout, $exit_code);
    $logger->trace('DEBUG', 'postemsg output: ' . $cmdLine);
    $logger->trace('DEBUG', 'Done.');
}
?>