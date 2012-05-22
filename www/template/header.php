<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Header Template
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
$table_attributes        = 'cellpadding="0" cellspacing="0" border="0" width="100%"';
$hidden_table_attributes = $table_attributes . ' style="display: none"';

$css_layout = 'layout.css';
$css_style  = 'style.css';
if (!$auth->isAuthorized()) {
    $showLoginForm = true;
    $onload        = 'onload="document.logon.username.focus();"';
    $body_tag      = '<body ' . $onload . '>';
    $css_layout    = 'layout_login.css';
    $css_style     = 'style_login.css';
} elseif (isset($_REQUEST['cat']) && 
          (($_REQUEST['mode']=='view' && 
            $_REQUEST['cat']=='jobs') || 
            ($_REQUEST['mode']=='details' && 
             $_REQUEST['cat']=='workflows'))) {
    $onload   = 'onload="getRefreshTime();"';
    $body_tag = '<body class="ortro-body" ' . $onload . '>';
} else {
    $body_tag = '<body class="ortro-body">';
}

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
    <link rel="stylesheet" type="text/css" href="css/<?php echo $css_layout; ?>"/>
    <link rel="stylesheet" type="text/css" href="css/<?php echo $css_style; ?>"/>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon"/>
    <script type="text/javascript" charset="utf-8" 
            src="js/FCKeditor/fckeditor.js"></script>
    <script type="text/javascript" charset="utf-8" src="js/js.php"></script>
    <script type="text/javascript" charset="utf-8" src="js/ortro.js"></script>
    <script type="text/javascript" charset="utf-8" src="js/overlibmws.js"></script>
    <!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.jqplot.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.css" />
    <?php if ($_REQUEST['cat'] == 'calendars') { ?>
        <link type="text/css" href="css/no-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<script type="text/javascript" src="js/ui.core.js"></script>
		<script type="text/javascript" src="js/ui.datepicker.patched.js"></script>
		<script type="text/javascript" src="js/ui.datepicker.ortro.js"></script>
    <?php } ?>
    <?php if ($_REQUEST['cat'] == 'workflows') { ?>
        <link type="text/css" href="css/no-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<script type="text/javascript" src="js/ui.core.js"></script>
		<script type="text/javascript" src="js/ui.tabs.js"></script>
		<script type="text/javascript" src="js/ui.accordion.js"></script>
		<script type="text/javascript" src="js/ortro_wf.js"></script>
    <?php } ?>
</head>

<?php echo $body_tag?>

<!--  Start wrapper div -->
<div class="wrapper">
<?php 
if ($auth->isAuthorized()) { 
    $div   = '<div class="header">';
    $date  = date('H:i:s Y-m-d');
} else {
    $div   = '<div class="header-login">';
    $logo  = '<img src="img/ortro-logo.png" border="0" alt="ortro"/>';
    $date  = '';
}
?>

<!--  Start ortro-header div -->
    <?php echo $div; ?>
    <noscript>
    <center>
    <table class="warning">
        <tr>
            <td align="left" valign="top">
            <img src="img/warning.png" alt="warning"/></td>
            <td align="left" valign="top"><?php echo JS_NOT_ENABLED; ?></td>
        </tr>
    </table>
    </center>
    </noscript>
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr>
            <?php if ($auth->isAuthorized()) { ?>
                <td class="img-ortro">
                    <img src="img/ortro-logo-small.png" alt=""/>    
                </td>
                <td align="right" id="ortro-logout" 
                    nowrap="nowrap">
                    <div id="ortro-tabs">
                    <ul>
                    <li>
                        <?php 
                            echo createHref('?mode=edit&amp;cat=user&amp;' .
                                            'profile=edit', 
                                            HEADER_PROFILE_TOOLTIP, 
                                            HEADER_PROFILE_DESCRIPTION); 
                        ?>
                    </li>
                    <li>|</li>
                    <li>
                        <?php 
                            echo createHref('?logout=out', 
                                              HEADER_LOGOUT, 
                                              HEADER_LOGOUT . 
                                            ' [' . $auth->getSessionData('username') . ']'); 
                        ?>
                    </li>
                    <br/><br/>
                    <li>
                        <?php echo $date;?>
                    </li>
                    </ul>
                    </div>
                </td>
    <?php 
} else {
    ?>
            <td class="img-ortro">
                <?php echo $logo; ?>
            </td>
    <?php 
}
    ?>
        </tr>
    <!-- /tbody -->
    </table>
    </div>
    <!-- End header div -->