<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Parses a Nmap XML output file
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

require_once 'XML/Parser.php';
require_once 'Net/Nmap/Host.php';
require_once 'Net/Nmap/Service.php';

/**
 * Parses a Nmap XML output file
 *
 * @category  Net
 * @package   Net_Nmap
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_Nmap
 * @link      http://nmap.org/data/nmap.dtd
 */
class Net_Nmap_Parser extends XML_Parser
{
    /**
     * Container for the host objects
     * @var ArrayIterator
     * @access private
     */
    private $_hosts;
     
    /**
     * start handler
     *
     * @param resource $parser  xml parser resource
     * @param string   $name    element name
     * @param array    $attribs attributes
     *
     * @return void
     * @access public
     */
    public function startHandler($parser, $name, $attribs)
    {
        switch ($name) {
        case 'host':
            if (!$this->_hosts instanceof ArrayIterator) {
                $this->_hosts = new ArrayIterator();
            }
            $this->_hosts->append(new Net_Nmap_Host());
            if ($this->_hosts->count() > 1) {
                $this->_hosts->next();
            }
            $this->_host = $this->_hosts->current();
            break;
        case 'status':
            $this->_host->setStatus($attribs['state']);
            break;
        case 'address':
            $this->_host->addAddress($attribs['addrtype'], $attribs['addr']);
            break;
        case 'hostname':
            $this->_host->addHostname($attribs['name']);
            break;
        case 'port':
            $this->_service = new Net_Nmap_Service();

            $this->_service->protocol = @$attribs['protocol'];
            $this->_service->port     = @$attribs['portid'];
            break;
        case 'service':
            $this->_service->name      = @$attribs['name'];
            $this->_service->product   = @$attribs['product'];
            $this->_service->version   = @$attribs['version'];
            $this->_service->extrainfo = @$attribs['extrainfo'];
            if (isset($attribs['ostype'])) {
                $this->_host->addOS('0', $attribs['ostype']);                 
            }
            break;
        case 'osmatch':
            $this->_host->addOS($attribs['accuracy'], $attribs['name']);
            break;
        default:
            $this->currentTag = $name;
            break;
        }
    }

    /**
     * end handler
     *
     * @param resource $parser xml parser resource
     * @param string   $name   element name
     *
     * @return void
     * @access public
     */
    public function endHandler($parser, $name)
    {
        switch ($name) {
        case 'port':
            $this->_host->addService($this->_service);
            break;
        default:
            break;
        }

        $this->currentTag = null;
    }
    
    /**
     * handle character data
     *
     * @param resource $parser xml parser resource
     * @param string   $data   data
     * 
     * @return void | true if $data is empty
     * @access public
     */
    public function cdataHandler($parser, $data)
    {
        $data = trim($data);
        if (empty($data)) {
            return true;
        }
    }

    /**
     * Get all the discovered hosts
     *
     * @return ArrayIterator The discovered hosts
     * @access public
     */
    public function getHosts()
    {
        return $this->_hosts;
    }
}


?>