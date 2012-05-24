<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the database settings
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
 
if (isset($_REQUEST['id_db'])) {
    //an error occourred on host setting modify
    $id_system = $_REQUEST['id_system'];
    $id_db     = $_REQUEST['id_db'];
} else {
    $id_db     = key($_REQUEST['id_chk']);
    $id_system = $_REQUEST['id_chk'][$id_db];    
}

$dbUtil   = new DbUtil();
$dbh      = $dbUtil->dbOpenConnOrtro();
$db_info  = $dbUtil->dbQuery($dbh, 
                              $dbUtil->getDbById($id_db), 
                              MDB2_FETCHMODE_ASSOC);
$dbmsList = $dbUtil->dbQuery($dbh, $dbUtil->getdbmsList(), MDB2_FETCHMODE_ASSOC);
$dbh      = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

// --- Create the select list for dbms type ---
require_once 'installUtil.php';
$db_supported = checkDBDriver();
foreach ($dbmsList as $key) {
    if (isset($db_supported['db_supported'][$key["label"]])) {
        $select_dbms[$key["id_dbms_type"]] = $key["description"];    
    }
}

$f_select_dbms = $form->addElement('select', 'dbms', '', $select_dbms, '');
$f_select_dbms->setSelected($db_info[0]["id_dbms_type"]);
$f_select_dbms_html = $f_select_dbms->toHTML();

$f_db_label = $form->addElement('text', 'db_label', '', 'id=db_label');
$f_db_label->setValue($db_info[0]['label']);
$f_db_label_html = $f_db_label->toHTML();

$f_sid = $form->addElement('text', 'sid', '', 'id=sid');
$f_sid->setValue($db_info[0]['sid']);
$f_sid_html = $f_sid->toHTML();

$f_port = $form->addElement('text', 'port', '', 'id=port');
$f_port->setValue($db_info[0]['port']);
$f_port_html = $f_port->toHTML();

$form->addRule('db_label', MSG_LABEL_REQUIRED, 'required', '', 'client');
$form->addRule('sid', MSG_SID_REQUIRED, 'required', '', 'client');
$form->addRule('port', MSG_PORT_REQUIRED, 'required', '', 'client');
$form->addRule('port', MSG_PORT_IS_A_NUMBER, 'numeric', '', 'client');

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_db', $id_db)->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_system', $id_system)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
   <?php echo DATABASE_EDIT_TOP; ?>
</div>    
<p>
<?php echo DATABASE_EDIT_TITLE; ?>
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
    $table->addRow(array($f_db_label_html . '&nbsp;' . FIELD_LABEL . '<br/>' . 
                         $f_sid_html . '&nbsp;' . FIELD_DB_NAME_SID . '<br/>' . 
                         $f_port_html . '&nbsp;' . FIELD_PORT . '<br/>'. 
                         $f_select_dbms_html .'&nbsp;' . FIELD_DBMS . '<br/>'), 
                   'align=left valign=top', 'TD', false);
    $table->display();
    $table = new HTML_Table($table_attributes);
    $table->addRow(array ($f_submit), 'align=left colspan=2', 'TD', false);
    $table->display();
?>
</div>
</form>