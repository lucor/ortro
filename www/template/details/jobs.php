<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page that shows the job details
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

$id_job    = key($_REQUEST['id_chk']);
$id_system = $_REQUEST['id_chk'][$id_job];

$dbUtil         = new DbUtil();
$dbh            = $dbUtil->dbOpenConnOrtro();
$job_info       = $dbUtil->dbQuery($dbh, 
                                    $dbUtil->getJobInfoById($id_job), 
                                    MDB2_FETCHMODE_ASSOC);
$job_shd_labels = $dbUtil->dbQuery($dbh, 
                                    $dbUtil->getSystemHostDbInfoById($id_job), 
                                    MDB2_FETCHMODE_ASSOC);
$dbh            = $dbUtil->dbCloseConn($dbh);
unset($dbh);

/* JOB FIELDS */
$table = new HTML_Table($table_attributes . ' id=table_job_0');
$table->addRow(array(FIELD_JOB_PROPERTIES), 'colspan=2', 'TH');
$table->addRow(array(FIELD_LABEL,FIELD_JOB_PRIORITY_DETAILS), '', 'TH');
$table->addRow(array($job_info[0]["label"],$job_info[0]["priority"]),
               'align=left valign=top', 'TD', false);
$table->addRow(array(FIELD_JOB_DESCRIPTION), 'colspan=2', 'TH');
$table->addRow(array(rawurldecode($job_info[0]["description"])),
               'align=left valign=top', 'TD', false);
$properties = $dbUtil->dbUnserialize($job_info[0]["properties"]);
$table->addRow(array(FIELD_JOB_MAX_ATTEMPT,FIELD_JOB_DELAY), '', 'TH');
$table->addRow(array($properties['max_check_attempts'],
                     $properties['delay_retry']),
               'align=left valign=top', 'TD', false);
$table_jobs = $table->toHTML();

/* CRONTAB FIELDS */
$crontab = getCrontabValues($job_info[0]);

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_JOB_SCHEDULE), 'colspan=5', 'TH');
$table->addRow(array($crontab['html']['schedule_type']),
               'colspan=5 align=left valign=top', 'TD', false);
$table->addRow(array(FIELD_JOB_SCHEDULE_MINUTE,
                     FIELD_JOB_SCHEDULE_HOUR,
                     FIELD_JOB_SCHEDULE_DAY_OF_MONTH,
                     FIELD_JOB_SCHEDULE_MONTH_OF_YEAR,
                     FIELD_JOB_SCHEDULE_DAY_OF_WEEK), '', 'TH');
$table->addRow(array($crontab['html']['m'],
                     $crontab['html']['h'],
                     $crontab['html']['dom'],
                     $crontab['html']['mon'],
                     $crontab['html']['dow']),
               'align=left valign=top', 'TD', false);
$table_crontab = $table->toHTML();

/* PLUGIN FIELDS */
$tables_plugin_type   = '';
$plugin_prefix        = 'plugin';
$plugin_field_values  = $dbUtil->dbUnserialize($job_info[0]["parameters"]);
$plugin_type          = $job_info[0]["job_type_label"];
$plugin_type_category = $job_info[0]["job_type_category"];

$cfg_file = ORTRO_PLUGINS . $plugin_type_category . DS . 
            $plugin_type . DS . 'configure.php';

