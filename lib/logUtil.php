<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger class.
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

/**
 * Logger Class
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class LogUtil
{
    
    // {{{ constructor
    /**
     * Constructor
     * 
     * @param string $fileName set a custom name for the log file
     * @param string $logPath  set a custom path for the log file
     * 
     * @return logUtil
     * 
     * @access public
     */
    function logUtil($fileName, $logPath='')
    {
        $this->fileName = $fileName;
        if ($logPath=='') {
            //Use application log file
            if (isset($GLOBALS['conf']['env']['dateFormat'])) {
                $this->logPath = ORTRO_LOG . 'ortro_' . 
                                 date($GLOBALS['conf']['env']['dateFormat']) . 
                                 '.log';
            } else {
                //installation log
                $this->logPath = ORTRO_LOG . 'ortro_install.log';
            }
        } else {
            //Use custom log file
            $this->logPath = $logPath . '_' . 
                             date($GLOBALS['conf']['env']['dateFormat']) . 
                             '.log';
        }
        if (!is_dir(dirname($this->logPath))) {
            mkdir(dirname($this->logPath), 0755, true);
        }
        if (!file_exists($this->logPath)) {
            $logfile = fopen($this->logPath, 'w+');
            fclose($logfile);
        }
        if (isset($GLOBALS['conf']['env']['log_level'])) {
            $this->logLevel = $GLOBALS['conf']['env']['log_level'];
        } else {
            //running installer set to debug
            $this->logLevel = 'DEBUG';
        }
    }
    // }}}
    
    // {{{ trace()
    /**
     * This method writes in the log file.
     * 
     * @param string $traceLevel The tracelevel
     * @param string $message    The message to write
     * 
     * @return void
     */
    function trace($traceLevel,$message)
    {
        if (isset($GLOBALS['conf']['env']['timeFormat'])) {
            $dateFormat = date($GLOBALS['conf']['env']['timeFormat']);
        } else {
            $dateFormat = date('Y-m-d');
        }
        if ($this->getTraceLevel($this->logLevel) >= 
            $this->getTraceLevel($traceLevel)) {
            $logMessage = '[' . $traceLevel . '] ' . $dateFormat . 
                      ' - ' . $this->fileName .
                      ': ' . $message . chr(10);
                
            error_log($logMessage, 3, $this->logPath);
        }
    }
    // }}}
    
    // {{{ getTraceLevel()
    /**
     * This method allows to get the weight of the tracelevel  
     *
     * @param string $traceLevel The tracelevel
     * 
     * @return int The weight
     */
    function getTraceLevel($traceLevel)
    {
        switch ($traceLevel) {
        case 'DEBUG':
            $weight = 2;
            break;
        case 'INFO':
            $weight = 1;
            break;
        case 'ERROR':
            $weight = 0;
            break;
        default:
            $weight = 0;
            break;
        }
        return $weight;    
    }
    // }}}
}
?>