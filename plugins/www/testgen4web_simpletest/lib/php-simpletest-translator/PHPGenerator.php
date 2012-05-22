<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : PHPGenerator.php
     * created    : Thu 29 Sep 2005 04:18:57 PM PDT
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

    require_once __TESTGEN4WEB_ROOT . '/Config.php';

    function help() {
        echo "Usage: php PHPGenerator.php [options]\n";
        echo "    where options are: \n";
        echo "      --input-file=<input-file>    Input XML recording.\n";
        echo "      [--test-type=<test-type>]    Type of test suite to be generated (defaults to 'simpletest')\n";
        echo "      [--output-dir=<dir-path>]    Output directory where generated source code is placed.\n";
        echo "      [--help]                     Print this message and exit.\n";
	echo "      \n";
	echo "      Proxy settings:                 If you are behind a proxy and no connect directly to internet\n";
	echo "                                      you have to specify these parameters\n";
	echo "      [--proxy-host=<Proxy>]          Proxy host to connect\n";
	echo "      [--proxy-user=<User>]           User used to autenthicate if needed\n";
	echo "      [--proxy-password=<Password>]   User password to autenthicate.\n";
        echo "\n";
    }

    function getTestGeneratorClass($testtype) {
        $testtype = strtolower($testtype);
        switch($testtype) {
        case "simpletest":
            return "/Generator/SimpleTest/SimpleTestGenerator.php";

        default:
            return false;
        }
    }

    $options = array();

    for($ii=1; $ii < $argc; $ii++) {
        if(strpos($argv[$ii], "--input-file=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--input-file'] = $option['--input-file'];
        }
        else if(strpos($argv[$ii], "--test-type=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--test-type'] = $option['--test-type'];
        }
        else if(strpos($argv[$ii], "--output-dir=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--output-dir'] = $option['--output-dir'];
        }
        else if(strpos($argv[$ii], "--proxy-host=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--proxy-host'] = $option['--proxy-host'];
        }
        else if(strpos($argv[$ii], "--proxy-user=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--proxy-user'] = $option['--proxy-user'];
        }
        else if(strpos($argv[$ii], "--proxy-password=") === 0) {
            parse_str($argv[$ii], $option);
            $options['--proxy-password'] = $option['--proxy-password'];
        }
        else if(strpos($argv[$ii], "--help") === 0) {
            help();
            exit(0);
        }
    }

    $logger->debug(print_r($options, true));
    $options['--input-file'] = realpath($options['--input-file']);
    if(empty($options['--input-file'])) {
        die("Input file must be specified.");
    }
    else if(!is_readable($options['--input-file'])) {
        die("Cannot read input file: " . $options['--input-file'] . "\n");
    }
    else if(empty($options['--test-type'])) {
        $options['--test-type'] = "simpletest";
    }

    $logger->info("Parsing recording: " . $options['--input-file']);
    
    $testClass = getTestGeneratorClass($options['--test-type']);
    if(empty($testClass)) {
        die("Unknown test type: " . $options['--test-type'] . "\n");
    }
    else {
        require_once __TESTGEN4WEB_ROOT . $testClass;
    }

    $generator =& new TestGen4Web_Generator_SimpleTest_SimpleTestGenerator();
    $generator->setFilePath($options['--input-file']);
    $generator->setTargetDir($options['--output-dir']);
    $generator->setProxyConnection($options['--proxy-host'], $options['--proxy-user'], $options['--proxy-password']);
    $generator->generate();

    $logger->info("Test Code generated in: " 
        . realpath($generator->getGeneratedFileName()));
?>
