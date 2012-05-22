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

$plugin_actions['custom_script'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['custom_script'][0]['action']      = 'plugin';
$plugin_actions['custom_script'][0]['file']        = 'display_archive_results';
$plugin_actions['custom_script'][0]['image']       = 'archive.png'; 

$plugin_field['custom_script'][0]['version']           = '1.2.3';
$plugin_field['custom_script'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['custom_script'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['custom_script'][0]['description']       = PLUGIN_GENERAL_CUSTOM_SCRIPT_DESCRIPTION;
$plugin_field['custom_script'][0]['title']             = PLUGIN_GENERAL_CUSTOM_SCRIPT_TITLE;

$plugin_field['custom_script'][1]['type']        = 'select';
$plugin_field['custom_script'][1]['name']        = 'custom_script_location';
$plugin_field['custom_script'][1]['value']       = array('remote' => PLUGIN_GENERAL_CUSTOM_SCRIPT_SCRIPT_LOCATION_REMOTE,
                                                         'local' => PLUGIN_GENERAL_CUSTOM_SCRIPT_SCRIPT_LOCATION_LOCAL);
$plugin_field['custom_script'][1]['attributes']  = '';
$plugin_field['custom_script'][1]['description'] = PLUGIN_GENERAL_CUSTOM_SCRIPT_SCRIPT_LOCATION_DESCRIPTION;

$plugin_field['custom_script'][2]['type']              = 'text';
$plugin_field['custom_script'][2]['name']              = 'custom_script_path';
$plugin_field['custom_script'][2]['value']             = '';
$plugin_field['custom_script'][2]['attributes']        = 'disabled size=70';
$plugin_field['custom_script'][2]['description']       = PLUGIN_GENERAL_CUSTOM_SCRIPT_PATH_DESCRIPTION;
$plugin_field['custom_script'][2]['num_rules']         = '1';
$plugin_field['custom_script'][2]['rule_msg'][0]       = PLUGIN_GENERAL_CUSTOM_SCRIPT_RULE_1_0;
$plugin_field['custom_script'][2]['rule_type'][0]      = 'required';
$plugin_field['custom_script'][2]['rule_attribute'][0] = '';

$plugin_field['custom_script'][3]['type']              = 'text';
$plugin_field['custom_script'][3]['name']              = 'custom_script_user';
$plugin_field['custom_script'][3]['value']             = '';
$plugin_field['custom_script'][3]['attributes']        = 'disabled';
$plugin_field['custom_script'][3]['description']       = PLUGIN_USER;
$plugin_field['custom_script'][3]['num_rules']         = '1';
$plugin_field['custom_script'][3]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['custom_script'][3]['rule_type'][0]      = 'required';
$plugin_field['custom_script'][3]['rule_attribute'][0] = '';

$plugin_field['custom_script'][4]['type']        = 'text';
$plugin_field['custom_script'][4]['name']        = 'custom_script_port';
$plugin_field['custom_script'][4]['value']       = '';
$plugin_field['custom_script'][4]['attributes']  = 'disabled';
$plugin_field['custom_script'][4]['description'] = PLUGIN_PORT;

$plugin_field['custom_script'][5]['type']        = 'text';
$plugin_field['custom_script'][5]['name']        = 'custom_script_retention';
$plugin_field['custom_script'][5]['value']       = '';
$plugin_field['custom_script'][5]['attributes']  = 'disabled';
$plugin_field['custom_script'][5]['description'] = PLUGIN_RETENTION;
?>
