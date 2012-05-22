<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Show message template
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
if (isset($_SESSION['action_msg'])) {
    showMessage($_SESSION['action_msg'], $_SESSION['type_msg']);
    unset($_SESSION['action_msg']);
    unset($_SESSION['type_msg']);
}
?>
