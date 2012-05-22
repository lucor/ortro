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

/* Environment configuration */

$conf_metadata['webtest_selenium']['description']                = PLUGIN_METADATA_CONFIGURATION; 
$conf_metadata['webtest_selenium']['python_path']['description'] = PLUGIN_WEBTEST_SELENIUM_METADATA_PYTHON_PATH_DESCRIPTION;
$conf_metadata['webtest_selenium']['python_path']['type']        = 'text';
$conf_metadata['webtest_selenium']['python_path']['name']        = 'webtest_selenium-python_path';
$conf_metadata['webtest_selenium']['python_path']['value']       = '/usr/bin/';
$conf_metadata['webtest_selenium']['python_path']['attributes']  = 'size=30';

$conf_metadata['webtest_selenium']['selenium_path']['description'] = PLUGIN_WEBTEST_SELENIUM_METADATA_SELENIUM_PATH_DESCRIPTION;
$conf_metadata['webtest_selenium']['selenium_path']['type']        = 'text';
$conf_metadata['webtest_selenium']['selenium_path']['name']        = 'webtest_selenium-selenium_path';
$conf_metadata['webtest_selenium']['selenium_path']['value']       = '/usr/bin/';
$conf_metadata['webtest_selenium']['selenium_path']['attributes']  = 'size=30';
?>