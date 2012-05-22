<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Telnet Custom Script  Plugin, allows to execute command or script on
 * remote machine accessible via telnet.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Danilo Alfano <ph4ntom@users.sourceforge.net> 
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

require_once realpath(dirname($argv[0])) . '/../../init.inc.php';
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil   = new CronUtil($request_type);
$job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
$parameters = $job_infos['parameters'];
set_error_handler("errorHandler");

//###### End required core code ######

try {

    //---- Start plugin code -----
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                                 ' with id=' . $id_job);
    
    //---- Retrieve job parameters for execution ----//

    $ip       = $job_infos['ip'];
    $path     = dirname($argv[0]);    
    $hostname = $job_infos['ip'];
    $port     = $parameters['telnet_custom_script_port'];

    if ( ! isset ($port) || $port == "" ) $port=23;

    $user   = $job_infos['identity']['username'];
    $pwd    = $job_infos['identity']['password'];
    $script = $parameters['telnet_custom_script_script'];   
  

    $command_string = $path . '/telnet.sh ' . $hostname . ' ' 
                                            . $port     . ' ' 
                                            . $user     . ' ' 
                                            . $pwd      . ' ' 
                                            . ' "echo ; echo Begin Telnet Custom;'
                                            . $script   . '"'
                                            . ' 2>&1'        ; 

    exec($command_string, $stdout, $exit_code);

    // Searching the begin of the script
    $searchKey = array_search('Begin Telnet Custom', $stdout);
    // Slice aray after Begin script 
    $stdout_sliced = array_slice($stdout, $searchKey);
    // Eliminate the Begin script tag
    array_shift($stdout_sliced);
    // Eliminate the end script "connection closed"
    array_pop($stdout_sliced);

    //---- Set the attachments -----//
    $attachments['txt']  = implode("\n", $stdout_sliced);
    $attachments['html'] = implode("<br/>", $stdout_sliced);
    
    $loggerPlugin->trace('DEBUG', 'id_job=' . $id_job . "\n" .
                                  'exit_code=' . $exit_code . "\n" .
                                  "Message:\n" . $attachments['txt']);
    
    $msg_exec = $attachments['txt'];
    
    //---- For debug uses ----//
    //$msg_exec =  $command_string;
    
    //  $msg_exec = $parameters['telnet_custom_script_is_error']; 
    //---- Check Result if Threshold is specified or return exit code value ----//

    //---- Retrieve job parameter for result check ----//
    $operator  = $parameters['telnet_custom_script_operator'];
    $threshold = $parameters['telnet_custom_script_threshold'];
    $is_error  = $parameters['telnet_custom_script_is_error']; 
    $testValue = $attachments['txt']; 

    if (!isset ($threshold) || $threshold == "") {
        if ($exit_code == '0') {
            $result = '1';
        } else {
            $loggerPlugin->trace('ERROR', 'id_job=' . $id_job . "\n" .
                                          'exit_code=' . $exit_code . "\n" .
                                          "Message:\n" . $attachments['txt']);
            $result = '0';
        }
    } else {
         $result = notifyUtil::test($testValue, $threshold, $operator, $is_error);
    }

    //---- Archive job result ----//
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['telnet_custom_script_retention'];
    var_dump($retention_data);         

    //---- End plugin code ----//

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
