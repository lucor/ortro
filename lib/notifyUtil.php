<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Handle the notification plugins.
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
require_once 'dbUtil.php';

/**
 * Notification Class
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class NotifyUtil
{
    // {{{ constructor
    /**
     * notifyUtil Class constructor. 
     * This flavour of the constructor only enable logging 
     * identifying it by the name of the class file.
     * 
     * @access public
     */
    function notifyUtil()
    {
        $this->logger = new LogUtil('notifyUtil.php');
    }
    // }}}
    
    // {{{ test()
    /**
     * This method tests a input value against a specified threshold. 
     *
     * @param int    $value     The value to test
     * @param int    $threshold The threshold
     * @param string $operator  The logical operator
     * @param int    $is_error  On a true comparison result an error
     *                          or success value is returned as defined 
     *                          by this value
     * 
     * @return int 1 on success and 0 on error
     */
    function &test($value, $threshold, $operator, $is_error)
    {
        switch ($operator) {
        case '>':
            if ($value > $threshold) {
                $testResult =  1;
            } else {
                $testResult =  0;
            }
            break;
        case '<':
            if ($value < $threshold) {
                $testResult = 1;
            } else {
                $testResult = 0;
            }
            break;
        case '=':
            if ($value == $threshold) {
                $testResult = 1;
            } else {
                $testResult = 0;
            }
            break;
        case '!=':
            if ($value != $threshold) {
                $testResult = 1;
            } else {
                $testResult = 0;
            }
            break;
        }
        
        if ($testResult && $is_error == 0) {
            //set success
            $result = 1;        
        }
        if ($testResult && $is_error == 1) {
            //set error
            $result = 0;        
        }
        if (!$testResult && $is_error == 0) {
            //set error
            $result = 0;        
        }
        if (!$testResult && $is_error == 1) {
            //set success
            $result = 1;        
        }
        return $result;
    }
    // }}}
    
    // {{{ sendNotify()
    /**
     * This method execute the requested notification plugin
     * using the concept of variable functions. 
     *
     * @param array $notify_type The notification info
     * @param int   $result                 The job result
     * @param array $attachments            The attachments for notification
     * 
     * @return void
     */
    function sendNotify($notify_type, $parameters, $attachments)
    {
        //send the notify
        $notify_plugin_path = ORTRO_NOTIFICATION_PLUGINS . 
                              $notify_type . DS;
        @include_once $notify_plugin_path . 'configure.php';
        include_once $notify_plugin_path . 'core.php';
        //add the job_result to notify_info for eventual attach
        $notify_function = $notify_type . 'Notify';
        $notify_function($parameters, $attachments);
    }
    // }}}
}
?>