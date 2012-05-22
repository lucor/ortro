<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the system settings
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
    //an error occourred on system name modify
    $id_system   = $_REQUEST['id_system'];
    $system_name = $_REQUEST['system_name'];
} else {
    $id_system   = key($_REQUEST['id_chk']);
    $system_name = $_REQUEST['id_chk'][$id_system];    
}

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_systemName_obj = $form->addElement('text', 'system_name', '', 'id=systemname');
$f_systemName_obj->setValue($system_name);
$f_systemName = $f_systemName_obj->toHTML();

$form->addRule('system_name', MSG_SYSTEM_NAME_REQUIRED, 'required', '', 'client');
        
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = 
    $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'id_system', $id_system)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
   <?php echo SYSTEM_EDIT_TOP; ?>
</div>    
<p>
<?php echo SYSTEM_EDIT_TITLE; ?>
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
$table->addRow(array(SYSTEM_NAME), '', 'TH');
$table->addRow(array($f_systemName . '&nbsp;' . $f_submit), '', 'TD', false);
$table->display();
 
?>
</div>
</form>