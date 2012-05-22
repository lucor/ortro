<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FTP upload.
 * Uploads a file to an FTP server.
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
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 * @patch    lucke
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
    $log_prefix   = '[job: ' . $id_job . '] ';
    $loggerPlugin->trace('INFO', $log_prefix . 'Executing job');
    
    $result = 0;
    
    //Get the params required by plugin
    $user = $job_infos['identity']['username'];
    $pwd  = $job_infos['identity']['password'];
    
    $ftp_server = $job_infos['ip'];
    
    $local_dir = $parameters['file_ftp_upload_local_dir'];
    if (isset($local_dir) && trim($local_dir)=='') {
        $id_system = $job_infos['id_system'];
        $local_dir = ORTRO_INCOMING . $id_system;
    }

    $filename     = $parameters['file_ftp_upload_filename'];
    $remote_dir   = $parameters['file_ftp_upload_remote_dir'];
    $tranfer_mode = $parameters['file_ftp_upload_tranfer_mode'];
    $port         = $parameters['file_ftp_upload_port'];
    $way          = $parameters['file_ftp_upload_transfer_way'];
    
    if ($port == '') {
        $port = 21;
    }
    
    //check for existeing file locally
    $full_path = $local_dir . DS . $filename;
    $loggerPlugin->trace('ERROR', "Param : $full_path, $remote_dir, $tranfer_mode, $port, $ftp_server, $user, $pwd"); 

    if (($way != 'get') && (!file_exists($full_path))) {
        $msg_exec = 'File ' . $filename . ' not found!!!';
        $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec); 
        throw new Exception($msg_exec);
    }
    
    // set up basic connection
    $conn_id = ftp_connect($ftp_server, $port);

    // login with username and password
    $login_result = ftp_login($conn_id, $user, $pwd);
    
    // check connection
    if ((!$conn_id) || (!$login_result)) {
        $msg_exec = "FTP connection has failed! Attempted to connect to " .
                    " $ftp_server for user " . $user;
        $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
        throw new Exception($msg_exec);
    } else {
        $loggerPlugin->trace('DEBUG', 
                             "Connected to $ftp_server, for user " .$user);
    }
    
    // try to change the directory to somedir if defined
    if (isset($remote_dir) && $remote_dir != '') {
        if (ftp_chdir($conn_id, $remote_dir)) {
            $loggerPlugin->trace('DEBUG', 
                                 $log_prefix .  "cd " . ftp_pwd($conn_id));
        } else {
            $msg_exec = "Couldn't change directory: " . $remote_dir;
            $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
            throw new Exception($msg_exec);
        }
    }

    // upload the file
    if ($tranfer_mode == 'ascii') {
        $tranfer_mode = FTP_ASCII;
    } else {
        $tranfer_mode = FTP_BINARY;
    }
    
    if ($way == 'get') {
        $upload = ftp_get($conn_id, $full_path, $filename, $tranfer_mode);
    } else {
        $upload = ftp_put($conn_id, $filename, $full_path, $tranfer_mode);
    }
    
    // check upload status
    if (!$upload) {
        $msg_exec = 'FTP upload has failed!';
        $loggerPlugin->trace('ERROR', $log_prefix . $msg_exec);
        throw new Exception($msg_exec);
    } else {
        $loggerPlugin->trace('DEBUG', 
                             "Uploaded message to $ftp_server as $filename");
    }
    // close the FTP stream
    ftp_close($conn_id);
        

    $result   = 1;
    $msg_exec = '';
    $loggerPlugin->trace('INFO', $log_prefix . 'Done.');

    //---- End plugin code -----

} catch (Exception $e) {
    $msg_exec = $e->getMessage();
    $result   = 0;
}
//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, 
                       $id_job, 
                       $result, 
                       $msg_exec, 
                       $attachments, 
                       $output);
//###### End required core code ######
?>