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

$_plugin_name = 'webservice_soapui';
$plugin_field[$_plugin_name][0]['version']           = '1.0.1';
$plugin_field[$_plugin_name][0]['min_ortro_version'] = '1.3.1';
$plugin_field[$_plugin_name][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field[$_plugin_name][0]['description']       = PLUGIN_WEBSERVICE_SOAPUI_DESCRIPTION;
$plugin_field[$_plugin_name][0]['title']             = PLUGIN_WEBSERVICE_SOAPUI_TITLE;

$plugin_field[$_plugin_name][1]['type']        = 'file';
$plugin_field[$_plugin_name][1]['required']    = true;//check for uploaded file only when adding it.
$plugin_field[$_plugin_name][1]['name']        = 'webservice_soapui_testcase';
$plugin_field[$_plugin_name][1]['value']       = '';
$plugin_field[$_plugin_name][1]['attributes']  = 'disabled size=70';
$plugin_field[$_plugin_name][1]['description'] = PLUGIN_WEBSERVICE_SOAPUI_TESTCASE_DESCRIPTION;
$plugin_field[$_plugin_name][1]['num_rules']   = '0';
?>