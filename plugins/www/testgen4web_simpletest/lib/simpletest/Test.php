<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : Test.php 
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

    class Test extends WebTestCase {

        // Constructor
        function Test() {
            $this->WebTestCase();
        }

        // function
        function testEverything() {
			$this->get("https://apuleio.telecomitalia.local/ortro/");
			$this->assertTitle("ortro - An easy way to make scheduling and system/application monitoring");
			$this->setFieldByName("passwd", "luca");
			//$this->click("Login");
			$this->assertTitle("ortro - An easy way to make scheduling and system/application monitoring");
			$this->clickLink("Systems");
			$this->assertTitle("ortro - An easy way to make scheduling and system/application monitoring");
			$this->setFieldById("id_chk", "true");
			$this->clickLink("Hosts");
			$this->assertTitle("ortro - An easy way to make scheduling and system/application monitoring");

        }

    }

    // __main__
    // Remove the following section if you are including this class
    // as part of a group test elsewhere.

    if(is_array($argv) && strpos($argv[0], basename(__FILE__)) !== false) {
        $testsuite = &new GroupTest("TestGen4Web Generated Web Test Suite");
        $testsuite->addTestClass('Test');
        $testsuite->run(new TextReporter());
    }

?>
