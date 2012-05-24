<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the jobs defined in ortro.
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

if (isset($_REQUEST['systemHostDb'])) {
    $id_system = $_REQUEST['systemHostDb'][0];
    $id_host   = $_REQUEST['systemHostDb'][1];
    $id_db     = $_REQUEST['systemHostDb'][2];
}
if (isset($_REQUEST['job_type'])) {
    $job_type_category = $_REQUEST['job_type'][0];
    $id_job_type       = $_REQUEST['job_type'][1];
}
if (isset($_REQUEST['label'])) {
    $label = $_REQUEST['label'];
}
if (isset($_REQUEST['description'])) {
    $description = $_REQUEST['description'];
}
if (isset($_REQUEST['priority'])) {
    $priority = $_REQUEST['priority'];
}
$id_job         = '';
$job_type_label = '';

if (isset($_REQUEST['identity']) && $_REQUEST['identity'] != '') {
    $identity = $_REQUEST['identity'];    
} else {
    $identity = 0;
}

$properties = array();
if (isset($_REQUEST['properties_max_check_attempts'])) {
    $properties['max_check_attempts'] = $_REQUEST['properties_max_check_attempts'];
    $properties['delay_retry']        = $_REQUEST['properties_delay_retry'];    
}

/* required for edit */
if ($_REQUEST['action'] == 'edit') {
    $job_type_label = $_REQUEST['job_type_label'];
    $id_job_type    = $_REQUEST['id_job_type'];
    $label          = $_REQUEST['label'];
    $description    = $_REQUEST['description'];
    $priority       = $_REQUEST['priority'];
    $id_job         = $_REQUEST['id_job'];
}

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'copy':
case 'lock':
case 'unlock':
case 'details':
    break;
case 'kill':
    // No error check
    $redirect_to_view = true;
    foreach ($_REQUEST['id_chk'] as $id_job => $system) {       
        $rows   = $dbUtil->dbQuery($dbh, $dbUtil->getJobStatus($id_job));
        $result = $rows[0][0];
        switch ($result) {
        case 'W':
            $action_msg = MSG_ACTION_JOB_IS_NOT_RUNNING;
            $type_msg   = 'warning';
            $error      = true;
            break;
        case 'R':
            // Ok, job is running, try to kill.
            break;
        case 'L':
            $action_msg = MSG_ACTION_JOB_IS_LOCKED;
            $type_msg   = 'warning';
            $error      = true;
            break;
        default:
            $action_msg = MSG_ACTION_STRANGE_STATUS;
            $type_msg   = 'warning';
            $error      = true;
            break;
        }
    }
    break;
case 'run':
    $redirect_to_view = true;
    
    //Get all available shd
    $locked_shd_result = $dbUtil->dbQuery($dbh, $dbUtil->getLockedSystemHostDb());
    
    $locked_shd_arr = array();
    if (count($locked_shd_result)>0) {
        foreach ($locked_shd_result as $key=>$row) {
            $locked_shd_arr[] = $row[0];
        }
    }
    
    foreach ($_REQUEST['id_chk'] as $id_job => $system) {       
        $rows       = $dbUtil->dbQuery($dbh, $dbUtil->getJobStatus($id_job));
        $job_status = $rows[0][0];
        $job_id_shd = $rows[0][1];
        switch ($job_status) {
        case 'W':
            // Ok, correct status -> continue.
            break;
        case 'R':
            $action_msg = MSG_ACTION_JOB_IS_RUNNING;
            $type_msg   = 'warning';
            $error      = true;
            break;
        case 'L':
            $action_msg = MSG_ACTION_JOB_IS_LOCKED;
            $type_msg   = 'warning';
            $error      = true;
            break;
        default:
            $action_msg = MSG_ACTION_STRANGE_STATUS;
            $type_msg   = 'warning';
            $error      = true;
            break;
        }
        //system or host locked...
        if (in_array($job_id_shd, $locked_shd_arr)) {
            $action_msg = MSG_ACTION_SYSTEM_HOST_LOCKED;
            $type_msg   = 'warning';
            $error      = true;
        }
    }
    break;
