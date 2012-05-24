<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the notify settings
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

$dbUtil        = new DbUtil();
$dbh           = $dbUtil->dbOpenConnOrtro();
$id_notify     = key($_REQUEST['id_chk']);
$id_system     = $_REQUEST['id_chk'][$id_notify];
$notify_label  = $_REQUEST['notify_label'];
$system_name   = $_REQUEST['system_name'];
$job_label     = $_REQUEST['job_label'];
$notifyJobInfo = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getNotifyInfoById($id_notify), 
                                  MDB2_FETCHMODE_ASSOC);
$systemHostDb  = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getSystemJob($id_system),
                                  MDB2_FETCHMODE_ASSOC);
$dbh           = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

// --- Create Job select list fields ---
$id_host = '';
foreach ($systemHostDb as $key) {
    if (array_key_exists('ADMIN', $_policy) || 
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select[$key["id_job"]] = $key["label"];
    }
}
        
$f_jobs_obj = $form->addElement('select', 'id_job', '', $select, '');
$f_jobs_obj->setSelected(array_keys($select, $job_label));
$f_jobs = $f_jobs_obj->toHTML();
        
// --- Create the notify type fields ---        
$tables_plugin_type = '';
$plugin_prefix      = 'notify';

//extract the notify info
$plugin_field_values = $dbUtil->dbUnserialize($notifyJobInfo[0]["parameters"]);

$cfg_file = ORTRO_NOTIFICATION_PLUGINS . $notify_label . DS . 'configure.php';
// Include the plugin language definition
i18n('notification', $notify_label);
require_once $cfg_file;

$table = new HTML_Table($table_attributes . ' id=' . $plugin_prefix . '_1');

$plugin_key   = $plugin_field[$notify_label];
$plugin_title = $plugin_key[0]['title'];

$table->addRow(array($plugin_key[0]['description']), 'colspan=5', 'TD', false);

for ($i = 1; $i < sizeof($plugin_key); $i++) {        
    //It's a form field... continue...
    $plugin_key_name = $plugin_key[$i]['name'];
    
    $temp_form = createDynamicForm($form,
                                   $plugin_key[$i],
                                   $plugin_field_values[$plugin_key_name]);
    $table->addRow(array($temp_form['html']), 'colspan=5', 'TD', false);
}
$tables_plugin_type .= $table->toHTML();

//CHECK BOX FOR NOTIFICATION JOB
$f_notify_on[0] = $form->createElement('checkbox', "notify_on[0]", '', FIELD_NOTIFY_JOB_ERROR);
$f_notify_on[1] = $form->createElement('checkbox', "notify_on[1]", '', FIELD_NOTIFY_JOB_SUCCESS);
$f_notify_on[2] = $form->createElement('checkbox', "notify_on[2]", '', FIELD_NOTIFY_JOB_END);
$f_notify_on[3] = $form->createElement('checkbox', "notify_on[3]", '', FIELD_NOTIFY_JOB_START);

$f_notify_on[0]->updateAttributes(array('value' => 0));
$f_notify_on[1]->updateAttributes(array('value' => 1));
$f_notify_on[2]->updateAttributes(array('value' => 2));
$f_notify_on[3]->updateAttributes(array('value' => 3));

$f_chk_grp = $form->addGroup($f_notify_on, 'ichkNotify', '', array('&nbsp;&nbsp;&nbsp;'), false);
// Simple rule: at least 1 checkboxes should be checked
$form->addGroupRule('ichkNotify', MSG_SELECT_A_NOTIFICATION_ON, 'required', null, 1, 'client');

$notify_on = explode('-', $notifyJobInfo[0]['notify_on']);
array_shift($notify_on);
array_pop($notify_on);

foreach ($notify_on as $value) {
    $f_notify_on[$value]->setChecked(true);
}

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_notify', $id_notify)->toHTML();
$f_hidden .= $form->createElement('hidden', 'notify_type', $notify_label)->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_system', $id_system)->toHTML();
$f_hidden .= $form->createElement('hidden', 'identity', $notifyJobInfo[0]['identity'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'ichkNotify', '')->toHTML();//need it for addGroupRole

$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
    <?php echo NOTIFICATION_EDIT_TOP; ?>
</div>    
<p>
    <?php echo NOTIFICATION_EDIT_TITLE; ?>
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
$table->addRow(array($system_name), '', 'TD', false);
$table->addRow(array(FIELD_JOB), '', 'TH');
$table->addRow(array($f_jobs), '', 'TD', false);
$table->display();

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_NOTIFY_SEND_ON), '', 'TH');
$table->addRow(array($f_chk_grp->toHTML()), '', 'TD', false);
$table->display();

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_NOTIFY_METHOD), "", 'TH');
$table->addRow(array($plugin_title), "", 'TD', false);
$table->display();

echo $tables_plugin_type;

$table = new HTML_Table($table_attributes);
$table->addRow(array($f_submit), "", 'TD', false);
$table->display();
?>
</div>
</form>