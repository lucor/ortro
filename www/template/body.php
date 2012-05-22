<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Body Template
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

if (isset($_REQUEST['cat']) && isset($_REQUEST['mode'])) {
    if ($_REQUEST['cat'] == 'plugins') {
        //display archived reports or results
        $template = ORTRO_TEMPLATE . 'details' . DS . $_REQUEST['file'] . '.php';
    } else {
        $template = ORTRO_TEMPLATE . $_REQUEST['mode'] . DS . $_REQUEST['cat'] . '.php';
    }
    if (is_file($template) && (strpos($template, '..') === false)) {
        include $template;
    } else {
        showMessage(MSG_PAGE_NOT_FOUND, 'warning');
    }
} else {
    include 'default.php';
}
?>