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

$conf_metadata['advanced_file_transfer']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['advanced_file_transfer']['mode']['description'] = PLUGIN_ADVANCED_FILE_TRANSFER_METADATA_MODE_DESCRIPTION;
$conf_metadata['advanced_file_transfer']['mode']['type']        = 'select';
$conf_metadata['advanced_file_transfer']['mode']['name']        = 'advanced_file_transfer-mode';
$conf_metadata['advanced_file_transfer']['mode']['value']       = array('nofilter' =>  PLUGIN_ADVANCED_FILE_TRANSFER_METADATA_MODE_NO_DESCRIPTION,
                                                     'whitelist' => PLUGIN_ADVANCED_FILE_TRANSFER_METADATA_MODE_WHITELIST_DESCRIPTION,
                                                     'blacklist' => PLUGIN_ADVANCED_FILE_TRANSFER_METADATA_MODE_BLACKLIST_DESCRIPTION);
$conf_metadata['advanced_file_transfer']['mode']['attributes']  ='';

$conf_metadata['advanced_file_transfer']['rules']['description'] = PLUGIN_ADVANCED_FILE_TRANSFER_METADATA_ADDRESS_DESCRIPTION;
$conf_metadata['advanced_file_transfer']['rules']['type']        = 'textarea';
$conf_metadata['advanced_file_transfer']['rules']['name']        = 'advanced_file_transfer-rules';
$conf_metadata['advanced_file_transfer']['rules']['value']       = ";example\nlocalhost:/root";
$conf_metadata['advanced_file_transfer']['rules']['attributes']  = 'rows=50 cols=80';
?>