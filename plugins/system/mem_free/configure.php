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
 * @author   Francesco Acquista <f.acquista@gmail.com>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_actions['mem_free'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['mem_free'][0]['action']      = 'plugin';
$plugin_actions['mem_free'][0]['file']        = 'display_archive_results';
$plugin_actions['mem_free'][0]['image']       = 'archive.png';

$plugin_field['mem_free'][0]['version']           = '1.0.1';
$plugin_field['mem_free'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['mem_free'][0]['authors'][0]        = 'Francesco Acquista <f.acquista@gmail.com>';
$plugin_field['mem_free'][0]['description']       = PLUGIN_MEM_FREE_DESCRIPTION;
$plugin_field['mem_free'][0]['title']             = PLUGIN_MEM_FREE_TITLE;

$plugin_field['mem_free'][1]['type']              = 'text';
$plugin_field['mem_free'][1]['name']              = 'mem_free_user';
$plugin_field['mem_free'][1]['value']             = '';
$plugin_field['mem_free'][1]['attributes']        = 'disabled';
$plugin_field['mem_free'][1]['description']       = PLUGIN_USER;
$plugin_field['mem_free'][1]['num_rules']         = '1';
$plugin_field['mem_free'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['mem_free'][1]['rule_type'][0]      = 'required';
$plugin_field['mem_free'][1]['rule_attribute'][0] = '';

$plugin_field['mem_free'][2]['type']        = 'text';
$plugin_field['mem_free'][2]['name']        = 'mem_free_port';
$plugin_field['mem_free'][2]['value']       = '';
$plugin_field['mem_free'][2]['attributes']  = 'disabled';
$plugin_field['mem_free'][2]['description'] = PLUGIN_PORT;

$plugin_field['mem_free'][3]['type']              = 'text';
$plugin_field['mem_free'][3]['name']              = 'mem_free_threshold';
$plugin_field['mem_free'][3]['value']             = '';
$plugin_field['mem_free'][3]['attributes']        = 'disabled';
$plugin_field['mem_free'][3]['description']       = PLUGIN_MEM_FREE_THRESHOLD_DESCRIPTION;
$plugin_field['mem_free'][3]['num_rules']         = '1';
$plugin_field['mem_free'][3]['rule_msg'][0]       = PLUGIN_MEM_FREE_RULE_3_0;
$plugin_field['mem_free'][3]['rule_type'][0]      = 'required';
$plugin_field['mem_free'][3]['rule_attribute'][0] = '';

$plugin_field['mem_free'][4]['type']        = 'text';
$plugin_field['mem_free'][4]['name']        = 'mem_free_retention';
$plugin_field['mem_free'][4]['value']       = '';
$plugin_field['mem_free'][4]['attributes']  = 'disabled';
$plugin_field['mem_free'][4]['description'] = PLUGIN_RETENTION;
?>
