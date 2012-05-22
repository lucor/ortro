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

$conf_metadata['scp']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['scp']['mode']['description'] = PLUGIN_SCP_METADATA_MODE_DESCRIPTION;
$conf_metadata['scp']['mode']['type']        = 'select';
$conf_metadata['scp']['mode']['name']        = 'scp-mode';
$conf_metadata['scp']['mode']['value']       = array('nofilter' =>  PLUGIN_SCP_METADATA_MODE_NO_DESCRIPTION,
                                                     'whitelist' => PLUGIN_SCP_METADATA_MODE_WHITELIST_DESCRIPTION,
                                                     'blacklist' => PLUGIN_SCP_METADATA_MODE_BLACKLIST_DESCRIPTION);
$conf_metadata['scp']['mode']['attributes']  ='';

$conf_metadata['scp']['rules']['description'] = PLUGIN_SCP_METADATA_ADDRESS_DESCRIPTION;
$conf_metadata['scp']['rules']['type']        = 'textarea';
$conf_metadata['scp']['rules']['name']        = 'scp-rules';
$conf_metadata['scp']['rules']['value']       = ";example\n localhost:/root";
$conf_metadata['scp']['rules']['attributes']  = 'rows=50 cols=80';
?>