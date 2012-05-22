<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the user settings
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

i18n('template', 'configure_metadata_install.php');
require_once ORTRO_CONF . 'configure_metadata_install.php';

if (isset($_REQUEST['profile']) && $_REQUEST['profile'] == 'edit') {
    $id_user = AuthUtil::getSessionData('userid');
} else {
    if (isset($_REQUEST['id_user'])) {
        $id_user = $_REQUEST['id_user'];
    } else {
        $id_user = key($_REQUEST['id_chk']);
    }
}

$dbUtil    = new DbUtil();
$dbh       = $dbUtil->dbOpenConnOrtro();
$user_info = $dbUtil->dbQuery($dbh, 
                               $dbUtil->getUserById($id_user), 
                               MDB2_FETCHMODE_ASSOC);
$dbh       = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form   = new HTML_QuickForm('frmUser', 'post');
$form_2 = new HTML_QuickForm('frmUser2', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_user', $id_user)->toHTML();
$f_hidden .= 
    $form->addElement('hidden', 'username', $user_info[0]['username'])->toHTML();

$f_hidden_2  = 
    $form_2->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden_2 .= 
    $form_2->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden_2 .= 
    $form_2->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden_2 .= 
    $form_2->createElement('hidden', 'id_user', $id_user)->toHTML();
$f_hidden_2 .= 
    $form_2->addElement('hidden', 'username', $user_info[0]['username'])->toHTML();


if ($user_info[0]['type'] == 'ldap') {
    //hide form if ldap user
    $f_name = rawurldecode($user_info[0]['name']);
    $f_mail = $user_info[0]['mail'];
} else {
    //form elements for password change
    $f_password = 
        $form->addElement('password', 'password', '', '')->toHTML();
        
    $f_password_confirm = 
        $form->addElement('password', 'password_confirm', '', '')->toHTML();
    $form->addRule('password', MSG_PASSWORD_REQUIRED, 'required', '', 'client');
    $form->addRule('password', MSG_PASSWORD_MIN_CHAR, 'minlength', 6, 'client');
    $form->addRule(array('password', 'password_confirm'), 
                         MSG_PASSWORD_NOT_MATCH, 'compare', 
                         null, 'client');
    
    //form elements for properties change
    $f_name_obj = $form_2->addElement('text', 'name', '', '');
    $f_name_obj->setValue(rawurldecode($user_info[0]['name']));
    $f_name = $f_name_obj->toHTML();
    $form_2->addRule('name', MSG_COMPLETE_NAME_REQUIRED, 'required', '', 'client');

    $f_mail_obj = $form_2->addElement('text', 'mail', '', '');
    $f_mail_obj->setValue($user_info[0]['mail']);
    $f_mail = $f_mail_obj->toHTML();
    $form_2->addRule('mail', MSG_MAIL_REQUIRED, 'required', '', 'client');
    $form_2->addRule('mail', MSG_MAIL_NOT_VALID, 'regex', 
       '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4}$/', 'client');
}

$f_language_obj = $form_2->addElement('select', 'language', '', 
                                      $conf_metadata['env']['lang']['value']);
$f_language_obj->setValue($user_info[0]['language']);
$f_language = $f_language_obj->toHTML();

$f_submit_2 = $form_2->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

$f_submit = 
    $form->addElement('submit', 'update_password', BUTTON_CHANGE_PASSWORD)->toHTML();
//convert form in array for extact js and attributes
$formArray  = $form->toArray(); 
$formArray2 = $form_2->toArray();
echo $formArray['javascript'];
echo $formArray2['javascript'];
?>
<!-- start body -->
<div id="ortro-title">
<?php echo USER_EDIT_TOP ?>
</div>
<p>
      <?php echo USER_EDIT_TITLE; ?>
</p>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<form  <?php echo $formArray2['attributes']; ?> >
<div class="ortro-table">
<?php 
echo $f_hidden_2; //hidden field

$table = new HTML_Table($table_attributes . ' class=c2');

$table->addRow(array(FIELD_USER . ':&nbsp;<b>' . 
                     $user_info[0]['username'] . '</b>'), 
               'align=left valign=top class=c2', 'TD', false);
if ($user_info[0]['type'] == 'db') {    
    $table->addRow(array($f_name . '&nbsp;' . FIELD_USER_COMPLETE_NAME), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_mail . '&nbsp;' . FIELD_USER_MAIL), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_language . '&nbsp;' . FIELD_USER_LANG), 
                   'align=left valign=top class=c2', 'TD', false);
} else {
    $table->addRow(array (FIELD_USER_COMPLETE_NAME . ': <b>' . $f_name . '</b>'), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array (FIELD_USER_MAIL . ': <b>' . $f_mail . '</b>'), 
                   'align=left valign=top class=c2', 'TD', false);    
    $table->addRow(array (FIELD_USER_LANG . ': ' . $f_language),
                   'align=left valign=top class=c2', 'TD', false);
}
$table->addRow(array($f_submit_2), 'align=left valign=top class=c2', 'TD', false);
$table->display();
?>
</div>
</form>

<form  <?php echo $formArray['attributes']; ?> >
<div class="ortro-table">
<?php
if ($user_info[0]['type'] == 'db') {
    echo $f_hidden; //hidden field
    $table = new HTML_Table($table_attributes . ' class=c2');
    $table->addRow(array($f_password . '&nbsp;' . FIELD_PASSWORD), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_password_confirm . '&nbsp;' . FIELD_CONFIRM_PASSWORD), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_submit), 
                   'align=left valign=top class=c2', 'TD', false);   
    $table->display();
}
?>
</div>
</form>