case 'delete':
    foreach ($_REQUEST['id_chk'] as $id_job => $system) {
        $rows   = $dbUtil->dbQuery($dbh, $dbUtil->checkJobNotify($id_job));
        $result = $rows[0][0];
        if ($result != '0') {    
            //Notify present for this job!!!
            $action_msg       = MSG_ACTION_REMOVE_ALL_NOTIFICATIONS;
            $type_msg         = 'warning';
            $error            = true;
            $redirect_to_view = true;
            break;
        }
    }
    break;
case 'add':
case 'edit':
    //check for unique job label 
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsJob($label));
        
    if (isset($rows[0][0]) && $rows[0][0] != $id_job) {
        //Label alreay used
        $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
 
    // --- check for plugin requirement --- //
    // -- DB Plugin -- //
    $rows              = $dbUtil->dbQuery($dbh,
                                           $dbUtil->getJobTypeLabel($id_job_type));
    $job_type_label    = $rows[0][0];
    $job_type_category = $rows[0][1];

    if ($job_type_category == 'database' && $id_db==1) {
        //DB Plugin and NO db selected
        $action_msg = MSG_ACTION_PLUGIN_REQUIRE_DB;
        $type_msg   = 'warning';
        $error      = true;
    }
    
    $cfg_file = ORTRO_PLUGINS . $job_type_category . DS .
                                   $job_type_label . DS .'configure.php';
    if (is_file($cfg_file)) {
        i18n('template', 'common.php');
        i18n($job_type_category, $job_type_label);
        include_once $cfg_file;
        $plugin_key = $plugin_field[$job_type_label];
        $file_tmp   = array();
        $form_tmp   = new HTML_QuickForm('frm_tmp', 'post');
    } else {
        $action_msg = MSG_ACTION_CONFIGURE_FILE_NOT_FOUND;
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
    // No error found !!!
    $redirect_to_view = true;
    
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD JOB */
        //Get the shd_id
        $rows  = $dbUtil->dbQuery($dbh, $dbUtil->getSystemHostDbId($id_system,
                                                                    $id_host,
                                                                    $id_db));
        $shdId = $rows[0][0];

        
        $parameters = array();
        $file_idx   = 0;

        for ($i = 1; $i < sizeof($plugin_key); $i++) {
            $parameters[$plugin_key[$i]['name']] = '';
            if (isset($_REQUEST[$plugin_key[$i]['name']])) {
                $parameters[$plugin_key[$i]['name']] = $_REQUEST[$plugin_key[$i]['name']];
            }
            if ($plugin_key[$i]['type'] == 'file') {
                //Maybe a file is expected...
                if (isset($_FILES[$plugin_key[$i]['name']])) {
                    //There are files to attach will do it later 
                    //now store only the field name in the database
                    $parameters[$plugin_key[$i]['name']] = $plugin_key[$i]['name'];
                    $file_tmp[$file_idx] =& $form_tmp->addElement('file', $plugin_key[$i]['name'], '', '');

                    if (!$file_tmp[$file_idx]->isUploadedFile() && $plugin_key[$i]['required']) {
                        $action_msg = PLUGIN_REQUIRED_PREFIX .
                                      $plugin_key[$i]['description'] . '<br>' .
                                      MSG_ACTION_PROBLEM_DURING_TRANSFER .
                                      ini_get('upload_max_filesize');
                        $type_msg   = 'warning';
                        $error      = true;
                    }
                    $file_idx++;
                }
            }
        }
        
        if ($error) {
            break; 
        }
        $dbUtil->dbExec($dbh, $dbUtil->setJob($shdId, 
                                               $id_job_type, 
                                               $label, 
                                               $description, 
                                               $priority, 
                                               $dbUtil->dbSerialize($parameters),
                                               $identity,
                                               $dbUtil->dbSerialize($properties)));
        $id_job  = $dbh->lastInsertID();
        $crontab = getCrontabValues($_REQUEST);
        $dbUtil->dbExec($dbh, $dbUtil->setJobCrontab($id_job, $crontab['db']['m'],
                         $crontab['db']['h'], $crontab['db']['dom'],
                         $crontab['db']['mon'], $crontab['db']['dow'],
                         $crontab['schedule_type']));
                         
        if (count($file_tmp > 0)) {
            //create dir for file storing
            $path_upload = ORTRO_ATTACHMENTS . $id_job . DS;
            mkdir($path_upload, 0700, true);
            
            foreach ($file_tmp as $uploaded_file) {
                //the files are stored in according to configuration file.
                //user filename is ignored
                $uploaded_file->moveUploadedFile($path_upload, $uploaded_file->getName());
            }
        }

        $action_msg = MSG_ACTION_JOB_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT JOB */    
        //Get the shd_id
        $rows  = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getSystemHostDbId($id_system, 
                                                             $id_host,
                                                             $id_db));
        $shdId = $rows[0][0];
        
        $parameters   = array();
        $dynamic_info = false;
        for ($i = 1; $i < sizeof($plugin_key); $i++) {
            if ($plugin_key[$i]['name'] != $job_type_label.'_get_dynamic_params') {
                $parameters[$plugin_key[$i]['name']] = $_REQUEST[$plugin_key[$i]['name']];
            } else {
                $dynamic_info = true;
            }
            if ($plugin_key[$i]['type'] == 'file') {
                //Maybe a file is expected...
                if (isset($_FILES[$plugin_key[$i]['name']])) {
                    //There are files to attach will do it later 
                    //now store only the field name in the database
                    $parameters[$plugin_key[$i]['name']] = $plugin_key[$i]['name'];
                    $file_tmp[$file_idx] =& $form_tmp->addElement('file', $plugin_key[$i]['name'], '', '');

                    if (!$file_tmp[$file_idx]->isUploadedFile() && $plugin_key[$i]['required']) {
                        $action_msg = PLUGIN_REQUIRED_PREFIX .
                                      $plugin_key[$i]['description'] . '<br>' .
                                      MSG_ACTION_PROBLEM_DURING_TRANSFER .
                                      ini_get('upload_max_filesize');
                        $type_msg   = 'warning';
                        $error      = true;
                    }
                    $file_idx++;
                }
            }
        }
        
        if ($error) {
            break; 
        }
        
        if ($dynamic_info) {
            //get the dynamic parameter and store them
            foreach ($_REQUEST as $key => $value) {
                if (!(strpos($key, 'dynamic_field_') === false)) {
                    if (isset($value) && $value != '') {
                        $parameters['dyn_params'][str_replace('dynamic_field_', '', $key)] = $value;
                    }
                }
            }
        }
        
        if (isset($_REQUEST[$job_type_label.'_get_dynamic_params'])) {
            $redirect_to_view = false;
        }
        
        $dbUtil->dbExec($dbh, $dbUtil->updateJob($id_job,
                                                 $shdId, 
                                                 $id_job_type, 
                                                 $label, 
                                                 $description, 
                                                 $priority, 
                                                 $dbUtil->dbSerialize($properties),
                                                 $dbUtil->dbSerialize($parameters),
                                                 $identity));
        $crontab = getCrontabValues($_REQUEST);
        $dbUtil->dbExec($dbh, $dbUtil->updateJobCrontab($id_job,
                         $crontab['db']['m'], $crontab['db']['h'],
                         $crontab['db']['dom'], $crontab['db']['mon'],
                         $crontab['db']['dow'], $crontab['schedule_type']));
                         
        //store uploaded files
        $path_upload = ORTRO_ATTACHMENTS . $id_job . DS;
        
        foreach ($file_tmp as $uploaded_file) {
            //the files are stored in according to configuration file.
            //the filename sqpecified by the user is ignored
            $uploaded_file->moveUploadedFile($path_upload, $uploaded_file->getName());
        }
                         
        $action_msg = MSG_ACTION_JOB_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        require_once 'ioUtil.php';
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $dbUtil->dbExecMulti($dbh, $dbUtil->deleteJob($id_job));
            if (is_dir(ORTRO_ATTACHMENTS . $id_job)) {
                removeDirectory(ORTRO_ATTACHMENTS . $id_job);
            }
        }
        $action_msg = MSG_ACTION_JOB_DELETED;
        $type_msg   = 'success';
        break;
    case 'copy':
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $rows      = $dbUtil->dbQuery($dbh, $dbUtil->getJobsLabel($id_job));
            $label     = $rows[0][1];
            $max_limit = true;
            for ($index = 1; $index < 100; $index++) {
                //check for existing copy_label
                $new_label = $label . '_copy_' . $index;
                $rows      = $dbUtil->dbQuery($dbh,
                                               $dbUtil->checkExistsJob($new_label));

                if (!isset($rows[0][0])) {
                    $max_limit = false;
                    break; 
                }
                unset($rows);
            }
            if ($max_limit) {
                $action_msg = MSG_ACTION_MAX_NUM_OF_COPY;
                $type_msg   = 'warning';
                break;
            } else {
                $dbUtil->dbExec($dbh, $dbUtil->copyJob($id_job, $new_label));
                //check for existing attached files to copy
                $src_dir = ORTRO_ATTACHMENTS . $id_job;
                if (is_dir($src_dir) && (strpos($file_to_download, '..') === false)) {
                    $id_job_new = $dbh->lastInsertID();
                    $dest_dir   = ORTRO_ATTACHMENTS . $id_job_new;
                    $files      = scandir($src_dir);
                    mkdir($dest_dir, 0700, true);
                    foreach ($files as $i => $value) {
                        if (substr($value, 0, 1) != '.') {
                            copy($src_dir . DS . $value, $dest_dir . DS . $value);
                        }
                    }
                }
                $dbUtil->dbExec($dbh, $dbUtil->copyJobCrontab($id_job));
                $action_msg = MSG_ACTION_JOB_COPIED;
                $type_msg   = 'success';
            }
        }
        break;
    case 'run':
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $rows     = $dbUtil->dbQuery($dbh, $dbUtil->getJobType($id_job));
            $label    = $rows[0][0];
            $category = $rows[0][1];
            cronUtil::execJob($id_job, $label, $category);
            $action_msg = MSG_ACTION_JOB_EXECUTED;
            $type_msg   = 'success';
        }
        break;
    case 'lock':
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $dbUtil->dbExec($dbh, $dbUtil->setJobStatus($id_job, 'L'));
            $action_msg = MSG_ACTION_JOB_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'unlock':
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $dbUtil->dbExec($dbh, $dbUtil->setJobStatus($id_job, 'W'));
            $action_msg = MSG_ACTION_JOB_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'kill':
        foreach ($_REQUEST['id_chk'] as $id_job => $system) {
            $cronUtil = new CronUtil();
            $cronUtil->killJob($id_job);
            $dbUtil->dbExec($dbh, $dbUtil->setJobStatus($id_job, 'W', MSG_EXECUTION_ABORTED));
            $action_msg = MSG_EXECUTION_ABORTED;
            $type_msg   = 'success';
        }
        break;
    default:
        break;
    }
    
    if (isset($_REQUEST[$job_type_label.'_get_dynamic_params']) && $id_job != '') {
        //Shows the modify view. The plugin need to load dynamic info 
        $redirect_to_view   = false;
        $_REQUEST['mode']   = 'edit';
        $_REQUEST['id_chk'] = array($id_job => $id_system);
    }
    $_REQUEST['action'] = '';
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=jobs&mode=view');
    exit;    
}
?>
