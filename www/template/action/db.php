<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the database defined in ortro.
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

$dbUtil      = new DbUtil();
$dbh         = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;

if (isset($_REQUEST['systemHost'])) {
    $id_system = $_REQUEST['systemHost'][0];
    $id_host   = $_REQUEST['systemHost'][1];
}
if (isset($_REQUEST['id_db'])) {
    $id_db = $_REQUEST['id_db'];
}

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'add':
    //check for unique db 
    $rows = $dbUtil->dbQuery($dbh,
                              $dbUtil->checkExistsDb($_REQUEST['db_label']));
    //$result = $rows[0][0];
    if (isset($rows[0][0])) {
        //Label alreay used 
        $action_msg = MSG_ACTION_DB_NAME_ALREADY_EXISTS;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'edit':
    //check for unique db 
    $rows = $dbUtil->dbQuery($dbh,
                              $dbUtil->checkExistsDb($_REQUEST['db_label']));
    if (isset($rows[0][0]) && $rows[0][0] != $id_db) {    
        //Label alreay used 
        $action_msg = MSG_ACTION_DB_NAME_ALREADY_EXISTS;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'delete':
    //check for associated notify 
    foreach ($_REQUEST['id_chk'] as $id_db => $id_system) {
        $rows = $dbUtil->dbQuery($dbh,
                $dbUtil->checkJobDatabase($id_db), MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg = MSG_ACTION_DB_REMOVE_JOB_FIRST . $job_list;
            $type_msg   = 'warning';
            $error      = true;
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
        /* ADD DB */
        $dbUtil->dbExec($dbh, $dbUtil->setDb($_REQUEST['dbms'], 
                                             $_REQUEST['db_label'],
                                             $_REQUEST['sid'],
                                             $_REQUEST['port']));
        $id_db = $dbh->lastInsertID();
        $dbUtil->dbExec($dbh, $dbUtil->setSystemHostDb($id_system,
                                                        $id_host, $id_db));
        $action_msg = MSG_ACTION_DB_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT DB */
        $dbUtil->dbExec($dbh, $dbUtil->updateDb($id_db,
                                                 $_REQUEST['dbms'],
                                                 $_REQUEST['db_label'],
                                                 $_REQUEST['sid'],
                                                 $_REQUEST['port']));
        $action_msg = MSG_ACTION_DB_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE DB(S) */
        foreach ($_REQUEST['id_chk'] as $id_db => $id_system) {
            $dbUtil->dbExecMulti($dbh, $dbUtil->deleteDatabase($id_db));
        }
        $action_msg = MSG_ACTION_DB_DELETED;
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
    header('location:?cat=db&mode=view');
    exit;    
}
?>