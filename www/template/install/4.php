<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: SSH Page
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

$check_result = 0;

if (isset($_POST['env-ssh_type']) && $_POST['env-ssh_type'] != "") {
    
    $ssh_key_dir = ORTRO_CONF . '.ssh' . DS;
    
    if (!is_dir($ssh_key_dir)) {
        mkdir($ssh_key_dir);
    }
    
    chmod($ssh_key_dir, 0700);
    
    $cmdLine = $_SESSION['installation']['env']['ssh_path'] . 
               'ssh-keygen -t ' . $_POST['env-ssh_type'] .
               ' -f ' . $ssh_key_dir . $_POST['env-ssh_keyname'] .
               ' -C  ortro -N \'\'' .
               ' -b ' . $_POST['env-ssh_bits'] . 
               ' 2>&1';

    exec($cmdLine, $stdout, $exit_code);
    
    if ($exit_code != '0') {
        showMessage(implode('<br/>', $stdout), 'warning');
        $_SESSION['installation']['ssh_key_created'] = '0';
    } else {
        $check_result = 1;
        
        $_SESSION['installation']['ssh_key_created'] = '1';
        $_SESSION['installation']['env']['ssh_type'] = $_REQUEST['env-ssh_type'];
        
        $_SESSION['installation']['env']['ssh_keyname'] = 
            $_REQUEST['env-ssh_keyname'];
        $_SESSION['installation']['env']['ssh_bits']    = $_REQUEST['env-ssh_bits'];
        
        $_SESSION['installation']['env']['ssh_StrictHostKeyChecking'] = 
            $_REQUEST['env-ssh_StrictHostKeyChecking'];
        
        showMessage(implode("<br/>", $stdout), 'success');
    }

}
/* ACTION TOOLBAR */
$form = new HTML_QuickForm('frm', 'post');

$table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';


$array_results = array();

$table = new HTML_Table($table_attributes);

$html = '';

foreach ($conf_metadata as $key => $value) {
    if ($key == 'env') {
        foreach ($value as $myKey => $myValue) {
            if (is_array($myValue)) {
                $elements = split('-', $myValue['name']);
                if (strpos($elements[1], 'ssh') !== false && 
                    $elements[1] != 'ssh_path') {
                    if (isset($_SESSION['installation']
                                       [$elements[0]][$elements[1]])) {
                        $element_value = 
                            $_SESSION['installation'][$elements[0]][$elements[1]];
                    } else {
                        //load default value
                        $element_value = $myValue['value'];
                        if (is_array($element_value)) {
                            if (isset($myValue['value'][0])) {
                                $element_value = $myValue['value'][0];
                            } else {
                                $element_value = '';
                            }
                        }
                    }
                    if (isset($_SESSION['installation']['ssh_key_created'])) {
                        $temp_array = explode('.', $myValue['description']);
                        
                        $html .= $temp_array[0] . ': <b>' . $element_value . 
                                 '</b><br/>';
                        unset($temp_array);
                    } else {
                        $temp  = createDynamicForm($form, 
                                                     $myValue,
                                                     $element_value,
                                                     true);
                        $html .= $temp['html'] . '<br/>';
                    }
                }
            }
        }
    }     
}

$table->addRow(array($html), 'colspan=2', 'TD', true);

if (!isset($_SESSION['installation']['ssh_key_created'])) {
    $f_submit = $form->createElement('submit', 
                                     'create_key', 
                                     INSTALL_BUTTON_GENERATE)->toHTML();
    $table->addRow(array($f_submit), 'colspan=2', 'TD', true);
}

//Create Toolbar
$table_toolbar = new HTML_Table($table_attributes);

if ($check_result || isset($_SESSION['installation']['ssh_key_created'])) {
    $toolbar = createToolbar(array('back'=>'default',
                                   'forward'=>'default',
                                   'install'=>INSTALL_SSH_TITLE));
} else {
    $toolbar = createToolbar(array('back'=>'default',
                                   'install'=>INSTALL_SSH_TITLE));
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
<?php     echo $f_hidden; ?>
<div class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
    <?php $table->display(); ?>
</div>
</form>