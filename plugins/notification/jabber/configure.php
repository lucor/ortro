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

$plugin_field['jabber'][0]['version']           = '1.2.3';
$plugin_field['jabber'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['jabber'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['jabber'][0]['authors'][1]        = 'David Black <dblackia@users.sourceforge.net>';
$plugin_field['jabber'][0]['title']             = PLUGIN_JABBER_TITLE;
$plugin_field['jabber'][0]['description']       = PLUGIN_JABBER_DESCRIPTION;

$plugin_field['jabber'][1]['type']        = 'text';
$plugin_field['jabber'][1]['name']        = 'jabber_to';
$plugin_field['jabber'][1]['value']       = '';
$plugin_field['jabber'][1]['attributes']  = 'disabled size=70';
$plugin_field['jabber'][1]['description'] = PLUGIN_JABBER_TO_DESCRIPTION;
$plugin_field['jabber'][1]['description_detail'] = PLUGIN_JABBER_TO_DESCRIPTION_DETAIL;

$plugin_field['jabber'][2]['type']        = 'textarea';
$plugin_field['jabber'][2]['name']        = 'jabber_message';
$plugin_field['jabber'][2]['value']       = '';
$plugin_field['jabber'][2]['attributes']  = 'disabled rows=10 cols=50';
$plugin_field['jabber'][2]['description'] = PLUGIN_JABBER_MESSAGE_DESCRIPTION;

$plugin_field['jabber'][3]['type']        = 'checkbox';
$plugin_field['jabber'][3]['name']        = 'jabber_attach_result';
$plugin_field['jabber'][3]['value']       = '';
$plugin_field['jabber'][3]['attributes']  = '';
$plugin_field['jabber'][3]['description'] = PLUGIN_ATTACH_RESULT;
?>