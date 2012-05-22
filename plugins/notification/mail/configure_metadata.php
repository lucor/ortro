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

$conf_metadata['mail']['description']         = PLUGIN_METADATA_CONFIGURATION;
$conf_metadata['mail']['type']['description'] = PLUGIN_MAIL_METADATA_NAME_DESCRIPTION;
$conf_metadata['mail']['type']['type']        = 'select';
$conf_metadata['mail']['type']['name']        = 'mail-type';
$conf_metadata['mail']['type']['value']       = array('smtp' => 'smtp',
                                                      'sendmail' => 'sendmail',
                                                      'mail' => 'mail');
$conf_metadata['mail']['type']['attributes']  ='';

$conf_metadata['mail']['from']['description'] = PLUGIN_MAIL_METADATA_ADDRESS_DESCRIPTION;
$conf_metadata['mail']['from']['type']        = 'text';
$conf_metadata['mail']['from']['name']        = 'mail-from';
$conf_metadata['mail']['from']['value']       = 'ortro@myserver.com';
$conf_metadata['mail']['from']['attributes']  = 'size=30';

$conf_metadata['mail']['reply_to']['description'] = PLUGIN_MAIL_METADATA_REPLY_TO_DESCRIPTION;
$conf_metadata['mail']['reply_to']['type']        = 'text';
$conf_metadata['mail']['reply_to']['name']        = 'mail-reply_to';
$conf_metadata['mail']['reply_to']['value']       = 'ortro@myserver.com';
$conf_metadata['mail']['reply_to']['attributes']  = 'size=30';

$conf_metadata['mail']['signature']['description'] = PLUGIN_MAIL_METADATA_SIGNATURE_DESCRIPTION;
$conf_metadata['mail']['signature']['type']        = 'textarea';
$conf_metadata['mail']['signature']['name']        = 'mail-signature';
$conf_metadata['mail']['signature']['value']       = '<br/><br/>--<br/>This mail was generated automatically by Ortro. Please do not reply to it.<br/>For further information please contact <a href="mailto:ortro@myserver.com">Ortro administrator</a>.';
$conf_metadata['mail']['signature']['attributes']  = 'rows=6 cols=70';

$conf_metadata['mail']['sendmail_path']['description'] = PLUGIN_MAIL_METADATA_PATH_DESCRIPTION;
$conf_metadata['mail']['sendmail_path']['type']        = 'text';
$conf_metadata['mail']['sendmail_path']['name']        = 'mail-sendmail_path';
$conf_metadata['mail']['sendmail_path']['value']       = '';
$conf_metadata['mail']['sendmail_path']['attributes']  = 'size=30';

$conf_metadata['mail']['sendmail_args']['description'] = PLUGIN_MAIL_METADATA_PARAMETERS_DESCRIPTION;
$conf_metadata['mail']['sendmail_args']['type']        = 'text';
$conf_metadata['mail']['sendmail_args']['name']        = 'mail-sendmail_args';
$conf_metadata['mail']['sendmail_args']['value']       = '';
$conf_metadata['mail']['sendmail_args']['attributes']  = 'size=30';

$conf_metadata['mail']['host']['description'] = PLUGIN_MAIL_METADATA_SERVER_DESCRIPTION;
$conf_metadata['mail']['host']['type']        = 'text';
$conf_metadata['mail']['host']['name']        = 'mail-host';
$conf_metadata['mail']['host']['value']       = 'localhost';
$conf_metadata['mail']['host']['attributes']  = 'size=30';

$conf_metadata['mail']['port']['description'] = PLUGIN_MAIL_METADATA_PORT_DESCRIPTION;
$conf_metadata['mail']['port']['type']        = 'text';
$conf_metadata['mail']['port']['name']        = 'mail-port';
$conf_metadata['mail']['port']['value']       = '25';
$conf_metadata['mail']['port']['attributes']  = 'size=30';

$conf_metadata['mail']['auth']['description'] = PLUGIN_MAIL_METADATA_SMTP_DESCRIPTION;
$conf_metadata['mail']['auth']['type']        = 'select';
$conf_metadata['mail']['auth']['name']        = 'mail-auth';
$conf_metadata['mail']['auth']['value']       = array('0' => 'false',
                                                      '1' => 'true');
$conf_metadata['mail']['auth']['attributes']  = '';

$conf_metadata['mail']['username']['description'] = PLUGIN_MAIL_METADATA_USER_DESCRIPTION;
$conf_metadata['mail']['username']['type']        = 'text';
$conf_metadata['mail']['username']['name']        = 'mail-username';
$conf_metadata['mail']['username']['value']       = '';
$conf_metadata['mail']['username']['attributes']  = 'size=30';

$conf_metadata['mail']['password']['description'] = PLUGIN_MAIL_METADATA_PASSWORD_DESCRIPTION;
$conf_metadata['mail']['password']['type']        = 'password';
$conf_metadata['mail']['password']['name']        = 'mail-password';
$conf_metadata['mail']['password']['value']       = '';
$conf_metadata['mail']['password']['attributes']  = 'size=30';

$conf_metadata['mail']['localhost']['description'] = PLUGIN_MAIL_METADATA_HELO_DESCRIPTION;
$conf_metadata['mail']['localhost']['type']        = 'text';
$conf_metadata['mail']['localhost']['name']        = 'mail-localhost';
$conf_metadata['mail']['localhost']['value']       = php_uname('n');
$conf_metadata['mail']['localhost']['attributes']  ='size=30';

$conf_metadata['mail']['timeout']['description'] = PLUGIN_MAIL_METADATA_TIMEOUT_DESCRIPTION;
$conf_metadata['mail']['timeout']['type']        = 'text';
$conf_metadata['mail']['timeout']['name']        = 'mail-timeout';
$conf_metadata['mail']['timeout']['value']       = '';
$conf_metadata['mail']['timeout']['attributes']  = 'size=30';
?>