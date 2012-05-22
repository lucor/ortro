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

$plugin_actions['testgen4web_simpletest'][0]['description'] = PLUGIN_ARCHIVE_RESULTS;
$plugin_actions['testgen4web_simpletest'][0]['action']      = 'plugin';
$plugin_actions['testgen4web_simpletest'][0]['file']        = 'display_archive_results';
$plugin_actions['testgen4web_simpletest'][0]['image']       = 'archive.png';

$plugin_field['testgen4web_simpletest'][0]['version']           = '1.2.6';
$plugin_field['testgen4web_simpletest'][0]['min_ortro_version'] = '1.2.0';
$plugin_field['testgen4web_simpletest'][0]['authors'][0]        = 'Luca Corbo <lucor@ortro.net>';
$plugin_field['testgen4web_simpletest'][0]['authors'][1]        = 'Danilo Alfano <ph4ntom@users.sourceforge.net>';
$plugin_field['testgen4web_simpletest'][0]['description']       = PLUGIN_TEST4WEB_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][0]['title']             = PLUGIN_TEST4WEB_TITLE;

$plugin_field['testgen4web_simpletest'][1]['type']        = 'text';
$plugin_field['testgen4web_simpletest'][1]['name']        = 'testgen4web_simpletest_identity';
$plugin_field['testgen4web_simpletest'][1]['value']       = '';
$plugin_field['testgen4web_simpletest'][1]['attributes']  = 'disabled readonly';
$plugin_field['testgen4web_simpletest'][1]['description'] = PLUGIN_IDENTITY;
$plugin_field['testgen4web_simpletest'][1]['num_rules']   = '0';

$plugin_field['testgen4web_simpletest'][2]['type']        = 'text';
$plugin_field['testgen4web_simpletest'][2]['name']        = 'testgen4web_simpletest_proxy_host';
$plugin_field['testgen4web_simpletest'][2]['value']       = '';
$plugin_field['testgen4web_simpletest'][2]['attributes']  = 'disabled size=50';
$plugin_field['testgen4web_simpletest'][2]['description'] = PLUGIN_TEST4WEB_PROXY_HOST_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][2]['num_rules']   = '0';

$plugin_field['testgen4web_simpletest'][3]['type']        = 'text';
$plugin_field['testgen4web_simpletest'][3]['name']        = 'testgen4web_simpletest_proxy_user';
$plugin_field['testgen4web_simpletest'][3]['value']       = '';
$plugin_field['testgen4web_simpletest'][3]['attributes']  = 'disabled size=30';
$plugin_field['testgen4web_simpletest'][3]['description'] = PLUGIN_TEST4WEB_PROXY_USER_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][3]['num_rules']   = '0';

$plugin_field['testgen4web_simpletest'][4]['type']        = 'password';
$plugin_field['testgen4web_simpletest'][4]['name']        = 'testgen4web_simpletest_proxy_password';
$plugin_field['testgen4web_simpletest'][4]['value']       = '';
$plugin_field['testgen4web_simpletest'][4]['attributes']  ='disabled size=30';
$plugin_field['testgen4web_simpletest'][4]['description'] = PLUGIN_TEST4WEB_PROXY_PASSWORD_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][4]['num_rules']   = '0';

$plugin_field['testgen4web_simpletest'][5]['type']              = 'textarea';
$plugin_field['testgen4web_simpletest'][5]['name']              = 'testgen4web_simpletest_input_script';
$plugin_field['testgen4web_simpletest'][5]['value']             = '';
$plugin_field['testgen4web_simpletest'][5]['attributes']        = 'disabled rows=50 cols=80';
$plugin_field['testgen4web_simpletest'][5]['description']       = PLUGIN_TEST4WEB_XML_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][5]['num_rules']         = '1';
$plugin_field['testgen4web_simpletest'][5]['rule_msg'][0]       = PLUGIN_TEST4WEB_RULE_5_0;
$plugin_field['testgen4web_simpletest'][5]['rule_type'][0]      = 'required';
$plugin_field['testgen4web_simpletest'][5]['rule_attribute'][0] = '';

$plugin_field['testgen4web_simpletest'][6]['type']        = 'select';
$plugin_field['testgen4web_simpletest'][6]['name']        = 'testgen4web_simpletest_type';
$plugin_field['testgen4web_simpletest'][6]['value']       = array('details' => PLUGIN_TEST4WEB_DETAILS,
                                                                  'time' => PLUGIN_TEST4WEB_ONLY_TIME);
$plugin_field['testgen4web_simpletest'][6]['attributes']  = '';
$plugin_field['testgen4web_simpletest'][6]['description'] = PLUGIN_TEST4WEB_TYPE;
$plugin_field['testgen4web_simpletest'][6]['num_rules']   = '0';

$plugin_field['testgen4web_simpletest'][7]['type']              = 'text';
$plugin_field['testgen4web_simpletest'][7]['name']              = 'testgen4web_simpletest_threshold';
$plugin_field['testgen4web_simpletest'][7]['value']             = '0';
$plugin_field['testgen4web_simpletest'][7]['attributes']        = 'disabled';
$plugin_field['testgen4web_simpletest'][7]['description']       = PLUGIN_TEST4WEB_THRESHOLD_DESCRIPTION;
$plugin_field['testgen4web_simpletest'][7]['num_rules']         = '2';
$plugin_field['testgen4web_simpletest'][7]['rule_msg'][0]       = PLUGIN_TEST4WEB_RULE_7_0;
$plugin_field['testgen4web_simpletest'][7]['rule_type'][0]      = 'required';
$plugin_field['testgen4web_simpletest'][7]['rule_attribute'][0] = '';
$plugin_field['testgen4web_simpletest'][7]['rule_msg'][1]       = PLUGIN_TEST4WEB_RULE_7_1;
$plugin_field['testgen4web_simpletest'][7]['rule_type'][1]      = 'numeric';
$plugin_field['testgen4web_simpletest'][7]['rule_attribute'][1] = '';

$plugin_field['testgen4web_simpletest'][8]['type']        = 'text';
$plugin_field['testgen4web_simpletest'][8]['name']        = 'testgen4web_simpletest_retention';
$plugin_field['testgen4web_simpletest'][8]['value']       = '';
$plugin_field['testgen4web_simpletest'][8]['attributes']  = 'disabled';
$plugin_field['testgen4web_simpletest'][8]['description'] = PLUGIN_RETENTION;
?>