if (is_file($cfg_file)) {    
    // Include the plugin language definition
    i18n($plugin_type_category, $plugin_type);
    include_once $cfg_file;
    $table        = new HTML_Table($table_attributes);
    $plugin_key   = $plugin_field[$plugin_type];
    $plugin_title = $plugin_key[0]['title'];
    $table->addRow(array(FIELD_JOB_DESCRIPTION), '', 'TH');
    $table->addRow(array($plugin_key[0]['description']), '', 'TD', false);

    for ($i = 1; $i < sizeof($plugin_key); $i++) {
        if ($plugin_key[$i]['type'] != 'submit') {
            if ($plugin_key[$i]['type'] == 'password') {
                $plugin_field_value = '******';
            } elseif ($plugin_key[$i]['type'] == 'select' &&
                      $plugin_key[$i]['attributes'] == 'multiple') {
                foreach ($plugin_field_values['service_check_ports'] as $key => $value) {
                    if (isset($value)) {
                        $plugin_field_value .= $value . "\n";
                    }
                }
            } else {
                $plugin_field_value = $plugin_field_values[$plugin_key[$i]['name']];
            }
            $table->addRow(array($plugin_key[$i]['description']), '', 'TH');
            $table->addRow(array(str_replace("\n",
                                             '<br/>',
                                             htmlentities($plugin_field_value))), 
                                             '', 'TD', false);
        }
    }
    $tables_plugin_type .= $table->toHTML();
}

//Load plugin actions
$plugin_action_html = '';
$toolbar_actions    = '';
if (isset($plugin_actions[$plugin_type])) {
    $formPlugin      = new HTML_QuickForm('frmActionPlugin', 'post');
    $formPluginArray = $formPlugin->toArray();
    
    for ($i = 0; $i < sizeof($plugin_actions[$plugin_type]); $i++) {
        $plugin_action_html = 
            $formPlugin->createElement('hidden', 'cat', 'plugins')->toHTML() .
            $formPlugin->createElement('hidden', 
                                       'file', 
                               $plugin_actions[$plugin_type][$i]['file'])->toHTML() .
            $formPlugin->createElement('hidden', 'id_system', $id_system)->toHTML() .
            $formPlugin->createElement('hidden', 'id_job', $id_job)->toHTML() .
            $formPlugin->createElement('hidden', 
                                       'plugin_type', 
                                       $plugin_type)->toHTML() .
            $formPlugin->createElement('hidden', 
                                       'plugin_type_category', 
                                       $plugin_type_category)->toHTML() .
            $formPlugin->createElement('hidden', 'mode', 'detail')->toHTML();

            $toolbar_actions .= createHref('javascript:void(0);',
                                $plugin_actions[$plugin_type][$i]['description'],
                                '<img src="img/' . 
                                $plugin_actions[$plugin_type][$i]['image']. 
                                '" border="0"/>',
                                'onclick="document.forms.frmActionPlugin.submit();'. 
                                'return false;" class="toolbar"');
    }
}
/* ACTION TOOLBAR */
if ($toolbar_actions != '') {
    $toolbar = createToolbar(array('backPage'=>'default',
                                   'custom'=>$toolbar_actions));
} else {
    $toolbar = createToolbar(array('backPage'=>'default'));
}
     
?>
<!-- start body -->
<div id="ortro-title">
<?php echo JOB_DETAILS_TOP; ?>
</div>
<?php 
if (isset($formPluginArray['attributes'])) { 
    ?>
    <form  <?php echo $formPluginArray['attributes']; ?> >
    <?php 
} 
?>
<div class="ortro-table">
    <?php 
    echo $plugin_action_html;
    echo $toolbar['header']; 
    ?>
</div>
<?php if (isset($formPluginArray['attributes'])) { ?>
    </form>
    <?php 
} ?>
<br/>
<div class="ortro-table">
<?php
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM,
                     FIELD_HOSTNAME,
                     FIELD_DATABASE_ONLY), '', 'TH');
$table->addRow(array($job_shd_labels[0]['name'],
                     $job_shd_labels[0]['hostname'],
                     $job_shd_labels[0]['label']), '', 'TD', false);
$table->display();
?>
</div>
<br/>
<div class="ortro-table">
<?php
echo $table_jobs;
?>
</div>
<br/>
<div class="ortro-table">
<?php
echo $table_crontab;
?>
</div>
<br/>
<div class="ortro-table">
<?php
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_JOB_TYPE), "colspan=5", 'TH');
$table->addRow(array($plugin_title), "colspan=5", 'TD', false);
$table->display();
echo $tables_plugin_type;
?>
</div>
