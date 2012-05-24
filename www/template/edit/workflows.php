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
$systemJob           = $dbUtil->dbQuery($dbh, 
                                         $dbUtil->getSystemJob($id_system), 
                                         MDB2_FETCHMODE_ASSOC);
$workflow_basic_info = $dbUtil->dbQuery($dbh, 
                                   $dbUtil->getWorkflowBasicInfoById($id_workflow), 
                                         MDB2_FETCHMODE_ASSOC);
$workflow_info       = $dbUtil->dbQuery($dbh, 
                                   $dbUtil->getWorkflowInfoById($id_workflow, '*'), 
                                         MDB2_FETCHMODE_ASSOC);
$dbh                 = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default',
                               'add'=>'default',
                               'details'=>'guest',
                               'edit'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* CRONTAB FIELDS */    
$crontab_array = createCrontabHtml($form, $workflow_basic_info[0], 'workflow');

/* SYSTEM SYSTEM JOB AND WORKFLOW SELECT FIELDS */
//create the advancede select list for system->jobs                
$select_job_to_exec[0] = FIELD_SELECT_JOB;

foreach ($systemJob as $key) {
        $select_job_to_exec[$key["id_job"]] = $key["label"];
}

$f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML();

// create the fields for each workflow step already defined.

$table_workflow = new HTML_Table($table_attributes . ' id=table_workflow');

$checkbox = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();

$table_workflow->addRow(array($f_chk_all,
                              FIELD_WORKFLOW_STEP,
                              FIELD_WORKFLOW_JOB_TO_EXEC,
                              FIELD_WORKFLOW_STEP_SUCCESS,
                              FIELD_WORKFLOW_STEP_ERROR), '', 'TH');

//Create the array used to create the select field for adding step
$select_step['-'] = FIELD_SELECT_STEP;
$select_step['0'] = FIELD_SELECT_ADD_A_STEP;
for ($i = 1; $i <= count($workflow_info); $i++) {
    $select_step[$i] = $i;
}

