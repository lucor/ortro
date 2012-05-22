<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Configuration file.
 * Allows to generate dinamically the web form for the plugin configuration
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

$conf_metadata['windows_remote_execution']['description'] = PLUGIN_METADATA_CONFIGURATION;
 
$conf_metadata['windows_remote_execution']['winexe_path']['description']       = PLUGIN_WINDOWS_REMOTE_EXECUTION_METADATA_WINEXE_PATH_DESCRIPTION;
$conf_metadata['windows_remote_execution']['winexe_path']['type']              = 'text';
$conf_metadata['windows_remote_execution']['winexe_path']['name']              = 'windows_remote_execution-winexe_path';
$conf_metadata['windows_remote_execution']['winexe_path']['value']             = '/usr/bin/winexe';
$conf_metadata['windows_remote_execution']['winexe_path']['attributes']        = 'size=30';
$conf_metadata['windows_remote_execution']['winexe_path']['num_rules']         = '1';
$conf_metadata['windows_remote_execution']['winexe_path']['rule_msg'][0]       = PLUGIN_WINDOWS_REMOTE_EXECUTION_METADATA_RULE_1_0;
$conf_metadata['windows_remote_execution']['winexe_path']['rule_type'][0]      = 'required';
$conf_metadata['windows_remote_execution']['winexe_path']['rule_attribute'][0] = '';
?>
