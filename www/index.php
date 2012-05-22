<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Index page.
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

session_start();
header('Cache-Control: private,
                       must-revalidate, 
                       max-age=3600, 
                       post-check=3600, 
                       pre-check=3600');
require_once 'init.inc.php';
require_once 'Pear/HTML/Table.php';
require_once 'Pear/HTML/QuickForm.php';
require_once 'htmlUtil.php';
require_once 'dbUtil.php';
require_once 'langUtil.php';
require_once 'authUtil.php';


if (!file_exists(ORTRO_CONF . 'configure.php')) {
    //show the setup page
    include ORTRO_TEMPLATE . 'install/index.php';
    exit;
}

$auth = new AuthUtil();

$language = $auth->getSessionData('language'); 
if (!$language) {
    //setting the default language for the session
    $language = ORTRO_DEFAULT_LANGUAGE;
    if (isset($conf['env']['lang'])) {
        $language = $conf['env']['lang'];
    } 
    $auth->setSessionData('language', $language);
}

if (!$auth->isAuthorized()) {
    //show the login page
    i18n('template', 'common.php');
    include ORTRO_TEMPLATE . 'login.php';
    exit;
} elseif (isset($_REQUEST['logout'])) {
    //logout
    $auth->logout();
    i18n('template', 'action_msg.php');
    header('location:?');
    exit;
} else {
    //Get the system id from request for apply policy
    $policy_id_system     = false;
    $user_is_system_admin = false;
    $user_is_system_guest = false;
    $user_is_admin        = false;
    $user_is_guest        = false;
    $error_code           = '';

    //Check fo valid categories
    $allowed_cat = array('autodiscovery',
                        'cas_settings',
                        'db',
                        'filemanager',
                        'group',
                        'host',
                        'identity_management',
                        'import_export',
                        'jobs',
                        'ldap_settings',
                        'notify',
                        'plugin',
                        'plugin_notify',
                        'settings',
                        'system',
                        'user',
                        'workflows');

    if (!in_array($_REQUEST['cat'], $allowed_cat)) {
        $error_code = 404;
    }

    //Check fo valid categories
    $allowed_mode = array('action', 'add', 'details', 'edit', 'install', 'view');

    if (!in_array($_REQUEST['mode'], $allowed_mode)) {
        $error_code = 404;
    }

    $_policy = $auth->getSessionData('policy');
    
    if (isset($_REQUEST['systemHost'][0])) {
        //used when you add/modify a system/host
        $policy_id_system = $_REQUEST['systemHost'][0];
    }
    if (isset($_REQUEST['systemHostDb'][0])) {
        //used when you add/modify a db
        $policy_id_system = $_REQUEST['systemHostDb'][0];
    }
    if (isset($_REQUEST['id_system'])) {
        //used when you add/modify a db
        $policy_id_system = $_REQUEST['id_system'];
    }

    if (isset($_REQUEST['id_chk'])) {
        //used when you request action from view
        $policy_id_system = $_REQUEST['id_chk'][key($_REQUEST['id_chk'])];
    }

    if (isset($_policy['SYSTEM_GUEST'])) {
        if (in_array($policy_id_system,
        explode(',', $_policy['SYSTEM_GUEST']))) {
            $user_is_system_guest = true;
        }
    }
    if (array_key_exists('GUEST', $_policy)) {
        $user_is_guest = true;
    }
    if (isset($_policy['SYSTEM_ADMIN'])) {
        if (in_array($policy_id_system,
        explode(',', $_policy['SYSTEM_ADMIN']))) {
            $user_is_admin_for_system = true;
        }
        if (count($_policy['SYSTEM_ADMIN']) > 0) {
            if (count(explode(',',
            $_policy['SYSTEM_ADMIN']))>0) {
                $user_is_system_admin = true;
            }
        }
    }
    if (array_key_exists('ADMIN', $_policy)) {
        $user_is_admin = true;
    }

    if (isset($_REQUEST['download']) && $_REQUEST['download'] != '') {
        $id_system = $_REQUEST['id_system'];
        if ($_REQUEST['cat'] == 'filemanager') {
            $file_path = ORTRO_INCOMING . $id_system . DS;
        } else {
            $file_path = ORTRO_REPORTS . $id_system . DS . $_REQUEST['id_job'];
        }
        $file_to_download = $file_path . DS . $_REQUEST['download'];
        if (is_file($file_to_download) && (strpos($file_to_download, '..') === false)) {
            httpDownload($_REQUEST['download'], $file_to_download);
            exit;
        }
    }

    if (($user_is_admin || $user_is_system_admin) &&
    isset($_REQUEST['cat']) &&
    $_REQUEST['cat'] == 'ssh_public_key') {
        //allows to download the ssh public key and exit
        $ssh_public_key = $GLOBALS['conf']['env']['ssh_keyname'] . '.pub';
        httpDownload($ssh_public_key, ORTRO_SSH_PATH . $ssh_public_key);
        exit;
    }

    /* Define the policy for the categories */
    
    if (isset($_REQUEST['action']) && $_REQUEST['action'] != '') {
        // An action was requested...
        i18n('template', 'action_msg.php');
        if ($user_is_system_admin || $user_is_admin ||
        ($_REQUEST['profile'] == 'edit' &&
         $auth->getSessionData('userid') == $_REQUEST['id_user'])) {
            // Administrator for this system or modify the owner profile
            $template = ORTRO_TEMPLATE . 'action' . DS .
            $_REQUEST['cat'] . '.php';
            if (is_file($template) && (strpos($template, '..') === false)) {
                include $template;
                if ($_REQUEST['ajax'] == 1) {
                    exit;
                }
            } else {
                $error_code = 404;
            }
        } else {
            $error_code = 403;
        }
    } else {
        if (!$user_is_admin) {
            switch ($_REQUEST['cat']) {
            case 'identity_management':
                if (!$user_is_system_admin) {
                    $error_code = 403;
                }
                break;
            case 'system':
            case 'identity_management':
            case 'userGroup':
            case 'group':
                //mode not allowed
                $error_code = 403;
                break;
                
            case 'user':
                // allows to edit the own profile
                if ($_REQUEST['profile'] != 'edit') {
                    $error_code = 403;
                }
                break;
                
            default:
                //access to the category is allowed
                break;
            }
            if (!isset($error_code) && !$user_is_system_admin) {
                // other profile check for policy on mode
                switch ($_REQUEST['mode']) {
                case 'add':
                    if (!$user_is_system_admin) {
                        $error_code = 403;
                    }
                    break;
                    
                case 'view':
                case 'refresh_page':
                case 'details':
                case 'detail':
                    break;
                    
                default:
                    $error_code = 403;
                    break;
                    
                }
            }
        }
    }

    switch ($error_code) {
    case 403:
        i18n('template', 'action_msg.php');
        $error_msg = MSG_PERMISSION_DENIED;
        break;
    case 404:
        $error_msg = MSG_PAGE_NOT_FOUND;
        break;
    default:
        break;
    }
    i18n('template', 'common.php');
    i18n('template', 'header.php');
    include ORTRO_TEMPLATE . 'header.php';
    ?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr valign="top">
        <td width="1%">
    <?php      
    i18n('template', 'menu.php');
    include ORTRO_TEMPLATE . 'menu.php';
    ?>
        </td>
        <td width="99%" class="content">
    <?php
        include ORTRO_TEMPLATE . 'showMessage.php';
        include ORTRO_TEMPLATE . 'body.php';
    ?>
        </td>
    </tr>
    </table>
    <?php
    include ORTRO_TEMPLATE . 'footer.php';
}
//print_r($_SESSION);
?>