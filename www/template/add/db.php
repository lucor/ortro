<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a database in Ortro
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
 
$dbUtil     = new DbUtil();
$dbh        = $dbUtil->dbOpenConnOrtro();
$systemHost = $dbUtil->dbQuery($dbh, 
                                $dbUtil->getSystemHost(),
                                MDB2_FETCHMODE_ASSOC);
$dbmsList   = $dbUtil->dbQuery($dbh, $dbUtil->getdbmsList(), MDB2_FETCHMODE_ASSOC);
$dbh        = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

// --- Create System/host fields ---
$select1       = array ();
$select2       = array ();
$select1[0]    = FIELD_SELECT_SYSTEM;
$select2[0][0] = FIELD_SELECT_HOST;

foreach ($systemHost as $key) {
    if (array_key_exists('ADMIN', $_policy) ||  
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
  
        $select1[$key["id_system"]]                  = $key["name"];
        $select2[$key["id_system"]][$key["id_host"]] = 
                 $key["hostname"]."(".$key["ip"].")";
    }
}

$sel = & $form->addElement('hierselect', 'systemHost', '');
$sel->setOptions(array ($select1, $select2));
//used only for apply rules
$f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML();
$form->registerRule('checkHier', 'callback', 'checkHier');
$form->addRule('rule', MSG_SELECT_A_SYSTEM, 'checkHier', 'systemHost[0]', 'client');
$f_systemHost = $sel->toHTML();

// --- Create the select list for dbms type ---
require_once 'installUtil.php';
$db_supported = checkDBDriver();
foreach ($dbmsList as $key) {
    if (isset($db_supported['db_supported'][$key["label"]])) {
        $select_dbms[$key["id_dbms_type"]] = $key["description"];    
    }
}
$f_select_dbms = $form->addElement('select', 
                                   'dbms', 
                                   '', 
                                   $select_dbms, '')->toHTML();

$f_label  = $form->addElement('text', 'db_label', '', 'id=db_label')->toHTML();
$f_sid    = $form->addElement('text', 'sid', '', 'id=sid')->toHTML();
$f_port   = $form->addElement('text', 'port', '', 'id=port')->toHTML();
$f_submit = $form->addElement('submit', 'Update', 'Add database')->toHTML();
$form->addRule('db_label', MSG_LABEL_REQUIRED, 'required', '', 'client');
$form->addRule('sid', MSG_SID_REQUIRED, 'required', '', 'client');
$form->addRule('port', MSG_PORT_REQUIRED, 'required', '', 'client');
$form->addRule('port', MSG_PORT_IS_A_NUMBER, 'numeric', '', 'client');

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

/* HIDDEN FIELDS */
//Input field needed for action type
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_db', '0')->toHTML();
$f_hidden .= $f_hidden_rule_field;

$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
   <?php echo DATABASE_ADD_TOP; ?>
</div>    
<p>
<?php echo DATABASE_ADD_TITLE; ?>
</p>

<form  <?php echo $formArray['attributes'];?> >
<div id="toolbar" class="ortro-table">
    <?php  echo $toolbar['javascript']; ?>
    <?php  echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
<?php 
    echo $f_hidden; //hidden field
    $table = new HTML_Table($table_attributes);
    $table->addRow(array (FIELD_SYSTEM_HOST), '', 'TH');
    $table->addRow(array ($f_systemHost), '', 'TD', false);
    $table->display();
    $table = new HTML_Table($table_attributes. ' id=table_db_0');
    $table->addRow(array (FIELD_DATABASE_ONLY), "", 'TH');
    $table->addRow(array ($f_label . '&nbsp;' . FIELD_LABEL . 
                          '<br/>' . $f_sid . '&nbsp;' . FIELD_DB_NAME_SID . 
                          '<br/>' . $f_port . '&nbsp;' . FIELD_PORT . 
                          '<br/>'. $f_select_dbms .'&nbsp;' . FIELD_DBMS . 
                          '<br/>'), "align=left valign=top", 'TD', false);
    $table->display();
    $table = new HTML_Table($table_attributes);
    $table->addRow(array ($f_submit), 'align=left colspan=2', 'TD', false);
    $table->display();
    echo '</div>';
?>
</form>