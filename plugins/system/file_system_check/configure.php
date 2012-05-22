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

$plugin_field['file_system_check'][0]['version']           = '1.2.4';
$plugin_field['file_system_check'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['file_system_check'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['file_system_check'][0]['description']       = PLUGIN_FILE_SYSTEM_CHECK_DESCRIPTION;
$plugin_field['file_system_check'][0]['title']             = PLUGIN_FILE_SYSTEM_CHECK_TITLE;

$plugin_field['file_system_check'][1]['type']              = 'text';
$plugin_field['file_system_check'][1]['name']              = 'file_system_check_user';
$plugin_field['file_system_check'][1]['value']             = '';
$plugin_field['file_system_check'][1]['attributes']        = 'disabled';
$plugin_field['file_system_check'][1]['description']       = PLUGIN_USER;
$plugin_field['file_system_check'][1]['num_rules']         = '1';
$plugin_field['file_system_check'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['file_system_check'][1]['rule_type'][0]      = 'required';
$plugin_field['file_system_check'][1]['rule_attribute'][0] = '';

$plugin_field['file_system_check'][2]['type']        = 'text';
$plugin_field['file_system_check'][2]['name']        = 'file_system_check_port';
$plugin_field['file_system_check'][2]['value']       = '';
$plugin_field['file_system_check'][2]['attributes']  = 'disabled';
$plugin_field['file_system_check'][2]['description'] = PLUGIN_PORT;

$plugin_field['file_system_check'][3]['type']        = 'submit';
$plugin_field['file_system_check'][3]['name']        = 'file_system_check_get_dynamic_params';
$plugin_field['file_system_check'][3]['value']       = 'Refresh';
$plugin_field['file_system_check'][3]['attributes']  = 'disabled';
$plugin_field['file_system_check'][3]['description'] = PLUGIN_FILE_SYSTEM_CHECK_REFRESH_DESCRIPTION;
?>