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

$conf_metadata['db']['description']            = CONF_INSTALL_DB_DESCRIPTION;
$conf_metadata['db']['phptype']['description'] = CONF_INSTALL_DB_PHPTYPE_DESCRIPTION;
$conf_metadata['db']['phptype']['type']        = 'select';
$conf_metadata['db']['phptype']['name']        = 'db-phptype';
$conf_metadata['db']['phptype']['value']       = array('mysql' => CONF_INSTALL_DB_PHPTYPE_VALUE_1,
                                                       'mysqli' => CONF_INSTALL_DB_PHPTYPE_VALUE_2,
                                                       'oci8' => CONF_INSTALL_DB_PHPTYPE_VALUE_3,
                                                       'pgsql' => CONF_INSTALL_DB_PHPTYPE_VALUE_4,
                                                       'sqlite' => CONF_INSTALL_DB_PHPTYPE_VALUE_5,
                                                       'mssql' => CONF_INSTALL_DB_PHPTYPE_VALUE_6,
                                                       'ibase' => CONF_INSTALL_DB_PHPTYPE_VALUE_7);
$conf_metadata['db']['phptype']['attributes']  = '';

$conf_metadata['db']['host']['description']       = CONF_INSTALL_DB_HOST_DESCRIPTION;
$conf_metadata['db']['host']['type']              = 'text';
$conf_metadata['db']['host']['name']              = 'db-host';
$conf_metadata['db']['host']['value']             = CONF_INSTALL_DB_HOST_VALUE;
$conf_metadata['db']['host']['attributes']        = 'size=30';
$conf_metadata['db']['host']['num_rules']         = '1';
$conf_metadata['db']['host']['rule_msg'][0]       = CONF_INSTALL_DB_HOST_RULE_1_0;
$conf_metadata['db']['host']['rule_type'][0]      = 'required';
$conf_metadata['db']['host']['rule_attribute'][0] = '';

$conf_metadata['db']['port']['description']       = CONF_INSTALL_DB_PORT_DESCRIPTION;
$conf_metadata['db']['port']['type']              = 'text';
$conf_metadata['db']['port']['name']              = 'db-port';
$conf_metadata['db']['port']['value']             = '3306';
$conf_metadata['db']['port']['attributes']        = 'size=10';
$conf_metadata['db']['port']['num_rules']         = '1';
$conf_metadata['db']['port']['rule_msg'][0]       = CONF_INSTALL_DB_PORT_RULE_1_0;
$conf_metadata['db']['port']['rule_type'][0]      = 'numeric';
$conf_metadata['db']['port']['rule_attribute'][0] = '';

$conf_metadata['db']['database']['description']       = CONF_INSTALL_DB_DATABASE_DESCRIPTION;
$conf_metadata['db']['database']['type']              = 'text';
$conf_metadata['db']['database']['name']              = 'db-database';
$conf_metadata['db']['database']['value']             = CONF_INSTALL_DB_DATABASE_VALUE;
$conf_metadata['db']['database']['attributes']        = 'size=30';
$conf_metadata['db']['database']['rule_msg'][0]       = CONF_INSTALL_DB_DATABASE_RULE_1_0;
$conf_metadata['db']['database']['rule_type'][0]      = 'required';
$conf_metadata['db']['database']['rule_attribute'][0] = '';

$conf_metadata['db']['username']['description']       = CONF_INSTALL_DB_USERNAME_DESCRIPTION;
$conf_metadata['db']['username']['type']              = 'text';
$conf_metadata['db']['username']['name']              = 'db-username';
$conf_metadata['db']['username']['value']             = CONF_INSTALL_DB_USERNAME_VALUE;
$conf_metadata['db']['username']['attributes']        = 'size=30';
$conf_metadata['db']['username']['rule_msg'][0]       = CONF_INSTALL_DB_USERNAME_RULE_1_0;
$conf_metadata['db']['username']['rule_type'][0]      = 'required';
$conf_metadata['db']['username']['rule_attribute'][0] = '';

$conf_metadata['db']['password']['description'] = CONF_INSTALL_DB_PASSWORD_DESCRIPTION;
$conf_metadata['db']['password']['type']        = 'password';
$conf_metadata['db']['password']['name']        = 'db-password';
$conf_metadata['db']['password']['value']       = '';
$conf_metadata['db']['password']['attributes']  = 'size=30';

$conf_metadata['db']['tableprefix']['description'] = CONF_INSTALL_DB_TABLEPREFIX_DESCRIPTION;
$conf_metadata['db']['tableprefix']['type']        = 'text';
$conf_metadata['db']['tableprefix']['name']        = 'db-tableprefix';
$conf_metadata['db']['tableprefix']['value']       = CONF_INSTALL_DB_TABLEPREFIX_VALUE;
$conf_metadata['db']['tableprefix']['attributes']  = 'size=30';

/* Auth configuration */
require_once 'authUtil.php';
$conf_metadata['auth']['description'] = CONF_INSTALL_AUTH_DESCRIPTION;

