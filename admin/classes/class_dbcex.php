<?php 
/**
 * Datenbankklasse (mysqli)
 * 
 * Klasse, die Datenbankmethoden zur Verfügung stellt
 * 
 * Die Datenbankklasse regelt die Zugriffe auf die MySQL-Datenbank 
 * bzw. stellt bei Datenbankoperationen automatisch die Verbindung zur DB her.
 * 
 * @author J.Hahn <info@content-o-mat.de>, J.Braun <info@content-o-mat.de>
 * @version 2017-03-30
 */

namespace Contentomat;

class DBCex {

	private $databases;
	private $currentDatabase;

	public $lastinsertedid;
	public $dbLastError;
	public $dbLastErrorNr;
	
	public $availableCharsets;
	public $charsetConversionTable;

	private $connection;
	private $dbResult;
	
	private $useCharset;
	
	protected $affectedRows = 0;
	protected $maxConnectionRetries = 3;


	/**
	 * Konstruktor
	 * Erzeugt das Datenbankobjekt. Optional kann ein Parameter übergeben werden,
	 * der eine Datenbank aus dem Array mit den Verbindungsdaten auswählt.
	 * 
	 * @param string $currentDatabase Name/Schlüssel im Array, welcher die auszuwählende Datenbank bestimmt.
	 * 
	 * @return void
	 */
	public function __construct($currentDatabase='default') {

		require (INCLUDEPATHTOADMIN."settings_db.inc");
		
		// Variablen initalisieren
		$this->availableCharsets = array();

		$this->charsetConversionTable = array(
			'utf-8' => 'utf8',
			'iso-8859-1' => 'latin1',
			'iso-8859-2' => 'latin2',
			'iso-8859-9' => 'latin5',
			'iso-8859-13' => 'latin7'
		);
		
		if (!is_array($databases)) {
			$this->databases = array(
				'default' => array(
					'server' => $server,
					'db' => $db,
					'user' => $user,
					'pw' => $pw
				)
			);
		} else {
			$this->databases = $databases;
		}
		
		$this->selectDatabase($currentDatabase);
		$this->getAvailableCharsets();
		$this->setCharset(CHARSET);		// Anfangs wird Zeichensatz in CMT_DEFAULT gesetzt.
	}

	/**
	 * public function selectDatabase()
	 * Wählt eine Datenbank aus und verbindet mit dieser
	 * 
	 * @param string $selectedDatabase Name der Datenbank mit der verbunden werden soll
	 * 
	 * @return bool Gibt den Wert der Methode connect() zurück)
	 */
	public function selectDatabase($selectedDatabase='default') {
		
		if (!in_array($selectedDatabase, array_keys($this->databases))) {
			return false;
		}
				
		// Ausgewählte DB merken
		$this->currentDatabase = $selectedDatabase;
		
		// Verbinden
		return $this->connect();	
	}

	/**
	 * public function addDatabase()
	 * Fügt eine Datenbank (bzw. die Zugangsdaten) zur internen Liste der Datenbanken.
	 *
	 * @param array $databases Multidimensionales Array.
	 *
	 * @return boolean
	 */
	public function addDatabase($databases) {

		if (!is_array($databases) || empty($databases)) {
			return false;
		}
		
		foreach ($databases as $databaseName => $databaseData) {
			$this->databases[$databaseName]['server'] = $databaseData['server'];
			$this->databases[$databaseName]['db'] = $databaseData['db'];
			$this->databases[$databaseName]['user'] = $databaseData['user'];
			$this->databases[$databaseName]['pw'] = $databaseData['pw'];
		}
		
		return true;
	}
	
	/**
	 * public function resetDatabase()
	 * Setzt die Datenbank auf die Standardverbindung zurück und verbindet mit dieser DB.
	 *
	 * @param void
	 * @return boolean Gibt das Ergebnis des Verbindungsversuches zurück.
	 */
	public function resetDatabase() {
		return $this->selectDatabase();
	}

	/**
	 * Returns the current database and all login informations. 
	 * @return array
	 */
	public function getCurrentDatabase() {
		return (array)$this->databases[$this->currentDatabase];
	}
	
