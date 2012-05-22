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

$_plugin_name = 'execute_sql_from_file';
$plugin_field[$_plugin_name][0]['version']           = '1.0.1';
$plugin_field[$_plugin_name][0]['min_ortro_version'] = '1.3.1';
$plugin_field[$_plugin_name][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field[$_plugin_name][0]['description']       = PLUGIN_DB_EXECUTE_SQL_FROM_FILE_DESCRIPTION;
$plugin_field[$_plugin_name][0]['title']             = PLUGIN_DB_EXECUTE_SQL_FROM_FILE_TITLE;

$plugin_field[$_plugin_name][1]['type']              = 'text';
$plugin_field[$_plugin_name][1]['name']              = 'db_execute_sql_from_file_identity';
$plugin_field[$_plugin_name][1]['value']             = '';
$plugin_field[$_plugin_name][1]['attributes']        = 'disabled readonly';
$plugin_field[$_plugin_name][1]['description']       = PLUGIN_IDENTITY;
$plugin_field[$_plugin_name][1]['num_rules']         = '1';
$plugin_field[$_plugin_name][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field[$_plugin_name][1]['rule_type'][0]      = 'required';
$plugin_field[$_plugin_name][1]['rule_attribute'][0] = '';

$plugin_field[$_plugin_name][2]['type']              = 'file';
$plugin_field[$_plugin_name][2]['required']          = true;//check for uploaded file only when adding it.
$plugin_field[$_plugin_name][2]['name']              = 'db_execute_sql_from_file_query';
$plugin_field[$_plugin_name][2]['value']             = '';
$plugin_field[$_plugin_name][2]['attributes']        = 'disabled size=70';
$plugin_field[$_plugin_name][2]['description']       = PLUGIN_DB_EXECUTE_SQL_FROM_FILE ;
$plugin_field[$_plugin_name][2]['num_rules']         = '0';
?>