// Authentication settings
$conf_metadata['auth']['default']['description'] = CONF_INSTALL_AUTH_DEFAULT_DESCRIPTION;
$conf_metadata['auth']['default']['type']        = 'select';
$conf_metadata['auth']['default']['name']        = 'auth-default';
$conf_metadata['auth']['default']['value']       = authUtil::getAvailableAuthMethods();
$conf_metadata['auth']['default']['attributes']  = '';

// Database fallback authentication method
$conf_metadata['auth']['fallback']['description'] = CONF_INSTALL_AUTH_FALLBACK_DESCRIPTION;
$conf_metadata['auth']['fallback']['type']        = 'select';
$conf_metadata['auth']['fallback']['name']        = 'auth-fallback';
$conf_metadata['auth']['fallback']['value']       = array('0' => DISABLE, '1' => ENABLE);
$conf_metadata['auth']['fallback']['attributes']  = '';

/* Environment configuration */
$conf_metadata['env']['description'] = CONF_INSTALL_ENV_DESCRIPTION;

// Language settings
$conf_metadata['env']['lang']['description'] = CONF_INSTALL_ENV_LANG_DESCRIPTION;
$conf_metadata['env']['lang']['type']        = 'select';
$conf_metadata['env']['lang']['name']        = 'env-lang';
$conf_metadata['env']['lang']['value']       = getAvailableLanguages();
$conf_metadata['env']['lang']['attributes']  = '';

$conf_metadata['env']['php_path']['description']       = CONF_INSTALL_ENV_PHP_PATH_DESCRIPTION;
$conf_metadata['env']['php_path']['type']              = 'text';
$conf_metadata['env']['php_path']['name']              = 'env-php_path';
$conf_metadata['env']['php_path']['value']             = '/usr/bin/';
$conf_metadata['env']['php_path']['attributes']        = 'size=30';
$conf_metadata['env']['php_path']['rule_msg'][0]       = CONF_INSTALL_ENV_PHP_PATH_RULE_1_0;
$conf_metadata['env']['php_path']['rule_type'][0]      = 'required';
$conf_metadata['env']['php_path']['rule_attribute'][0] = '';

// SSH Settings
$conf_metadata['env']['ssh_path']['description']       = CONF_INSTALL_ENV_SSH_PATH_DESCRIPTION;
$conf_metadata['env']['ssh_path']['type']              = 'text';
$conf_metadata['env']['ssh_path']['name']              = 'env-ssh_path';
$conf_metadata['env']['ssh_path']['value']             = '/usr/bin/';
$conf_metadata['env']['ssh_path']['attributes']        = 'size=30';
$conf_metadata['env']['ssh_path']['rule_msg'][0]       = CONF_INSTALL_ENV_SSH_PATH_RULE_1_0;
$conf_metadata['env']['ssh_path']['rule_type'][0]      = 'required';
$conf_metadata['env']['ssh_path']['rule_attribute'][0] = '';

$conf_metadata['env']['ssh_keyname']['description']       = CONF_INSTALL_ENV_SSH_KEYNAME_DESCRIPTION;
$conf_metadata['env']['ssh_keyname']['type']              = 'text';
$conf_metadata['env']['ssh_keyname']['name']              = 'env-ssh_keyname';
$conf_metadata['env']['ssh_keyname']['value']             = 'ortro_rsa';
$conf_metadata['env']['ssh_keyname']['attributes']        = 'size=30';
$conf_metadata['env']['ssh_keyname']['rule_msg'][0]       = CONF_INSTALL_ENV_SSH_KEYNAME_RULE_1_0;
$conf_metadata['env']['ssh_keyname']['rule_type'][0]      = 'required';
$conf_metadata['env']['ssh_keyname']['rule_attribute'][0] = '';

$conf_metadata['env']['ssh_type']['description'] = CONF_INSTALL_ENV_SSH_TYPE_DESCRIPTION;
$conf_metadata['env']['ssh_type']['type']        = 'select';
$conf_metadata['env']['ssh_type']['name']        = 'env-ssh_type';
$conf_metadata['env']['ssh_type']['value']       = array('rsa' => 'rsa', 'dsa' => 'dsa');
$conf_metadata['env']['ssh_type']['attributes']  = '';

$conf_metadata['env']['ssh_bits']['description'] = CONF_INSTALL_ENV_SSH_BITS_DESCRIPTION;
$conf_metadata['env']['ssh_bits']['type']        = 'select';
$conf_metadata['env']['ssh_bits']['name']        = 'env-ssh_bits';
$conf_metadata['env']['ssh_bits']['value']       = array('1024' => '1024', '2048' => '2048');
$conf_metadata['env']['ssh_bits']['attributes']  = '';

$conf_metadata['env']['ssh_StrictHostKeyChecking']['description'] = CONF_INSTALL_ENV_SSH_STRICTHOSTKEYCHECKING_DESCRIPTION;
$conf_metadata['env']['ssh_StrictHostKeyChecking']['type']        = 'select';
$conf_metadata['env']['ssh_StrictHostKeyChecking']['name']        = 'env-ssh_StrictHostKeyChecking';
$conf_metadata['env']['ssh_StrictHostKeyChecking']['value']       = array('no' => NO, 'ask' => ASK, 'yes' => YES);
$conf_metadata['env']['ssh_StrictHostKeyChecking']['attributes']  ='';


