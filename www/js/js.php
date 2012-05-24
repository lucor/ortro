<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Authentication and permission management class.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Javascript
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
session_start();
require_once '../init.inc.php';
require_once 'langUtil.php';

i18n('js','');

echo 'JS_LANG="'. JS_LANG . '";'; //used to set lang for FCKEditor
echo 'JS_MSG_SELECT_A_FIELD="' . JS_MSG_SELECT_A_FIELD . '";';
echo 'JS_MSG_SELECT_ONLY_A_FIELD="' . JS_MSG_SELECT_ONLY_A_FIELD . '";';
echo 'JS_MSG_CANNOT_EDIT_LDAP_USER="' . JS_MSG_CANNOT_EDIT_LDAP_USER . '";';
echo 'JS_MSG_CONFIRM_DELETE="' . JS_MSG_CONFIRM_DELETE . '";';
echo 'JS_MSG_CONFIRM_KILL="' . JS_MSG_CONFIRM_KILL . '";';
echo 'JS_MSG_SELECT_A_SYSTEM="' . JS_MSG_SELECT_A_SYSTEM . '";';
?>