	/**
	 * public function connect() 
	 * Verbindet mit ausgewählter Datenbank
	 * 
	 * @param void
	 * 
	 * @return bool Gibt den Wert je nach Erfolg true oder false zurück
	 */	
	public function connect() {
		
		$tries = 0;
		
		do {
			$this->connection = mysqli_connect(
				$this->databases[$this->currentDatabase]['server'], 
				$this->databases[$this->currentDatabase]['user'],
				$this->databases[$this->currentDatabase]['pw'],
				$this->databases[$this->currentDatabase]['db']
			);
		} while(mysqli_connect_error() && $tries++ < $this->maxConnectionRetries);
		
		if (mysqli_connect_error()) {
			$this->connection_error("Konnte keine Verbindung zum Server herstellen");
			unset($this->connection);
			return false;
		}
		
		// Fix for MySQL 5.7.x [](https://www.digitalocean.com/community/tutorials/how-to-prepare-for-your-mysql-5-7-upgrade)
		// jbraun 2016-09-26
		if (mysqli_query($this->connection, "set sql_mode = ''") === FALSE) {
			return false;
		}
				
		return true;
	}
	
	public function setMaxConnectionRetries($retries) {
		
		$retries = (int)$retries;
		
		if ($retries > 10) {
			return false;
		}
		
		$this->maxConnectionRetries = $retries;

		// This ist senseless here?
        // Fix for MySQL 5.7.x [](https://www.digitalocean.com/community/tutorials/how-to-prepare-for-your-mysql-5-7-upgrade)
        // jbraun 2016-09-26
// 		if (mysqli_query($this->connection, "set sql_mode = ''") === FALSE) {
// 			return false;
// 		}

		return true;
	}

	/**
	 * public function close() 
	 * Schließt eine bestehende Verbindung zur Datenbank
	 * 
	 * @param void
	 * 
	 * @return void
	 */	
	public function close() {
		if ($this->connection) {
			mysqli_close($this->connection);
		}
		unset ($this->connection);
	}


	/**
	 * public function query() 
	 * Führt die ausgewählte Query aus. Falls Fehler gespeichert werden sollen 
	 * und einer auftritt, werden alle Fehlerdaten in 'cmt_dberrorlog' gespeichert.
	 * 
	 * @param string $query	Datenbank-Query
	 * 
	 * @return number Gibt den Wert der Funktion "mysql_errno()" zurück, also im Idealfall 0
	 */	
	public function query($query) {

		$query = trim($query);
		
		if (!$query) {
			// empty query error nr. see: https://dev.mysql.com/doc/refman/5.1/en/error-messages-server.html
			return 1065;
		}
		
		if (!$this->connection) {
			$this->Connect();
		}

		$errorNr = 0;
		$this->dbLastError = '';
		$this->dbLastErrorNr = 0;
		
		//$this->connect();

		// Zeichensatz ausgewählt?
		if ($this->useCharset) {
			mysqli_query($this->connection, "SET NAMES '".$this->useCharset."'");
		}

		$dbResult = mysqli_query($this->connection, $query);
		
		if (!mysqli_error($this->connection)) {
			if (!$dbResult || empty ($dbResult)) {
				mysqli_free_result($dbResult);
			} else {
				$this->dbResult = $dbResult;
			}
			$this->dbLastError = '';
			$this->dbLastErrorNr = 0;
		} else {
			$this->dbLastError = mysqli_error($this->connection);
			$this->dbLastErrorNr = mysqli_errno($this->connection);
			
			//Fehler aufgetreten und Error-Logging an?
			if (CMT_DBERRORLOG == true && $this->dbLastErrorNr) {
				$this->saveError($query);
			}
		}
		$this->lastinsertedid = mysqli_insert_id($this->connection);
		$this->affectedRows = mysqli_affected_rows($this->connection);
		
		// check if permanently closing and opening connections slows down the scripts.
		// But some webspaces allow just a limited number of simultaneously open connections (e.g. 1und1)
		$this->close();
		 
		return $this->dbLastErrorNr;
	}

	/**
	 * public function saveError() 
	 * Speichert im Fehlerfall Query und weitere Daten in der Systemtabelle 'cmt_dberrorlog'
	 * 
	 * @param string $query	Datenbank-Query
	 * 
	 * @return void
	 */	
	private function saveError($query) {
		// Fehlernummer
		$errorNr = $this->dbLastErrorNr;
		
		// Content-o-mat: Seiten-ID
		if (defined('PAGEID')) $pid = intval(PAGEID);
		
		// Content-o-mat: Sprach-ID
		if (defined('PAGELANG')) $lang = substr(PAGELANG, 0, 64);
		
		// Content-o-mat: Applikations-ID (interne Applikationen)
		if (defined('CMT_APPID')) $launch = intval(CMT_APPID);
		
		// Content-o-mat: User-ID
		if (defined('CMT_USERID')) $uid = intval(CMT_USERID);
		
		mysqli_query($this->connection,
			"INSERT INTO cmt_dberrorlog SET " .
			 "error_datetime = '".date('Y-m-d H:i:s')."', " .
			 "mysql_error_number = '".$this->dbLastErrorNr."', " .
			 "mysql_error_message = '".addslashes($this->dbLastError)."', " .
			 "mysql_query = '".$this->mysqlQuote($query)."', " .
			 "script_name = '".$this->mysqlQuote($_SERVER['SCRIPT_NAME'])."', " .
			 "script_querystring = '".$this->mysqlQuote($_SERVER['QUERY_STRING'])."', " .
			 "cmt_pageid = '".$pid."', " .
			 "cmt_pagelang = '".$this->mysqlQuote($lang)."', " .
			 "cmt_userid = '".$uid."', " .
			 "cmt_applicationid = '".$launch."'"
		);
	}


