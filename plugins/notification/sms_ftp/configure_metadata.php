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

$conf_metadata['sms_ftp']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['sms_ftp']['host']['description'] = PLUGIN_SMS_FTP_METADATA_SERVER_DESCRIPTION;
$conf_metadata['sms_ftp']['host']['type']        = 'text';
$conf_metadata['sms_ftp']['host']['name']        = 'sms_ftp-host';
$conf_metadata['sms_ftp']['host']['value']       = 'localhost';
$conf_metadata['sms_ftp']['host']['attributes']  = 'size=30';

$conf_metadata['sms_ftp']['port']['description'] = PLUGIN_SMS_FTP_METADATA_PORT_DESCRIPTION;
$conf_metadata['sms_ftp']['port']['type']        = 'text';
$conf_metadata['sms_ftp']['port']['name']        = 'sms_ftp-port';
$conf_metadata['sms_ftp']['port']['value']       = '';
$conf_metadata['sms_ftp']['port']['attributes']  = 'size=5';

$conf_metadata['sms_ftp']['file_prefix']['description'] = PLUGIN_SMS_FTP_METADATA_FILE_PREFIX_DESCRIPTION;
$conf_metadata['sms_ftp']['file_prefix']['type']        = 'text';
$conf_metadata['sms_ftp']['file_prefix']['name']        = 'sms_ftp-file_prefix';
$conf_metadata['sms_ftp']['file_prefix']['value']       = 'ortro_';
$conf_metadata['sms_ftp']['file_prefix']['attributes']  = 'size=20';
?>