<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the OpenSSH utility tools.
 *
 * PHP version 5
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA
 *
 * @category  Net
 * @package   Net_OpenSSH
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2009 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_OpenSSH
 */

require_once 'System.php';
require_once 'File.php';
require_once 'File/Util.php';
require_once 'Net/OpenSSH/Exception.php';

/**
 * The method mapErrorCode in each OpenSSH implementation maps
 * native codes to one of these.
 *
 * If you add a code here, make sure you also add a textual
 * version of it in Net_OpenSSH::getMessage().
 */

define('OPENSSH_OK', 0);
define('OPENSSH_ERROR', 1);
define('OPENSSH_PACKAGE_NOT_FOUND', 2);
define('OPENSSH_CLASS_NOT_FOUND', 3);
define('OPENSSH_OPTION_NOT_VALID', 4);
define('OPENSSH_BINARY_NOT_FOUND', 5);
define('OPENSSH_OPTION_REQUIRED', 6);


/**
 * Simple wrapper interface for the OpenSSH utility tools.
 *
 * @category  Net
 * @package   Net_SSH
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2009 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_OpenSSH
 * @link      http://www.openssh.com/
 */
class Net_OpenSSH
{
    /**
     * Absolute path of the temporary script implements SSH_ASKPASS.
     *
     * @var string
     * @link http://www.openbsd.org/cgi-bin/man.cgi?query=ssh&sektion=1#ENVIRONMENT
     */
    protected $ssh_askpass_scripts = null;


    /**
     * List of options managed by __set and __get methods
     *
     * @internal
     * @var array
     */
    protected $options = array();

    /**
     * Allowed OpenSSH tool option arguments
     * List of options allowed in the wrapper implementation
     * for the specific driver
     *
     * @var array
     */
    protected $allowed_options = array();

    /**
     * Input stream
     *
     * @see exec()
     * @var string
     */
    protected $std_input = null;
    