$form->registerRule('checkHier', 'callback', 'checkHier');
foreach ($workflow_info as $row) {
    $step        = $row['step'];
    $step_prefix = 'step_' . $step . '_';
    
    $sel = & $form->addElement('select', 
                               $step_prefix . 'job_to_exec', 
                               '',
                               $select_job_to_exec);

    $js_error_message = '[' . FIELD_WORKFLOW_STEP . ' ' . $step . '] ' . 
                        MSG_SELECT_A_WORKFLOW_OR_JOB;
    $form->addRule('rule', $js_error_message, 'checkHier', 
                   $step_prefix . 'job_to_exec', 'client');

    if ($row['id_job'] != 0) {
        $sel->setSelected($row['id_job']);
        $disable_step_form_fields = '';
    } else {
        //New step
        $disable_step_form_fields = 'disabled';
    }

    $f_job_to_exec = $sel->toHTML();
    $on_success    = $row['on_success'];
    $on_error      = $row['on_error'];
    
    $checkbox = $form->createElement('checkbox', 'id_chk[' . $step . ']', '', '');
    $checkbox->updateAttributes(array('value' => 'step_workflow'));
    $checkbox->updateAttributes(array('id' => 'id_chk'));
    $checkbox->updateAttributes(array('role' => 'admin'));
    $f_chk = $checkbox->toHTML();    

    $select_step_on_success = $select_step;
    $selected_on_success    = '-';
    unset($select_step_on_success[$step]);
    if ($on_success != 0) {
        unset($select_step_on_success['0']);
        $selected_on_success   = $on_success;
        $hide_checkbox_success = false;
    } else {
        $hide_checkbox_success = true;
    }
    
    $sel_on_success = $form->addElement('select', $step_prefix . 'on_success',
                                        '', 
                                        $select_step_on_success, 
                                        $disable_step_form_fields . 
                                        ' onchange="this.form.on_success.value=\'' .
                                        $step . '\';this.form.submit();"');
    $sel_on_success->setSelected($selected_on_success);
    
    if (!$hide_checkbox_success) {
        $check_success_1 =& 
            $form->addElement('radio', 
                              $step_prefix . 'on_success_when',
                              null,
                              '<img src="img/immediate.png" title="' .
                              TOOLTIP_STEP_EXEC_IMMEDIATE .  '">', 
                              'R');
    
        $check_success_2 =& 
            $form->addElement('radio',
                              $step_prefix . 'on_success_when',
                              null,
                              '<img src="img/wait-for-exec.png" title="' . 
                              TOOLTIP_STEP_WAIT_FOR_SCHED . '">',
                              'W');
        if ($row['on_success_when'] == 'W') {
            $check_success_2->setChecked(true);    
        } else {
            $check_success_1->setChecked(true);
        }
        $check_success_html = $check_success_1->toHTML() . '&nbsp;' . 
                              $check_success_2->toHTML();
    } else {
        $check_success_html = '&nbsp;';
    }
    
    $select_step_on_error = $select_step;
    $selected_on_error    = '-';
    unset($select_step_on_error[$step]);
    if ($on_error != 0) {
        unset($select_step_on_error['0']);
        $selected_on_error   = $on_error;
        $hide_checkbox_error = false;
    } else {
        $hide_checkbox_error = true;
    }
        
    $sel_on_error = & $form->addElement('select', 
                                        $step_prefix . 'on_error',
                                        '', 
                                        $select_step_on_error, 
                                        $disable_step_form_fields . 
                                        ' onchange="this.form.on_error.value=\'' .
                                        $step . '\';this.form.submit();"');
    $sel_on_error->setSelected($selected_on_error);
    
    if (!$hide_checkbox_error) {
        $check_error_1 =& $form->addElement('radio',
                                            $step_prefix . 'on_error_when',
                                            null,
                                            '<img src="img/immediate.png" title="' . 
                                            TOOLTIP_STEP_EXEC_IMMEDIATE .  '">', 
                                            'R');
        $check_error_2 =& 
            $form->addElement('radio',
                              $step_prefix . 'on_error_when',
                              null,
                              '<img src="img/wait-for-exec.png" title="' . 
                              TOOLTIP_STEP_WAIT_FOR_SCHED . '">',
                              'W');
        if ($row['on_error_when'] == 'W') {
            $check_error_2->setChecked(true);    
        } else {
            $check_error_1->setChecked(true);
        }
        $check_error_html = $check_error_1->toHTML() . 
                            '&nbsp;' . 
                            $check_error_2->toHTML();
    } else {
        $check_error_html = '&nbsp;';
    }

    $cell_success = $sel_on_success->toHTML() . '&nbsp;' . $check_success_html;
    $cell_error   = $sel_on_error->toHTML() . '&nbsp;' . $check_error_html;
                                     
    $table_workflow->addRow(array($f_chk,
                                  $step,
                                  $f_job_to_exec,
                                  $cell_success,
                                  $cell_error), 
                            'valign=top id=step_'. $step, 'TD', false);
}
$table_workflow->updateColAttributes('0', 'width=1%');

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
$f_hidden .= $form->createElement('hidden', 'id_workflow', $id_workflow)->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_system', $id_system)->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'is_admin_for_a_system', 
                                  true)->toHTML();
$f_hidden .= $form->createElement('hidden', 'total_steps', $step)->toHTML();
$f_hidden .= $form->createElement('hidden', 'on_success', '')->toHTML();
$f_hidden .= $form->createElement('hidden', 'on_error', '')->toHTML();
$f_hidden .= $f_hidden_rule_field;

//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo WORKFLOW_EDIT_TOP; ?>
</div>
<p>
<?php echo WORKFLOW_EDIT_TITLE; ?>
</p>
<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
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

$table_workflow->display();

$table = new HTML_Table($table_attributes);
$table->addRow(array($f_submit), "", 'TD', false);
$table->display();

?>
</div>
<div id="toolbar_menu" class="ortro-table">
    <?php echo $toolbar['menu']; ?>
</div>
</form>
