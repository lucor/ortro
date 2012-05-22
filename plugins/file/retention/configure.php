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

$plugin_field['retention'][0]['version']           = '1.2.4';
$plugin_field['retention'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['retention'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['retention'][0]['description']       = PLUGIN_FILE_RETENTION_DESCRIPTION;
$plugin_field['retention'][0]['title']             = PLUGIN_FILE_RETENTION_TITLE;

$plugin_field['retention'][1]['type']              = 'text';
$plugin_field['retention'][1]['name']              = 'file_retention_user';
$plugin_field['retention'][1]['value']             = '';
$plugin_field['retention'][1]['attributes']        = 'disabled';
$plugin_field['retention'][1]['description']       = PLUGIN_USER;
$plugin_field['retention'][1]['num_rules']         = '1';
$plugin_field['retention'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['retention'][1]['rule_type'][0]      = 'required';
$plugin_field['retention'][1]['rule_attribute'][0] = '';

$plugin_field['retention'][2]['type']        = 'text';
$plugin_field['retention'][2]['name']        = 'file_retention_port';
$plugin_field['retention'][2]['value']       = '';
$plugin_field['retention'][2]['attributes']  = 'disabled';
$plugin_field['retention'][2]['description'] = PLUGIN_PORT;

$plugin_field['retention'][3]['type']              = 'text';
$plugin_field['retention'][3]['name']              = 'file_retention_dir_path';
$plugin_field['retention'][3]['value']             = '';
$plugin_field['retention'][3]['attributes']        = 'disabled size=70';
$plugin_field['retention'][3]['description']       = PLUGIN_FILE_RETENTION_PATH_DESCRIPTION;
$plugin_field['retention'][3]['num_rules']         = '1';
$plugin_field['retention'][3]['rule_msg'][0]       = PLUGIN_FILE_RETENTION_RULE_3_0;
$plugin_field['retention'][3]['rule_type'][0]      = 'required';
$plugin_field['retention'][3]['rule_attribute'][0] = '';

$plugin_field['retention'][4]['type']              = 'text';
$plugin_field['retention'][4]['name']              = 'file_retention_search_for';
$plugin_field['retention'][4]['value']             = '';
$plugin_field['retention'][4]['attributes']        = 'disabled';
$plugin_field['retention'][4]['description']       = PLUGIN_FILE_RETENTION_SEARCH_DESCRIPTION;
$plugin_field['retention'][4]['num_rules']         = '1';
$plugin_field['retention'][4]['rule_msg'][0]       = PLUGIN_FILE_RETENTION_RULE_4_0;
$plugin_field['retention'][4]['rule_type'][0]      = 'required';
$plugin_field['retention'][4]['rule_attribute'][0] = '';

$plugin_field['retention'][5]['type']        = 'select';
$plugin_field['retention'][5]['name']        = 'file_retention_recursive';
$plugin_field['retention'][5]['value']       = array('0' => PLUGIN_FILE_RETENTION_RECURSIVE_VALUE_FALSE, 
                                                     '1' => PLUGIN_FILE_RETENTION_RECURSIVE_VALUE_TRUE);
$plugin_field['retention'][5]['attributes']  = '';
$plugin_field['retention'][5]['description'] = PLUGIN_FILE_RETENTION_RECURSIVE_DESCRIPTION;

$plugin_field['retention'][6]['type']        = 'select';
$plugin_field['retention'][6]['name']        = 'file_retention_compress_program';
$plugin_field['retention'][6]['value']       = array('0' => PLUGIN_FILE_RETENTION_COMPRESS_PROGRAM_VALUE_0, 
                                                     'gzip' => PLUGIN_FILE_RETENTION_COMPRESS_PROGRAM_VALUE_1,
                                                     'compress' => PLUGIN_FILE_RETENTION_COMPRESS_PROGRAM_VALUE_2);
$plugin_field['retention'][6]['attributes']  = '';
$plugin_field['retention'][6]['description'] = PLUGIN_FILE_RETENTION_PROGRAM_DESCRIPTION;

$plugin_field['retention'][7]['type']        = 'text';
$plugin_field['retention'][7]['name']        = 'file_retention_compress_program_path';
$plugin_field['retention'][7]['value']       = '';
$plugin_field['retention'][7]['attributes']  = 'disabled size=70';
$plugin_field['retention'][7]['description'] = PLUGIN_FILE_RETENTION_COMPRESS_PATH_DESCRIPTION;

$plugin_field['retention'][8]['type']              = 'text';
$plugin_field['retention'][8]['name']              = 'file_retention_compress_retention';
$plugin_field['retention'][8]['value']             = '0';
$plugin_field['retention'][8]['attributes']        = 'disabled';
$plugin_field['retention'][8]['description']       = PLUGIN_FILE_RETENTION_RETENTION_COMPRESS_PERIOD_DESCRIPTION;
$plugin_field['retention'][8]['num_rules']         = '1';
$plugin_field['retention'][8]['rule_msg'][0]       = PLUGIN_FILE_RETENTION_RULE_8_0;
$plugin_field['retention'][8]['rule_type'][0]      = 'required';
$plugin_field['retention'][8]['rule_attribute'][0] = '';

$plugin_field['retention'][9]['type']        = 'select';
$plugin_field['retention'][9]['name']        = 'file_retention_remove_flag';
$plugin_field['retention'][9]['value']       = array('0' => PLUGIN_FILE_RETENTION_REMOVE_FLAG_VALUE_0, 
                                                     '1' => PLUGIN_FILE_RETENTION_REMOVE_FLAG_VALUE_1, 
                                                     '2' => PLUGIN_FILE_RETENTION_REMOVE_FLAG_VALUE_2);
$plugin_field['retention'][9]['attributes']  = '';
$plugin_field['retention'][9]['description'] = PLUGIN_FILE_RETENTION_REMOVE_FILES_DESCRIPTION;

$plugin_field['retention'][10]['type']              = 'text';
$plugin_field['retention'][10]['name']              = 'file_retention_remove_retention';
$plugin_field['retention'][10]['value']             = '0';
$plugin_field['retention'][10]['attributes']        = 'disabled';
$plugin_field['retention'][10]['description']       = PLUGIN_FILE_RETENTION_RETENTION_PERIOD_DESCRIPTION;
$plugin_field['retention'][10]['num_rules']         = '1';
$plugin_field['retention'][10]['rule_msg'][0]       = PLUGIN_FILE_RETENTION_RULE_10_0;
$plugin_field['retention'][10]['rule_type'][0]      = 'required';
$plugin_field['retention'][10]['rule_attribute'][0] = '';
?>