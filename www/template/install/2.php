<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Pre-Installation Check Page
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

require 'installUtil.php';

/* ACTION TOOLBAR */
$form = new HTML_QuickForm('frm', 'post');

$array_results = array();

$table = new HTML_Table($table_attributes);

//PHP Tests
$check_result = 0;
$php_path = null;
if (isset($_REQUEST['php_path']) &&  $_REQUEST['php_path'] != '') {
    $php_path = $_REQUEST['php_path'];
}

$checkPHP     = checkPHP($php_path);
$table->addRow(array(INSTALL_PHP), 'colspan=2', 'TH');
$table->addRow(array(INSTALL_PHP_VERSION,
                     showResultImage($checkPHP['php_version'])), '', 'TD', true);
$table->addRow(array(INSTALL_PHP_CLI_VERSION,
                     showResultImage($checkPHP['php-cli_version'])), 
                     '', 'TD', true);
if ($checkPHP['php_path'] == 'no' || isset($_SESSION['installation']['php_path'])) {
    $f_php_path = $form->createElement('text', 'php_path', '', 'size=29');
    $f_php_path->setValue($_SESSION['installation']['php_path']);
    
    if ($checkPHP['php_path'] != 'no') {
        $check_result = 1;
    }
    
    $table->addRow(array(INSTALL_PHP_CLI_PATH . $f_php_path->toHTML(), 
                   showResultImage($check_result)), '', 'TD', true);
}

array_push($array_results, $checkPHP['test_result']);

//Check for required extensions
$check_result = 0;
$check_extensions    = checkExtensions();
$table->addRow(array(INSTALL_EXTENSIONS), 'colspan=2', 'TH');
if ($check_extensions['test_result']) {
    $table->addRow(array(INSTALL_EXTENSIONS, 
                         showResultImage($check_extensions['test_result'])), 
                         '', 'TD', true);
} else {
    $table->addRow(array(INSTALL_EXTENSIONS_ALERT . '<br/> - ' .
                         implode('<br/> - ', $check_extensions['missed_extensions']), 
                         showResultImage($check_extensions['test_result'])), 
                         '', 'TD', true);
}

array_push($array_results, $check_extensions['test_result']);

//SSH Client Tests
$check_result = 0;
$checkSSH     = checkSSH();
$table->addRow(array(INSTALL_SSH), 'colspan=2', 'TH');
$table->addRow(array(INSTALL_SSH_CLIENT,
                     showResultImage($checkSSH['test_result'])), 
                     '', 'TD', true);
if ($checkSSH['ssh_path'] == 'no' || isset($_SESSION['installation']['ssh_path'])) {
    $f_ssh_path = $form->createElement('text', 'ssh_path', '', 'size=29');
    $f_ssh_path->setValue($_SESSION['installation']['ssh_path']);
    
    if ($checkSSH['ssh_path'] != 'no') {
        $check_result = 1;
    }
    $table->addRow(array(INSTALL_SSH_CLIENT_PATH . 
                         $f_ssh_path->toHTML(), 
                         showResultImage($check_result)), '', 'TD', true);
}

array_push($array_results, $checkSSH['test_result']);

//conf lang log plugins
//Tests for Ortro directories permissions
$table->addRow(array(INSTALL_FILE_PERMISSION), 'colspan=2', 'TH');

$check_result = 0;
$check_result = @is_writable(ORTRO_PATH);
$table->addRow(array('ORTRO_PATH: ' . ORTRO_PATH . ' ' . INSTALL_WRITE_PERMISSION,
                     showResultImage($check_result)), 
                     '', 'TD', true);
array_push($array_results, $check_result);

if (strpos(ORTRO_CONF, ORTRO_PATH) === false) { 
    //ORTRO_CONF is external to ORTRO_PATH
    $check_result = 0;
    $check_result = @is_writable(ORTRO_CONF);
    $table->addRow(array('ORTRO_CONF: ' . ORTRO_CONF . ' ' . 
                         INSTALL_WRITE_PERMISSION,
                         showResultImage($check_result)), 
                         '', 'TD', true);
    array_push($array_results, $check_result);
}

if (strpos(ORTRO_DATA, ORTRO_PATH) === false) { 
    //ORTRO_DATA is external to ORTRO_PATH
    $check_result = 0;
    $check_result = @is_writable(ORTRO_DATA);
    $table->addRow(array('ORTRO_DATA:' . ORTRO_DATA . ' ' . 
                         INSTALL_WRITE_PERMISSION,
                         showResultImage($check_result)), '', 'TD', true);
    array_push($array_results, $check_result);
}

if (strpos(ORTRO_LOG, ORTRO_PATH) === false) { 
    //ORTRO_LOG is external to ORTRO_PATH
    $check_result = 0;
    $check_result = @is_writable(ORTRO_LOG);
    $table->addRow(array('ORTRO_LOG: ' . ORTRO_LOG . ' ' . 
                         INSTALL_WRITE_PERMISSION,
                         showResultImage($check_result)), '', 'TD', true);
    array_push($array_results, $check_result);
}

//DB Driver Tests
$check_result  = 0;
$checkDBDriver = checkDBDriver();
$table->addRow(array(INSTALL_RDBMS), 'colspan=2', 'TH');
$supported_db = count($checkDBDriver['db_supported']);

if ($supported_db == 0) {
    $msg = INSTALL_RDBMS_ALERT;
    $table->addRow(array($msg, showResultImage(0)), '', 'TD', true);
} else {
    $check_result = 1;
    foreach ($checkDBDriver['db_supported'] as $key => $value) {
        $table->addRow(array($key, showResultImage(1)), '', 'TD', true);
    }
    $_SESSION['installation']['metadata']['db']['phptype']['value'] = 
        $checkDBDriver['db_supported'];
}
array_push($array_results, $check_result);

//Create Toolbar
if (in_array(0, $array_results)) {
    $toolbar = createToolbar(array('back'=>'default',
                                   'reload_page'=>'default',
                                   'install'=>INSTALL_MENU_PRE_INSTALL_CHECK));
    
} else {
    $toolbar = createToolbar(array('back'=>'default',
                                   'reload_page'=>'default',
                                   'forward'=>'default',
                                   'install'=>INSTALL_MENU_PRE_INSTALL_CHECK));
    
}
// The toolbar javascript is used below $toolbar['javascript'];

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