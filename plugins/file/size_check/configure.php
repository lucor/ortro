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

$plugin_field['size_check'][0]['version']           = '1.2.3';
$plugin_field['size_check'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['size_check'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['size_check'][0]['description']       = PLUGIN_FILE_SIZE_CHECK_DESCRIPTION;
$plugin_field['size_check'][0]['title']             = PLUGIN_FILE_SIZE_CHECK_TITLE;

$plugin_field['size_check'][1]['type']              = 'text';
$plugin_field['size_check'][1]['name']              = 'file_size_check_user';
$plugin_field['size_check'][1]['value']             = '';
$plugin_field['size_check'][1]['attributes']        = 'disabled';
$plugin_field['size_check'][1]['description']       = PLUGIN_USER;
$plugin_field['size_check'][1]['num_rules']         = '1';
$plugin_field['size_check'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['size_check'][1]['rule_type'][0]      = 'required';
$plugin_field['size_check'][1]['rule_attribute'][0] = '';

$plugin_field['size_check'][2]['type']        = 'text';
$plugin_field['size_check'][2]['name']        = 'file_size_check_port';
$plugin_field['size_check'][2]['value']       = '';
$plugin_field['size_check'][2]['attributes']  = 'disabled';
$plugin_field['size_check'][2]['description'] = PLUGIN_PORT;

$plugin_field['size_check'][3]['type']              = 'text';
$plugin_field['size_check'][3]['name']              = 'file_size_check_dir_path';
$plugin_field['size_check'][3]['value']             = '';
$plugin_field['size_check'][3]['attributes']        = 'disabled size=70';
$plugin_field['size_check'][3]['description']       = PLUGIN_FILE_SIZE_CHECK_PATH_DESCRIPTION;
$plugin_field['size_check'][3]['num_rules']         = '1';
$plugin_field['size_check'][3]['rule_msg'][0]       = PLUGIN_FILE_SIZE_CHECK_RULE_3_0;
$plugin_field['size_check'][3]['rule_type'][0]      = 'required';
$plugin_field['size_check'][3]['rule_attribute'][0] = '';

$plugin_field['size_check'][4]['type']              = 'text';
$plugin_field['size_check'][4]['name']              = 'file_size_check_search_for';
$plugin_field['size_check'][4]['value']             = '';
$plugin_field['size_check'][4]['attributes']        = 'disabled';
$plugin_field['size_check'][4]['description']       = PLUGIN_FILE_SIZE_CHECK_SEARCH_DESCRIPTION;
$plugin_field['size_check'][4]['num_rules']         = '1';
$plugin_field['size_check'][4]['rule_msg'][0]       = PLUGIN_FILE_SIZE_CHECK_RULE_4_0;
$plugin_field['size_check'][4]['rule_type'][0]      = 'required';
$plugin_field['size_check'][4]['rule_attribute'][0] = '';

$plugin_field['size_check'][5]['type']        = 'select';
$plugin_field['size_check'][5]['name']        = 'file_size_check_recursive';
$plugin_field['size_check'][5]['value']       = array('0' => PLUGIN_FILE_SIZE_CHECK_RECURSIVE_VALUE_FALSE,
                                                      '1' => PLUGIN_FILE_SIZE_CHECK_RECURSIVE_VALUE_TRUE);
$plugin_field['size_check'][5]['attributes']  = '';
$plugin_field['size_check'][5]['description'] = PLUGIN_FILE_SIZE_CHECK_RECURSIVE_DESCRIPTION;

$plugin_field['size_check'][6]['type']              = 'text';
$plugin_field['size_check'][6]['name']              = 'file_size_check_size';
$plugin_field['size_check'][6]['value']             = '';
$plugin_field['size_check'][6]['attributes']        = 'disabled';
$plugin_field['size_check'][6]['description']       = PLUGIN_FILE_SIZE_CHECK_THRESHOLD_DESCRIPTION;
$plugin_field['size_check'][6]['num_rules']         = '1';
$plugin_field['size_check'][6]['rule_msg'][0]       = PLUGIN_FILE_SIZE_CHECK_RULE_6_0;
$plugin_field['size_check'][6]['rule_type'][0]      = 'required';
$plugin_field['size_check'][6]['rule_attribute'][0] = '';
?>