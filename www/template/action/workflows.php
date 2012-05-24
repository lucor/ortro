<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the workflows defined in ortro.
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

i18n('template', 'common.php');

$page_to_display = '';
$label           = '';

/* common actions */
if (isset($_REQUEST['id_workflow']) && $_REQUEST['action'] != 'add') {
    $action_from_view_section = false;
    $id_workflow              = $_REQUEST['id_workflow'];
    $id_system                = $_REQUEST['id_system'];
} else {
    $action_from_view_section = true;
    $id_workflow              = @key($_REQUEST['id_chk']);
}

if ($_REQUEST['mode'] == 'details' && $_REQUEST['action'] == 'edit' &&
    isset($_REQUEST['id_chk'])) {
    //display the job details
    $job_to_exec_prefix = 'step_' . key($_REQUEST['id_chk']) . '_job_to_exec';
    $id_job             = $_REQUEST[$job_to_exec_prefix];
    $qs                 = 'mode=details&cat=jobs&action=&id_chk[' . $id_job . ']=' .
    $id_system;
    if (is_numeric($id_job) && (is_numeric($id_system))) {
        header('location:?' . $qs);
    }
    exit;
}

if ($_REQUEST['action'] == 'edit' && isset($_REQUEST['id_chk'])) {
    //display the job details
    $job_to_exec_prefix = 'step_' . key($_REQUEST['id_chk']) . '_job_to_exec';
    $id_job             = $_REQUEST[$job_to_exec_prefix];
    $qs                 = 'mode=edit&cat=jobs&action=&id_chk[' . $id_job . ']=' .
    $id_system;
    if (is_numeric($id_job) && (is_numeric($id_system))) {
        header('location:?' . $qs);
    }
    exit;
}

/* required for add */
if ($_REQUEST['action'] == 'add') {
    $label           = $_REQUEST['label'];
    $description     = $_REQUEST['description'];
    $on_success_step = 0;
    $on_error_step   = 0;
    $on_success_when = 'R';
    $on_error_when   = 'R';
    $id_system       = $_REQUEST['systemJob'][0];
    $id_job          = $_REQUEST['systemJob'][1];
    $id_workflow     = '0';
}

