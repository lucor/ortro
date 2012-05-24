<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the notification plugins defined in ortro.
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
 
require_once 'ioUtil.php';

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;
/* ERROR CHECK */

$error = false;    

switch ($_REQUEST['action']) {

case 'add':
    //check for unique System label
    $form      = new HTML_QuickForm('frm', 'post');
    $file      =& $form->addElement('file', 'filename', '', '');
    $file_info = $file->getValue();

    $split_file_info = split("-", $file_info['name']);

    $file_name = $split_file_info[0];

    $split_file_name = split('_', $file_name);
    $category        = array_shift($split_file_name);
        
    $plugin_name = implode('_', $split_file_name);

    $rows           = $dbUtil->dbQuery($dbh,
                      $dbUtil->checkExistsPluginNotify($plugin_name),
                               MDB2_FETCHMODE_ASSOC);
    $upgrade_plugin = false;
    if (count($rows) > 0) {
        $upgrade_plugin = true;
        $id_notify_type = $rows[0]['id_notify_type'];
    }
    if (!$file->isUploadedFile()) {
        $action_msg = MSG_ACTION_PROBLEM_DURING_TRANSFER;
        $type_msg   = 'warning';
        $error      = true;
    }
    if ($category != 'notification') {
        $action_msg = $plugin_name  . MSG_ACTION_NOT_NOTIFICATION_PLUGIN;
        $type_msg   = 'warning';
        $error      = true;
    }

    include_once 'Archive/Tar.php';

    $temp_dir = ORTRO_TEMP . basename($file_info['tmp_name']) . DS;
    @mkdir($temp_dir, 0700, true);
    $tar_object = new Archive_Tar($file_info['tmp_name']);

    //Extract contents to a temp dir for tests
    $tar_object->extract($temp_dir);
    if (PEAR::isError($tar_object->error_object)) {
        $action_msg = $tar_object->error_object->getMessage() . '<br/>' .
                      $tar_object->error_object->getDebugInfo();
        $type_msg   = 'warning';
        $error      = true;
    } else {
        $plugin_conf_file = $temp_dir . $category . DS . $plugin_name . DS .
                            'configure.php';
        if (!is_file($plugin_conf_file)) {
            $action_msg = MSG_ACTION_CONFIGURE_FILE_NOT_FOUND . $file_info['name'];
            $type_msg   = 'warning';
            $error      = true;
        } else {
            @include_once $plugin_conf_file;
            if (version_compare(ORTRO_VERSION,
                    $plugin_field[$plugin_name][0]['min_ortro_version']) == '-1') {
                //Cannot install plugin min version of ortro not satisfied
                $action_msg = MSG_ACTION_MIN_VERSION_REQUIRED .
                    $plugin_field[$plugin_name][0]['min_ortro_version'];
                $type_msg   = 'warning';
                $error      = true;
            }
            unset($plugin_field);
        }
    }
    @removeDirectory($temp_dir);

    break;
case 'edit':   
    break;
case 'delete':
    foreach ($_REQUEST['id_chk'] as $id_job_type => $label) {
        $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkNotifyPlugin($id_job_type),
                                                 MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            $action_msg       = MSG_ACTION_REMOVE_NOTIFICATION_FIRST . $job_list;
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
        /* ADD PLUGIN */

        $tar_object = new Archive_Tar($file_info['tmp_name']);
        $tar_object->extract(ORTRO_PLUGINS);

        //Move the language files starting from untar folder
        $dir_lang_plugin   = ORTRO_NOTIFICATION_PLUGINS . DS . $plugin_name 
                                                        . DS . 'lang' . DS;
        $dir_lang_contents = scandir($dir_lang_plugin);
        foreach ($dir_lang_contents as $item) {
            if (is_dir($dir_lang_plugin . $item) && $item != '.' && $item != '..'
                                                 && $item != '.svn') {
                $full_path_lang =  ORTRO_LANG . $item . DS . 'plugins'
                                                      . DS . 'notification'
                                                      . DS . $plugin_name . DS;
                @mkdir($full_path_lang, 0700, true);
                @copy($dir_lang_plugin . $item . DS . 'language.php', $full_path_lang
                                               . DS . 'language.php');
            }
        }
        //Remove install language files
        @removeDirectory($dir_lang_plugin);
            
        if (PEAR::isError($tar_object->error_object)) {
            $action_msg = $tar_object->error_object->getMessage() . '<br/>' .
                          $tar_object->error_object->getDebugInfo();
            $type_msg   = 'warning';
        } else {
            $plugin_conf_file = ORTRO_NOTIFICATION_PLUGINS . $plugin_name
                                                           . DS . 'configure.php';
            if (is_file($plugin_conf_file)) {
                i18n('notification', $plugin_name);
                include_once $plugin_conf_file;
                if (!$upgrade_plugin) {
                    $dbUtil->dbExec($dbh,
                                     $dbUtil->setPluginNotification($plugin_name));
                    $id_notify_type = $dbh->lastInsertID();
                }
                $action_msg = MSG_ACTION_PLUGIN_INSTALLED;
                $type_msg   = 'success';    
            } else {
                $action_msg = 'Plugin ' . $plugin_name . ': '
                                        . MSG_ACTION_CONFIGURE_FILE_NOT_FOUND
                                        . $file_info['name'];

                $type_msg         = 'warning';
                $redirect_to_view = false;
            }
        }
            
        break;
    case 'edit':
        $plugin_path = ORTRO_NOTIFICATION_PLUGINS . $_REQUEST['plugin_name'] . DS;
        if (!is_dir($plugin_path) || (strpos($plugin_path, '..') !== false)) {
            $action_msg       = MSG_ACTION_CONFIGURATION_FILE_NOT_FOUND;
            $type_msg         = 'warning';
            $redirect_to_view = false;
            break;
        }
        i18n('notification', $_REQUEST['plugin_name']);
        include_once $plugin_path . 'configure_metadata.php';
        include_once 'Pear/Config.php';
        if (!is_dir(ORTRO_CONF_PLUGINS)) {
            @mkdir(ORTRO_CONF_PLUGINS, 0700);
        }
        $c = new Config();
            
        foreach ($_REQUEST as $key => $value) {
            $elements = split('-', $key);
            if (array_key_exists($elements[0], $conf_metadata)) {
                $config_array[$elements[0]][$elements[1]] = stripslashes($value);    
            }
        }
            
        $plugin_config_file = ORTRO_CONF_PLUGINS . 'notification_'
                                                 . $_REQUEST['plugin_name'] . '.php';
        if (strpos($plugin_config_file, '..') === false) {
            $c->parseconfig($config_array, 'phparray');
            $c->writeConfig($plugin_config_file, 'phparray');
            @chmod($plugin_config_file, 0600);
        }
        $action_msg = MSG_ACTION_CONFIGURATION_UPDATED; 
        $type_msg   = 'success';

        break;
    case 'delete':
        /* DELETE PLUGIN(S) */
        include_once 'ioUtil.php';
        foreach ($_REQUEST['id_chk'] as $id_notify_type => $label) {
            $dbUtil->dbExec($dbh,
                             $dbUtil->deletePluginNotification($id_notify_type));
            if ((strpos($label, '..') === false)) {
                //remove configuration files
                @unlink(ORTRO_CONF_PLUGINS . 'notification_' . $label . '.php');
                //remove plugin dir
                @removeDirectory(ORTRO_NOTIFICATION_PLUGINS . $label);
                //remove plugin language files
                $dir_lang_contents = scandir(ORTRO_LANG);
                foreach ($dir_lang_contents as $item) {
                    if (is_dir(ORTRO_LANG . $item) && $item != '.' && $item != '..'
                                                                   && $item != '.svn') {
                        @removeDirectory(ORTRO_LANG . $item . DS . 'plugins'
                                                            . DS . 'notification'
                                                            . DS . $label);
                    }
                }
            }
        }
        $action_msg = MSG_ACTION_PLUGIN_DELETED;
        $type_msg   = 'success';
        break;
    }
    if ($_REQUEST['mode']=='add') {
        $_REQUEST['mode'] = 'edit';
        $redirect_to_view = false;
    }
    $_REQUEST['action'] = '';
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=plugin_notify&mode=view');
    exit;    
}
?>