<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: License Page
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

$form = new HTML_QuickForm('frm', 'post');

$table = new HTML_Table($table_attributes);

$toolbar = createToolbar(array('back'=>'default',
                               'forward'=>'default',
                               'install'=>INSTALL_MENU_LICENSE));

$welcome_text = '<u>' . INSTALL_LICENSE . 
                '</u><br\><iframe src="template/install/gpl.html" ' . 
                'frameborder="0" width="100%" height="450px" marginwidth="25px"' . 
                ' scrolling="auto"></iframe>';

$table->addRow(array($welcome_text), '', 'TD', true);

$f_hidden  = $form->createElement('hidden', 'action', 
                                  $_SESSION['installation_step'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', 
                                  $_SESSION['installation_step'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', 'install')->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>

<form  <?php echo $formArray['attributes']; ?> >
<?php     echo $f_hidden; ?>
<div class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
    <?php $table->display(); ?>
</div>
</form>