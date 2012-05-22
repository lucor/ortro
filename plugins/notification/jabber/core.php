<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Jabber Notification Plugin, 
 * allows to send a notify message using the jabber protocol.
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
 * @author   David Black <dblackia@users.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
 
require_once 'logUtil.php';
require_once 'lib/class.jabber2.php';

global $conf;
require_once ORTRO_CONF_PLUGINS . 'notification_jabber.php';

/**
 * Sends an Instant Message using the Jabber protocol
 *
 * @param array $elements    The user form values 
 * @param array $attachments The files to attach
 * 
 * @return void
 */
function jabberNotify($elements, $attachments)
{
    $logger = new LogUtil('jabber_notification');

    $jabber_server   = $GLOBALS['conf']['jabber']['host'];
    $jabber_port     = $GLOBALS['conf']['jabber']['port'];
    $jabber_username = $GLOBALS['conf']['jabber']['username'];
    $jabber_password = $GLOBALS['conf']['jabber']['password'];
    
    switch ($GLOBALS['conf']['jabber']['security']) {
    case 'ssl':
        $jabber_smode = SECURITY_SSL;
        break;
    case 'tsl':
        $jabber_smode = SECURITY_TSL;
        break;
    default:
        $jabber_smode = SECURITY_NONE;
        break;
    }
    
    $logger->trace('INFO', "Sending jabber IM to " .
                           $elements['jabber_to'] .
                           ", username=" . $jabber_username .
                           ", smode=" . $jabber_smode .
                           ", port=" . $jabber_port .
                           ", server=" . $jabber_server);

    try {
        $JABBER = new Jabber($jabber_username,
                             $jabber_password,
                             $jabber_smode,
                             $jabber_port,
                             $jabber_server);
         
        if ($logger->logLevel != 'DEBUG') {
            //disable jabber_class logging
            $JABBER->log_enabled = false;
            $JABBER->log         = null;
        }
    
        $JABBER->login();
    
        $data_message = $elements['jabber_message'];
    
        if ($elements['jabber_attach_result']=='1'
            && array_key_exists('txt', $attachments)) {
            $data_message .= $attachments['txt'];
        }
    
        $JABBER->send_message($elements['jabber_to'],
                              $data_message);
         
        $logger->trace('DEBUG', print_r($JABBER->log, true));
    
        $JABBER->disconnect();
    } catch (Exception $e) {
        $logger->trace('ERROR', $e->getMessage());
    }
    
    $logger->trace('INFO', 'Done.');
    return true;
}
?>
