<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Service Check Plugin.
 * to check if a service on the specified port is a alive.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @author   Marcello Sessa <hunternet@users.sourceforge.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$services = array(21  => 'ftp:21',
                  22  => 'ssh:22',
                  23  => 'telnet:23',
                  25  => 'smtp:25',
                  80  => 'www-http:80',
                  110 => 'pop3:110',
                  143 => 'imap:143',
                  389 => 'ldap:389',
                  443 => 'www-https:443',
                  993 => 's-imap:993',
                  995 => 's-pop:995',
                  1521 => 'oracle:1521',
                  3306 => 'mysql:3306'
        );
?>
