<?php
/* Copyright (C) 2001		Fabien Seisen			<seisen@linuxfr.org>
 * Copyright (C) 2002-2007	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2006		Andre Cianfarani		<acianfa@free.fr>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2014-2015  Raphaël Doursenaud      <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France             <frederic.france@free.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *    Class to manage Dolibarr database access for an SQL database
 */
interface Database
{
	/**
	 * Format a SQL IF
	 *
	 * @param   string $test Test string (example: 'cd.statut=0', 'field IS NULL')
	 * @param   string $resok result if test is equal
	 * @param   string $resko result if test is not equal
	 * @return	string                SQL string
	 */
	public function ifsql($test, $resok, $resko);

	/**
	 * Return SQL string to aggregate using the Standard Deviation of population
	 *
	 * @param	string	$nameoffield	Name of field
	 * @return	string					SQL string
	 */
	public function stddevpop($nameoffield);

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return data as an array
	 * @TODO deprecate this. Use fetch_object() so you can access a field with its name instead of using an index of position of field.
	 *
	 * @param   mysqli_result|resource|SQLite3Result $resultset 	Resultset of request
	 * @return  array<string|int,mixed>|null|int<0,0>      			Array
	 */
	public function fetch_row($resultset);
	// phpcs:enable

	/**
	 * Convert (by PHP) a GM Timestamp date into a string date with PHP server TZ to insert into a date field.
	 * Function to use to build INSERT, UPDATE or WHERE predica
	 *
	 * @param   int		$param 		Date TMS to convert
	 * @param	'gmt'|'tzserver'	$gm		'gmt'=Input information are GMT values, 'tzserver'=Local to server TZ
	 * @return  string            	Date in a string YYYYMMDDHHMMSS
	 */
	public function idate($param, $gm = 'tzserver');

	/**
	 * Return last error code
	 *
	 * @return  string    lasterrno
	 */
	public function lasterrno();

	/**
	 * Start transaction
	 *
	 * @param	string	$textinlog		Add a small text into log. '' by default.
	 * @return  int      				1 if transaction successfully opened or already opened, 0 if error
	 */
	public function begin($textinlog = '');

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Create a new database
	 * Do not use function xxx_create_db (xxx=mysql, ...) as they are deprecated
	 * We force to create database with charset this->forcecharset and collate this->forcecollate
	 *
	 * @param   string 		$database 		Database name to create
	 * @param   string 		$charset 		Charset used to store data
	 * @param   string 		$collation 		Charset used to sort data
	 * @param   string 		$owner 			Username of database owner
	 * @return  bool|SQLite3Result|mysqli_result|resource      Resource result of the query to create database if OK, null if KO
	 */
	public function DDLCreateDb($database, $charset = '', $collation = '', $owner = '');
	// phpcs:enable

	/**
	 * Return version of database server into an array
	 *
	 * @return	string[]        Version array
	 */
	public function getVersionArray();

	/**
	 *  Convert a SQL request in Mysql syntax to native syntax
	 *
	 * @param   string $line SQL request line to convert
	 * @param   string $type Type of SQL order ('ddl' for insert, update, select, delete or 'dml' for create, alter...)
	 * @return  string        SQL request line converted
	 */
	public function convertSQLFromMysql($line, $type = 'ddl');

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return the number of lines in the result of a request INSERT, DELETE or UPDATE
	 *
	 * @param   mysqli_result|resource|SQLite3Result $resultset 	Cursor of the desired request
	 * @return 	int            						Number of lines
	 * @see    	num_rows()
	 */
	public function affected_rows($resultset);
	// phpcs:enable

	/**
	 * Return description of last error
	 *
	 * @return  string        Error text
	 */
	public function error();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  List tables into a database
	 *
	 *  @param	string		$database	Name of database
	 *  @param	string		$table		Name of table filter ('xxx%')
	 *  @return	string[] of tables in an array
	 */
	public function DDLListTables($database, $table = '');
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  List tables into a database with table type
	 *
	 *  @param	string		$database	Name of database
	 *  @param	string		$table		Name of table filter ('xxx%')
	 *  @return	array<array{0:string,1:string}>		List of tables in an array
	 */
	public function DDLListTablesFull($database, $table = '');
	// phpcs:enable

