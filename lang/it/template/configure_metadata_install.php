<?php

/**
 *
 * AUTOMATICALLY GENERATED CODE - DO NOT EDIT BY HAND
 *
**/
define('CONF_INSTALL_DB_DESCRIPTION', "Configurazione Database");
define('CONF_INSTALL_DB_PHPTYPE_DESCRIPTION', "Tipologia di database utilizzato");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_1', "MySQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_2', "MySQLi");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_3', "Oracle");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_4', "PostgreSQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_5', "SQLite");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_6', "Microsoft SQL Server");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_7', "InterBase");
define('CONF_INSTALL_DB_HOST_DESCRIPTION', "Host");
define('CONF_INSTALL_DB_HOST_VALUE', "localhost");
define('CONF_INSTALL_DB_HOST_RULE_1_0', "Host è obbligatorio");
define('CONF_INSTALL_DB_PORT_DESCRIPTION', "Port");
define('CONF_INSTALL_DB_PORT_RULE_1_0', "Port deve essere un numero");
define('CONF_INSTALL_DB_DATABASE_DESCRIPTION', "Database a cui connettersi.");
define('CONF_INSTALL_DB_DATABASE_VALUE', "ortrodb");
define('CONF_INSTALL_DB_DATABASE_RULE_1_0', "Database è obbligatorio");
define('CONF_INSTALL_DB_USERNAME_DESCRIPTION', "Username");
define('CONF_INSTALL_DB_USERNAME_VALUE', "ortrouser");
define('CONF_INSTALL_DB_USERNAME_RULE_1_0', "Username è obbligatorio");
define('CONF_INSTALL_DB_PASSWORD_DESCRIPTION', "Password");
define('CONF_INSTALL_DB_TABLEPREFIX_DESCRIPTION', "Prefisso della tabella");
define('CONF_INSTALL_DB_TABLEPREFIX_VALUE', "ortro_");
define('CONF_INSTALL_ENV_DESCRIPTION', "Configurazione ambiente");
define('CONF_INSTALL_ENV_LANG_DESCRIPTION', "Lingua di default");
define('CONF_INSTALL_ENV_PHP_PATH_DESCRIPTION', "Il percorso assoluto del programma php-cli.");
define('CONF_INSTALL_ENV_PHP_PATH_RULE_1_0', "Il percorso assoluto di php-cli è obbligatorio");
define('CONF_INSTALL_ENV_SSH_PATH_DESCRIPTION', "Il percorso assoluto del programma ssh.");
define('CONF_INSTALL_ENV_SSH_PATH_RULE_1_0', "Il percorso assoluto di ssh è obbligatorio");
define('CONF_INSTALL_ENV_SSH_KEYNAME_DESCRIPTION', "Il nome della chiave ssh. Default ortro_rsa");
define('CONF_INSTALL_ENV_SSH_KEYNAME_RULE_1_0', "Il nome della chiave ssh è obbligatorio");
define('CONF_INSTALL_ENV_SSH_TYPE_DESCRIPTION', "Il tipo della chiave.");
define('CONF_INSTALL_ENV_SSH_BITS_DESCRIPTION', "Il numero di bits nella chiave. La chiavi di tipo DSA devono essere esattamente di 1024 bits come specificata da FIPS 186-2.");
define('CONF_INSTALL_ENV_SSH_STRICTHOSTKEYCHECKING_DESCRIPTION', "ssh StrictHostKeyChecking. <br/> Specifica se ignorare gli errori con la chive host del server in fase di connessione, <br/> uscire immediatamente, oppure chiedere all'utente se continuare. (Default: no)");
define('CONF_INSTALL_ENV_LOG_LEVEL_DEBUG', DEBUG);
define('CONF_INSTALL_ENV_LOG_LEVEL_INFO', INFO);
define('CONF_INSTALL_ENV_LOG_LEVEL_ERROR', ERROR);
define('CONF_INSTALL_ENV_LOG_LEVEL_DESCRIPTION', "Livello verbosità del Log");
define('CONF_INSTALL_ENV_ZIP_PATH_DESCRIPTION', "Il percorso assoluto del programma zip. Default è /usr/bin/zip");
define('CONF_INSTALL_ENV_ZIP_THRESHOLD_DESCRIPTION', "Se abilitata la modalità di archiviazione, i files saranno compressi se la loro grandezza supereranno il valore specificato (byte)");
define('CONF_INSTALL_ENV_DATEFORMAT_DESCRIPTION', "Formato della data");
define('CONF_INSTALL_DATEFORMAT_RULE_1_0', "Formato della data è obbligatorio");
define('CONF_INSTALL_ENV_TIMEFORMAT_DESCRIPTION', "Formato dell' Orario");
define('CONF_INSTALL_ENV_DATEFORMAT_RULE_1_0', "Orario è obbligatorio");
define('CONF_INSTALL_XMLRPC_DESCRIPTION', "Configurazione dei servizi XML-RPC");
define('CONF_INSTALL_XMLRPC_TIMEOUT_DESCRIPTION', "Timeout della sessione (secondi).");
define('CONF_INSTALL_XMLRPC_TIMEOUT_RULE_1_0', "Timeout della sessione è obbligatorio.");
define('CONF_INSTALL_AUTH_DESCRIPTION',"Configurazione autenticazione");
define('CONF_INSTALL_AUTH_DEFAULT_DESCRIPTION',"Modalità di autenticazione");
define('CONF_INSTALL_AUTH_FALLBACK_DESCRIPTION',"Fallback dall'autenticazione");
define('CONF_INSTALL_ENV_JOB_TIMEOUT_DESCRIPTION', "Valore di timeout per i jobs (minuti). Lascia 0 per disabilitare.");
define('CONF_INSTALL_ENV_JOB_TIMEOUT_RULE_1_0', "Il timeout per il job è obbligatorio.");
?>