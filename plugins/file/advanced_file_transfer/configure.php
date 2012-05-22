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

$plugin_actions['advanced_file_transfer'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['advanced_file_transfer'][0]['action']      = 'plugin';
$plugin_actions['advanced_file_transfer'][0]['file']        = 'display_archive_results';
$plugin_actions['advanced_file_transfer'][0]['image']       = 'archive.png';

$plugin_field['advanced_file_transfer'][0]['version']           = '1.0.0';
$plugin_field['advanced_file_transfer'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['advanced_file_transfer'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['advanced_file_transfer'][0]['description']       = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_DESCRIPTION;
$plugin_field['advanced_file_transfer'][0]['title']             = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_TITLE;

$plugin_field['advanced_file_transfer'][1]['type']        = 'text';
$plugin_field['advanced_file_transfer'][1]['name']        = 'file_advanced_file_transfer_src_user';
$plugin_field['advanced_file_transfer'][1]['value']       = '';
$plugin_field['advanced_file_transfer'][1]['attributes']  = 'disabled size=30';
$plugin_field['advanced_file_transfer'][1]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_SRC_USER_DESCRIPTION;
$plugin_field['advanced_file_transfer'][1]['num_rules']         = '1';
$plugin_field['advanced_file_transfer'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['advanced_file_transfer'][1]['rule_type'][0]      = 'required';
$plugin_field['advanced_file_transfer'][1]['rule_attribute'][0] = '';

$plugin_field['advanced_file_transfer'][2]['type']              = 'text';
$plugin_field['advanced_file_transfer'][2]['name']              = 'file_advanced_file_transfer_src_filename';
$plugin_field['advanced_file_transfer'][2]['value']             = '';
$plugin_field['advanced_file_transfer'][2]['attributes']        = 'disabled size=50';
$plugin_field['advanced_file_transfer'][2]['description']       = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_SRC_FILE_DESCRIPTION;
$plugin_field['advanced_file_transfer'][2]['num_rules']         = '1';
$plugin_field['advanced_file_transfer'][2]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['advanced_file_transfer'][2]['rule_type'][0]      = 'required';
$plugin_field['advanced_file_transfer'][2]['rule_attribute'][0] = '';

$plugin_field['advanced_file_transfer'][3]['type']              = 'text';
$plugin_field['advanced_file_transfer'][3]['name']              = 'file_advanced_file_transfer_src_rsync_path';
$plugin_field['advanced_file_transfer'][3]['value']             = '';
$plugin_field['advanced_file_transfer'][3]['attributes']        = 'disabled size=50';
$plugin_field['advanced_file_transfer'][3]['description']       = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_SRC_RSYNC_PATH;

$plugin_field['advanced_file_transfer'][4]['type']        = 'text';
$plugin_field['advanced_file_transfer'][4]['name']        = 'file_advanced_file_transfer_dest_host';
$plugin_field['advanced_file_transfer'][4]['value']       = '';
$plugin_field['advanced_file_transfer'][4]['attributes']  = 'disabled size=30';
$plugin_field['advanced_file_transfer'][4]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_DEST_HOST_DESCRIPTION;
$plugin_field['advanced_file_transfer'][4]['num_rules']         = '1';
$plugin_field['advanced_file_transfer'][4]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['advanced_file_transfer'][4]['rule_type'][0]      = 'required';
$plugin_field['advanced_file_transfer'][4]['rule_attribute'][0] = '';

$plugin_field['advanced_file_transfer'][5]['type']        = 'text';
$plugin_field['advanced_file_transfer'][5]['name']        = 'file_advanced_file_transfer_dest_user';
$plugin_field['advanced_file_transfer'][5]['value']       = '';
$plugin_field['advanced_file_transfer'][5]['attributes']  = 'disabled size=30';
$plugin_field['advanced_file_transfer'][5]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_DEST_USER_DESCRIPTION;
$plugin_field['advanced_file_transfer'][5]['num_rules']         = '1';
$plugin_field['advanced_file_transfer'][5]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['advanced_file_transfer'][5]['rule_type'][0]      = 'required';
$plugin_field['advanced_file_transfer'][5]['rule_attribute'][0] = '';

$plugin_field['advanced_file_transfer'][6]['type']        = 'text';
$plugin_field['advanced_file_transfer'][6]['name']        = 'file_advanced_file_transfer_dest_dir';
$plugin_field['advanced_file_transfer'][6]['value']       = '';
$plugin_field['advanced_file_transfer'][6]['attributes']  = 'disabled size=50';
$plugin_field['advanced_file_transfer'][6]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_DEST_DIR_DESCRIPTION;

$plugin_field['advanced_file_transfer'][7]['type']              = 'text';
$plugin_field['advanced_file_transfer'][7]['name']              = 'file_advanced_file_transfer_dest_rsync_path';
$plugin_field['advanced_file_transfer'][7]['value']             = '';
$plugin_field['advanced_file_transfer'][7]['attributes']        = 'disabled size=50';
$plugin_field['advanced_file_transfer'][7]['description']       = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_DEST_RSYNC_PATH;

$plugin_field['advanced_file_transfer'][8]['type']        = 'select';
$plugin_field['advanced_file_transfer'][8]['name']        = 'file_advanced_file_transfer_checkpoint';
$plugin_field['advanced_file_transfer'][8]['value']       = array('0' => NO,
                                                           '1' => YES);
$plugin_field['advanced_file_transfer'][8]['attributes']  = '';
$plugin_field['advanced_file_transfer'][8]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_CHECKPOINT_DESCRIPTION;

$plugin_field['advanced_file_transfer'][9]['type']        = 'select';
$plugin_field['advanced_file_transfer'][9]['name']        = 'file_advanced_file_transfer_compress';
$plugin_field['advanced_file_transfer'][9]['value']       = array('0' => NO,
                                                           '1' => YES);
$plugin_field['advanced_file_transfer'][9]['attributes']  = '';
$plugin_field['advanced_file_transfer'][9]['description'] = PLUGIN_FILE_ADVANCED_FILE_TRANSFER_COMPRESS_DESCRIPTION;

$plugin_field['advanced_file_transfer'][10]['type']        = 'text';
$plugin_field['advanced_file_transfer'][10]['name']        = 'file_advanced_file_transfer_retention';
$plugin_field['advanced_file_transfer'][10]['value']       = '7';
$plugin_field['advanced_file_transfer'][10]['attributes']  = 'disabled';
$plugin_field['advanced_file_transfer'][10]['description'] = PLUGIN_RETENTION;
?>