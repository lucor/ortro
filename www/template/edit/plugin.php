<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the plugin settings
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

if (!isset($id_job_type)) {
    //$id_job_type could be defined in the add action 
    $id_job_type = key($_REQUEST['id_chk']);    
}

$dbUtil      = new DbUtil();
$dbh         = $dbUtil->dbOpenConnOrtro();
$rows        = $dbUtil->dbQuery($dbh, $dbUtil->getJobTypeLabel($id_job_type));
$plugin_name = $rows[0][0];
$category    = $rows[0][1];
$dbh         = $dbUtil->dbCloseConn($dbh);
unset($dbh);
$plugin_path = ORTRO_PLUGINS . $category . DS . $plugin_name . DS;

$plugin_metadata_conf_file = $plugin_path . 'configure_metadata.php';

unset($conf_metadata);
unset($conf);
// Include the plugin language definition
i18n($category, $plugin_name);
include $plugin_metadata_conf_file;
@include ORTRO_CONF_PLUGINS . $category . '_' . $plugin_name . '.php';
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
                    $element_value = $myValue['value'][0];    
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
$f_hidden .= 
    $form->createElement('hidden', 'plugin_category', $category)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray(); 
echo $formArray['javascript'];
?>
<div id="ortro-title">
      <?php echo PLUGIN_EDIT_TOP; ?>
</div>    
<p>
    <?php echo PLUGIN_EDIT_TITLE; ?>
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