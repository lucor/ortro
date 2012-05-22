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

$plugin_field['tibco_rvd'][0]['version']           = '1.2.2';
$plugin_field['tibco_rvd'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['tibco_rvd'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['tibco_rvd'][0]['title']             = PLUGIN_TIBCO_RVD_TITLE;
$plugin_field['tibco_rvd'][0]['description']       = PLUGIN_TIBCO_RVD_DESCRIPTION;

$plugin_field['tibco_rvd'][1]['type']        = 'text';
$plugin_field['tibco_rvd'][1]['name']        = 'tibco_rvd_service';
$plugin_field['tibco_rvd'][1]['value']       = '';
$plugin_field['tibco_rvd'][1]['attributes']  = 'disabled size=50';
$plugin_field['tibco_rvd'][1]['description'] = PLUGIN_TIBCO_RVD_SERVICE_DESCRIPTION;

$plugin_field['tibco_rvd'][2]['type']        = 'text';
$plugin_field['tibco_rvd'][2]['name']        = 'tibco_rvd_network';
$plugin_field['tibco_rvd'][2]['value']       = '';
$plugin_field['tibco_rvd'][2]['attributes']  = 'disabled size=50';
$plugin_field['tibco_rvd'][2]['description'] = PLUGIN_TIBCO_RVD_NETWORK_DESCRIPTION;

$plugin_field['tibco_rvd'][3]['type']        = 'text';
$plugin_field['tibco_rvd'][3]['name']        = 'tibco_rvd_daemon';
$plugin_field['tibco_rvd'][3]['value']       = '';
$plugin_field['tibco_rvd'][3]['attributes']  = 'disabled size=50';
$plugin_field['tibco_rvd'][3]['description'] = PLUGIN_TIBCO_RVD_DAEMON_DESCRIPTION;

$plugin_field['tibco_rvd'][4]['type']              = 'text';
$plugin_field['tibco_rvd'][4]['name']              = 'tibco_rvd_subject';
$plugin_field['tibco_rvd'][4]['value']             = '';
$plugin_field['tibco_rvd'][4]['attributes']        = 'disabled size=50';
$plugin_field['tibco_rvd'][4]['description']       = PLUGIN_TIBCO_RVD_SUBJECT_DESCRIPTION;
$plugin_field['tibco_rvd'][4]['num_rules']         = '1';
$plugin_field['tibco_rvd'][4]['rule_msg'][0]       = PLUGIN_TIBCO_RVD_RULE_4_0;
$plugin_field['tibco_rvd'][4]['rule_type'][0]      = 'required';
$plugin_field['tibco_rvd'][4]['rule_attribute'][0] = '';

$plugin_field['tibco_rvd'][5]['type']        = 'textarea';
$plugin_field['tibco_rvd'][5]['name']        = 'tibco_rvd_message';
$plugin_field['tibco_rvd'][5]['value']       = '';
$plugin_field['tibco_rvd'][5]['attributes']  = 'disabled rows=10 cols=70';
$plugin_field['tibco_rvd'][5]['description'] = PLUGIN_TIBCO_RVD_MESSAGE_DESCRIPTION;

$plugin_field['tibco_rvd'][6]['type']        = 'checkbox';
$plugin_field['tibco_rvd'][6]['name']        = 'tibco_rvd_attach_result';
$plugin_field['tibco_rvd'][6]['value']       = '';
$plugin_field['tibco_rvd'][6]['attributes']  = '';
$plugin_field['tibco_rvd'][6]['description'] = PLUGIN_ATTACH_RESULT;
?>