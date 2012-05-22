<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : ActionParser.php
     * created    : Thu 29 Sep 2005 04:19:25 PM PDT
     * 
     * @category 
     * @package TestGen4Web_Parser
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
        define('__TESTGEN4WEB_ROOT', dirname(dirname(__FILE__)));
    }

    require_once __TESTGEN4WEB_ROOT . '/Parser/XMLParser.php';

    class TestGen4Web_Parser_ActionParser extends TestGen4Web_Parser_XMLParser {

        protected $_lastActionId;
        protected $_lastFilePath;

        public function __constructor() {
            parent::__constructor();
            $this->_docroot = array();
        }

        public function startHandler($xp, $name, $attrs) {
            parent::startHandler(&$xp, &$name, &$attrs);
            switch($name) {
            case "TG4W":
                // Get the metadata
                $this->_docroot['TG4W'][ATTRIBUTES] =& $this->handleAttrTag($name, $attrs);
                break;

            case "ACTION":
                $this->_docroot['TG4W']['ACTIONS'][] = array();
                $this->_lastActionId = count($this->_docroot['TG4W']['ACTIONS']) - 1;
                $this->_docroot['TG4W']['ACTIONS'][$this->_lastActionId][ATTRIBUTES] =& $this->handleAttrTag($name, $attrs);
                break;
            }
        }

        public function endHandler($xp, $name) {
            switch($name) {
            case "XPATH":
                $this->_docroot['TG4W']['ACTIONS'][$this->_lastActionId]['XPATH'] = trim($this->getCData());
                break;

            case "VALUE":
                $this->_docroot['TG4W']['ACTIONS'][$this->_lastActionId]['VALUE'] = trim($this->getCData());
                break;
            }
            parent::endHandler(&$xp, &$name, &$attrs);
        }
    }

    // __MAIN__
    if(is_array($argv) && in_array('__main__', $argv) && strpos($argv[0], basename(__FILE__)) !== false) {
        $file = "/opt/npac/svn-root/views/npac/testgen4web/recorder/samples/logintest4.xml";
        $parser =& new TestGen4Web_ActionParser();
        if(is_readable($file)) {
            $parser->setInputFile($file);
            $parser->parse();
            print "After parsing\n";
            print_r($parser->getDocumentArray());
        }
    }
?>
