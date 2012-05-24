<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Index Page
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

if (!isset($_SESSION['ortro_id'])) {    
    $_SESSION['ortro_id'] = 'ortro_' . md5(uniqid());
}
$conf['id'] = $_SESSION['ortro_id'];

$language = AuthUtil::getSessionData('language');
$avalaible_langs = getAvailableLanguages();

if (isset($_REQUEST['language']) && $_REQUEST['language'] != '') {
    if (array_key_exists($_REQUEST['language'], $avalaible_langs)) {
        $language = $_REQUEST['language'];
    } else {
        $language = ORTRO_DEFAULT_LANGUAGE;
    }
} elseif (!$language) {
    //setting the default language for the session    
    require_once 'I18Nv2/Negotiator.php';
    $negotiator = new I18Nv2_Negotiator();
    $user_language = $negotiator->getLanguageMatch();
    if (array_key_exists($user_language, $avalaible_langs)) {
        $language = $user_language;
    } else {
        $language = ORTRO_DEFAULT_LANGUAGE;
    }
}

AuthUtil::setSessionData('language', $language);


/**
 * Creates html img code depends on test result
 *
 * @param string $result 1 for success, 0 for error
 *
 * @return string The html code
 */


function showResultImage($result)
{
    if ($result == '1') {
        $img = 'success.png';
    } else {
        $img = 'warning.png';
    }
    $img_html = '<img src="img/' . $img  . '" border="0">';
    return $img_html;
}

i18n('template', 'install.php');
i18n('template', 'common.php');
i18n('template', 'action_msg.php');

if (!isset($_SESSION['installation'])) {
    $_SESSION['installation']      = array('1');
    $_SESSION['installation_step'] = 0;
} else {
    if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
        case 'forward':
            $_SESSION['installation_step'] = $_SESSION['installation_step'] + 1;
            break;

        case 'back':
            $_SESSION['installation_step'] = $_SESSION['installation_step'] - 1;
            break;

        default:
            break;
        }
    }
}

$template = $_SESSION['installation_step'] . '.php';

$msg = '';
if (!is_file(dirname(__FILE__) . DS . $template)) {
    $msg      = MSG_PAGE_NOT_FOUND;
    $template = '0.php';
    
    $_SESSION['installation_step'] = 0;
}

require 'header.php';
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr valign="top">
        <td width="1%"><?php       
        require 'menu.php';
        ?></td>
        <td width="99%" class="content">
<?php  
if ($msg != '') {
    showMessage($msg, 'warning');
}
require $template;
?>
        </td>
    </tr>
</table>
<?php
require 'footer.php';
?>