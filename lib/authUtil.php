<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Authentication and permission management class.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once 'dbUtil.php';
require_once 'logUtil.php';

/**
 * Authentication Class
 * 
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class AuthUtil
{
    
    //The default authentication method to use
    private $_auth_default;
    
    //Enable fallback authentication
    private $_auth_fallback;
    
    // {{{ constructor
    /**
     * ldapUtil Class constructor. 
     * This flavour of the constructor only enable logging 
     * identifying it by the name of the class file.
     * 
     * @access public
     */
    function authUtil()
    {
        $this->logger = new LogUtil('authUtil.php');
        //Roles in according with the values defined in the db
        if (isset($GLOBALS['conf']['xmlrpc']['timeout'])) {
            $this->session_expire = $GLOBALS['conf']['xmlrpc']['timeout'];
        } else {
            $this->session_expire = 3600;
        }
        
        if (isset($GLOBALS['conf']['auth'])) {
            $this->_auth_default  = $GLOBALS['conf']['auth']['default'];
            $this->_auth_fallback = $GLOBALS['conf']['auth']['fallback'];
        }
        $this->_id = $GLOBALS['conf']['id'];
        
        define('ADMIN', '1');
        define('SYSTEM_ADMIN', '2');
        define('GUEST', '3');
        define('SYSTEM_GUEST', '4');
    }
    // }}}

    /**
     * Return the default authentication method
     *
     * @return string
     * @access public
     */
    public function getDefaultAuthMethod() {
        return $this->_auth_default;
    }

	/**
     * Retrieve the list of available methods for authenticate user in ortro
     * 
     * @return array Available methods
     */
    public static function getAvailableAuthMethods() {
        $ortro_auth_methods['MDB2'] = 'MDB2';
        if (extension_loaded('ldap')) {
            $ortro_auth_methods['LDAP'] = 'LDAP';
        }
        if (@include_once 'CAS.php') {
            $ortro_auth_methods['CAS'] = 'CAS';
        }
        return $ortro_auth_methods;
    }
    
    /**
     * Register additional information that is to be stored
     * in the session.
     *
     * @param  string  Name of the data field
     * @param  mixed   Value of the data field
     * @return void
     * @access public
     */
    public static function isAuthorized() {
        if (isset($_SESSION[$GLOBALS['conf']['id']]['userid'])) {
            return true;
        }
        return false;
    }
    
    /**
     * Register additional information that is to be stored
     * in the session.
     *
     * @param  string  Name of the data field
     * @param  mixed   Value of the data field
     * @return void
     * @access public
     */
    public static function setSessionData($key, $value) {
        $_SESSION[$GLOBALS['conf']['id']][$key] = $value;
    }
    
    /**
     * Get information that is stored in the session.
     *
     * @param  string Name of the data field
     * @return mixed  Value of the data field.
     * @access public
     */
    public static function getSessionData($key) {
        if (isset($_SESSION[$GLOBALS['conf']['id']][$key])) {
            return $_SESSION[$GLOBALS['conf']['id']][$key]; 
        }
        return false;
    }
    
    /**
     * Logout function
     * 
     * @return void
     */
    function logout()
    {
        unset($_SESSION[$this->_id]);
    }
    
    /**
     * Login function
     * 
     * @param string $username The username
     * @param string $password The password
     * 
     * @return mixed return an array with the user info on success or a PEAR_Error
     */
    function login($username, $password)
    {
        require_once 'Pear/Auth.php';
        
        $this->logger->trace('DEBUG', "Trying login for user: " . $username);

        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConnOrtro();
        
        $options = array();
        
        $options_default = array('sessionName'=>$this->_id);
        
        $options_mdb2 = array('dsn' => $dbUtil->getDSN(),
                              'table' => $dbUtil->setTablePrefix('users'),
                              'usernamecol' => 'username',
                              'passwordcol' => 'password',
                              );
        
        switch ($this->_auth_default) {
        	case 'MDB2':
        	   $options = array_merge($options_mdb2, $options_default);   
        	break;
        	
        	case 'LDAP':
				require_once ORTRO_CONF . 'configure_ldap.php'; 
                $options_ldap = array('url' => $conf['ldap']['server'],
                                     'version' => 3,
                                     'basedn' => $conf['ldap']['base_dn'],
                                     'binddn' => $conf['ldap']['bind_dn'],
                                     'bindpw' => $conf['ldap']['bind_password'],
                                     'userattr' => $conf['ldap']['search_attribute_uid'],
    							     'userfilter' => '',
                                     'groupattr' => $conf['ldap']['search_attribute_cn'],
    							     'groupfilter' => '',
               );
               $options = array_merge($options_ldap, $options_default);
        	break;
            case 'CAS':
                require_once ORTRO_CONF . 'configure_cas.php';
                $options_cas = array(
                            'server_hostname' => $conf['cas']['server_hostname'],
                            'server_port' => $conf['cas']['server_port'],
                            'server_uri' => $conf['cas']['server_uri'],
                            'curl_opt_ssl_version' => $conf['cas']['curl_opt_ssl_version'],
                            'cas_server_validation' => $conf['cas']['cas_server_validation'],
               );
               
               $options = array_merge($options_cas, $options_default);
            break;
        }

        //Override $_POST variables required by Auth pear package.
        $_POST['username'] = $username;
        $_POST['password'] = $password;
        
        $_auth = new Auth($this->_auth_default, $options, '', false);
        $_auth->start();
        
        $login_result = false;
        
        if ($_auth->getAuth()) {
            $login_result = true;
            $username     = $_auth->getUsername();
        } elseif ($this->_auth_default != 'MDB2' && $this->_auth_fallback) {
            unset($_auth);
            $this->logger->trace('DEBUG', "Trying db authentication for user: " . 
										  $username);
            $_auth = new Auth('MDB2', array_merge($options_mdb2, $options_default),'',false);
            $_auth->start();
            if ($_auth->getAuth()) {
                $login_result = true;
            }
        }
        
        if ($login_result){
            // Store the last access time
            $dbUtil->dbExec($dbh, $dbUtil->setUserLastLogin($username, time()));
                             
            $userInfo = $dbUtil->dbQuery($dbh, $dbUtil->getUserInfo($username),
                                                MDB2_FETCHMODE_ASSOC);
            if (count($userInfo) > 0) {
                $this->setSessionData('userid', $userInfo[0]['id_user']);
                if ($userInfo[0]['language'] != 'none') {
                    //Enable the user language preference.
                    $this->setSessionData('language', $userInfo[0]['language']);
                }
                $this->setSessionData('policy', $this->getPolicyRoles($userInfo));
                $login_result = true;
            }  else {
                $login_result = false;
                $this->logout();
            }
        }
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        return $login_result;
    }
    
    /**
     * Get policy roles for the current user
     * 
     * @param array $userInfos The infos for the current user
     * 
     * @return array return an array with the correct policies
     */
    function getPolicyRoles($userInfos) 
    {
        $policy    = array();
        $sys_admin = '';
        $sys_guest = '';
        foreach ($userInfos as $row) {
            switch ($row['id_role']) {
            case ADMIN:
                $policy['ADMIN'] = '1';
                break;
                break;
            case SYSTEM_ADMIN:
                if ($sys_admin == '') {
                    $sys_admin = $row['id_systems'];
                } else {
                    $sys_admin .= ',' . $row['id_systems'];
                }
                break;
            case GUEST:
                $policy['GUEST'] = '1';
                break;
            case SYSTEM_GUEST:
                if ($sys_guest == '') {
                    $sys_guest = $row['id_systems'];
                } else {
                    $sys_guest .= ',' . $row['id_systems'];
                }
                break;
            }
        }
        
        if ($policy['ADMIN'] != '1') {
            if ($sys_admin != '') {
                $policy['SYSTEM_ADMIN'] = $sys_admin;
            }
            if ($policy['GUEST'] != '1') {
                $policy['SYSTEM_GUEST'] = $sys_guest;
            }
        }
        
        return $policy;
    }
    
    /**
     * Create a valid session id.
     * 
     * @return string The session id
     * 
     */
    function createSessionId() 
    {
        session_start();
        $session_id             = session_id();
        $auth_session_save_path = ORTRO_CONF . 'auth . DS';
        if (!is_dir(ORTRO_SESSION_SAVE_PATH)) {
            @mkdir(ORTRO_SESSION_SAVE_PATH, 0700);
        }
        @touch(ORTRO_SESSION_SAVE_PATH . $session_id);
        $this->destroySessionId();
        return $session_id;
    }
   
    /**
     * Check for a valid session id.
     * 
     * @param string $session_id The session id
     * 
     * @return boolean true on success
     */
    function checkSessionId($session_id) 
    {
        if (is_file(ORTRO_SESSION_SAVE_PATH . $session_id) && 
            fileatime(ORTRO_SESSION_SAVE_PATH . $session_id) > 
                      time()-$this->session_expire) {
            @touch(ORTRO_SESSION_SAVE_PATH . $session_id);
            return true;
        } else {
            $this->destroySessionId();
            return false;
        }
    }
    
    /**
     * Destroy a session id.
     * 
     * @param string $session_id The session id (optional)
     * 
     * @return boolean true on success
     */
    function destroySessionId($session_id='') 
    {
        if ($session_id == '') {
            $dir_contents = scandir(ORTRO_SESSION_SAVE_PATH);
            foreach ($dir_contents as $item) {
                if (is_file(ORTRO_SESSION_SAVE_PATH.$item) && 
                    fileatime(ORTRO_SESSION_SAVE_PATH . $item) < 
                    time()-$this->session_expire && $item != '.' && $item != '..') {
                    @unlink(ORTRO_SESSION_SAVE_PATH.$item);
                }
            }
        } else {
            @unlink(ORTRO_SESSION_SAVE_PATH . $session_id);             
        }
        return true;
    }
}
?>
