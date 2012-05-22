<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the calendar settings
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

if (isset($_REQUEST['id_calendar'])) {
    //an error occourred on host setting modify
    $id_calendar = $_REQUEST['id_calendar'];
} else {
    $id_calendar = key($_REQUEST['id_chk']);
    $label       = $_REQUEST['id_chk'][$id_calendar];
}

$dbUtil        = new DbUtil();
$dbh           = $dbUtil->dbOpenConnOrtro();
$calendar_info = $dbUtil->dbQuery($dbh,
                                   $dbUtil->getCalendarById($id_calendar),
                                   MDB2_FETCHMODE_ASSOC);
$systems       = $dbUtil->dbQuery($dbh, 
                                   $dbUtil->getSystems(), 
                                   MDB2_FETCHMODE_ASSOC);
$dbh           = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$select_system_to_share = array();
foreach ($systems as $key) {
    if (array_key_exists('ADMIN', $_policy) ||  
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select_system[$key["id_system"]] = $key["name"];
    }
    if ($key["id_system"] != $calendar_info[0]['system']) {
        $select_system_to_share[$key["id_system"]] = $key["name"];
    }
}

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_label = $form->addElement('text', 'label', '', 'size="50"');
$f_label->setValue($calendar_info[0]['label']);
$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');

$f_calendar = $form->addElement('hidden', 'calendar', '', 'id="calendar"');
$f_calendar->setValue($calendar_info[0]['calendar']);
$form->addRule('calendar', MSG_CALENDAR_REQUIRED, 'required', '', 'client');

$f_share_with = $form->addElement('select', 
                                  'id_shared_systems', 
                                  '', 
                                  $select_system_to_share, 
                                  'multiple');
$share_with   = explode('#', $calendar_info[0]['share_with']);
array_pop($share_with);
array_shift($share_with);
$f_share_with->setValue($share_with);

/* SUBMIT BUTTON */
$f_submit       = $form->addElement('submit', 
                                    'update', 
                                    BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_calendar', $id_calendar)->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo ID_ADMIN_EDIT_TOP; ?>
</div>
<p>
<?php echo ID_ADMIN_EDIT_TITLE; ?>
</p>

<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
<?php 
echo $f_hidden; //hidden field
 
$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM), '', 'TH');
$table->addRow(array($select_system[$calendar_info[0]['system']]), '', 'TD', false);
$table->display(); 
$table = new HTML_Table($table_attributes . ' class=c2');
$table->addRow(array(FIELD_LABEL), '', 'TH');
$table->addRow(array($f_label->toHTML()),
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array(CALENDAR_LABEL), '', 'TH');
$table->addRow(array('<div id="datepicker"></div>' . $f_calendar->toHTML()),
               'align=left valign=top class=c2', 'TD', false);

if (count($select_system) > 1) {
    $table->addRow(array(FIELD_SHARE), '', 'TH');
    $table->addRow(array (ID_ADMIN_SHARE_WITH.'<br/>' . $f_share_with->toHTML()),
                   'align=left valign=top class=c2', 'TD', false);
}

$table->addRow(array($f_submit), 'colspan=2', 'TD', false);
$table->display();

?>
</div>
</form>