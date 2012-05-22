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

$conf_metadata['tivoli_postemsg']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['tivoli_postemsg']['path']['description'] = PLUGIN_TIVOLI_POSTEMSG_METADATA_TIVOLI_PATH_DESCRIPTION;
$conf_metadata['tivoli_postemsg']['path']['type']        = 'text';
$conf_metadata['tivoli_postemsg']['path']['name']        = 'tivoli_postemsg-path';
$conf_metadata['tivoli_postemsg']['path']['value']       = '/opt/Tivoli/lcf/bin/postemsg';
$conf_metadata['tivoli_postemsg']['path']['attributes']  = 'size=30';

$conf_metadata['tivoli_postemsg']['host']['description'] = PLUGIN_TIVOLI_POSTEMSG_METADATA_SERVER_DESCRIPTION;
$conf_metadata['tivoli_postemsg']['host']['type']        = 'text';
$conf_metadata['tivoli_postemsg']['host']['name']        = 'tivoli_postemsg-host';
$conf_metadata['tivoli_postemsg']['host']['value']       = 'localhost';
$conf_metadata['tivoli_postemsg']['host']['attributes']  = 'size=30';
?>