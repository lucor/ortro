<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to import and export Ortro settings and database.
 * 
 * PHP version 5
 * 
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Alberto Bravi <alberto.bravi@gmail.com>
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

i18n('template', 'common.php');

require_once 'MDB2.php';
require_once 'MDB2/Schema.php';
require_once 'Archive/Tar.php';
require_once 'ioUtil.php';
require_once ORTRO_CONF . 'configure.php';

$error            = false;
$redirect_to_view = false;
$mode             = 'view';

$file_dump    = 'schema.xml';
$date         = date("d_m_y");
$file_backup  = "ortro_settings_$date.tar.gz";
$backup_dir   = ORTRO_CONF."backup".DS;
$old_conf     = $backup_dir."old_ortro_setting_$date.tar.gz";
$sub_temp_dir = ORTRO_TEMP . 'import_export' . DS;
$options_con  = array(
                'log_line_break'     => '<br>',
                'idxname_format'     => '%s',
                'debug'	             => true,
                'quote_identifier'   => false,
                'force_defaults'     => false,
                'portability'        => true,
                'use_transactions'   => false
                );
$dump_options = array(
                'output_mode'        => 'file',
                'output'             => $sub_temp_dir . $file_dump,
                'end_of_line'        => "\n",
                );

if (is_dir($sub_temp_dir)) {
    removeDirectory($sub_temp_dir);
}

mkdir($sub_temp_dir, 0755, true);

switch ($_REQUEST['action']) {
case 'export':
    $error = false;
    break;
case 'import':
    //Check for uploading file.
    $form      = new HTML_QuickForm('frm', 'post');
    $file      =& $form->addElement('file', 'importFile', '', '');
    $file_info = $file->getValue();        
    if (!$file->isUploadedFile()) {
        $action_msg = MSG_ACTION_PROBLEM_DURING_TRANSFER .
                      ini_get('upload_max_filesize');
        $type_msg   = 'warning';
        $error      = true;
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
    $redirect_to_view = true;
    $mode             = 'view';
    
    $dbUtil = new dbUtil();
    $dsn    = $dbUtil->setDSN(array('phptype'  => $GLOBALS['conf']['db']['phptype'],
                                    'hostspec' => $GLOBALS['conf']['db']['host'] . 
                                    ':' . 
                                    $GLOBALS['conf']['db']['port'],
                                    'database' => $GLOBALS['conf']['db']['database'],
                                    'username' => $GLOBALS['conf']['db']['username'],
                                    'password' => $GLOBALS['conf']['db']['password']
                                    ));
    
    $schema =& MDB2_Schema::factory($dsn, $options_con);
    
    switch ($_REQUEST['action']) {
    case 'export':
        //dump database
        $definition = $schema->getDefinitionFromDatabase();
        if (PEAR::isError($definition)) {
            $action_msg = $definition->getMessage();
            $type_msg   = 'warning';
        } else {
            $op = $schema->dumpDatabase($definition, 
            $dump_options, 
            MDB2_SCHEMA_DUMP_ALL);
            if (PEAR::isError($op)) {
                $action_msg = $op->getMessage();
                $type_msg   = 'warning';
            } else {
                $redirect_to_view = true;
            }
        }

        //create a backup tar file
        $file_full      = $sub_temp_dir . $file_backup;
        $file_to_backup = scanDirectories(ORTRO_CONF);
        $archive        = new Archive_Tar($file_full, 'gz');
        $archive->addModify($sub_temp_dir . $file_dump, 
                            '', 
                            $sub_temp_dir);
        foreach ($file_to_backup as $key) {
            $archive->addModify($key, '', ORTRO_CONF);
        }

        //download the backup tar file
        if (file_exists($file_full) && file_exists($sub_temp_dir . $file_dump)) {
            unlink($sub_temp_dir . $file_dump);
            httpDownload($file_backup, $file_full);
            exit;
        } else {
            $action_msg = FIELD_EXPORT_ERROR;
            $type_msg   = 'warning';
        }
        break;

    case 'import':
        $res = $file->moveUploadedFile($sub_temp_dir);
        if ($res) {
            //if not exist backup dir, create it.
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0700, true);
            }

            //backup of old ORTRO_CONF
            $archive        = new Archive_Tar($old_conf, 'gz');
            $list_extracted = scanDirectories(ORTRO_CONF);
            foreach ($list_extracted as $key) {
                if ($archive->addModify($key, '', ORTRO_CONF)) {
                    unlink($key);
                }
            }

            //extract the backup tar file
            $obj = new Archive_Tar($sub_temp_dir . $file_info['name']);
            $obj->extract(ORTRO_CONF);

            //restore database
            $filedb     = ORTRO_CONF . $file_dump;
            $definition = $schema->parseDatabaseDefinitionFile($filedb);
            if (PEAR::isError($definition)) {
                $action_msg = $definition->getMessage();
                $type_msg   = 'warning';
            } else {
                if (isset($_REQUEST['check_dbae'])) {
                    $mdb2 =& MDB2::factory($dsn);
                    if (PEAR::isError($mdb2)) {
                        die($mdb2->getMessage());
                    }
                    $mdb2->loadModule('Manager');
                    if (!$mdb2->dropDatabase($definition['name'])) {
                        die($mdb2);
                    }
                }
                $op = $schema->createDatabase($definition, array(), false);
                if (PEAR::isError($op)) {
                    $action_msg = FIELD_DATABASE_ALREADY_EXISTS;
                    $type_msg   = 'warning';
                } else {
                    $action_msg = FIELD_IMPORT_SUCCESS;
                    $type_msg   = 'success';
                }
                $_SESSION['check_new_settings'] = true;
            }
            removeDirectory($sub_temp_dir);
            unlink(ORTRO_CONF.$file_dump);
        } else {
            $action_msg = MSG_ACTION_FILE_UPLOAD_ERROR;
            $type_msg   = 'warning';
            $mode       = 'view';
        }
        break;
    }

    $_REQUEST['action'] = 'view';
}

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=import_export&mode='. $mode);
    exit;    
}
?>