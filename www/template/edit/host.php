<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the host settings
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
    //an error occourred on host setting modify
    $id_system = $_REQUEST['id_system'];
    $id_host   = $_REQUEST['id_host'];
} else {
    $id_host   = key($_REQUEST['id_chk']);
    $id_system = $_REQUEST['id_chk'][$id_host];    
}

$dbUtil    = new DbUtil();
$dbh       = $dbUtil->dbOpenConnOrtro();
$host_info = $dbUtil->dbQuery($dbh, 
                               $dbUtil->getHostById($id_host), 
                               MDB2_FETCHMODE_ASSOC);
$dbh       = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_hostname = $form->addElement('text', 'hostname', '', 'id=hostname');
$f_hostname->setValue($host_info[0]['hostname']);


$f_ip = $form->addElement('text', 'ip', '', 'id=ip');
$f_ip->setValue($host_info[0]['ip']);
$f_ip_html = $f_ip->toHTML();

$form->addRule('hostname', MSG_HOST_NAME_REQUIRED, 'required', '', 'client');
$form->addRule('ip', MSG_IP_REQUIRED, 'required', '', 'client');

$f_hostname_html = $f_hostname->toHTML();

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_host', $id_host)->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_system', $id_system)->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];

?>
<!-- start body -->
<div id="ortro-title">
    <?php echo HOST_EDIT_TOP; ?>
</div>
<p>
<?php echo HOST_EDIT_TITLE; ?>
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
 $table->addRow(array(FIELD_HOSTNAME,FIELD_IP), '', 'TH');
 $table->addRow(array ($f_hostname_html, $f_ip_html), '', 'TD', false);
 $table->addRow(array($f_submit), 'colspan=2', 'TD', false);
 $table->display();
?>
</div>
</form>
