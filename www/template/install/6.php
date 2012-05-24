<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Plugin Page
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

$plugins_package_dir =  ORTRO_INSTALL . 'plugins' . DS;
if (is_dir($plugins_package_dir)) {
    $install_plugin = true;
    $dir_contents   = scandir($plugins_package_dir);
} else {
    $install_plugin = false;
}

if (isset($_POST['installPlugin']) && $_POST['installPlugin'] != "") {
    include_once 'Archive/Tar.php';
    $check_result = true;
    $msg_metadata = '';
    $msg_error    = '';
    $msg_success  = '';
    //Create the required directories for plugins
    if (!is_dir(ORTRO_PLUGINS)) {
        @mkdir(ORTRO_PLUGINS, 0700);
    }
    if (!is_dir(ORTRO_CONF_PLUGINS)) {
        @mkdir(ORTRO_CONF_PLUGINS, 0700);
    }
    
    $dbUtil = new DbUtil();
    
    $dbh = $dbUtil->dbOpenConn($_SESSION['installation']['db']['phptype'], 
                               $_SESSION['installation']['db']['host'],
                               $_SESSION['installation']['db']['port'],
                               $_SESSION['installation']['db']['database'],
                               $_SESSION['installation']['db']['username'],
                               $_SESSION['installation']['db']['password']);
                                 
    $GLOBALS['conf']['db']['tableprefix'] = 
        $_SESSION['installation']['db']['tableprefix'];
    foreach ($dir_contents as $plugin_package) {
        if ($plugin_package != '.' && $plugin_package != '..') {
            
            $tar_object = new Archive_Tar($plugins_package_dir . $plugin_package);
            $tar_object->extract(ORTRO_PLUGINS);
            
            if (PEAR::isError($tar_object->error_object)) {
                $action_msg = $tar_object->error_object->getMessage() . 
                              '<br/>' . $tar_object->error_object->getDebugInfo();
                $type_msg   = 'warning';
            } else {
                $split_file_info  = split('-', $plugin_package);
                $file_name        = $split_file_info[0];
                $split_file_name  = split('_', $file_name);
                $category         = array_shift($split_file_name);
                $plugin_name      = implode('_', $split_file_name);
                $plugin_conf_file = ORTRO_PLUGINS . $category . DS . 
                                    $plugin_name . DS . 'configure.php';
                                    
                $plugin_conf_metadata_file = ORTRO_PLUGINS . $category . DS . 
                                             $plugin_name . DS . 
                                             'configure_metadata.php';
                
                if (is_file($plugin_conf_file)) {
                    //Move the language files starting from untar folder
                    $dir_lang_plugin   = ORTRO_PLUGINS . $category . DS . 
                                         $plugin_name . DS . 'lang' . DS;
                    $dir_lang_contents = scandir($dir_lang_plugin);
                    foreach ($dir_lang_contents as $item) {
                        if (is_dir($dir_lang_plugin . $item) && 
                            $item != '.' && $item != '..' && $item != '.svn') {
                            $full_path_lang =  ORTRO_LANG . $item . DS . 
                                               'plugins' . DS .$category . DS . 
                                               $plugin_name . DS;
                            @mkdir($full_path_lang, 0700, true);
                            @copy($dir_lang_plugin . $item . DS . 'language.php', 
                                  $full_path_lang . DS . 'language.php');
                        }
                    }
                    //Remove install language files
                    include_once 'ioUtil.php';
                    @removeDirectory($dir_lang_plugin);
                    
                    i18n($category, $plugin_name);
                    include_once $plugin_conf_file;
                    if ($category == 'notification') {
                        $dbUtil->dbExec($dbh,
                            $dbUtil->setPluginNotification($plugin_name));
                    } else {
                        $dbUtil->dbExec($dbh,
                            $dbUtil->setPlugin($plugin_name, $category));
                    }
                    $msg_success .= '-&nbsp;' . $file_name . '<br/>';
                    if (is_file($plugin_conf_metadata_file)) { 
                        $msg_metadata .= '-&nbsp;' . $file_name . '<br/>';
                    }
                } else {
                    $msg_error .= '<br/>' . 
                                  INSTALL_MSG_ACTION_CONFIGURE_FILE_NOT_FOUND . 
                                  $file_info['name'];
                }
            }
        }
    }
}

/* ACTION TOOLBAR */
$form = new HTML_QuickForm('frm', 'post');

$table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';

$array_results = array();

$table = new HTML_Table($table_attributes);

if ($install_plugin && !$check_result) {
    $table->addRow(array(INSTALL_PLUGIN_NAME,INSTALL_PLUGIN_VERSION), '', 'TH');
    foreach ($dir_contents as $plugin_package) {
        if ($plugin_package != '.' && $plugin_package != '..') {
            $temp = explode('-', basename($plugin_package, '.tar.gz'));
            $table->addRow(array($temp[0], $temp[1]), '', 'TD', true);
        }
    }
    $f_submit = $form->createElement('submit', 
                                     'installPlugin',
                                     INSTALL_BUTTON_INSTALL)->toHTML();
    $table->addRow(array($f_submit), '', 'TD', true);
} else {
    $table->addRow(array(INSTALL_MSG_NO_PLUGIN_TO_INSTALL), '', 'TD', true);
    $_SESSION['installation']['installPlugin'] = true;
}

//Create Toolbar
$table_toolbar = new HTML_Table($table_attributes);

if ($check_result || isset($_SESSION['installation']['installPlugin'])) {
    $toolbar = createToolbar(array('back'=>'default',
                                   'forward'=>'default',
                                   'install'=>INSTALL_MENU_PLUGINS));
} else {
    $toolbar = createToolbar(array('back'=>'default',
                                   'install'=>INSTALL_MENU_PLUGINS));
}

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
if (isset($_POST['installPlugin']) && $_POST['installPlugin'] != "") {
    $msg_success_hdr = INSTALL_MSG_INSTALL_OK . '<br/>';
    showMessage($msg_success_hdr . $msg_success, 'success');
    if ($msg_error != '') {
        showMessage($msg_error, 'warning');
    }
    if ($msg_metadata != '') {
        $msg_metadata_hdr = INSTALL_MSG_PLUGIN_FURTHER_CONFIGURATION . '<br/>';
        showMessage($msg_metadata_hdr . $msg_metadata, 'warning');
    }
}
?>