<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the notification plugin settings
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

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

if (!isset($id_notify_type)) {
    //$id_notify_type could be defined in the add action 
    $id_notify_type = key($_REQUEST['id_chk']);    
}

$rows        = $dbUtil->dbQuery($dbh, $dbUtil->getNotifyLabel($id_notify_type));
$plugin_name = $rows[0][0];
$dbh         = $dbUtil->dbCloseConn($dbh);
unset($dbh);
$plugin_path = ORTRO_NOTIFICATION_PLUGINS . $plugin_name . DS;

$plugin_metadata_conf_file = $plugin_path . 'configure_metadata.php';

unset($conf_metadata);
unset($conf);
// Include the plugin language definition
i18n('notification', $plugin_name);
include $plugin_metadata_conf_file;
@include ORTRO_CONF_PLUGINS . 'notification_' . $plugin_name . '.php';
//Create the form
$form = new HTML_QuickForm('frmFilter', 'post');

$html = '';
foreach ($conf_metadata as $key => $value) {
    $html .= '<FIELDSET><LEGEND>' . $value['description'] . '</LEGEND><P>';
    foreach ($value as $myKey => $myValue) {
        if (is_array($myValue)) {
            $elements = split('-', $myValue['name']);
            if (isset($conf[$elements[0]][$elements[1]])) {
                $element_value = $conf[$elements[0]][$elements[1]];
            } else {
                //load default value
                $element_value = $myValue['value'];
                if (is_array($element_value)) {
                    $element_value = '';
                    if (isset( $myValue['value'][0])) {
                        $element_value = $myValue['value'][0];
                    }    
                }
            }
            $temp  = createDynamicForm($form, $myValue, $element_value, true);
            $html .= $temp['html'] . '<br/>';
        }
    }
    $html .= '</FIELDSET>';
     
}
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 
                              'save_settings', 
                              BUTTON_SAVE_SETTING)->toHTML();

$f_hidden  = 
    $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= 
    $form->createElement('hidden', 'plugin_name', $plugin_name)->toHTML();
$formArray = 
    $form->toArray(); 
//convert form in array for extact js and attributes
echo $formArray['javascript'];
?>
<div id="ortro-title">
      <?php echo NOTIFICATION_PLUGIN_EDIT_TOP; ?>
</div>    
<p>
      <?php echo NOTIFICATION_PLUGIN_EDIT_TITLE; ?>
</p>
<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<?php
    echo PLUGIN_EDIT_ENV;
    echo $html;
    echo $f_hidden;
    echo '<br/>';
    echo $f_submit;
?>
</form>