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
        
$f_select_system = $form->addElement('select', 'id_system', '', 
                                     $select_system, '')->toHTML();
$form->addRule('id_system', MSG_SELECT_A_SYSTEM, 'nonzero', null, 'client');
        
$f_label = $form->addElement('text', 'label', '', '')->toHTML();
$form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');
$f_username = $form->addElement('text', 'username', '', '')->toHTML();
$form->addRule('username', MSG_USERNAME_REQUIRED, 'required', '', 'client');
$f_password         = $form->addElement('password', 'password', '', '')->toHTML();
$f_password_confirm = $form->addElement('password', 
                                        'password_confirm', 
                                        '', 
                                        '')->toHTML();
$form->addRule(array('password','password_confirm'), 
               MSG_PASSWORD_NOT_MATCH, 'compare', null, 'client');
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
<?php echo ID_ADMIN_ADD_TOP; ?>
</div>

<p>
<?php echo ID_ADMIN_ADD_TITLE; ?>
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
 $table->addRow(array(FIELD_IDENTITY), '', 'TH');
 $table->addRow(array ($f_label . '&nbsp;' . 
                FIELD_LABEL_IDENTITY), 
                "align=left valign=top class=c2", 'TD', false);
 $table->addRow(array ($f_username . '&nbsp;' . 
                       FIELD_USERNAME), 
                       "align=left valign=top class=c2", 'TD', false);
 $table->addRow(array ($f_password . '&nbsp;' . 
                       FIELD_PASSWORD), "align=left valign=top class=c2", 
                       'TD', false);
 $table->addRow(array ($f_password_confirm . '&nbsp;' . 
                       FIELD_CONFIRM_PASSWORD), 
                       "align=left valign=top class=c2", 'TD', false);
 $table->display();
if (count($select_system_to_share) > 1) {
     $table = new HTML_Table($table_attributes . ' class=c2');
     $table->addRow(array(FIELD_SHARE), '', 'TH');
     $table->addRow(array (ID_ADMIN_SHARE_WITH.'<br/>' .
                   $f_share_with), "align=left valign=top class=c2", 'TD', false);
    $table->display();    
}
 $table = new HTML_Table($table_attributes . ' class=c2');
 $table->addRow(array($f_submit), 'align=left colspan=2', 'TD', false);
 $table->display();
?>
</div>
</form>