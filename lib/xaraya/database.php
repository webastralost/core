<?php
/**
 * Database Abstraction Layer API Helpers
 *
 * @package database
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage database
 * @author Marco Canini
**/

// Import our db abstraction layer
// Theoretically any adodb like layer could come in here.
sys::import('xaraya.creole');

/**
 * Initializes the database connection.
 *
 * This function loads up the db abstraction layer  and starts the database
 * connection using the required parameters then it sets
 * the table prefixes and xartables up and returns true
 *
 * @access protected
 * @param string args[databaseType] database type to use
 * @param string args[databaseHost] database hostname
 * @param string args[databaseName] database name
 * @param string args[userName] database username
 * @param string args[password] database password
 * @param bool args[persistent] flag to say we want persistent connections (optional)
 * @param string args[systemTablePrefix] system table prefix
 * @param string args[siteTablePrefix] site table prefix
 * @param bool   args[doConnect] on inialisation, also connect, defaults to true if not specified
 * @return bool true
 * @todo <marco> move template tag table definition somewhere else?
**/
function xarDB_init(array &$args)
{
    xarDB::setPrefix($args['prefix']);

    // Register postgres driver, since Creole uses a slightly different alias
    // We do this here so we can remove customisation from creole lib.
    xarDB::registerDriver('postgres','creole.drivers.pgsql.PgSQLConnection');

    if(!isset($args['doConnect']) or $args['doConnect']) xarDBNewConn($args);
    return true;
}

/**
 * Initialise a new db connection
 *
 * Create a new connection based on the supplied parameters
 *
 * @access public
 */
function &xarDBNewConn(array $args = null)
{
    // Get database parameters
    $dsn = array('phptype'   => $args['databaseType'],
                 'hostspec'  => $args['databaseHost'],
                 'username'  => $args['userName'],
                 'password'  => $args['password'],
                 'database'  => $args['databaseName']);
    // Set flags
    $flags = 0;
    $persistent = !empty($args['persistent']) ? true : false;
    if($persistent) $flags |= xarDB::PERSISTENT;
    $conn = null;
    $conn = xarDB::getConnection($dsn,$flags);

    // if code uses assoc fetching and makes a mess of column names, correct
    // this by forcing returns to be lowercase
    // <mrb> : this is not for nothing a COMPAT flag. the problem still lies
    //         in creating the database schema case sensitive in the first
    //         place. Unfortunately, that is just not portable.
    $flags |= xarDB::COMPAT_ASSOC_LOWER;

    $conn = xarDB::getConnection($dsn,$flags); // cached on dsn hash, so no worries
    xarLogMessage("New connection created, now serving " . count(xarDB::$count) . " connections");
    return $conn;
}

/**
 * Get an array of database tables
 *
 * @deprec
 * @see xarDB::getTables()
 */
function &xarDBGetTables() { $t = xarDB::getTables(); return $t;}


/**
 * Get a database connection
 *
 * @deprec
 * @see xarDB::getConn()
 */
function &xarDBGetConn($index = 0) {$t = xarDB::getConn($index); return $t;}
?>