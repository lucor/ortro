<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Ortro environment settings
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

//########### ENV SETTINGS ###########
// DON'T MODIFY THE LINES BELOW !!!! #
//####################################

define('ORTRO_VERSION', '1.3.5');
define('ORTRO_DEFAULT_LANGUAGE', 'en');

define('DS', DIRECTORY_SEPARATOR);
define('ORTRO_PATH', realpath(dirname(__FILE__) . DS . '..') . DS);

define('ORTRO_CONF', ORTRO_PATH . 'conf' . DS);
define('ORTRO_CONF_PLUGINS', ORTRO_CONF . 'plugins' . DS);
define('ORTRO_SSH_PATH', ORTRO_CONF .'.ssh' . DS);
define('ORTRO_SESSION_SAVE_PATH', ORTRO_CONF .'session' . DS);

define('ORTRO_DATA', ORTRO_PATH . 'data' . DS);
define('ORTRO_FILES', ORTRO_DATA . 'files' . DS);
define('ORTRO_INCOMING', ORTRO_DATA . 'incoming' . DS);
define('ORTRO_REPORTS', ORTRO_DATA . 'reports' . DS);
define('ORTRO_SQLITE_DB', ORTRO_DATA . 'db' . DS);
define('ORTRO_ATTACHMENTS', ORTRO_DATA . 'attachments' . DS);

define('ORTRO_LANG', ORTRO_PATH . 'lang' . DS);

define('ORTRO_LIB', ORTRO_PATH . 'lib' . DS);
define('ORTRO_LIB_PEAR', ORTRO_LIB . 'Pear' . DS);

define('ORTRO_LOG', ORTRO_PATH . 'log' . DS);
define('ORTRO_LOG_PLUGINS', ORTRO_LOG . 'plugins' . DS);

define('ORTRO_PLUGINS', ORTRO_PATH . 'plugins' . DS);
define('ORTRO_NOTIFICATION_PLUGINS', ORTRO_PLUGINS . 'notification' . DS);

define('ORTRO_TEMP', ORTRO_PATH . 'tmp' . DS);

define('ORTRO_WEB', ORTRO_PATH . 'www' . DS);
define('ORTRO_TEMPLATE', ORTRO_WEB . 'template' . DS);
define('ORTRO_INSTALL', ORTRO_TEMPLATE . 'install' . DS);

//### ADD LIBS TO LIB_PATH ###
//Avoid problems with magic_quotes_runtime
@ini_set('magic_quotes_runtime', false);
//Disable display error
@ini_set('display_startup_errors', false);
@ini_set('display_errors', false); 

set_include_path(".:".ORTRO_LIB.":".ORTRO_LIB_PEAR.":".ORTRO_PLUGINS);

//prepare config array()
global $conf;
$conf = array();

if (file_exists(ORTRO_CONF . 'configure.php')) {
    include ORTRO_CONF . 'configure.php';
}

/**
 * Throw new exception on error
 * 
 * @param int    $errno      Contains the level of the error raised, as an integer.
 * @param string $errstr     Contains the error message
 * @param string $errfile    Contains the filename that the error was raised in
 * @param int    $errline    Contains the line number the error was raised at
 * @param array  $errcontext An array that points to the active symbol 
 *                           table at the point the error occurred. 
 *                           In other words, errcontext will contain 
 *                           an array of every variable that existed in the scope
 *                           the error was triggered in.
 * 
 * @return boolean
 * 
 */
function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
    if ($errno == E_WARNING || $errno == E_PARSE || $errno == E_ERROR) {
        throw new Exception($errstr, $errno);
        return true;
    } else {
        return false;
    }
}
?>