	/**
	 * public function get() 
	 * Ermittelt die Daten aus der letzten gültigen Datenbankabfrage
	 * 
	 * @param string $keyType	MYSQLI_ASSOC oder MYSQLI_NUM: Definiert, wie das Rückgabearray aussehen soll
	 * 
	 * @return mixed Gibt entweder den Datensatz als Array oder ein leeres Array zurück
	 */	
	public function get($keyType = MYSQLI_ASSOC) {	

		if ($this->dbResult) {
			$r = mysqli_fetch_array($this->dbResult, $keyType);

			if (is_array($r)) {
				return $r;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	/**
	 * public function getAll()
	 * Liefert alle Datensätze einer Datenbankabfrage auf einmal zurück.
	 *
	 * @param boolean $idAsKey Optional: Die ID des jeweiligen Eintrags ist sein Schlüssel im Multidimensionalen Array. Standard ist true. 
	 * @param string $keyType Optional: MYSQLI_ASSOC, MYSQLI_NUM oder MYSQLI_NUM: Je nach Angabe werden die Datenreihen 
	 * mit assoziativen Schlüssel (Feldname = Schlüsselname), numerischen Schlüssel (fortlaufende Nummer = Schlüsselname) 
	 * oder mit beiden Angaben geliefert. Standard ist MYSQLI_ASSOC.  
	 *
	 * @return array Array mit allen Datensätzen oder leer.
	 */
	public function getAll($idAsKey = true, $keyType = MYSQLI_ASSOC) {	
		
		$data = array();
		
		if ($this->dbResult) {
			
			if ($idAsKey) {
				while ($r = mysqli_fetch_array($this->dbResult, $keyType)) {
					if ($r['id']) {
						$data[$r['id']] = $r;
					} else {
						$data[] = $r;
					}
				}
			} else {
				while ($r = mysqli_fetch_array($this->dbResult, $keyType)) {
					$data[] = $r;
				}
			}
		}
		
		return $data;
		
	}
	
	/**
	 * public function getCurrentFieldInfo() 
	 * OUTDATED? Gibt Detailinformationen zu den Feldern der aktuellen Datenbankabrage aus
	 * 
	 * @param void
	 * 
	 * @return array Multidimensionales Array mit Informationen
	 */	
	public function getCurrentFieldInfo() {

		$i = 0;
		$cols = mysqli_num_fields($this->dbResult);
		$row = array();
		
		$translateType = array(
			MYSQLI_TYPE_DATETIME => 'dateime',
			MYSQLI_NOT_NULL_FLAG => 'not null',
			MYSQLI_PRI_KEY_FLAG  => 'primary index',
			MYSQLI_UNIQUE_KEY_FLAG  => 'unique index',
			MYSQLI_MULTIPLE_KEY_FLAG => 'index',
			MYSQLI_BLOB_FLAG => 'blob',
			MYSQLI_UNSIGNED_FLAG => 'unsigned',
			MYSQLI_ZEROFILL_FLAG => 'zerofill',
			MYSQLI_AUTO_INCREMENT_FLAG => 'auto increment',
			MYSQLI_TIMESTAMP_FLAG => 'timestamp',
			MYSQLI_SET_FLAG => 'set',
			MYSQLI_NUM_FLAG => 'numeric',
			MYSQLI_PART_KEY_FLAG => 'multi index',
			MYSQLI_GROUP_FLAG => 'group by',
			MYSQLI_TYPE_DECIMAL => 'decimal',
			MYSQLI_TYPE_TINY => 'tinyint',
			MYSQLI_TYPE_SHORT => 'int',
			MYSQLI_TYPE_LONG => 'int',
			MYSQLI_TYPE_FLOAT => 'float',
			MYSQLI_TYPE_DOUBLE => 'double',
			MYSQLI_TYPE_NULL => 'default null',
			MYSQLI_TYPE_TIMESTAMP => 'timestamp',
			MYSQLI_TYPE_LONGLONG => 'bigint',
			MYSQLI_TYPE_INT24 => 'mediumint',
			MYSQLI_TYPE_DATE => 'date',
			MYSQLI_TYPE_TIME => 'time',
			MYSQLI_TYPE_DATETIME => 'datetime',
			MYSQLI_TYPE_YEAR => 'year',
			MYSQLI_TYPE_NEWDATE => 'date',
			MYSQLI_TYPE_ENUM => 'enum',
			MYSQLI_TYPE_SET => 'set',
			MYSQLI_TYPE_TINY_BLOB => 'tinyblob',
			MYSQLI_TYPE_MEDIUM_BLOB => 'mediumblob',
			MYSQLI_TYPE_LONG_BLOB => 'longblob',
			MYSQLI_TYPE_BLOB => 'blob',
			MYSQLI_TYPE_VAR_STRING => 'varchar',
			MYSQLI_TYPE_STRING => 'char',
			MYSQLI_TYPE_GEOMETRY => 'geometry'			
		);

		while ($i < $cols) {
			$fieldData = mysqli_fetch_field_direct($this->dbResult, $i);
			$name = $fieldData->name;
			$row['type'][$name] = $translateType[$fieldData->type];
			$row['length'][$name] = $fieldData->length;
			$row['flags'][$name] = $fieldData->flags;
			$row['name'][$name] = $name;

			$i ++;
		}
		$row['rows'] = mysqli_num_rows($this->dbResult);
		return $row;
	}

	/**
	 * public function getCurrentFieldNames() 
	 * Gets name, type, length and the flags of the fields in a table.
	 * 
	 * @param string $fieldIndex Optional field index => only the informations of this field are returned.
	 * 
	 * @return array Multidimensional array with all informations. In case of an error: empty array
	 * 
	 */	
	public function getCurrentFieldNames($fieldIndex = 0) {

		if ($fieldIndex) {
			$fieldData = mysqli_fetch_field_direct($this->dbResult, $fieldIndex);
			return array(
				$fieldData->name
			);
		}
				
		$cols = mysqli_num_fields($this->dbResult);
		$fieldNames = array();
		
		while ($fieldIndex < $cols) {
			$fieldData = mysqli_fetch_field_direct($this->dbResult, $fieldIndex);
			$fieldNames[] = $fieldData->name;
			$fieldIndex++;
		}
		return $fieldNames;
		
	}


	/**
	 * public function getFieldInfo() 
	 * Gets name, type, length and the flags of the fields in a table.
	 * 
	 * @param string $tableName Name of the table
	 * 
	 * @return array Multidimensional array with all informations. In case of an error: empty array
	 */	
	public function getFieldInfo($tableName='') {

		if (!$tableName) {
			return array();
		}

		$query = "SELECT * FROM ".$tableName." LIMIT 1";
		$r = $this->query($query);
		
		if ($this->getLastErrorNr()) {
			return array();
		}
		
		return $this->getCurrentFieldInfo();
		
// 		$dbResult = $this->dbResult;

// 		$i = 0;
// 		$cols = mysqli_num_fields($this->dbResult);
// 		$row = array();

// 		while ($i < $cols) {
// 			$fieldData = mysqli_fetch_field_direct($this->dbResult, $i);
// 			$name = $fieldData->name;
// 			$row['type'][$name] = $fieldData->type;
// 			$row['length'][$name] = $fieldData->length;
// 			$row['flags'][$name] = $fieldData->flags;

// 			$i ++;
// 		}
// 		$row['rows'] = mysqli_num_rows($this->dbResult);
// 		return $row;
	}

	/**
	 * public function getMysqlFieldInfo() 
	 * Gets the MySQL field informations of one or all fields in a table.
	 * 
	 * @param string $tableName MySQL name of the target table
	 * @param string $fieldName Optional MySQL name of a field in the target table 
	 * 
	 * @return array Multidimensional array with all requested field informations.
	 */	
	public function getMysqlFieldInfo($tableName="", $fieldName="") {
		if (!$tableName) {
			return array();
		}

		$query = "SHOW COLUMNS FROM ".$tableName;
		if ($fieldName) {
			$query .= " LIKE '".$fieldName."'";
		}

		$this->query($query);
		if ($this->getLastErrorNr()) {
			return array();
		}

		while ($r = $this->get()) {
			$rows[$r[Field]] = $r;
		}
		return $rows;

	}


	/**
	 * public function affectedRows() 
	 * Returns the number of rows affected by the last query.
	 * 
	 * @param void 
	 * 
	 * @return number Number of rows affected by the last query.
	 */	
	public function affectedRows() {
		return intval($this->affectedRows);
	}


	/**
	 * public function countSelectedRows() 
	 * Gibt Anzahl der von einer SELECT-Abfrage betroffenen Zeilen zurück.
	 * 
	 * @param string $query	Optional kann eine SQL-Query übergeben werden, die ausgeführt wird. Ansonsten wird das Resultat der letzten Abfrage verwendet. 
	 * 
	 * @return number Anzahl betroffener Zeilen
	 */	
	public function countSelectedRows($query="") {
		
		if ($query) {
			$this->query($query);	
		}
		return mysqli_num_rows($this->dbResult);
	}

	
	/**
	 * public function lastInsertedId()
	 * OUTDATED: Returns the ID of the last inserted row
	 * 
	 * @param void 
	 * 
	 * @return number ID s
	 */	
	 public	function lastInsertedId() {
		return $this->getLastInsertedId();
	}

	/**
	 * public function lastInsertedID()
	 * Returns the ID of the last inserted row by the last executed query.
	 *
	 * @param void
	 *
	 * @return number ID
	 */
	 public	function getLastInsertedID() {
		return $this->lastinsertedid;
	}


	/**
	 * public function lastInsertedId() 
	 * Returns all tables name in the current database.
	 * 
	 * @param void 
	 * 
	 * @return mixed Array mit Tabellennamen oder false
	 */	
	public function getAllTables() {

		$tables = array();
		
		// Achtung: Datenbankname in "Backticks" (`...`) falls er ein Minus oder anderes Sonderzeichen enthält
		$this->query("SHOW TABLES FROM `" . trim($this->databases[$this->currentDatabase]['db']) . "`");
		//$this->query("SHOW TABLES FROM " . trim($this->databases[$this->currentDatabase]['db']) . "");
		while (($r = $this->get(MYSQLI_ASSOC) ) && !empty($r)) {
			$tables[] = array_shift($r);
		}
		
		return $tables;
	}


	/**
	 * public function getEngines() 
	 * Returns a list of all database storage engines.
	 * 
	 * @param void 
	 * 
	 * @return array List of all storage engines.
	 */	
	public function getEngines() {

		$engines = array();
		$this->query('SHOW ENGINES');
		
		while ($r = $this->get()) {
			$engines[$r['Engine']] = $r; 
		}
		
		return $engines;
	}

	/**
	 * public function getAvailableEngines() 
	 * Returns a list of names of all available storage engines. 
	 * 
	 * @param void 
	 * 
	 * @return array List of available storage engines.
	 */	
	public function getAvailableEngines() {
		$engines = $this->getEngines();
		
		if (is_array($engines)) {
			$availableEngines = array();
			foreach ($engines as $engineName => $engineData) {
				
				$engineSupport = strtolower($engineData['Support']);

				if ($engineSupport == 'yes' || $engineSupport == 'default') {
					$availableEngines[] = $engineName;
				}
			}
			
			return $availableEngines;
		}
		return array();
		
	}


	/**
	 * public function getDefaultEngines() 
	 * Gets the name of the default storage engine
	 * 
	 * @param void 
	 * 
	 * @return string Liefert den Namen der Standard-Engine der Datenbank
	 */	
	public function getDefaultEngine() {
		$engines = $this->getEngines();
		
		if (is_array($engines)) {

			foreach ($engines as $engineName => $engineData) {
				
				$engineSupport = strtolower($engineData['Support']);

				if ($engineSupport == 'default') {
					return $engineName;
				}
			}
		}
		
		return '';
	}


	/**
	 * public function setEngine() 
	 * Changes the storage engine for a table (e.g. InnoDB, MyISAM ...)
	 * 
	 * @param string $tableName	MySQL name of the target table
	 * @param string $engineName MySQL name of the engine
	 * 
	 * @return bool true or false
	 */	
	public function setEngine($tableName, $engineName) {
		$engines = $this->getAvailableEngines();
		
		if (!in_array($engineName, $engines)) return false;
		
		$this->query('ALTER TABLE '.$tableName.' ENGINE = '.$engineName);
		if ($this->getLastErrorNr) {
			return false;
		} else { 
			return true;
		}
	}

	/**
	 * public function getEngine()
	 * Returns the name of a table's storage engine.
	 *
	 * @param string $tableName	Table's MySQL name
	 *
	 * @return string Engine name
	 */
	public function getEngine($tableName) {
		
		$this->query("SHOW TABLE STATUS LIKE  '".$tableName."'");

		if ($this->getLastErrorNr) {
			return '';
		} else { 
			$r = $this->get();
			return $r['Engine'];
		}
	}
	
	// OUTDATED: getTableEngine()
	public function getTableEngine($tableName) {
		return $this->getEngine($tableName);
	}
	/**
	 * public function getLastErrorNr() 
	 * Returns the number of the last database error.
	 * 
	 * @param void 
	 * 
	 * @return number MySQL error number
	 */	
	public function getLastErrorNr() {
		return $this->dbLastErrorNr;
	}


	/**
	 * public function getLastError() 
	 * Returns the last MySQL error message.
	 * 
	 * @param void 
	 * 
	 * @return string MySQL error message
	 */		
	public function getLastError() {
		return $this->dbLastError;
	}
	
	
	/**
	 * public function makeSetQuery()
	 * 
	 * Erzeugt aus einem Array der Form $a[$key] = $value einen String, der an den SET-Teil einer DB-Query gehängt werden kann
	 * 
	 * @param $vars array Associative array: key names are the fields, values are used for the value statement (e.g. SET key = 'value')
	 * @param $separator string Separator. default is ","
	 * @param $quote string Quoting char. Default is "'"
	 * 
	 * @return string / boolean Returns a string for use in a query (e.g. field1 = 'value1', field2 = 'value2')
	 */
	public function makeSetQuery ($vars, $separator=',', $quote="'") {
		
		if (!is_array($vars)) {
			return '';
		}
		
		foreach ($vars as $k=>$v) {
			$queryTemp[] = $k . '='. $quote . $this->dbQuote($v) . $quote;
		}
		return implode($separator, $queryTemp);
	}

	/**
	 * public function dbQuote()
	 * Method prepares a string to use in SQL statements (quotes some chars)
	 * 
	 * @param $value string String to quote 
	 * @return string Quoted/ prepared string
	 */
	public function dbQuote($value) {
		
   		// stripslashes, if neccessary
   		if (get_magic_quotes_gpc()) {
       		$value = stripslashes($value);
   		}

   		// quote if a string
   		if (!is_int($value)) {
   		   	
   			if (!$this->connection || !mysqli_ping($this->connection)) {
   				$this->connect();
   			}
       		$value = mysqli_real_escape_string($this->connection, $value);
   		}
   		return $value;
	}

	/**
	 * public function mysqlQuote()
	 *
	 * OUTDATED: Alias for dbQuote
	 */
	public function mysqlQuote($value) {
		return $this->dbQuote($value);
	}

	/**
	 * function checkConnection()
	 * 
	 * Prüft, ob die MySQL-Verbindung noch steht, oder ob neu verbunden werden muss.
	 * 
	 * @param void
	 * @return boolean true, wenn die Verbindung noch besteht, false, wenn neu verbunden werden musste
	 */
	public function checkConnection() {
		if (!mysqli_ping($this->connection)) {
			//$this->connect();
			return false;
		} else {
			return true;
		}
	}

// TODO / OUTDATED???
	/**
	 * public function setCharset()
	 * Setter: Setzt den Zeichensatz für eine DB-Verbindung
	 * 
	 * @param string $charsetName Bezeichnung des Zeichenstzes, z.B. 'utf8', 'latin', etc.
	 * @return bool True oder false, je nach erfolgter Umstellung
	 */
	public function setCharset($charsetName="") {
		
		$charsetName = $this->convertCharsetName($charsetName);
		
		if (in_array($charsetName, $this->availableCharsets)) {
			
			// Zeichensatz wird von DB unterstützt
			$this->useCharset = $charsetName;
			return true;
			
		} else {
			
			// Zeichensatz existiert nicht, dann Content-o-mat Default-Zeichensatz probieren
			$charsetName = $this->convertCharsetName(CMT_DEFAULTCHARSET);
			
			if (in_array($charsetName, $this->availableCharsets)) {
				$this->useCharset = $charsetName;
			} else {
				
				// Auch Default-Zeichensatz existiert nicht
				$this->useCharset = false;
				return false;
			}
		}
	}

// TODO / OUTDATED???
	/**
	 * public function convertCharsetName()
	 * Konvertiert den Zeichensatznamen in einen MySQL-Zeichensatznamen (sofern möglich).
	 * 
	 * @param string $charsetName Bezeichnung des Zeichenstzes, z.B. 'utf8', 'utf-8', 'latin1', 'iso-8859-1' etc. Sowohl HTML, als auch MySQL-Zeichensätze möglich
	 * @return string Gibt entweder den konvertierten zeichensatzname zurück oder den Originalnamen, wenn im Konvertierugnsarray der Zeichensatz nicht gefunden wurde.
	 */
	public function convertCharsetName($charsetName) {

		// 1. Muss Zeichensatz in MySQL-Bezeichnung konvertiert werden?
		$csn = $this->charsetConversionTable[strtolower($charsetName)];
//echo "Konvertiert: ".$csn;
		if ($csn) {
			return $csn;
		} else {
			return $charsetName;
		}
	}

	/**
	 * public function getAvailableCharsets()
	 * Liest alle verfügbaren Zeichensätze der Datenbank aus.
	 * 
	 * @param void Verlangt keine Parameter
	 * @return array Gibt ein Array zurück, das entweder leer ist (Fehler) oder die Zeichensätze als Werte enthält.
	 */	
	public function getAvailableCharsets($fullData=false) {
		
		if (empty($this->availableCharsets) || $fullData) {
			$this->connect();
			$charSetData = array();
			$dbResult = $this->query("SHOW CHARACTER SET");
			
			while ($r = $this->get()) {
				$this->availableCharsets[] = $r['Charset'];
				$charSetData[$r['Charset']] = $r;
			};
		}

		if ($fullData) {
			return $charSetData;
		} else {		
			return $this->availableCharsets;
		}
	}

	/**
	 * public function getCollations()
	 * Get all available collations optionally filtered for a charset.
	 *
	 * @param string $charset Optional: Valid charset name
	 *
	 * @return array List of collation names or empty array
	 */
	public function getCollations($charset=null) {
		
		$query = "SHOW COLLATION";
		if ($charset) {
			$query .= " WHERE Charset LIKE '". $this->dbQuote(trim($charset)) . "'";
		}
		
		$this->query($query);

		$collations = array();
		while ($r = $this->get()) {
			$collations[$r['Collation']] = $r;
		}
	
		return $collations;

	}

	/**
	 * public function getDefaultCollation()
	 * Returns the default collation for a given charset
	 *
	 * @param string $charset Valid charset name
	 *
	 * @return string Collation name
	 */
	public function getDefaultCollation($charset) {
	
		if (!$charset) {
			return array();
		}

		$query = "SHOW CHARACTER SET WHERE CHARSET = '". $this->dbQuote(trim($charset)) . "'";
		$this->query($query);
		$r = $this->get();

		return (string)$r['Default collation'];
	}
	
	/**
	 * public function isValidCollation()
	 * Check if a collation is valid for a charset.
	 *
	 * @param string $collation Valid collation name
	 * @param string $charset Valid charset name
	 *
	 * @return boolean
	 */
	public function isValidCollation($collation, $charset) {
		
		$collations = $this->getCollations($charset);
		
		if (in_array($collation, $collations)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * public function getTableStatus()
	 * Gets the tables informations provided by MySQLs "SHOW TABLE STATUS". More informations:
	 * http://dev.mysql.com/doc/refman/5.1/en/show-table-status.html
	 *
	 * @param string $tableName Optional: name of a tabke. If missing, all tables status is read. 
	 * @return array Associative array: table name => array(status data)
	 *
	 */
	public function getTableStatus($tableName='') {
		
		$query = "SHOW TABLE STATUS";
		if ($tableName) {
			$query .= " LIKE '". $this->dbQuote(trim($tableName)) . "'";
		}
		
		$this->query($query);
		
		$status = array();
		while ($r = $this->get()) {
			$status[$r['Name']] = $r;
		}
		
		if ($tableName) {
			return $status[$tableName];
		} else {
			return $status;
		}
		
	}
	
	public function getTableCharset($tableName) {
		
		if (!$tableName) {
			return '';
		}
		
		$tableStatus = $this->getTableStatus($tableName);
		
		$this->query("SHOW COLLATION WHERE Collation = '" . $tableStatus['Collation'] . "'");
		$collation = $this->get();

		return $collation['Charset'];
	}
	
	public function getTableCollation($tableName) {
	
		if (!$tableName) {
			return '';
		}
	
		$tableStatus = $this->getTableStatus($tableName);
	
		return $tableStatus['Collation'];
	}	

	/**
	 * public function renameTable()
	 * Renames a database table.
	 *
	 * @param string $oldTableName Name of the table to rename.
	 * @param string $newTableName New table name
	 *
	 * @return boolean
	 */
	public function renameTable($oldTableName = '', $newTableName = '') {
	
		if (!$oldTableName || !$newTableName) {
			return false;
		}
		$check = $this->query("RENAME TABLE " . $this->dbQuote($oldTableName) . " TO " . $this->dbQuote($newTableName));
	
		return !(boolean)$check;
	}
	
	/**
	 * public function changeCollation()
	 * Changes the collation (way the table sorts its entries). 
	 *
	 * @param string $tableName Name of the target database table.
	 * @param string $collation Valid collation name.
	 *
	 * @return boolean
	 */
	public function changeCollation($tableName='', $collation='') {
		
		if (!$tableName || !$collation) {
			return false;
		}
		
		$check = $this->query("ALTER TABLE " . $this->dbQuote($tableName) . " COLLATE " . $this->dbQuote($collation));
		
		return !(boolean)$check;
	}
	
	/**
	 * public function changeCharset()
	 * Changes a tables character set and optionally its collation.
	 *
	 * @param string $tableName Name of the database table
	 * @param string $charset Valid name of the new character set
	 * @param string $collation optional: Valid name of the collation (must fit to the chosen character set in $charset!)
	 *
	 * @return boolean
	 */
	public function changeCharset($tableName, $charset, $collation=null) {
		
		if (!$tableName || !$charset) {
			return false;
		}
		
		$collation = trim($collation);
		$addQUery = '';
		
		if ($collation) {
			$addQuery = " COLLATE " . $this->dbQuote($collation);
		}
		
		$check = $this->query("ALTER TABLE " . $this->dbQuote($tableName) . " CONVERT TO CHARACTER SET " . $this->dbQuote($charset) . $addQuery);
		return !(boolean)$check;
	}

	/**
	 * public function getLastUpdateDate()
	 * Gets the datetime of the last update of a table.
	 *
	 * @param string $tableName Database table name
	 *
	 * @return string Datetime in MySQL format or '0000-00-00 00:00:00' in case of an error.
	 */
	public function getLastUpdateTime($tableName) {
		
		$updateTime = $this->getTableInformations($tableName, 'UPDATE_TIME');
		
		if ($updateTime['UPDATE_TIME']) {
			return $updateTime['UPDATE_TIME'];
		} else {
			return '000-00-00 00:00:00';
		}
	}

	/**
	 * public function getCreationTime()
	 * Gets the datetime of table creation
	 *
	 * @param string $tableName
	 *
	 * @return Datetime in MySQL format or '0000-00-00 00:00:00' in case of an error.
	 */
	public function getCreationTime($tableName) {
	
		$createTime = $this->getTableInformations($tableName, 'CREATE_TIME');
	
		if ($createTime['CREATE_TIME']) {
			return $createTime['CREATE_TIME'];
		} else {
			return '000-00-00 00:00:00';
		}
	}

	/**
	 * public function getTableInformations()
	 * Gets all table informations from MySQL's information schema db.
	 *
	 * @param string $tableName Database table name
	 * @param string $fieldName Optional field name. If no field name is given the method returns an array with all fields/ variables
	 *  
	 * @return array All variables in an associative array.
	 */
	public function getTableInformations($tableName, $fieldName='') {
	
		$tableName = trim($tableName);
		$fieldName = trim($fieldName);
		if (!$tableName) {
			return array();
		}
	
		if (!$fieldName) {
			$fieldName = '*';
		}
		$this->query("SELECT " . $this->dbQuote($fieldName) . " FROM information_schema.tables WHERE TABLE_SCHEMA = '" . $this->databases[$this->currentDatabase]['db'] . "' AND TABLE_NAME = '" . $this->dbQuote($tableName) . "'");
		return $this->get();
	}
	
	
	public function updatePositions($params = array())  {
		
		$defaultParams = array(
			'startAt' => 1,
			'table' => '',
			'field' => '',
			'where' => ''
		);
		$params = array_merge($defaultParams, $params);
		
		$table = $this->dbQuote($params['table']);
		$field = $this->dbQuote($params['field']);
		$where = $this->dbQuote($params['where']);
		$startAt = intval($params['startAt']);
		
		$query = "UPDATE " . $table . " SET " . $field . " = (@newPos :=  @newPos + 1) "; 

		if ($where) {
			$query .= " WHERE " . $params['where'] ." ";
		}
		
		$query .= "ORDER BY " . $field . " ASC";

		$startAt = 1;
		$this->query("SET @newPos = " . $startAt);
		$check = $this->query($query);
		
		return (boolean) !$check;

	}
	/* 
	 * Veraltete Methoden
	 */ 

	// 1. Verbindungsfehler
	public function connection_error($add_text) {

		$error_no = mysql_errno();
		$error_msg = mysql_error();
		echo $add_text." (Mysql-Fehler: ($error_no) $error_msg)";
		exit;
	}

	// 2. Normale Fehler -> Nummer
	public function last_errorNr() {
		return $this->dbLastErrorNr;
	}

	// 3. Normale Fehler -> Meldung
	public function last_error() {
		return $this->dbLastError;
	}
}
?>
