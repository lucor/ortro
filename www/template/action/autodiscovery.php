<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Autodiscovery actions.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$id_system = $_REQUEST['id_system'];
$id_db     = '0';

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view      = false;
$redirect_to_host_view = false;

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
    case 'add':
        $msg[] = '<br/>';
        foreach ($_REQUEST['id_chk'] as $id_chk) {
            //check for unique ip/hostname
            $hostname = $_REQUEST['hostname_' . $id_chk];
            $ip       = $_REQUEST['ip_' . $id_chk];

            $rows   = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsHost($ip, $hostname));
            $result = $rows[0][0];

            if ($result != 0) {
                //ip/hostname alreay used
                $msg[]      = $hostname . ' (' . $ip . ')<br/>';
                $action_msg = MSG_ACTION_IP_HOSTNAME_ALREADY_USED .
                implode('- ', $msg);
                $type_msg   = 'warning';
                $error      = true;
                break;
            }
        }
        break;
    case 'scan':
        break;
    default:
        $action_msg       = MSG_ACTION_NOT_VALID;
        $type_msg         = 'warning';
        $error            = true;
        $redirect_to_view = true;
        break;
}

if (!$error) {
    // No error found !!!
    $redirect_to_view = false;

    switch ($_REQUEST['action']) {
        case 'scan':
            require_once 'System.php';
            require_once 'Net/Nmap.php';

            //Define the target to scan
            $target = explode(' ', $_REQUEST['target']);

            $options = array();
            $options['nmap_binary'] = System::which('nmap');
            if (isset($_REQUEST['nmap_binary']) && $_REQUEST['nmap_binary'] != '') {
                $options['nmap_binary'] = $_REQUEST['nmap_binary'];
            }

            try {
                $nmap = new Net_Nmap($options);
                //Enable os detection if required
                if ($_REQUEST['os_detection']) {
                    $nmap->enableOptions(array('os_detection' => true));
                }
                //Scan target
                $res = $nmap->scan($target);

                /*
                 * $hosts and $failed_to_resolve are used
                 * in the edit template.
                 */
                //Parse XML Output to retrieve Hosts Object
                $hosts = $nmap->parseXMLOutput();
                //Get failed hosts
                $failed_to_resolve = $nmap->getFailedToResolveHosts();
                if (count($failed_to_resolve) > 0) {
                    i18n('template', 'common.php');
                    $action_msg = FAILED_TO_RESOLVE . 
                                  implode(', ', $failed_to_resolve);
                    $type_msg   = 'warning';
                }
            } catch (Net_Nmap_Exception $ne) {
                //echo $ne->getMessage();
                $action_msg = $ne->getMessage();
                $type_msg   = 'warning';

                $redirect_to_view = true;
            }
            break;
        case 'add':
            /* ADD HOST */
            foreach ($_REQUEST['id_chk'] as $id_chk) {
                //check for unique ip/hostname
                $hostname = $_REQUEST['hostname_' . $id_chk];
                $ip       = $_REQUEST['ip_' . $id_chk];
                //add a new host
                $dbUtil->dbExec($dbh, $dbUtil->setHost($ip, $hostname));
                $id_host = $dbh->lastInsertID();
                $dbUtil->dbExec($dbh,
                $dbUtil->setSystemHostDb($id_system, $id_host, $id_db));
            }
            $redirect_to_host_view = true;
            $action_msg = MSG_ACTION_HOST_ADDED;
            $type_msg   = 'success';
            break;
    }
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=autodiscovery&mode=view');
    exit;
} elseif ($redirect_to_host_view) {
    unset($_REQUEST);
    header('location:?cat=host&mode=view&filter_system=' . $id_system);
    exit;
}
?>