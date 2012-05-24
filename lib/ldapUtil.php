<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Ldap interface class.
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

require_once 'logUtil.php';

/**
 * LDAP Class Interface
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class LdapUtil
{
    
    // {{{ constructor
    /**
     * ldapUtil Class constructor. 
     * This flavour of the constructor only enable logging 
     * identifying it by the name of the class file.
     * 
     * @access public
     */
    function ldapUtil() 
    {
        $this->logger = new LogUtil('ldapUtil.php');
    }
    // }}}
    
    // {{{ ldapSearch()
    /**
     * This method allows to search on the defined ldap tree.
     *
     * @param string $filter                The search filter can be simple 
     *                                      or advanced, using boolean operators in 
     *                                      the format described in the LDAP 
     *                                      documentation.
     * @param array  $only_these_attributes An array of the required attributes, 
     *                                      e.g. array("mail", "sn", "cn"). 
     *                                      Note that the "dn" is always returned 
     *                                      irrespective of which attributes 
     *                                      types are requested.
     * 
     * @return array the found entries
     */
    function ldapSearch($filter, $only_these_attributes)
    {
        $ldapserver = @ldap_connect($GLOBALS['conf']['ldap']['server']);
        $info       = '';
        //for fix strange behavior ldap_search doesn't see it as an array
        $temp_array = array_reverse($only_these_attributes); 
        if ($ldapserver != true) {
            $this->logger->trace('ERROR', "LDAP connection failed");
            ldap_close($ldapserver);
            $info = 'error';
            return $info;
        }
        
        if ($GLOBALS['conf']['ldap']['bind_dn'] == '') {
            //anonymous search allowed
            $bind = @ldap_bind($ldapserver);
        } else {
            $bind = @ldap_bind($ldapserver,
                               $GLOBALS['conf']['ldap']['bind_dn'],
                               $GLOBALS['conf']['ldap']['bind_password']);
        }
        
        if ($bind != true) {
            $this->logger->trace('ERROR', 'LDAP bind failed');
            ldap_close($ldapserver);
            $info = 'error';
            return $info;
        }
        
        if ($only_these_attributes == '') {
            $result = ldap_search($ldapserver,
                                  $GLOBALS['conf']['ldap']['base_dn'],
                                  $filter);
        } else {
            $result = ldap_search($ldapserver,
                                  $GLOBALS['conf']['ldap']['base_dn'],
                                  $filter,
                                  $temp_array);
        }
        
        if ($result != true) {
            $this->logger->trace('ERROR', 'LDAP search failed');
            ldap_close($ldapserver);
            $info = 'error';
            return $info;
        }
        
        if ($info != 'error') {
            $info = @ldap_get_entries($ldapserver, $result);    
        }
        
        ldap_close($ldapserver);
        
        return $info;
    }
    // }}}

    // {{{ ldapLogin()
    /**
     * This method authenticates the submitted credential against the ldap tree
     *
     * @param string $userid   The user id 
     * @param string $password The password
     * 
     * @return boolean true on authentication success
     */
    function ldapLogin($userid, $password)
    {
        $login_status = 'error';
        if (strlen($password)>0) {
            $ldapserver = @ldap_connect($GLOBALS['conf']['ldap']['server']);
            if ($ldapserver != true) {
                $this->logger->trace('ERROR', 'LDAP connection failed!');
                ldap_close($ldapserver);
                return $login_status;
            }
            
            if ($GLOBALS['conf']['ldap']['bind_dn'] == '') {
                //anonymous search allowed
                $bind = @ldap_bind($ldapserver);
            } else {
                $bind = @ldap_bind($ldapserver,
                                   $GLOBALS['conf']['ldap']['bind_dn'],
                                   $GLOBALS['conf']['ldap']['bind_password']);
            }
            
            if ($bind != true) {
                $this->logger->trace('ERROR', 'LDAP bind failed');
                ldap_close($ldapserver);
                return $login_status;
            }
            
            $filter = $GLOBALS['conf']['ldap']['search_attribute_uid'] . '=' . 
                      $userid;
            $result = @ldap_search($ldapserver,
                                   $GLOBALS['conf']['ldap']['base_dn'],
                                   $filter);
            $info   = @ldap_get_entries($ldapserver, $result);
            
            if ($result != true || $info['count'] == 0) {
                $this->logger->trace('ERROR', 'LDAP search failed');
                ldap_close($ldapserver);
                $login_status = false;
                return $login_status;
            }
    
            $user_dn = $info[0]['dn'];
            $bind    = @ldap_bind($ldapserver, $user_dn, $password);
            if ($bind) {
                $this->logger->trace('DEBUG', 'LDAP OK');
                $login_status = true;
            } else {
                $this->logger->trace('DEBUG', 'LDAP KO');
                $login_status = false;
            }
            
            ldap_close($ldapserver);
        }
        return $login_status;
    }
    // }}}
}
?>