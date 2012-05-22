<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [Solaris] svc Check, checks by SMF command if a service is online
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
 * @author   Danilo Alfano <ph4ntom@user.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

$current_path = realpath(dirname($argv[0]));
require_once $current_path . '/../../init.inc.php';
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil   = new CronUtil($request_type);
$job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
$parameters = $job_infos['parameters'];
set_error_handler("errorHandler");

//###### End required core code ######

//---- Start plugin code -----

try {

    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    //Get the params required by plugin from argv
    $user = $job_infos['identity']['username'];
    $pwd  = $job_infos['identity']['password'];
    
    $input_script   = $parameters['testgen4web_simpletest_input_script'];
    $proxy_host     = $parameters['testgen4web_simpletest_proxy_host'];
    $proxy_user     = $parameters['testgen4web_simpletest_proxy_user'];
    $proxy_password = $parameters['testgen4web_simpletest_proxy_password'];
    
    $proxy_string = '';
    
    if (isset($proxy_host) && $proxy_host != '') {
        $proxy_string = ' --proxy-host=\'' . $proxy_host .
                        '\' --proxy-user=\'' . $proxy_user .
                        '\' --proxy-password=\'' . $proxy_password . '\'';
    }
    
    $path     = dirname($argv[0]);    
    $tempFile = 'Test' . $id_job . time() . rand();
    $tempDir  = $path . DIRECTORY_SEPARATOR;
    
    //Replace username e password in the xml code
    $input_script = str_replace('${USERNAME}', $user, $input_script);
    $input_script = str_replace('${PASSWORD}', $pwd, $input_script);
    
    //Generate the t4g input file
    $fh = fopen($tempDir . $tempFile, 'w+');
    fwrite($fh, $input_script);
    
    fclose($fh);
    chmod($tempDir . $tempFile, 0600);
    
    //Translate the t4g code in simpletest code
    $cmdLine = 'php -d include_path=' . get_include_path() . ' ' . 
               $current_path . 
               '/lib/php-simpletest-translator/PHPGenerator.php --input-file=' .
               $tempDir . $tempFile  . 
               ' --output-dir=' . $current_path . $proxy_string . ' 2>&1'; 
    
    exec($cmdLine, $stdout, $exit_code);
    
    //remove the t4g
    @unlink($tempDir . $tempFile);
    unset($stdout);
    unset($exit_code); 
    
    $cmdLine = 'php ' . $current_path . DS . $tempFile  . '.php PHP_SIMPLETEST_HOME=' . 
               $current_path . '/lib 2>&1';
    
    // Get timestamp
    $start_navigation_time = time();
    //execute the test
    exec($cmdLine, $stdout, $exit_code);
    
    // stop timestamp
    $end_navigation_time = time();
    // test duration
    $elapsed_time = $end_navigation_time - $start_navigation_time;
    
    // Output details
    if ($parameters['testgen4web_simpletest_type'] == 'details') {
          array_shift($stdout);
          array_push($stdout, 'Execution Time for All Tests [s]: ' . 
                              $elapsed_time);
          $attachments['txt']  = implode("\n", $stdout);
          $attachments['html'] = implode("<br/>", $stdout);
    } else {
         $attachments['txt'] = $elapsed_time;
    }
    
    @unlink($tempDir . $tempFile . '.php');
    
    //Define threshold for timeout
    if (isset($parameters['testgen4web_simpletest_threshold']) &&
              $parameters['testgen4web_simpletest_threshold']!='0') {
        $threshold = $parameters['testgen4web_simpletest_threshold'];
    } else {
        $threshold = PHP_INT_MAX ;
    }
    
    if ($exit_code != '0') {
            // the test not finished.
        $loggerPlugin->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . $attachments['txt']);
        $result = '0';
    } else {
        // test finished , navigation has no failures.
        if ((strpos($attachments['txt'], 'FAILURES!!!') === false)) {
            if ($elapsed_time < $threshold) {
                $result = '1';
                $loggerPlugin->trace('INFO', 'id_job=' . 
                                             $id_job .
                                             ' -> Test OK');
            } else {
                $result = '0';
                $loggerPlugin->trace('INFO', 'id_job=' . 
                                             $id_job .
                                             ' -> Test KO, timeout');
            }
        } else {
            // test finished , navigation has errors
            $loggerPlugin->trace('ERROR', 'exit_code=' . 
                                          $exit_code . "\n" .
                                          "Message:\n" . 
                                          $attachments['txt']);
            $result = '0';
        }
    }
    
    $msg_exec = $attachments['txt'];
    
    //---- Archive job result ----
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['testgen4web_simpletest_retention'];

    //---- End plugin code -----

} catch (Exception $e) {
    $cronUtil->traceError($plugin_name, $e);
    $msg_exec = "Plugin exception occourred: " . $e->getMessage() . "\n" .
                "Please contact system administrator";
    
}

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);

if ($retention_data['retention'] > 0 && is_numeric($retention_data['retention'])) {
        //apply retention policy
        $cronUtil->archiveJobResult($id_job, $retention_data);
}
//###### End required core code ######
?>