	/**
	 * Return last request executed with query()
	 *
	 * @return	string                    Last query
	 */
	public function lastquery();

	/**
	 * Define sort criteria of request
	 *
	 * @param   string $sortfield List of sort fields
	 * @param   string $sortorder Sort order
	 * @return  string            String to provide syntax of a sort sql string
	 */
	public function order($sortfield = '', $sortorder = '');

	/**
	 * Decrypt sensitive data in database
	 *
	 * @param    string $value Value to decrypt
	 * @return   string                    Decrypted value if used
	 */
	public function decrypt($value);

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *    Return data as an array
	 *
	 * @param   mysqli_result|resource|SQLite3Result $resultset 	Resultset of request
	 * @return  array<int|string,mixed>|null|false  				Result with row
	 */
	public function fetch_array($resultset);
	// phpcs:enable

	/**
	 * Return last error label
	 *
	 * @return	string    lasterror
	 */
	public function lasterror();

	/**
	 * Escape a string to insert data
	 *
	 * @param   string $stringtoencode 		String to escape
	 * @return  string                      String escaped
	 */
	public function escape($stringtoencode);

	/**
	 *	Escape a string to insert data into a like.
	 *  Can be used this way: LIKE '%".dbhandler->escape(dbhandler->escapeforlike(...))."%'
	 *
	 *	@param	string	$stringtoencode		String to escape
	 *	@return	string						String escaped
	 */
	public function escapeforlike($stringtoencode);

	/**
	 * Sanitize a string for SQL forging
	 *
	 * @param   string $stringtosanitize 		String to escape
	 * @return  string                      String escaped
	 */
	public function sanitize($stringtosanitize);

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Get last ID after an insert INSERT
	 *
	 * @param	string 	$tab 		Table name concerned by insert. Not used under MySql but required for compatibility with Postgresql
	 * @param   string 	$fieldid 	Field name
	 * @return  int                	Id of row
	 */
	public function last_insert_id($tab, $fieldid = 'rowid');
	// phpcs:enable

	/**
	 *    Return full path of restore program
	 *
	 * @return        string        Full path of restore program
	 */
	public function getPathOfRestore();

	/**
	 *    Canceling a transaction and returning to old values
	 *
	 * @param	string $log Add more log to default log line
	 * @return  int                1 if cancellation ok or transaction not open, 0 if error
	 */
	public function rollback($log = '');

	/**
	 * Execute a SQL request and return the resultset
	 *
	 * @param   string 	$query 					SQL query string
	 * @param   int		$usesavepoint 			0=Default mode, 1=Run a savepoint before and a rollback to savepoint if error (this allow to have some request with errors inside global transactions).
	 *                            				Note that with Mysql, this parameter is not used as Myssql can already commit a transaction even if one request is in error, without using savepoints.
	 * @param   string 	$type 					Type of SQL order ('ddl' for insert, update, select, delete or 'dml' for create, alter...)
	 * @param	int		$result_mode			Result mode
	 * @return  bool|mysqli_result|resource		Resultset of answer or false
	 */
	public function query($query, $usesavepoint = 0, $type = 'auto', $result_mode = 0);

	/**
	 * Connection to server
	 *
	 * @param   string 			$host 						Database server host
	 * @param   string 			$login 						Login
	 * @param   string 			$passwd 					Password
	 * @param   string 			$name 						Name of database (not used for mysql, used for pgsql)
	 * @param   int    			$port 						Port of database server
	 * @return  false|resource|mysqli|mysqliDoli|PgSql\Connection|SQLite3    Database access handler
	 * @see     close()
	 */
	public function connect($host, $login, $passwd, $name, $port = 0);

	/**
	 *    Define limits and offset of request
	 *
	 * @param   int $limit Maximum number of lines returned (-1=conf->liste_limit, 0=no limit)
	 * @param   int $offset Numero of line from where starting fetch
	 * @return  string            String with SQL syntax to add a limit and offset
	 */
	public function plimit($limit = 0, $offset = 0);

	/**
	 * Return value of server parameters
	 *
	 * @param   string	$filter			Filter list on a particular value
	 * @return  array<string,string>	Array of key-values (key=>value)
	 */
	public function getServerParametersValues($filter = '');

	/**
	 * Return value of server status
	 *
	 * @param   string $filter			Filter list on a particular value
	 * @return  array<string,string>	Array of key-values (key=>value)
	 */
	public function getServerStatusValues($filter = '');

