<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : Config.php
     * created    : Thu 29 Sep 2005 04:19:09 PM PDT
     * 
     * @category 
     * @package 
     * @author Nimish Pachapurkar <npac@spikesource.com>
     * @copyright Copyright (C) 2004-2006 SpikeSource, Inc.
     * @license http://www.spikesource.com/license.html Open Software License v2.1
     * @version $Revision: $
     * @link 
     *
     * modifications:
     *
     */
    
    if(!defined('__TESTGEN4WEB_ROOT')) {
        define('__TESTGEN4WEB_ROOT', dirname(__FILE__));
    }

    require_once __TESTGEN4WEB_ROOT . '/Util/Logger.php';

    $logger =& new TestGen4Web_Util_Logger();
    $logger->setLevel("LOG_DEBUG");

?>
