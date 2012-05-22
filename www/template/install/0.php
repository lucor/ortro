<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Welcome page
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

/* ACTION TOOLBAR */
$form  = new HTML_QuickForm('frm', 'post');
$table = new HTML_Table($table_attributes);

$toolbar = createToolbar(array('reload_page'=>'default',
                               'forward'=>'default',
                               'install'=>INSTALL_WELCOME_DESCRIPTION));

$f_language = $form->createElement('select', 'language', '',
                                   $conf_metadata['env']['lang']['value']);

$f_language->setSelected(AuthUtil::getSessionData('language'));

$welcome_text = '<br/>' .
                INSTALL_COMMENT_DESCRIPTION .
                '<br/><br/>' .
                INSTALL_HOW_DESCRIPTION .
                '<br/><br/>' .
                $conf_metadata['env']['lang']['description'] . ': ' . 
                $f_language->toHTML();

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
<?php echo $f_hidden; ?>
<div class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
    <?php $table->display(); ?>
</div>
</form>