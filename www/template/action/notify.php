<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the notifications defined in ortro.
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

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$notify_type_label = '';
$redirect_to_view  = false;

$notify_on = '';

if (isset($_REQUEST['notify_on'])) {
    $notify_on      = '-' . implode('-', array_values($_REQUEST['notify_on'])) . '-';
    $notify_type_id = $_REQUEST['notify_type'];
}

if ($_REQUEST['action'] == 'add') {
    $id_job         = $_REQUEST['systemHost'][1];
    $notify_type_id = $_REQUEST['notify_type'];
    //get Notify Label
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->getNotifyLabel($notify_type_id));

    $notify_type_label = $rows[0][0];
}

/* required for edit */
if ($_REQUEST['action'] == 'edit') {
    $id_job            = $_REQUEST['id_job'];
    $id_notify         = $_REQUEST['id_notify'];
    $notify_type_label = $_REQUEST['notify_type'];
}

if (isset($_REQUEST['identity']) && $_REQUEST['identity'] != '') {
    $identity = $_REQUEST['identity'];    
} else {
    $identity = 0;
}

$cfg_file = ORTRO_NOTIFICATION_PLUGINS . $notify_type_label . DS . 'configure.php';

$notify_info = '';

/* ERROR CHECK */

$error = false;

if (!is_file($cfg_file) || (strpos($cfg_file, '..') !== false)) {
    $action_msg = MSG_ACTION_CONFIGURATION_FILE_NOT_FOUND . $notify_type_label;
    $type_msg   = 'warning';
    $error      = true;
}

switch ($_REQUEST['action']) {
case 'add':
case 'edit':
case 'copy':
case 'delete':
    break;    
default:
    $action_msg       = MSG_ACTION_NOT_VALID;
    $type_msg         = 'warning';
    $error            = true;
    $redirect_to_view = true;
    break;
}

if (!$error) {
    // No error found !!!
    $redirect_to_view = true;
    
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD JOB */
        i18n('notification', $notify_type_label);
        include_once $cfg_file;
        $plugin_key = $plugin_field[$notify_type_label];
        for ($i = 1; $i < sizeof($plugin_key); $i++) {
            $field_value = '';
            if (isset($_REQUEST[$plugin_key[$i]['name']])) {
                $field_value = $_REQUEST[$plugin_key[$i]['name']];
            }
            $parameters[$plugin_key[$i]['name']] =  stripslashes($field_value);
        }
        
        $dbUtil->dbExec($dbh, $dbUtil->setNotify($id_job, $notify_type_id,
                                                  $dbUtil->dbSerialize($parameters),
                                                  $notify_on, $identity));

        $action_msg = MSG_ACTION_NOTIFICATION_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT JOB */    
        i18n('notification', $notify_type_label);
        include_once $cfg_file;
        $plugin_key = $plugin_field[$notify_type_label];
        for ($i = 1; $i < sizeof($plugin_key); $i++) {
            $parameters[$plugin_key[$i]['name']] = '';
            if (isset($_REQUEST[$plugin_key[$i]['name']])) {
                $parameters[$plugin_key[$i]['name']] =
                    stripslashes($_REQUEST[$plugin_key[$i]['name']]);
            }
        }
        $dbUtil->dbExec($dbh, $dbUtil->updateNotify($id_job, $id_notify,
                         $dbUtil->dbSerialize($parameters), $notify_on, $identity));
        $action_msg = MSG_ACTION_NOTIFICATION_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        foreach ($_REQUEST['id_chk'] as $id_notify => $system) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteNotify($id_notify));
        }
        $action_msg = MSG_ACTION_NOTIFICATION_DELETED;
        $type_msg   = 'success';
        break;
    case 'copy':
        foreach ($_REQUEST['id_chk'] as $id_notify => $system) {
            $dbUtil->dbExec($dbh, $dbUtil->copyNotify($id_notify));
            $action_msg = MSG_ACTION_NOTIFICATION_COPIED;
            $type_msg   = 'success';
        }
        break;
    default:
        break;
    }
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=notify&mode=view');
    exit;    
}
?>