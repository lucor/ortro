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

$conf_metadata['jabber']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['jabber']['host']['description'] = PLUGIN_JABBER_METADATA_SERVER_DESCRIPTION;
$conf_metadata['jabber']['host']['type']        = 'text';
$conf_metadata['jabber']['host']['name']        = 'jabber-host';
$conf_metadata['jabber']['host']['value']       = 'localhost';
$conf_metadata['jabber']['host']['attributes']  = 'size=30';

$conf_metadata['jabber']['security']['description'] = PLUGIN_JABBER_METADATA_SECURITY_DESCRIPTION;
$conf_metadata['jabber']['security']['type']        = 'select';
$conf_metadata['jabber']['security']['name']        = 'jabber-security';
$conf_metadata['jabber']['security']['value']       = array('none' => PLUGIN_JABBER_METADATA_SECURITY_NONE,
                                                            'ssl' => PLUGIN_JABBER_METADATA_SECURITY_SSL,
                                                            'tsl' => PLUGIN_JABBER_METADATA_SECURITY_TSL);
$conf_metadata['jabber']['security']['attributes']  = '';

$conf_metadata['jabber']['port']['description'] = PLUGIN_JABBER_METADATA_PORT_DESCRIPTION;
$conf_metadata['jabber']['port']['type']        = 'text';
$conf_metadata['jabber']['port']['name']        = 'jabber-port';
$conf_metadata['jabber']['port']['value']       = '';
$conf_metadata['jabber']['port']['attributes']  = 'size=5';

$conf_metadata['jabber']['username']['description'] = PLUGIN_JABBER_METADATA_USER_DESCRIPTION;
$conf_metadata['jabber']['username']['type']        = 'text';
$conf_metadata['jabber']['username']['name']        = 'jabber-username';
$conf_metadata['jabber']['username']['value']       = '';
$conf_metadata['jabber']['username']['attributes']  = 'size=30';

$conf_metadata['jabber']['password']['description'] = PLUGIN_JABBER_METADATA_PASSWORD_DESCRIPTION;
$conf_metadata['jabber']['password']['type']        = 'password';
$conf_metadata['jabber']['password']['name']        = 'jabber-password';
$conf_metadata['jabber']['password']['value']       = '';
$conf_metadata['jabber']['password']['attributes']  = 'size=30';
?>