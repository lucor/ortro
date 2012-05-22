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

$plugin_field['search'][0]['version']           = '1.2.4';
$plugin_field['search'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['search'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['search'][0]['description']       = PLUGIN_FILE_SEARCH_DESCRIPTION;
$plugin_field['search'][0]['title']             = PLUGIN_FILE_SEARCH_TITLE;

$plugin_field['search'][1]['type']              = 'text';
$plugin_field['search'][1]['name']              = 'file_search_user';
$plugin_field['search'][1]['value']             = '';
$plugin_field['search'][1]['attributes']        = 'disabled';
$plugin_field['search'][1]['description']       = PLUGIN_USER;
$plugin_field['search'][1]['num_rules']         = '1';
$plugin_field['search'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['search'][1]['rule_type'][0]      = 'required';
$plugin_field['search'][1]['rule_attribute'][0] = '';

$plugin_field['search'][2]['type']        = 'text';
$plugin_field['search'][2]['name']        = 'file_search_port';
$plugin_field['search'][2]['value']       = '';
$plugin_field['search'][2]['attributes']  = 'disabled';
$plugin_field['search'][2]['description'] = PLUGIN_PORT;

$plugin_field['search'][3]['type']              = 'text';
$plugin_field['search'][3]['name']              = 'file_search_dir_path';
$plugin_field['search'][3]['value']             = '';
$plugin_field['search'][3]['attributes']        = 'disabled size=90';
$plugin_field['search'][3]['description']       = PLUGIN_FILE_SEARCH_PATH_DESCRIPTION;
$plugin_field['search'][3]['num_rules']         = '1';
$plugin_field['search'][3]['rule_msg'][0]       = PLUGIN_FILE_SEARCH_FILE_RULE_3_0;
$plugin_field['search'][3]['rule_type'][0]      = 'required';
$plugin_field['search'][3]['rule_attribute'][0] = '';

$plugin_field['search'][4]['type']              = 'text';
$plugin_field['search'][4]['name']              = 'file_search_search_for';
$plugin_field['search'][4]['value']             = '';
$plugin_field['search'][4]['attributes']        = 'size=90 disabled';
$plugin_field['search'][4]['description']       = PLUGIN_FILE_SEARCH_SEARCH_DESCRIPTION;
$plugin_field['search'][4]['num_rules']         = '1';
$plugin_field['search'][4]['rule_msg'][0]       = PLUGIN_FILE_SEARCH_FILE_RULE_4_0;
$plugin_field['search'][4]['rule_type'][0]      = 'required';
$plugin_field['search'][4]['rule_attribute'][0] = '';

$plugin_field['search'][5]['type']        = 'select';
$plugin_field['search'][5]['name']        = 'file_search_recursive';
$plugin_field['search'][5]['value']       = array('0' => PLUGIN_FILE_SEARCH_RECURSIVE_VALUE_FALSE, 
                                                  '1' => PLUGIN_FILE_SEARCH_RECURSIVE_VALUE_TRUE);
$plugin_field['search'][5]['attributes']  = '';
$plugin_field['search'][5]['description'] = PLUGIN_FILE_SEARCH_RECURSIVE_DESCRIPTION;

$plugin_field['search'][6]['type']        = 'select';
$plugin_field['search'][6]['name']        = 'file_search_operator';
$plugin_field['search'][6]['value']       = array('-gt' => '>', '-lt' => '<', '-eq' => '=', '-ne' => '!=');
$plugin_field['search'][6]['attributes']  = '';
$plugin_field['search'][6]['description'] = PLUGIN_FILE_SEARCH_OPERATOR_DESCRIPTION;
$plugin_field['search'][6]['num_rules']   = '0';

$plugin_field['search'][7]['type']        = 'text';
$plugin_field['search'][7]['name']        = 'file_search_expected_occurrence';
$plugin_field['search'][7]['value']       = '';
$plugin_field['search'][7]['attributes']  = 'disabled';
$plugin_field['search'][7]['description'] = PLUGIN_FILE_SEARCH_EXPECTED_OCCURENCE_DESCRIPTION;

$plugin_field['search'][8]['type']        = 'select';
$plugin_field['search'][8]['name']        = 'file_search_is_error';
$plugin_field['search'][8]['value']       = array('success' => PLUGIN_FILE_SEARCH_IS_ERROR_VALUE_1,
                                                  'error' => PLUGIN_FILE_SEARCH_IS_ERROR_VALUE_2);
$plugin_field['search'][8]['attributes']  = '';
$plugin_field['search'][8]['description'] = PLUGIN_FILE_SEARCH_IS_ERROR_DESCRIPTION;
$plugin_field['search'][8]['num_rules']   = '0';
?>