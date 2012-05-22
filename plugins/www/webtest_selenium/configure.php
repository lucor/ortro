<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Configuration file, allows to generate dinamically the web form for the plugin
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_field['webtest_selenium'][0]['version']           = '1.2.2';
$plugin_field['webtest_selenium'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['webtest_selenium'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['webtest_selenium'][0]['description']       = PLUGIN_WEBTEST_SELENIUM_DESCRIPTION;
$plugin_field['webtest_selenium'][0]['title']             = PLUGIN_WEBTEST_SELENIUM_TITLE;

$plugin_field['webtest_selenium'][1]['type']        = 'select';
$plugin_field['webtest_selenium'][1]['name']        = 'webtest_selenium_programming_language';
$plugin_field['webtest_selenium'][1]['value']       = array('perl' => PLUGIN_WEBTEST_SELENIUM_LANGUAGE_VALUE_PERL, 
                                                            'python' => PLUGIN_WEBTEST_SELENIUM_LANGUAGE_VALUE_PYTHON);
$plugin_field['webtest_selenium'][1]['attributes']  ='';
$plugin_field['webtest_selenium'][1]['description'] = PLUGIN_WEBTEST_SELENIUM_LANGUAGE_DESCRIPTION;

$plugin_field['webtest_selenium'][2]['type']        = 'text';
$plugin_field['webtest_selenium'][2]['name']        = 'webtest_selenium_identity';
$plugin_field['webtest_selenium'][2]['value']       = '';
$plugin_field['webtest_selenium'][2]['attributes']  = 'disabled readonly';
$plugin_field['webtest_selenium'][2]['description'] = PLUGIN_IDENTITY;
$plugin_field['webtest_selenium'][2]['num_rules']   = '0';

$plugin_field['webtest_selenium'][3]['type']              = 'textarea';
$plugin_field['webtest_selenium'][3]['name']              = 'webtest_selenium_input_script';
$plugin_field['webtest_selenium'][3]['value']             = '';
$plugin_field['webtest_selenium'][3]['attributes']        = 'disabled rows=30 cols=80';
$plugin_field['webtest_selenium'][3]['description']       = PLUGIN_WEBTEST_SELENIUM_CODE_DESCRIPTION;
$plugin_field['webtest_selenium'][3]['num_rules']         = '1';
$plugin_field['webtest_selenium'][3]['rule_msg'][0]       = PLUGIN_WEBTEST_SELENIUM_RULE_3_0;
$plugin_field['webtest_selenium'][3]['rule_type'][0]      = 'required';
$plugin_field['webtest_selenium'][3]['rule_attribute'][0] = '';
?>