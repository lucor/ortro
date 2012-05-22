<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a identity in Ortro
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

$dbUtil  = new DbUtil();
$dbh     = $dbUtil->dbOpenConnOrtro();
$systems = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$dbh     = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));
// The toolbar javascript is used below $toolbar['javascript'];

//Create the select list for System
$select_system['0'] = '---';
foreach ($systems as $key) {
    if (array_key_exists('ADMIN', $_policy) ||  
        in_array($key['id_system'], 
        explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select_system[$key["id_system"]] = $key["name"];
    }
    $select_system_to_share[$key["id_system"]] = $key["name"];
}
        
$f_select_system = $form->addElement('select', 'id_system', '', $select_system, '')->toHTML();
$form->addRule('id_system', MSG_SELECT_A_SYSTEM, 'nonzero', null, 'client');
        
$f_label = $form->addElement('text', 'label', '', 'size="50"')->toHTML();
$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');

$f_calendar = $form->addElement('hidden', 'calendar', '', 'id="calendar"')->toHTML();
$form->addRule('calendar', MSG_CALENDAR_REQUIRED, 'required', '', 'client');


$f_share_with = $form->addElement('select', 
                                  'id_shared_systems', 
                                  '', 
                                  $select_system_to_share, 
                                  'multiple')->toHTML();
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

/* HIDDEN FIELDS */
//Input field needed for action type
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo CALENDAR_ADD_TOP; ?>
</div>

<p>
<?php echo CALENDAR_ADD_TITLE; ?>
</p>

<?php
    //convert form in array for extact js and attributes 
    $formArray = $form->toArray();
    echo $formArray['javascript'];
?>

<form  <?php echo $formArray['attributes'];?> >
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
 $table->addRow(array(FIELD_SYSTEM), '', 'TH');
 $table->addRow(array($f_select_system), "", 'TD', false);
 $table->display();
 $table = new HTML_Table($table_attributes . ' class=c2');
 $table->addRow(array(FIELD_LABEL), '', 'TH');
 $table->addRow(array($f_label), "", 'TD', false);
 $table->addRow(array(CALENDAR_LABEL), '', 'TH');
 $table->addRow(array ('<div id="datepicker"></div>'.$f_calendar),
                "align=left valign=top class=c2", 'TD', false);
 
 $table->display();
if (count($select_system_to_share) > 1) {
     $table = new HTML_Table($table_attributes . ' class=c2');
     $table->addRow(array(FIELD_SHARE), '', 'TH');
     $table->addRow(array (CALENDAR_SHARE_WITH.'<br/>' .
                   $f_share_with), "align=left valign=top class=c2", 'TD', false);
    $table->display();    
}
 $table = new HTML_Table($table_attributes . ' class=c2');
 $table->addRow(array($f_submit), 'align=left colspan=2', 'TD', false);
 $table->display();
?>
</div>
</form>