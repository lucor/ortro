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

$plugin_field['sms_ftp'][0]['version']           = '1.2.3';
$plugin_field['sms_ftp'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['sms_ftp'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['sms_ftp'][0]['title']             = PLUGIN_SMS_FTP_TITLE;
$plugin_field['sms_ftp'][0]['description']       = PLUGIN_SMS_FTP_DESCRIPTION;

$plugin_field['sms_ftp'][1]['type']        = 'text';
$plugin_field['sms_ftp'][1]['name']        = 'sms_ftp_dir';
$plugin_field['sms_ftp'][1]['value']       = '';
$plugin_field['sms_ftp'][1]['attributes']  = 'disabled size=50';
$plugin_field['sms_ftp'][1]['description'] = PLUGIN_SMS_FTP_REMOTE_DIR_DESCRIPTION;
$plugin_field['sms_ftp'][1]['description_detail'] = PLUGIN_SMS_FTP_REMOTE_DIR_DESCRIPTION_DETAIL;

$plugin_field['sms_ftp'][2]['type']              = 'text';
$plugin_field['sms_ftp'][2]['name']              = 'sms_ftp_identity';
$plugin_field['sms_ftp'][2]['value']             = '';
$plugin_field['sms_ftp'][2]['attributes']        = 'disabled readonly size=50';
$plugin_field['sms_ftp'][2]['description']       = PLUGIN_IDENTITY;
$plugin_field['sms_ftp'][2]['num_rules']         = '1';
$plugin_field['sms_ftp'][2]['rule_msg'][0]       = PLUGIN_IDENTITY_RULE;
$plugin_field['sms_ftp'][2]['rule_type'][0]      = 'required';
$plugin_field['sms_ftp'][2]['rule_attribute'][0] = '';

$plugin_field['sms_ftp'][3]['type']        = 'textarea';
$plugin_field['sms_ftp'][3]['name']        = 'sms_ftp_message';
$plugin_field['sms_ftp'][3]['value']       = '';
$plugin_field['sms_ftp'][3]['attributes']  = 'disabled rows=10 cols=70';
$plugin_field['sms_ftp'][3]['description'] = PLUGIN_SMS_FTP_MESSAGE_DESCRIPTION;

$plugin_field['sms_ftp'][4]['type']        = 'checkbox';
$plugin_field['sms_ftp'][4]['name']        = 'sms_ftp_attach_result';
$plugin_field['sms_ftp'][4]['value']       = '';
$plugin_field['sms_ftp'][4]['attributes']  = '';
$plugin_field['sms_ftp'][4]['description'] = PLUGIN_ATTACH_RESULT;

$plugin_field['sms_ftp'][5]['type']        = 'checkbox';
$plugin_field['sms_ftp'][5]['name']        = 'sms_ftp_attach_timestamp';
$plugin_field['sms_ftp'][5]['value']       = '';
$plugin_field['sms_ftp'][5]['attributes']  = '';
$plugin_field['sms_ftp'][5]['description'] = PLUGIN_ATTACH_DATE;
?>