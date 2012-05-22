<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the ssh-keygen utility.
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
 * The Net_OpenSSH_SSHKeygen class is a concrete implementation of the Net_OpenSSH
 * abstract class for the ssh-keygen authentication key generation,
 * management and conversion tool.
 *
 * PLEASE NOTE that you must create a Net_OpenSSH_ssh like this :
 * $open_ssh =& Net_OpenSSH::factory('SSHKeygen');
 *
 * @category  Net
 * @package   Net_OpenSSH
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2009 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_OpenSSH
 */
class Net_OpenSSH_SSHKeygen extends Net_OpenSSH
{

    /**
     * ssh-keygen option arguments
     * List of options managed by __set and __get methods
     *
     * @var array
     * @see Net_OpenSSH_SSHKeygen::__construct()
     * @link http://www.openbsd.org/cgi-bin/man.cgi?query=ssh-keygen
     */
     protected $allowed_options = array('openssh_binary' => 'ssh-keygen',
                                        'silence' => null,
                                        'bits' => null,
                                        'type' => null,
                                        'new_passphrase' => '',
                                        'comment' => null,
                                        'output_keyfile' => null,
                                        'overwrite_existing_key' => null);

    /**
     * Creates a new SSH-KEYGEN object
     *
     * @param array $options optional. An array of options used to create the
     *                       SSH-KEYGEN object. All options must be optional
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
    protected function createCommandLine()
    {

        $cmd = $this->openssh_binary;

        if ($this->silence) {
            $cmd .= ' -q ';
        }

        if ($this->bits !== null) {
            $cmd .= ' -b ' . $this->bits;
        }

        if ($this->type !== null) {
            $cmd .= ' -t ' . $this->type;
        }

        $cmd .= ' -N ' . $this->new_passphrase;

        if ($this->comment !== null) {
            $cmd .= ' -C ' . $this->comment;
        }

        if ($this->output_keyfile !== null) {
            $cmd .= ' -f ' . $this->output_keyfile;
        }

        if ($this->overwrite_existing_key) {
            $this->std_input = 'y';
        } else {
            $this->std_input = 'n';
        }
        
        return $cmd;
    }
}
?>
