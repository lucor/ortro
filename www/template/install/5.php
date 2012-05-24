<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Crontab Page
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

$check_result = 0;
if (isset($_POST['addcron']) && $_POST['addcron'] != "") {
    /* Add Ortro in crontab */

    $cmdLine = 'sh ' . ORTRO_INSTALL . 'backup_cron.sh ' . ORTRO_INSTALL . ' 2>&1';
    exec($cmdLine, $stdout, $exit_code);

    if ($exit_code != '0') {
        $message_old  = INSTALL_CRONTAB_OLD . '<br/>' . implode('<br/>', $stdout);
        $type_msg_old = 'warning';
    } else {
        $message_old  = INSTALL_CRONTAB_OLD . '<br/>' . implode('<br/>', $stdout);
        $type_msg_old = 'success';
        
        $cmdLine = 'sh ' . ORTRO_INSTALL . 
                   'add_cron.sh ' .  
                   ORTRO_PATH . ' ' .
                   ORTRO_INSTALL . ' ' . 
                   $_SESSION['installation']['env']['php_path'] .
                   ' 2>&1';
        exec($cmdLine, $stdout, $exit_code);
        
        if ($exit_code != '0') {
            $message_new  = INSTALL_CRONTAB_NEW . '<br/>' . 
                            implode('<br/>', $stdout);
            $type_msg_new = 'warning';
        } else {
            if (strpos($stdout[0], 'no crontab for') !== false) {
                array_shift($stdout);
            }
            $message_new  = INSTALL_CRONTAB_NEW . '<br/>' . 
                            implode('<br/>', $stdout);
            $type_msg_new = 'success';
            
            $_SESSION['installation']['addcron'] = 1;
            
            $check_result = 1;
        }
    }
}

/* ACTION TOOLBAR */
$form = new HTML_QuickForm('frm', 'post');

$table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';

$array_results = array();

$table = new HTML_Table($table_attributes);

$table->addRow(array(INSTALL_CRONTAB_MSG_PART_1 . '<b>' . 
                     exec('whoami') . '</b> ' . INSTALL_CRONTAB_MSG_PART_2 . 
                     ' <br/>'.
                     '<b>* * * * * ' . 
                     $_SESSION['installation']['env']['php_path'] . 'php ' . 
                     ORTRO_PATH . 'bin/crontab.php 2>&1 > ' . 
                     ORTRO_PATH . 'log/crontab.log</b><br/><br/>'.
                     INSTALL_CRONTAB_MSG_PART_3 . '<br/><b>' .
                     ORTRO_INSTALL . 'crontab_backup</b>' 
                     ), 
                'colspan=2', 'TD', true);

$f_submit = $form->createElement('submit', 
                                 'addcron', 
                                 INSTALL_BUTTON_EDIT_CRONTAB)->toHTML();

$table->addRow(array($f_submit), 'colspan=2', 'TD', true);

//Create Toolbar
$table_toolbar = new HTML_Table($table_attributes);

$toolbar = createToolbar(array('back'=>'default',
                               'forward'=>'default',
                               'install'=>INSTALL_CRONTAB_TITLE));

// The toolbar javascript is used below $toolbar['javascript'];

if ($check_result) {
    $f_hidden  = $form->createElement('hidden', 
                                      'action', 
                                      $_SESSION['installation_step'])->toHTML();
    $f_hidden .= $form->createElement('hidden',
                                      'mode',
                                      $_SESSION['installation_step'])->toHTML();    
} else {
    $f_hidden  = $form->createElement('hidden', 
                                      'action',
                                      $_SESSION['installation_step']-1)->toHTML();
    $f_hidden .= $form->createElement('hidden',
                                      'mode',
                                      $_SESSION['installation_step']-1)->toHTML();
}

$f_hidden .= $form->createElement('hidden', 'cat', 'install')->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];


?>
<form  <?php echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
<div class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
<?php 
if (!$check_result) { 
    $table->display();
} 
?>
</div>
</form>
<?php
if (isset($_POST['addcron']) && $_POST['addcron'] != "") {
    showMessage($message_old, $type_msg_old);
    showMessage($message_new, $type_msg_new);
}
?>