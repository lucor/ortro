<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the Nmap utility.
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
 * @package   Net_Nmap
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_Nmap
 */

require_once 'System.php';
require_once 'Nmap/Parser.php';
require_once 'Nmap/Exception.php';

/**
 * Simple wrapper interface for the Nmap utility.
 *
 * @category  Net
 * @package   Net_Nmap
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_Nmap
 */
class Net_Nmap
{
    
    /**
     * Location of the nmap binary
     *
     * @var string  $nmap_path
     * @see Net_Nmap::__construct()
     * @access private
     */
    private $_nmap_binary;
    
    /**
     * Absolute path to store the Nmap XML output file.
     *
     * @var string
     * @see Net_Nmap::__construct()
     * @access private
     */
    private $_output_file = null;
    
    /**
     * Delete Nmap output file after parsing
     *
     * @var    string
     * @access private
     */
    private $_delete_output_file = true;
    
    /**
     * The hostname/IP failed to resolve 
     *
     * @var    array
     * @access private
     */
    private $_failed_to_resolve = array();
    
    /**
     * Nmap option arguments
     *
     * @var array
     * @link http://nmap.org/book/man-briefoptions.html
     * @access private
     */
    private $_nmap_options = array();
    
    /**
     * Creates a new Nmap object
     *
     * Available options are:
     * 
     * - string  nmap_binary:  The location of the Nmap binary. 
     *                         If not specified, defaults to '/usr/bin/nmap'.
     *
     * - string  output_file:  Path to store the Nmap XML output file.
     *                         If not specified, a temporary file is created.
     *
     * @param array $options optional. An array of options used to create the
     *                       Nmap object. All options must be optional and are
     *                       represented as key-value pairs.
     * 
     * @access public
     */
    public function __construct(array $options = array())
    {
        if (array_key_exists('nmap_binary', $options)) {
            $this->_nmap_binary = (string)$options['nmap_binary'];
        } else {
            $this->_nmap_binary = System::which('nmap');
        }

        if (array_key_exists('output_file', $options)) {
            $this->_output_file = (string)$options['output_file'];
        }
    }
    
    /**
     * Prepare the command to execute 
     *
     * @param array $targets contains hostnames, IP addresses, networks to scan
     * 
     * @return string 
     * @access private
     */
    private function _createCommandLine($targets)
    {

        if ($this->_output_file === null) {
             $this->_output_file = tempnam(System::tmpdir(), __CLASS__);
        } else {
            $this->_delete_output_file = false;
        }
        
        $cmd  = escapeshellarg($this->_nmap_binary);
        $cmd .= ' ' . implode(' ', $this->_nmap_options);
        $cmd .= ' -oX ' . escapeshellarg($this->_output_file) . ' ';
        foreach ($targets as $target) {
            $cmd .= escapeshellarg($target) . ' ';
        }
        $cmd .= '2>&1';
        if (OS_WINDOWS) {
            $cmd = '"' . $cmd . '"';
        }
        return $cmd;
    }
    
    /**
     * Scan the specified target 
     *
     * @param array $targets Array contains hostnames, IP addresses, 
     *                       networks to scan
     * 
     * @return true | PEAR_Error
     * @throws Net_Nmap_Exception If Nmap binary does not exist or 
     *                            the command failed to execute.
     * @access public
     */
    public function scan($targets)
    {        
        exec($this->_createCommandLine($targets), $out, $ret_var);

        if ($ret_var > 0) {
            throw new Net_Nmap_Exception(implode(' ', $out));
        } else {
            foreach ($out as $row) {
                preg_match('@^Failed to resolve given hostname/IP:\s+(.+)\.\s+Note@',
                           $row,
                           $matches);
                if (count($matches) > 0) {
                    $this->_failed_to_resolve[] = $matches[1];
                }
            }
            return true;
        }
    }
    
    /**
     * Get all the discovered hosts
     * 
     * @param string $output_file Absolute path of the file to parse (optional)
     *
     * @return ArrayIterator      Returns Hosts Object on success. 
     * @throws Net_Nmap_Exception If a parsing error occurred. 
     * @access public
     */
    public function parseXMLOutput($output_file = null)
    {
        if ($output_file === null) {
            $output_file = $this->_output_file;
        } else {
            $this->_delete_output_file = false;
        }
        $parse = new Net_Nmap_Parser();
        $parse->setInputFile($output_file);
        $parse->folding = false;
        
        $res = $parse->parse();
        if (PEAR::isError($res)) {
            throw new Net_Nmap_Exception($res);
        }
        if ($this->_delete_output_file) {
            unlink($this->_output_file);
        }
        return $parse->getHosts();
    }
    
    /**
     * Get all the hostnames/IPs failed to resolve during scanning operation
     * 
     * @return Array    Returns array
     * @access public
     */
    public function getFailedToResolveHosts()
    {
        return $this->_failed_to_resolve;
    }
    
    /**
     * Enable Nmap options
     * Available nmap options are:
     * 
     * - boolean os_detection: Enable the OS detection (-O).
     * - boolean service_info: Probe open ports to determine 
     *                         service/version info (-sV)
     * - string  port_ranges : Port ranges, only scan 
     *                         specified ports (-p <port ranges>)
     *                         Ex: 22; 1-65535; U:53,111,137,T:21-25,80,139,8080
     * - boolean all_options : Enables OS detection and Version detection, 
     *                         Script scanning and Traceroute (-A)
     * 
     * @param array $nmap_options Nmap options to enable
     * 
     * @return void
     * @link http://nmap.org/book/man-briefoptions.html
     * @throws Net_Nmap_Exception If the option argument is not valid.
     * @access public
     */
    public function enableOptions($nmap_options)
    {
        $enable_os_detection = array_key_exists('os_detection', $nmap_options);
        $enable_service_info = array_key_exists('service_info', $nmap_options);
        $enable_port_ranges  = array_key_exists('port_ranges', $nmap_options);
        $enable_all_options  = array_key_exists('all_options', $nmap_options);
        
        if ($enable_os_detection && $nmap_options['os_detection']) {    
            $this->_nmap_options[] = '-O';
        }
        if ($enable_service_info && $nmap_options['service_info']) {    
            $this->_nmap_options[] = '-sV';
        }
        if ($enable_port_ranges) {
            $port_ranges    = $nmap_options['port_ranges'];
            $allowed_format = '([U,T]\:)*[0-9]+(-[0-9]+)*';
            
            $regexp = '/^' . $allowed_format . '(,' . $allowed_format . ')*$/';
            if (preg_match($regexp, $port_ranges)) {
                $this->_nmap_options[] = '-p ' . $port_ranges;
            } else {
                throw new Net_Nmap_Exception('Port ranges: not valid format.');
            }  
        }
        if ($enable_all_options && $nmap_options['all_options']) {    
            $this->_nmap_options[] = '-A';
        }
    }
}
?>