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

$plugin_field['read_file'][0]['version']           = '1.0.2';
$plugin_field['read_file'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['read_file'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['read_file'][0]['description']       = PLUGIN_FILE_READ_FILE_DESCRIPTION;
$plugin_field['read_file'][0]['title']             = PLUGIN_FILE_READ_FILE_TITLE;

$plugin_field['read_file'][1]['type']              = 'text';
$plugin_field['read_file'][1]['name']              = 'file_read_file_name';
$plugin_field['read_file'][1]['value']             = '';
$plugin_field['read_file'][1]['attributes']        = 'disabled';
$plugin_field['read_file'][1]['description']       = PLUGIN_FILE_NAME;
$plugin_field['read_file'][1]['num_rules']         = '1';
$plugin_field['read_file'][1]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['read_file'][1]['rule_type'][0]      = 'required';
$plugin_field['read_file'][1]['rule_attribute'][0] = '';
?>