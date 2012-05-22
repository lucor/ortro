<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Login Page
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

$qs = '';

if ($auth->getDefaultAuthMethod() == 'CAS') {
    if($auth->isAuthorized() === false) {
        $auth->login('fake', 'fake');
        header('location:?');
        exit;
    }
}

if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
    $result = $auth->login($_REQUEST['username'], $_REQUEST['password']);
    if (!$result) {
        $_SESSION['action_msg'] = MSG_AUTHENTICATION_FAILED;
        $_SESSION['type_msg']   = 'warning';
    } else {
        $qs = 'mode=view';
    }
    header('location:?' . $qs);
    exit;
}

require ORTRO_TEMPLATE . 'header.php';

$form =& new HTML_Quickform('logon', 'post', 'index.php');
 
$html_user  = $form->addElement('text', 'username', '', '')-> toHTML();
$html_user .= '  ' . FIELD_USERNAME;
$form->addRule('username', MSG_USERNAME_REQUIRED, 'required', '', 'client');

$html_passwd  = $form->addElement('password', 'password', '', '')->toHTML();
$html_passwd .= '  ' . FIELD_PASSWORD;
$form->addRule('passwd', MSG_PASSWORD_REQUIRED, 'required', '', 'client');

$html_submit = $form->addElement('submit', 'btnSubmit', BUTTON_LOGIN)->toHTML();
 
$formArray = $form->toArray(); //convert form in array for extract js and attributes
echo $formArray['javascript'];
?>
<center>
<?php include ORTRO_TEMPLATE . 'showMessage.php'; ?>
<form <?php echo $formArray['attributes']; ?>>
<?php
$table = new HTML_Table('cellpadding="0" cellspacing="5" border="0"');
$table->addRow(array($html_user), 'align="left"', 'TD', false);
$table->addRow(array($html_passwd), 'align="left"', 'TD', false);
$table->addRow(array($html_submit), 'align="left"', 'TD', false);
echo $table->toHTML();
?>
</form>
</center>
<?php require ORTRO_TEMPLATE . 'footer.php'; ?>