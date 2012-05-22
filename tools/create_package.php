<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Create the Ortro packages starting from svn repository
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Tools
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once '../conf/init.php';
require_once 'Archive/Tar.php';
require_once 'ioUtil.php';
require_once 'langUtil.php';

$input_dir             = realpath('..') . DS;
$input_dir_plugins     = $input_dir . 'plugins' . DS;
$output_dir            = realpath('..') . DS . 'build' . DS;
$output_dir_plugins    = $output_dir . 'plugins' . DS;
$output_dir_ortro      = $output_dir . 'ortro-core' . DS;
$output_dir_ortro_full = $output_dir . 'ortro-full' . DS;

$exclude_list = array('build',
                      '.svn',
                      '.project',
                      'create_package.php',
                      'plugins',
                      'tools',
                      'packages');

$ortro_full_plugin_list = array('database_check_tablespace' ,
                                'database_custom_query' ,
                                'database_custom_query_report' ,
                                'file_create' ,
                                'file_ftp_upload' ,
                                'file_retention' ,
                                'file_read_file' ,
                                'file_search' ,
                                'file_size_check' ,
                                'general_custom_script' ,
                                'general_windows_remote_execution' ,
                                'notification_jabber' ,
                                'notification_mail' ,
                                'notification_sms_ftp' ,
                                'notification_tibco_rvd' ,
                                'notification_tivoli_postemsg' ,
                                'system_cpu_idle' ,
                                'system_file_system_check' ,
                                'system_ping' ,
                                'system_service_check' ,
                                'www_check_uri_response_code' ,
                                'www_testgen4web_simpletest');

$file_ext = '.tar.gz';

if (!is_dir($output_dir)) {
    @mkdir($output_dir, 0755);
    @mkdir($output_dir_ortro, 0755);
    @mkdir($output_dir_ortro_no_plugins, 0755);
}

/**
 * Creates all plugins starting from svn.
 *
 * @param array $ortro_full_plugin_list The list of Plugin to create
 * 
 * @return void
 */
function createPluginsPackage($ortro_full_plugin_list = array())
{
    global $input_dir_plugins,$output_dir_plugins,$file_ext,$exclude_list;
    echo 'Removing old package (if exists)...';
    @removeDirectory($output_dir_plugins);
    @mkdir($output_dir_plugins, 0755);
    echo ' -> Done.' . "\n";
    $dir_contents = scandir($input_dir_plugins);
    foreach ($dir_contents as $category) {
        if (is_dir($input_dir_plugins.$category) &&
        $category != '.' && $category != '..' &&
        $category != '.svn') {
            $sub_dir = $input_dir_plugins . $category . DS;
            //echo 'Entering category "' .  $category . "\"\n";
            $sub_dir_contents = scandir($sub_dir);
            foreach ($sub_dir_contents as $plugin_name) {
                if (is_dir($sub_dir.$plugin_name) &&
                $plugin_name != '.' &&
                $plugin_name != '..' &&
                $plugin_name != '.svn') {
                    if (count($ortro_full_plugin_list)==0 ||
                    in_array($category . '_' . $plugin_name,
                    $ortro_full_plugin_list)) {
                        echo '  Starting to create package for plugin ' .
                        $plugin_name . " ... \n";
                        i18n($category, $plugin_name);
                        include $sub_dir . $plugin_name . DS . 'configure.php';
                        $filename   = $category . '_' .
                        $plugin_name . '-' .
                        $plugin_field[$plugin_name][0]['version'] .
                        $file_ext;
                        $tar_object = new Archive_Tar($output_dir_plugins .
                        $filename, true);
                        $tar_object->createModify($sub_dir . $plugin_name,
                                                  '', 
                        $input_dir_plugins,
                        $exclude_list);

                        //Add the language files starting from svn repository
                        $dir_lang_contents = scandir(ORTRO_LANG);
                        foreach ($dir_lang_contents as $item) {
                            if (is_dir(ORTRO_LANG . $item) &&
                            $item != '.' &&
                            $item != '..' &&
                            $item != '.svn') {
                                $full_path = ORTRO_LANG . $item . DS .
                                             'plugins'. DS .
                                $category . DS .
                                $plugin_name . DS;
                                if (file_exists($full_path)) {
                                    $tar_object->addModify($full_path .
                                                           'language.php', 
                                    $category . DS .
                                    $plugin_name . DS .
                                                           'lang' . DS . $item,
                                    $full_path,
                                    $exclude_list);
                                }
                            }
                        }
                        echo '  Package ' . $filename. " created.\n\n";
                    }
                }
            }
        }
    }
}

