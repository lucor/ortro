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
 * @author   Michael Mueller <mmuell24@csc.com>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_actions['mysql_table_check'][0]['description'] = PLUGIN_METADATA_CONFIGURATION;
$plugin_actions['mysql_table_check'][0]['action']      = 'plugin';
$plugin_actions['mysql_table_check'][0]['file']        = 'display_archive_results';
$plugin_actions['mysql_table_check'][0]['image']       = 'archive.png';

$plugin_field['mysql_table_check'][0]['version']           = '1.0.0';
$plugin_field['mysql_table_check'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['mysql_table_check'][0]['authors'][0]        = 'Michael Mueller <mmuell24@csc.com>';
$plugin_field['mysql_table_check'][0]['description']       = PLUGIN_DB_MYSQL_CHECK_TABLES_DESCRIPTION;
$plugin_field['mysql_table_check'][0]['title']             = PLUGIN_DB_MYSQL_CHECK_TABLES_TITLE;

$plugin_field['mysql_table_check'][1]['type']              = 'text';
$plugin_field['mysql_table_check'][1]['name']              = 'db_mysql_table_check_identity';
$plugin_field['mysql_table_check'][1]['value']             = '';
$plugin_field['mysql_table_check'][1]['attributes']        = 'disabled readonly';
$plugin_field['mysql_table_check'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['mysql_table_check'][1]['num_rules']         = '1';
$plugin_field['mysql_table_check'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['mysql_table_check'][1]['rule_type'][0]      = 'required';
$plugin_field['mysql_table_check'][1]['rule_attribute'][0] = '';

$plugin_field['mysql_table_check'][2]['type']        = 'text';
$plugin_field['mysql_table_check'][2]['name']        = 'db_mysql_table_check_retention';
$plugin_field['mysql_table_check'][2]['value']       = '';
$plugin_field['mysql_table_check'][2]['attributes']  = 'disabled';
$plugin_field['mysql_table_check'][2]['description'] = PLUGIN_RETENTION;
?>