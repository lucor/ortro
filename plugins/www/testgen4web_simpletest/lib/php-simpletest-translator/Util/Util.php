<?php

    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : Util.php
     * created    : Wed 19 Oct 2005 11:17:24 AM PDT
     * 
     * @category 
     * @package TestGen4Web_Util
     * @author Nimish Pachapurkar <npac@spikesource.com>
     * @copyright Copyright (C) 2004-2006 SpikeSource, Inc.
     * @license http://www.spikesource.com/license.html Open Software License v2.1
     * @version $Revision: $
     * @link 
     *
     * modifications:
     *
     */
    
    class TestGen4Web_Util_Util {

        /*{{{ public function capitalizeDriveLetter() */

        /** 
         * Convert the drive letter to upper case
         * 
         * @param $path Windows path with "c:<blah>"
         * @return String Path with driver letter capitalized.
         * @access public
        */
        public function capitalizeDriveLetter($path) {
            if(strpos($path, ":") === 1) {
                $path = strtoupper(substr($path, 0, 1)) . substr($path, 1);
            }
            return $path;
        }

        /*}}}*/
        /*{{{ public function replaceBackslashes() */

        /** 
        * Convert the back slash path separators with forward slashes. 
        * 
        * @param $path Windows path with backslash path separators
        * @return String Path with back slashes replaced with forward slashes.
        * @access public
        */
        public function replaceBackslashes($path) {
            $path = str_replace("\\", "/", $path);
            return $this->capitalizeDriveLetter($path);
        }

        /*}}}*/
        /*{{{ public function makeDirRecursive() */

        /** 
         * Make directory recursively. 
         * (Taken from: http://aidan.dotgeek.org/lib/?file=function.mkdirr.php)
         * 
         * @param $dir Directory path to create
         * @param $mode=0755 
         * @return True on success, False on failure
         * @access public
        */
        public function makeDirRecursive($dir, $mode=0755) {
            // Check if directory already exists
            if (is_dir($dir) || empty($dir)) {
                return true;
            }

            // Ensure a file does not already exist with the same name
            if (is_file($dir)) {
                error_log("File already exists: " . $dir,
                    __FILE__, __LINE__);
                return false;
            }

            $dir = $this->replaceBackslashes($dir);

            // Crawl up the directory tree
            $next_pathname = substr($dir, 0, strrpos($dir, "/"));
            if($this->makeDirRecursive($next_pathname, $mode)) {
                if (!file_exists($dir)) {
                    return mkdir($dir, $mode);
                }
            }

            return false;
        }
        /*}}}*/
    }

    $util = new TestGen4Web_Util_Util();
    global $util;
?>