/**
 * Create the Ortro core package
 * 
 * @return void
 */
function createOrtroPackage()
{
    global $input_dir, $output_dir_ortro, $file_ext, $exclude_list;
    echo 'Removing old package (if exists)...';
    @removeDirectory($output_dir_ortro);
    @mkdir($output_dir_ortro, 0755);
    echo ' -> Done.' . "\n";
    $filename = 'ortro-core-' . ORTRO_VERSION . $file_ext;
    echo "Starting to create the ortro-core package... \n";
    $tar_object = new Archive_Tar($output_dir_ortro . $filename, true);
    $tar_object->createModify($input_dir,
                              'ortro-core-' . ORTRO_VERSION, 
                              $input_dir,
                              $exclude_list);
    //Add the init.inc.php file
    $list_v[0] = $input_dir . 'conf/init.inc.php';
    $tar_object->addModify($list_v,
                           'ortro-core-' . ORTRO_VERSION . '/bin',
    $input_dir . 'conf/');
    $tar_object->addModify($list_v,
                           'ortro-core-' . ORTRO_VERSION . '/plugins',
    $input_dir . 'conf/');
    $tar_object->addModify($list_v,
                           'ortro-core-' . ORTRO_VERSION . '/www',
    $input_dir . 'conf/');
    echo '  Package ' . $filename. " created.\n\n";
}

/**
 * Create the Ortro full package
 *
 * @return void 
 */
function createOrtroFullPackage()
{
    global $input_dir, $output_dir, $output_dir_plugins;
    global $output_dir_ortro_full, $file_ext, $exclude_list;
    echo 'Removing old package (if exists)...';
    @removeDirectory($output_dir_ortro_full);
    @mkdir($output_dir_ortro_full, 0755);
    echo ' -> Done.' . "\n";
    $filename = 'ortro-full-' . ORTRO_VERSION . $file_ext;
    echo "Starting to create the ortro-full package... \n";
    $tar_object = new Archive_Tar($output_dir_ortro_full . $filename, true);
    $tar_object->createModify($input_dir,
                              'ortro-full-' . ORTRO_VERSION, 
    $input_dir,
    $exclude_list);
    $tar_object->addModify($output_dir_plugins,
                           'ortro-full-' . ORTRO_VERSION . 
                           '/www/template/install/plugins',
    $output_dir_plugins);
    //Add the init.inc.php file
    $list_v[0] = $input_dir. 'conf/init.inc.php';
    $tar_object->addModify($list_v,
                           'ortro-full-' . ORTRO_VERSION . '/bin',
    $input_dir . 'conf/');
    $tar_object->addModify($list_v,
                           'ortro-full-' . ORTRO_VERSION . '/plugins',
    $input_dir . 'conf/');
    $tar_object->addModify($list_v,
                           'ortro-full-' . ORTRO_VERSION . '/www',
    $input_dir . 'conf/');
    echo '  Package ' . $filename. " created.\n\n";
}

$usage = 'Usage: php create_package.php --ortro-full | ' .
         ' --ortro-core | --plugins[=plugin]' . "\n";

if (!isset($argv[1])) {
    echo $usage;
    exit;
}

switch ($argv[1]) {
case '--ortro-core':
    createOrtroPackage();
    break;

case '--plugins':
    createPluginsPackage();
    break;
    
case '--ortro-full':
    createPluginsPackage($ortro_full_plugin_list);
    createOrtroFullPackage();
    break;
    
default:
    //check for single plugin or category
    if ((strpos($argv[1], '--plugins=') === false)) {
        echo $usage;
        exit;
    } else {
        $temp = explode('=', $argv[1]);
        createPluginsPackage(array($temp[1]));
    }
    break;
    
}

echo 'input_dir: ' . $input_dir . "\n";
echo 'output_dir: ' . $output_dir . "\n";
exit;
?>