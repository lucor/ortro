<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple functions to check installation requirements
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once 'System.php';

/**
 * Checks for php presence and version
 * 
 * @return array Test results 
 */
function checkPHP($php_path = null)
{
    $required_version      = '5.1';
    $result['php_version'] = phpversion() < $required_version ? 0 : 1;
    //check for php-cli
    if ($php_path != null) {
        $rev_php_path = strrev($php_path);
        if ($rev_php_path[0] != DIRECTORY_SEPARATOR) {
            $php_path .= DIRECTORY_SEPARATOR;
        }
    } elseif (isset($_SESSION['installation']['env']['php_path'])) {
        $php_path = $_SESSION['installation']['env']['php_path'];
    } else {
        $php_path = '';
    }
    
    $cmdLine = escapeshellarg($php_path . 'php') . " -r 'echo phpversion() < " .
               $required_version . " ? 0 : 1;'";
    exec($cmdLine, $stdout, $exit_code);
    if ($exit_code == '0') {
        //php is in the path
        $result['php-cli_version'] = $stdout[0];
        $result['php_path']        = $php_path;
        $result['test_result']     = 1;
        
        $_SESSION['installation']['env']['php_path'] = $php_path;
    } else {
        $result['php_path']    = 'no';
        $result['test_result'] = 0;
    }
    return $result;
}

/**
 * Check for ssh presence
 * 
 * @return array Test results 
 */
function checkSSH()
{
    //check for openssh client
    if (isset($_REQUEST['ssh_path'])) {
        $ssh_path     = $_REQUEST['ssh_path'];
        $rev_ssh_path = strrev($ssh_path);
        if ($rev_ssh_path[0] != DIRECTORY_SEPARATOR) {
            $ssh_path .= DIRECTORY_SEPARATOR;
        }
    } elseif (isset($_SESSION['installation']['env']['ssh_path'])) {
        $ssh_path = $_SESSION['installation']['env']['ssh_path'];    
    } else {
        $ssh_path = '';
    }
    
    if (!System::which($ssh_path . 'ssh')) {
        $result['ssh_path']    = 'no';
        $result['test_result'] = 0;
    } else {
        //ssh is installed
        $result['ssh_path']    = $ssh_path;
        $result['test_result'] = 1;
        
        $_SESSION['installation']['env']['ssh_path'] = $ssh_path;
    }
    return $result;
}

/**
 * Check for required extensions
 * 
 * @return array Test results 
 */
function checkExtensions()
{
    
    $required_ext_list = array('bz2',
                               'date',
                               'ftp',
                               'pcre',
                               'xml',
                               //'zip',
                               'zlib'
                               );                               

    $result['missed_extensions'] = array();
                                   
    foreach ($required_ext_list as $ext_to_check) {
        if (!extension_loaded($ext_to_check)) {
            $result['missed_extensions'][] = $ext_to_check;
        }
    }
    
    if (count($result['missed_extensions']) > 0) {
        $result['test_result'] = 0;
    } else {
        $result['test_result'] = 1;
    }
    return $result;
}

/**
 * Check for installed DB driver
 * 
 * @return array Test results 
 */
function checkDBDriver()
{           
    if (function_exists('mysql_connect')) {
        $result['db_supported']['mysql'] = 'MySQL';
    }
    if (function_exists('mysqli_connect')) {
        $result['db_supported']['mysqli'] = 'MySQLi';
    }
    if (function_exists('oci_connect')) {
        $result['db_supported']['oci8'] = 'Oracle';
    }
    if (function_exists('pg_connect')) {
        $result['db_supported']['pgsql'] = 'PostgreSQL';
    }
    if (function_exists('sqlite_open')) {
        $result['db_supported']['sqlite'] = 'SQLite';
    }
    if (function_exists('mssql_connect')) {
        $result['db_supported']['mssql'] = 'Microsoft SQL Server';
    }
    if (function_exists('ibase_connect')) {
        $result['db_supported']['ibase'] = 'InterBase';
    }
    
    return $result;
}
?>
