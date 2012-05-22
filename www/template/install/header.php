<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Header Template
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

//Define common table attributes
$table_attributes = 'cellpadding="0" cellspacing="0" border="0" width="100%"';

$hidden_table_attributes = $table_attributes . ' style="display: none"';
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>"
      lang="<?php echo $language; ?>">
<head>
    <title><?php echo TITLE; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>    
    <link rel="stylesheet" type="text/css" href="css/layout.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon"/>
    <script type="text/javascript" charset="utf-8" src="js/ortro.js"></script>
</head>

<body class="ortro-body">
    <div class="wrapper">
        <noscript>
        <center>
        <table class="warning">
            <tr>
                <td align="left" valign="top">
                <img alt="" src="img/warning.png"/>
                </td>
                <td align="left" valign="top"><?php echo JS_NOT_ENABLED; ?></td>
            </tr>
        </table>
        </center>
        </noscript>
        <div class="header">
        <table cellpadding="0" cellspacing="0" width="100%" border="0">
                <tr>
                    <td class="img">
                       <img src="img/ortro-logo-small.png" border="0" alt="ortro"/>
                    </td>
                </tr>
        </table>
        </div>