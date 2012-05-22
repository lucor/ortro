<?php
    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : simpletest.inc.php
     * created    : Wed 19 Oct 2005 11:15:44 AM PDT
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
?>
<?php
    $PHP_SIMPLETEST_REPORT_PATH = false;
    global $PHP_SIMPLETEST_REPORT_PATH;
	for($ii=1; $ii < $argc; $ii++) {
		if(strpos($argv[$ii], "PHP_SIMPLETEST_HOME=") !== false) {
			parse_str($argv[$ii]);
		}
        else if(strpos($argv[$ii], "PHP_SIMPLETEST_REPORT_PATH=") !== false) {
            parse_str($argv[$ii]);
        }
	}
    if(empty($PHP_SIMPLETEST_HOME) || !is_dir($PHP_SIMPLETEST_HOME)) {
        $PHP_SIMPLETEST_HOME = getenv('PHP_SIMPLETEST_HOME');
        if(empty($PHP_SIMPLETEST_HOME) || !is_dir($PHP_SIMPLETEST_HOME)) {
            $msg = "ERROR: Could not locate PHP_SIMPLETEST_HOME [$PHP_SIMPLETEST_HOME]. ";
            $msg .= "Use 'php <filename> PHP_SIMPLETEST_HOME=/path/to/simpletest/home'\n";
            die($msg);
        }
    }
	error_log("PHP_SIMPLETEST_HOME=" . $PHP_SIMPLETEST_HOME);
	$include_path = get_include_path();
	set_include_path($PHP_SIMPLETEST_HOME. PATH_SEPARATOR . $include_path);
    define('__PHP_SIMPLETEST_HOME', $PHP_SIMPLETEST_HOME);
    echo $PHP_SIMPLETEST_REPORT_PATH;
?>
