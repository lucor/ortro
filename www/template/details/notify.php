<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page that shows the notification details
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

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$id_notify   = key($_REQUEST['id_chk']);
$id_system   = $_REQUEST['id_chk'][$id_notify];
$notify_type = $_REQUEST['notify_label'];
$system_name = $_REQUEST['system_name'];

$dbUtil        = new DbUtil();
$dbh           = $dbUtil->dbOpenConnOrtro();
$notifyJobInfo = $dbUtil->dbQuery($dbh,
                                  $dbUtil->getNotifyInfoById($id_notify),
                                  MDB2_FETCHMODE_ASSOC);
$dbh           = $dbUtil->dbCloseConn($dbh);
unset($dbh);

// --- Create the notify type fields ---
$tables_plugin_type = '';
$plugin_prefix      = 'notify';

$plugin_field_values = $dbUtil->dbUnserialize($notifyJobInfo[0]["parameters"]);

$cfg_file = ORTRO_NOTIFICATION_PLUGINS . $notify_type . DS . 'configure.php';

if (is_file($cfg_file)) {
    // Include the plugin language definition
    i18n('notification', $notify_type);
    include_once  $cfg_file;
    $table      = new HTML_Table($table_attributes);
    $plugin_key = $plugin_field[$notify_type];
    $table->addRow(array(FIELD_NOTIFY_DESCRIPTION), '', 'TH');
    $table->addRow(array($plugin_key[0]['description']), '', 'TD', false);
    $plugin_title = $plugin_key[0]['title'];
    for ($i = 1; $i < sizeof($plugin_key); $i++) {
        $table->addRow(array($plugin_key[$i]['description']), '', 'TH');
        if ($plugin_key[$i]['type'] == 'password') {
            $table->addRow(array('******'), "", 'TD', false);
        } else {
            $plugin_field_value = str_replace("\n",
                                              '<br/>',
            htmlentities($plugin_field_values[$plugin_key[$i]['name']]));
            if ((strpos($plugin_key[$i]['attributes'], 'htmlarea') === false)) {
                // Fix html editor strange behavior
                $table->addRow(array($plugin_field_value), '', 'TD', false);
            } else {
                $table->addRow(array(html_entity_decode(str_replace('\\\\\\\'',
                                                                    "'",
                $plugin_field_value))),
                                      '', 'TD', false);
            }
        }
    }
    $tables_plugin_type .= $table->toHTML();
}

$notify_on = explode('-', $notifyJobInfo[0]['notify_on']);
array_shift($notify_on);
array_pop($notify_on);

$f_notify_on = '';

for ($i = 0; $i < sizeof($notify_on); $i++) {
    switch ($notify_on[$i]) {
    case '0':
        $f_notify_on .= FIELD_NOTIFY_JOB_ERROR;
        break;
    case '1':
        $f_notify_on .= FIELD_NOTIFY_JOB_SUCCESS;
        break;
    case '2':
        $f_notify_on .= FIELD_NOTIFY_JOB_END;
        break;
    case '3':
        $f_notify_on .= FIELD_NOTIFY_JOB_START;
        break;
    }
    if ($i < (sizeof($notify_on) - 1)) {
        $f_notify_on .= ', ';
    }
}
?>
<!-- start body -->

<div id="ortro-title"><?php echo NOTIFICATION_DETAILS_TOP; ?></div>
<div id="toolbar" class="ortro-table"><?php echo $toolbar['javascript']; ?>
<?php echo $toolbar['header']; ?></div>
<br />
<div class="ortro-table"><?php
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM), '', 'TH');
$table->addRow(array($system_name), '', 'TD', false);
$table->addRow(array(FIELD_JOB), '', 'TH');
$table->addRow(array($_REQUEST['job_label']), '', 'TD', false);
$table->display();
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_NOTIFY_SEND_ON), '', 'TH');
$table->addRow(array($f_notify_on), '', 'TD', false);
$table->display();
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_NOTIFY_METHOD), "", 'TH');
$table->addRow(array($plugin_title), "", 'TD', false);
$table->display();
echo $tables_plugin_type;
?></div>
