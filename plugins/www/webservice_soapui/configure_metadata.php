<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Configuration file, allows to generate dinamically the web form for the plugin configuration
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

/* Environment configuration */

$_plugin_name = 'webservice_soapui';
$conf_metadata[$_plugin_name]['description']              = PLUGIN_METADATA_CONFIGURATION; 

$conf_metadata[$_plugin_name]['soapui_path']['description'] = PLUGIN_WEBSERVICE_SOAPUI_METADATA_SOAPUI_PATH_DESCRIPTION;
$conf_metadata[$_plugin_name]['soapui_path']['type']        = 'text';
$conf_metadata[$_plugin_name]['soapui_path']['name']        = 'webservice_soapui-soapui_path';
$conf_metadata[$_plugin_name]['soapui_path']['value']       = '/opt/soapui/';
$conf_metadata[$_plugin_name]['soapui_path']['attributes']  = 'size=30';
?>