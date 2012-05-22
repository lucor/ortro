<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the ssh utility.
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

/**
 * The Net_OpenSSH_SSH class is a concrete implementation of the Net_OpenSSH::
 * abstract class for the OpenSSH SSH client tool.
 *
 * PLEASE NOTE that you must create a Net_OpenSSH_ssh like this :
 * $open_ssh =& Net_OpenSSH::factory('SSH');
 *
 * @category  Net
 * @package   Net_OpenSSH
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2009 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_OpenSSH
 */
class Net_OpenSSH_SSH extends Net_OpenSSH
{

    /**
     * SSH option arguments
     * List of the allowed options managed by __set and __get methods
     *
     * @var array
     * @see Net_OpenSSH_ssh::__construct()
     * @link http://www.openbsd.org/cgi-bin/man.cgi?query=ssh
     */
    protected $allowed_options = array('openssh_binary' => 'ssh',
                                       'login_name' => null,
                                       'password' => null,
                                       'hostname' => null,
                                       'identity_file' => null,
                                       'option' => null,
                                       'port' => 22,
                                       'command' => null);

    /**
     * Creates a new SSH object
     *
     * @param array $options optional. An array of options used to create the
     *                       SSH object. All options must be optional and are
     *                       represented as key-value pairs.
     */
    function __construct($options = array())
    { 
        parent::init($options);
    }

    /**
     * Prepare the command line to execute
     * 
     * @return string
     */
    protected function createCommandLine()
    {
        $cmd = escapeshellarg($this->openssh_binary);

        if ($this->identity_file !== null) {
            $cmd .= ' -i ' . escapeshellarg($this->identity_file);
        }
        
        if ($this->login_name !== null) {
            $cmd .= ' -l ' . escapeshellarg($this->login_name);
        }

        if ($this->option !== null) {
            foreach ($this->option as $key => $option) {
                $cmd .= ' -o ' . escapeshellarg($option);
            }
        }

        if ($this->port !== null) {
            $cmd .= ' -p ' . $this->port;
        }

        if ($this->password !== null) {
            $this->ssh_askpass($this->password);
            $cmd = "SSH_ASKPASS=" . 
                   escapeshellarg($this->ssh_askpass_scripts) .
                   ' ' . $cmd;
        }

        if ($this->hostname !== null) {
            $cmd .= ' ' . escapeshellarg($this->hostname);
        }
        
        if ($this->command !== null) {
            //Check for input script
            if (is_file($this->command)) {
                //reading commands to execute from file
                $cmd .= ' < ' . $this->command;
            } else {
                $cmd .= ' ' . $this->command;
            }
        }
        return $cmd;
    }
}
?>
