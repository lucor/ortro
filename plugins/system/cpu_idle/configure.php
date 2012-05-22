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
 * @author   Fabrizio Cardarello <hunternet@users.sourceforge.net>
 * @author   Francesco Acquista <f.acquista@gmail.com>
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_actions['cpu_idle'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['cpu_idle'][0]['action']      = 'plugin';
$plugin_actions['cpu_idle'][0]['file']        = 'display_archive_results';
$plugin_actions['cpu_idle'][0]['image']       = 'archive.png';

$plugin_field['cpu_idle'][0]['version']           = '1.0.4';
$plugin_field['cpu_idle'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['cpu_idle'][0]['authors'][0]        = 'Fabrizio Cardarello <hunternet@users.sourceforge.net>';
$plugin_field['cpu_idle'][0]['authors'][1]        = 'Francesco Acquista <f.acquista@gmail.com>';
$plugin_field['cpu_idle'][0]['description']       = PLUGIN_CPU_IDLE_DESCRIPTION;
$plugin_field['cpu_idle'][0]['title']             = PLUGIN_CPU_IDLE_TITLE;

$plugin_field['cpu_idle'][1]['type']              = 'text';
$plugin_field['cpu_idle'][1]['name']              = 'cpu_idle_user';
$plugin_field['cpu_idle'][1]['value']             = '';
$plugin_field['cpu_idle'][1]['attributes']        = 'disabled';
$plugin_field['cpu_idle'][1]['description']       = PLUGIN_USER;
$plugin_field['cpu_idle'][1]['num_rules']         = '1';
$plugin_field['cpu_idle'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['cpu_idle'][1]['rule_type'][0]      = 'required';
$plugin_field['cpu_idle'][1]['rule_attribute'][0] = '';

$plugin_field['cpu_idle'][2]['type']        = 'text';
$plugin_field['cpu_idle'][2]['name']        = 'cpu_idle_port';
$plugin_field['cpu_idle'][2]['value']       = '';
$plugin_field['cpu_idle'][2]['attributes']  = 'disabled';
$plugin_field['cpu_idle'][2]['description'] = PLUGIN_PORT;

$plugin_field['cpu_idle'][3]['type']              = 'text';
$plugin_field['cpu_idle'][3]['name']              = 'cpu_idle_threshold';
$plugin_field['cpu_idle'][3]['value']             = '';
$plugin_field['cpu_idle'][3]['attributes']        = 'disabled';
$plugin_field['cpu_idle'][3]['description']       = PLUGIN_CPU_IDLE_THRESHOLD_DESCRIPTION;
$plugin_field['cpu_idle'][3]['num_rules']         = '1';
$plugin_field['cpu_idle'][3]['rule_msg'][0]       = PLUGIN_CPU_IDLE_RULE_3_0;
$plugin_field['cpu_idle'][3]['rule_type'][0]      = 'required';
$plugin_field['cpu_idle'][3]['rule_attribute'][0] = '';

$plugin_field['cpu_idle'][4]['type']        = 'text';
$plugin_field['cpu_idle'][4]['name']        = 'cpu_idle_retention';
$plugin_field['cpu_idle'][4]['value']       = '';
$plugin_field['cpu_idle'][4]['attributes']  = 'disabled';
$plugin_field['cpu_idle'][4]['description'] = PLUGIN_RETENTION;
?>
