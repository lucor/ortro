<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : %%CLASS_NAME%%.php 
     * created    : 
     * 
     * @category 
     * @package 
     * @author 
     * @copyright 
     * @license http://www.spikesource.com/license.html Open Software License v2.1
     * @version $Revision: $
     * @link 
     *
     * modifications:
     *
     */
    
    require_once dirname(__FILE__) . '/simpletest.inc.php';
    require_once 'simpletest/web_tester.php';
    require_once 'simpletest/reporter.php';

    class %%CLASS_NAME%% extends WebTestCase {

        // Constructor
        function %%CLASS_NAME%%() {
            $this->WebTestCase();
        }

        // function
        function testEverything() {
//%%PROXY%%//

//%%CODE%%//
        }

    }

    // __main__
    // Remove the following section if you are including this class
    // as part of a group test elsewhere.

    if(is_array($argv) && strpos($argv[0], basename(__FILE__)) !== false) {
        $testsuite = &new GroupTest("TestGen4Web Generated Web Test Suite");
        $testsuite->addTestClass('%%CLASS_NAME%%');
        $testsuite->run(new TextReporter());
    }

?>
