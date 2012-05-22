<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SCP plugin.
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
set_include_path(realpath(dirname($argv[0])) . "/lib/Pear/:" .
                 ini_get("include_path"));
@require_once ORTRO_CONF_PLUGINS . 'file_scp.php';
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

    //Get job info
    $src_host = $job_infos['ip'];
    $src_user   = $parameters['file_scp_src_user'];
    $src_filename = $parameters['file_scp_src_filename'];
    
    $dest_host  = $parameters['file_scp_dest_host'];
    $dest_user  = $parameters['file_scp_dest_user'];
    $dest_dir   = $parameters['file_scp_dest_dir'];
    
    $enable_recursive_copy = $parameters['file_scp_recursive'];
    $enable_compress       = $parameters['file_scp_compress'];

    //Get whitelist/blacklist filter mode
    $filter_mode  = $conf['scp']['mode'];

    if ($filter_mode != 'nofilter') {
        //Filter enabled
        $loggerPlugin->trace('INFO', $log_prefix . 'Enabled filter: ' . $filter_mode);
        //retrieve an array with specified rules
        $rules = array_map('trim', explode("\n", $conf['scp']['rules']));

        //Get the ip:path to check
        $address = $dest_host . ':' . $dest_dir;

        //Check for whitelist/blacklist
        $test = in_array($address, $rules);

        $msg_deny = "The specified host:path ($address) is not allowed.\nPlease contact the System Administrator";

        $loggerPlugin->trace('DEBUG', $log_prefix . 'IP:Path to check: ' . $address);
        $loggerPlugin->trace('DEBUG', $log_prefix . 'List to filter: ' . print_r($rules, true));
        switch ($filter_mode) {
            case 'whitelist':
                if ($test !== true) {
                    throw new Exception($msg_deny);
                }
            case 'blacklist':
                if ($test === true) {
                    throw new Exception($msg_deny);
                }
            default:
                    throw new Exception('Filter mode "' . $filter_mode . '" not supported. Please contact the System Administrator');
                break;
        }
    }
    
    require_once 'Net/OpenSSH.php';
    require_once 'sshUtil.php';

    $options = array('openssh_binary' => 'scp',
                        'identity_file' => null,
                        'option' => null,
                        'port' => 22,
                        'limit' => null,
                        'recursive' => null,
                        'src_config' => array(
                                              array(
                                              'path'=>$src_filename)
                                             ),
                        'dest_config' => array('user'=>$dest_user,
                                               'hostname'=>$dest_host,
                                               'path'=>$dest_dir
                                               ),
                        );

    if ($enable_recursive_copy == 1) {
        $options['recursive'] = 'y';
    }
    
    if ($enable_compress == 1) {
        $options['option'][0] = 'Compression=yes';
    }

    $ssh_client = Net_OpenSSH::factory('Scp', $options);

    $scp_command = $ssh_client->createCommandLine();

    $ssh              = new SSHUtil();
    $start_timer      = time();
    $sshCommandResult = $ssh->sshConn($src_user, $src_host, '22', $scp_command, false);
    $end_timer        = time();
    $stdout           = $sshCommandResult['stdout'];
    $exit_code        = $sshCommandResult['exit_code'];

    $attachments['txt']  = implode("\n", $stdout);
    $attachments['html'] = implode("<br/>", $stdout);

    if ($exit_code == '0') {
        $result = '1';
        $attachments['txt'] = $end_timer - $start_timer;
    } else {
        $loggerPlugin->trace('ERROR', 'id_job=' . $id_job . "\n" .
                                      'exit_code=' . $exit_code . "\n" .
                                      "Message:\n" . $attachments['txt']);
        $result = '0';
    }

    $msg_exec = $attachments['txt'];

     //---- Archive job result ----
    $retention_data['archive_mode'] = 'DB';
    $retention_data['retention']    = $parameters['file_scp_retention'];
    
    $loggerPlugin->trace('INFO', $log_prefix . 'Done.');

    //---- End plugin code -----

} catch (Exception $e) {
    $msg_exec = $e->getMessage();
    $result   = 0;
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