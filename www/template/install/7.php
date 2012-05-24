<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Save configuration Page
 * 
 * PHP version 5
 * 
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

i18n('template', 'configure_metadata_install.php');
require_once ORTRO_CONF . 'configure_metadata_install.php';
require_once 'Pear/Config.php';

$installation = '';

$c = new Config();

foreach ($conf_metadata as $key => $value) {
    foreach ($value as $myKey => $myValue) {
        if (is_array($myValue)) {
            $elements = split('-', $myValue['name']);
            if (isset($_SESSION['installation'][$elements[0]][$elements[1]])) {
                $element_value = 
                    $_SESSION['installation'][$elements[0]][$elements[1]];
            } else {
                //load default value
                $element_value = $myValue['value'];
                if (is_array($element_value)) {
                    $temp_value    = array_values($myValue['value']);
                    $element_value = $temp_value[0];
                }
            }
            $config_array[$key][$elements[1]] = $element_value;
        }
    } 
}

$config_array['id'] = $_SESSION['ortro_id'];

$c->parseconfig($config_array, 'phparray');
$result = $c->writeConfig(ORTRO_CONF . 'configure.php', 'phparray');

//Set the correct permission for the conf directory
@chmod(ORTRO_CONF, 0700);

if (PEAR::isError($result)) {
    showMessage($result->getMessage() . '<br/>' . 
                $result->getDebugInfo(), 'warning');
} else {
    $message = INSTALL_FINISH_MSG_PART_1 . '<br/>' .
               INSTALL_FINISH_MSG_PART_2 . 
               '<a href="'  . $_SERVER['REQUEST_URI'] . '">' . 
               INSTALL_FINISH_MSG_PART_3 .'</a>' .
               INSTALL_FINISH_MSG_PART_4;
    showMessage($message, 'success');
    
    $message_security = INSTALL_FINISH_MSG_PART_5 . '<br/><b>' .
                        ORTRO_INSTALL . '</b>';
    showMessage($message_security, 'warning');
}
?>