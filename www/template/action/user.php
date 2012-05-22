<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the users defined in ortro.
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

if (isset($_REQUEST['username'])) {
    $username = $_REQUEST['username'];
}
if (isset($_REQUEST['id_user'])) {
    $id_user = $_REQUEST['id_user'];
}
if (isset($_REQUEST['password'])) {
    $password = $_REQUEST['password'];
}
if (isset($_REQUEST['name'])) {
    $name = $_REQUEST['name'];
}
if (isset($_REQUEST['mail'])) {
    $mail = $_REQUEST['mail'];
}
if (isset($_REQUEST['language'])) {
    $language = $_REQUEST['language'];
}
if (isset($_REQUEST['id_groups'])) {
    $id_groups = $_REQUEST['id_groups'];
}
if (isset($_REQUEST['update_password'])) {
    $submit_type = 'update_password';
} else {
    $submit_type = 'update_properties';
}
if (isset($_REQUEST['type'])) {
    $type = 'ldap';
} else {
    $type = 'db';
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;
/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'add':
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsUser($username));
    if ($rows[0][0] > 0) {
        //Username alreay used
        $action_msg = MSG_ACTION_USERNAME_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'edit':
case 'delete':
case 'addToGroup':
case 'delFromGroup':
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
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD USER */
        $dbUtil->dbExec($dbh, $dbUtil->setUser($username, 
                                                $password, 
                                                $name, 
                                                $mail,
                                                $type,
                                                'none'));
        $id_user = $dbh->lastInsertID();
        $dbUtil->dbExec($dbh, $dbUtil->setGroupUser($id_user));
        $action_msg       = MSG_ACTION_USER_ADDED;
        $redirect_to_view = true;
        $type_msg         = 'success';
        break;
    case 'addToGroup':
        /* ADD USER TO GROUP*/
        foreach ($id_groups as $id_group) {
            $dbUtil->dbExec($dbh, $dbUtil->setGroupUser($id_user, $id_group));
        }
        $action_msg       = MSG_ACTION_USER_IN_GROUP_ADDED;
        $redirect_to_view = true;
        $type_msg         = 'success';
        break;
    case 'delFromGroup':
        /* REMOVE USER FROM GROUP*/
        foreach ($id_groups as $id_group) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteGroupUser($id_user, $id_group));
        }
        $action_msg       = MSG_ACTION_USER_DELETED_FROM_GROUP;
        $redirect_to_view = true;
        $type_msg         = 'success';
        break;
    case 'edit':
        /* EDIT HOST */
        if ($submit_type == 'update_password') {
            $dbUtil->dbExec($dbh, $dbUtil->updateUserPassword($id_user, $password));
        } else {
            if ($type == 'ldap') {
                $dbUtil->dbExec($dbh, $dbUtil->updateUserProperties($id_user, 
                                                                    $language));
            } else {
                $dbUtil->dbExec($dbh, $dbUtil->updateUserProperties($id_user, 
                                                                    $language, 
                                                                    $name, 
                                                                    $mail));
            }
            if (AuthUtil::getSessionData('userid') == $id_user) {
                AuthUtil::setSessionData('language', $language);
            }
        }
        $action_msg = MSG_ACTION_USER_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE HOST(S) */
        foreach ($_REQUEST['id_chk'] as $id_user => $id_system) {
            $dbUtil->dbExecMulti($dbh, $dbUtil->deleteUser($id_user));
        }
        $action_msg = MSG_ACTION_USER_DELETED;
        $type_msg   = 'success';
        break;
    }
    $cat = $_REQUEST['cat'];
} else {
    $cat = 'user';
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=' . $cat . '&mode=view');
    exit;    
}
?>