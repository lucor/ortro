<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains the metadata used to generate dinamically the web form
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Core
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

/* LDAP Configuration */
$conf_metadata['ldap']['description'] = CONF_LDAP_DESCRIPTION;

$conf_metadata['ldap']['address_book']['description'] = CONF_LDAP_ADDRESS_BOOK_DESCRIPTION;
$conf_metadata['ldap']['address_book']['type']        = 'select';
$conf_metadata['ldap']['address_book']['name']        = 'ldap-address_book';
$conf_metadata['ldap']['address_book']['value']       = array('0' => DISABLE, '1' => ENABLE);
$conf_metadata['ldap']['address_book']['attributes']  = '';

$conf_metadata['ldap']['server']['description'] = CONF_LDAP_SERVER_DESCRIPTION;
$conf_metadata['ldap']['server']['type']        = 'text';
$conf_metadata['ldap']['server']['name']        = 'ldap-server';
$conf_metadata['ldap']['server']['value']       = 'ldap://directory.localhost:389';
$conf_metadata['ldap']['server']['attributes']  = 'size=30';

$conf_metadata['ldap']['base_dn']['description'] = CONF_LDAP_BASE_DN_DESCRIPTION;
$conf_metadata['ldap']['base_dn']['type']        = 'text';
$conf_metadata['ldap']['base_dn']['name']        = 'ldap-base_dn';
$conf_metadata['ldap']['base_dn']['value']       = 'cn=users,dc=example,dc=com';
$conf_metadata['ldap']['base_dn']['attributes']  = 'size=30';

$conf_metadata['ldap']['bind_dn']['description'] = CONF_LDAP_BIND_DN_DESCRIPTION;
$conf_metadata['ldap']['bind_dn']['type']        = 'text';
$conf_metadata['ldap']['bind_dn']['name']        = 'ldap-bind_dn';
$conf_metadata['ldap']['bind_dn']['value']       = '';
$conf_metadata['ldap']['bind_dn']['attributes']  = 'size=30';

$conf_metadata['ldap']['bind_password']['description'] = CONF_LDAP_BIND_PASSWORD_DESCRIPTION;
$conf_metadata['ldap']['bind_password']['type']        = 'password';
$conf_metadata['ldap']['bind_password']['name']        = 'ldap-bind_password';
$conf_metadata['ldap']['bind_password']['value']       = '';
$conf_metadata['ldap']['bind_password']['attributes']  = 'size=10';

$conf_metadata['ldap']['search_attribute_uid']['description'] = CONF_LDAP_SEARCH_ATTRIBUTE_UID_DESCRIPTION;
$conf_metadata['ldap']['search_attribute_uid']['type']        = 'text';
$conf_metadata['ldap']['search_attribute_uid']['name']        = 'ldap-search_attribute_uid';
$conf_metadata['ldap']['search_attribute_uid']['value']       = 'uid';
$conf_metadata['ldap']['search_attribute_uid']['attributes']  = 'size=10';

$conf_metadata['ldap']['search_attribute_cn']['description'] = CONF_LDAP_SEARCH_ATTRIBUTE_CN_DESCRIPTION;
$conf_metadata['ldap']['search_attribute_cn']['type']        = 'text';
$conf_metadata['ldap']['search_attribute_cn']['name']        = 'ldap-search_attribute_cn';
$conf_metadata['ldap']['search_attribute_cn']['value']       = 'cn';
$conf_metadata['ldap']['search_attribute_cn']['attributes']  = 'size=10';

$conf_metadata['ldap']['search_attribute_mail']['description'] = CONF_LDAP_SEARCH_ATTRIBUTE_MAIL_DESCRIPTION;
$conf_metadata['ldap']['search_attribute_mail']['type']        = 'text';
$conf_metadata['ldap']['search_attribute_mail']['name']        = 'ldap-search_attribute_mail';
$conf_metadata['ldap']['search_attribute_mail']['value']       = 'mail';
$conf_metadata['ldap']['search_attribute_mail']['attributes']  = 'size=10';
?>