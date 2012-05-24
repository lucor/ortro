<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML-RPC services
 * 
 * PHP version 5
 * 
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category XML-RPC
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once realpath(dirname(__FILE__) . '/../init.inc.php');
require_once 'XML/RPC/Server.php';
require_once 'authUtil.php';

$exec_job_sig = array(array('struct', 'string', 'string', 'string'));
$exec_job_doc = 'Allows to execute a job. It accepts three string parameters: ' .
                'the job label, username and password.<br/>' .
                'Returns a struct contains: result (boolean),  msg_exec (string), '. 
                'txt_attachment (string), html_attachment (string).';

/**
 * Allows to execute a job. 
 * It accepts three string parameters:
 * the job label, username and password.
 * Returns a struct contains: result (boolean), msg_exec (string),
 * txt_attachment (string), html_attachment (string).
 *
 * @param XML_RPC_Message $msg The XML RPC Message
 * 
 * @return XML_RPC_Response
 */
function execJob($msg) 
{
    $job_label  = $msg->getParam(0)->scalarval();
    $session_id = $msg->getParam(1)->scalarval();
    
    $authUtil      = new AuthUtil();
    $authenticated = $authUtil->checkSessionId($session_id);
    if ($authenticated) {
        $cronUtil = new CronUtil();
        $ret      = $cronUtil->runJobRPC($job_label);
    } else {        
        $ret = array('result' => new XML_RPC_Value(0, 'boolean'),
                     'msg_exec' => new XML_RPC_Value('Session expired. '.
                                                     'Please login.',
                                                     'string'));
    }
    $result = new XML_RPC_Value($ret, 'struct');

    return new XML_RPC_Response($result);
}

$get_job_basic_info_sig = array(array('string', 'int','string'));
$get_job_basic_info_doc = 'Get all info for a job. It accepts two parameters: ' . 
                          'the job id (int), the session id (string).<br/>' .
                          'Returns a string contains: the info resulset serialized'. 
                          ' on success.';

/**
 * Get all info for a job. 
 * It accepts two string parameters:
 * the job id (int), the session id (string).
 * Returns a string contains: the info resulset serialized on success.
 *
 * @param XML_RPC_Message $msg The XML RPC Message
 * 
 * @return XML_RPC_Response
 */
function getJobBasicInfo($msg) 
{
    $job_id     = $msg->getParam(0)->scalarval();
    $session_id = $msg->getParam(1)->scalarval();
    
    $authUtil      = new AuthUtil();
    $authenticated = $authUtil->checkSessionId($session_id);
    if ($authenticated) {
        $dbUtil    = new DbUtil();
        $dbh       = $dbUtil->dbOpenConnOrtro();
        $job_infos = $dbUtil->dbQuery($dbh, 
                                       $dbUtil->getJobBasicInfo($job_id), 
                                       MDB2_FETCHMODE_ASSOC);
        $dbh       = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        
        if (PEAR::isError($job_infos)) {
            $ret = 'false';
        } else {
            $ret = serialize($job_infos);
        }
    } else {        
        $ret = 'false';
    }
    
    $result = new XML_RPC_Value($ret, 'string');

    return new XML_RPC_Response($result);
}

$get_archived_job_result_sig = array(array('string', 'int','string'));
$get_archived_job_result_doc = 'Get all archived results for a job. ' .
                               'It accepts two parameters: the job id (int), '.
                               'the session id (string).<br/>' .
                               'Returns a string contains: the archived job '.
                               'resulset serialized on success.';

/**
 * Get all archived results for a job.
 * It accepts two parameters: the job id (int), the session id (string).
 * Returns a string contains: the archived job resulset serialized on success.
 *
 * @param XML_RPC_Message $msg The XML RPC Message
 * 
 * @return XML_RPC_Response
 */
function getArchivedJobResult($msg) 
{
    $job_id     = $msg->getParam(0)->scalarval();
    $session_id = $msg->getParam(1)->scalarval();
    
    $authUtil      = new AuthUtil();
    $authenticated = $authUtil->checkSessionId($session_id);
    if ($authenticated) {
        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConnOrtro();
        
        $job_results = $dbUtil->dbQuery($dbh, 
                                         $dbUtil->getArchivedJobResults($job_id), 
                                         MDB2_FETCHMODE_ASSOC);
        $dbh         = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        
        if (PEAR::isError($job_results)) {
            $ret = 'false';
        } else {
            $ret = serialize($job_results);
        }
    } else {        
        $ret = 'false';
    }
    
    $result = new XML_RPC_Value($ret, 'string');

    return new XML_RPC_Response($result);
}

$do_login_sig = array(array('string', 'string', 'string'));
$do_login_doc = 'Authenticate the user. It accepts two string '.
                'parameters: the username and password.<br/>' .
                'Returns the session id (string).';

/**
 * Authenticate the user.
 * t accepts two string parameters: the username and password.
 * Returns the session id (string).
 *
 * @param XML_RPC_Message $msg The XML RPC Message
 * 
 * @return XML_RPC_Response
 */
function doLogin($msg) 
{
    $username = $msg->getParam(0)->scalarval();
    $password = $msg->getParam(1)->scalarval();
    $authUtil = new AuthUtil();
    $userInfo = $authUtil->login($username, $password);
    if (PEAR::isError($userInfo)) {
        $ret = 'false';
    } else {        
        $ret = $authUtil->createSessionId();
    }
    $result = new XML_RPC_Value($ret, 'string');

    return new XML_RPC_Response($result);
}

$do_logout_sig = array(array('boolean', 'string'));
$do_logout_doc = 'Destroy the current session. It accepts a string parameter:'.
                 ' the session_id.<br/>' .
                 'Return true on success.';

/**
 * Destroy the current session.
 * It accepts a string parameter: the session_id.
 * Return true on success.
 *
 * @param XML_RPC_Message $msg The XML RPC Message
 * 
 * @return XML_RPC_Response
 */
function doLogout($msg) 
{
    $session_id = $msg->getParam(0)->scalarval();
    $authUtil   = new AuthUtil();
    $ret        = $authUtil->destroySessionId($session_id);
    $result     = new XML_RPC_Value($ret, 'boolean');
    return new XML_RPC_Response($result);
}

$map = array('exec_job' =>
             array('function'  => 'exec_job',
                   'signature' => $exec_job_sig,
                   'docstring' => $exec_job_doc),
             'get_archived_job_result' =>
             array('function'  => 'get_archived_job_result',
                   'signature' => $get_archived_job_result_sig,
                   'docstring' => $get_archived_job_result_doc),
             'get_job_basic_info' =>
             array('function'  => 'get_job_basic_info',
                   'signature' => $get_job_basic_info_sig,
                   'docstring' => $get_job_basic_info_doc),
             'do_logout' =>
             array('function'  => 'do_logout',
                   'signature' => $do_logout_sig,
                   'docstring' => $do_logout_doc),
             'do_login' =>
             array('function'  => 'do_login',
                   'signature' => $do_login_sig,
                   'docstring' => $do_login_doc));
new XML_RPC_Server($map);
?>
