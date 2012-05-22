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

$plugin_field['tivoli_postemsg'][0]['version']           = '1.2.2';
$plugin_field['tivoli_postemsg'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['tivoli_postemsg'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['tivoli_postemsg'][0]['title']             = PLUGIN_TIVOLI_POSTEMSG_TITLE;
$plugin_field['tivoli_postemsg'][0]['description']       = PLUGIN_TIVOLI_POSTEMSG_DESCRIPTION;

$plugin_field['tivoli_postemsg'][1]['type']        = 'select';
$plugin_field['tivoli_postemsg'][1]['name']        = 'tivoli_postemsg_severity';
$plugin_field['tivoli_postemsg'][1]['value']       = array('CRITICAL' => 'CRITICAL', 
                                                           'WARNING' => 'WARNING', 
                                                           'HARMLESS' => 'HARMLESS');
$plugin_field['tivoli_postemsg'][1]['attributes']  = '';
$plugin_field['tivoli_postemsg'][1]['description'] = PLUGIN_TIVOLI_POSTEMSG_SEVERITY_DESCRIPTION;

$plugin_field['tivoli_postemsg'][2]['type']        = 'textarea';
$plugin_field['tivoli_postemsg'][2]['name']        = 'tivoli_postemsg_message';
$plugin_field['tivoli_postemsg'][2]['value']       = '';
$plugin_field['tivoli_postemsg'][2]['attributes']  = 'disabled rows=10 cols=70';
$plugin_field['tivoli_postemsg'][2]['description'] = PLUGIN_TIVOLI_POSTEMSG_MESSAGE_DESCRIPTION;

$plugin_field['tivoli_postemsg'][3]['type']        = 'textarea';
$plugin_field['tivoli_postemsg'][3]['name']        = 'tivoli_postemsg_attribute';
$plugin_field['tivoli_postemsg'][3]['value']       = '';
$plugin_field['tivoli_postemsg'][3]['attributes']  = 'disabled rows=10 cols=70';
$plugin_field['tivoli_postemsg'][3]['description'] = PLUGIN_TIVOLI_POSTEMSG_ATTRIBUTE_DESCRIPTION;

$plugin_field['tivoli_postemsg'][4]['type']        = 'text';
$plugin_field['tivoli_postemsg'][4]['name']        = 'tivoli_postemsg_class';
$plugin_field['tivoli_postemsg'][4]['value']       = '';
$plugin_field['tivoli_postemsg'][4]['attributes']  = 'disabled size=50';
$plugin_field['tivoli_postemsg'][4]['description'] = PLUGIN_TIVOLI_POSTEMSG_CLASS_DESCRIPTION;

$plugin_field['tivoli_postemsg'][5]['type']        = 'text';
$plugin_field['tivoli_postemsg'][5]['name']        = 'tivoli_postemsg_source';
$plugin_field['tivoli_postemsg'][5]['value']       = '';
$plugin_field['tivoli_postemsg'][5]['attributes']  = 'disabled size=50';
$plugin_field['tivoli_postemsg'][5]['description'] = PLUGIN_TIVOLI_POSTEMSG_SOURCE_DESCRIPTION;

$plugin_field['tivoli_postemsg'][6]['type']        = 'checkbox';
$plugin_field['tivoli_postemsg'][6]['name']        = 'tivoli_postemsg_attach_result';
$plugin_field['tivoli_postemsg'][6]['value']       = '';
$plugin_field['tivoli_postemsg'][6]['attributes']  = '';
$plugin_field['tivoli_postemsg'][6]['description'] = PLUGIN_ATTACH_RESULT;
?>