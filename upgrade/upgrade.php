<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Upgrade Ortro from 1.2.x to 1.3.2
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

$mdb2 =& MDB2::connect($dsn);

if (PEAR::isError($mdb2)) {
    echo $mdb2->getMessage() . "\n" . $mdb2->getDebugInfo();
    exit;
}
// Update the database schema

echo UPGRADE_DB_SCHEMA . "\n"; 
/* TODO USE MDB2 SCHEMA */
$query_alter_table = array(
    "alter table " . $db_prefix . "host add column status char(1)",
    "alter table " . $db_prefix . "system add column status char(1)"
);

for ($i = 0; $i < sizeof($query_alter_table); $i++) {
    echo $query_alter_table[$i] . "\n";
    $affected =& $mdb2->exec($query_alter_table[$i]);
    if (MDB2::isError($affected)) {
        echo "Query failed: " . $affected->getMessage() ."\n";
        exit;
    }
    echo "Done" . "\n";
}

if (PEAR::isError($result)) {
    echo $result->getMessage() . "\n" . $result->getDebugInfo();
} else {
    echo "Upgrade done.\n";
}
?>
