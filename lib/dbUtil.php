<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Database class.
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

require_once 'Pear/MDB2.php';
require_once 'logUtil.php';
require_once 'cronUtil.php';

/**
 * DbUtil Class
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class DbUtil
{
    var $_dsn='';
    
     /**
     * DbUtil Class constructor.
     * 
     * @return void
     */
    function dbUtil()
    {
        $this->logger = new LogUtil('dbUtil.php');
    }
    
    /**
     * Add the custom prefix to the specified table
     * 
     * @param string $tableName The table name
     * 
     * @return string The table name with prefix
     */
    function setTablePrefix($tableName)
    {
        return $GLOBALS['conf']['db']['tableprefix'] . $tableName;
    }
    
    /**
     * Set the DSN resource
     * 
     * @param array $dsnArray The dsn array
     * 
     * @return string The dsn string
     */
    function setDSN($dsnArray)
    {
        switch ($dsnArray['phptype']) {
        case 'oci8':
            $hostspec = split(':', $dsnArray['hostspec']);
            $ip       = $hostspec['0'];
            $port     = $hostspec['1'];
            $sid      = $dsnArray['database'];
            $tnsnames = "(DESCRIPTION = (ADDRESS = (PROTOCOL = tcp)(Host = $ip)" .
                        "(Port = $port))(CONNECT_DATA =(SID = $sid)))";
            $dsn      = $dsnArray['phptype'] . '://' . $dsnArray['username'] . ':' . 
                        $dsnArray['password'] . '@' . $tnsnames;
            break;
        case 'sqlite':
            $dsn = $dsnArray['phptype'] . ':///' . ORTRO_SQLITE_DB . 
                   $dsnArray['database'] . '?mode=0600';
            break;
        /*
         * Add here dsn different by default case if needed
        case mysql:
            $dsn= $dsnArray['phptype'] . '://' . $dsnArray['user'] . ':' . 
                  $dsnArray['password'] . '@' . $dsnArray['hostspec'] . '/' . 
                  $dsnArray['database'];
            break;
        */
        default:
            $dsn = $dsnArray['phptype'] . '://' . $dsnArray['username'] . ':' . 
                   $dsnArray['password'] . '@' . $dsnArray['hostspec'] . '/' . 
                   $dsnArray['database'];
            break;
        }
        $this->_dsn = $dsn;
        return $this->_dsn;
    }
    
    /**
     * Get the DSN resource
     * 
     * @return string The DSN string
     */
    function getDSN()
    {
        return $this->_dsn;
    }
    
    /**
     * Create a new MDB2 connection object and connect 
     * to the Ortro database
     * 
     * @return object The connection object
     */
    function dbOpenConnOrtro()
    {
        $dsn = $this->setDSN(array('phptype'  => $GLOBALS['conf']['db']['phptype'],
                                   'hostspec' => $GLOBALS['conf']['db']['host'] . 
                                                 ':' . 
                                                 $GLOBALS['conf']['db']['port'],
                                   'database' => $GLOBALS['conf']['db']['database'],
                                   'username' => $GLOBALS['conf']['db']['username'],
                                  'password' => $GLOBALS['conf']['db']['password']));
        
        $dbh =& MDB2 :: connect($dsn);
        if (MDB2 :: isError($dbh)) {
            $this->logger->trace('DEBUG', 'Database connection failed: ');
            $this->logger->trace('DEBUG', $dbh->getDebugInfo());
            exit;
        }
        $dbh->setErrorHandling(PEAR_ERROR_DIE);
        return $dbh;
    }
    
    /**
     * Create a new MDB2 connection object and connect 
     * to the specified database
     * 
     * @param string $dbType   Database back end type
     * @param string $hostname Host
     * @param string $port     Port
     * @param string $dbName   Database name
     * @param string $user     Username
     * @param string $password Password 
     * 
     * @return object The connection object
     */
    function dbOpenConn($dbType, $hostname, $port, $dbName, $user, $password)
    {
        $dsn = $this->setDSN(array('phptype'  => $dbType,
                                   'hostspec' => $hostname . ':' . $port,
                                   'database' => $dbName,
                                   'username' => $user,
                                   'password' => $password));
        try {
            $dbh =& MDB2 :: connect($dsn);
        } catch (Exception $e) {
            if ($dbType == 'oci8' && 
                (!(strpos($e->getMessage(), 'ORA-12505') === false))) {
                //Trying to use SERVICE_NAME instead of SID
                $dsn        = str_replace('=(SID', '=(SERVICE_NAME', $dsn);
                $this->_dsn = $dsn;
                $dbh        =& MDB2 :: connect($dsn);
                if (MDB2 :: isError($dbh)) {
                    $error_msg = "\nDatabase connection failed: \n" . 
                                 $dbh->getDebugInfo();
                    $this->logger->trace('ERROR', $error_msg);
                    throw new Exception($error_msg);
                }
            } else {
                $error_msg = "\nDatabase connection failed: \n" . $e->getMessage();
                $this->logger->trace('ERROR', $error_msg);
                throw new Exception($error_msg);
            }
        }
        return $dbh;
    }
    
    /**
     * Execute the specified query, fetch all the rows 
     * of the result set into a two dimensional array 
     * and then frees the result set.
     * 
     * @param Object $dbh  the MDB2 object connection
     * @param string $stmt the SQL query
     * @param int    $mode how the array data should be indexed
     * 
     * @return mixed MDB2_OK or data array on success, a MDB2 error on failure
     */
    function dbQuery($dbh, $stmt, $mode=MDB2_FETCHMODE_ORDERED) 
    {
        $stmt_log = preg_replace('/password=\'\S+\'/', 'password=******', $stmt);
        $this->logger->trace('DEBUG', $stmt_log);
        $result = $dbh->queryAll($stmt, null, $mode);
        // Always check that result is not an error
        if (PEAR::isError($result)) {
            $error_msg = "\nQuery failed: " . $stmt_log . 
                         "\n" . $result->getMessage();
            $this->logger->trace('ERROR', $error_msg);
            throw new Exception($error_msg);
        }
        return $result;
    }
    
    /**
     * Execute a manipulation query to the database and 
     * return the number of affected rows
     * 
     * @param Object $dbh  the MDB2 object connection
     * @param string $stmt the SQL query
     * 
     * @return mixed number of affected rows on success, a MDB2 error on failure
     */
    function dbExec($dbh, $stmt)
    {
        $stmt_log = preg_replace('/password=\'\S+\'/', 'password=******', $stmt);
        $this->logger->trace('DEBUG', $stmt_log);
        $affected =& $dbh->exec($stmt);
        // Always check that result is not an error
        if (PEAR::isError($affected)) {
            $error_msg = "\nQuery failed: " . $stmt_log . "\n" . 
                         $affected->getMessage();
            $this->logger->trace('ERROR', $error_msg);
            throw new Exception($error_msg);
            exit;
        }
        return $affected;
    }
    
    /**
     * Execute multiple manipulation queries to the database
     * 
     * @param Object $dbh  the MDB2 object connection
     * @param array  $stmt an array with the SQL queries
     * 
     * @return void
     */
    function dbExecMulti($dbh, $stmt)
    {
        for ($i = 0; $i < sizeof($stmt); $i++) {
            $this->dbExec($dbh, $stmt[$i]);
        }    
    }

    /**
     * Execute a sql script from file
     *
     * @param Object $dbh  the MDB2 object connection
     * @param string  the SQL file
     *
     * @return mixed   true on success, a MDB2 error on failure
     *
     * @access public
     */
    function dbExecFromFile($dbh, $sql_file)
    {
        $contents = file_get_contents($sql_file);
        
        // Remove C style and inline comments
        $comment_patterns = array('/\/\*.*(\n)*.*(\*\/)?/', //C comments
                                  '/\s*--.*\n/', //inline comments start with --
                                  '/\s*#.*\n/', //inline comments start with #
                                  );
        $contents = preg_replace($comment_patterns, "\n", $contents);
        
        //Retrieve sql statements 
        $statements = explode(";\n", $contents);
        $statements = preg_replace("/\s/", ' ', $statements);
        
        foreach ($statements as $query) {
            if (trim($query) != '') {
                $this->logger->trace('DEBUG','Executing query: ' . $query);
                $this->dbExec($dbh, $query);
            }
        }
        return true;
    }
    
    
    
    /**
     * Log out and disconnect from the database.
     * 
     * @param Object $dbh the MDB2 object connection
     * 
     * @return true on success, false if not connected and error object on error
     */
    function dbCloseConn($dbh)
    {
        $dbh->disconnect();
        return $dbh;
    }
    
    /**
     * Encodes a string representation of variable using MIME base64 algorithm. 
     * 
     * @param string $string2serialize The string to serialize
     * 
     * @return string
     */
    function dbSerialize($string2serialize)
    {
        return base64_encode(serialize($string2serialize));
    }
    
    /**
     * Takes a string representation of variable decoded using MIME base64 
     * algorithm and recreates it 
     * 
     * @param string $string2unserialize The string to unserialize
     * 
     * @return mixed
     */
    function dbUnserialize($string2unserialize)
    {
        return unserialize(base64_decode($string2unserialize));
    }
    
    /**
     * SQL query to add a System
     * 
     * @param string $system the system label
     * 
     * @return string the sql statement
     */
    function setSystem($system)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('system') . 
                " (name, status) values ('". $system . "', 'W')";
        return $stmt;
    }
    
    /**
     * SQL query to get all systems
     * 
     * @return string the sql statement
     */
    function getSystems()
    {
        $stmt = 'select * from ' . $this->setTablePrefix('system') . 
                ' order by name';
        return $stmt;
    }
    
    /**
     * SQL query to get the system by aspecified id
     * 
     * @param int $id_system the system id
     * 
     * @return string the sql statement
     */
    function getSystemById($id_system)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('system') . 
                ' where id_system=' . $id_system;
        return $stmt;
    }
    
    /**
     * SQL query to update the system values
     * 
     * @param int    $id_system  the system id
     * @param string $systemName the system name
     * 
     * @return string the sql statement
     */
    function updateSystem($id_system, $systemName)
    {
        $stmt = 'update ' . $this->setTablePrefix('system') . 
                " set name='" . $systemName . "'" .
                ' where id_system=' . $id_system;
        return $stmt;
    }

    /**
     * SQL query to check if the system name is already defined
     * 
     * @param string $label the system name
     * 
     * @return string the sql statement
     */
    function checkExistsSystem($label)
    {
        $stmt = 'select count(*) from ' . $this->setTablePrefix('system') . 
                " where name='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to set the relation between system, host and db
     * 
     * @param int $id_system the system id
     * @param int $id_host   the host id
     * @param int $id_db     the database id
     * 
     * @return string the sql statement
     */
    function setSystemHostDb($id_system, $id_host, $id_db)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('system_host_db') .
                             ' (id_system,id_host,id_db)' . 
                             ' values ('. $id_system . ',' .
                                          $id_host .','. 
                                          $id_db. ')';
        return $stmt;
    }
    
    /**
     * SQL query to get all hosts with associated systems
     * 
     * @param string $filter_system the system id used to filter result (optional)
     * 
     * @return string the sql statement
     */
    function getSystemHost($filter_system = '')
    {
        $filter_stmt = '';
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' and a.id_system=' . $filter_system;
        }
        
        $stmt = 'select distinct a.id_system, a.name, b.id_host, b.ip, b.hostname, b.status ' .
                'from ' . $this->setTablePrefix('system') . ' as a, ' . 
                          $this->setTablePrefix('host') . ' as b, '. 
                          $this->setTablePrefix('system_host_db') . ' as c ' .
                ' where a.id_system=c.id_system and b.id_host=c.id_host ' .
                $filter_stmt .
                ' order by a.name,a.id_system,b.hostname';
        return $stmt;
    }
    
    /**
     * SQL query to get all jobs with associated systems
     * 
     * @param int $id_system the system id used to filter result (optional)
     * 
     * @return string the sql statement
     */
    function getSystemJob($id_system = '')
    {
        $filter_stmt = '';
        if ($id_system != '') {
            $system_filter = ' and b.id_system=' . $id_system;
        }
        $stmt = 'select a.id_job, a.label, b.id_system, b.name ' .
                ' from ' . $this->setTablePrefix('jobs') . ' as a, '
                         . $this->setTablePrefix('system') . ' as b, ' 
                         . $this->setTablePrefix('system_host_db') . ' as c ' 
                . ' where a.id_shd=c.id_shd and b.id_system=c.id_system '
                . $filter_stmt
                . ' order by b.name,a.label';
        return $stmt;
    }
    
    /**
     * SQL query to check if host name is already defined
     * 
     * @param string $ip       the ip address
     * @param string $hostname the hostname
     * 
     * @return string the sql statement
     */
    function checkExistsHost($ip, $hostname)
    {
        $stmt = 'select count(*) ' .
                'from ' . $this->setTablePrefix('host') .
                " where ip='" . $ip . "'" .
                " and hostname='" . $hostname . "'";
        return $stmt;
    }
    
    /**
     * SQL query to check if identity label is already defined
     * 
     * @param string $label the identity label
     * 
     * @return string the sql statement
     */
    function checkExistsIdentity($label)
    {
        $stmt = 'select id_identity ' .
                'from ' . $this->setTablePrefix('identity_management') .
                " where label='" . $label . "'";
        return $stmt;
    }
    
    /**
     * SQL query to check for matching password
     * 
     * @param int    $id_identity the identity id
     * @param string $password    the passwrd
     * 
     * @return string the sql statement
     */
    function checkIdentityPassword($id_identity, $password)
    {
        $stmt = 'select count(*) ' .
                'from ' . $this->setTablePrefix('identity_management') .
                ' where id_identity=' . $id_identity .
                " and password='" . $password . "'";
        return $stmt;
    }
    
    /**
     * SQL query to check if already exists a relation 
     * between system, host and database
     * 
     * @param int $id_system the system id
     * @param int $id_host   the host id
     * @param int $id_db     the database id
     * 
     * @return string the sql statement
     */
    function checkExistsSystemHostDb($id_system, $id_host, $id_db)
    {
        $stmt = 'select count(*) ' .
                'from ' . $this->setTablePrefix('system_host_db') .
                ' where id_system=' . $id_system .
                ' and id_host=' . $id_host .
                ' and id_db=' . $id_db;
        return $stmt;
    }
    
    /**
     * SQL query to get the system,  host, db relation id
     * 
     * @param int $id_system the system id
     * @param int $id_host   the host id
     * @param int $id_db     the database id
     * 
     * @return string the sql statement
     */
    function getSystemHostDbId($id_system, $id_host, $id_db)
    {
        $stmt = 'select id_shd ' .
                'from ' . $this->setTablePrefix('system_host_db') .
                ' where id_system=' . $id_system .
                ' and id_host=' . $id_host .
                ' and id_db=' . $id_db;
        return $stmt;
    }
    
    /**
     * SQL query to get the locked system,  host, db relation id
     * 
     * @return string the sql statement
     */
    function getLockedSystemHostDb()
    {
        $stmt = 'select a.id_shd ' .
                'from ' . 
                $this->setTablePrefix('system_host_db') . ' as a, ' .
                $this->setTablePrefix('system') . ' as b, ' .
                $this->setTablePrefix('host') . ' as c ' .
                " where (b.status != 'W' and a.id_system=b.id_system)" . 
                " or (a.id_host=c.id_host and c.status != 'W')"; 
        return $stmt;
    }
    
    
    /**
     * SQL query to get the system, host, db labels by job id
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getSystemHostDbInfoById($id_job)
    {
        $stmt = 'select a.name,b.hostname, c.label ' .
                'from ' . $this->setTablePrefix('system') . ' as a, '
                . $this->setTablePrefix('host') . ' as b, '
                . $this->setTablePrefix('db') . ' as c, '
                . $this->setTablePrefix('system_host_db') . ' as d, '
                . $this->setTablePrefix('jobs') . ' as e ' .
                ' where d.id_system=a.id_system and d.id_host=b.id_host and ' . 
                ' d.id_db=c.id_db and d.id_shd=e.id_shd and ' .
                ' e.id_job=' . $id_job;
        return $stmt;
    }
    
    /**
     * SQL query to add a host
     * 
     * @param string $ip       the ip address
     * @param string $hostname the hostname
     * 
     * @return string the sql statement
     */
    function setHost($ip, $hostname)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('host') . 
                " (ip,hostname,status) values ('" .  $ip . "','" . $hostname . "', 'W')";
        return $stmt;
    }
    
   /**
     * SQL query to change the host status
     * 
     * @param string $id_host the host id
     * @param string $status  the status to set
     * 
     * @return string the sql statement
     */
    function setHostStatus($id_host, $status)
    {
        $stmt = 'update ' . $this->setTablePrefix('host') . 
                " set status='" .  $status . "' " . 
                " where id_host=" . $id_host;
        return $stmt;
    }

   /**
     * SQL query to change the system status
     * 
     * @param string $id_system the system id
     * @param string $status    the status to set
     * 
     * @return string the sql statement
     */
    function setSystemStatus($id_system, $status)
    {
        $stmt = 'update ' . $this->setTablePrefix('system') . 
                " set status='" .  $status . "' " . 
                " where id_system=" . $id_system;
        return $stmt;
    }
    
    
    /**
     * SQL query to add an identity
     * 
     * @param string $label             the identity label
     * @param string $username          the username
     * @param string $password          the password
     * @param int    $id_system         the system id
     * @param int    $id_shared_systems the systems id to share with
     * 
     * @return string the sql statement
     */
    function setIdentity($label, 
                         $username, 
                         $password, 
                         $id_system, 
                         $id_shared_systems)
    {
        $stmt = "insert into " . $this->setTablePrefix('identity_management') .
                " (label,username,password,system,share_with) " . 
                " values ('" .  $label . "','" . 
                                $username . "','" . 
                                $password . "'," .
                                $id_system . ",'" .
                                $id_shared_systems . "')";
        return $stmt;
    }

    /**
     * SQL query to get identities starting from system id
     * 
     * @param string $id_system the system id
     * 
     * @return string the sql statement
     */
    function getIdentityBySystem($id_system)
    {
        $stmt = 'select id_identity,label from ' . 
                $this->setTablePrefix('identity_management') .
                ' where system=' . $id_system . 
                        ' or share_with like \'%#' . $id_system . '#%\'';
        return $stmt;
    }
    
    /**
     * SQL query to get all identities 
     * a filter for system id may be used
     * 
     * @param string $filter_system the system id
     * 
     * @return string the sql statement
     */
    function getIdentities($filter_system = '')
    {
        $filter_stmt = '';
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' where system=' . $filter_system;
        }
        $stmt = 'select id_identity,label,system,share_with from ' . 
                $this->setTablePrefix('identity_management') . 
                $filter_stmt . 
                ' order by system';
        return $stmt;
    }
    
    /**
     * SQL query to get an identity by label
     * 
     * @param string $label the identity label
     * 
     * @return string the sql statement
     */
    function getIdentity($label)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('identity_management') .
                " where label='" . $label . "'";
        return $stmt;
    }
    
    /**
     * SQL query to get an identity by id
     * 
     * @param string $id_identity the identity id
     * 
     * @return string the sql statement
     */
    function getIdentityById($id_identity)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('identity_management') .
                ' where id_identity=' . $id_identity;
        return $stmt;
    }
    
    /**
     * SQL query to all hosts
     * 
     * @return string the sql statement
     */
    function getHosts()
    {
        $stmt = 'select * from ' . $this->setTablePrefix('host')  . 
                ' order by hostname';
        return $stmt;
    }
    
    /**
     * SQL query to get a host by id
     * 
     * @param string $id_host the host id
     * 
     * @return string the sql statement
     */
    function getHostById($id_host)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('host') .
                ' where id_host=' . $id_host;
        return $stmt;
    }
    
    /**
     * SQL query to update the host values
     * 
     * @param int    $id_host  the host id
     * @param string $ip       the ip address
     * @param string $hostname the hostname
     * 
     * @return string the sql statement
     */
    function updateHost($id_host, $ip, $hostname)
    {
        $stmt = 'update ' . $this->setTablePrefix('host') .
                " set ip='" . $ip . "', " .
                " hostname='" . $hostname . "' " .
                ' where id_host=' . $id_host;
        return $stmt;
    }
    
    /**
     * SQL query to update identity properties
     * 
     * @param int    $id_identity the identity id
     * @param string $label       the identity label
     * @param string $username    the username
     * @param string $password    the password
     * 
     * @return string the sql statement
     */
    function updateIdentity($id_identity, $label, $username, $password)
    {
        $stmt = 'update ' . $this->setTablePrefix('identity_management') .
                " set label='" . $label . "', " .
                " username='" . $username . "', " .
                " password='" . $password . "' " .
                ' where id_identity=' . $id_identity;
        return $stmt;
    }
    
    /**
     * SQL query to update the info to share identity across systems
     * 
     * @param int    $id_identity    the identity id
     * @param string $shared_systems the system that share this identity
     * 
     * @return string the sql statement
     */
    function updateIdentityShare($id_identity, $shared_systems)
    {
        $stmt = 'update ' . $this->setTablePrefix('identity_management') .
                " set share_with='" . $shared_systems . "' " .
                ' where id_identity=' . $id_identity;
        return $stmt;
    }
    
    /**
     * SQL query to get the system, host, db info's
     * 
     * @param string $filter_system filter on system id
     * 
     * @return string the sql statement
     */
    function getSystemHostDb($filter_system = '')
    {
        $filter_stmt = '';
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' and a.id_system=' . $filter_system;
        }
        $stmt = 'select a.id_system, a.name, b.id_host, b.ip, b.hostname, ' . 
                'c.id_db, c.label as db_label ' .
                'from ' . $this->setTablePrefix('system') . ' as a, ' . 
                $this->setTablePrefix('host') . ' as b, ' .
                $this->setTablePrefix('db') . ' as c, ' .
                $this->setTablePrefix('system_host_db') . ' as d ' .
                ' where a.id_system=d.id_system and ' . 
                'b.id_host=d.id_host and c.id_db=d.id_db ' .
                $filter_stmt .
                ' order by a.name,a.id_system,b.hostname,db_label';
        return $stmt;
    }    

    /**
     * SQL query to remove a database by id
     * 
     * @param int $id_db database id
     * 
     * @return string the sql statement
     */
    function deleteDatabase($id_db)
    {
        $stmt    = array();
        $stmt[0] = 'delete from ' . $this->setTablePrefix('db') . 
                   ' where id_db=' . $id_db;
        $stmt[1] = 'delete from ' . $this->setTablePrefix('system_host_db') . 
                   ' where id_db=' . $id_db;
        return $stmt;  
    }
    
    /**
     * SQL query to remove a host by id
     * 
     * @param int $id_system the system id
     * @param int $id_host the host id
     * 
     * @return string the sql statement
     */
    function deleteHost($id_system, $id_host)
    {
        $stmt    = array();
        $stmt[0] = 'delete from ' . $this->setTablePrefix('system_host_db') . 
                   ' where id_host=' . $id_host .
                   ' and id_system=' . $id_system;
        $stmt[1] = 'delete from ' . $this->setTablePrefix('host') . 
                   ' where id_host=' . $id_host . 
                   ' and ((select count(*) from ' .
                   $this->setTablePrefix('system_host_db') . 
                   ' where id_host=' . $id_host  . ') = 0)';
        return $stmt;  
    }
    
    /**
     * SQL query to delete a system by id
     * 
     * @param int $id_system the system id
     * 
     * @return string the sql statement
     */
    function deleteSystem($id_system)
    {
        $stmt = 'delete from ' . 
                $this->setTablePrefix('system') .
                ' where ' .
                ' id_system=' . $id_system;
        return $stmt;  
    }
    
    /**
     * SQL query delete an identity by id
     * 
     * @param int $id_identity the identity id
     * 
     * @return string the sql statement
     */
    function deleteIdentity($id_identity)
    {
        $stmt = 'delete from ' . 
                $this->setTablePrefix('identity_management') .
                ' where ' .
                ' id_identity=' . $id_identity;
        return $stmt;  
    }
    
    /**
     * SQL query to remove the system, host, db relation by system id
     * 
     * @param int $id_system the system id
     * 
     * @return string the sql statement
     */
    function deleteSystemHostDb($id_system)
    {
        $stmt = 'delete from ' . 
                $this->setTablePrefix('system_host_db') .
                ' where ' .
                ' id_system=' . $id_system;
        return $stmt;  
    }
    
    /**
     * SQL query to get all db info's 
     * 
     * @return string the sql statement
     */
    function getDb()
    {
        $stmt = 'select id_db, sid from ' . $this->setTablePrefix('db');
        return $stmt;
    }
    
    /**
     * SQL query to get minimal db info's by id
     * 
     * @param int $id_db the database id
     * 
     * @return string the sql statement
     */
    function getDbById($id_db)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('db') .
                ' where id_db=' . $id_db;
        return $stmt;
    }
    
    /**
     * SQL query to get all db info's by id
     * 
     * @param int $id_db the database id
     * 
     * @return string the sql statement
     */
    function getDbInfo($id_db)
    {
        $stmt = 'select a.label,a.sid,a.port,b.description from ' . 
                $this->setTablePrefix('db') . ' as a,' .
                $this->setTablePrefix('dbms_type') . ' as b' .
                ' where a.id_db=' . $id_db .
                ' and a.id_dbms_type=b.id_dbms_type';
        return $stmt;
    }
    
    /**
     * SQL query to get dbms lists
     * 
     * @return string the sql statement
     */
    function getdbmsList()
    {
        $stmt = 'select * from ' . $this->setTablePrefix('dbms_type');
        return $stmt;
    }
    
    /**
     * SQL query to add a database
     * 
     * @param int    $dbmsId the dbms id
     * @param string $label  the database label
     * @param string $sid    the database sid
     * @param int    $port   the port
     * 
     * @return string the sql statement
     */
    function setDb($dbmsId, $label, $sid, $port)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('db') . 
                ' (id_dbms_type, label, sid, port) values ('
                                            . $dbmsId . ",'"
                                            . $label . "','"
                                            . $sid . "',"
                                            . $port . ')';
        return $stmt;
    }
    
    /**
     * SQL query to update the database info's
     * 
     * @param int    $id_db  the db id
     * @param int    $dbmsId the dbms id
     * @param string $label  the database label
     * @param string $sid    the database sid
     * @param int    $port   the port
     * 
     * @return string the sql statement
     */
    function updateDb($id_db, $dbmsId, $label, $sid, $port)
    {
        $stmt = 'update ' . $this->setTablePrefix('db') . 
                ' set id_dbms_type=' . $dbmsId . 
                ", label='" . $label . "'," .
                " sid='" . $sid . "'," .
                ' port='.  $port . 
                ' where id_db=' . $id_db;
        return $stmt;
    }

    /**
     * SQL query to check if a database with the same label is already defined
     * 
     * @param string $label the database name
     * 
     * @return string the sql statement
     */
    function checkExistsDb($label)
    {
        $stmt = "select id_db " .
                "from " . $this->setTablePrefix('db') .
                " where label='" . $label . "'";
        return $stmt;
    }
    
    /**
     * SQL query to add a job
     * 
     * @param int    $shdId        the system/host/db id
     * @param int    $jobTypeId    the job type id
     * @param string $label        the job label
     * @param string $description  the description
     * @param string $priority     the priority
     * @param string $parameters   the parameters
     * @param string $identity     the identity
     * @param string $properties   the properties
     * @param string $num_failures the number of failures
     * 
     * @return string the sql statement
     */
    function setJob($shdId, $jobTypeId, $label, $description, 
                    $priority, $parameters, $identity,
                    $properties, $num_failures, $calendar)
    {
        $status  = 'W';
        $msgExec = '';
        
        $stmt = 'insert into ' . $this->setTablePrefix('jobs') 
                . ' (id_shd, id_job_type, label, description, status, priority,' 
                . ' msg_exec, parameters, identity, properties, num_failures, calendar) '
                . ' values ('
                . $shdId . ',' 
                . $jobTypeId . ",'"
                . $label . "','"
                . rawurlencode(stripslashes($description)) . "','"
                . $status . "',"
                . $priority . ",'"
                . $msgExec . "','"
                . $parameters . "',"
                . $identity . ",'"
                . $properties . "',"
                . $num_failures . ","
                . $calendar . ')';
        return $stmt;
    }
    
    /**
     * SQL query to add a workflow
     * 
     * @param string $label       the workflow label
     * @param string $description the description
     * @param string $status      the status
     * @param int    $id_system   the system id
     * @param int    $id_calendar the calendar id
     * 
     * @return string the sql statement
     */
    function setWorkflow($label, $description, $status, $id_system, $id_calendar)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflows') . 
                " (label,description,status,id_system,calendar) values ('" .
                $label . "','" . $description . "','" . $status .  
                "'," . $id_system . "," . $id_calendar . ")";
        return $stmt;
    }

    /**
     * SQL query to update workflow properties
     * 
     * @param int    $id_workflow the workflow id
     * @param string $label       the workflow label
     * @param string $description the description
     * @param int    $id_calendar the calendar id
     * 
     * @return string the sql statement
     */
    function updateWorkflow($id_workflow,$label,$description,$id_calendar)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflows') . 
                ' set ' .
                "label='" . $label . "', " .
                "description='" . $description . "', " .
                "calendar=" . $id_calendar .
                ' where id_workflow=' . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to set a workflow node
     * 
     * @param int    $id_workflow     the wokflow id
     * @param int    $id_parent_node  the node parent id (root = 0)
     * @param int    $id_to_exec      the job or workflow id to execute
     * @param int    $type_to_exec    the typology to execute (J=job, W=workflow)
     * @param string $exec_condition  the condition to be satisfied
     * @param int    $exec_properties the properties uses in object execution
     * @param string $status          the status of the node
     * 
     * @return string the sql statement
     */
    function setWorkflowNode($id_workflow, $id_parent_node, $id_to_exec,
                             $type_to_exec, $exec_condition, $exec_properties, $status)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflow_nodes') .
                ' (id_workflow,id_parent_node,id_to_exec, type_to_exec, ' .
                ' exec_condition,exec_properties, status) values (' .
                $id_workflow . "," .
                $id_parent_node . "," .
                $id_to_exec . ",'" .
                $type_to_exec . "','" .
                $exec_condition . "','" .
                $exec_properties . "','" .
                $status . "')";
        return $stmt;
    }

    /**
     * SQL query to update the workflow step properties
     * 
     * @param int    $id_workflow workflow id
     * @param int    $step        step
     * @param string $on_result   define if the action must be performed 
     *                            on success or error
     * @param int    $go_to_step  step to execute
     * @param string $when        define when execute the step
     * 
     * @return string the sql statement
     */
    function updateWorkflowNodeInfo($id_workflow, $id_node, $exec_condition, $exec_properties)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflow_nodes') . ' set ' .
                'exec_condition="' . $exec_condition . '",' .
                'exec_properties="' . $exec_properties . '"' .
                ' where id_workflow=' . $id_workflow . 
                ' and id_node=' . $id_node;
        return $stmt;
    }

    /**
     * SQL query to update the workflow step on deleting action
     * 
     * @param int    $id_workflow workflow id  
     * @param int    $step        step
     * @param string $on_result   define if the action must be performed 
     *                            on success or error
     * 
     * @return string the sql statement
     */
    function updateWorkflowStepWorkflowOnDelete($id_workflow, $step, $on_result)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflows_step') . ' set ' .
                $on_result . '=0' . ',' . 
                $on_result . "_when='R'" .
                ' where id_workflow=' . $id_workflow .
                ' and ' . $on_result . '=' . $step;
        return $stmt;
    }

    /**
     * SQL query to update the workflow step properties
     * 
     * @param int $id_workflow workflow id
     * @param int $id_job      job id  
     * @param int $step        step
     * 
     * @return string the sql statement
     */
    function updateWorkflowStepActions($id_workflow, $id_job, $step)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflows_step') . ' set ' .
                'id_job=' . $id_job .
                ' where id_workflow=' . $id_workflow . 
                ' and step=' . $step;
        return $stmt;
    }

    /**
     * SQL query to update the workflow status
     * 
     * @param int    $id_workflow workflow id
     * @param string $status      status
     * @param int    $end_exec    end execution time
     * 
     * @return string the sql statement
     */
    function updateWorkflowStatus($id_workflow, $status, $end_exec='')
    {
        $stmt = 'update ' . $this->setTablePrefix('workflows') . ' set ' .
                "status='" . $status . "'";
        if ($end_exec != '') {
              $stmt .= ',end_exec=' . $end_exec;
        }
        $stmt .= ' where id_workflow=' . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to update the workflow step status
     * 
     * @param int    $id_workflow workflow id
     * @param string $step        step
     * @param int    $status      status
     * 
     * @return string the sql statement
     */
    function updateWorkflowNodeStatus($id_workflow, $id_node, $status)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflow_nodes') . ' set ' .
                "status='" . $status . "'" .
                ' where id_workflow=' . $id_workflow; 
        if ($id_node != '*') {
            $stmt .= ' and id_node=' . $id_node;
        }
        return $stmt;
    }

    /**
     * SQL query to set the dynamic parameters ti use in the next workflow step
     * 
     * @param int    $id_workflow    workflow id
     * @param int    $step           step
     * @param string $dynamic_params dynamic parameters
     * 
     * @return string the sql statement
     */
    function setWorkflowStepDynamicParams($id_workflow, $step, $dynamic_params)
    {
        $stmt = 'update ' . $this->setTablePrefix('workflows_step') . ' set ' .
                "dynamic_params='" . $dynamic_params . "'" .
                ' where id_workflow=' . $id_workflow . 
                ' and step=' . $step;            
        return $stmt;
    }

    /**
     * SQL query to retrieve the dynamic parameters for step
     * 
     * @param int $id_job job id
     * 
     * @return string the sql statement
     */
    function getWorkflowStepDynamicParams($id_job)
    {
        $stmt = 'select dynamic_params from ' . 
                $this->setTablePrefix('workflows_step') .
                ' where id_job=' . $id_job . 
                " and status='R'";            
        return $stmt;
    }

    /**
     * SQL query to get info about a wf node
     * 
     * @param int $id_workflow workflow id 
     * @param int $id_node     node id
     * 
     * @return string the sql statement
     */
    function getWorkflowNodeInfo($id_workflow, $id_node)
    {
        $stmt = 'select * ' . 
                'from ' . $this->setTablePrefix('workflow_nodes') .
                ' where id_workflow=' . $id_workflow . 
                ' and id_node=' . $id_node;
        return $stmt;
    }

    /**
     * SQL query to check for running workflow
     * 
     * @param int $id_job job id
     * 
     * @return string the sql statement
     */
    function checkWorkflowNodeInfoByIdJob($id_job)
    {
        $stmt = 'select * ' . 
                'from ' . $this->setTablePrefix('workflow_nodes') .
                ' where id_to_exec=' . $id_job .
                " and status='R' and type_to_exec = 'J'";
        return $stmt;
    }

    /**
     * SQL query to check for running nodes in workflow
     *
     *
     * @return string the sql statement
     */
    function checkWorkflowRunningNodes()
    {
        $stmt = 'select * ' .
                'from ' . $this->setTablePrefix('workflow_nodes') .
                " where status in ('R','W')";
        return $stmt;
    }

    /**
     * SQL query to delete worklow node
     * 
     * @param int $id_workflow workflow id     
     * @param int $id_node     node_id
     * 
     * @return string the sql statement
     */
    function deleteWorkflowNode($id_workflow, $id_node)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('workflow_nodes') .
                ' where id_workflow=' . $id_workflow;
        if ($id_node != '*') {
                $stmt .=' and id_node=' . $id_node;
        }
        return $stmt;
    }

    /**
     * SQL query to delete a worklow
     * 
     * @param int $id_workflow workflow id
     * 
     * @return string the sql statement
     */
    function deleteWorkflow($id_workflow)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('workflows') . 
                ' where id_workflow=' . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to delete the defined crontab for the specified worklow
     * 
     * @param int $id_workflow workflow id
     * 
     * @return string the sql statement
     */
    function deleteWorkflowCrontab($id_workflow)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('workflow_crontab') . 
                ' where id_workflow=' . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to copy a job
     * 
     * @param string $jobId     the job id
     * @param string $new_label the new label
     * 
     * @return string the sql statement
     */
    function copyJob($jobId, $new_label)
    {
        $stmt = "insert into " . $this->setTablePrefix('jobs')
                . ' (id_shd, id_job_type, label, description, status, priority,' 
                . ' msg_exec, parameters, identity, properties, num_failures) '
                . " select id_shd,id_job_type,'"
                . $new_label
                . "',description,'L',priority,''," 
                . 'parameters,identity,properties,num_failures from '
                . $this->setTablePrefix('jobs')
                . '  where id_job ='
                . $jobId;
        return $stmt;
    }
    
    /**
     * SQL query to copy a workflow
     * 
     * @param string $id_workflow the workflow id
     * @param string $new_label   the new label
     * 
     * @return string the sql statement
     */
    function copyWorkflow($id_workflow, $new_label)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflows') 
                . ' (label, description, status, id_system) '
                . " select '"
                . $new_label
                . "',description, status, id_system from "
                . $this->setTablePrefix('workflows')
                . '  where id_workflow ='
                . $id_workflow;
        return $stmt;
    }
    
    /**
     * SQL query to copy a workflow step
     * 
     * @param string $id_workflow     the workflow id
     * @param string $new_id_workflow the new workflow id
     * 
     * @return string the sql statement
     */
    function copyWorkflowStep($id_workflow, $new_id_workflow)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflows_step') 
                . ' (id_workflow,id_job,step,on_success,on_success_when,on_error,' 
                . 'on_error_when,status,dynamic_params) '
                . ' select ' 
                . $new_id_workflow
                . ',id_job,step,on_success,on_success_when,on_error,on_error_when,'
                .'status,dynamic_params from '
                . $this->setTablePrefix('workflows_step')
                . '  where id_workflow ='
                . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to copy a workflow crontab
     * 
     * @param string $id_workflow     the workflow id
     * @param string $new_id_workflow the new workflow id
     * 
     * @return string the sql statement
     */
    function copyWorkflowCrontab($id_workflow, $new_id_workflow)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflow_crontab')
                . ' (id_workflow,crontab_m,crontab_h,crontab_dom,'
                .'crontab_mon,crontab_dow,schedule_type) '
                . ' select ' 
                . $new_id_workflow
                . ',crontab_m,crontab_h,crontab_dom,crontab_mon,'
                .'crontab_dow,schedule_type from '
                . $this->setTablePrefix('workflow_crontab')
                . ' where id_workflow ='
                . $id_workflow;
        return $stmt;
    }
    
    /**
     * SQL query to update the job properties
     *
     * @param int    $jobId       the job id 
     * @param int    $shdId       the system/host/db id
     * @param int    $jobTypeId   the job type id
     * @param string $label       the job label
     * @param string $description the description
     * @param string $priority    the priority
     * @param string $properties  the properties
     * @param string $parameters  the parameters
     * @param string $identity    the identity
     * 
     * @return string the sql statement
     */
    function updateJob($jobId, $shdId, $jobTypeId, $label, $description, $priority, 
                       $properties, $parameters='', $identity, $calendar)
    {
        $stmt = 'update ' . $this->setTablePrefix('jobs') . ' set ' .
                ' id_shd=' . $shdId . ', ' .
                ' id_job_type=' . $jobTypeId . ', ' .
                " label='" . $label . "', " .
                " description='" . rawurlencode(stripslashes($description)) . "', " .
                ' priority=' . $priority . ', ' .
                " parameters='" . $parameters . "', " .
                ' identity=' . $identity . ', ' .
                ' calendar=' . $calendar . ', ' .
                " properties='" . $properties . "' " .
                ' where id_job=' . $jobId;
        return $stmt;
    }

    /**
     * SQL query to add crontab for job
     * 
     * @param int    $jobId         the job id
     * @param string $minute        the crontab for minutes
     * @param string $hour          the crontab for hour
     * @param string $day           the crontab for day
     * @param string $month         the crontab for month
     * @param string $dayweek       the crontab for dayweek
     * @param string $schedule_type the type of schedule
     * 
     * @return string the sql statement
     */
    function setJobCrontab($jobId, $minute, $hour, 
                           $day, $month, $dayweek, $schedule_type)
    {
        $stmt = "insert into " . $this->setTablePrefix('job_crontab') . " values ("
                                            . $jobId . ",'" 
                                            . $minute . "','"
                                            . $hour . "','"
                                            . $day . "','"
                                            . $month . "','"
                                            . $dayweek . "','"
                                            . $schedule_type . "')";
        return $stmt;
    }

    /**
     * SQL query to add crontab for workflow
     * 
     * @param int    $id_workflow   the workwflow id
     * @param string $minute        the crontab for minutes
     * @param string $hour          the crontab for hour
     * @param string $day           the crontab for day
     * @param string $month         the crontab for month
     * @param string $dayweek       the crontab for dayweek
     * @param string $schedule_type the type of schedule
     * 
     * @return string the sql statement
     */
    function setWorkflowCrontab($id_workflow, $minute, $hour, $day, 
                                $month, $dayweek, $schedule_type)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('workflow_crontab') . 
                ' values ('
                            . $id_workflow . ",'" 
                            . $minute . "','"
                            . $hour . "','"
                            . $day . "','"
                            . $month . "','"
                            . $dayweek . "','"
                            . $schedule_type . "')";
        return $stmt;
    }
    
    /**
     * SQL query to copy the job crontab
     * 
     * @param int $jobId the job id
     * 
     * @return string the sql statement
     */
    function copyJobCrontab($jobId)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('job_crontab')
                . ' (crontab_m,crontab_h,crontab_dom,crontab_mon,crontab_dow,'
                .'schedule_type) '
                . ' select crontab_m,crontab_h,crontab_dom,crontab_mon,'
                .'crontab_dow,schedule_type from '
                . $this->setTablePrefix('job_crontab')
                . ' where id_job ='
                . $jobId;
        return $stmt;
    }    

    /**
     * SQL query to update crontab values
     * 
     * @param int    $jobId         the job id
     * @param string $minute        the crontab for minutes
     * @param string $hour          the crontab for hour
     * @param string $day           the crontab for day
     * @param string $month         the crontab for month
     * @param string $dayweek       the crontab for dayweek
     * @param string $schedule_type the type of schedule
     * 
     * @return string the sql statement
     */
    function updateJobCrontab($jobId, $minute, $hour, 
                              $day, $month, $dayweek, $schedule_type)
    {
        $stmt = "update " . $this->setTablePrefix('job_crontab') . " set " .
                " crontab_m='" . $minute . "', " .
                " crontab_h='" . $hour . "', " .
                " crontab_dom='" . $day . "', " .
                " crontab_mon='" . $month . "', " .
                " crontab_dow='" . $dayweek . "'," .
                " schedule_type='" . $schedule_type . "'" .
                " where id_job=" . $jobId;
        return $stmt;
    }

    /**
     * SQL query to set the system, host, db relation
     * 
     * @param int    $id_workflow   the workflow id
     * @param string $minute        the crontab for minutes
     * @param string $hour          the crontab for hour
     * @param string $day           the crontab for day
     * @param string $month         the crontab for month
     * @param string $dayweek       the crontab for dayweek
     * @param string $schedule_type the type of schedule
     * 
     * @return string the sql statement
     */
    function updateWorkflowCrontab($id_workflow, $minute, $hour, 
                                   $day, $month, $dayweek, $schedule_type)
    {
        $stmt = "update " . $this->setTablePrefix('workflow_crontab') . " set " .
                " crontab_m='" . $minute . "', " .
                " crontab_h='" . $hour . "', " .
                " crontab_dom='" . $day . "', " .
                " crontab_mon='" . $month . "', " .
                " crontab_dow='" . $dayweek . "'," .
                " schedule_type='" . $schedule_type . "'" .
                " where id_workflow=" . $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to retrieve all jobs with relative systems
     * 
     * @param string $filter_job_label filter job label
     * @param string $filter_system    filter system id
     * @param string $filter_status    filter job status
     * @param string $filter_result    filter job result
     * @param string $orderby          filter order by
     * 
     * @return string the sql statement
     */
    function getSystemJobs($filter_job_label = '', 
                           $filter_system = '', 
                           $filter_status = '',
                           $filter_result = '', 
                           $orderby = '')
    {
        $filter_stmt = '';
        if ($filter_job_label != '' && $filter_job_label != '*') {
            $filter_stmt .= ' and a.label like \'%' . addslashes($filter_job_label) . '%\'';
        }
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' and b.id_system=' . $filter_system;
        }
        if ($filter_status != '' && $filter_status != '*') {
            $filter_stmt .= ' and a.status=\'' . $filter_status . '\'';
        }
        if ($filter_result != '' && $filter_result != '*') {
            $filter_stmt .= ' and a.status_exec=' . $filter_result;
        }
        if ($orderby == '') {
            $orderby = ' b.name,a.label desc';
        }
        $stmt = 'select a.label, a.description, a.end_exec, a.status, '.
                'a.status_exec, a.msg_exec, b.id_system, b.name, d.*, ' .
                'e.calendar as calendars  ' .
                ' from ' . 
                $this->setTablePrefix('jobs') . ' as a, ' .
                $this->setTablePrefix('system') . ' as b, ' .
                $this->setTablePrefix('system_host_db') . ' as c, ' .
                $this->setTablePrefix('job_crontab') . ' as d, ' .
                $this->setTablePrefix('calendars') . ' as e ' .
                ' where a.id_shd=c.id_shd ' .
                ' and b.id_system=c.id_system' .
                ' and a.id_job=d.id_job' .
                ' and a.calendar=e.id_calendar ' .
                $filter_stmt .
                ' order by ' . $orderby;
        return $stmt;
    }

    /**
     * SQL query to retrieve the last step added
     * 
     * @param int $id_workflow workflow id 
     * 
     * @return string the sql statement
     */
    function getLastWorkflowStep($id_workflow)
    {
        $stmt = 'select max(step) ' .
                ' from ' .                
                $this->setTablePrefix('workflows_step') .
                " where id_workflow=" . $id_workflow;
        return $stmt;
    }
    
    /**
     * SQL query to retrieve all workflows
     * 
     * @param string $filter_workflow_label  filter workflow label
     * @param string $filter_workflow_status filter workflow status
     * 
     * @return string the sql statement
     */
    function getWorkflows($filter_workflow_label='', $filter_workflow_status='')
    {
        $filter_stmt = '';
        if ($filter_workflow_label != '' && $filter_workflow_label != '*') {
            $filter_stmt .= ' and a.label like \'%' . 
                            addslashes($filter_workflow_label) . '%\'';
        }
        if ($filter_workflow_status != '' && $filter_workflow_status != '*') {
            $filter_stmt .= ' and a.status like \'%' . 
                            $filter_workflow_status . '%\'';
        }
        $stmt = 'select a.*,b.*,c.calendar as calendars, d.name as system_label ' .
                ' from ' .
                $this->setTablePrefix('workflows') . ' as a, ' .
                $this->setTablePrefix('workflow_crontab') . ' as b, ' .
                $this->setTablePrefix('calendars') . ' as c, ' .
                $this->setTablePrefix('system') . ' as d ' .
                ' where a.id_workflow=b.id_workflow ' .
                ' and a.calendar=c.id_calendar ' .
                ' and a.id_system=d.id_system ' .
                $filter_stmt .
                ' order by a.label';
        return $stmt;
    }

    /**
     * SQL query to retrieve all workflows label
     *
     * @param int $id_job the workflow id
     *
     * @return string the sql statement
     */
    function getWorkflowsLabel($id_wf = '')
    {
        $stmt = 'select id_workflow, label ' .
                ' from ' . $this->setTablePrefix('workflows');
        if ($id_job != '') {
            $stmt .= ' where id_workflow=' . $id_wf;
        }
        return $stmt;
    }

    /**
     * SQL query to retrieve minimal workflow info
     * 
     * @param int $id_workflow workflow id 
     * 
     * @return string the sql statement
     */
    function getWorkflowBasicInfoById($id_workflow)
    {
        $stmt = 'select a.*, b.* ' .
                ' from ' .
                $this->setTablePrefix('workflows') . ' as a, ' .
                $this->setTablePrefix('workflow_crontab') . ' as b ' .
                ' where a.id_workflow=b.id_workflow and a.id_workflow=' . 
                $id_workflow;
        return $stmt;
    }

    /**
     * SQL query to retrieve  workflow info
     * 
     * @param int    $id_workflow workflow id
     * 
     * @return string the sql statement
     */
    function getWorkflowInfoById($id_workflow, $id_parent_node = '*', $exec_condition = '*')
    {
        $stmt = 'select * ' .
                ' from ' .
                $this->setTablePrefix('workflow_nodes') .
                ' where id_workflow=' . $id_workflow;
        if ($id_parent_node != '*') {
            $stmt .= ' and id_parent_node = ' . $id_parent_node;
        }
        if ($exec_condition != '*') {
            $stmt .= ' and exec_condition = ' . $exec_condition;
        }
        $stmt .= ' order by id_node';
        return $stmt;
    }

    /**
     * SQL query to retrieve all notifications with relative systems
     * 
     * @param string $filter_job_label   filter jobs label
     * @param string $filter_system      filter system
     * @param string $filter_notify_type filter notify_type
     * @param string $filter_notify_on   filter notify_on
     * 
     * @return string the sql statement
     */
    function getSystemJobsNotify($filter_job_label = '',
                                 $filter_system = '',
                                 $filter_notify_type = '',
                                 $filter_notify_on = '')
    {
        $filter_stmt = '';
        if ($filter_job_label != '' && $filter_job_label != '*') {
            $filter_stmt .= ' and a.label like \'%' . addslashes($filter_job_label) . '%\'';
        }
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' and b.id_system=' . $filter_system;
        }
        if ($filter_notify_type != '' && $filter_notify_type != '*') {
            $filter_stmt .= ' and d.id_notify_type=' . $filter_notify_type;
        }
        if ($filter_notify_on != '' && $filter_notify_on != '*') {
            $filter_stmt .= " and d.notify_on like '%-" . $filter_notify_on . "-%'";
        }
        $stmt = 'select a.label as job_label, b.id_system, b.name as system_name,'.
                ' d.id_notify, d.notify_on, d.parameters, e.label as notify_label' .
                ' from ' . 
                $this->setTablePrefix('jobs') . ' as a, ' .
                $this->setTablePrefix('system') . ' as b, ' .
                $this->setTablePrefix('system_host_db') . ' as c, ' .
                $this->setTablePrefix('notify') . ' as d, ' .
                $this->setTablePrefix('notify_type') . ' as e ' .
                ' where a.id_shd=c.id_shd ' .
                ' and b.id_system=c.id_system' .
                ' and a.id_job=d.id_job' .
                ' and d.id_notify_type=e.id_notify_type' .
                $filter_stmt .
                ' order by b.name,job_label';
        return $stmt;
    }

    /**
     * SQL query to retrieve the job type list
     * 
     * @return string the sql statement
     */
    function getJobTypeList()
    {
        $stmt = 'select * ' .
                ' from ' . $this->setTablePrefix('job_type') . 
                ' order by category,label';
        return $stmt;
    }

    /**
     * SQL query to retrieve the job type label
     * 
     * @param int $jobTypeId the job type id
     * 
     * @return string the sql statement
     */
    function getJobTypeLabel($jobTypeId)
    {
        $stmt = 'select label, category ' .
                ' from ' . $this->setTablePrefix('job_type') .
                ' where id_job_type=' . $jobTypeId;
        return $stmt;
    }

    /**
     * SQL query to check if a job with the same label is already defined
     * 
     * @param string $label the job label
     * 
     * @return string the sql statement
     */
    function checkExistsJob($label)
    {
        $stmt = 'select id_job ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                " where label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to check if a workflow with the same label is already defined
     * 
     * @param string $label the workflow label
     * 
     * @return string the sql statement
     */
    function checkExistsWorkflow($label)
    {
        $stmt = "select id_workflow " .
                " from " . $this->setTablePrefix('workflows') . 
                " where label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to check if a plugin with the same label is already defined
     * 
     * @param string $label the plugin label
     * 
     * @return string the sql statement
     */
    function checkExistsPlugin($label)
    {
        $stmt = 'select id_job_type ' .
                ' from ' . $this->setTablePrefix('job_type') . 
                " where label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to check if a notification plugin with the same 
     * label is already defined
     * 
     * @param string $label the notification plugin label
     * 
     * @return string the sql statement
     */
    function checkExistsPluginNotify($label)
    {
        $stmt = 'select id_notify_type ' .
                ' from ' . $this->setTablePrefix('notify_type') . 
                " where label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to check if a notification is associated with the job
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function checkJobNotify($id_job)
    {
        $stmt = 'select count(id_job) ' .
                ' from ' . $this->setTablePrefix('notify') . 
                " where id_job=" . $id_job;
        return $stmt;
    }

    /**
     * SQL query to check if a job is associated with the database
     * 
     * @param string $id_db the database id
     * 
     * @return string the sql statement
     */
    function checkJobDatabase($id_db)
    {
        $stmt = 'select id_job, label ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                ' where id_shd in (' .
                                   ' select id_shd ' .
                                   ' from '  . 
                                   $this->setTablePrefix('system_host_db') .
                                   ' where id_db=' . $id_db .
                                   ')';
        return $stmt;
    }

    /**
     * SQL query to check if a job is associated with the system
     * 
     * @param string $id_system the system id
     * 
     * @return string the sql statement
     */
    function checkJobSystem($id_system)
    {
        $stmt = 'select id_job, label ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                ' where id_shd in (' .
                                   ' select id_shd' .
                                   ' from '  . 
                                   $this->setTablePrefix('system_host_db') .
                                   ' where id_system=' . $id_system .
                                   ')';
        return $stmt;
    }

    /**
     * SQL query to check if a plugin is associated with any job
     * 
     * @param string $id_job_type the plugin type
     * 
     * @return string the sql statement
     */
    function checkJobPlugin($id_job_type)
    {
        $stmt = 'select id_job, label ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                ' where id_job_type=' . $id_job_type;
        return $stmt;
    }

    /**
     * SQL query to check if a notification plugin 
     * is associated with any job
     * 
     * @param string $id_notify_type the plugin notification type
     * 
     * @return string the sql statement
     */
    function checkNotifyPlugin($id_notify_type)
    {
        $stmt = 'select id_notify ' .
                ' from ' . $this->setTablePrefix('notify') . 
                ' where id_notify_type=' . $id_notify_type;
        return $stmt;
    }
    
    /**
     * SQL query to check if the host is associated with any job
     * 
     * @param string $id_system the system id
     * @param string $id_host the host id
     * 
     * @return string the sql statement
     */
    function checkJobHost($id_system, $id_host)
    {
        $stmt = 'select id_job, label ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                ' where id_shd in (' .
                                   ' select id_shd ' .
                                   ' from '  . 
                                   $this->setTablePrefix('system_host_db') .
                                   ' where id_host=' . $id_host .
                                   ' and id_system=' . $id_system .
                                   ')';
        return $stmt;
    }

    /**
     * SQL query to check if the identity is associated with any job
     * 
     * @param string $id_identity the identity id
     * 
     * @return string the sql statement
     */
    function checkJobIdentity($id_identity)
    {
        $stmt = 'select label  ' .
                ' from ' . $this->setTablePrefix('jobs') . 
                ' where identity=' . $id_identity;
        return $stmt;
    }
    
    /**
     * SQL query to remove a plugin
     * 
     * @param int $id_job_type the job type id
     * 
     * @return string the sql statement
     */
    function deletePlugin($id_job_type)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('job_type') . 
                ' where id_job_type=' . $id_job_type;
        return $stmt;
    }

    /**
     * SQL query to remove a notification plugin
     * 
     * @param int $id_notify_type the notification type id
     * 
     * @return string the sql statement
     */  
    function deletePluginNotification($id_notify_type)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('notify_type') . 
                ' where id_notify_type=' . $id_notify_type;
        return $stmt;
    }
    
    /**
     * SQL query to remove a job
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */  
    function deleteJob($id_job)
    {
        $stmt    = array();
        $stmt[0] = 'delete from ' . $this->setTablePrefix('jobs') . 
                   ' where id_job=' . $id_job;
        $stmt[1] = 'delete from ' . $this->setTablePrefix('job_crontab') . 
                   ' where id_job=' . $id_job;
        $stmt[2] = 'delete from ' . $this->setTablePrefix('jobs_archive') . 
                   ' where id_job=' . $id_job;
        return $stmt;
    }

    /**
     * SQL query to retrieve all job labels
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getJobsLabel($id_job = '')
    {
        $stmt = 'select id_job, label ' .
                ' from ' . $this->setTablePrefix('jobs');
        if ($id_job != '') {
            $stmt .= ' where id_job=' . $id_job;
        }
        return $stmt;
    }

    /**
     * SQL query to retrieve the job type
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getJobType($id_job)
    {
        $stmt = 'select  b.label, b.category ' .
                ' from ' . 
                $this->setTablePrefix('jobs') . ' as a, ' .
                $this->setTablePrefix('job_type') . ' as b ' . 
                ' where a.id_job_type=b.id_job_type ' . 
                ' and a.id_job=' . $id_job;
        return $stmt;
    }

    /**
     * SQL query to retrieve the job result and execution message
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getJobStatusDetail($id_job)
    {
        $stmt = 'select status, msg_exec ' .
                ' from ' . $this->setTablePrefix('jobs') .
                ' where id_job=' . $id_job;
        return $stmt;
    }

    /**
     * SQL query to retrieve the job status
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getJobStatus($id_job)
    {
        $stmt = 'select status, id_shd ' .
                ' from ' . $this->setTablePrefix('jobs') .
                ' where id_job=' . $id_job;
        return $stmt;
    }
    
    /**
     * SQL query to retrieve the job minimal info
     * 
     * @param int $id_job the job id
     * 
     * @return string the sql statement
     */
    function getJobBasicInfo($id_job)
    {
        $stmt = 'select label,description ' .
                ' from ' . $this->setTablePrefix('jobs') .
                ' where id_job=' . $id_job;
        return $stmt;
    }

    /**
     * SQL query to set the job status
     * 
     * @param int    $id_job    the job id
     * @param string $newStatus the new status
     * @param string $message   the message to display
     * 
     * @return string the sql statement
     */
    function setJobStatus($id_job, $newStatus, $message='')
    {
        $msg_statement = '';
        if ($message != '') {
            $msg_statement = ", msg_exec='" . $message . "'";
        }
        $stmt = 'update ' . $this->setTablePrefix('jobs') . ' set ' .
                " status='" . $newStatus . "' " .
                $msg_statement .
                ' where id_job=' . $id_job;
        return $stmt;
    }

    /**
     * SQL query to retrieve info needed to execute the specified job 
     * 
     * @param string $label the job label
     * 
     * @return string the sql statement
     */
    function getJobsToExecRPC($label)
    {
        $stmt = 'select distinct a.id_job, b.label, b.category ' .
                'from ' . $this->setTablePrefix('jobs') . ' as a, ' . 
                          $this->setTablePrefix('job_type') . ' as b ' .
                'where a.id_job_type=b.id_job_type ' .
                "and a.status='W' " .
                "and a.label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to retrieve all jobs in timeout
     * 
     * @return string the sql statement
     */
    function getJobsTimeout()
    {
        
        $timeout = time()-($GLOBALS['conf']['env']['job_timeout']) * 60;
        
        $stmt = 'select a.id_job ' .
                'from ' . $this->setTablePrefix('jobs') . ' as a ' . 
                "where a.status='R' " .
                "and a.start_exec < " .
                $timeout;                
        return $stmt;
    }
    
    /**
     * SQL query to retrieve all jobs to execute
     * 
     * @return string the sql statement
     */
    function getJobsToExec($available_shd)
    {
        
        //Note: length(d.crontab_dom)=85 if crontab_dom is *
        
        $stmt = 'select distinct a.id_job, c.label, c.category ' .
                'from ' . $this->setTablePrefix('jobs') . ' as a, ' . 
                          $this->setTablePrefix('job_type') . ' as c, ' .
                          $this->setTablePrefix('job_crontab') . ' as d, ' .
                          $this->setTablePrefix('calendars') . ' as e ' .
                'where a.id_job_type=c.id_job_type ' .
                "and (a.calendar=0 or (a.calendar=e.id_calendar and e.calendar like '%#" . mktime(0,0,0) . "#%' )) " .
                'and a.id_shd not in ('  . $available_shd . ') ' .
                'and a.id_job=d.id_job '  .
                "and a.status='W' " .
                "and (d.schedule_type='A' or d.schedule_type='J')  " .
                "and d.crontab_m like '%-" . date('i') . "-%' " .
                "and d.crontab_h like '%-" . date('G') . "-%' " .
                "and d.crontab_mon like '%-" . date('n') . "-%' " .
                'and (' .
                '     (length(d.crontab_dom) != 85 and ' . 
                      "length(d.crontab_dow) != 15 and (d.crontab_dom like '%-" . 
                       date('j') . 
                       "-%'  or d.crontab_dow like '%-" . date('w') . "-%')) ".
                '      or ' .
                "      (d.crontab_dom like '%-" . date('j') . 
                       "-%' and d.crontab_dow like '%-" . date('w') . "-%')" .
                '    )   ' .
                'order by a.priority desc';
        return $stmt;
    }
    
    /**
    * Query to get all jobs part of workflow to execute
    * 
    * @return string $stmt The sql statement
    */
    function getJobsWorkflowToExec($available_shd)
    {
        
        //Note: length(d.crontab_dom)=85 if crontab_dom is *
        
        $stmt = 'select distinct b.id_workflow, b.id_node ' .
                'from ' . $this->setTablePrefix('jobs') . ' as a, ' .
                          $this->setTablePrefix('workflow_nodes') . ' as b, ' .
                          //$this->setTablePrefix('job_type') . ' as c, ' .
                          $this->setTablePrefix('job_crontab') . ' as d, ' .
                          $this->setTablePrefix('workflows') . ' as e, ' .
                          $this->setTablePrefix('calendars') . ' as f ' .
                //'where a.id_job_type=c.id_job_type ' .
                'where ' .
                "(a.calendar=1 or (a.calendar=f.id_calendar and f.calendar like '%#" . mktime(0,0,0) . "#%' )) " .
                'and a.id_shd not in ('  . $available_shd . ') ' .
                //'and a.id_job=b.id_job '  .
                'and b.id_to_exec=d.id_job '  .
                //'and b.id_workflow=e.id_workflow '  .
                "and a.status='W' " .
                "and e.status='R' and b.status='W' " .
                "and d.schedule_type='T' " .
                "and d.crontab_m like '%-" . date('i') . "-%' " .
                "and d.crontab_h like '%-" . date('G') . "-%' " .
                "and d.crontab_mon like '%-" . date('n') . "-%' " .
                'and (' .
                '     (length(d.crontab_dom) != 85 and ' . 
                      "length(d.crontab_dow) != 15 and (d.crontab_dom like '%-" .
                       date('j') . 
                       "-%'  or d.crontab_dow like '%-" . date('w') . "-%')) ".
                "      or " .
                "      (d.crontab_dom like '%-" . date('j') . 
                       "-%' and d.crontab_dow like '%-" . 
                       date('w') . "-%')" .
                '    )   ' .
                'order by a.priority desc';
        return $stmt;
    }
    
    /**
    * Query to get all workflows to execute
    * 
    * @return string $stmt The sql statement
    */
    function getWorkflowsToExec($available_shd)
    {
        
        //Note: length(d.crontab_dom)=85 if crontab_dom is *
        
        $stmt = 'select distinct a.id_workflow, b.id_node '.
                'from ' . $this->setTablePrefix('workflows') . ' as a, ' .
                          $this->setTablePrefix('workflow_nodes') . ' as b, ' .
                          $this->setTablePrefix('calendars') . ' as c, ' .
                          $this->setTablePrefix('workflow_crontab') . ' as d ' .
                "where (a.calendar=1 or (a.calendar=c.id_calendar and c.calendar like '%#" . mktime(0,0,0) . "#%' )) " .
                "and a.status='W' " .
                "and b.id_parent_node = 0 " .
                "and a.id_workflow=b.id_workflow " .
                "and b.id_workflow=d.id_workflow " .
                "and d.schedule_type='A' " .
                "and d.crontab_m like '%-" . date('i') . "-%' " .
                "and d.crontab_h like '%-" . date('G') . "-%' " .
                "and d.crontab_mon like '%-" . date('n') . "-%' " .
                "and (" .
                '       (length(d.crontab_dom) != 85 and ' . 
                        "length(d.crontab_dow) != 15 and (d.crontab_dom like '%-" .
                         date('j') . "-%'  or d.crontab_dow like '%-" . 
                         date('w') . "-%')) ".
                '     or ' .
                "       (d.crontab_dom like '%-" . date('j') . 
                        "-%' and d.crontab_dow like '%-" . date('w') . "-%')" .
                '    )   ';
        return $stmt;
    }
    
    /**
     * SQL query to retrieve all info for the jobs to execute
     * 
     * @param string $jobId the job id
     * 
     * @return string the sql statement
     */
    function getJobToExecInfo ($jobId)
    {
        $stmt = 'select ' .
                'a.parameters, a.identity, b.ip, c.sid, c.port, '.
                'd.label as dbms, e.id_system ' .
                'from ' . $this->setTablePrefix('jobs') . ' as a, ' .
                          $this->setTablePrefix('host') . ' as b, ' .
                          $this->setTablePrefix('db') . ' as c, ' .
                          $this->setTablePrefix('dbms_type') . ' as d, ' .
                          $this->setTablePrefix('system_host_db') . ' as e ' .
                 ' where a.id_shd=e.id_shd ' .
                 ' and b.id_host=e.id_host ' .
                 ' and c.id_db=e.id_db ' .
                 ' and c.id_dbms_type=d.id_dbms_type ' .
                 ' and a.id_job='  . $jobId;
        return $stmt;
    }

    /**
     * SQL query to retrieve all info for the jobs
     * 
     * @param string $jobId the job id
     * 
     * @return string the sql statement
     */
    function getJobInfoById($jobId)
    {
        $stmt = 'select ' .
                ' a.id_job, a.label, a.description, a.priority, a.parameters, '.
                'a.identity, a.calendar,  a.properties, a.id_job_type, b.id_system, b.id_host,'.
                ' b.id_db, c.label as job_type_label, '.
                'c.category as job_type_category, d.* ' . 
                ' from ' . 
                $this->setTablePrefix('jobs') . ' as a, ' .
                $this->setTablePrefix('system_host_db') . ' as b, ' .
                $this->setTablePrefix('job_type') . ' as c, ' .
                $this->setTablePrefix('job_crontab') . ' as d ' .
                ' where ' .
                ' a.id_shd=b.id_shd and a.id_job_type=c.id_job_type and '.
                ' a.id_job=d.id_job and' .
                ' a.id_job=' . $jobId;
        return $stmt;
    }

    /**
     * SQL query to retrieve all job properties
     * 
     * @param string $jobId the job id
     * 
     * @return string the sql statement
     */
    function getJobProperties($jobId)
    {
        $stmt = 'select ' .
                ' properties, num_failures' . 
                ' from ' . 
                $this->setTablePrefix('jobs') .
                ' where ' .
                ' id_job=' . $jobId;
        return $stmt;
    }

    /**
     * SQL query to set the job execution start time
     * 
     * @param int    $jobId   the job id
     * @param string $msgExec the execution message
     * 
     * @return string the sql statement
     */
    function setJobStart($jobId, $msgExec)
    {
        $stmt = 'update ' . $this->setTablePrefix('jobs') . ' set ' .
                "status='R'," .
                'start_exec=' . time() . ', ' .
                "msg_exec='" . rawurlencode($msgExec) . "'" .
                ' where id_job=' . $jobId;
        return $stmt;
    }

    /**
     * SQL query to set the job execution end values
     * 
     * @param int    $jobId        the job id
     * @param int    $execStatus   the execution status
     * @param string $msgExec      the execution message
     * @param int    $num_failures the number of failures
     * 
     * @return string the sql statement
     */
    function setJobEnd($jobId, $execStatus, $msgExec, $num_failures=0)
    {
        $stmt = 'update ' . $this->setTablePrefix('jobs') . ' set ' .
                "status='W'," .
                'end_exec=' . time() . ', ' .
                'status_exec=' . $execStatus . ', ' .
                "msg_exec='" . rawurlencode($msgExec) . "', " .
                ' num_failures=' . $num_failures .
                ' where id_job=' . $jobId;
        return $stmt;  
    }    

    /**
     * SQL query to retrieve the archived job results
     * 
     * @param int $jobId the job id
     * 
     * @return string the sql statement
     */
    function getArchivedJobResults($jobId)
    {
        $stmt = 'select * from ' . $this->setTablePrefix('jobs_archive') .
                ' where id_job=' . $jobId .
                ' order by end_exec desc';
        return $stmt;  
    }

    /**
     * SQL query archive the result for the specified job
     * 
     * @param int $jobId the job id
     * 
     * @return string the sql statement
     */
    function archiveJobResult($jobId)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('jobs_archive') .
                ' select id_job,start_exec,end_exec,status_exec,msg_exec from ' . 
                $this->setTablePrefix('jobs') .
                ' where id_job=' . 
                $jobId;
        return $stmt;
    }

    /**
     * SQL query to apply the specified retention on the archived results
     * 
     * @param int $jobId the job id
     * @param int $date  the retention time
     * 
     * @return string the sql statement
     */
    function applyRetentionPolicy($jobId, $date)
    {
        $stmt = 'delete from '  . $this->setTablePrefix('jobs_archive') .
                ' where id_job=' . $jobId . 
                ' and end_exec < ' . $date;
        return $stmt;
    }

    /**
     * SQL query to add a notification
     * 
     * @param int    $jobId        the job id
     * @param int    $notifyTypeId the notification type id
     * @param string $notifyInfo   the notification info
     * @param string $notify_on    define if sends the notification 
     *                             on error or success
     * @param string $id_identity  the identity to use to send the notification
     * 
     * @return string the sql statement
     */
    function setNotify($jobId, $notifyTypeId, $notifyInfo, $notify_on, $id_identity)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('notify') 
                . ' (id_job, id_notify_type, parameters, notify_on, identity)'
                . ' values ('
                . $jobId . ',' 
                . $notifyTypeId . ",'"
                . $notifyInfo .    "','"
                . $notify_on . "',"
                . $id_identity . ')';
        return $stmt;  
    }

    /**
     * SQL query to copy a notification
     * 
     * @param int $notifyId the notification id
     * 
     * @return string the sql statement
     */
    function copyNotify($notifyId)
    {
        $stmt = 'insert into ' 
                . $this->setTablePrefix('notify')
                . ' (id_job, id_notify_type, parameters, notify_on, identity) '
                . ' select id_job, id_notify_type, parameters, '
                . ' notify_on,identity from '
                . $this->setTablePrefix('notify')
                . '  where id_notify ='
                . $notifyId;
        return $stmt;
    }

    /**
     * SQL query to update a notification values
     * 
     * @param int    $id_job      the job id
     * @param int    $id_notify   the notification id
     * @param string $notifyInfo  the notification info
     * @param string $notify_on   define if sends the notification 
     *                            on error or success
     * @param string $id_identity the identity to use to send the notification
     * 
     * @return string the sql statement
     */
    function updateNotify($id_job, $id_notify, $notifyInfo, $notify_on, $id_identity)
    {
        $stmt = 'update ' . $this->setTablePrefix('notify') .
                " set parameters='" . $notifyInfo .    "', " .
                ' id_job=' . $id_job . ', ' .
                " notify_on='" . $notify_on . "', " .
                ' identity=' . $id_identity .
                ' where id_notify=' . $id_notify;
        return $stmt;  
    }

    /**
     * SQL query to remove a notification
     * 
     * @param int $id_notify the notification id
     * 
     * @return string the sql statement
     */
    
    function deleteNotify($id_notify)
    {
        $stmt = 'delete from ' . $this->setTablePrefix('notify') .
                ' where id_notify=' . $id_notify;
        return $stmt;  
    }

    /**
     * SQL query to retrieve all notification associated with the specified job
     * 
     * @param int $jobId     the job id
     * @param int $notify_on the notify_on value to filter on
     * 
     * @return string the sql statement
     */
    function getNotifyInfoByJobId($jobId, $notify_on='')
    {
        $stmt = 'select a.id_job, a.parameters, a.notify_on, b.label, a.identity ' .
                'from ' . $this->setTablePrefix('notify') . ' as a, ' . 
                $this->setTablePrefix('notify_type') . ' as b ' .
                'where a.id_notify_type=b.id_notify_type ' .
                'and a.id_job=' . $jobId;
        if ($notify_on != '') {
            $stmt .= " and a.notify_on like '%-" . $notify_on . "-%'"; 
        }
        return $stmt;
    }

    /**
     * SQL query to retrieve all the properties for the specified notification
     * 
     * @param int $id_notify the notification id
     * 
     * @return string the sql statement
     */
    function getNotifyInfoById($id_notify)
    {
        $stmt = 'select * ' .
                'from ' . $this->setTablePrefix('notify') . ' as a ' .
                'where id_notify=' . $id_notify;
        return $stmt;
    }

    /**
     * SQL query to retrieve the notification type lists
     * 
     * @return string the sql statement
     */
    function getNotifyTypeList()
    {
        $stmt = 'select * from ' .
                $this->setTablePrefix('notify_type') . 
                ' order by label';
        return $stmt;
    }

    /**
     * SQL query to retrieve the notification type label
     * 
     * @param int $notifyTypeId the notification type id
     * 
     * @return string the sql statement
     */
    function getNotifyLabel($notifyTypeId)
    {
        $stmt = 'select label ' .
                ' from ' . $this->setTablePrefix('notify_type') .
                ' where id_notify_type=' . $notifyTypeId;
        return $stmt;
    }

    /**
     * SQL query to retrieve the login user type
     * 
     * @param string $username the username
     * 
     * @return string the sql statement
     */
    function getUserLoginType($username)
    {
        $stmt = 'select type ' .
                ' from ' . $this->setTablePrefix('users') .
                " where username='" . $username . "'";
        return $stmt;
    }

    /**
     * SQL query to check for authorized user
     * 
     * @param string $username the username
     * @param string $password the password
     * 
     * @return string the sql statement
     */
    function login($username,$password)
    {
        $stmt = 'select a.id_user ' .
                ' from ' . $this->setTablePrefix('users') . ' as a ' .
                " where a.username='" . $username . 
                "' and a.password='" . $password . "'";
        return $stmt;
    }

    /**
     * SQL query to retrieve the user info
     * 
     * @param string $username the username
     * 
     * @return string the sql statement
     */
    function getUserInfo($username)
    {
        $stmt = 'select a.id_user,a.language,b.id_group,c.id_role,'.
                'c.id_systems,d.label ' .
                ' from ' . $this->setTablePrefix('users') . ' as a, ' .
                $this->setTablePrefix('group_users') . ' as b, ' .
                $this->setTablePrefix('group_roles') . ' as c, ' .
                $this->setTablePrefix('roles') . ' as d ' .
                ' where b.id_group=c.id_group and a.id_user=b.id_user and ' . 
                ' c.id_role=d.id_role ' .
                " and a.username='" . $username . "'" . 
                ' order by c.id_role';
        return $stmt;
    }
    
    /* Users and groups */

    /**
     * SQL query to retrieve all users
     * 
     * @param string $filter_search filter search
     * @param string $filter_text   filter text
     * @param string $filter_group  filter group
     * 
     * @return string the sql statement
     */
    function getUsers($filter_search = '',
                      $filter_text = '',
                      $filter_group = '')
    {
        $filter_stmt = '';
        if ($filter_text != '') {
            if ($filter_search == 'name') {
                $filter_text = rawurlencode($filter_text);
            }
            $filter_stmt .= ' and a.' . $filter_search . 
                            ' like \'%' . addslashes($filter_text) . '%\'';
        }
        if ($filter_group != '' && $filter_group != '*') {
            $filter_stmt .= ' and b.groupname=\'' . $filter_group . '\'';
        }
        
        $stmt = 'select a.*, b.groupname ' .
                ' from ' . $this->setTablePrefix('users') . ' as a, ' .
                $this->setTablePrefix('groups') . ' as b, ' .
                $this->setTablePrefix('group_users') . ' as c ' .  
                ' where a.id_user=c.id_user ' .
                ' and b.id_group=c.id_group ' .
                $filter_stmt .
                ' order by a.name';
        return $stmt;
    }

    /**
     * SQL query to retrieve user properties
     * 
     * @param int $id_user the user id
     * 
     * @return string the sql statement
     */
    function getUserById($id_user)
    {
        $stmt = 'select username,name,mail,type,language ' .
                ' from ' . $this->setTablePrefix('users') .
                ' where id_user=' . $id_user;
        return $stmt;
    }

    /**
     * SQL query to check for existing user
     * 
     * @param string $username the username
     * 
     * @return string the sql statement
     */
    function checkExistsUser($username)
    {
        $stmt = 'select count(*) ' .
                ' from ' . $this->setTablePrefix('users') .
                " where username='" . $username .  "'";
        return $stmt;
    }

    /**
     * SQL query to add a user
     * 
     * @param string $username the username
     * @param string $password the password
     * @param string $name     the complete name
     * @param string $mail     the mail
     * @param string $type     the authentication type
     * @param string $language the language
     * 
     * @return string the sql statement
     */
    function setUser($username, $password, $name, $mail, $type, $language)
    {    
        $md5_password = md5($password);
        
        $stmt = 'insert into  ' . $this->setTablePrefix('users') .
                ' (username, password, name, mail, type, language) values (' . 
                "'" . $username . "'," .
                "'" . $md5_password . "'," . 
                "'" . rawurlencode(stripslashes($name)) . "'," .
                "'" . $mail . "'," .
                "'" . $type . "'," .
                "'" . $language . "')";
        return $stmt;
    }

    /**
     * SQL query to set the last login time for user
     * 
     * @param string $username  the username
     * @param int    $lastLogin the login time
     * 
     * @return string the sql statement
     */
    function setUserLastLogin($username, $lastLogin)
    {
        $stmt = 'update ' . $this->setTablePrefix('users') .
                ' set last_login=' . $lastLogin . 
                " where username='" . $username . "'";
        return $stmt;
    }

    /**
     * SQL query to update password for user
     * 
     * @param int    $id_user  the user id
     * @param string $password the password
     * 
     * @return string the sql statement
     */
    function updateUserPassword($id_user, $password)
    {
        $md5_password = md5($password);
        
        $stmt = 'update  ' . $this->setTablePrefix('users') .
                ' set ' .
                " password='" . $md5_password . "'" . 
                ' where id_user=' . $id_user;
        return $stmt;
    }

    /**
     * SQL query to update the user properties
     * 
     * @param int    $id_user  the user id
     * @param string $language the language    
     * @param string $name     the complete name
     * @param string $mail     the mail
     * 
     * @return string the sql statement
     */
    function updateUserProperties($id_user, $language, $name='', $mail='')
    {
        $stmt = 'update  ' . $this->setTablePrefix('users') . ' set ';
        if ($name != '') {                 
            $stmt .= " name='" . rawurlencode(stripslashes($name)) . "'," .
                     " mail='" . $mail . "',";
        }
        $stmt .= " language='" . $language . "'" .
                 ' where id_user=' . $id_user;
        return $stmt;
    }

    /**
     * SQL query to delete a user
     * 
     * @param int $id_user the user id
     * 
     * @return string the sql statement
     */
    function deleteUser($id_user)
    {
        $stmt    = array();
        $stmt[0] = 'delete from ' . $this->setTablePrefix('users') . 
                   ' where id_user=' . $id_user;
        $stmt[1] = 'delete from ' . $this->setTablePrefix('group_users') . 
                   ' where id_user=' . $id_user;                
        return $stmt;
    }
    
    /**
     * SQL query to add a user in a group
     * 
     * @param int $id_user  the user id
     * @param int $id_group the group id
     * 
     * @return string the sql statement
     */
    function setGroupUser($id_user, $id_group='1')
    {
        $stmt = 'insert into  ' . $this->setTablePrefix('group_users') .
                ' values (' . 
                $id_user . ',' .
                $id_group . ')';
        return $stmt;
    }

    /**
     * SQL query to delete a user from a group
     * 
     * @param int $id_user  the user id
     * @param int $id_group the group id
     * 
     * @return string the sql statement
     */
    function deleteGroupUser($id_user, $id_group)
    {
        $stmt = 'delete from ' .
                $this->setTablePrefix('group_users') .
                ' where id_user=' . $id_user . 
                ' and id_group=' . $id_group;
        return $stmt;
    }

    /**
     * SQL query to retrieve all groups
     * 
     * @return string the sql statement
     */
    function getGroups()
    {
        $stmt = 'select a.groupname, b.*, c.label ' .
                ' from ' . $this->setTablePrefix('groups') . ' as a, ' .
                $this->setTablePrefix('group_roles') . ' as b, ' .
                $this->setTablePrefix('roles') . ' as c ' .
                ' where a.id_group=b.id_group and ' .
                ' b.id_role=c.id_role' .
                ' order by a.groupname';
        return $stmt;
    }

    /**
     * SQL query to retrieve all roles
     * 
     * @return string the sql statement
     */
    function getRoles()
    {
        $stmt = 'select * ' .
                ' from ' . $this->setTablePrefix('roles');
        return $stmt;
    }

    /**
     * SQL query to retrieve the label for the specified role
     * 
     * @param int $id_role the role id
     * 
     * @return string the sql statement
     */
    function getRoleById($id_role)
    {
        $stmt = 'select label ' .
                ' from ' . $this->setTablePrefix('roles') .
                ' where id_role=' . $id_role;
        return $stmt;
    }
    
    /**
     * SQL query to check for existing group with the specified label
     * 
     * @param string $groupname the group name
     * 
     * @return string the sql statement
     */
    function checkExistsGroup($groupname)
    {
        $stmt = 'select id_group ' .
                ' from ' . $this->setTablePrefix('groups') .
                " where groupname='" . $groupname .  "'";
        return $stmt;
    }

    /**
     * SQL query to add a group
     * 
     * @param string $groupname the group name
     * 
     * @return string the sql statement
     */
    function setGroup($groupname)
    {
        $stmt = 'insert into  ' . $this->setTablePrefix('groups') .
                ' (groupname) values (' . 
                "'" . $groupname . "')";
        return $stmt;
    }
    
    /**
     * SQL query to define a group with a specified role 
     * 
     * @param int    $id_group   the group id
     * @param int    $id_role    the role id
     * @param string $id_systems the system id separated by comma
     * 
     * @return string the sql statement
     */
    function setGroupRole($id_group, $id_role, $id_systems)
    {
        $stmt = 'insert into  ' . $this->setTablePrefix('group_roles') .
                ' values (' .
                $id_group . ',' .
                $id_role . ',' .
                "'" . $id_systems . "')";
        return $stmt;
    }
    
    /**
     * SQL query to retrieve the properties for the specified group
     * 
     * @param int $id_group the group id
     * 
     * @return string the sql statement
     */
    function getGroupInfoById($id_group)
    {
        $stmt = 'select a.groupname,b.* ' .
                ' from ' . $this->setTablePrefix('groups') . ' as a, ' .
                $this->setTablePrefix('group_roles') . ' as b ' .
                ' where a.id_group=b.id_group and ' .
                ' a.id_group=' . $id_group;
        return $stmt;
    }

    /**
     * SQL query to update the grup name
     * 
     * @param int $id_group  the user id
     * @param int $groupname the group name
     * 
     * @return string the sql statement
     */
    function updateGroup($id_group, $groupname)
    {
        $stmt = 'update  ' . $this->setTablePrefix('groups') .
                ' set ' .
                " groupname='" . $groupname . "'" .
                ' where id_group=' . $id_group;
        return $stmt;
    }

    /**
     * SQL query to update the role for the the specified group 
     * 
     * @param int    $id_group   the group id
     * @param int    $id_role    the role id
     * @param string $id_systems the system id separated by comma
     * 
     * @return string the sql statement
     */
    function updateGroupRole($id_group, $id_role, $id_systems)
    {
        $stmt = 'update  ' . $this->setTablePrefix('group_roles') .
                ' set ' .
                ' id_role=' . $id_role . ',' .
                " id_systems='" . $id_systems . "'" .
                ' where id_group=' . $id_group;
        return $stmt;
    }
    
    /**
     * SQL query to delete the specified group 
     * 
     * @param int $id_group the group id
     * 
     * @return string the sql statement
     */
    function deleteGroup($id_group)
    {
        $stmt    = array();
        $stmt[0] = 'delete from ' . $this->setTablePrefix('groups') . 
                   ' where id_group=' . $id_group;
        $stmt[1] = 'delete from ' . $this->setTablePrefix('group_roles') . 
                   ' where id_group=' . $id_group;
        return $stmt;
    }

    /**
     * SQL query to retrieve all users in the specified group 
     * 
     * @param int $id_group the group id
     * 
     * @return string the sql statement
     */
    function checkUserGroup($id_group)
    {
        $stmt = 'select b.username ' .
                ' from ' . $this->setTablePrefix('group_users') . ' as a, ' .
                $this->setTablePrefix('users') . ' as b ' .
                ' where a.id_user=b.id_user and ' .
                ' a.id_group=' . $id_group;
        return $stmt;
    }

    /**
     * SQL query to retrieve the groups (not) associated with the specified user 
     * 
     * @param int $id_group the group id
     * @param int $in       must have the values: "IN" or "NOT IN" 
     * 
     * @return string the sql statement
     */
    function getGroupUser($id_group, $in)
    {
        //$in must have the values: "IN" or "NOT IN"        
        $stmt = 'select id_group, groupname ' .
                ' from ' . $this->setTablePrefix('groups')  .
                ' where id_group ' . $in . 
                                   ' (select id_group from ' .
                                      $this->setTablePrefix('group_users') .
                                   ' where id_user=' . $id_group . 
                                   ') order by groupname';
        return $stmt;
    }

    /**
     * SQL query to add a plugin
     * 
     * @param string $label    the plugin name
     * @param string $category the category
     * 
     * @return string the sql statement
     */
    function setPlugin($label, $category)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('job_type') . 
                " (label, category) values ('" . $label . "','" . $category . "')";
        return $stmt;
    }

    /**
     * SQL query to add a notification plugin
     * 
     * @param string $label the plugin name
     * 
     * @return string the sql statement
     */
    function setPluginNotification($label)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('notify_type') . 
                " (label) values ('" . $label . "')";
        return $stmt;
    }


    /* Calendars */

    /**
     * SQL query to check if calendar label is already defined
     *
     * @param string $label the calendar label
     *
     * @return string the sql statement
     */
    function checkExistsCalendar($label)
    {
        $stmt = 'select id_calendar ' .
                'from ' . $this->setTablePrefix('calendars') .
                " where label='" . $label . "'";
        return $stmt;
    }

    /**
     * SQL query to add an identity
     *
     * @param string $label             the calendar label
     * @param string $calendar          the calendar
     * @param int    $id_system         the system id
     * @param int    $id_shared_systems the systems id to share with
     *
     * @return string the sql statement
     */
    function setCalendar($label,
                         $calendar,
                         $id_system,
                         $id_shared_systems)
    {
        $stmt = "insert into " . $this->setTablePrefix('calendars') .
                " (label,calendar,system,share_with) " .
                " values ('" .  $label . "','" .
                                $calendar . "'," .
                                $id_system . ",'" .
                                $id_shared_systems . "')";
        return $stmt;
    }

    /**
     * SQL query to get all calendars
     * a filter for system id may be used
     *
     * @param string $filter_system the system id
     *
     * @return string the sql statement
     */
    function getCalendars($filter_system = '')
    {
        $filter_stmt = '';
        if ($filter_system != '' && $filter_system != '*') {
            $filter_stmt .= ' where system=' . $filter_system;
        }
        $stmt = 'select id_calendar,label,system,share_with from ' .
                $this->setTablePrefix('calendars') .
                $filter_stmt .
                ' order by system';
        return $stmt;
    }

    /**
     * SQL query to get a calendar by id
     *
     * @param string $id_calendar the calendar id
     *
     * @return string the sql statement
     */
    function getCalendarById($id_calendar, $fields = '*')
    {
        $stmt = 'select ' . $fields . ' from ' . $this->setTablePrefix('calendars') .
                ' where id_calendar=' . $id_calendar;
        return $stmt;
    }

    /**
     * SQL query to update calendar properties
     *
     * @param int    $id_calendar       the calendar id
     * @param string $label             the calendar label
     * @param string $calendar          the calendar
     * @param string $id_shared_systems the systems id to share with
     *
     * @return string the sql statement
     */
    function updateCalendar($id_calendar, $label, $calendar, $id_shared_systems)
    {
        $stmt = 'update ' . $this->setTablePrefix('calendars') .
                " set label='" . $label . "', " .
                " calendar='" . $calendar . "', " .
                " share_with='" . $id_shared_systems . "' " .
                ' where id_calendar=' . $id_calendar;
        return $stmt;
    }

    /**
     * SQL query to check if the calendar is associated with any job
     *
     * @param string $id_calendar the calendar id
     *
     * @return string the sql statement
     */
    function checkJobCalendar($id_calendar)
    {
        $stmt = 'select label  ' .
                ' from ' . $this->setTablePrefix('jobs') .
                ' where calendar=' . $id_calendar;
        return $stmt;
    }

    /**
     * SQL query delete a calendar by id
     *
     * @param int $id_calendar the identity id
     *
     * @return string the sql statement
     */
    function deleteCalendar($id_calendar)
    {
        $stmt = 'delete from ' .
                $this->setTablePrefix('calendars') .
                ' where ' .
                ' id_calendar=' . $id_calendar;
        return $stmt;
    }


    /**
     * SQL query to copy a calendar
     *
     * @param string $id_calendar the calendar id
     * @param string $new_label   the new label
     *
     * @return string the sql statement
     */
    function copyCalendar($id_calendar, $new_label)
    {
        $stmt = 'insert into ' . $this->setTablePrefix('calendars')
                . ' (label, calendar, system, share_with) '
                . " select '"
                . $new_label
                . "', calendar, system, share_with from "
                . $this->setTablePrefix('calendars')
                . '  where id_calendar ='
                . $id_calendar;
        return $stmt;
    }

    /**
     * SQL query to get calendars starting from system id
     *
     * @param string $id_system the system id
     *
     * @return string the sql statement
     */
    function getCalendarBySystem($id_system)
    {
        $stmt = 'select id_calendar,label from ' .
                $this->setTablePrefix('calendars') .
                ' where system=' . $id_system .
                        ' or share_with like \'%#' . $id_system . '#%\'';
        return $stmt;
    }

}
?>
