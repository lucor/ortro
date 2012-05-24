<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a host in Ortro
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
$hosts   = $dbUtil->dbQuery($dbh, $dbUtil->getHosts(), MDB2_FETCHMODE_ASSOC);
$dbh     = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

//Create the select list for System
$select_system['0'] = '---';
foreach ($systems as $key) {
    if (array_key_exists('ADMIN', $_policy) ||  
        in_array($key['id_system'], explode(',', 
                                            $_policy['SYSTEM_ADMIN']))) {
        $select_system[$key["id_system"]] = $key["name"];
    }
}
        
$f_select_system = $form->addElement('select', 
                                     'id_system', 
                                     '',
                                     $select_system, 
                                     '')->toHTML();
$form->addRule('id_system', MSG_SELECT_A_SYSTEM, 'nonzero', null, 'client');
        
//Create the select list for Host
$select_host['0'] = FIELD_ADD_HOST;
foreach ($hosts as $key) {
    $select_host[$key["id_host"]] = $key["hostname"] . ' (' . $key["ip"] . ')';
}

$f_select_host = $form->createElement('select', 
                                      'id_host', 
                                      '', 
                                      $select_host,
                                      'onchange="showFormFields(this.value,' . 
                                      ' \'table_host_\', 1);"')->toHTML();

$f_hostname = $form->addElement('text', 'hostname', '', 'id=hostname')->toHTML();
$f_ip       = $form->addElement('text', 'ip', '', 'id=ip')->toHTML();
$form->addRule('hostname', MSG_HOST_NAME_REQUIRED, 'required', '', 'client');
$form->addRule('ip', MSG_IP_REQUIRED, 'required', '', 'client');

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
   <?php echo HOST_ADD_TOP; ?>
</div>

<p>
<?php echo HOST_ADD_TITLE; ?>
</p>

<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>

<?php
    //convert form in array for extact js and attribute 
    $formArray = $form->toArray(); 
    echo $formArray['javascript']
?>

<form  <?php echo $formArray['attributes'];?> >
<div class="ortro-table">
<?php 
 //Hidden fields
 echo $f_hidden;
 
 $table = new HTML_Table($table_attributes);
 $table->addRow(array(FIELD_SYSTEM), '', 'TH');
 $table->addRow(array($f_select_system), "", 'TD', false);
 $table->display();
 $table = new HTML_Table($table_attributes);
 $table->addRow(array(FIELD_HOSTNAME), '', 'TH');
 $table->addRow(array($f_select_host), '', 'TD', false);
 $table->display();
 $table = new HTML_Table($table_attributes . ' id=table_host_0');
 $table->addRow(array ($f_hostname . '&nbsp;'. FIELD_HOSTNAME . 
                                     '<br/>' . $f_ip . '&nbsp;'. 
                                     FIELD_IP), 
                                     "align=left valign=top class=c2", 
                                     'TD', false);
 $table->display();
 $table = new HTML_Table($table_attributes);
 $table->addRow(array($f_submit), 'align=left colspan=2', 'TD', false);
 $table->display();
?>
</div>
</form>