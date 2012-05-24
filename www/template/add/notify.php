<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a notification in Ortro
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

$systemJob  = $dbUtil->dbQuery($dbh, $dbUtil->getSystemJob(), 
                                       MDB2_FETCHMODE_ASSOC);
$notifyType = $dbUtil->dbQuery($dbh, $dbUtil->getNotifyTypeList(), 
                                       MDB2_FETCHMODE_ASSOC);
$dbh        = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

/* SYSTEM SYSTEM JOB FIELDS */                
$select1[0]    = FIELD_SELECT_SYSTEM;
$select2[0][0] = FIELD_SELECT_JOB;
$id_host       = '';
foreach ($systemJob as $key) {
    if (array_key_exists('ADMIN', $_policy) || 
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select1[$key['id_system']]                 = $key['name'];
        $select2[$key['id_system']][$key['id_job']] = $key['label'];
    }
}
//used only for apply rules
$f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML(); 
$sel                 = & $form->addElement('hierselect', 'systemHost', '');
$sel->setOptions(array($select1, $select2));
$form->registerRule('checkHier', 'callback', 'checkHier');
$form->addRule('rule', MSG_SELECT_A_SYSTEM, 'checkHier', 'systemHost[0]', 'client');

$f_systemJob = $sel->toHTML();

/* NOTIFY FIELDS */        
$tables_plugin_type   = '';
$plugin_prefix        = 'notify';
$select_pluginType[0] = FIELD_SELECT_NOTIFICATION;
$max_id_notify_type   = 0;
foreach ($notifyType as $key) {
    $cfg_file = ORTRO_NOTIFICATION_PLUGINS . 
                $key['label'] . DS . 'configure.php';
    if (is_file($cfg_file)) {
        // Include the plugin language definition
        i18n('notification', $key['label']);
        include_once $cfg_file;

        $plugin_key = $plugin_field[$key['label']];
        
        $select_pluginType[$key['id_notify_type']] = $plugin_key[0]['title'];

        $table = new HTML_Table($hidden_table_attributes . 
                                ' id=' . $plugin_prefix . 
                                '_' . $key['id_notify_type']);
        $table->addRow(array($plugin_key[0]['description']),
                             'colspan=5', 
                             'TD', 
                             false);
        for ($i = 1; $i < sizeof($plugin_key); $i++) {
            //It's a form field... continue.....
            $field_value = '';
            if (isset($_REQUEST[$plugin_key[$i]['name']])) {
                        $field_value = $_REQUEST[$plugin_key[$i]['name']];
            }
            $temp_form = createDynamicForm($form,
                                             $plugin_key[$i],
                                             $field_value,
                                             'get_metadata_conf_value');
            $table->addRow(array($temp_form['html']),
                                 'colspan=5', 'TD', false);
        }
        $tables_plugin_type .= $table->toHTML();
    }
    if ($key['id_notify_type'] > $max_id_notify_type) {
        $max_id_notify_type = $key['id_notify_type'];
    }
}

$f_plugin_type = $form->addElement('select', 'notify_type', 
                                   '', $select_pluginType,
                                   '  onchange="showFormFields(this.value, \'' .
                                   $plugin_prefix . '_\', ' . 
                                   $max_id_notify_type . ');"')->toHTML();
$form->addRule('notify_type', MSG_SELECT_A_NOTIFICATION_TYPE, 'nonzero', null, 'client');

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


/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

/* HIDDEN FIELDS */
//Input field needed for action type
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'identity', '')->toHTML();
$f_hidden .= $form->createElement('hidden', 'ichkNotify', '')->toHTML();//need it for addGroupRole
$f_hidden .= $f_hidden_rule_field;
//convert form in array for ext;
$formArray = $form->toArray();
echo $formArray['javascript'];
?>  
<!-- start body -->
<div id="ortro-title">
    <?php echo NOTIFICATION_ADD_TOP; ?>
</div>    
<p>
    <?php echo NOTIFICATION_ADD_TITLE; ?>
</p>
<form  <?php echo $formArray['attributes'];?> >
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
$table->addRow(array(FIELD_SYSTEM . ' / ' . FIELD_JOB), '', 'TH');
$table->addRow(array($f_systemJob), '', 'TD', false);
$table->addRow(array(FIELD_NOTIFY_SEND_ON), '', 'TH');
$table->addRow(array($f_chk_grp->toHTML()), '', 'TD', false);
$table->display();

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_NOTIFY_METHOD), "", 'TH');
$table->addRow(array($f_plugin_type), "", 'TD', false);
$table->display();

echo $tables_plugin_type;

$table = new HTML_Table($table_attributes);
$table->addRow(array($f_submit), "", 'TD', false);
$table->display();
?>
</div>
</form>