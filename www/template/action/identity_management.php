<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the identity defined in ortro.
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

if (isset($_REQUEST['id_identity'])) {
    $id_identity = $_REQUEST['id_identity'];    
}
if (isset($_REQUEST['id_system'])) {
    $id_system = $_REQUEST['id_system'];
}
if (isset($_REQUEST['label'])) {
    $label = $_REQUEST['label'];
}
if (isset($_REQUEST['username'])) {
    $username = $_REQUEST['username'];
    $password = $dbUtil->dbSerialize($_REQUEST['password']);
}
if (isset($_REQUEST['old_password'])) {
    $old_password = $dbUtil->dbSerialize($_REQUEST['old_password']);
}
$shared_systems = 0;
if (isset($_REQUEST['id_shared_systems'])) {
    $shared_systems = '#' . implode('#', $_REQUEST['id_shared_systems']) . '#';    
}

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {

case 'add':
    //check for unique label
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsIdentity($label));        
    if (isset($rows[0][0])) {
        //label
        $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'identity_picker':
    break;
case 'edit':
    $update_share_identity = false;
    if (isset($_REQUEST['update_share'])) {
        $update_share_identity = true;
    } else {
        //check for unique label
        $rows   = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsIdentity($label));
        $result = $rows[0][0];
        if ($result != $id_identity && count($rows)>0) {
            //label
            $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
            $type_msg   = 'warning';
            $error      = true;
        }
        //check for matching password
        $rows   = $dbUtil->dbQuery($dbh,
                                    $dbUtil->checkIdentityPassword($id_identity,
                                                                   $old_password));
        $result = $rows[0][0];

        if ($result != 1) {
            //label
            $action_msg = MSG_ACTION_OLD_PASSWORD_NOT_VALID;
            $type_msg   = 'warning';
            $error      = true;
        }
    }
    break;
case 'delete':
    //check for associated notify 
    foreach ($_REQUEST['id_chk'] as $id_identity => $label) {
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->checkJobIdentity($id_identity),
                                  MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg       = MSG_ACTION_REMOVE_IDENTITY_FIRST . $job_list;
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
    case 'identity_picker':
        //show the popup for the identity picker
        $rows = $dbUtil->dbQuery($dbh,
                $dbUtil->getIdentityBySystem($_REQUEST['id_system']),
                MDB2_FETCHMODE_ASSOC);
        if (count($rows)>0) {
            $form            = new HTML_QuickForm('frm',
                                                  'post',
                                                  'javascript:fillIdentity(\'' .
                                                  $_REQUEST['field_name'] . '\');');
            $select_identity = array ();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $select_identity[$rows[$i]['id_identity']] = $rows[$i]['label'];
            }
            $f_select_identity = $form->createElement('select', 'identity', '',
                                                      $select_identity, '');

            $f_submit = $form->addElement('submit', 'Update', MSG_IDENTITY_PICK);
            //convert form in array for extact js and attributes
            $formArray = $form->toArray();
            $html      = $formArray['javascript'] .
                    '<form ' . $formArray['attributes'] . '>' .
                    '<br/>' . FIELD_IDENTITY_PICK . '<br/><br/>' .
                    $f_select_identity->toHTML() .
                    $f_submit->toHTML();    
        } else {
            $html = MSG_IDENTITY_NOT_FOUND;
        }

        echo '<html><head><title>' . FIELD_IDENTITY_PICK_TITLE . '</title>' .
             '<link rel="stylesheet" type="text/css" href="css/global.css">' .
             '<script language="JavaScript" type="text/javascript"
              src="js/ortro.js"></script>' . '</head><body>';
        echo $html;
        echo '</form></body></html>';
        exit;
        break;
    case 'add':
        /* ADD IDENTITY */
        //add a new host
        $dbUtil->dbExec($dbh, $dbUtil->setIdentity($label,
                                                    $username,
                                                    $password,
                                                    $id_system,
                                                    $shared_systems));
        $action_msg = MSG_ACTION_IDENTITY_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT IDENTITY */
        if ($update_share_identity) {
            $dbUtil->dbExec($dbh, $dbUtil->updateIdentityShare($id_identity,
                                                                $shared_systems));
        } else {
            $dbUtil->dbExec($dbh, $dbUtil->updateIdentity($id_identity,
                                                           $label,
                                                           $username,
                                                           $password));
        }

        $action_msg = MSG_ACTION_IDENTITY_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE IDENTITY */
        foreach ($_REQUEST['id_chk'] as $id_identity => $label) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteIdentity($id_identity));
        }
        $action_msg = MSG_ACTION_IDENTITY_DELETED;
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
    header('location:?cat=identity_management&mode=view');
    exit;    
}
?>