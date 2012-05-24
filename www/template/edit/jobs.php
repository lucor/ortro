<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the job settings
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
 
if (isset($_REQUEST['id_system'])) {
    //an error occourred on host setting modify    
    $id_job    = $_REQUEST['id_job'];
    $id_system = $_REQUEST['id_system'];
} else {
    $id_job    = key($_REQUEST['id_chk']);
    $id_system = $_REQUEST['id_chk'][$id_job];    
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$systemHostDb = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getSystemHostDb(),
                                  MDB2_FETCHMODE_ASSOC);
if (count($systemHostDb) == 0) {
    //any database defined yet => get only the System <-> Host relation
    $systemHostDb = $dbUtil->dbQuery($dbh, 
                                      $dbUtil->getSystemHost(), 
                                      MDB2_FETCHMODE_ASSOC);
}

$job_info = $dbUtil->dbQuery($dbh, 
                              $dbUtil->getJobInfoById($id_job), 
                              MDB2_FETCHMODE_ASSOC);
$dbh      = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

/* SYSTEM HOST DB FIELDS */                 
$id_host          = '';
$select1[0]       = FIELD_SELECT_SYSTEM;
$select2[0][0]    = FIELD_SELECT_HOST;
$select3[0][0][0] = FIELD_SELECT_DB;

foreach ($systemHostDb as $key) {
    $select1[$key['id_system']] = $key['name'];
    if ($id_host == '' || $id_host != $key['name']) {
        $select2[$key['id_system']][0] = FIELD_SELECT_HOST;
        $id_host                       = $key['id_host'];
    }
    $select2[$key['id_system']][$key['id_host']] = $key['hostname'] . 
                                                   "(" . $key['ip'] . ")";
    $select3[$key['id_system']][0][0]            = FIELD_SELECT_DB;

    //Get the ip address required for dynamic plugin data
    if ($key['id_host'] == $job_info[0]['id_host']) {
        $ip = $key['ip'];
    }
    if (isset($key['id_db'])) {
        $select3[$key['id_system']][$key['id_host']][$key['id_db']] = 
            $key['db_label'];    
    } else {
        $select3[$key['id_system']][$key['id_host']][1] = FIELD_SELECT_NONE;
    }
}

$sel = & $form->addElement('hierselect', 'systemHostDb', '');
$sel->setOptions(array($select1, $select2, $select3));
$form->setDefaults(array('systemHostDb'=>array($job_info[0]['id_system'],
                                               $job_info[0]['id_host'],
                                               $job_info[0]['id_db'])));

$form->registerRule('checkHier', 'callback', 'checkHier');
$f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML();
$form->addRule('rule', MSG_SELECT_A_SYSTEM, 'checkHier',
               'systemHostDb[0]', 'client');
$form->addRule('rule', MSG_SELECT_A_HOST, 'checkHier',
               'systemHostDb[1]', 'client');
$f_systemHostDb = $sel->toHTML();

/* JOB FIELDS */
$f_label = $form->addElement('text', 'label', '', 'id=label size=60');
$f_label->setValue($job_info[0]['label']);
$f_label_html = $f_label->toHTML();

$f_description = $form->addElement('textarea', 'description', '', 
                                   'id=description rows=5 cols=50');
$f_description->setValue(rawurldecode($job_info[0]['description']));
$f_description_html = $f_description->toHTML();

$f_priority = $form->addElement('text', 'priority', '', 'id=priority size=29');
$f_priority->setValue($job_info[0]['priority']);
$f_priority_html = $f_priority->toHTML();

$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');
$form->addRule('priority', MSG_PRIORITY_REQUIRED, 'required', '', 'client');
$form->registerRule('rangeValue', 'callback', 'rangeValue');
$form->addRule('priority', MSG_PRIORITY_HAS_VALUE, 'rangeValue', '1-100', 'client');
$form->addRule('priority', MSG_PRIORITY_HAS_VALUE, 'regex', '/^[1-9]/', 'client');

$properties = $dbUtil->dbUnserialize($job_info[0]["properties"]);

$f_properties_max_check_attempt_obj = 
    $form->addElement('text', 'properties_max_check_attempts', '', 
                      'id=properties_max_check_attempts size=10');
$f_properties_max_check_attempt_obj->setValue($properties['max_check_attempts']);
$f_properties_max_check_attempt = $f_properties_max_check_attempt_obj->toHtml();
$form->addRule('properties_max_check_attempts', MSG_MAX_CHECK_ATTEMPT_IS_NUMERIC, 
               'numeric', '', 'client');