/* required for add/edit workflow step */
$on_result_value = '';
if (isset($_REQUEST['on_success']) && $_REQUEST['on_success'] != '') {
    $on_result            = 'on_success';
    $step                 = $_REQUEST['on_success'];
    $on_result_value      = $_REQUEST['step_' . $step . '_on_success'];
    $on_result_when_value = '';
    if (isset($_REQUEST['step_' . $step . '_on_success_when'])) {
        $on_result_when_value = $_REQUEST['step_' . $step . '_on_success_when'];
    }
}
if (isset($_REQUEST['on_error']) && $_REQUEST['on_error'] != '') {
    $on_result            = 'on_error';
    $step                 = $_REQUEST['on_error'];
    $on_result_value      = $_REQUEST['step_' . $step . '_on_error'];
    $on_result_when_value = '';
    if (isset($_REQUEST['step_' . $step . '_on_error_when'])) {
        $on_result_when_value = $_REQUEST['step_' . $step . '_on_error_when'];
    }
}
switch ($on_result_value) {
case '0':
    $_REQUEST['action'] = 'add_step';
    break;
case '-':
    $_REQUEST['action'] = 'edit_step';
    $on_result_value    = '0';
    break;
case '':
    //do nothing normal edits
    break;
default:
    $_REQUEST['action'] = 'edit_step';
    break;
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;
/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'copy':
case 'lock':
case 'unlock':
case 'details':
case 'add_step':
case 'edit_step':
    // no error check required.
    break;
case 'kill':
    $rows = $dbUtil->dbQuery($dbh, 
                          $dbUtil->getWorkflowBasicInfoById($id_workflow), 
                          MDB2_FETCHMODE_ASSOC);
    if ($rows[0]['status'] != 'R') {
        $action_msg       = MSG_ACTION_WORKFLOW_IS_NOT_RUNNING;
        $type_msg         = 'warning';
        $error            = true;
        $redirect_to_view = true;
    }
    break;
case 'run':
    $rows = $dbUtil->dbQuery($dbh, 
                              $dbUtil->getWorkflowBasicInfoById($id_workflow), 
                              MDB2_FETCHMODE_ASSOC);
    if ($rows[0]['status'] == 'L') {
        $action_msg       = MSG_ACTION_WORKFLOW_IS_LOCKED;
        $type_msg         = 'warning';
        $error            = true;
        $redirect_to_view = true;
    }
    break;
case 'delete':
    if (!$action_from_view_section) {
        $step_list = array();
        foreach ($_REQUEST['id_chk'] as $step=>$value) {
            $rows = $dbUtil->dbQuery($dbh, 
                                      $dbUtil->checkWorkflowStep($id_workflow, 
                                                                 $step), 
                                      MDB2_FETCHMODE_ASSOC);
            if (isset($rows[0]['on_success']) && $rows[0]['on_success'] > 0) {
                $step_list[$rows[0]['on_success']] = 1;
            }
            if (isset($rows[0]['on_error']) && $rows[0]['on_error'] > 0) {
                $step_list[$rows[0]['on_error']] = 1;
            }
        }
        if (count($step_list) > 0) {
            $action_msg      = MSG_ACTION_WORKFLOW_REMOVE_STEP_FIRST . '<br/> - ' . 
                               implode('<br/> - ', array_keys($step_list));
            $type_msg        = 'warning';
            $error           = true;
            $page_to_display = 'edit';
        }
    }
    break;
case 'add':
case 'edit':
    //check for unique job label
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsWorkflow($label));
    if (isset($rows[0][0]) && $rows[0][0] != $id_workflow) {
        //Label alreay used
        $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    unset($rows);
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
        /* ADD WORKFLOW */
        $status = 'L';
        $dbUtil->dbExec($dbh, $dbUtil->setWorkflow($label, 
                                                    $description,
                                                    $status,
                                                    $id_system));
        $id_workflow = $dbh->lastInsertID();
        $step        = 1;
        $status      = '-';
        $dbUtil->dbExec($dbh, $dbUtil->setWorkflowStep($id_workflow,
                                                        $id_job,
                                                        $step,
                                                        $on_success_step,
                                                        $on_success_when,
                                                        $on_error_step,
                                                        $on_error_when,
                                                        $status));
        $crontab = getCrontabValues($_REQUEST);
        $dbUtil->dbExec($dbh, 
                         $dbUtil->setWorkflowCrontab($id_workflow, 
                                                     $crontab['db']['m'], 
                                                     $crontab['db']['h'], 
                                                     $crontab['db']['dom'], 
                                                     $crontab['db']['mon'], 
                                                     $crontab['db']['dow'],
                                                     $crontab['schedule_type']));
        $action_msg      = MSG_ACTION_WORKFLOW_ADDED;
        $type_msg        = 'success';
        $page_to_display = 'edit';
        break;
    case 'add_step':
        /* ADD A WORKFLOW STEP WORKFLOW*/
        $rows = $dbUtil->dbQuery($dbh, $dbUtil->getLastWorkflowStep($id_workflow));
        
        $last_step       = $rows[0][0];
        $new_step        = $last_step+1;
        $id_job          = 0;
        $on_success_step = 0;
        $on_error_step   = 0;
        $when            = 'R';
        $status          = '-';
        $dbUtil->dbExec($dbh, $dbUtil->setWorkflowStep($id_workflow,
                                                        $id_job,
                                                        $new_step,
                                                        $on_success_step,
                                                        $when,
                                                        $on_error_step,
                                                        $when,
                                                        $status));
        $dbUtil->dbExec($dbh, $dbUtil->updateWorkflowStepWorkflow($id_workflow,
                                                                   $step,
                                                                   $on_result,
                                                                   $new_step,
                                                                   $when));
        $action_msg      = MSG_ACTION_WORKFLOW_STEP_ADDED;
        $type_msg        = 'success';
        $page_to_display = 'edit';
        break;
    case 'copy':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $rows = $dbUtil->dbQuery($dbh,
                                  $dbUtil->getWorkflowBasicInfoById($id_workflow),
                                      MDB2_FETCHMODE_ASSOC);

            $label     = $rows[0]['label'];
            $max_limit = true;
            for ($index = 1; $index < 100; $index++) {
                //check for existing copy_label
                $new_label = $label . '_copy_' . $index;
                
                $rows = $dbUtil->dbQuery($dbh,
                                          $dbUtil->checkExistsWorkflow($new_label));
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
                $dbUtil->dbExec($dbh, $dbUtil->copyWorkflow($id_workflow, 
                                                            $new_label));
                $new_id_workflow = $dbh->lastInsertID();
                $dbUtil->dbExec($dbh,
                                 $dbUtil->copyWorkflowStep($id_workflow,
                                                           $new_id_workflow));
                $dbUtil->dbExec($dbh,
                                 $dbUtil->copyWorkflowCrontab($id_workflow,
                                                              $new_id_workflow));

                $action_msg = MSG_ACTION_WORKFLOW_COPIED;
                $type_msg   = 'success';
            }
        }
        break;
    case 'edit':
        /* EDIT WORKFLOW */
        $label       = $_REQUEST['label'];
        $description = $_REQUEST['description'];
        $total_steps = $_REQUEST['total_steps'];

        $dbUtil->dbExec($dbh, $dbUtil->updateWorkflow($id_workflow,
                                                       $label,
                                                       $description));

        for ($step = 1; $step <= $total_steps; $step++) {
            $step_prefix         = 'step_' . $step . '_';
            $key_job_to_exec     = $step_prefix . 'job_to_exec';
            $key_on_success      = $step_prefix . 'on_success';
            $key_on_success_when = $step_prefix . 'on_success_when';
            $key_on_error        = $step_prefix . 'on_error';
            $key_on_error_when   = $step_prefix . 'on_error_when';

            $id_job = $_REQUEST[$key_job_to_exec];
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepActions($id_workflow, 
                                                                $id_job, 
                                                                $step));

            $on_result       = 'on_success';
            $on_result_value = '-';
            if (isset($_REQUEST[$key_on_success])) {
                $on_result_value = $_REQUEST[$key_on_success];
            }
            $on_result_when_value = '';
            if (isset($_REQUEST[$key_on_success_when])) {
                $on_result_when_value = $_REQUEST[$key_on_success_when];
            }
            if (isset($on_result_value) && $on_result_value != '-') {
                $dbUtil->dbExec($dbh, 
                         $dbUtil->updateWorkflowStepWorkflow($id_workflow,
                                                             $step,
                                                             $on_result,
                                                             $on_result_value,
                                                             $on_result_when_value));
            }

            $on_result       = 'on_error';
            $on_result_value = '-';
            if (isset($_REQUEST[$key_on_error])) {
                $on_result_value = $_REQUEST[$key_on_error];
            }
            $on_result_when_value = '';
            if (isset($_REQUEST[$key_on_error_when])) {
                $on_result_when_value = $_REQUEST[$key_on_error_when];
            }
            if (isset($on_result_value) && $on_result_value != '-') {
                $dbUtil->dbExec($dbh, 
                     $dbUtil->updateWorkflowStepWorkflow($id_workflow,
                                                         $step,
                                                         $on_result,
                                                         $on_result_value,
                                                         $on_result_when_value));
            }
        }

        $crontab = getCrontabValues($_REQUEST);
        $dbUtil->dbExec($dbh, 
                         $dbUtil->updateWorkflowCrontab($id_workflow, 
                                                        $crontab['db']['m'], 
                                                        $crontab['db']['h'], 
                                                        $crontab['db']['dom'], 
                                                        $crontab['db']['mon'], 
                                                        $crontab['db']['dow'],
                                                        $crontab['schedule_type']));

        $action_msg      = MSG_ACTION_WORKFLOW_MODIFIED;
        $type_msg        = 'success';
        $page_to_display = 'edit';
        break;
    case 'edit_step':
        /* EDIT WORKFLOW STEP */
        $dbUtil->dbExec($dbh,
                         $dbUtil->updateWorkflowStepWorkflow($id_workflow,
                                                             $step,
                                                             $on_result,
                                                             $on_result_value,
                                                             $on_result_when_value));
        $action_msg      = MSG_ACTION_WORKFLOW_MODIFIED;
        $type_msg        = 'success';
        $page_to_display = 'edit';
        break;

    case 'delete':
        $delete_workflow = false;
        if ($action_from_view_section) {
            //delete all steps
            foreach ($_REQUEST['id_chk'] as $id_workflow=>$value) {
                $dbUtil->dbExec($dbh,
                                 $dbUtil->deleteWorkflowStep($id_workflow, '*'));
            }
            $delete_workflow = true;
        } else {
            foreach ($_REQUEST['id_chk'] as $step=>$value) {
                $dbUtil->dbExec($dbh,
                                 $dbUtil->deleteWorkflowStep($id_workflow, $step));
                $dbUtil->dbExec($dbh,
                    $dbUtil->updateWorkflowStepWorkflowOnDelete($id_workflow,
                                                                $step,
                                                                'on_success'));
                $dbUtil->dbExec($dbh,
                    $dbUtil->updateWorkflowStepWorkflowOnDelete($id_workflow,
                                                                $step,
                                                                'on_error'));
                if ($step == 1) {
                    $delete_workflow = true;
                }
            }
        }
        if ($delete_workflow) {
            if ($action_from_view_section) {
                //delete all workflows as requested
                foreach ($_REQUEST['id_chk'] as $id_workflow=>$value) {
                    $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflow($id_workflow));
                    $dbUtil->dbExec($dbh,
                                     $dbUtil->deleteWorkflowCrontab($id_workflow));
                }
            } else {
                $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflow($id_workflow));
                $dbUtil->dbExec($dbh, 
                                 $dbUtil->deleteWorkflowCrontab($id_workflow));
            }
            $action_msg = MSG_ACTION_WORKFLOW_DELETED;
            $type_msg   = 'success';
        } else {
            $action_msg      = MSG_ACTION_WORKFLOW_STEP_DELETED;
            $type_msg        = 'success';
            $page_to_display = 'edit';
        }
        break;
    case 'lock':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $dbUtil->dbExec($dbh, $dbUtil->updateWorkflowStatus($id_workflow, 'L'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '*', 'L'));
            $action_msg = MSG_ACTION_WORKFLOW_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'unlock':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStatus($id_workflow, 'W'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '*', '-'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '1', 'W'));
            $action_msg = MSG_ACTION_WORKFLOW_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'kill':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            //Get runnig job
            $rows   = $dbUtil->dbQuery($dbh, 
                                        $dbUtil->getWorkflowInfoById($id_workflow,
                                                                    '*','R'), 
                                        MDB2_FETCHMODE_ASSOC);
            $id_job = $rows[0]['id_job'];
            // kill the running job
            $cronUtil = new CronUtil();
            $cronUtil->killJob($id_job);
            $dbUtil->dbExec($dbh, $dbUtil->setJobStatus($id_job, 'W', MSG_EXECUTION_ABORTED));
            // update the workflow status
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStatus($id_workflow, 'W'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '*', '-'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '1', 'W'));
            $action_msg = MSG_ACTION_WORKFLOW_KILLED;
            $type_msg   = 'success';
        }
        break;
    case 'run':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $dbUtil->dbExec($dbh, 
                             $dbUtil->updateWorkflowStatus($id_workflow, 'R'));
            $rows   = $dbUtil->dbQuery($dbh, 
                                        $dbUtil->getWorkflowInfoById($id_workflow,
                                                                    '1'), 
                                        MDB2_FETCHMODE_ASSOC);
            $id_job = $rows[0]['id_job'];

            include_once 'logUtil.php';
            $logger     = new LogUtil('cronUtil.php');
            $log_prefix = '[workflow: ' . $id_workflow . '][step: 1][job: ' . 
                          $id_job . ']';
            $logger->trace('DEBUG', $log_prefix . 
                                    '[mode: manual] workflow started...');

            //Update the step status for this workflow as undefined
            $dbUtil->dbExec($dbh, 
                             $dbUtil->updateWorkflowStepStatus($id_workflow,
                                                               '*',
                                                               '-'));
            //Update the step status as running
            $dbUtil->dbExec($dbh, 
                             $dbUtil->updateWorkflowStepStatus($id_workflow, 
                                                               1, 
                                                               'R'));
            $rows     = $dbUtil->dbQuery($dbh, $dbUtil->getJobType($id_job));
            $label    = $rows[0][0];
            $category = $rows[0][1];
            cronUtil::execJob($id_job, $label, $category);
            $action_msg = MSG_ACTION_WORKFLOW_EXECUTED;
            $type_msg   = 'success';
        }
        break;
    case 'details':
        $page_to_display = 'details';
        break;
    default:
        break;
    }
}


$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($page_to_display == 'edit') {
    //Shows the modify view. The plugin need to load dynamic info
    $qs = 'cat=workflows&mode=edit&id_chk[' . $id_workflow . ']=' . $id_system;
} elseif ($page_to_display == 'details') {
    //$_REQUEST['mode'] = 'view';
} else {
    $_REQUEST['mode'] = 'view';
}
$_REQUEST['action'] = '';

if ($page_to_display == 'edit') {
    header('location:?' . $qs);
    exit;
}

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=workflows&mode=view');
    exit;    
}
?>
