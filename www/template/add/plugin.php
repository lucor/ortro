<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a plugin in Ortro
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
$file =& $form->addElement('file', 'filename', '', 'size=60');
$form->addRule('filename', MSG_SELECT_A_FILE, 'required', '', 'client');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_INSTALL)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
   <?php echo PLUGIN_ADD_TOP; ?>
</div>    
<p>
   <?php echo PLUGIN_ADD_TITLE; ?>
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
$table->addRow(array(PLUGIN_ADD_ARCHIVE), '', 'TH');
$table->addRow(array($file->toHtml() . '<br/><br/>' . $f_submit), '', 'TD', false);
$table->display();

?>
</div>
</form>