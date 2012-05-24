<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the systems defined in ortro.
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


if (isset($_REQUEST['system_name'])) {
    $system_name = $_REQUEST['system_name'];
}

/* required for edit */
if ($_REQUEST['action'] == 'edit') {
    $id_system = $_REQUEST['id_system'];
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;
/* ERROR CHECK */
$error = false;

switch ($_REQUEST['action']) {
case 'add':
case 'edit':
    //check for unique System label
    $rows   = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsSystem($system_name));
    $result = $rows[0][0];

    if ($result != 0) {
        //Label alreay used
        $action_msg = MSG_ACTION_SYSTEM_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'lock':
case 'unlock':
    break;
case 'delete':
    foreach ($_REQUEST['id_chk'] as $id_system => $system_name) {
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->checkJobSystem($id_system), 
                                  MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg       = MSG_ACTION_SYSTEM_REMOVE_JOB_FIRST . $job_list;
            $type_msg         = 'warning';
            $error            = true;
            $redirect_to_view = true;
            break;
        }
    }
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
        /* ADD SYSTEM */
        $dbUtil->dbExec($dbh, $dbUtil->setSystem($system_name));
        $action_msg = MSG_ACTION_SYSTEM_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT SYSTEM */
        $dbUtil->dbExec($dbh,
                         $dbUtil->updateSystem($id_system, $system_name));
        $action_msg = MSG_ACTION_SYSTEM_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE SYSTEM(S) */
        foreach ($_REQUEST['id_chk'] as $id_system => $system_name) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteSystem($id_system));
            $dbUtil->dbExec($dbh, $dbUtil->deleteSystemHostDb($id_system));
        }
        $action_msg = MSG_ACTION_SYSTEM_DELETED;
        $type_msg   = 'success';
        break;
    case 'lock':
        /* LOCK SYSTEM(S) */
        foreach ($_REQUEST['id_chk'] as $id_system => $system_name) {
            $dbUtil->dbExec($dbh, $dbUtil->setSystemStatus($id_system, 'L'));
        }
        $action_msg = MSG_ACTION_SYSTEM_STATUS_UPDATED;
        $type_msg   = 'success';
        break;
    case 'unlock':
        /* LOCK SYSTEM(S) */
        foreach ($_REQUEST['id_chk'] as $id_system => $system_name) {
            $dbUtil->dbExec($dbh, $dbUtil->setSystemStatus($id_system, 'W'));
        }
        $action_msg = MSG_ACTION_SYSTEM_STATUS_UPDATED;
        $type_msg   = 'success';
        break;
    }
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=system&mode=view');
    exit;    
}
?>