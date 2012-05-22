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

$plugin_actions['oracle_freespa_report'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['oracle_freespa_report'][0]['action']      = 'plugin';
$plugin_actions['oracle_freespa_report'][0]['file']        = 'display_reports';
$plugin_actions['oracle_freespa_report'][0]['image']       = 'archive.png'; 

$plugin_field['oracle_freespa_report'][0]['version']           ='1.0.2';
$plugin_field['oracle_freespa_report'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['oracle_freespa_report'][0]['authors'][0]        = 'Giuseppe De Santis <desantis.g@gmail.com>';
$plugin_field['oracle_freespa_report'][0]['description']       = PLUGIN_ORACLE_FREESPA_REPORT_DESCRIPTION;
$plugin_field['oracle_freespa_report'][0]['title']             = PLUGIN_ORACLE_FREESPA_REPORT_TITLE;

$plugin_field['oracle_freespa_report'][1]['type']              = 'text';
$plugin_field['oracle_freespa_report'][1]['name']              = 'db_oracle_freespa_report_identity';
$plugin_field['oracle_freespa_report'][1]['value']             = '';
$plugin_field['oracle_freespa_report'][1]['attributes']        = 'disabled readonly';
$plugin_field['oracle_freespa_report'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['oracle_freespa_report'][1]['num_rules']         = '1';
$plugin_field['oracle_freespa_report'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['oracle_freespa_report'][1]['rule_type'][0]      = 'required';
$plugin_field['oracle_freespa_report'][1]['rule_attribute'][0] = '';

$plugin_field['oracle_freespa_report'][2]['type']              = 'textarea';
$plugin_field['oracle_freespa_report'][2]['name']              = 'db_oracle_freespa_report_tablespaces';
$plugin_field['oracle_freespa_report'][2]['value']             = '';
$plugin_field['oracle_freespa_report'][2]['attributes']        = 'disabled';
$plugin_field['oracle_freespa_report'][2]['description']       = PLUGIN_ORACLE_FREESPA_REPORT_TABLESPACES_DESCRIPTION;

$plugin_field['oracle_freespa_report'][3]['type']              = 'select';
$plugin_field['oracle_freespa_report'][3]['name']              = 'db_oracle_freespa_report_attach_result';
$plugin_field['oracle_freespa_report'][3]['value']             = array('html' => '.html', 
                                                                       'txt' => '.txt', 
                                                                       'csv' => '.csv');
$plugin_field['oracle_freespa_report'][3]['attributes']        = '';
$plugin_field['oracle_freespa_report'][3]['description']       = PLUGIN_ATTACH_RESULT;

$plugin_field['oracle_freespa_report'][4]['type']              = 'text';
$plugin_field['oracle_freespa_report'][4]['name']              = 'db_oracle_freespa_report_file_name';
$plugin_field['oracle_freespa_report'][4]['value']             = '';
$plugin_field['oracle_freespa_report'][4]['attributes']        = 'disabled';
$plugin_field['oracle_freespa_report'][4]['description']       = PLUGIN_FILE_NAME;
$plugin_field['oracle_freespa_report'][4]['num_rules']         = '1';
$plugin_field['oracle_freespa_report'][4]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['oracle_freespa_report'][4]['rule_type'][0]      = 'required';
$plugin_field['oracle_freespa_report'][4]['rule_attribute'][0] = '';

$plugin_field['oracle_freespa_report'][5]['type']        = 'text';
$plugin_field['oracle_freespa_report'][5]['name']        = 'db_oracle_freespa_report_retention';
$plugin_field['oracle_freespa_report'][5]['value']       = '';
$plugin_field['oracle_freespa_report'][5]['attributes']  = 'disabled';
$plugin_field['oracle_freespa_report'][5]['description'] = PLUGIN_RETENTION;
?>