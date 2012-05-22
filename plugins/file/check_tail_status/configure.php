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
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$plugin_field['check_tail_status'][0]['version']           = '1.2.3';
$plugin_field['check_tail_status'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['check_tail_status'][0]['authors'][0]        = 'Marcello Sessa <zodd81@users.sourceforge.net>';
$plugin_field['check_tail_status'][0]['description']       = PLUGIN_FILE_CHECK_TAIL_STATUS_DESCRIPTION;
$plugin_field['check_tail_status'][0]['title']             = PLUGIN_FILE_CHECK_TAIL_STATUS_TITLE;

$plugin_field['check_tail_status'][1]['type']              = 'text';
$plugin_field['check_tail_status'][1]['name']              = 'check_tail_status_user';
$plugin_field['check_tail_status'][1]['value']             = '';
$plugin_field['check_tail_status'][1]['attributes']        = 'disabled';
$plugin_field['check_tail_status'][1]['description']       = PLUGIN_USER;
$plugin_field['check_tail_status'][1]['num_rules']         = '1';
$plugin_field['check_tail_status'][1]['rule_msg'][0]       = PLUGIN_USER_RULE;
$plugin_field['check_tail_status'][1]['rule_type'][0]      = 'required';
$plugin_field['check_tail_status'][1]['rule_attribute'][0] = '';

$plugin_field['check_tail_status'][2]['type']        = 'text';
$plugin_field['check_tail_status'][2]['name']        = 'check_tail_status_port';
$plugin_field['check_tail_status'][2]['value']       = '';
$plugin_field['check_tail_status'][2]['attributes']  = 'disabled';
$plugin_field['check_tail_status'][2]['description'] = PLUGIN_PORT;

$plugin_field['check_tail_status'][3]['type']              = 'text';
$plugin_field['check_tail_status'][3]['name']              = 'check_tail_status_dir_path';
$plugin_field['check_tail_status'][3]['value']             = '';
$plugin_field['check_tail_status'][3]['attributes']        = 'disabled size=70';
$plugin_field['check_tail_status'][3]['description']       = PLUGIN_FILE_CHECK_TAIL_STATUS_DIR_PATH_DESCRIPTION;
$plugin_field['check_tail_status'][3]['num_rules']         = '1';
$plugin_field['check_tail_status'][3]['rule_msg'][0]       = PLUGIN_FILE_CHECK_TAIL_STATUS_RULE_3_0;
$plugin_field['check_tail_status'][3]['rule_type'][0]      = 'required';
$plugin_field['check_tail_status'][3]['rule_attribute'][0] = '';

$plugin_field['check_tail_status'][4]['type']              = 'text';
$plugin_field['check_tail_status'][4]['name']              = 'check_tail_status_sleep';
$plugin_field['check_tail_status'][4]['value']             = '';
$plugin_field['check_tail_status'][4]['attributes']        = 'disabled';
$plugin_field['check_tail_status'][4]['description']       = PLUGIN_FILE_CHECK_TAIL_STATUS_SLEEP_DESCRIPTION;
$plugin_field['check_tail_status'][4]['num_rules']         = '1';
$plugin_field['check_tail_status'][4]['rule_msg'][0]       = PLUGIN_FILE_CHECK_TAIL_STATUS_RULE_4_0;
$plugin_field['check_tail_status'][4]['rule_type'][0]      = 'required';
$plugin_field['check_tail_status'][4]['rule_attribute'][0] = '';
?>