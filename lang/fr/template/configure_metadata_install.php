<?php

/**
 *
 * AUTOMATICALLY GENERATED CODE - DO NOT EDIT BY HAND
 *
**/
define('CONF_INSTALL_DB_DESCRIPTION', "Paramètres de la base de données Ortro");
define('CONF_INSTALL_DB_PHPTYPE_DESCRIPTION', "Sélection du type de base de données");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_1', "MySQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_2', "MySQLi");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_3', "Oracle");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_4', "PostgreSQL");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_5', "SQLite");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_6', "Microsoft SQL Server");
define('CONF_INSTALL_DB_PHPTYPE_VALUE_7', "InterBase");
define('CONF_INSTALL_DB_HOST_DESCRIPTION', "Serveur");
define('CONF_INSTALL_DB_HOST_VALUE', "localhost");
define('CONF_INSTALL_DB_HOST_RULE_1_0', "Le nom du serveur ou l'adresse IP est obligatoire.");
define('CONF_INSTALL_DB_PORT_DESCRIPTION', "Port");
define('CONF_INSTALL_DB_PORT_RULE_1_0', "Le numéro de port TCP de la base de données doit être un nombre.");
define('CONF_INSTALL_DB_DATABASE_DESCRIPTION', "Nom de la base de données");
define('CONF_INSTALL_DB_DATABASE_VALUE', "ortrodb");
define('CONF_INSTALL_DB_DATABASE_RULE_1_0', "Le nom de la base de données est obligatoire.");
define('CONF_INSTALL_DB_USERNAME_DESCRIPTION', "Utilisateur");
define('CONF_INSTALL_DB_USERNAME_VALUE', "ortrouser");
define('CONF_INSTALL_DB_USERNAME_RULE_1_0', "Le nom de l'utilisateur est obligatoire.");
define('CONF_INSTALL_DB_PASSWORD_DESCRIPTION', "Mot de passe");
define('CONF_INSTALL_DB_TABLEPREFIX_DESCRIPTION', "Préfixe des tables");
define('CONF_INSTALL_DB_TABLEPREFIX_VALUE', "ortro_");
define('CONF_INSTALL_ENV_DESCRIPTION', "Paramètres divers");
define('CONF_INSTALL_ENV_LANG_DESCRIPTION', "Langue par défaut");
define('CONF_INSTALL_ENV_PHP_PATH_DESCRIPTION', "Chemin absolu sur l'exécutable PHP-CLI. Par défaut la valeur est /usr/bin/php");
define('CONF_INSTALL_ENV_PHP_PATH_RULE_1_0', "Le chemin absolu sur l'exécutable PHP-CLI est obligatoire.");
define('CONF_INSTALL_ENV_SSH_PATH_DESCRIPTION', "Chemin absolu sur l'exécutable ssh. Par défaut 
/usr/bin/ssh");
define('CONF_INSTALL_ENV_SSH_PATH_RULE_1_0', "Le chemin absolu sur l'exécutable ssh est obligatoire.");
define('CONF_INSTALL_ENV_SSH_KEYNAME_DESCRIPTION', "Le nom de la clé SSH. Par défaut ortro_rsa.");
define('CONF_INSTALL_ENV_SSH_KEYNAME_RULE_1_0', "Le nom de la clé SSH est obligatoire.");
define('CONF_INSTALL_ENV_SSH_TYPE_DESCRIPTION', "Type de codage de la clé SSH.");
define('CONF_INSTALL_ENV_SSH_BITS_DESCRIPTION', "Taille de la clé (en bits). Note : les clés de type DSA doivent être de 1024, comme défini dans FIPS 186-2.");
define('CONF_INSTALL_ENV_SSH_STRICTHOSTKEYCHECKING_DESCRIPTION', "Vérification de StrictHostKey dans ssh.<br/>Lors d'une connexion SSH, est ce que Ortro doit ignorer les erreurs sur la clé d'un serveur distant, abandonner immédiatement ou demander à l'utilisateur si il doit continuer ? Par défaut: non.");
define('CONF_INSTALL_ENV_LOG_LEVEL_DEBUG', DEBUG);
define('CONF_INSTALL_ENV_LOG_LEVEL_INFO', INFO);
define('CONF_INSTALL_ENV_LOG_LEVEL_ERROR', ERREUR);
define('CONF_INSTALL_ENV_LOG_LEVEL_DESCRIPTION', "Niveau de trace: DEBUG niveau maximum, ERROR niveau minimum.");
define('CONF_INSTALL_ENV_ZIP_PATH_DESCRIPTION', "Chemin absolu sur l'exécutable zip. Par défaut /usr/bin/zip.");
define('CONF_INSTALL_ENV_ZIP_THRESHOLD_DESCRIPTION', "Taille maximum pour une archive zip (en octets)");
define('CONF_INSTALL_ENV_DATEFORMAT_DESCRIPTION', "Format de date");
define('CONF_INSTALL_DATEFORMAT_RULE_1_0', "Le format de la date est obligatoire.");
define('CONF_INSTALL_ENV_TIMEFORMAT_DESCRIPTION', "Format de l'heure");
define('CONF_INSTALL_ENV_DATEFORMAT_RULE_1_0', "Le format de l'heure est obligatoire.");
define('CONF_INSTALL_XMLRPC_DESCRIPTION', "Configuration du service XML-RPC");
define('CONF_INSTALL_XMLRPC_TIMEOUT_DESCRIPTION', "Délai d'inactivité de la session (en secondes)");
define('CONF_INSTALL_XMLRPC_TIMEOUT_RULE_1_0', "Le délai d'inactivité de la session est obligatoire.");
define('CONF_INSTALL_AUTH_DESCRIPTION', "Ortro Authentication settings");
define('CONF_INSTALL_AUTH_DEFAULT_DESCRIPTION', "Default authentication method");
define('CONF_INSTALL_AUTH_FALLBACK_DESCRIPTION', "Fallback authentication");

?>