<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Menu Sidebar Template
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

$entry[0]['mode']  ='view';
$entry[0]['title'] = MENU_SYSTEMS_DESCRIPTION;
if (array_key_exists('ADMIN', $_policy)) {
    $view_cat['system']['alt']  = MENU_SYSTEMS_SYSTEMS_ALT ;
    $view_cat['system']['text'] = MENU_SYSTEMS_SYSTEMS_DESCRIPTION;
}
$view_cat['host']['alt']  = MENU_SYSTEMS_HOSTS_ALT;
$view_cat['host']['text'] = MENU_SYSTEMS_HOSTS_DESCRIPTION;
$view_cat['db']['alt']    = MENU_SYSTEMS_DATABASES_ALT;
$view_cat['db']['text']   = MENU_SYSTEMS_DATABASES_DESCRIPTION;
if (isset($user_is_system_admin) || isset($user_is_admin)) {
    $view_cat['identity_management']['alt']  = MENU_SYSTEMS_IDENTITY_ALT;
    $view_cat['identity_management']['text'] = MENU_SYSTEMS_IDENTITY_DESCRIPTION;
}
$view_cat['jobs']['alt']       = MENU_SYSTEMS_JOBS_ALT;
$view_cat['jobs']['text']      = MENU_SYSTEMS_JOBS_DESCRIPTION;
$view_cat['calendars']['alt']       = MENU_SYSTEMS_CALENDARS_ALT;
$view_cat['calendars']['text']      = MENU_SYSTEMS_CALENDARS_DESCRIPTION;
$view_cat['notify']['alt']     = MENU_SYSTEMS_NOTIFICATIONS_ALT;
$view_cat['notify']['text']    = MENU_SYSTEMS_NOTIFICATIONS_DESCRIPTION;
$view_cat['workflows']['alt']  = MENU_SYSTEMS_WORKFLOWS_ALT;
$view_cat['workflows']['text'] = MENU_SYSTEMS_WORKFLOWS_DESCRIPTION;

if (isset($user_is_system_admin) || isset($user_is_admin)) {
    $view_cat['ssh_public_key']['alt']  = MENU_SYSTEMS_SSH_KEY_ALT;
    $view_cat['ssh_public_key']['text'] = MENU_SYSTEMS_SSH_KEY_DESCRIPTION;
    $view_cat['filemanager']['alt']     = MENU_SYSTEMS_FILE_MANAGER;
    $view_cat['filemanager']['text']    = MENU_SYSTEMS_FILE_MANAGER_DESCRIPTION;
}
$entry[0]['categories'] = $view_cat;

if (array_key_exists('ADMIN', $_policy)) {
    $entry[1]['mode']  ='view';
    $entry[1]['title'] = MENU_USERS_GROUPS_DESCRIPTION;

    $view_users['user']['alt']   = MENU_USERS_GROUPS_USERS_ALT;
    $view_users['user']['text']  = MENU_USERS_GROUPS_USERS_DESCRIPTION;
    $view_users['group']['alt']  = MENU_USERS_GROUPS_GROUPS_ALT;
    $view_users['group']['text'] = MENU_USERS_GROUPS_GROUPS_DESCRIPTION;

    $entry[1]['categories'] = $view_users;
    $entry[2]['mode']       ='view';
    $entry[2]['title']      = MENU_SETTINGS_DESCRIPTION;

    $view_settings['settings']['alt']       = MENU_SETTINGS_GENERAL_SETTINGS_ALT;
    $view_settings['settings']['text']      = MENU_SETTINGS_GENERAL_SETTINGS_DESCRIPTION;
    $view_settings['ldap_settings']['alt']  = MENU_SETTINGS_LDAP_SETTINGS_ALT;
    $view_settings['ldap_settings']['text'] = MENU_SETTINGS_LDAP_SETTINGS_DESCRIPTION;
    if (@include_once 'CAS.php') {
        $view_settings['cas_settings']['alt']  = 'Click to view CAS settings';
        $view_settings['cas_settings']['text'] = 'CAS Settings';
    }
    $view_settings['plugin']['alt']         = MENU_SETTINGS_PLUGINS_ALT;
    $view_settings['plugin']['text']        = MENU_SETTINGS_PLUGINS_DESCRIPTION;
    $view_settings['plugin_notify']['alt']  = MENU_SETTINGS_NOTIFICATION_PLUGIN_ALT;
    $view_settings['plugin_notify']['text'] = MENU_SETTINGS_NOTIFICATION_PLUGIN_DESCRIPTION;

    $entry[2]['categories'] = $view_settings;
    
    $entry[3]['mode']  ='view';
    $entry[3]['title'] = MENU_TOOLS_DESCRIPTION;

    $view_tools['autodiscovery']['alt']  = MENU_TOOLS_AUTODISCOVERY_ALT;
    $view_tools['autodiscovery']['text'] = MENU_TOOLS_AUTODISCOVERY_DESCRIPTION;
    $view_tools['import_export']['alt']  = MENU_TOOLS_IMPORT_EXPORT_ALT;
    $view_tools['import_export']['text'] = MENU_TOOLS_IMPORT_EXPORT_DESCRIPTION;

    $entry[3]['categories'] = $view_tools;
    
}
?>

<div class="sidebar"><?php 
$menu    = '';
$cat_req = '';
if (isset($_REQUEST['cat'])) {
    $cat_req = $_REQUEST['cat'];
}
foreach ($entry as $key) {
    $menu .= '<ul><li class="category">' . $key['title'] . '</li>';

    foreach ($key['categories'] as $cat=>$value) {
        if ($cat_req == $cat) {
            $menu .= '<li class="currentlink">';
        } else {
            $menu .= '<li>';
        }
        $menu .= createHref('index.php?mode=' . $key['mode'] .
                            '&amp;cat=' . $cat, $value['alt'], $value['text']);
        $menu .= '</li>';
    }
    $menu .= '</ul>';
}
echo $menu;
?></div>
<br />
<div
    id="menu-toolbar-box"><!-- Used only to determinate the toolbar menu position -->
</div>