	/**
	 * Return collation used in database
	 *
	 * @return  string        Collation value
	 */
	public function getDefaultCollationDatabase();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return number of lines for result of a SELECT
	 *
	 * @param   mysqli_result|resource|SQLite3Result 	$resultset 	Resulset of requests
	 * @return 	int                        							Nb of lines
	 * @see    	affected_rows()
	 */
	public function num_rows($resultset);
	// phpcs:enable

	/**
	 * Return full path of dump program
	 *
	 * @return        string        Full path of dump program
	 */
	public function getPathOfDump();

	/**
	 * Return version of database client driver
	 *
	 * @return            string      Version string
	 */
	public function getDriverInfo();

	/**
	 * Return generic error code of last operation.
	 *
	 * @return    string        Error code (Examples: DB_ERROR_TABLE_ALREADY_EXISTS, DB_ERROR_RECORD_ALREADY_EXISTS...)
	 */
	public function errno();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Create a table into database
	 *
	 * @param        string $table 			Name of table
	 * @param        array<string,array{type:string,label?:string,enabled?:int<0,2>|string,position?:int,notnull?:int,visible?:int<-2,5>|string,alwayseditable?:int<0,1>,noteditable?:int<0,1>,default?:string,index?:int,foreignkey?:string,searchall?:int<0,1>,isameasure?:int<0,1>,css?:string,csslist?:string,help?:string,showoncombobox?:int<0,2>,disabled?:int<0,1>,arrayofkeyval?:array<int,string>,autofocusoncreate?:int<0,1>,comment?:string,copytoclipboard?:int<1,2>,validate?:int<0,1>}> 	$fields 		Associative table [field name][table of descriptions]
	 * @param        string $primary_key 	Name of the field that will be the primary key
	 * @param        string $type 			Type of the table
	 * @param        ?array<string,mixed>	$unique_keys 	Associative array Name of fields that will be unique key => value
	 * @param        string[] 	$fulltext_keys 	Field name table that will be indexed in fulltext
	 * @param        string[]	$keys 			Table of key fields names => value
	 * @return       int                    Return integer <0 if KO, >=0 if OK
	 */
	public function DDLCreateTable($table, $fields, $primary_key, $type, $unique_keys = null, $fulltext_keys = null, $keys = null);
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Drop a table into database
	 *
	 * @param        string $table 			Name of table
	 * @return       int                    Return integer <0 if KO, >=0 if OK
	 */
	public function DDLDropTable($table);
	// phpcs:enable

	/**
	 * Return list of available charset that can be used to store data in database
	 *
	 * @return	?array<int,array{charset:string,description:string}>	List of Charset
	 */
	public function getListOfCharacterSet();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Create a new field into table
	 *
	 * @param    string $table 				Name of table
	 * @param    string $field_name 		Name of field to add
	 * @param    array{type:string,label?:string,enabled?:int<0,2>|string,position?:int,notnull?:int,visible?:int,noteditable?:int,default?:string,extra?:string,null?:string,index?:int,foreignkey?:string,searchall?:int,isameasure?:int,css?:string,csslist?:string,help?:string,showoncombobox?:int,disabled?:int,arrayofkeyval?:array<int,string>,comment?:string} $field_desc 		Associative array of description of the field to insert [parameter name][parameter value]
	 * @param    string $field_position 	Optional ex .: "after field stuff"
	 * @return   int                        Return integer <0 if KO, >0 if OK
	 */
	public function DDLAddField($table, $field_name, $field_desc, $field_position = "");
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Drop a field from table
	 *
	 * @param    string $table 				Name of table
	 * @param    string $field_name 		Name of field to drop
	 * @return   int                        Return integer <0 if KO, >0 if OK
	 */
	public function DDLDropField($table, $field_name);
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Update format of a field into a table
	 *
	 * @param    string 	$table 			Name of table
	 * @param    string 	$field_name 	Name of field to modify
	 * @param    array{type:string,label:string,enabled:int<0,2>|string,position:int,notnull?:int,visible:int,noteditable?:int,default?:string,index?:int,foreignkey?:string,searchall?:int,isameasure?:int,css?:string,csslist?:string,help?:string,showoncombobox?:int,disabled?:int,arrayofkeyval?:array<int,string>,comment?:string} 	$field_desc 	Array with description of field format
	 * @return   int                        Return integer <0 if KO, >0 if OK
	 */
	public function DDLUpdateField($table, $field_name, $field_desc);
	// phpcs:enable

