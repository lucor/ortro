<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : SimpleTestGenerator.php
     * created    : Thu 29 Sep 2005 04:18:22 PM PDT
     * 
     * @category 
     * @package TestGen4Web_Generator_SimpleTest
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
        define('__TESTGEN4WEB_ROOT', dirname(dirname(dirname(__FILE__))));
    }

    require_once __TESTGEN4WEB_ROOT . '/Generator/Generator.php';
    require_once __TESTGEN4WEB_ROOT . '/Generator/SimpleTest/SimpleTestActions.inc.php';

    class TestGen4Web_Generator_SimpleTest_SimpleTestGenerator extends TestGen4Web_Generator_Generator {
        /*{{{ Members */
        protected $_stub;
        protected $_include;
        protected $_functions;

        /*}}}*/
        /*{{{ public function __construct() */

        public function __construct() {
            parent::__construct();
            $this->_stub = __TESTGEN4WEB_ROOT . "/Generator/SimpleTest/Stub/MyWebTest.php";
            $this->_include = __TESTGEN4WEB_ROOT . "/Generator/SimpleTest/Stub/simpletest.inc.php";
            $this->initializeFunctions();
        }

        /*}}}*/
        /*{{{ public function generate() */

        public function generate() {
            parent::generate();

            $this->processSimpleTest();
        }

        /*}}}*/
        /*{{{ protected function initializeFunctions() */

        protected function initializeFunctions() {
            global $SIMPLETEST_ACTIONS;
            $this->_functions =& $SIMPLETEST_ACTIONS;
        }

        /*}}}*/
        /*{{{ protected function getFunctionString() */

        protected function getFunctionString(
            $action, &$element, $param2=false
        ) {
            $param1 = false;
            if(is_array($element)) {
                foreach($element as $key => $value) {
                    switch($key) {
                    case 'TAG':
                        $tag = $value;
                        break;

                        //only one of these
                    case 'CDATA':
                        // Remove second param
                        $param2 = false;

                    case 'NAME':
                    case 'VALUE':
                    case 'ID':
                    case '*':
                        $name = $key;
                        $param1 = $value;
                        break;

                    default:
                        $name = false;
                    }
                }
            }
            $this->_logger->debug("[Action: " . $action . "] [Tag: " . $tag . "] [Name: " . $name . "] [Param1: " . $param1 . "]", __FILE__, __LINE__);

            $functionCall = "";
            if(isset($this->_functions[$action])) {
                $curAction =& $this->_functions[$action];
		if($action == 'wait-for-ms') {
                            $functionCall .= $curAction['*']['*']['function'] . '(' . $param2 / 1000 . "); \n";
                } elseif(isset($curAction[$tag])) {
                    $curTag =& $curAction[$tag];
                    if($name && isset($curTag[$name])) {
			if($action == 'wait-for-ms') {
                            $functionCall .= $curTag[$name]['function'] . '(';
			} else {
                            $functionCall .= '$this->' . $curTag[$name]['function'] . '(';
                        }
                        if($curTag[$name]['params'] == 1) {
                            // if only one parameter is allowed
                            if(!empty($param1)) {
                                // ... and first param is set
                                // ignore the second param
                                $param2 = false;
                            }
                        }
                        // Assign params
                        if(!empty($param1)) {
                            $functionCall .= '"' . $param1 . '"';
                        }
                        if(!empty($param1) && !empty($param2)) {
                            $functionCall .= ', ';
                        }
                        if(!empty($param2)) {
                            if($action == 'wait-for-ms') {
                                $functionCall .= $param2 / 1000;
                            } else {
                                $functionCall .= '"' . $param2 . '"';
			    }
                        }
                        $functionCall .= ');' . "\n";
                    }
                }
            }
            return $functionCall;
        }

        /*}}}*/
        /*{{{ protected function processSimpleTest() */

        protected function processSimpleTest() {
            if(empty($this->_actions)) {
                $this->_logger->warn("No actions to generate",
                    __FILE__, __LINE__);
                return false;
            }

            $this->_genFile = $this->_targetDir . DIRECTORY_SEPARATOR . $this->_genClassName . ".php";

            copy($this->_stub, $this->_genFile);
            copy($this->_include, $this->_targetDir . DIRECTORY_SEPARATOR . basename($this->_include));
            $this->_genString = file_get_contents($this->_genFile);
            $this->processActions();
        }

        /*}}}*/
        /*{{{ protected function processActions() */

        protected function processActions() {

            $code = array();

            for($i = 0; $i < count($this->_actions['ACTIONS']); $i++) {
                $action =& $this->_actions['ACTIONS'][$i];
                $this->_logger->debug("Action is: " . print_r($action, true),
                    __FILE__, __LINE__);
                $action['TYPE'] = strtolower($action['__ATTRIBUTES__']['TYPE']);

                $element =& $this->getElementFromXpath($action['XPATH']);
                $this->_logger->debug("Element: " . print_r($element, true),
                    __FILE__, __LINE__);
                $code[] = $this->getFunctionString(
                    $action['TYPE'], $element, $action['VALUE']
                );
                $this->_logger->debug("Code: " . $code[count($code)-1],
                    __FILE__, __LINE__);
            }
            $this->writeFile($code);
        }

        /*}}}*/
        /*{{{ protected function writeFile() */

        protected function writeFile(&$code) {
            $codeStr = "";
            for($i = 0; $i < count($code); $i++) {
                if(!empty($code[$i])) {
                    $codeStr .= "\t\t\t" . $code[$i];
                }
            }

		    // Check if test is driven throught e proxy connection
		    if (isset($this->_proxyHost)) {
		                 $proxyCode = "\t\t\t" .
			                      '$this->useProxy(\'' . 
				              $this->_proxyHost .     '\',\'' . 
				              $this->_proxyUser .     '\',\'' . 
				              $this->_proxyPassword . '\');';
		    }
		    // set an empty string to replace 
		    else $proxyCode="";

            // Replace the code in template
            $this->_genString = str_replace('//%%PROXY%%//' , $proxyCode , $this->_genString);

            $this->_genString = str_replace('%%CLASS_NAME%%', $this->_genClassName, $this->_genString);
            $this->_genString = str_replace('//%%CODE%%//', $codeStr, $this->_genString);
            file_put_contents($this->_genFile, $this->_genString);
        }
    }

    /*}}}*/
    /*{{{ MAIN */

    // __MAIN__
    if(is_array($argv) && in_array('__main__', $argv) && strpos($argv[0], basename(__FILE__)) !== false) {
        //$file = "/opt/npac/svn-root/views/npac/testgen4web/recorder/samples/logintest4.xml";
        $file = "/opt/npac/svn-root/views/npac/testgen4web/recorder/samples/sugar3.xml";
        $generator =& new TestGen4Web_Generator_SimpleTest_SimpleTestGenerator();
        $generator->setFilePath($file);
        $generator->generate();
    }

    /*}}}*/
?>
