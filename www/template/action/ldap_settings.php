<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the ldap settings defined in ortro.
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

i18n('template', 'common.php');
i18n('template', 'configure_metadata_ldap.php');
require_once ORTRO_CONF . 'configure_metadata_ldap.php';    
require_once 'Pear/Config.php';

$c = new Config();

foreach ($_REQUEST as $key => $value) {
    $elements = split('-', $key);
    if (array_key_exists($elements[0], $conf_metadata)) {
        $config_array[$elements[0]][$elements[1]] = stripslashes($value);    
    }
}

$c->parseconfig($config_array, 'phparray');
$c->writeConfig(ORTRO_CONF . 'configure_ldap.php', 'phparray');

$action_msg = MSG_ACTION_CONFIGURATION_UPDATED; 
$type_msg   = 'success';

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

unset($_REQUEST);
header('location:?cat=ldap_settings&mode=view');
exit;
?>