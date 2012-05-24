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

if (isset($_REQUEST['id_workflow']) && $_REQUEST['action'] != 'add') {
    $id_workflow = $_REQUEST['id_workflow'];
    $id_system   = $_REQUEST['id_system'];

} else {
    $id_workflow = key($_REQUEST['id_chk']);
    $id_system   = $_REQUEST['id_chk'][$id_workflow];;
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
$f_refresh_html = $form->addElement('select',
                                    'refresh', 
                                    '', 
array('none'=>REFRESH_MANUAL,
                                          '15'=>REFRESH_EVERY_15,
                                          '30'=>REFRESH_EVERY_30,
                                          '60'=>REFRESH_EVERY_60,
                                          '180'=>REFRESH_EVERY_180),
                                    'onchange=\'refreshPage();\'')->toHTML();

$toolbar = createToolbar(array('backPage'=>'default',
                                'reload_page'=>'default',
                                'edit'=>'default',
                                'auto_refresh'=>$f_refresh_html));

// The toolbar javascript is used below $toolbar['javascript'];

foreach ($systemJob as $key) {
    $job_label[$key["id_job"]] = $key["label"];
}

// create the fields for each workflow step already defined.

$table_workflow = new HTML_Table($table_attributes . ' id=table_workflow');

$table_workflow->addRow(array(FIELD_WORKFLOW_STEP,
                              FIELD_WORKFLOW_JOB_TO_EXEC,
                              FIELD_WORKFLOW_STATUS,
                              FIELD_WORKFLOW_STEP_SUCCESS,
                              FIELD_WORKFLOW_STEP_ERROR),
                        '', 'TH');

/**
 * Show image
 *
 * @param string $when available values R or W
 * 
 * @return string The html code for the image
 */

function getWhenImage($when)
{
    switch ($when) {
    case 'R':
        $img_status = 'immediate.png';
        $alt_status = TOOLTIP_STEP_EXEC_IMMEDIATE;
        break;
    case 'W':
        $img_status = 'wait-for-exec.png';
        $alt_status = TOOLTIP_STEP_WAIT_FOR_SCHED;
        break;
    }

    $img_html = '<img src="img/' . $img_status  . '" border="0" title="' .
    $alt_status . '" alt="' . $alt_status . '">';
    return $img_html;
}

foreach ($workflow_info as $row) {
    $step        = $row['step'];
    $step_prefix = 'step_' . $step . '_';

    if ($row['id_job'] != 0) {
        $job_to_exec = $job_label[$row['id_job']];
    } else {
        //New step
        $disable_step_form_fields = 'disabled';
    }

    $on_success      = $row['on_success'];
    $on_error        = $row['on_error'];
    $on_success_when = $row['on_success_when'];
    $on_error_when   = $row['on_error_when'];

    switch ($row["status"]) {
    case 'W':
        $img_status = 'wait.png';
        $alt_status = TOOLTIP_WAIT_FOR_EXEC;
        break;
    case 'R':
        $img_status = 'running.png';
        $alt_status = TOOLTIP_RUNNING;
        break;
    case '1':
        $img_status = 'success.png';
        $alt_status = TOOLTIP_SUCCESS;
        break;
    case '0':
        $img_status = 'warning.png';
        $alt_status = TOOLTIP_ERROR;
        break;
    case '-':
        $img_status = '-';
        $alt_status = '-';
        break;
    }

    if ($img_status != '-') {
        $img_status_html = '<img src="img/' . $img_status  . 
                           '" border="0" title="' . $alt_status . '" alt="' . 
                           $alt_status . '">';
    } else {
        $img_status_html = '-';
    }

    $on_success_html = '&nbsp;';
    $on_error_html   = '&nbsp;';

    if ($on_success != 0) {
        $on_success_html = getWhenImage($on_success_when) . 
                           '&nbsp;&nbsp;<a href="#' . $on_success . '">' . 
                           $on_success . '</a>';
    }
    if ($on_error != 0) {
        $on_error_html = getWhenImage($on_error_when) . 
                         '&nbsp;&nbsp;<a href="#' . $on_error . '">' . 
                         $on_error . '</a>';
    }

    $step_html =  '<a class="anchor" name="' . $step . '">' . $step . '</a>';

    $table_workflow->addRow(array($step_html,
                                  $job_to_exec,
                                  $img_status_html,
                                  $on_success_html,
                                  $on_error_html),
                            'id=step_'. $step, 'TD', false);
}
$table_workflow->updateColAttributes('2', 'align=center');
$table_workflow->updateColAttributes('3', 'valign=top');
$table_workflow->updateColAttributes('4', 'align=top');

/* WORKFLOW PROPERTIES */

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
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title"><?php echo WORKFLOW_DETAILS_TOP; ?></div>
<form <?php echo $formArray['attributes'];  ?>>
<div id="toolbar" class="ortro-table"><?php echo $toolbar['javascript']; ?>
<?php echo $toolbar['header']; ?></div>
<br />
<div class="ortro-table"><?php
//Hidden fields
echo $f_hidden; //hidden field

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM), '', 'TH');
$table->addRow(array($systemJob[0]['name']),
               'align=left valign=top', 'TD', false);
$table->addRow(array(FIELD_LABEL), '', 'TH');
$table->addRow(array($workflow_basic_info[0]['label']),
               'align=left valign=top', 'TD', false);
$table->addRow(array(FIELD_DESCRIPTION), '', 'TH');
$table->addRow(array(rawurldecode($workflow_basic_info[0]['description'])),
               'align=left valign=top', 'TD', false);
$table->display();

$table_workflow->display();

?>
</div>
<div id="toolbar_menu" class="ortro-table"><?php echo $toolbar['menu']; ?>
</div>
</form>