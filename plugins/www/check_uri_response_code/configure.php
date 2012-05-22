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

$plugin_field['check_uri_response_code'][0]['version']           = '1.0.4';
$plugin_field['check_uri_response_code'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['check_uri_response_code'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['check_uri_response_code'][0]['description']       = PLUGIN_CHECK_URI_RESPONSE_CODE_DESCRIPTION;
$plugin_field['check_uri_response_code'][0]['title']             = PLUGIN_CHECK_URI_RESPONSE_CODE_TITLE;

$plugin_field['check_uri_response_code'][1]['type']        = 'select';
$plugin_field['check_uri_response_code'][1]['name']        = 'check_uri_response_code_protocol';
$plugin_field['check_uri_response_code'][1]['value']       = array('http' => 'http', 'https' => 'https');
$plugin_field['check_uri_response_code'][1]['attributes']  = '';
$plugin_field['check_uri_response_code'][1]['description'] = PLUGIN_HTTP_PROTOCOL;
$plugin_field['check_uri_response_code'][1]['num_rules']   = '0';

$plugin_field['check_uri_response_code'][2]['type']        = 'text';
$plugin_field['check_uri_response_code'][2]['name']        = 'check_uri_response_code_port';
$plugin_field['check_uri_response_code'][2]['value']       = '';
$plugin_field['check_uri_response_code'][2]['attributes']  = 'disabled';
$plugin_field['check_uri_response_code'][2]['description'] = PLUGIN_HTTP_PORT;
$plugin_field['check_uri_response_code'][2]['num_rules']   = '0';

$plugin_field['check_uri_response_code'][3]['type']        = 'text';
$plugin_field['check_uri_response_code'][3]['name']        = 'check_uri_response_code_url';
$plugin_field['check_uri_response_code'][3]['value']       = '';
$plugin_field['check_uri_response_code'][3]['attributes']  = 'disabled';
$plugin_field['check_uri_response_code'][3]['description'] = PLUGIN_CHECK_URI_RESPONSE_CODE_URL_DESCRIPTION;
$plugin_field['check_uri_response_code'][3]['num_rules']   = '0';
?>