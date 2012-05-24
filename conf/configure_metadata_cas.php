<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains the metadata used to generate dinamically the web form
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Core
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

/* cas Configuration */
$conf_metadata['cas']['description'] = 'CAS Configuration';

$conf_metadata['cas']['server_hostname']['description'] = 'The hostname of the CAS server';
$conf_metadata['cas']['server_hostname']['type']        = 'text';
$conf_metadata['cas']['server_hostname']['name']        = 'cas-server_hostname';
$conf_metadata['cas']['server_hostname']['value']       = 'localhost';
$conf_metadata['cas']['server_hostname']['attributes']  = 'size=30';

$conf_metadata['cas']['server_port']['description'] = 'The port the CAS server is running on';
$conf_metadata['cas']['server_port']['type']        = 'text';
$conf_metadata['cas']['server_port']['name']        = 'cas-server_port';
$conf_metadata['cas']['server_port']['value']       = '443';
$conf_metadata['cas']['server_port']['attributes']  = 'size=30';

$conf_metadata['cas']['server_uri']['description'] = 'The URI the CAS server is responding on';
$conf_metadata['cas']['server_uri']['type']        = 'text';
$conf_metadata['cas']['server_uri']['name']        = 'cas-server_uri';
$conf_metadata['cas']['server_uri']['value']       = '';
$conf_metadata['cas']['server_uri']['attributes']  = 'size=30';

$conf_metadata['cas']['curl_opt_ssl_version']['description'] = 'The SSL version (2 or 3) to use.
                                  By default PHP will try to determine this itself,
                                  although in some cases this must be set manually.';
$conf_metadata['cas']['curl_opt_ssl_version']['type']        = 'select';
$conf_metadata['cas']['curl_opt_ssl_version']['name']        = 'cas-curl_opt_ssl_version';
$conf_metadata['cas']['curl_opt_ssl_version']['value']       = array('0' => 'Not specified',
                                                                 '2' => 'SSL version 2',
                                                                 '3' => 'SSL version 3');
$conf_metadata['cas']['curl_opt_ssl_version']['attributes']  = '';
?>