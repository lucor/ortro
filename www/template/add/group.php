<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a group in Ortro
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
$roles   = $dbUtil->dbQuery($dbh, $dbUtil->getRoles(), MDB2_FETCHMODE_ASSOC);
$systems = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$dbh     = $dbUtil->dbCloseConn($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

$f_groupname = $form->addElement('text', 'groupname', '', '')->toHTML();
$form->addRule('groupname', MSG_GROUP_NAME_REQUIRED, 'required', '', 'client');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));
// The toolbar javascript is used below $toolbar['javascript'];

foreach ($roles as $key) {
    $select_role[$key["id_role"]] = $key["label"];
}

$f_role = $form->addElement('select', 'id_role', '', 
                            $select_role, 
                        'onchange="disableMultiSelSystems(this.value);"')->toHTML();

foreach ($systems as $key) {
    $select_system[$key["id_system"]] = $key["name"];
}

$f_system = $form->addElement('select', 'id_systems', '', $select_system, 
                              'id=id_systems_sel multiple disabled=true')->toHTML();

$form->registerRule('checkMultiSelect', 'callback', 'checkMultiSelect');
$form->addRule('id_role', MSG_SELECT_A_VALUE_FOR_SYSTEM, 
                         'checkMultiSelect', 
                         'id_systems_sel', 
                         'client');

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
?>
<!-- start body -->
<div id="ortro-title">
<?php echo GROUP_ADD_TOP; ?>
</div>    
<p>
<?php echo GROUP_ADD_TITLE; ?>
</p>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>

<?php 
    $formArray = $form->toArray();
    //convert form in array for extact js and attributes
    echo $formArray['javascript'];
?>
<form  <?php echo $formArray['attributes']; ?> >
<div class="ortro-table">
<?php  
 echo $f_hidden; //hidden field
 
 $table = new HTML_Table($table_attributes . ' class=c2');
 $table->addRow(array($f_groupname . '&nbsp;' . FIELD_GROUP_NAME), 
                'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array ($f_role . '&nbsp;' . FIELD_ROLE), 
                'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array($f_system . '&nbsp;' . FIELD_SYSTEMS), 
                'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array($f_submit), 'align=left valign=top class=c2', 'TD', false);
 $table->display();
?>
</div>
</form>