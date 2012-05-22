<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the identity settings
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

if (isset($_REQUEST['id_identity'])) {
    //an error occourred on host setting modify
    $id_identity = $_REQUEST['id_identity'];
} else {
    $id_identity = key($_REQUEST['id_chk']);
    $label       = $_REQUEST['id_chk'][$id_identity];    
}

$dbUtil        = new DbUtil();
$dbh           = $dbUtil->dbOpenConnOrtro();
$identity_info = $dbUtil->dbQuery($dbh, 
                                   $dbUtil->getIdentityById($id_identity),
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
    if ($key["id_system"] != $identity_info[0]['system']) {
        $select_system_to_share[$key["id_system"]] = $key["name"];
    }
}

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_label = $form->addElement('text', 'label', '', '');
$f_label->setValue($identity_info[0]['label']);
$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');

$f_username = $form->addElement('text', 'username', '', '');
$f_username->setValue($identity_info[0]['username']);
$form->addRule('username', MSG_USERNAME_REQUIRED, 'required', '', 'client');

$f_old_password = $form->addElement('password', 'old_password', '', '');
$f_old_password->setValue('');

$f_password = $form->addElement('password', 'password', '', '');
$f_password->setValue('');

$f_password_confirm = $form->addElement('password', 'password_confirm', '', '');
$f_password_confirm->setValue('');

$form->addRule(array ('password','password_confirm'), 
               MSG_PASSWORD_NOT_MATCH, 
               'compare', 
               null, 
               'client');
$f_share_with = $form->addElement('select', 
                                  'id_shared_systems', 
                                  '', 
                                  $select_system_to_share, 
                                  'multiple');
$share_with   = explode('#', $identity_info[0]['share_with']);
array_pop($share_with);
array_shift($share_with);
$f_share_with->setValue($share_with);

/* SUBMIT BUTTON */
$f_submit       = $form->addElement('submit', 
                                    'update', 
                                    BUTTON_APPLY)->toHTML();
$f_submit_share = $form->addElement('submit', 
                                    'update_share', 
                                    BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_identity', $id_identity)->toHTML();

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
$table->addRow(array($select_system[$identity_info[0]['system']]), '', 'TD', false);
$table->display(); 
$table = new HTML_Table($table_attributes . ' class=c2');
$table->addRow(array(FIELD_IDENTITY), '', 'TH');
$table->addRow(array($f_label->toHTML() . '&nbsp;'. FIELD_LABEL_IDENTITY), 
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array($f_username->toHTML() . '&nbsp;'.FIELD_USERNAME), 
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array($f_old_password->toHTML() . '&nbsp;' . FIELD_OLD_PASSWORD), 
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array($f_password->toHTML() . '&nbsp;' . FIELD_PASSWORD), 
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array($f_password_confirm->toHTML() . 
                     '&nbsp;' . 
                     FIELD_CONFIRM_PASSWORD),
               'align=left valign=top class=c2', 'TD', false);
$table->addRow(array($f_submit), 'colspan=2', 'TD', false);
$table->display();
if (count($select_system) > 1) {
    $table = new HTML_Table($table_attributes . ' class=c2');
    $table->addRow(array(FIELD_SHARE), '', 'TH');
    $table->addRow(array (ID_ADMIN_SHARE_WITH.'<br/>' . $f_share_with->toHTML()), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->display();
    $table = new HTML_Table($table_attributes . ' class=c2');
    $table->addRow(array($f_submit_share), 'colspan=2', 'TD', false);
    $table->display();
}
?>
</div>
</form>