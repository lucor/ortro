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

// Required for calendar
$id_calendar = 1;
if (isset($_REQUEST['calendar_id'])) {
    $id_calendar     = $_REQUEST['calendar_id'];
}

/* required for add */
if ($_REQUEST['action'] == 'add') {
    $id_system = $_REQUEST['wf_node'][1];
    switch ($_REQUEST['wf_node'][0]) {
        case 1:
            $type_to_exec = 'J';
            break;
        case 2:
            $type_to_exec = 'W';
            break;
    }

    $id_to_exec     = $_REQUEST['wf_node'][2];
    $label          = $_REQUEST['label'];
    $description    = $_REQUEST['description'];
    $id_parent_node = '0';
}

/* required for add */

if ($_REQUEST['action'] == 'add_node') {
    $id_system = $_REQUEST['wf_next_node'][1];
    switch ($_REQUEST['wf_next_node'][0]) {
        case 1:
            $type_to_exec = 'J';
            break;
        case 2:
            $type_to_exec = 'W';
            break;
    }

    $id_to_exec     = $_REQUEST['wf_next_node'][2];
    $id_parent_node = $_REQUEST['id_node'];
    $exec_condition = $_REQUEST['wf_exec_condition'];
    $exec_properties = $_REQUEST['wf_exec_properties'];
}

