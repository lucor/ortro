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

$plugin_field['spreadsheet_to_db'][0]['version']           = '1.0.3';
$plugin_field['spreadsheet_to_db'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['spreadsheet_to_db'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['spreadsheet_to_db'][0]['description']       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_DESCRIPTION;
$plugin_field['spreadsheet_to_db'][0]['title']             = PLUGIN_GENERAL_SPREADSHEET_TO_DB_TITLE;

$plugin_field['spreadsheet_to_db'][1]['type']              = 'text';
$plugin_field['spreadsheet_to_db'][1]['name']              = 'db_spreadsheet_to_db_identity';
$plugin_field['spreadsheet_to_db'][1]['value']             = '';
$plugin_field['spreadsheet_to_db'][1]['attributes']        = 'disabled readonly';
$plugin_field['spreadsheet_to_db'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['spreadsheet_to_db'][1]['num_rules']         = '1';
$plugin_field['spreadsheet_to_db'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['spreadsheet_to_db'][1]['rule_type'][0]      = 'required';
$plugin_field['spreadsheet_to_db'][1]['rule_attribute'][0] = '';

$plugin_field['spreadsheet_to_db'][2]['type']              = 'text';
$plugin_field['spreadsheet_to_db'][2]['name']              = 'db_spreadsheet_to_db_table_name';
$plugin_field['spreadsheet_to_db'][2]['value']             = '';
$plugin_field['spreadsheet_to_db'][2]['attributes']        = 'disabled';
$plugin_field['spreadsheet_to_db'][2]['description']       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_TABLE_NAME_DESCRIPTION ;
$plugin_field['spreadsheet_to_db'][2]['num_rules']         = '1';
$plugin_field['spreadsheet_to_db'][2]['rule_msg'][0]       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_RULE_2_0;
$plugin_field['spreadsheet_to_db'][2]['rule_type'][0]      = 'required';
$plugin_field['spreadsheet_to_db'][2]['rule_attribute'][0] = '';

$plugin_field['spreadsheet_to_db'][3]['type']  = 'textarea';
$plugin_field['spreadsheet_to_db'][3]['name']  = 'db_spreadsheet_to_db_table_definition';
$plugin_field['spreadsheet_to_db'][3]['value'] = '<field>
												    <name>column_1</name>
												    <type>integer</type>
												    <default/>
												    <notnull>1</notnull>
												  </field>
												  <field>
												    <name>column_2</name>
												    <type>text</type>
												    <length>30</length>
												    <notnull>1</notnull>
												  </field>';

$plugin_field['spreadsheet_to_db'][3]['attributes']        = 'disabled rows=30 cols=80';
$plugin_field['spreadsheet_to_db'][3]['description']       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_TABLE_DEFINITION_DESCRIPTION ;
$plugin_field['spreadsheet_to_db'][3]['num_rules']         = '1';
$plugin_field['spreadsheet_to_db'][3]['rule_msg'][0]       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_RULE_3_0;
$plugin_field['spreadsheet_to_db'][3]['rule_type'][0]      = 'required';
$plugin_field['spreadsheet_to_db'][3]['rule_attribute'][0] = '';

$plugin_field['spreadsheet_to_db'][4]['type']              = 'text';
$plugin_field['spreadsheet_to_db'][4]['name']              = 'db_spreadsheet_to_db_spreadsheet_filename';
$plugin_field['spreadsheet_to_db'][4]['value']             = '';
$plugin_field['spreadsheet_to_db'][4]['attributes']        = 'disabled size=50';
$plugin_field['spreadsheet_to_db'][4]['description']       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_SPREADSHEET_PATH_DESCRIPTION;
$plugin_field['spreadsheet_to_db'][4]['num_rules']         = '1';
$plugin_field['spreadsheet_to_db'][4]['rule_msg'][0]       = PLUGIN_GENERAL_SPREADSHEET_TO_DB_RULE_4_0;
$plugin_field['spreadsheet_to_db'][4]['rule_type'][0]      = 'required';
$plugin_field['spreadsheet_to_db'][4]['rule_attribute'][0] = '';

$plugin_field['spreadsheet_to_db'][5]['type']        = 'select';
$plugin_field['spreadsheet_to_db'][5]['name']        = 'febo_spreadsheet_to_db_field_with_quote';
$plugin_field['spreadsheet_to_db'][5]['value']       = array('escape' => PLUGIN_GENERAL_SPREADSHEET_TO_DB_FIELD_WITH_QUOTE_VALUE_1, 
                                                             'remove' => PLUGIN_GENERAL_SPREADSHEET_TO_DB_FIELD_WITH_QUOTE_VALUE_2);
$plugin_field['spreadsheet_to_db'][5]['attributes']  = '';
$plugin_field['spreadsheet_to_db'][5]['description'] = PLUGIN_GENERAL_SPREADSHEET_TO_DB_FIELD_WITH_QUOTE_DESCRIPTION;
$plugin_field['spreadsheet_to_db'][5]['num_rules']   = '0';
?>