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
 * @author   Marcello Sessa <zodd81@users.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once 'services.php';

$plugin_actions['service_check'][0]['description']     = PLUGIN_METADATA_CONFIGURATION;
$plugin_actions['service_check'][0]['action']          = 'plugin';
$plugin_actions['service_check'][0]['file']            = 'display_archive_results';
$plugin_actions['service_check'][0]['image']           = 'archive.png';

$plugin_field['service_check'][0]['version']           = '1.2.4';
$plugin_field['service_check'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['service_check'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['service_check'][0]['authors'][1]        = 'Marcello Sessa <zodd81@users.sourceforge.net>';
$plugin_field['service_check'][0]['description']       = PLUGIN_SERVICE_CHECK_DESCRIPTION;
$plugin_field['service_check'][0]['title']             = PLUGIN_SERVICE_CHECK_TITLE;

$plugin_field['service_check'][1]['type']              = 'select';
$plugin_field['service_check'][1]['name']              = 'service_check_default_ports';
$plugin_field['service_check'][1]['value']             = $services;
$plugin_field['service_check'][1]['attributes']        = 'multiple';
$plugin_field['service_check'][1]['description']       = PLUGIN_SERVICE_CHECK_SERVICE_PORT_LIST_DESCRIPTION;

$plugin_field['service_check'][2]['type']              = 'text';
$plugin_field['service_check'][2]['name']              = 'service_check_custom_ports';
$plugin_field['service_check'][2]['value']             = '';
$plugin_field['service_check'][2]['attributes']        = 'disabled';
$plugin_field['service_check'][2]['description']       = PLUGIN_SERVICE_CHECK_SERVICE_PORT_DESCRIPTION;

$plugin_field['service_check'][3]['type']              = 'text';
$plugin_field['service_check'][3]['name']              = 'service_check_timeout';
$plugin_field['service_check'][3]['value']             = '';
$plugin_field['service_check'][3]['attributes']        = 'disabled';
$plugin_field['service_check'][3]['description']       = PLUGIN_SERVICE_TIMEOUT_DESCRIPTION;
$plugin_field['service_check'][3]['num_rules']         = '1';
$plugin_field['service_check'][3]['rule_msg'][0]       = PLUGIN_SERVICE_CHECK_RULE_3_0;
$plugin_field['service_check'][3]['rule_type'][0]      = 'numeric';
$plugin_field['service_check'][3]['rule_attribute'][0] = '';

$plugin_field['service_check'][4]['type']              = 'text';
$plugin_field['service_check'][4]['name']              = 'service_check_retention';
$plugin_field['service_check'][4]['value']             = '';
$plugin_field['service_check'][4]['attributes']        = 'disabled';
$plugin_field['service_check'][4]['description']       = PLUGIN_RETENTION;
?>
