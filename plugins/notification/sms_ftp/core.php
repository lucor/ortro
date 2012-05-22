<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMS (FTP Upload).
 * Uploads a brief text message via FTP, 
 * such as is used for SMS (Short Message System)
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
 */

require_once 'logUtil.php';

global $conf;
require_once ORTRO_CONF_PLUGINS . 'notification_sms_ftp.php';

/**
 * Uploads a brief text message via FTP, 
 * such as is used for SMS (Short Message System)
 * 
 * @param array $elements    The user form values 
 * @param array $attachments The files to attach
 * 
 * @return void
 */
function sms_ftpNotify($elements,$attachments)
{
    $logger = new LogUtil('sms_ftp_notification');

    $logger->trace('DEBUG', 'Sending sms using ftp interface');
    $ftp_server  = $GLOBALS['conf']['sms_ftp']['host'];
    $port        = $GLOBALS['conf']['sms_ftp']['port'];
    $file_prefix = $GLOBALS['conf']['sms_ftp']['file_prefix'];
    
    // set up basic connection
    $conn_id = ftp_connect($ftp_server, $port);

    // login with username and password
    $login_result = ftp_login($conn_id, 
                              $elements['identity']['username'], 
                              $elements['identity']['password']);

    // check connection
    if ((!$conn_id) || (!$login_result)) {
        $logger->trace('ERROR', "FTP connection has failed! " . 
                                "Attempted to connect to $ftp_server for user " . 
                                $elements['identity']['username']);
        $exit_code = 1;
    } else {
        $logger->trace('DEBUG', "Connected to $ftp_server, for user " .
                                $elements['identity']['username']);
        $exit_code = 0;
    }

    //create the temporary sms message
    $tempDir = ORTRO_NOTIFICATION_PLUGINS . 'tmp/';
    if (!is_dir($tempDir)) {
        mkdir($tempDir);
    }
    $tempFile = $file_prefix . 'sms_' . 
                $elements['id_job'] . '_' . 
                time() . rand();
    @unlink($tempDir . $tempFile); // just in case
    
    $fh           = fopen($tempDir . $tempFile, 'w+');
    $data_message = $elements['sms_ftp_message'];
    if ($elements['sms_ftp_attach_result'] == '1' && 
        array_key_exists('txt', $attachments)) {
        $data_message .= $attachments['txt'];
    } 
    if ($elements['sms_ftp_attach_timestamp'] == '1') {
                $data_message .= ' - Date: ' . date('Y-m-d H:i');
    }
    fwrite($fh, $data_message);
    fclose($fh);
    chmod($tempDir . $tempFile, 0777);

    // try to change the directory to somedir if defined
    if (isset($elements['sms_ftp_dir']) && $elements['sms_ftp_dir'] != '') {
        if (ftp_chdir($conn_id, $elements['sms_ftp_dir'])) {
            $logger->trace('DEBUG', "cd " . ftp_pwd($conn_id));
        } else {
            $logger->trace('ERROR', "Couldn't change directory");
        }
    }

    // upload the file
    $upload = ftp_put($conn_id, $tempFile, $tempDir . $tempFile, FTP_ASCII);
    
    // check upload status
    if (!$upload) {
        $logger->trace('ERROR', 'FTP upload has failed!');
    } else {
        $logger->trace('DEBUG', "Uploaded message to $ftp_server as $tempFile");
    }
    // close the FTP stream
    ftp_close($conn_id);
    
    //remove temp file
    unlink($tempDir . $tempFile);
    $logger->trace('DEBUG', 'Done.');
}
?>