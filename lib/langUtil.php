<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * i18n utilities.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category  Libs
 * @package   Ortro
 * @author    Danilo Alfano <ph4ntom@users.sourceforge.net>
 * @author    Luca Corbo <lucor@ortro.net>
 * @link      http://www.ortro.net
 */

/**
 * Include the language definition file in according with the selected language.
 * If $_SESSION['lang'] variable is not set the installation language will be used (english is the default).
 * If $_SESSION['lang'] is set but the plug in is not yet translated in the selected language is forced to english.
 * @param string $category The category of the plugin or 'template' to translate a page
 * @param string $name The name of the plugin or the template page name
 */

function i18n($category, $name) {

    //Check for malicious include
    if ((strpos($category, '..') !== false) || (strpos($name, '..') !== false)) {
        return false;
    }

    require_once 'authUtil.php';
    //Include common tranlation file for plugin.
    $relative_plugin_language_file_path = '';
    $absolute_plugin_language_file_path = '';
    $absolute_plugin_common_language_file_path = '';
    $relative_plugin_common_language_file_path = DS . 'template' . DS . 'plugin_common.php';

    switch ($category) {
        case 'template':
            //Include tranlation file for page.
            $relative_plugin_language_file_path = DS . 'template' . DS . $name;
            break;
        case 'js':
            //Include tranlation file for page.
            $relative_plugin_language_file_path = DS . 'js' . DS . 'js.php';
            break;
        default:
            //Include tranlation file for plugin.
            $relative_plugin_language_file_path = DS . 'plugins' . DS . $category .
                                                  DS . $name . DS . 'language.php';
            break;
    }

    $language = AuthUtil::getSessionData('language');

    //Trying to load language file in according to session value
    if ($language !== false){
        $absolute_plugin_common_language_file_path = ORTRO_LANG . $language . $relative_plugin_common_language_file_path;
        $absolute_plugin_language_file_path        = ORTRO_LANG . $language . $relative_plugin_language_file_path;
    }

    if (!file_exists($absolute_plugin_language_file_path)){
        //Force to load language file in according to language default value
        $absolute_plugin_common_language_file_path = ORTRO_LANG . ORTRO_DEFAULT_LANGUAGE . $relative_plugin_common_language_file_path;
        $absolute_plugin_language_file_path        = ORTRO_LANG . ORTRO_DEFAULT_LANGUAGE . $relative_plugin_language_file_path;
    }
    if ($category != 'template') {
        @include_once ($absolute_plugin_common_language_file_path);
    }
    @include_once ($absolute_plugin_language_file_path);
}

/**
 * Retrieve all available languages using the current locale.
 * @return array The available languages
 */

function getAvailableLanguages() {
    require_once 'authUtil.php';
    require_once 'I18Nv2/Language.php';

    $i18n = new I18Nv2_Language();
    
    $language_codes = $i18n->getAllCodes();
    
    $lang_dirs = scandir(ORTRO_LANG);
    foreach ($lang_dirs as $lang_available) {
    	if ($lang_available[0] != '.') {
    	    $ortro_languages[$lang_available] = $language_codes[$lang_available];
    	}
    }
    return $ortro_languages;
}
?>