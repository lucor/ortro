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

$plugin_field['webtest_slimdog'][0]['version']           = '1.2.2';
$plugin_field['webtest_slimdog'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['webtest_slimdog'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['webtest_slimdog'][0]['description']       = PLUGIN_WEBTEST_SLIMDOG_DESCRIPTION;
$plugin_field['webtest_slimdog'][0]['title']             = PLUGIN_WEBTEST_SLIMDOG_TITLE;

$plugin_field['webtest_slimdog'][1]['type']        = 'text';
$plugin_field['webtest_slimdog'][1]['name']        = 'webtest_slimdog_identity';
$plugin_field['webtest_slimdog'][1]['value']       = '';
$plugin_field['webtest_slimdog'][1]['attributes']  = 'disabled readonly';
$plugin_field['webtest_slimdog'][1]['description'] = PLUGIN_IDENTITY;
$plugin_field['webtest_slimdog'][1]['num_rules']   = '0';

$plugin_field['webtest_slimdog'][2]['type']              = 'textarea';
$plugin_field['webtest_slimdog'][2]['name']              = 'webtest_slimdog_input_script';
$plugin_field['webtest_slimdog'][2]['value']             = '';
$plugin_field['webtest_slimdog'][2]['attributes']        = 'disabled rows=30 cols=80';
$plugin_field['webtest_slimdog'][2]['description']       = PLUGIN_WEBTEST_SLIMDOG_CODE_DESCRIPTION;
$plugin_field['webtest_slimdog'][2]['num_rules']         = '1';
$plugin_field['webtest_slimdog'][2]['rule_msg'][0]       = PLUGIN_WEBTEST_SLIMDOG_RULE_2_0;
$plugin_field['webtest_slimdog'][2]['rule_type'][0]      = 'required';
$plugin_field['webtest_slimdog'][2]['rule_attribute'][0] = '';
?>