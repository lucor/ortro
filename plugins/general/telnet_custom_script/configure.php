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
 * @author   Danilo Alfano <ph4ntom@users.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_actions['telnet_custom_script'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['telnet_custom_script'][0]['action']      = 'plugin';
$plugin_actions['telnet_custom_script'][0]['file']        = 'display_archive_results';
$plugin_actions['telnet_custom_script'][0]['image']       = 'archive.png';

$plugin_field['telnet_custom_script'][0]['version']           = '1.0.0';
$plugin_field['telnet_custom_script'][0]['min_ortro_version'] = '1.2.0';

$plugin_field['telnet_custom_script'][0]['authors'][0]  = 'Danilo Alfano <ph4ntom@users.sourceforge.net>';
$plugin_field['telnet_custom_script'][0]['description'] = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_DESCRIPTION;
$plugin_field['telnet_custom_script'][0]['title']       = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_TITLE;

$plugin_field['telnet_custom_script'][1]['type']              = 'text';
$plugin_field['telnet_custom_script'][1]['name']              = 'telnet_custom_script_script';
$plugin_field['telnet_custom_script'][1]['value']             = '';
$plugin_field['telnet_custom_script'][1]['attributes']        = 'disabled size=70';
$plugin_field['telnet_custom_script'][1]['description']       = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_PATH_COMMAND;
$plugin_field['telnet_custom_script'][1]['num_rules']         = '1';
$plugin_field['telnet_custom_script'][1]['rule_msg'][0]       = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_RULE_1_0;
$plugin_field['telnet_custom_script'][1]['rule_type'][0]      = 'required';
$plugin_field['telnet_custom_script'][1]['rule_attribute'][0] = '';

$plugin_field['telnet_custom_script'][2]['type']              = 'text';
$plugin_field['telnet_custom_script'][2]['name']              = 'db_telnet_custom_script_identity';
$plugin_field['telnet_custom_script'][2]['value']             = '';
$plugin_field['telnet_custom_script'][2]['attributes']        = 'disabled readonly';
$plugin_field['telnet_custom_script'][2]['description']       = PLUGIN_IDENTITY;
$plugin_field['telnet_custom_script'][2]['num_rules']         = '1';
$plugin_field['telnet_custom_script'][2]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['telnet_custom_script'][2]['rule_type'][0]      = 'required';
$plugin_field['telnet_custom_script'][2]['rule_attribute'][0] = '';

$plugin_field['telnet_custom_script'][3]['type']        = 'text';
$plugin_field['telnet_custom_script'][3]['name']        = 'telnet_custom_script_port';
$plugin_field['telnet_custom_script'][3]['value']       = '';
$plugin_field['telnet_custom_script'][3]['attributes']  = 'disabled';
$plugin_field['telnet_custom_script'][3]['description'] = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_PORT;

$plugin_field['telnet_custom_script'][4]['type']        = 'select';
$plugin_field['telnet_custom_script'][4]['name']        = 'telnet_custom_script_operator';
$plugin_field['telnet_custom_script'][4]['value']       = array('>' => '>', '<' => '<', '=' => '=', '!=' => '!=');
$plugin_field['telnet_custom_script'][4]['attributes']  = '';
$plugin_field['telnet_custom_script'][4]['description'] = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_OPERATOR_DESCRIPTION;
$plugin_field['telnet_custom_script'][4]['num_rules']   = '0';

$plugin_field['telnet_custom_script'][5]['type']              = 'text';
$plugin_field['telnet_custom_script'][5]['name']              = 'telnet_custom_script_threshold';
$plugin_field['telnet_custom_script'][5]['value']             = '';
$plugin_field['telnet_custom_script'][5]['attributes']        = 'disabled';
$plugin_field['telnet_custom_script'][5]['description']       = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_THRESHOLD_DESCRIPTION;
$plugin_field['telnet_custom_script'][5]['num_rules']         = '0';

$plugin_field['telnet_custom_script'][6]['type']        = 'select';
$plugin_field['telnet_custom_script'][6]['name']        = 'telnet_custom_script_is_error';
$plugin_field['telnet_custom_script'][6]['value']       = array('0' => PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_SUCCESS,
                                                                 '1' => PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_ERROR);
$plugin_field['telnet_custom_script'][6]['attributes']  = '';
$plugin_field['telnet_custom_script'][6]['description'] = PLUGIN_GENERAL_TELNET_CUSTOM_SCRIPT_ERROR_DESCRIPTION;
$plugin_field['telnet_custom_script'][6]['num_rules']   = '0';

$plugin_field['telnet_custom_script'][7]['type']        = 'text';
$plugin_field['telnet_custom_script'][7]['name']        = 'check_telnet_retention';
$plugin_field['telnet_custom_script'][7]['value']       = '';
$plugin_field['telnet_custom_script'][7]['attributes']  = 'disabled';
$plugin_field['telnet_custom_script'][7]['description'] = PLUGIN_RETENTION;
?>
