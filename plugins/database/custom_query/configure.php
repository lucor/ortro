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

$plugin_actions['custom_query'][0]['description'] = PLUGIN_METADATA_CONFIGURATION;
$plugin_actions['custom_query'][0]['action']      = 'plugin';
$plugin_actions['custom_query'][0]['file']        = 'display_archive_results';
$plugin_actions['custom_query'][0]['image']       = 'archive.png';

$plugin_field['custom_query'][0]['version']           = '1.2.2';
$plugin_field['custom_query'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['custom_query'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['custom_query'][0]['description']       = PLUGIN_DB_CUSTOM_QUERY_DESCRIPTION;
$plugin_field['custom_query'][0]['title']             = PLUGIN_DB_CUSTOM_QUERY_TITLE;

$plugin_field['custom_query'][1]['type']              = 'text';
$plugin_field['custom_query'][1]['name']              = 'db_custom_query_identity';
$plugin_field['custom_query'][1]['value']             = '';
$plugin_field['custom_query'][1]['attributes']        = 'disabled readonly';
$plugin_field['custom_query'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['custom_query'][1]['num_rules']         = '1';
$plugin_field['custom_query'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['custom_query'][1]['rule_type'][0]      = 'required';
$plugin_field['custom_query'][1]['rule_attribute'][0] = '';

$plugin_field['custom_query'][2]['type']              = 'textarea';
$plugin_field['custom_query'][2]['name']              = 'db_custom_query_query';
$plugin_field['custom_query'][2]['value']             = '';
$plugin_field['custom_query'][2]['attributes']        = 'disabled rows=5 cols=50';
$plugin_field['custom_query'][2]['description']       = PLUGIN_QUERY ;
$plugin_field['custom_query'][2]['num_rules']         = '1';
$plugin_field['custom_query'][2]['rule_msg'][0]       = PLUGIN_QUERY_RULE;
$plugin_field['custom_query'][2]['rule_type'][0]      = 'required';
$plugin_field['custom_query'][2]['rule_attribute'][0] = '';

$plugin_field['custom_query'][3]['type']        = 'select';
$plugin_field['custom_query'][3]['name']        = 'db_custom_query_operator';
$plugin_field['custom_query'][3]['value']       = array('>' => '>', '<' => '<', '=' => '=', '!=' => '!=');
$plugin_field['custom_query'][3]['attributes']  = '';
$plugin_field['custom_query'][3]['description'] = PLUGIN_DB_CUSTOM_QUERY_OPERATOR_DESCRIPTION;
$plugin_field['custom_query'][3]['num_rules']   = '0';

$plugin_field['custom_query'][4]['type']              = 'text';
$plugin_field['custom_query'][4]['name']              = 'db_custom_query_threshold';
$plugin_field['custom_query'][4]['value']             = '';
$plugin_field['custom_query'][4]['attributes']        = 'disabled';
$plugin_field['custom_query'][4]['description']       = PLUGIN_DB_CUSTOM_QUERY_THRESHOLD_DESCRIPTION;
$plugin_field['custom_query'][4]['num_rules']         = '1';
$plugin_field['custom_query'][4]['rule_msg'][0]       = PLUGIN_DB_CUSTOM_QUERY_RULE_4_0;
$plugin_field['custom_query'][4]['rule_type'][0]      = 'required';
$plugin_field['custom_query'][4]['rule_attribute'][0] = '';

$plugin_field['custom_query'][5]['type']        = 'select';
$plugin_field['custom_query'][5]['name']        = 'db_custom_query_is_error';
$plugin_field['custom_query'][5]['value']       = array('0' => PLUGIN_DB_CUSTOM_QUERY_SUCCESS, 
                                                        '1' => PLUGIN_DB_CUSTOM_QUERY_ERROR);
$plugin_field['custom_query'][5]['attributes']  = '';
$plugin_field['custom_query'][5]['description'] = PLUGIN_DB_CUSTOM_QUERY_ERROR_DESCRIPTION ;
$plugin_field['custom_query'][5]['num_rules']   = '0';

$plugin_field['custom_query'][6]['type']        = 'text';
$plugin_field['custom_query'][6]['name']        = 'db_custom_query_retention';
$plugin_field['custom_query'][6]['value']       = '';
$plugin_field['custom_query'][6]['attributes']  = 'disabled';
$plugin_field['custom_query'][6]['description'] = PLUGIN_RETENTION;
?>