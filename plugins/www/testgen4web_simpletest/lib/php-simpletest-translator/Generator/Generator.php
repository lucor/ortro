<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : Generator.php
     * created    : Thu 29 Sep 2005 04:17:02 PM PDT
     * 
     * @category 
     * @package TestGen4Web_Generator
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

    require_once __TESTGEN4WEB_ROOT . '/Config.php';
    require_once __TESTGEN4WEB_ROOT . '/Util/Util.php';
    require_once __TESTGEN4WEB_ROOT . '/Parser/ActionParser.php';

    /** 
     * Code Generator base class.
     * 
     * @category 
     * @package TestGen4Web_Generator
     * @author Nimish Pachapurkar <npac@spikesource.com>
     * @version $Revision: $
     * @copyright Copyright (C) 2004-2006 SpikeSource, Inc.
     * @license http://www.spikesource.com/license.html Open Software License v2.1
     * @link 
     */
    abstract class TestGen4Web_Generator_Generator {
/*{{{ Members */

        protected $_parser;
        protected $_filepath;
        protected $_actions;
        protected $_pattern;
        protected $_genClassName;
        protected $_genString;
        protected $_genFile;
        protected $_targetDir;
        protected $_logger;

/*}}}*/
/*{{{ public function __construct() */

        public function __construct($generatorType="simpletest") {
            $this->_parser =& new TestGen4Web_Parser_ActionParser();
            // Matches INPUT[@ID="user"] 
            // Matches INPUT[@TYPE="password" and @NAME="pass"]
            $this->_pattern = '/([^\[]*)\[@([^=]*)="([^"]*)"( and @([^=]*)="([^"]*)")?\]/';

            global $logger;
            $this->_logger =& $logger;
        }

/*}}}*/
/*{{{ public function setFilePath() */

        public function setFilePath($filepath) {
            $this->_filepath = $filepath;
            $this->_parser->setInputFile($this->_filepath);
            $this->_genClassName = $this->getGeneratedClassName();
        }

		public function setProxyConnection($proxyHost, $proxyUser, $proxyPassword) {
	            $this->_proxyHost = $proxyHost;
		    	$this->_proxyUser = $proxyUser;
		    	$this->_proxyPassword = $proxyPassword ;
		}

/*}}}*/
/*{{{ public function generate() */
        
        public function generate() {
            $this->_parser->parse();
            $doc =& $this->_parser->getDocumentArray();
            $this->_actions =& $doc['TG4W'];
            $this->makeTargetDir();
        }

/*}}}*/
/*{{{ public function getElementFromXpath() */
        public function getElementFromXpath($xpath) {
            if(empty($xpath)) {
                return false;
            }

            $element = array();

            $pos = strrpos($xpath, "/");
            if($pos !== false && $pos < strlen($xpath)) {
                $lastPart = substr($xpath, $pos+1);
            }
            else {
                $lastPart = $xpath;
            }
            $matches = array();
            preg_match($this->_pattern, $lastPart, $matches);

            if(is_array($matches) && count($matches) > 0) {
                // Remove unwanted matches
                if(isset($matches[0])) {
                    array_splice($matches, 0, 1);
                    if(isset($matches[3])) {
                        array_splice($matches, 3, 1);
                    }
                }
                $this->_logger->debug("Array matches: " .  print_r($matches, true));
                for($i = 0; $i < count($matches); $i++) {
                    if($i == 0) {
                        // This should be the HTML tag
                        $element['TAG'] = $matches[$i];
                    }
                    else if ($i%2 != 0) {
                        // Rest all are name value pairs
                        if(isset($matches[$i+1])) {
                            $element[$matches[$i]] = $matches[$i+1];
                        }
                        else {
                            $element[$matches[$i]] = false;
                        }
                    }
                }
            }
            else {
                $element['TAG'] = "*";
                $element['*'] = false;
            }
            return $element;
        }

/*}}}*/
/*{{{ protected function getGeneratedClassName() */

        protected function getGeneratedClassName($filepath) {
            $genClassName = trim(basename($this->_filepath, '.xml'));
            if(!empty($genClassName)) {
                $genClassName = str_replace('_', '', $genClassName);
                $genClassName = str_replace('-', '', $genClassName);
                $genClassName = str_replace('.', '', $genClassName);
                $genClassName = str_replace(' ', '', $genClassName);
                $genClassName = str_replace('\\', '', $genClassName);
                $firstLetter = substr($genClassName, 0, 1);
                if(is_numeric($firstLetter)) {
                    $genClassName = "NaN_" . $genClassName;
                }
            }
            else {
                $genClassName = 'MyWebTest';
            }

            return ucfirst($genClassName);
        }

/*}}}*/
/*{{{ protected function makeTargetDir() */

        protected function makeTargetDir() {
            global $util;
            if(empty($this->_targetDir)) {
                $this->_targetDir = "testgen";
            }
            return $util->makeDirRecursive($this->_targetDir);
        }

/*}}}*/
/*{{{ Getters and Setters */

        public function getTargetDir() {
            return $this->_targetDir;
        }

        public function setTargetDir($targetDir) {
            $this->_targetDir = $targetDir;
        }

        public function getGeneratedFileName() {
            return $this->_genFile;
        }

/*}}}*/
    }
?>
