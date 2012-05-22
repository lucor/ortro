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
 * @author   Marcello Sessa <zodd81@users.sourceforge.net>
 * @author   Danilo Alfano <ph4ntom@users.sourceforge.net>
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_actions['check_glance_status'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['check_glance_status'][0]['action']      = 'plugin';
$plugin_actions['check_glance_status'][0]['file']        = 'display_archive_results';
$plugin_actions['check_glance_status'][0]['image']       = 'archive.png';

$plugin_field['check_glance_status'][0]['version']           = '1.0.1';
$plugin_field['check_glance_status'][0]['min_ortro_version'] = '1.2.0';

$plugin_field['check_glance_status'][0]['authors'][0]  = 'Marcello Sessa <zodd81@users.sourceforge.net>'; 
$plugin_field['check_glance_status'][0]['authors'][1]  = 'Danilo Alfano <ph4ntom@users.sourceforge.net>';
$plugin_field['check_glance_status'][0]['description'] = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_DESCRIPTION;
$plugin_field['check_glance_status'][0]['title']       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_TITLE;

$plugin_field['check_glance_status'][1]['type']              = 'text';
$plugin_field['check_glance_status'][1]['name']              = 'check_glance_status_dir_path';
$plugin_field['check_glance_status'][1]['value']             = '';
$plugin_field['check_glance_status'][1]['attributes']        = 'disabled size=70';
$plugin_field['check_glance_status'][1]['description']       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_PATH_COMMAND;
$plugin_field['check_glance_status'][1]['num_rules']         = '1';
$plugin_field['check_glance_status'][1]['rule_msg'][0]       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_RULE_1_0;
$plugin_field['check_glance_status'][1]['rule_type'][0]      = 'required';
$plugin_field['check_glance_status'][1]['rule_attribute'][0] = '';

$plugin_field['check_glance_status'][2]['type']              = 'text';
$plugin_field['check_glance_status'][2]['name']              = 'check_glance_status_user';
$plugin_field['check_glance_status'][2]['value']             = '';
$plugin_field['check_glance_status'][2]['attributes']        = 'disabled';
$plugin_field['check_glance_status'][2]['description']       = PLUGIN_USER;
$plugin_field['check_glance_status'][2]['num_rules']         = '1';
$plugin_field['check_glance_status'][2]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['check_glance_status'][2]['rule_type'][0]      = 'required';
$plugin_field['check_glance_status'][2]['rule_attribute'][0] = '';

$plugin_field['check_glance_status'][3]['type']        = 'text';
$plugin_field['check_glance_status'][3]['name']        = 'check_glance_status_port';
$plugin_field['check_glance_status'][3]['value']       = '';
$plugin_field['check_glance_status'][3]['attributes']  = 'disabled';
$plugin_field['check_glance_status'][3]['description'] = PLUGIN_PORT;

$plugin_field['check_glance_status'][4]['type']  = 'select';
$plugin_field['check_glance_status'][4]['name']  = 'check_glance_type';
$plugin_field['check_glance_status'][4]['value'] = array('CPU' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_CPU,
                                                         'IO' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_IO,
                                                         'MEM' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_MEM,
                                                         'NFS' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_NFS,
                                                         'PRO' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_PRO,
                                                         'NET' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_NET);

$plugin_field['check_glance_status'][4]['attributes']        = '';
$plugin_field['check_glance_status'][4]['description']       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_TYPE;
$plugin_field['check_glance_status'][4]['num_rules']         = '1';
$plugin_field['check_glance_status'][4]['rule_msg'][0]       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_RULE_4_0;
$plugin_field['check_glance_status'][4]['rule_type'][0]      = 'required';
$plugin_field['check_glance_status'][4]['rule_attribute'][0] = '';

$plugin_field['check_glance_status'][5]['type']        = 'text';
$plugin_field['check_glance_status'][5]['name']        = 'check_glance_status_number';
$plugin_field['check_glance_status'][5]['value']       = '';
$plugin_field['check_glance_status'][5]['attributes']  = 'disabled';
$plugin_field['check_glance_status'][5]['description'] = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_NUMBER_DESCRIPTION;

$plugin_field['check_glance_status'][6]['type']        = 'select';
$plugin_field['check_glance_status'][6]['name']        = 'check_glance_status_operator';
$plugin_field['check_glance_status'][6]['value']       = array('>' => '>', '<' => '<', '=' => '=', '!=' => '!=');
$plugin_field['check_glance_status'][6]['attributes']  = '';
$plugin_field['check_glance_status'][6]['description'] = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_OPERATOR_DESCRIPTION;
$plugin_field['check_glance_status'][6]['num_rules']   = '0';

$plugin_field['check_glance_status'][7]['type']              = 'text';
$plugin_field['check_glance_status'][7]['name']              = 'check_glance_status_threshold';
$plugin_field['check_glance_status'][7]['value']             = '';
$plugin_field['check_glance_status'][7]['attributes']        = 'disabled';
$plugin_field['check_glance_status'][7]['description']       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_THRESHOLD_DESCRIPTION;
$plugin_field['check_glance_status'][7]['num_rules']         = '1';
$plugin_field['check_glance_status'][7]['rule_msg'][0]       = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_RULE_7_0;
$plugin_field['check_glance_status'][7]['rule_type'][0]      = 'required';
$plugin_field['check_glance_status'][7]['rule_attribute'][0] = '';

$plugin_field['check_glance_status'][8]['type']        = 'select';
$plugin_field['check_glance_status'][8]['name']        = 'check_glance_status_is_error';
$plugin_field['check_glance_status'][8]['value']       = array('0' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_SUCCESS,
                                                               '1' => PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_ERROR);
$plugin_field['check_glance_status'][8]['attributes']  = '';
$plugin_field['check_glance_status'][8]['description'] = PLUGIN_SYSTEM_CHECK_GLANCE_STATUS_ERROR_DESCRIPTION;
$plugin_field['check_glance_status'][8]['num_rules']   = '0';

$plugin_field['check_glance_status'][9]['type']        = 'text';
$plugin_field['check_glance_status'][9]['name']        = 'check_glance_retention';
$plugin_field['check_glance_status'][9]['value']       = '';
$plugin_field['check_glance_status'][9]['attributes']  = 'disabled';
$plugin_field['check_glance_status'][9]['description'] = PLUGIN_RETENTION;
?>