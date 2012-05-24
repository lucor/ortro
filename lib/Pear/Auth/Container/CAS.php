<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver to use against a CAS service
 *
 * PHP versions 4 and 5
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
 * @category   Authentication
 * @package    Auth
 * @author     Luca Corbo <lucor@php.net>
 * @copyright  Luca Corbo <lucor@php.net>
 * @license    GNU/LGPL v2.1
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.6.1
 */

/**
 * Include Auth_Container base class
 */
require_once "Auth/Container.php";


require_once "CAS.php";
/**
 * Containers for authenticate against a CAS service.
 * CAS provides enterprise single sign on service.
 *
 * Further options may be available and can be found on the CAS site at
 * http://www.ja-sig.org/products/cas/
 *
 * This class require the phpCAS client available on the phpCAS site at
 * http://www.ja-sig.org/wiki/display/CASC/phpCAS
 *
 * This acts as gateway so not authenticated users are redirect to
 * the CAS login service form.
 *
 * To use this storage containers, you have to use the
 * following syntax:
 *
 * <?php
 *  include_once 'Auth/Auth.php';
 *  include_once 'Auth/Container/CAS.php';
 *  include_once 'Log.php';
 *
 *  $params = array(
 *           'enableLogging' => true,
 *           'server_version'=> CAS_VERSION_2_0,//    the version of the CAS server
 *           'server_hostname'=> 'your_domain',//    the hostname of the CAS server
 *           'server_port'=> '8443',//    the port the CAS server is running on
 *           'server_uri'=> 'cas',//     the URI the CAS server is responding on
 *           'curl_opt_ssl_version'=>3
 *           );
 *
 *  $myauth = new Auth('CAS', $params);
 *
 *  $myauth->logger = &Log::factory('file', '/tmp/out.log', 'TEST');
 *
 *  if ($myauth->checkAuth()) {
 *      echo 'User ' . $myauth->getUsername() . ' login with success';
 *  } else {
 *      //Required to force login, simulate user login.
 *      $myauth->post['username'] = 'fake';
 *      //Start process authentication
 *      $myauth->start();
 *  }
 * ?>
 *
 *
 * @category   Authentication
 * @package    Auth
 * @author     Luca Corbo <lucor@php.net>
 * @copyright  Luca Corbo <lucor@php.net>
 * @license    GNU/LGPL v2.1
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.6.1
 */
class Auth_Container_CAS extends Auth_Container
{

    // {{{ properties

    /**
     * Options for the class
     * @var array
     * @access private
     */
    var $_options = array(
        'server_version'=> CAS_VERSION_2_0,//    the version of the CAS server
        'server_hostname'=> 'localhost',//    the hostname of the CAS server
        'server_port'=> '443',//    the port the CAS server is running on
        'server_uri'=> '',//     the URI the CAS server is responding on
        'curl_opt_ssl_version'=> 0 //The SSL version (2 or 3) to use.
                                  //By default PHP will try to determine this itself,
                                  //although in some cases this must be set manually.
        
    );

    // }}}
    // {{{ Auth_Container_CAS() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  $options, associative array with cas options features
     * @see    $_options
     */
    function Auth_Container_CAS($options)
    {
        //Check for valid options
        foreach ($options as $key => $value) {
            $this->_options[$key] = $value;
        }
        
    }

    // }}}

    // {{{ fetchData()

    /**
     * Checks if the user is authenticated (use the gateway feature).
     * If the user is not authenticated, halt by redirecting to the CAS server.
     *
     * @param  string Username
     * @param  string Password
     * @return mixed true on success
     */
    function fetchData($username = null, $password = null)
    {
        $this->log('Auth_Container_CAS::fetchData() called.', AUTH_LOG_DEBUG);

        phpCAS::client($this->_options['server_version'],
                       $this->_options['server_hostname'],
                       intval($this->_options['server_port']),
                       $this->_options['server_uri'],
                       false);

        // check for specified SSL version (2 or 3) to use with curl
        if ($this->_options['curl_opt_ssl_version'] != 0) {
            phpCAS::setExtraCurlOption(CURLOPT_SSLVERSION,
                                       $this->_options['curl_opt_ssl_version']);
        }
        
        phpCAS::setNoCasServerValidation();
        
        $this->log('Trying to login at: ' . phpCAS::getServerLoginURL(), AUTH_LOG_DEBUG);
        phpCAS::forceAuthentication();
        
        $this->log('Override fake username with cas user:' . phpCAS::getUser(), AUTH_LOG_DEBUG);
        $this->_auth_obj->username = phpCAS::getUser();
        return true;
    }
}
?>
