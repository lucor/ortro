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

$plugin_field['windows_remote_execution'][0]['version']           = '1.0.1';
$plugin_field['windows_remote_execution'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['windows_remote_execution'][0]['authors'][0]        = 'Danilo Alfano<ph4ntom@users.sourceforge.net>';
$plugin_field['windows_remote_execution'][0]['description']       = PLUGIN_WINDOWS_REMOTE_EXECUTION_DESCRIPTION;
$plugin_field['windows_remote_execution'][0]['title']             = PLUGIN_WINDOWS_REMOTE_EXECUTION_TITLE;

$plugin_field['windows_remote_execution'][1]['type']              = 'text';
$plugin_field['windows_remote_execution'][1]['name']              = 'windows_remote_execution_identity';
$plugin_field['windows_remote_execution'][1]['value']             = '';
$plugin_field['windows_remote_execution'][1]['attributes']        = 'disabled readonly';
$plugin_field['windows_remote_execution'][1]['description']       = PLUGIN_IDENTITY;
$plugin_field['windows_remote_execution'][1]['num_rules']         = '1';
$plugin_field['windows_remote_execution'][1]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['windows_remote_execution'][1]['rule_type'][0]      = 'required';
$plugin_field['windows_remote_execution'][1]['rule_attribute'][0] = '';

$plugin_field['windows_remote_execution'][2]['type']        = 'text';
$plugin_field['windows_remote_execution'][2]['name']        = 'windows_remote_execution_domain';
$plugin_field['windows_remote_execution'][2]['value']       = '';
$plugin_field['windows_remote_execution'][2]['attributes']  = 'disabled';
$plugin_field['windows_remote_execution'][2]['description'] = PLUGIN_WINDOWS_REMOTE_EXECUTION_DOMAIN_DESCRIPTION;
$plugin_field['windows_remote_execution'][2]['num_rules']   = '0';

$plugin_field['windows_remote_execution'][3]['type']              = 'text';
$plugin_field['windows_remote_execution'][3]['name']              = 'windows_remote_execution_command';
$plugin_field['windows_remote_execution'][3]['value']             = '';
$plugin_field['windows_remote_execution'][3]['attributes']        = 'disabled';
$plugin_field['windows_remote_execution'][3]['description']       = PLUGIN_WINDOWS_REMOTE_EXECUTION_COMMAND_DESCRIPTION;
$plugin_field['windows_remote_execution'][3]['num_rules']         = '1';
$plugin_field['windows_remote_execution'][3]['rule_msg'][0]       = PLUGIN_WINDOWS_REMOTE_EXECUTION_RULE_2_0;
$plugin_field['windows_remote_execution'][3]['rule_type'][0]      = 'required';
$plugin_field['windows_remote_execution'][3]['rule_attribute'][0] = '';
?>