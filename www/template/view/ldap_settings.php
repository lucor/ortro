<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the ldap settings defined in Ortro
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
//unset($conf);

i18n('template', 'configure_metadata_ldap.php');
require ORTRO_CONF . 'configure_metadata_ldap.php';
require_once 'installUtil.php';

//Create the form
$form = new HTML_QuickForm('frmFilter', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

if (!function_exists('ldap_connect')) {
    showMessage(MSG_LDAP_NOT_ENABLED, 'warning');
} else {
    @include ORTRO_CONF . 'configure_ldap.php';
    //modify settings
    $intro            = LDAP_SETTING_TITLE . '<br/>';
    $table_open       = '';
    $table_close      = '';
    $f_hidden_install = '';
    $html             = '';
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
                        $element_value = $myValue['value'][0];    
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
                                  'save_settings', BUTTON_SAVE_SETTING)->toHTML();
    
    $f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
    $f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
    $f_hidden .= $form->createElement('hidden', 'action', 'ldap_settings')->toHTML();
    $f_hidden .= $f_hidden_install;
    $formArray = $form->toArray(); 
    //convert form in array for extact js and attributes
    echo $formArray['javascript'];
    ?>
    <form  <?php echo $formArray['attributes']; ?> >
    <div id="toolbar" class="ortro-table">
        <?php echo $toolbar['javascript']; ?>
        <?php echo $toolbar['header']; ?>
    </div>
    <br/>
    <?php
        echo $intro . $table_open;
        echo $html;
        echo $f_hidden;
        echo '<br/>';
        echo $f_submit;
        echo $table_close;
    ?>
    </form>
    <?php 
} 
?>