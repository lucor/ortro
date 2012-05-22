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

$conf_metadata['tibco_rvd']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['tibco_rvd']['path']['description'] = PLUGIN_TIBCO_RVD_METADATA_TIBCO_PATH_DESCRIPTION;
$conf_metadata['tibco_rvd']['path']['type']        = 'text';
$conf_metadata['tibco_rvd']['path']['name']        = 'tibco_rvd-path';
$conf_metadata['tibco_rvd']['path']['value']       = '/opt/tibco/tibrv/bin/tibrvsend';
$conf_metadata['tibco_rvd']['path']['attributes']  = 'size=30';
?>