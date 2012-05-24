<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the groups defined in ortro.
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

$redirect_to_view = false;

if (isset($_REQUEST['groupname'])) {
    $groupname = $_REQUEST['groupname'];
}
if (isset($_REQUEST['id_role'])) {
    $id_role = $_REQUEST['id_role'];
}
$id_group = '';
if (isset($_REQUEST['id_group'])) {
    $id_group = $_REQUEST['id_group'];
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] != 'delete') {
    $rows      = $dbUtil->dbQuery($dbh, $dbUtil->getRoleById($id_role));
    $role_name = $rows[0][0];
    
    if ((stripos($role_name, 'system') === false)) {
        $id_systems = '*';
    } else {
        $id_systems = implode(', ', $_REQUEST['id_systems']);
    }
}
/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'add':
case 'edit':
    //check for unique group label 
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsGroup($groupname));
        
    if (count($rows) > 0 && $rows[0][0] != $id_group) {
        $action_msg = MSG_ACTION_GROUP_NAME_ALREADY_EXISTS;
        $type_msg   = 'warning';
        $error      = true;
    }        
    break;
case 'delete':
    //check for associated notify 
    foreach ($_REQUEST['id_chk'] as $id_group => $id_system) {
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->checkUserGroup($id_group),
                                  MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $user_list .= '<br/> - ' . $key['username'];
            }
            $action_msg       = MSG_ACTION_REMOVE_USER_FIRST . $user_list;
            $type_msg         = 'warning';
            $error            = true;
            $redirect_to_view = true;
            break;
        }
    }
    break;
default:
    $action_msg = MSG_ACTION_NOT_VALID;
    $type_msg   = 'warning';
    $error      = true;
    break;
}

if (!$error) {
    // No error found !!!
    $redirect_to_view = true;
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD GROUP */
        $dbUtil->dbExec($dbh, $dbUtil->setGroup($groupname));
        $id_group = $dbh->lastInsertID();
        $dbUtil->dbExec($dbh, $dbUtil->setGroupRole($id_group,
                                                    $id_role, 
                                                    $id_systems));
        $action_msg = MSG_ACTION_GROUP_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT GROUP */
        $dbUtil->dbExec($dbh, $dbUtil->updateGroup($id_group, $groupname));    
        $dbUtil->dbExec($dbh, $dbUtil->updateGroupRole($id_group, 
                                                       $id_role,
                                                       $id_systems));
        $action_msg = MSG_ACTION_GROUP_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE GROUP(S) */
        foreach ($_REQUEST['id_chk'] as $id_group => $system) {
            $dbUtil->dbExecMulti($dbh, $dbUtil->deleteGroup($id_group));
            $action_msg = MSG_ACTION_GROUP_DELETED;
            $type_msg   = 'success';
        }
        break;
    }
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;
if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=group&mode=view');
    exit;    
}
?>