<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Left Menu Box
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

$entry[0]['action']     = 'install';
$entry[0]['title']      = INSTALL_MENU_TITLE;
$view_step['0']['text'] = INSTALL_MENU_WELCOME;
$view_step['1']['text'] = INSTALL_MENU_LICENSE;
$view_step['2']['text'] = INSTALL_MENU_PRE_INSTALL_CHECK;
$view_step['3']['text'] = INSTALL_MENU_DATABASE;
$view_step['4']['text'] = INSTALL_MENU_SSH;
$view_step['5']['text'] = INSTALL_MENU_CRONTAB;
$view_step['6']['text'] = INSTALL_MENU_PLUGINS;
$view_step['7']['text'] = INSTALL_MENU_FINISH;
$entry[0]['step']       = $view_step;

//create menu
$menu = '';

foreach ($entry as $key) {
    $menu .= '<ul><li class="category">' . $key['title'] . '</li>';
    foreach ($key['step'] as $step=>$value) {
        if ($_SESSION['installation_step'] == $step) {
            $menu .= '<li class="install-step-current">';
        } else {
            $menu .= '<li class="install-step">';
        }
        $menu .= $value['text'];
        $menu .= '</li>';
    }
    $menu .= '</ul>';
}
?>

<div class="sidebar">
<?php
    echo $menu;
?>
</div>