$form->addRule('properties_max_check_attempts', MSG_MAX_CHECK_ATTEMPT_REQUIRED, 
               'required', '', 'client');
$f_properties_delay_retry_obj = 
    $form->addElement('text', 'properties_delay_retry', '', 
                      'id=properties_delay_retry size=10');
$f_properties_delay_retry_obj->setValue($properties['delay_retry']);
$f_properties_delay_retry = $f_properties_delay_retry_obj->toHtml();
$form->addRule('properties_delay_retry', MSG_MAX_DELAY_IS_NUMERIC, 
               'numeric', '', 'client');
$form->addRule('properties_delay_retry', MSG_MAX_DELAY_REQUIRED, 
               'required', '', 'client');

$table = new HTML_Table($table_attributes . ' id=table_job_0');
$table->addRow(array(FIELD_LABEL .'<br/>' . $f_label_html . 
                  '<br/>' . FIELD_JOB_DESCRIPTION . '<br/>' . 
                  $f_description_html . 
                  '<br/>' . FIELD_JOB_PRIORITY . '<br/>' . 
                  $f_priority_html .
                  '<br/>' . FIELD_JOB_MAX_ATTEMPT . '<br/>' . 
                  $f_properties_max_check_attempt .
                  '<br/>' . FIELD_JOB_DELAY . '<br/>' . 
                  $f_properties_delay_retry
                  ), 'align=left valign=top', 'TD', false);
$table_jobs = $table->toHTML();    

/* CRONTAB FIELDS */
$crontab_array = createCrontabHtml($form, $job_info[0]);

/* PLUGIN FIELDS */
$tables_plugin_type = '';

$plugin_prefix = 'plugin';

$plugin_field_values = $dbUtil->dbUnserialize($job_info[0]["parameters"]);
$cfg_file_path       = ORTRO_PLUGINS . $job_info[0]["job_type_category"] . DS . 
                       $job_info[0]["job_type_label"] . DS;
$cfg_file            = $cfg_file_path . 'configure.php';
if (is_file($cfg_file)) {
    // Include the plugin language definition
    i18n($job_info[0]["job_type_category"], $job_info[0]["job_type_label"]);
    include_once $cfg_file;
    $table = new HTML_Table($table_attributes . ' id=' . $plugin_prefix . '_1');
    
    $plugin_key   = $plugin_field[$job_info[0]["job_type_label"]];
    $plugin_title = $plugin_key[0]['title'];
    $table->addRow(array($plugin_key[0]['description']), 'colspan=5', 'TD', false);
    for ($i = 1; $i < sizeof($plugin_key); $i++) {        
        //It's a form field... continue.....
        $plugin_key_name = $plugin_key[$i]['name'];
        if (isset($_REQUEST[$plugin_key_name])) {
            $temp_form = createDynamicForm($form, 
                                             $plugin_key[$i],
                                             $_REQUEST[$plugin_key_name],
                                             $cfg_file_path);
        } else {
            $temp_form = createDynamicForm($form,
                                             $plugin_key[$i],
                                             $plugin_field_values[$plugin_key_name],
                                             $cfg_file_path);
        }
        $table->addRow(array($temp_form['html']), 'colspan=5', 'TD', false);
    }
    $tables_plugin_type .= $table->toHTML();
}

$f_plugin_type  = $job_info[0]["job_type_label"];
$f_plugin_title = $job_info[0]["job_type_label"];
        
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_job', $id_job)->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'job_type_label', 
                                  $job_info[0]['job_type_label'])->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'id_job_type',
                                  $job_info[0]['id_job_type'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_system', $id_system)->toHTML();    
$f_hidden .= $form->createElement('hidden', 
                                  'identity', 
                                  $job_info[0]['identity'])->toHTML();
$f_hidden .= $f_hidden_rule_field;

$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo JOB_EDIT_TOP; ?>
</div>
<p>
<?php echo JOB_EDIT_TITLE; ?>
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
echo $f_hidden;

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM_HOST_DB), '', 'TH');
$table->addRow(array($f_systemHostDb), '', 'TD', false);
$table->addRow(array(FIELD_JOB_PROPERTIES), '', 'TH');
$table->display();
 
echo $table_jobs;
echo $crontab_array['html'];

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_JOB_TYPE), 'colspan=5', 'TH');
$table->addRow(array($plugin_title), 'colspan=5', 'TD', false);
$table->display();

echo $tables_plugin_type;

$table = new HTML_Table($table_attributes);
$table->addRow(array($f_submit), 'colspan=5', 'TD', false);
$table->display();

?>
</div>
</form>