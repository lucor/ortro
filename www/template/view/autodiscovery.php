<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to perform a nmap scan on the specified ip/network
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

require_once 'System.php';

//Create the form
$form = new HTML_QuickForm('frm', 'post');
    
/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

// The toolbar javascript is used below $toolbar['javascript'];

$nmap_not_in_path = false;
if (!System::which('nmap')) {
    $f_nmap_binary = $form->addElement('text', 'nmap_binary', '', 'size=50')->toHTML();
    $form->addRule('nmap_binary', MSG_NMAP_BINARY_REQUIRED, 'required', '', 'client');
    $nmap_not_in_path = true;
}
$f_nmap_target = $form->addElement('text', 'target', '', 'size=70')->toHTML();
$form->addRule('target', MSG_TARGET_REQUIRED, 'required', '', 'client');

$f_nmap_os_detection = $form->createElement('checkbox', 
                                            'os_detection',
                                            '', 
                                            '&nbsp;' . FIELD_AUTODISCOVERY_OS_DETECTION
                                            )->toHTML();

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'scan', BUTTON_SCAN)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', 'scan')->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', 'add')->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();

//convert form in array for extract js and attributes
$formArray             = $form->toArray();
echo $formArray['javascript'];
?>
<div id="ortro-title">
<?php echo AUTODISCOVERY_TOP; ?>
</div>
<p>
<?php echo AUTODISCOVERY_TITLE; ?>
</p>

<form  <?php  echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
    <?php 
        $table = new HTML_Table($table_attributes);
        $table->addRow(array(FIELD_AUTODISCOVERY), '', 'TH');
        if ($nmap_not_in_path) {
            $table->addRow(array(FIELD_NMAP_BINARY . '<br/>' .
                                 $f_nmap_binary), '', 'TD', false);
        }
        $table->addRow(array(FIELD_NMAP_TARGET . '<br/>' .
                             $f_nmap_target . '<br/>' .
                             $f_nmap_os_detection), '', 'TD', false);
        $table->addRow(array($f_submit), '', 'TD', false); 
        $table->display();
    ?>
</div>
</form>

<div id="toolbar_menu" class="ortro-table">
    <?php echo $toolbar['menu']; ?>
</div>