if ($_REQUEST['action'] == 'delete_node' || $_REQUEST['action'] == 'edit_node') {
    $id_node = $_REQUEST['id_node'];
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;
$ajax_response = '';


if($_REQUEST['action'] == 'node_info' || $_REQUEST['action'] == 'update_wf_graph'){
        $id_node = $_REQUEST['id_node'];

        $rows = $dbUtil->dbQuery($dbh, $dbUtil->getJobsLabel(), MDB2_FETCHMODE_ASSOC);
        foreach ($rows as $row) {
            $jobs_label[$row['id_job']] = $row['label'];
        }
        $rows = $dbUtil->dbQuery($dbh, $dbUtil->getWorkflowsLabel(), MDB2_FETCHMODE_ASSOC);
        foreach ($rows as $row) {
            $workflows_label[$row['id_workflow']] = $row['label'];
        }

        unset($rows);
        unset($row);
}

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'copy':
case 'lock':
case 'unlock':
case 'details':
case 'edit_node':
case 'update_wf_graph':
case 'add_node':
case 'node_info':
    // no error check required.
    break;
case 'delete_node':
    $rows = $dbUtil->dbQuery($dbh,
                             $dbUtil->getWorkflowInfoById($id_workflow, $id_node),
                             MDB2_FETCHMODE_ASSOC);

    if (count($rows) != 0) {
        //Label alreay used
        $ajax_response['msg'] = MSG_ACTION_WORKFLOW_REMOVE_STEP_FIRST;
        $error   = true;
    }
    
    $ajax    = true;
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
    case 'update_wf_graph':

        $wf_data = $dbUtil->dbQuery($dbh,
                                    $dbUtil->getWorkflowInfoById($id_workflow),
                                    MDB2_FETCHMODE_ASSOC);
                                
        require_once 'Image/GraphViz.php';

        $outputfile = ORTRO_WEB . '/' . $id_workflow . '.svg';
        $gv = new Image_GraphViz();
        $gv->setAttributes(array(//'label'=> "*** ER diagram for ",
                                 //'labelloc'=>'t',
                                 //'orientation' => 'portrait',
                                 //'labeljust'=>'l',
                                 'ratio'=>'1',
                                 'rankdir' => 'LR',
                           ));
        //$gv->setAttributes($attributes);
        $gv->binPath = '/usr/local/bin/';

        foreach ($wf_data as $row) {

            switch ($row['type_to_exec']) {
            case 'J':
                $node_label = $jobs_label[$row['id_to_exec']];
                break;
            case 'W':
                $node_label = $workflows_label[$row['id_to_exec']];
                break;
            }

            $node_color = '#000000';
            if ($id_node == '-' || $id_node == $row['id_node']) {
                $node_color = '#40e0d0';
            }

            //Create node
            $gv->addNode($row['id_node'],
                         array('URL'   => "javascript:void(top.update_wf_box('\N'));",
                               'label' => $node_label,
                               'shape' => 'box',
                               'color' => $node_color
                               )
                         );
            if ($row['id_parent_node'] != 0) {

                switch ($row['exec_condition']) {
                    case 0:
                        $color = 'green';
                        break;
                    case 1:
                        $color = 'red';
                        break;
                    default:
                        $color = 'black';
                        break;
                }

                $gv->addEdge(array($row['id_parent_node'] => $row['id_node']),
                             array('label' => '',
                                   'color' => $color));
            } else {
                $ajax_response['id_root_node'] = $row['id_node'];
            }
        }
        
        $dotfile = $gv->saveParsedGraph();
        $gv->renderDotFile($dotfile, $outputfile);

        $ajax = true;
        break;
    case 'add':
        /* ADD WORKFLOW */
        $status = 'L';
        $dbUtil->dbExec($dbh, $dbUtil->setWorkflow($label, 
                                                    $description,
                                                    $status,
                                                    $id_system,
                                                    $id_calendar));
        $id_workflow = $dbh->lastInsertID();

        $dbUtil->dbExec($dbh, $dbUtil->setWorkflowNode($id_workflow,
                                                       $id_parent_node,
                                                       $id_to_exec,
                                                       $type_to_exec,
                                                       '',
                                                       '',
                                                       '-'));
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
    case 'add_node':
        
        /* ADD A WORKFLOW NODE*/
        $dbUtil->dbExec($dbh, $dbUtil->setWorkflowNode($id_workflow,
                                                       $id_parent_node,
                                                       $id_to_exec,
                                                       $type_to_exec,
                                                       $exec_condition,
                                                       $exec_properties,
                                                       'W'));
        $ajax = true;

        break;
    case 'lock':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $dbUtil->dbExec($dbh, $dbUtil->updateWorkflowStatus($id_workflow, 'L'));
            //$dbUtil->dbExec($dbh,
            //                $dbUtil->updateWorkflowStepStatus($id_workflow, '*', 'L'));
            $action_msg = MSG_ACTION_WORKFLOW_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'unlock':
        foreach ($_REQUEST['id_chk'] as $id_workflow => $value) {
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStatus($id_workflow, 'W'));
            /*$dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '*', '-'));
            $dbUtil->dbExec($dbh,
                             $dbUtil->updateWorkflowStepStatus($id_workflow, '1', 'W'));
             * 
             */
            $action_msg = MSG_ACTION_WORKFLOW_STATUS_UPDATED;
            $type_msg   = 'success';
        }
        break;
    case 'edit':
        /* EDIT WORKFLOW */
        $label       = $_REQUEST['label'];
        $description = $_REQUEST['description'];
        $total_steps = $_REQUEST['total_steps'];

        $dbUtil->dbExec($dbh, $dbUtil->updateWorkflow($id_workflow,
                                                       $label,
                                                       $description,
                                                       $id_calendar));

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
    case 'delete_node':
        $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflowNode($id_workflow, $id_node));
        $ajax_response['msg'] = MSG_ACTION_WORKFLOW_STEP_DELETED;
        break;
    case 'delete':
        //delete all steps
        foreach ($_REQUEST['id_chk'] as $id_workflow=>$value) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflowNode($id_workflow, '*'));
            $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflow($id_workflow));
                $dbUtil->dbExec($dbh, $dbUtil->deleteWorkflowCrontab($id_workflow));
        }

        $action_msg = MSG_ACTION_WORKFLOW_DELETED;
        $type_msg   = 'success';
        break;
    case 'node_info':
        $rows = $dbUtil->dbQuery($dbh,
                                 $dbUtil->getWorkflowNodeInfo($id_workflow, $id_node),
                                 MDB2_FETCHMODE_ASSOC);

        
        //Label alreay used
        $ajax_response = $rows[0];
        switch ($rows[0]['type_to_exec']) {
            case 'J':
                $ajax_response['label'] = $jobs_label[$rows[0]['id_to_exec']];
                break;
            case 'W':
                $ajax_response['label'] = $workflows_label[$rows[0]['id_to_exec']];
                break;
            }
        
        $ajax    = true;
        break;
    case 'edit_node':
        $exec_condition = $_REQUEST['exec_condition'];
        $exec_properties = $_REQUEST['exec_properties'];
        $dbUtil->dbExec($dbh, $dbUtil->updateWorkflowNodeInfo($id_workflow, $id_node, $exec_condition, $exec_properties));

        $ajax_response['msg'] = MSG_ACTION_WORKFLOW_MODIFIED;
        $ajax    = true;
        break;
    default:
        break;
    }
}


$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($ajax){
    //Do nothing
    if ($ajax_response != '') {
        echo json_encode($ajax_response);
    }
    exit;
} else {
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
}
?>
