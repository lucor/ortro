<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the workflow settings
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

/* common actions */
if (isset($_REQUEST['id_workflow']) && $_REQUEST['action'] != 'add') {
    $id_workflow = $_REQUEST['id_workflow'];
    $id_system   = $_REQUEST['id_system'];
    
    $action_from_view_section = false;
} else {
    $id_workflow = @key($_REQUEST['id_chk']);
    $id_system   = $_REQUEST['id_chk'][$id_workflow];
    
    $action_from_view_section = true;
    unset($_REQUEST['id_chk']);
}

$dbUtil              = new DbUtil();
$dbh                 = $dbUtil->dbOpenConnOrtro();
$systemJob           = $dbUtil->dbQuery($dbh, $dbUtil->getSystemJob($id_system), MDB2_FETCHMODE_ASSOC);
$workflow_basic_info = $dbUtil->dbQuery($dbh, $dbUtil->getWorkflowBasicInfoById($id_workflow), MDB2_FETCHMODE_ASSOC);
$workflow_node_info  = $dbUtil->dbQuery($dbh, $dbUtil->getWorkflowInfoById($id_workflow,0), MDB2_FETCHMODE_ASSOC);
$workflows           = $dbUtil->dbQuery($dbh, $dbUtil->getWorkflows(), MDB2_FETCHMODE_ASSOC);

/* SYSTEM SYSTEM JOB AND WORKFLOW SELECT FIELDS */

$select1[0] = '--- Workflow / Job ---';
$select1[1] = 'Job';
$select1[2] = 'Workflow';
$select2[0][0]    = FIELD_SELECT_SYSTEM;
$select2[1][0]    = FIELD_SELECT_SYSTEM;
$select2[2][0]    = FIELD_SELECT_SYSTEM;
$select3[0][0][0] = FIELD_SELECT_LABEL;
$select3[1][0][0] = FIELD_SELECT_LABEL;
$select3[2][0][0] = FIELD_SELECT_LABEL;