$conf_metadata['env']['zip_path']['description'] = CONF_INSTALL_ENV_ZIP_PATH_DESCRIPTION;
$conf_metadata['env']['zip_path']['type']        = 'text';
$conf_metadata['env']['zip_path']['name']        = 'env-zip_path';
$conf_metadata['env']['zip_path']['value']       = '/usr/bin/zip';
$conf_metadata['env']['zip_path']['attributes']  ='size=30';

$conf_metadata['env']['zip_threshold']['description'] = CONF_INSTALL_ENV_ZIP_THRESHOLD_DESCRIPTION;
$conf_metadata['env']['zip_threshold']['type']        = 'text';
$conf_metadata['env']['zip_threshold']['name']        = 'env-zip_threshold';
$conf_metadata['env']['zip_threshold']['value']       = '512000';
$conf_metadata['env']['zip_threshold']['attributes']  = 'size=30';

// Log settings
$conf_metadata['env']['log_level']['description'] = CONF_INSTALL_ENV_LOG_LEVEL_DESCRIPTION;
$conf_metadata['env']['log_level']['type']        = 'select';
$conf_metadata['env']['log_level']['name']        = 'env-log_level';
$conf_metadata['env']['log_level']['value']       = array('DEBUG' => CONF_INSTALL_ENV_LOG_LEVEL_DEBUG,
                                                          'INFO' => CONF_INSTALL_ENV_LOG_LEVEL_INFO,
                                                          'ERROR' => CONF_INSTALL_ENV_LOG_LEVEL_ERROR);
$conf_metadata['env']['log_level']['attributes']  = '';

$conf_metadata['env']['dateFormat']['description']       = CONF_INSTALL_ENV_DATEFORMAT_DESCRIPTION;
$conf_metadata['env']['dateFormat']['type']              = 'text';
$conf_metadata['env']['dateFormat']['name']              = 'env-dateFormat';
$conf_metadata['env']['dateFormat']['value']             = 'Y-m-d';
$conf_metadata['env']['dateFormat']['attributes']        = 'size=10';
$conf_metadata['env']['dateFormat']['rule_msg'][0]       = CONF_INSTALL_DATEFORMAT_RULE_1_0;
$conf_metadata['env']['dateFormat']['rule_type'][0]      = 'required';
$conf_metadata['env']['dateFormat']['rule_attribute'][0] = '';

$conf_metadata['env']['timeFormat']['description']       = CONF_INSTALL_ENV_TIMEFORMAT_DESCRIPTION;
$conf_metadata['env']['timeFormat']['type']              = 'text';
$conf_metadata['env']['timeFormat']['name']              = 'env-timeFormat';
$conf_metadata['env']['timeFormat']['value']             = 'H:i:s';
$conf_metadata['env']['timeFormat']['attributes']        = 'size=10';
$conf_metadata['env']['timeFormat']['rule_msg'][0]       = CONF_INSTALL_ENV_DATEFORMAT_RULE_1_0;
$conf_metadata['env']['timeFormat']['rule_type'][0]      = 'required';
$conf_metadata['env']['timeFormat']['rule_attribute'][0] = '';

$conf_metadata['env']['job_timeout']['description'] = CONF_INSTALL_ENV_JOB_TIMEOUT_DESCRIPTION;
$conf_metadata['env']['job_timeout']['type']        = 'text';
$conf_metadata['env']['job_timeout']['name']        = 'env-job_timeout';
$conf_metadata['env']['job_timeout']['value']       = '0';
$conf_metadata['env']['job_timeout']['attributes']  = 'size=10';
$conf_metadata['env']['job_timeout']['rule_msg'][0]       = CONF_INSTALL_ENV_JOB_TIMEOUT_RULE_1_0;
$conf_metadata['env']['job_timeout']['rule_type'][0]      = 'numeric';
$conf_metadata['env']['job_timeout']['rule_attribute'][0] = '';

/* XML-RPC configuration */
$conf_metadata['xmlrpc']['description'] = CONF_INSTALL_XMLRPC_DESCRIPTION;

$conf_metadata['xmlrpc']['timeout']['description']       = CONF_INSTALL_XMLRPC_TIMEOUT_DESCRIPTION;
$conf_metadata['xmlrpc']['timeout']['type']              = 'text';
$conf_metadata['xmlrpc']['timeout']['name']              = 'xmlrpc-timeout';
$conf_metadata['xmlrpc']['timeout']['value']             = '3600';
$conf_metadata['xmlrpc']['timeout']['attributes']        = 'size=10';
$conf_metadata['xmlrpc']['timeout']['rule_msg'][0]       = CONF_INSTALL_XMLRPC_TIMEOUT_RULE_1_0;
$conf_metadata['xmlrpc']['timeout']['rule_type'][0]      = 'numeric';
$conf_metadata['xmlrpc']['timeout']['rule_attribute'][0] = '';
?>