    /**
     * Overloading of the __get method
     *
     * @param string $key The name of the variable that should be retrieved
     * 
     * @throws Net_OpenSSH_Exception If trying to get an undefined properties.
     * @return mixed The value of the object on success
     */
    protected function __get($key)
    {
        if (!key_exists($key, $this->allowed_options)) {
            throw new Net_OpenSSH_Exception(Net_OpenSSH::getMessage(OPENSSH_OPTION_NOT_VALID, $key));
        }
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            //return the default value
            return $this->allowed_options[$key];
        }
    }

    /**
     * Overloading of the __set method
     *
     * @param string $key   The name of the properties that should be set
     * @param mixed  $value parameter specifies the value that the object 
     *                      should set the $key
     * 
     * @throws Net_OpenSSH_Exception If trying to set an undefined properties.
     * @return mixed True on success
     */
    protected function __set($key, $value)
    {        
        if (!key_exists($key, $this->allowed_options)) {
            throw new Net_OpenSSH_Exception(Net_OpenSSH::getMessage(OPENSSH_OPTION_NOT_VALID, $key));
        }
        $this->options[$key] = $value;
        return true;
    }


    /**
     * Attempts to return a concrete OpenSSH instance of type $wrapper
     *
     * @param string $wrapper The type of concrete OpenSSH subclass to return.
     *                        Attempt to dynamically include the code for
     *                        this subclass.
     *
     * @param array  $options optional. An array of options used to create the
     *                        OpenSSH object. All options must be optional and are
     *                        represented as key-value pairs.
     * 
     * @throws Net_OpenSSH_Exception If OpenSSH package driver does not exist or
     *                               the class was not found.
     *
     * @return null
     */
    function &factory($wrapper, $options = array())
    {
        $class     = 'Net_OpenSSH_' . $wrapper;
        $classfile = 'Net/OpenSSH/' . $wrapper . '.php';

        // Attempt to include our version of the named class.
        if (!class_exists($class)) {
            $include = include_once $classfile;
            if (!$include) {
                throw new Net_OpenSSH_Exception(Net_OpenSSH::getMessage(OPENSSH_PACKAGE_NOT_FOUND, $classfile));
            }
        }

        /* If the class exists, return a new instance of it. */
        if (!class_exists($class)) {
            throw new Net_OpenSSH_Exception(Net_OpenSSH::getMessage(OPENSSH_CLASS_NOT_FOUND, $class));
        }
        
        $obj = &new $class($options);
        return $obj;
    }


    /**
     * Checks if the specified tool binary exists and for valid options.
     *
     * @param array $options optional. An array of options used to create the
     *                       OpenSSH object. All options must be optional and are
     *                       represented as key-value pairs.
     *
     * @throws Net_OpenSSH_Exception If the specified OpenSSH binary tool was not found.
     * @return void
     */
    protected function init($options)
    {
        //Check for ssh binary
        if (array_key_exists('openssh_binary', $options)) {
            $this->openssh_binary = escapeshellarg($options['openssh_binary']);
        } else {
            $this->openssh_binary = System::which($this->allowed_options['openssh_binary']);
            if (!$this->openssh_binary) {
                throw new Net_OpenSSH_Exception(
                    Net_OpenSSH::getMessage(
                        OPENSSH_BINARY_NOT_FOUND,
                        $this->allowed_options['openssh_binary']
                    )
                );
            }
        }

        //Check for valid options
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }

     /**
      * Overloading of the __destruct method, ensure to remove the SSH_ASKPASS
      * script if created.
      */
    function __destruct()
    {
        if ($this->ssh_askpass_scripts !== null && is_file($this->ssh_askpass_scripts)) {
            System::rm($this->ssh_askpass_scripts);
        }
    }

    /**
     * Generate the temporary ssh_askpass script
     *
     * @param string $password The password to use for login.
     * 
     * @return void
     */
    protected function ssh_askpass($password)
    {

        $this->ssh_askpass_scripts = File_Util::tmpFile();

        $askpass_data = 'echo ' . $password . ' < /dev/null';
        File::write($this->ssh_askpass_scripts, $askpass_data);
        File::closeAll();
        chmod($this->ssh_askpass_scripts, 0700);
    }



    /**
     * Abstract implementation of the createCommandLine() method.
     * 
     * @return false
     */
    protected function createCommandLine()
    {
        return false;
    }
    
    /**
     * Execute the command.
     *
     * @param string $std_output The standard output of the executed command
     * @param string $std_error  The standard error of the executed command
     * @param int    $exit_code  The return status of the executed command
     * 
     * @return true on success
     */
    function exec(&$std_output, &$std_error, &$exit_code)
    {
        $cmd = $this->createCommandLine();
        //This value can be set in the createCommandLine method implementation
        $std_input = $this->std_input; 

        $descriptorspec = array(0 => array("pty"),
                                1 => array("pty"),
                                2 => array("pty"));

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (is_resource($process)) {
            stream_set_blocking($pipes[0], false);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            if ($std_input !== null) {
                $std_output .= fgets($pipes[1], 4096);
                sleep(2);
                fwrite($pipes[0], $std_input . "\n");
                fflush($pipes[0]);
            }

            while (!feof($pipes[1])) {
                $std_output .= fgets($pipes[1], 4096);
            }
            
            while (!feof($pipes[2])) {
                $std_error .= fgets($pipes[2], 4096);
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exit_code = proc_close($process);
        }
        return true;
    }

    /**
     * Return a textual message for an OpenSSH code
     *
     * @param int    $code  OpenSSH code,
     *                        used to get the current code-message map.
     * @param string $value Optional. The argument to be inserted at the first
     *                        %-sign in the format string
     *
     * @return string The OpenSSH message string
     *
     */
    protected function getMessage($code, $value = null)
    {
        static $codeMessages;
        if (!isset($codeMessages)) {
            $codeMessages = array(
                OPENSSH_OK                => 'No error',
                OPENSSH_ERROR             => 'Unknown error',
                OPENSSH_PACKAGE_NOT_FOUND => 'Unable to find package %s',
                OPENSSH_CLASS_NOT_FOUND   => 'Unable to load class %s',
                OPENSSH_OPTION_NOT_VALID  => 'Trying to use an undefined option %s for the object ' . __CLASS__,
                OPENSSH_BINARY_NOT_FOUND  => 'Unable to found the OpenSSH binary command "%s"',
                OPENSSH_OPTION_REQUIRED   => 'The option %s is required for the object ' . __CLASS__
            );
        }
        return sprintf($codeMessages[$code], $value);
    }
}
?>