<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * The ssh class.
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
 
require_once 'logUtil.php';

/**
 * SSH Class
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class SSHUtil
{
    // {{{ constructor
    /**
     * SshUtil Class constructor.
     * This flavour of the constructor only enable logging 
     * identifying it by the name of the class file.
     * 
     * @access public
     */
    function sshUtil() 
    {
        $this->logger = new LogUtil('sshUtil.php');
    }
    // }}}
    
    // {{{ sshConn()
    /**
     * This method executes a command on a remote host using ssh
     *
     * @param string  $user        The user to login
     * @param string  $ip          The ip address of the remote host
     * @param string  $port        The port to connect
     * @param string  $path_script The command script to execute
     * @param boolean $local       If the script to execute is stored locally or
     *                             on the remote machine (default)
     * @param boolean $run_bg      Run the command in background
     * 
     * @return array Contains the standard output and exit code
     */
    function sshConn($user, $ip, $port, $path_script, $local = false, $local_parameters = '')
    {
        if ($port == '') {
            $port = '22';        
        }

        if ($GLOBALS['conf']['env']['ssh_StrictHostKeyChecking'] == 'no') {
            $StrictHostKeyChecking = ' -o StrictHostKeyChecking=no ';
        } else {
            $StrictHostKeyChecking = '';
        }

        if ($local === false) {
            $script = '"' . $path_script . '" 2>&1';
        } else {
            if (!is_file($path_script)) {
                $msg = "The script $path_script was not found on the local server";
                return  array('stdout' => array($msg), 'exit_code' => '1' );
            }
            $script = '"/bin/sh -s ' . $local_parameters .
                      ' 2>&1" < ' . escapeshellarg($path_script);
        }


        $cmdLine = 'nohup ' . escapeshellarg($GLOBALS['conf']['env']['ssh_path'] . 'ssh') . 
                   ' -i ' . 
                   escapeshellarg(ORTRO_SSH_PATH . 
                                  $GLOBALS['conf']['env']['ssh_keyname']) .
                   $run_bg .
                   $StrictHostKeyChecking .
                   ' -p ' . 
                   escapeshellarg($port) . 
                   ' ' .
                   escapeshellarg($user) . 
                   '@' . 
                   escapeshellarg($ip) . 
                   ' ' .
                   $script;
        
        $this->logger->trace('DEBUG', $cmdLine);
        
        exec($cmdLine, $stdout, $exit_code);
        
        if ($exit_code != '0') {
            $this->logger->trace('ERROR', 'exit_code=' . $exit_code . "\n" .
                                          "Message:\n" . implode("\n", $stdout));
        }
        
        $result = array('stdout' => $stdout, 'exit_code' => $exit_code );
        return $result;        
    }
    // }}}
}
?>
