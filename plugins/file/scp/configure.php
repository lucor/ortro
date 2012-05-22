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

$plugin_actions['scp'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['scp'][0]['action']      = 'plugin';
$plugin_actions['scp'][0]['file']        = 'display_archive_results';
$plugin_actions['scp'][0]['image']       = 'archive.png';

$plugin_field['scp'][0]['version']           = '1.0.3';
$plugin_field['scp'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['scp'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['scp'][0]['description']       = PLUGIN_FILE_SCP_DESCRIPTION;
$plugin_field['scp'][0]['title']             = PLUGIN_FILE_SCP_TITLE;

$plugin_field['scp'][1]['type']        = 'text';
$plugin_field['scp'][1]['name']        = 'file_scp_src_user';
$plugin_field['scp'][1]['value']       = '';
$plugin_field['scp'][1]['attributes']  = 'disabled size=30';
$plugin_field['scp'][1]['description'] = PLUGIN_FILE_SCP_SRC_USER_DESCRIPTION;
$plugin_field['scp'][1]['num_rules']         = '1';
$plugin_field['scp'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['scp'][1]['rule_type'][0]      = 'required';
$plugin_field['scp'][1]['rule_attribute'][0] = '';


$plugin_field['scp'][2]['type']              = 'text';
$plugin_field['scp'][2]['name']              = 'file_scp_src_filename';
$plugin_field['scp'][2]['value']             = '';
$plugin_field['scp'][2]['attributes']        = 'disabled size=50';
$plugin_field['scp'][2]['description']       = PLUGIN_FILE_SCP_SRC_FILE_DESCRIPTION;
$plugin_field['scp'][2]['num_rules']         = '1';
$plugin_field['scp'][2]['rule_msg'][0]       = PLUGIN_FILE_NAME_RULE;
$plugin_field['scp'][2]['rule_type'][0]      = 'required';
$plugin_field['scp'][2]['rule_attribute'][0] = '';

$plugin_field['scp'][3]['type']        = 'text';
$plugin_field['scp'][3]['name']        = 'file_scp_dest_host';
$plugin_field['scp'][3]['value']       = '';
$plugin_field['scp'][3]['attributes']  = 'disabled size=30';
$plugin_field['scp'][3]['description'] = PLUGIN_FILE_SCP_DEST_HOST_DESCRIPTION;
$plugin_field['scp'][3]['num_rules']         = '1';
$plugin_field['scp'][3]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['scp'][3]['rule_type'][0]      = 'required';
$plugin_field['scp'][3]['rule_attribute'][0] = '';

$plugin_field['scp'][4]['type']        = 'text';
$plugin_field['scp'][4]['name']        = 'file_scp_dest_user';
$plugin_field['scp'][4]['value']       = '';
$plugin_field['scp'][4]['attributes']  = 'disabled size=30';
$plugin_field['scp'][4]['description'] = PLUGIN_FILE_SCP_DEST_USER_DESCRIPTION;
$plugin_field['scp'][4]['num_rules']         = '1';
$plugin_field['scp'][4]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['scp'][4]['rule_type'][0]      = 'required';
$plugin_field['scp'][4]['rule_attribute'][0] = '';

$plugin_field['scp'][5]['type']        = 'text';
$plugin_field['scp'][5]['name']        = 'file_scp_dest_dir';
$plugin_field['scp'][5]['value']       = '';
$plugin_field['scp'][5]['attributes']  = 'disabled size=50';
$plugin_field['scp'][5]['description'] = PLUGIN_FILE_SCP_DEST_DIR_DESCRIPTION;

$plugin_field['scp'][6]['type']        = 'select';
$plugin_field['scp'][6]['name']        = 'file_scp_recursive';
$plugin_field['scp'][6]['value']       = array('0' => NO,
                                               '1' => YES);
$plugin_field['scp'][6]['attributes']  = '';
$plugin_field['scp'][6]['description'] = PLUGIN_FILE_SCP_RECURSIVE_DESCRIPTION;

$plugin_field['scp'][7]['type']        = 'select';
$plugin_field['scp'][7]['name']        = 'file_scp_compress';
$plugin_field['scp'][7]['value']       = array('0' => NO,
                                               '1' => YES);
$plugin_field['scp'][7]['attributes']  = '';
$plugin_field['scp'][7]['description'] = PLUGIN_FILE_SCP_COMPRESS_DESCRIPTION;

$plugin_field['scp'][8]['type']        = 'text';
$plugin_field['scp'][8]['name']        = 'file_scp_retention';
$plugin_field['scp'][8]['value']       = '7';
$plugin_field['scp'][8]['attributes']  = 'disabled';
$plugin_field['scp'][8]['description'] = PLUGIN_RETENTION;
?>