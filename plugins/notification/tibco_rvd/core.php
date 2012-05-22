<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tibco Rendezvous.
 * Posts a Tibco Rendezvous Message.
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
require_once ORTRO_CONF_PLUGINS . 'notification_tibco_rvd.php';

/**
 * Posts a Tibco Rendezvous Message.
 *
 * @param array $elements The user form values 
 * 
 * @return void
 */
function tibco_rvdNotify($elements)
{
    $logger = new LogUtil('tibco_rvd_notification');
    
    $logger->trace('DEBUG', 'Sending Tibco rvd message');
    
    $cmdLine = $GLOBALS['conf']['tibco_rvd']['path'];
    
    if ($elements['rvd_service'] != '') {
        $cmdLine .= ' -service ' . $elements['rvd_service'];
    }
    if ($elements['rvd_network'] != '') {
        $cmdLine .= ' -network "' . $elements['rvd_network'] . '"';
    }
    if ($elements['rvd_daemon'] != '') {
        $cmdLine .= ' -daemon ' . $elements['rvd_daemon'];
    }
    
    $data_message = $elements['rvd_message'];
    if ($elements['tibco_rvd_attach_result'] == '1' && 
        array_key_exists('txt', $attachments)) {
        $data_message .= $attachments['txt'];
    }
    
    $cmdLine .= ' "' . $elements['rvd_subject'] . '"' .
                 ' "' . $data_message . '"' .
                ' 2>&1 &';
    
    exec($cmdLine, $stdout, $exit_code);
    
    $logger->trace('DEBUG', 'tibrvsend output: ' . $stdout);
    $logger->trace('DEBUG', 'Done.');
}
?>