	/**
	 * Return list of available collation that can be used for database
	 *
	 * @return	?array<int,array{collation:string}>		List of Collation
	 */
	public function getListOfCollation();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return a pointer of line with description of a table or field
	 *
	 * @param    string 	$table 			Name of table
	 * @param    string 	$field 			Optional : Name of field if we want description of field
	 * @return   bool|resource|mysqli_result|SQLite3Result            Resource
	 */
	public function DDLDescTable($table, $field = "");
	// phpcs:enable

	/**
	 * Return version of database server
	 *
	 * @return            string      		Version string
	 */
	public function getVersion();

	/**
	 * Return charset used to store data in database
	 *
	 * @return        string        		Charset
	 */
	public function getDefaultCharacterSetDatabase();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Create a user and privileges to connect to database (even if database does not exists yet)
	 *
	 * @param    string $dolibarr_main_db_host 	Server IP
	 * @param    string $dolibarr_main_db_user 	Username to create
	 * @param    string $dolibarr_main_db_pass 	User password to create
	 * @param    string $dolibarr_main_db_name 	Database name where user must be granted
	 * @return   int                            Return integer <0 if KO, >=0 if OK
	 */
	public function DDLCreateUser(
		$dolibarr_main_db_host,
		$dolibarr_main_db_user,
		$dolibarr_main_db_pass,
		$dolibarr_main_db_name
	);
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * List information of columns into a table.
	 *
	 * @param   string 			$table 			Name of table
	 * @return  array<array<string,mixed>>		Array with information on table
	 */
	public function DDLInfoTable($table);
	// phpcs:enable

	/**
	 * Convert (by PHP) a PHP server TZ string date into a Timestamps date (GMT if gm=true)
	 * 19700101020000 -> 3600 with TZ+1 and gmt=0
	 * 19700101020000 -> 7200 whatever is TZ if gmt=1
	 *
	 * @param	string			$string		Date in a string (YYYYMMDDHHMMSS, YYYYMMDD, YYYY-MM-DD HH:MM:SS)
	 * @param	bool			$gm			1=Input information are GMT values, otherwise local to server TZ
	 * @return	int|''						Date TMS or ''
	 */
	public function jdate($string, $gm = false);

	/**
	 * Encrypt sensitive data in database
	 * Warning: This function includes the escape and add the SQL simple quotes on strings.
	 *
	 * @param	string	$fieldorvalue	Field name or value to encrypt
	 * @param	int		$withQuotes		Return string including the SQL simple quotes. This param must always be 1 (Value 0 is bugged and deprecated).
	 * @return	string					XXX(field) or XXX('value') or field or 'value'
	 */
	public function encrypt($fieldorvalue, $withQuotes = 1);

	/**
	 * Validate a database transaction
	 *
	 * @param   string 			$log 			Add more log to default log line
	 * @return	int                				1 if validation is OK or transaction level no started, 0 if ERROR
	 */
	public function commit($log = '');

	/**
	 * Free last resultset used.
	 *
	 * @param  	resource|mysqli_result|SQLite3Result	$resultset 		Free cursor
	 * @return  void
	 */
	public function free($resultset = null);

	/**
	 * Close database connection
	 *
	 * @return  boolean     					True if disconnect successful, false otherwise
	 * @see     connect()
	 */
	public function close();

	/**
	 * Return last query in error
	 *
	 * @return  string    lastqueryerror
	 */
	public function lastqueryerror();

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return connection ID
	 *
	 * @return  string      Id connection
	 */
	public function DDLGetConnectId();
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Returns the current line (as an object) for the resultset cursor
	 *
	 * @param   mysqli_result|resource|PgSql\Connection|SQLite3Result		$resultset 		Handler of the desired request
	 * @return  Object|false                    											Object result line or false if KO or end of cursor
	 */
	public function fetch_object($resultset);
	// phpcs:enable

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Select a database
	 *
	 * @param	string $database Name of database
	 * @return  boolean            true if OK, false if KO
	 */
	public function select_db($database);
	// phpcs:enable
}
