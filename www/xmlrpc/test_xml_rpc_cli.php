<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML-RPC client sample
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

?>
<?php
die();//Example file comment or rmove this line if you want use it. 

require_once 'XML/RPC.php';

$p1 = new XML_RPC_Value($argv[1], "string");
$p2 = new XML_RPC_Value($argv[2], "string");
$p3 = new XML_RPC_Value($argv[3], "string");

$params = array($p1, $p2, $p3);

$msg = new XML_RPC_Message('execJob', $params);

$cli = new XML_RPC_Client('/ortro/xmlrpc/', 'localhost');
$cli->setDebug(1); 
$resp = $cli->send($msg);

if (!$resp) {
    echo 'Communication error: ' . $cli->errstr;
    exit;
}

if (!$resp->faultCode()) {
    $val = $resp->value();
    //echo "result: " . $val->serialize();
    $result = $val->structmem("result");
    echo "job result: " . $result->scalarval();
    echo "\n";
    $msg_exec = $val->structmem("msg_exec");
    echo "job msg_exec: " . $msg_exec->scalarval();
    echo "\n";
    $txt_attachment = $val->structmem('txt_attachment');
    if (isset($txt_attachment)) {
        echo "TXT attachment: " . $txt_attachment->scalarval();
        echo "\n";
    }
    $html_attachment = $val->structmem('html_attachment');
    if (isset($html_attachment)) {
        echo "HTML attachment: " . $html_attachment->scalarval();
        echo "\n";
    }

} else {
    /*
     * Display problems that have been gracefully cought and
     * reported by the xmlrpc.php script.
     */
    echo 'Fault Code: ' . $resp->faultCode() . "\n";
    echo 'Fault Reason: ' . $resp->faultString() . "\n";
}
?>
