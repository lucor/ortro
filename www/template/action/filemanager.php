<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the files uploaded in ortro.
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

$path_upload = ORTRO_INCOMING . $_REQUEST['id_system'] . DS;

/* ERROR CHECK */

$error            = false;
$redirect_to_view = false;
$qs_id_system     = '';
$mode             = 'view';

if (!is_dir($path_upload) || (strpos($path_upload, '..') !== false)) {
    $action_msg       = MSG_ACTION_NOT_VALID;
    $type_msg         = 'warning';
    $error            = true;
    $redirect_to_view = true;
}

switch ($_REQUEST['action']) {
case 'add':
    //check for unique System label
    $form      = new HTML_QuickForm('frm', 'post');
    $file      =& $form->addElement('file', 'filename', '', '');
    $file_info = $file->getValue();        
    if (!$file->isUploadedFile()) {
        $action_msg = MSG_ACTION_PROBLEM_DURING_TRANSFER .
                      ini_get('upload_max_filesize');
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'edit':
                
    break;
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
    $mode             = 'edit';
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD FILE */

        //check for existing folder system
        if (!is_dir($path_upload)) {
            @mkdir($path_upload, 0700, true);
        }

        $res = $file->moveUploadedFile($path_upload);
        if ($res) {
            $action_msg       = MSG_ACTION_FILE_UPLOAD_SUCCESS;
            $type_msg         = 'success';
            $redirect_to_view = false;
            $_REQUEST['mode'] = 'edit';
        } else {
            $action_msg = MSG_ACTION_FILE_UPLOAD_ERROR;
            $type_msg   = 'warning';
            $mode       = 'view';
        }
        break;
    case 'edit':
        break;
    case 'delete':
        $file_to_del = $path_upload . $_REQUEST['file'];
        if (is_file($file_to_del) && (strpos($file_to_del, '..') === false)) {
            if (@unlink($file_to_del)) {
                $action_msg = MSG_ACTION_FILE_DELETED_SUCCESS;
                $type_msg   = 'success';
                $redirect_to_view = false;
                $mode = 'edit';
                $qs_id_system = '&id_system=' . $_REQUEST['id_system'];
            }
        }
        $action_msg = MSG_ACTION_FILE_DELETED_ERROR;
        $type_msg   = 'warning';
        break;
    }
    $_REQUEST['action'] = '';
}
$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;
if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=filemanager&mode='. $mode . $qs_id_system);
    exit;    
}
?>