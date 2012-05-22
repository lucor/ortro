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
 * @link     http://www.ortro.net
 */

$plugin_actions['check_tablespace'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['check_tablespace'][0]['action']      = 'plugin';
$plugin_actions['check_tablespace'][0]['file']        = 'display_reports';
$plugin_actions['check_tablespace'][0]['image']       = 'archive.png'; 

$plugin_field['check_tablespace'][0]['version']           = '1.2.2';
$plugin_field['check_tablespace'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['check_tablespace'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['check_tablespace'][0]['description']       = PLUGIN_DB_CHECK_TABLESPACE_DESCRIPTION;
$plugin_field['check_tablespace'][0]['title']             = PLUGIN_DB_CHECK_TABLESPACE_TITLE;

$plugin_field['check_tablespace'][1]['type']              = 'text';
$plugin_field['check_tablespace'][1]['name']              = 'db_check_tablespace_identity';
$plugin_field['check_tablespace'][1]['value']             = '';
$plugin_field['check_tablespace'][1]['attributes']        = 'disabled readonly';
$plugin_field['check_tablespace'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['check_tablespace'][1]['num_rules']         = '1';
$plugin_field['check_tablespace'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['check_tablespace'][1]['rule_type'][0]      = 'required';
$plugin_field['check_tablespace'][1]['rule_attribute'][0] = '';

$plugin_field['check_tablespace'][2]['type']  = 'textarea';
$plugin_field['check_tablespace'][2]['name']  = 'db_check_tablespace_query';
$plugin_field['check_tablespace'][2]['value'] = 'SELECT df.tablespace_name TABLESPACE,'.
                                                'df.total_space_mb TOTAL_SPACE_MB,'.
                                                '(df.total_space_mb - fs.free_space_mb) USED_SPACE_MB,'.
                                                'fs.free_space_mb FREE_SPACE_MB,'.
                                                'ROUND(100 * (fs.free_space / df.total_space),2) PCT_FREE '.
                                                'FROM (SELECT tablespace_name, SUM(bytes) TOTAL_SPACE, '.
                                                'ROUND(SUM(bytes) / 1048576) TOTAL_SPACE_MB '.
                                                'FROM dba_data_files '.
                                                'GROUP BY tablespace_name) df, '.
                                                '(SELECT tablespace_name, SUM(bytes) FREE_SPACE, '.
                                                'ROUND(SUM(bytes) / 1048576) FREE_SPACE_MB '.
                                                'FROM dba_free_space '.
                                                'GROUP BY tablespace_name) fs '.
                                                'WHERE df.tablespace_name = fs.tablespace_name (+) '.
                                                'ORDER BY 5';

$plugin_field['check_tablespace'][2]['attributes']        = 'disabled rows=30 cols=70';
$plugin_field['check_tablespace'][2]['description']       = PLUGIN_QUERY;
$plugin_field['check_tablespace'][2]['num_rules']         = '1';
$plugin_field['check_tablespace'][2]['rule_msg'][0]       = PLUGIN_QUERY_RULE;
$plugin_field['check_tablespace'][2]['rule_type'][0]      = 'required';
$plugin_field['check_tablespace'][2]['rule_attribute'][0] = '';

$plugin_field['check_tablespace'][3]['type']        = 'select';
$plugin_field['check_tablespace'][3]['name']        = 'db_check_tablespace_attach_result';
$plugin_field['check_tablespace'][3]['value']       = array('html' => '.html',
                                                            'txt' => '.txt',
                                                            'csv' => '.csv');
$plugin_field['check_tablespace'][3]['attributes']  = '';
$plugin_field['check_tablespace'][3]['description'] = PLUGIN_ATTACH_RESULT;

$plugin_field['check_tablespace'][4]['type']              = 'text';
$plugin_field['check_tablespace'][4]['name']              = 'db_check_tablespace_file_name';
$plugin_field['check_tablespace'][4]['value']             = '';
$plugin_field['check_tablespace'][4]['attributes']        = 'disabled';
$plugin_field['check_tablespace'][4]['description']       = PLUGIN_FILE_NAME;
$plugin_field['check_tablespace'][4]['num_rules']         = '1';
$plugin_field['check_tablespace'][4]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['check_tablespace'][4]['rule_type'][0]      = 'required';
$plugin_field['check_tablespace'][4]['rule_attribute'][0] = '';

$plugin_field['check_tablespace'][5]['type']        = 'text';
$plugin_field['check_tablespace'][5]['name']        = 'db_check_tablespace_retention';
$plugin_field['check_tablespace'][5]['value']       = '';
$plugin_field['check_tablespace'][5]['attributes']  = 'disabled';
$plugin_field['check_tablespace'][5]['description'] = PLUGIN_RETENTION;
?>
