<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to add the autodiscovered hosts
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
$toolbar = createToolbar(array('backPage'=>'default',
                               'add'=>'admin'));

/* SYSTEMS */
//Create the select list for System
$select_system['0'] = '---';
foreach ($systems as $key) {
    $select_system[$key["id_system"]] = $key["name"];
}    
$f_select_system = $form->addElement('select', 'id_system', '', 
                                     $select_system, '')->toHTML();

$form->addRule('id_system', MSG_SELECT_A_SYSTEM, 'nonzero', null, 'client');

$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();

$table = new HTML_Table($table_attributes);
$table->addRow(array(FIELD_SYSTEM), 'colspan=4', 'TH');
$table->addRow(array($f_select_system) , 'colspan=4', 'TD', false);
$table->addRow(array($f_chk_all, FIELD_HOSTNAME, FIELD_IP, FIELD_OS_DETAILS), '', 'TH');

$i = 1;
if (count($hosts) > 0) {
    foreach ($hosts as $key => $host) {
        $checkbox = $form->addElement('checkbox', 
                              'id_chk[' . $i . ']', 
                              '',
                              '');
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('value' => $i));
        $checkbox->updateAttributes(array('role' => 'admin'));
        $f_chk = $checkbox->toHTML();
        
        $f_hostname = $form->addElement('text', 'hostname_' . $i, '', 'id=hostname_' . $i);
        $f_hostname->setValue($host->getHostname());
        
        $f_ip = $form->addElement('text', 'ip_' . $i, '', 'id=ip_' . $i);
        $f_ip->setValue($host->getAddress());
        
        $form->addRule('hostname_' . $i, MSG_HOST_NAME_REQUIRED, 'required', '', 'client');
        $form->addRule('ip_' . $i, MSG_IP_REQUIRED, 'required', '', 'client');

        $f_hostname_html = $f_hostname->toHTML();
        $f_ip_html = $f_ip->toHTML();
        
        $table->addRow(array($f_chk, $f_hostname_html, $f_ip_html, $host->getOS()), 
                       'class=c2 onmouseover=highlightRow(this)', 'TD', true);
    }
}

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'is_admin_for_a_system', true)->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
echo $formArray['javascript'];

?>
<!-- start body -->
<div id="ortro-title">
    <?php echo AUTODISCOVERY_ADD_TOP; ?>
</div>
<p>
<?php echo AUTODISCOVERY_ADD_TITLE; ?>
</p>
<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript'];
          echo $toolbar['header'];
          echo $f_hidden; //hidden field
     ?>
</div>
<?php
if (count($hosts) > 0) {
?>
    <br/>
    <div class="ortro-table">
    <?php 
        $table->display();
    ?>
    </div>
<?
}
?>
<div id="toolbar_menu" class="ortro-table">
    <?php echo $toolbar['menu']; ?>
</div>
</form>
