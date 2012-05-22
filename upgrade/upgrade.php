<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Upgrade Ortro from 1.3.4 to 1.4.0
 * 
 * PHP version 5
 * 
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */



$current_path = realpath(dirname($argv[0])) . DIRECTORY_SEPARATOR;
require_once ($current_path . '../conf/init.php');

if (!file_exists(ORTRO_CONF . 'configure.php')){
    echo "Upgrade error: configure.php not found!\n\n";
    exit;
}



//$ldap_auth_enabled = false;
//if (file_exists(ORTRO_CONF . 'configure_ldap.php')){
//    require_once ORTRO_CONF . 'configure_ldap.php';
//    if ($conf['ldap']['login'] == '1') {
//        $ldap_auth_enabled = true;
//    }
//    unset($conf);
//}
//
//include ORTRO_CONF . 'configure.php';
//require_once 'Pear/Config.php';
//
//$c = new Config();
//
//if ($ldap_auth_enabled) {
//    $conf['auth']['default']  = 'LDAP';
//    $conf['auth']['fallback'] = '1';
//} else {
//    $conf['auth']['default']  = 'MDB2';
//    $conf['auth']['fallback'] = '0';
//}
//
//$conf['id'] = 'ortro_' . md5(uniqid());
//$conf['env']['job_timeout'] = '0';
//
//$c->parseconfig($conf, 'phparray');
//$result = $c->writeConfig(ORTRO_CONF . 'configure.php', 'phparray');
//

include ORTRO_CONF . 'configure.php';
require_once 'langUtil.php';
require_once 'dbUtil.php';
i18n('template', 'install.php');

// create a db connection connection 
$db_name   = $GLOBALS['conf']['db']['database'];
$db_prefix = $GLOBALS['conf']['db']['tableprefix'];

$dbUtil = new DbUtil();

$dsn = $dbUtil->setDSN(array('phptype'  => $GLOBALS['conf']['db']['phptype'], 
                             'hostspec' => $GLOBALS['conf']['db']['host']  . ":" .  
                                           $GLOBALS['conf']['db']['port'],
                             'database' => $GLOBALS['conf']['db']['database'],
                             'username' => $GLOBALS['conf']['db']['username'],
                             'password' => $GLOBALS['conf']['db']['password']));

// Update the database schema

echo UPGRADE_DB_SCHEMA . "\n"; 

require_once 'MDB2/Schema.php';

$options = array('use_transactions'=> false);

$variables = array(
    //Specific ortro options
    'db_name'       => $db_name,
    'table_prefix'  => $db_prefix,
    'user_language' => 'en',
);


$schema =& MDB2_Schema::factory($dsn, $options);

if (PEAR::isError($schema)) {
    $error = $schema->getMessage();
    
} else {
    // first run with queries disabled to make sure everything is allright
    $disable_query = false;

    $previous_schema = $schema->getDefinitionFromDatabase();

    $op = $schema->updateDatabase('../www/template/install/ortro.schema.xml', $previous_schema, $variables, $disable_query);

    if (PEAR::isError($op)) {
        $error = $op->getMessage();
        $error = $op->getUserInfo();
    }
}

if (isset($error)) {
    var_dump($error);
} else {
    echo "Upgrade done.\n";
}

$schema->disconnect();

echo "Please remove the www/template/install directory.\n";

?>
