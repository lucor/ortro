<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the hosts defined in ortro.
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
 
if (isset($_REQUEST['id_host'])) {
    $id_host   = $_REQUEST['id_host'];
    $id_system = $_REQUEST['id_system'];
    $id_db     = 1;
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;

/* ERROR CHECK */

$error = false;    

switch ($_REQUEST['action']) {
case 'add':
case 'edit':
    if ($id_host == 0 || $_REQUEST['action'] == 'edit') {
        //check for unique ip/hostname
        $rows   = $dbUtil->dbQuery($dbh,
                                    $dbUtil->checkExistsHost($_REQUEST['ip'],
                                                             $_REQUEST['hostname']));
        $result = $rows[0][0];
            
        if ($result != 0) {
            //ip/hostname alreay used 
            $action_msg = MSG_ACTION_IP_HOSTNAME_ALREADY_USED;
            $type_msg   = 'warning';
            $error      = true;
        }
    } else {
        //check for unique System <-> Host
        $rows   = $dbUtil->dbQuery($dbh,
                                    $dbUtil->checkExistsSystemHostDb($id_system,
                                                                     $id_host,
                                                                     $id_db));
        $result = $rows[0][0];
        if ($result != 0) {    
            //Label alreay used 
            $action_msg = MSG_ACTION_SYSTEM_HOSTNAME_ALREADY_USED;
            $type_msg   = 'warning';
            $error      = true;
        }
    }
    break;
    
case 'lock':
case 'unlock':
    break;
case 'delete':
    //check for associated notify
    $job_list = '';
    foreach ($_REQUEST['id_chk'] as $id_host => $id_system) {
        $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkJobHost($id_system, $id_host),
                                                 MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg       = MSG_ACTION_HOST_REMOVE_JOB_FIRST . $job_list;
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
        /* ADD HOST */
        if ($id_host == 0) {
            //add a new host
            $dbUtil->dbExec($dbh, $dbUtil->setHost($_REQUEST['ip'],
                                                   $_REQUEST['hostname']));
            $id_host = $dbh->lastInsertID();
        }
        $dbUtil->dbExec($dbh, $dbUtil->setSystemHostDb($id_system,
                                                        $id_host, $id_db));
            $action_msg = MSG_ACTION_HOST_ADDED;
            $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT HOST */
        $dbUtil->dbExec($dbh, $dbUtil->updateHost($id_host,
                                                   $_REQUEST['ip'],
                                                   $_REQUEST['hostname']));
        $action_msg = MSG_ACTION_HOST_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE HOST(S) */
        foreach ($_REQUEST['id_chk'] as $id_host => $id_system) {
            $dbUtil->dbExecMulti($dbh, $dbUtil->deleteHost($id_system, $id_host));
        }
        $action_msg = MSG_ACTION_HOST_DELETED;
        $type_msg   = 'success';
        break;
    case 'lock':
        /* LOCK HOST(S) */
        foreach ($_REQUEST['id_chk'] as $id_host => $id_system) {
            $dbUtil->dbExec($dbh, $dbUtil->setHostStatus($id_host, 'L'));
        }
        $action_msg = MSG_ACTION_HOST_STATUS_UPDATED;
        $type_msg   = 'success';
        break;
    case 'unlock':
        /* UNLOCK HOST(S) */
        foreach ($_REQUEST['id_chk'] as $id_host => $id_system) {
            $dbUtil->dbExec($dbh, $dbUtil->setHostStatus($id_host, 'W'));
        }
        $action_msg = MSG_ACTION_HOST_STATUS_UPDATED;
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
    header('location:?cat=host&mode=view');
    exit;    
}
?>