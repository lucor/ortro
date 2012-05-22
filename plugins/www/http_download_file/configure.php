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

$plugin_field['http_download_file'][0]['version']           = '1.0.2';
$plugin_field['http_download_file'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['http_download_file'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['http_download_file'][0]['description']       = PLUGIN_HTTP_DOWNLOAD_FILE_DESCRIPTION;
$plugin_field['http_download_file'][0]['title']             = PLUGIN_HTTP_DOWNLOAD_FILE_TITLE;

$plugin_field['http_download_file'][1]['type']        = 'text';
$plugin_field['http_download_file'][1]['name']        = 'http_download_file_identity';
$plugin_field['http_download_file'][1]['value']       = '';
$plugin_field['http_download_file'][1]['attributes']  = 'disabled readonly size=30';
$plugin_field['http_download_file'][1]['description'] = PLUGIN_IDENTITY;
$plugin_field['http_download_file'][1]['num_rules']   = '0';

$plugin_field['http_download_file'][2]['type']        = 'select';
$plugin_field['http_download_file'][2]['name']        = 'http_download_file_protocol';
$plugin_field['http_download_file'][2]['value']       = array('http' => 'http', 'https' => 'https');
$plugin_field['http_download_file'][2]['attributes']  = '';
$plugin_field['http_download_file'][2]['description'] = PLUGIN_HTTP_PROTOCOL;
$plugin_field['http_download_file'][2]['num_rules']   = '0';

$plugin_field['http_download_file'][3]['type']        = 'text';
$plugin_field['http_download_file'][3]['name']        = 'http_download_file_port';
$plugin_field['http_download_file'][3]['value']       = '';
$plugin_field['http_download_file'][3]['attributes']  = 'disabled size=10';
$plugin_field['http_download_file'][3]['description'] = PLUGIN_HTTP_PORT;
$plugin_field['http_download_file'][3]['num_rules']   = '0';

$plugin_field['http_download_file'][4]['type']        = 'text';
$plugin_field['http_download_file'][4]['name']        = 'http_download_file_url';
$plugin_field['http_download_file'][4]['value']       = '';
$plugin_field['http_download_file'][4]['attributes']  = 'disabled size=50';
$plugin_field['http_download_file'][4]['description'] = PLUGIN_HTTP_DOWNLOAD_FILE_URL_DESCRIPTION;
$plugin_field['http_download_file'][4]['num_rules']   = '0';

$plugin_field['http_download_file'][5]['type']        = 'text';
$plugin_field['http_download_file'][5]['name']        = 'http_download_file_save_as';
$plugin_field['http_download_file'][5]['value']       = '';
$plugin_field['http_download_file'][5]['attributes']  = 'disabled size=50';
$plugin_field['http_download_file'][5]['description'] = PLUGIN_HTTP_DOWNLOAD_FILE_SAVE_AS_DESCRIPTION;
$plugin_field['http_download_file'][5]['num_rules']   = '0';
?>