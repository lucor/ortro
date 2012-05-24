<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a job in Ortro
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

$dbUtil       = new DbUtil();
$dbh          = $dbUtil->dbOpenConnOrtro();
$systemHostDb = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getSystemHostDb(), 
                                  MDB2_FETCHMODE_ASSOC);
if (count($systemHostDb) == 0) {
    //any database defined yet => get only the System <-> Host relation
    $systemHostDb = $dbUtil->dbQuery($dbh, 
                                      $dbUtil->getSystemHost(),
                                      MDB2_FETCHMODE_ASSOC);
}
$jobsLabel = $dbUtil->dbQuery($dbh, 
                               $dbUtil->getJobsLabel(),
                               MDB2_FETCHMODE_ASSOC);
$jobType   = $dbUtil->dbQuery($dbh, 
                               $dbUtil->getjobTypeList(),
                               MDB2_FETCHMODE_ASSOC);
$dbh       = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));
// The toolbar javascript is used below $toolbar['javascript'];

/* SYSTEM HOST DB FIELDS */                 
$select1[0]       = FIELD_SELECT_SYSTEM;
$select2[0][0]    = FIELD_SELECT_HOST;
$select3[0][0][0] = FIELD_SELECT_DB;
$id_host          = '';

foreach ($systemHostDb as $key) {
    if (array_key_exists('ADMIN', $_policy) ||
                         in_array($key['id_system'],
                         explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select1[$key['id_system']] = $key['name'];
        if ($id_host == '' || $id_host != $key['name']) {
            $select2[$key['id_system']][0] = FIELD_SELECT_HOST;
            $id_host                       = $key['id_host'];
        }
        $select2[$key['id_system']][$key['id_host']] = $key['hostname'] . ' (' .
                                                       $key['ip'] . ')';
        $select3[$key['id_system']][0][0]            = FIELD_SELECT_DB;
        if (isset($key["id_db"])) {
        	$select3[$key['id_system']][$key['id_host']][$key['id_db']] = 
                     $key['db_label'];    
        } else {
            $select3[$key['id_system']][$key['id_host']][1] = FIELD_SELECT_NONE;
        }
    }
}



$sel = & $form->addElement('hierselect', 'systemHostDb', '');
$sel->setOptions(array($select1, $select2, $select3));

$form->registerRule('checkHier', 'callback', 'checkHier');
//used only for apply rules
$f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML();
$form->addRule('rule', MSG_SELECT_A_SYSTEM, 'checkHier', 'systemHostDb[0]',
                       'client');
$form->addRule('rule', MSG_SELECT_A_HOST, 'checkHier', 'systemHostDb[1]',
                       'client');

$f_systemHostDb = $sel->toHTML();

        
/* JOB FIELDS */
        
$f_label        = $form->addElement('text', 'label',
                                    '', 'id=label size=60')->toHTML();
$f_description  = $form->addElement('textarea', 'description', '', 
                                   'id=description rows=5 cols=50')->toHTML();
$f_priority_obj = $form->addElement('text', 'priority',
                                    '', 'id=priority  size=10');
$f_priority_obj->setValue('1');
$f_priority = $f_priority_obj->toHTML();

$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');
$form->addRule('priority', MSG_PRIORITY_REQUIRED, 'required', '', 'client');
$form->registerRule('rangeValue', 'callback', 'rangeValue');
$form->addRule('priority', 
               MSG_PRIORITY_HAS_VALUE, 
               'rangeValue', 
               '1-100', 
               'client');    
$form->addRule('priority', MSG_PRIORITY_HAS_VALUE, 'regex', '/^[1-9]/', 'client');

$f_properties_max_check_attempt_obj = 
    $form->addElement('text', 
                      'properties_max_check_attempts',
                      '',
                      'id=properties_max_check_attempts size=10');
$f_properties_max_check_attempt_obj->setValue('0');
$f_properties_max_check_attempt = $f_properties_max_check_attempt_obj->toHtml();
$form->addRule('properties_max_check_attempts', 
               MSG_MAX_CHECK_ATTEMPT_IS_NUMERIC, 
               'numeric', 
               '', 
               'client');
$form->addRule('properties_max_check_attempts', 
               MSG_MAX_CHECK_ATTEMPT_REQUIRED, 
               'required', 
               '', 
               'client');

$f_properties_delay_retry_obj = 
                $form->addElement('text', 'properties_delay_retry', '',
                                  'id=properties_delay_retry size=10');
$f_properties_delay_retry_obj->setValue('0');
$f_properties_delay_retry = $f_properties_delay_retry_obj->toHtml();
$form->addRule('properties_delay_retry', MSG_MAX_DELAY_IS_NUMERIC, 'numeric',
                                         '', 'client');
$form->addRule('properties_delay_retry', MSG_MAX_DELAY_REQUIRED, 'required',
                                         '', 'client');

$table = new HTML_Table($table_attributes . ' id=table_job_0');
$table->addRow(array(FIELD_JOB_PROPERTIES), '', 'TH');
$table->addRow(array(FIELD_LABEL .'<br/>' . $f_label . 
                  '<br/>' . FIELD_JOB_DESCRIPTION . '<br/>' . $f_description . 
                  '<br/>' . FIELD_JOB_PRIORITY . '<br/>' . $f_priority .
                  '<br/>' . FIELD_JOB_MAX_ATTEMPT . '<br/>' .
                  $f_properties_max_check_attempt .
                  '<br/>' . FIELD_JOB_DELAY . '<br/>' . $f_properties_delay_retry
                  ), "align=left valign=top", 'TD', false);
$table_jobs = $table->toHTML();
        
/* CRONTAB FIELDS */
        
$crontab_array = createCrontabHtml($form);
        
        // --- Create the plugin type fields ---        
        $tables_plugin_type       = '';
        $plugin_prefix            = 'plugin';
        $select_pluginType1[0]    = FIELD_SELECT_CATEGORY;
        $select_pluginType2[0][0] = FIELD_SELECT_JOB;
        $max_id_job_type          = 0;
foreach ($jobType as $key) {
    $cfg_file_path = ORTRO_PLUGINS . $key['category'] . DS . $key['label'] . DS;
    $cfg_file      = $cfg_file_path . 'configure.php';
    if (is_file($cfg_file)) {
        // Include the plugin language definition
        i18n($key['category'], $key['label']);
        include_once $cfg_file;

        $plugin_key = $plugin_field[$key['label']];        
        //create the select list category->plugin
        $select_pluginType2[$key['category']][0] = FIELD_SELECT_JOB;
        $select_pluginType1[$key['category']]    = $key['category'];
        $select_pluginType2[$key['category']][$key['id_job_type']]
                                         = $plugin_key[0]['title'];
        
        $table = new HTML_Table($hidden_table_attributes . ' id=' .
                                $plugin_prefix . '_' . 
                                $key['id_job_type']);
        $table->addRow(array($plugin_key[0]['description']),
                       'colspan=5', 'TD', false);
        for ($i = 1; $i < sizeof($plugin_key); $i++) {
                    //It's a form field... continue.....
            if (isset($_REQUEST[$plugin_key[$i]['name']])) {
                $temp_form = createDynamicForm($form, $plugin_key[$i], 
                                                 $_REQUEST[$plugin_key[$i]['name']],
                                                 'get_metadata_conf_value',
                                                 $cfg_file_path);
            } else {
                $temp_form = createDynamicForm($form, 
                                                 $plugin_key[$i], 
                                                 'get_metadata_conf_value', 
                                                 $cfg_file_path);
            }
            $table->addRow(array($temp_form['html']), 'colspan=5', 'TD', false);
        }
        $tables_plugin_type .= $table->toHTML();
    }
    if ($key['id_job_type'] > $max_id_job_type) {
        $max_id_job_type = $key['id_job_type'];
    }
}
$f_plugin_type =& $form->addElement('hierselect',
                                    'job_type',
                                    '',
                                    'onchange="showFormFields(this.value, \'' .
                                    $plugin_prefix . '_\', ' . 
                                    $max_id_job_type . ');"');
$f_plugin_type->setOptions(array($select_pluginType1, $select_pluginType2));
$f_hidden_rule_field .= $form->addElement('hidden', 'rule_job', 'rule')->toHTML();
$form->addRule('rule_job', MSG_SELECT_A_JOB_TYPE, 
               'checkHier', 'job_type[1]', 'client');
        
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

/* HIDDEN FIELDS */
//Input field needed for action type
$f_hidden  = $form->createElement('hidden', 'action',
             $_REQUEST['mode'])->toHTML(); 
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->addElement('hidden', 'identity', '')->toHTML();
$f_hidden .= $f_hidden_rule_field;
//convert form in array for extact js and attributes
$formArray = $form->toArray(); 
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo JOB_ADD_TOP; ?>
</div>
<p>
<?php echo JOB_ADD_TITLE; ?>
</p>
<form <?php echo $formArray['attributes'];?> >
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
$table->addRow(array(FIELD_SYSTEM_HOST_DB), '', 'TH');
$table->addRow(array($f_systemHostDb), '', 'TD', false);
$table->display();

echo $table_jobs;

echo $crontab_array['html'];
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_JOB_TYPE), "colspan=5", 'TH');
$table->addRow(array($f_plugin_type->toHTML()), "colspan=5", 'TD', false);
$table->display();
echo $tables_plugin_type;

$table = new HTML_Table($table_attributes);
$table->addRow(array($f_submit), "colspan=5", 'TD', false);
$table->display(); 
?>
</div>
</form>