$id_host = '';
foreach ($systemJob as $key) {
    if (array_key_exists('ADMIN', $_policy) ||
        in_array($key['id_system'],
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select2[1][$key["id_system"]]                 = $key["name"];
        $select3[1][$key["id_system"]][$key["id_job"]] = $key["label"];
    }
}

foreach ($workflows as $key) {
    if ($key["id_workflow"] != $id_workflow &&
        array_key_exists('ADMIN', $_policy) ||
        in_array($key['id_system'],
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select2[2][$key["id_system"]]                 = $key["system_label"];
        $select3[2][$key["id_system"]][$key["id_workflow"]] = $key["label"];
    }
}

/* CALENDARS */
$calendars[1] = FIELD_SELECT_LABEL;
$dbUtil    = new DbUtil();
$dbh       = $dbUtil->dbOpenConnOrtro();
$rows_cal = $dbUtil->dbQuery($dbh, $dbUtil->getCalendarBySystem($id_system), MDB2_FETCHMODE_ASSOC);
foreach ($rows_cal as $row) {
    $calendars[$row['id_calendar']] = $row['label'];
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);


/* FORM WF NODE */

//Create the form
$form_wf_node = new HTML_QuickForm('frm_wf_node', 'post');

$f_hidden_rule_field = $form_wf_node->addElement('hidden', 'rule', 'rule')->toHTML();

$sel = & $form_wf_node->addElement('hierselect', 'wf_next_node', '');
$sel->setOptions(array($select1, $select2, $select3));
$form_wf_node->registerRule('checkHier', 'callback', 'checkHier');
$form_wf_node->addRule('rule', MSG_SELECT_A_WORKFLOW_OR_JOB, 'checkHier', 'wf_next_node[1]', 'client');

$f_wf_next_node = $sel->toHTML();

//Executeif select list
$f_wf_exec_condition = $form_wf_node->addElement('select', 'wf_exec_condition', '', array(0 => 'Success', 1 => 'Error'))->toHTML();

//Execute mode select list
$f_wf_exec_properties = $form_wf_node->addElement('select', 'wf_exec_properties', '', array('I' => 'Run immediately', 'W' => 'Wait for next node sched'))->toHTML();

//Executeif select list
$f_wf_exec_condition_current = $form_wf_node->addElement('select', 'wf_exec_condition_current', '', array(0 => 'Success', 1 => 'Error'), 'id="wf_exec_condition_current"')->toHTML();

//Execute mode select list
$f_wf_exec_properties_current = $form_wf_node->addElement('select', 'wf_exec_properties_current', '', array('I' => 'Run immediately', 'W' => 'Wait for next node sched'), 'id="wf_exec_properties_current"')->toHTML();



/* HIDDEN FIELDS */
$f_hidden_wf_node  = $form_wf_node->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden_wf_node .= $form_wf_node->createElement('hidden', 'action', 'add_node')->toHTML();
$f_hidden_wf_node .= $form_wf_node->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden_wf_node .= $form_wf_node->createElement('hidden', 'id_workflow', $id_workflow, '')->toHTML();
$f_hidden_wf_node .= $form_wf_node->createElement('hidden', 'id_node', $workflow_node_info[0]['id_node'], 'id="id_wf_node"')->toHTML();

$f_hidden_wf_node .= $f_hidden_rule_field;


/* FORM WF PROPERTIES */

//Create the form
$form = new HTML_QuickForm('frm', 'post');

// Calendar field
$f_calendar = $form->addElement('select', 'calendar_id', '', $calendars);
$f_calendar->setValue($workflow_basic_info[0]['calendar']);

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default',
                               'add'=>'default',
                               'details'=>'guest',
                               'edit'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* CRONTAB FIELDS */    
$crontab_array = createCrontabHtml($form, $workflow_basic_info[0], 'workflow');

/* WORKFLOW PROPERTIES */
$f_label = $form->addElement('text', 'label', '', 'id=label size=50');
$f_label->setValue($workflow_basic_info[0]['label']);
$f_label_html = $f_label->toHTML();

$f_description = $form->addElement('textarea', 'description', '', 
                                   'id=description rows=4 cols=50');
$f_description->setValue(rawurldecode($workflow_basic_info[0]['description']));
$f_description_html = $f_description->toHTML();

$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_workflow', $id_workflow, 'id="id_wf"')->toHTML();
//$f_hidden .= $form->createElement('hidden', 'id_wf_node', 1)->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'is_admin_for_a_system', 
                                  true)->toHTML();
//$f_hidden .= $form->createElement('hidden', 'total_steps', $step)->toHTML();
//$f_hidden .= $form->createElement('hidden', 'on_success', '')->toHTML();
//$f_hidden .= $form->createElement('hidden', 'on_error', '')->toHTML();

//convert form in array for extact js and attributes
$formArray = $form->toArray();
$formArray_wf_node = $form_wf_node->toArray();
echo $formArray['javascript'];
echo $formArray_wf_node['javascript'];
?>

<!-- start body -->
<div id="ortro-title">
<?php echo WORKFLOW_EDIT_TOP; ?>
</div>
<p>
<?php echo WORKFLOW_EDIT_TITLE; ?>
</p>


<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div id="wf_graph">
    <h3><a href="#">Workflow Chart</a></h3>
    <div style="height: 300px;">
        <object id="wf_svg" width="100%" height="100%" type="image/svg+xml" data=""/>
    </div>
</div>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Workflow</a></li>
        <li><a href="#tabs-2">Properties</a></li>
    </ul>
    <div class="ortro-table">
    <div id="tabs-2">
    <form  <?php echo $formArray['attributes']; ?> >
        <?php
        //Hidden fields
        echo $f_hidden; //hidden field

        $table = new HTML_Table($table_attributes);
        $table->addRow(array(FIELD_SYSTEM), '', 'TH');
        $table->addRow(array($systemJob[0]['name']), 'align=left valign=top', 'TD', false);
        $table->addRow(array(FIELD_LABEL), '', 'TH');
        $table->addRow(array($f_label_html), 'align=left valign=top', 'TD', false);
        $table->addRow(array(FIELD_DESCRIPTION), '', 'TH');
        $table->addRow(array($f_description_html), 'align=left valign=top', 'TD', false);
        $table->display();

        echo $crontab_array['html'];

        $table = new HTML_Table($table_attributes);
        $table->addRow(array(CALENDAR_LABEL), '', 'TH');
        $table->addRow(array($f_calendar->toHtml()), '', 'TD', false);
        $table->display();
        ?>
        <div><a id="update_properties" href="#">Update properties</a></div>
    </form>
    </div>
    <div id="tabs-1">
        <form  <?php echo $formArray_wf_node['attributes']; ?> >
        <?php
            //Hidden fields
            echo $f_hidden_wf_node; //hidden field
        ?>
            <fieldset>
                <legend>Current node</legend>
                <div id="wf_current_node_label">Task to execute: <span></span></div>
                <div id="wf_current_edit">
                    Execute if: <?php echo $f_wf_exec_condition_current; ?><br/>
                    Execute mode: <?php echo $f_wf_exec_properties_current; ?><br/>
                    <a id="edit_node" href="#">Edit</a>
                </div>
                <a id="delete_node" href="#">Delete</a>
            </fieldset>
            <fieldset>
                <legend>Add node</legend>
                <div id="wf_next_node">Task to execute: <?php echo $f_wf_next_node; ?></div>
                <div id="wf_exec_if">Execute if: <?php echo $f_wf_exec_condition; ?></div>
                <div id="wf_exec_mode">Execute mode: <?php echo $f_wf_exec_properties; ?></div>
                <div><a id="add_node" href="#">Add</a></div>
            </fieldset>
        </form>
    </div>
    </div>
</div>
<div id="toolbar_menu" class="ortro-table">
    <?php echo $toolbar['menu']; ?>
</div>
