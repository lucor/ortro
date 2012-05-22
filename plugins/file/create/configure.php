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

$plugin_field['create'][0]['version']           = '1.2.2';
$plugin_field['create'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['create'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['create'][0]['description']       = PLUGIN_FILE_CREATE_DESCRIPTION;
$plugin_field['create'][0]['title']             = PLUGIN_FILE_CREATE_TITLE;

$plugin_field['create'][1]['type']              = 'textarea';
$plugin_field['create'][1]['name']              = 'file_create_body';
$plugin_field['create'][1]['value']             = '';
$plugin_field['create'][1]['attributes']        = 'disabled rows=30 cols=30';
$plugin_field['create'][1]['description']       = PLUGIN_FILE_CREATE_FILE_BODY_DESCRIPTION;
$plugin_field['create'][1]['num_rules']         = '1';
$plugin_field['create'][1]['rule_msg'][0]       = PLUGIN_FILE_CREATE_FILE_RULE_1_0;
$plugin_field['create'][1]['rule_type'][0]      = 'required';
$plugin_field['create'][1]['rule_attribute'][0] = '';

$plugin_field['create'][2]['type']              = 'text';
$plugin_field['create'][2]['name']              = 'file_create_name';
$plugin_field['create'][2]['value']             = '';
$plugin_field['create'][2]['attributes']        = 'disabled';
$plugin_field['create'][2]['description']       = PLUGIN_FILE_NAME;
$plugin_field['create'][2]['num_rules']         = '1';
$plugin_field['create'][2]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['create'][2]['rule_type'][0]      = 'required';
$plugin_field['create'][2]['rule_attribute'][0] = '';
?>