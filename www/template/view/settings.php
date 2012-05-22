<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the environment settings defined in Ortro
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

unset($conf_metadata);
unset($conf);

require ORTRO_CONF . 'configure.php';
i18n('template', 'configure_metadata_install.php');
require ORTRO_CONF . 'configure_metadata_install.php';

require_once 'installUtil.php';

$db_supported = checkDBDriver();

$conf_metadata['db']['phptype']['value'] = $db_supported['db_supported'];

//Create the form
$form = new HTML_QuickForm('frmFilter', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$html = '';
foreach ($conf_metadata as $key => $value) {
    $html .= '<fieldset><legend>' . $value['description'] . '</legend><p>';
    foreach ($value as $myKey => $myValue) {
        if (is_array($myValue)) {
            $elements = split('-', $myValue['name']);
            if (isset($conf[$elements[0]][$elements[1]])) {
                $element_value = $conf[$elements[0]][$elements[1]];
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
            
            $temp  = createDynamicForm($form, $myValue, $element_value, true);
            $html .= $temp['html'] . '<br/>';
        }
    }
    $html .= '</p></fieldset>';
     
}
/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 
                              'save_settings', 
                              BUTTON_SAVE_SETTING)->toHTML();

$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', 'settings')->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
echo $formArray['javascript'];
?>
<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<?php
    echo SETTING_TITLE . '<br/>';
    echo $html;
    echo $f_hidden;
    echo '<br/>';
    echo $f_submit;
?>
</form>