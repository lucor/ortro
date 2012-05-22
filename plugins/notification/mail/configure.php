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

$plugin_field['mail'][0]['version']           = '1.2.6';
$plugin_field['mail'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['mail'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['mail'][0]['title']             = PLUGIN_MAIL_TITLE;
$plugin_field['mail'][0]['description']       = PLUGIN_MAIL_DESCRIPTION;


$plugin_field['mail'][1]['type']              = 'text';
$plugin_field['mail'][1]['name']              = 'mail_subject';
$plugin_field['mail'][1]['value']             = '';
$plugin_field['mail'][1]['attributes']        = 'disabled size=70';
$plugin_field['mail'][1]['description']       = PLUGIN_MAIL_SUBJECT_DESCRIPTION;
$plugin_field['mail'][1]['num_rules']         = '1';
$plugin_field['mail'][1]['rule_msg'][0]       = PLUGIN_MAIL_RULE_1_0;
$plugin_field['mail'][1]['rule_type'][0]      = 'required';
$plugin_field['mail'][1]['rule_attribute'][0] = '';

$plugin_field['mail'][2]['type']              = 'text';
$plugin_field['mail'][2]['name']              = 'mail_to';
$plugin_field['mail'][2]['value']             = '';
$plugin_field['mail'][2]['attributes']        = 'disabled size=70';
$plugin_field['mail'][2]['description']       = PLUGIN_MAIL_TO_DESCRIPTION;
$plugin_field['mail'][2]['num_rules']         = '2';
$plugin_field['mail'][2]['rule_msg'][0]       = PLUGIN_MAIL_RULE_2_0;
$plugin_field['mail'][2]['rule_type'][0]      = 'required';
$plugin_field['mail'][2]['rule_attribute'][0] = '';
$plugin_field['mail'][2]['rule_msg'][1]       = PLUGIN_MAIL_RULE_2_1;
$plugin_field['mail'][2]['rule_type'][1]      = 'regex';
$plugin_field['mail'][2]['rule_attribute'][1] = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4}(\s*,\s*([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4})*$/';

$plugin_field['mail'][3]['type']              = 'text';
$plugin_field['mail'][3]['name']              = 'mail_cc';
$plugin_field['mail'][3]['value']             = '';
$plugin_field['mail'][3]['attributes']        = 'disabled size=70';
$plugin_field['mail'][3]['description']       = PLUGIN_MAIL_CC_DESCRIPTION;
$plugin_field['mail'][3]['num_rules']         = '1';
$plugin_field['mail'][3]['rule_msg'][0]       = PLUGIN_MAIL_RULE_3_0;
$plugin_field['mail'][3]['rule_type'][0]      = 'regex';
$plugin_field['mail'][3]['rule_attribute'][0] = '/^(([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4}(\s*,\s*([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4})*)*$/';

$plugin_field['mail'][4]['type']        = 'textarea';
$plugin_field['mail'][4]['name']        = 'mail_html';
$plugin_field['mail'][4]['value']       = '';
$plugin_field['mail'][4]['attributes']  = 'disabled htmlarea rows=10 cols=50';
$plugin_field['mail'][4]['description'] = PLUGIN_MAIL_BODY_DESCRIPTION;

$plugin_field['mail'][5]['type']        = 'checkbox';
$plugin_field['mail'][5]['name']        = 'mail_attach_result';
$plugin_field['mail'][5]['value']       = '';
$plugin_field['mail'][5]['attributes']  = '';
$plugin_field['mail'][5]['description'] = PLUGIN_ATTACH_RESULT;

$plugin_field['mail'][6]['type']        = 'checkbox';
$plugin_field['mail'][6]['name']        = 'mail_attach_timestamp';
$plugin_field['mail'][6]['value']       = '';
$plugin_field['mail'][6]['attributes']  = '';
$plugin_field['mail'][6]['description'] = PLUGIN_ATTACH_DATE;
?>