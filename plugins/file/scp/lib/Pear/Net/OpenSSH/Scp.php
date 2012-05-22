<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the scp utility.
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
 * The Net_OpenSSH_ssh class is a concrete implementation of the Net_OpenSSH
 * abstract class for the scp utility.
 *
 * PLEASE NOTE that you must create a Net_OpenSSH_Scp like this :
 * $open_ssh =& Net_OpenSSH::factory('Scp');
 *
 * @category  Net
 * @package   Net_OpenSSH
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2009 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_OpenSSH
 */
class Net_OpenSSH_Scp extends Net_OpenSSH
{

    /**
     * scp option arguments
     * List of options managed by __set and __get methods
     *
     * @var array
     * @see Net_OpenSSH_ssh_keygen::__construct()
     * @link http://www.openbsd.org/cgi-bin/man.cgi?query=scp
     */
     protected $allowed_options = array('openssh_binary' => 'scp',
                                        'identity_file' => null,
                                        'option' => null,
                                        'port' => 22,
                                        'limit' => null,
                                        'recursive' => null,
                                        'src_config' => null,
                                        'dest_config' => null,
                                        'password' => null);

    /**
     * Creates a new Scp object
     *
     * @param array $options optional. An array of options used to create the
     *                       Scp object. All options must be optional
     *                       and are represented as key-value pairs.
     */
    function __construct($options = array())
    {
        parent::init($options);
    }

    /**
     * Prepare the command to execute
     *
     * @return string
     */
    public function createCommandLine()
    {
        $cmd = $this->openssh_binary;

        if ($this->recursive == 'y') {
             $cmd .= ' -r ';
        }

        if ($this->identity_file !== null) {
            $cmd .= ' -i ' . escapeshellarg($this->identity_file);
        }

        if ($this->port !== null) {
            $cmd .= ' -P ' . $this->port;
        }

        if ($this->limit !== null) {
            $cmd .= ' -l ' . $this->limit;
        }

        if ($this->option !== null) {
            foreach ($this->option as $key => $option) {
                $cmd .= ' -o ' . escapeshellarg($option);
            }
        }

        if ($this->password !== null) {
            $this->ssh_askpass($this->password);
            $cmd = "SSH_ASKPASS=" .
                   escapeshellarg($this->ssh_askpass_scripts) .
                   ' ' . $cmd;
        }

        if ($this->src_config !== null) {
            foreach ($this->src_config as $key => $src_config) {
                $cmd .= ' ';
                if (isset ($src_config['user'])) {
                    $cmd .= escapeshellarg($src_config['user']) . '@';
                }
                if (isset ($src_config['hostname'])) {
                    $cmd .= escapeshellarg($src_config['hostname']) . ':';
                }
                $cmd .= escapeshellarg($src_config['path']) . ' ';
            }
        }

        if ($this->dest_config !== null) {
            $cmd .= ' ';
            if (isset ($this->dest_config['user'])) {
                $cmd .= escapeshellarg($this->dest_config['user']) . '@';
            }
            if (isset ($this->dest_config['hostname'])) {
                $cmd .= escapeshellarg($this->dest_config['hostname']) . ':';
            }
            $cmd .= escapeshellarg($this->dest_config['path']) . ' ';
        }
        return $cmd;
    }
}
?>
