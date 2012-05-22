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
 * @patch    lucke
 */

$plugin_field['ftp_upload'][0]['version']           = '1.0.3';
$plugin_field['ftp_upload'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['ftp_upload'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['ftp_upload'][0]['description']       = PLUGIN_FILE_FTP_UPLOAD_DESCRIPTION;
$plugin_field['ftp_upload'][0]['title']             = PLUGIN_FILE_FTP_UPLOAD_TITLE;

$plugin_field['ftp_upload'][1]['type']        = 'text';
$plugin_field['ftp_upload'][1]['name']        = 'file_ftp_upload_identity';
$plugin_field['ftp_upload'][1]['value']       = '';
$plugin_field['ftp_upload'][1]['attributes']  = 'disabled readonly size=30';
$plugin_field['ftp_upload'][1]['description'] = PLUGIN_FILE_FTP_UPLOAD_IDENTITY_DESCRIPTION;

$plugin_field['ftp_upload'][2]['type']        = 'text';
$plugin_field['ftp_upload'][2]['name']        = 'file_ftp_upload_local_dir';
$plugin_field['ftp_upload'][2]['value']       = '';
$plugin_field['ftp_upload'][2]['attributes']  = 'disabled size=50';
$plugin_field['ftp_upload'][2]['description'] = PLUGIN_FILE_FTP_UPLOAD_LOCAL_DIR_DESCRIPTION;

$plugin_field['ftp_upload'][3]['type']              = 'text';
$plugin_field['ftp_upload'][3]['name']              = 'file_ftp_upload_filename';
$plugin_field['ftp_upload'][3]['value']             = '';
$plugin_field['ftp_upload'][3]['attributes']        = 'disabled size=50';
$plugin_field['ftp_upload'][3]['description']       = PLUGIN_FILE_NAME;
$plugin_field['ftp_upload'][3]['num_rules']         = '1';
$plugin_field['ftp_upload'][3]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['ftp_upload'][3]['rule_type'][0]      = 'required';
$plugin_field['ftp_upload'][3]['rule_attribute'][0] = '';

$plugin_field['ftp_upload'][4]['type']        = 'text';
$plugin_field['ftp_upload'][4]['name']        = 'file_ftp_upload_remote_dir';
$plugin_field['ftp_upload'][4]['value']       = '';
$plugin_field['ftp_upload'][4]['attributes']  = 'disabled size=50';
$plugin_field['ftp_upload'][4]['description'] = PLUGIN_FILE_FTP_UPLOAD_REMOTE_DIR_DESCRIPTION;

$plugin_field['ftp_upload'][5]['type']        = 'select';
$plugin_field['ftp_upload'][5]['name']        = 'file_ftp_upload_transfer_mode';
$plugin_field['ftp_upload'][5]['value']       = array('ascii' => PLUGIN_FILE_FTP_UPLOAD_TRANSFER_MODE_VALUE_0, 
                                                      'binary' => PLUGIN_FILE_FTP_UPLOAD_TRANSFER_MODE_VALUE_1);
$plugin_field['ftp_upload'][5]['attributes']  = '';
$plugin_field['ftp_upload'][5]['description'] = PLUGIN_FILE_FTP_UPLOAD_TRANSFER_MODE_DESCRIPTION;

$plugin_field['ftp_upload'][6]['type']        = 'text';
$plugin_field['ftp_upload'][6]['name']        = 'file_ftp_upload_port';
$plugin_field['ftp_upload'][6]['value']       = '';
$plugin_field['ftp_upload'][6]['attributes']  = 'disabled size=6';
$plugin_field['ftp_upload'][6]['description'] = PLUGIN_FILE_FTP_UPLOAD_PORT_DESCRIPTION;

$plugin_field['ftp_upload'][7]['type']        = 'select';
$plugin_field['ftp_upload'][7]['name']        = 'file_ftp_upload_transfer_way';
$plugin_field['ftp_upload'][7]['value']       = array('put' => PLUGIN_FILE_FTP_UPLOAD_TRANSFER_WAY_VALUE_0, 
                                                      'get' => PLUGIN_FILE_FTP_UPLOAD_TRANSFER_WAY_VALUE_1);
$plugin_field['ftp_upload'][7]['attributes']  = '';
$plugin_field['ftp_upload'][7]['description'] = PLUGIN_FILE_FTP_UPLOAD_TRANSFER_WAY_DESCRIPTION;
?>
