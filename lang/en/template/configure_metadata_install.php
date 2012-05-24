<?php

/**
 *
 * AUTOMATICALLY GENERATED CODE - DO NOT EDIT BY HAND
 *
**/
define('CONF_INSTALL_DB_DESCRIPTION', "Ortro Database settings");
define('CONF_INSTALL_DB_PHPTYPE_DESCRIPTION', "Database back end selection");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_1', "MySQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_2', "MySQLi");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_3', "Oracle");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_4', "PostgreSQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_5', "SQLite");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_6', "Microsoft SQL Server");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_7', "InterBase");
define('CONF_INSTALL_DB_HOST_DESCRIPTION', "Host");
define('CONF_INSTALL_DB_HOST_VALUE', "localhost");
define('CONF_INSTALL_DB_HOST_RULE_1_0', "The hostname or IP address is required");
define('CONF_INSTALL_DB_PORT_DESCRIPTION', "Port");
define('CONF_INSTALL_DB_PORT_RULE_1_0', "The database TCP port must be a number");
define('CONF_INSTALL_DB_DATABASE_DESCRIPTION', "Database name");
define('CONF_INSTALL_DB_DATABASE_VALUE', "ortrodb");
define('CONF_INSTALL_DB_DATABASE_RULE_1_0', "The database name is required");
define('CONF_INSTALL_DB_USERNAME_DESCRIPTION', "Username");
define('CONF_INSTALL_DB_USERNAME_VALUE', "ortrouser");
define('CONF_INSTALL_DB_USERNAME_RULE_1_0', "The username is required");
define('CONF_INSTALL_DB_PASSWORD_DESCRIPTION', "Password");
define('CONF_INSTALL_DB_TABLEPREFIX_DESCRIPTION', "Table prefix");
define('CONF_INSTALL_DB_TABLEPREFIX_VALUE', "ortro_");
define('CONF_INSTALL_ENV_DESCRIPTION', "Miscellaneous configuration");
define('CONF_INSTALL_ENV_LANG_DESCRIPTION', "Default language");
define('CONF_INSTALL_ENV_PHP_PATH_DESCRIPTION', "Absolute path to the PHP CLI executable - default /usr/bin/php");
define('CONF_INSTALL_ENV_PHP_PATH_RULE_1_0', "Absolute path to the PHP CLI executable is required");
define('CONF_INSTALL_ENV_SSH_PATH_DESCRIPTION', "Absolute path to the ssh executable - default /usr/bin/ssh");
define('CONF_INSTALL_ENV_SSH_PATH_RULE_1_0', "Absolute path to the ssh executable is required");
define('CONF_INSTALL_ENV_SSH_KEYNAME_DESCRIPTION', "The name of the SSH key - default ortro_rsa");
define('CONF_INSTALL_ENV_SSH_KEYNAME_RULE_1_0', "The name of the ssh-rsa key is required");
define('CONF_INSTALL_ENV_SSH_TYPE_DESCRIPTION', "Encryption type of SSH key");
define('CONF_INSTALL_ENV_SSH_BITS_DESCRIPTION', "The number of bits in the key. Note: DSA keys must be exactly 1024 bits, as specified by FIPS 186-2.");
define('CONF_INSTALL_ENV_SSH_STRICTHOSTKEYCHECKING_DESCRIPTION', "ssh StrictHostKey check<br/>On connecting with ssh, should Ortro ignore errors with a remote host's key, <br/>die immediately, or ask the user if it should continue? (Default: no)");
define('CONF_INSTALL_ENV_LOG_LEVEL_DEBUG', DEBUG);
define('CONF_INSTALL_ENV_LOG_LEVEL_INFO', INFO);
define('CONF_INSTALL_ENV_LOG_LEVEL_ERROR', ERROR);
define('CONF_INSTALL_ENV_LOG_LEVEL_DESCRIPTION', "Log trace level: DEBUG logs most, ERROR logs least");
define('CONF_INSTALL_ENV_ZIP_PATH_DESCRIPTION', "Absolute path to the zip executable - default /usr/bin/zip");
define('CONF_INSTALL_ENV_ZIP_THRESHOLD_DESCRIPTION', "Maximum allowable size of zip archive file (bytes)");
define('CONF_INSTALL_ENV_DATEFORMAT_DESCRIPTION', "Date format");
define('CONF_INSTALL_DATEFORMAT_RULE_1_0', "The Date format is required");
define('CONF_INSTALL_ENV_TIMEFORMAT_DESCRIPTION', "Time format");
define('CONF_INSTALL_ENV_DATEFORMAT_RULE_1_0', "The Date format is required");
define('CONF_INSTALL_XMLRPC_DESCRIPTION', "XML-RPC services configuration");
define('CONF_INSTALL_XMLRPC_TIMEOUT_DESCRIPTION', "Session timeout (seconds)");
define('CONF_INSTALL_XMLRPC_TIMEOUT_RULE_1_0', "The session timeout is required");
define('CONF_INSTALL_AUTH_DESCRIPTION',"Ortro Authentication settings");
define('CONF_INSTALL_AUTH_DEFAULT_DESCRIPTION',"Default authentication method");
define('CONF_INSTALL_AUTH_FALLBACK_DESCRIPTION',"Fallback authentication");
define('CONF_INSTALL_ENV_JOB_TIMEOUT_DESCRIPTION', "Timeout value for jobs (minutes). Leave 0 to disable");
define('CONF_INSTALL_ENV_JOB_TIMEOUT_RULE_1_0', "The job timeout is required");
?>