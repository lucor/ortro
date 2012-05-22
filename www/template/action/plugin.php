<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the plugins defined in ortro.
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

    $split_file_info = split('-', $file_info['name']);
    $file_name       = $split_file_info[0];
    $split_file_name = split('_', $file_name);
    $category        = array_shift($split_file_name);
    $plugin_name     = implode('_', $split_file_name);
    $upgrade_plugin  = false;
        
    $rows = $dbUtil->dbQuery($dbh, 
                              $dbUtil->checkExistsPlugin($plugin_name), 
                              MDB2_FETCHMODE_ASSOC);
        
    if (count($rows) > 0) {
        //Upgrade plugin...
        $upgrade_plugin = true;
        $id_job_type    = $rows[0]['id_job_type'];
    }
    if (!$file->isUploadedFile()) {
        $action_msg = MSG_ACTION_PROBLEM_DURING_TRANSFER;
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
        $plugin_conf_file = $temp_dir . $category . DS . 
                            $plugin_name . DS . 'configure.php';
        if (!is_file($plugin_conf_file)) {
            $action_msg = MSG_ACTION_CONFIGURE_FILE_NOT_FOUND . $file_info['name'];
            $type_msg   = 'warning';
            $error      = true;
        } else {
            @include_once $plugin_conf_file;
            $min_version = $plugin_field[$plugin_name][0]['min_ortro_version'];
            if (version_compare(ORTRO_VERSION, $min_version) == '-1') {
                //Cannot install plugin min version of ortro not satisfied
                $action_msg = MSG_ACTION_MIN_VERSION_REQUIRED . $min_version;
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
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->checkJobPlugin($id_job_type), 
                                  MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg = MSG_ACTION_REMOVE_JOB_FIRST . $job_list;
            $type_msg   = 'warning';
            $error      = true;
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
    //No error found !!!
    $redirect_to_view = true;
    
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD PLUGIN */
        include_once 'Archive/Tar.php';
                        
        $tar_object = new Archive_Tar($file_info['tmp_name']);
        $tar_object->extract(ORTRO_PLUGINS);
            
        //Move the language files starting from untar folder
        $dir_lang_plugin   = ORTRO_PLUGINS . $category . DS . $plugin_name . DS . 
                             'lang' . DS;
        $dir_lang_contents = scandir($dir_lang_plugin);
        foreach ($dir_lang_contents as $item) {
            if (is_dir($dir_lang_plugin . $item) && $item != '.' && $item != '..' &&
                $item != '.svn') {
                $full_path_lang =  ORTRO_LANG . $item . DS . 'plugins' . DS .
                                   $category . DS . $plugin_name . DS;
                @mkdir($full_path_lang, 0700, true);
                @copy($dir_lang_plugin . $item . DS . 'language.php', 
                      $full_path_lang . DS . 'language.php');
            }
        }
        //Remove install language files
        @removeDirectory($dir_lang_plugin);
          
        if (PEAR::isError($tar_object->error_object)) {
            $action_msg = $tar_object->error_object->getMessage() . '<br/>' . 
                          $tar_object->error_object->getDebugInfo();
            $type_msg   = 'warning';
            
            $redirect_to_view = false;
        } else {
            $plugin_path = ORTRO_PLUGINS . $category . DS . $plugin_name . DS;
            $plugin_conf_file = $plugin_path . 'configure.php';
            if (is_file($plugin_conf_file)) {
                i18n($category, $plugin_name);
                include_once $plugin_conf_file;
                if (!$upgrade_plugin) {
                    $dbUtil->dbExec($dbh,
                                     $dbUtil->setPlugin($plugin_name, 
                                     $category));
                    $id_job_type = $dbh->lastInsertID();
                }
                if (is_file($plugin_path . 'configure_metadata.php')) {
                    //Further configuration is required redirect to edit view
                    $redirect_to_view = false;
                }
                $action_msg = MSG_ACTION_PLUGIN_INSTALLED;
                $type_msg   = 'success';    
            } else {
                $action_msg = MSG_ACTION_CONFIGURE_FILE_NOT_FOUND . 
                              $file_info['name'];
                $type_msg   = 'warning';
                
                $redirect_to_view = false;
            }
        }
            
        break;
    case 'edit':
        $plugin_path = ORTRO_PLUGINS . $_REQUEST['plugin_category'] . DS . 
                       $_REQUEST['plugin_name'] . DS;

        if (!is_dir($plugin_path) || (strpos($plugin_path, '..') !== false)) {
            $action_msg       = MSG_ACTION_CONFIGURATION_FILE_NOT_FOUND;
            $type_msg         = 'warning';
            $redirect_to_view = false;
            break;
        }
        i18n($_REQUEST['plugin_category'], $_REQUEST['plugin_name']);
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
           
        $plugin_config_file = ORTRO_CONF_PLUGINS . $_REQUEST['plugin_category'] . 
                              '_' . $_REQUEST['plugin_name'] . '.php';
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
        foreach ($_REQUEST['id_chk'] as $id_job_type => $label) {
            $rows     = $dbUtil->dbQuery($dbh, 
                                          $dbUtil->getJobTypeLabel($id_job_type));
            $category = $rows[0][1];
            $dbUtil->dbExec($dbh, $dbUtil->deletePlugin($id_job_type));
            if ((strpos($category, '..') === false) && (strpos($label, '..') === false)) {
                //remove configuration files
                @unlink(ORTRO_CONF_PLUGINS . $category . '_' . $label . '.php');
                //remove plugin dir
                @removeDirectory(ORTRO_PLUGINS . $category . DS . $label);
                //remove plugin language files
                $dir_lang_contents = scandir(ORTRO_LANG);
                foreach ($dir_lang_contents as $item) {
                    if (is_dir(ORTRO_LANG . $item) && $item != '.' && $item != '..' &&
                        $item != '.svn') {
                        @removeDirectory(ORTRO_LANG . $item . DS . 'plugins' . DS .
                                          $category . DS . $label);
                    }
                }
            }
        }
        $action_msg = MSG_ACTION_PLUGIN_DELETED;
        $type_msg   = 'success';
        break;
    }
    if ($_REQUEST['mode'] == 'add' && !$redirect_to_view) {
        $_REQUEST['mode'] = 'edit';
    }
    $_REQUEST['action'] = '';
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=plugin&mode=view');
    exit;    
}
?>