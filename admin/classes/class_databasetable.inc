<?php
/**
 * OUTDATED: class_databasetable.inc
 * Klasse stellt Methoden zur Behandling von MySQL- UND Contentomat-Tabellen zur Verfügung
 *
 * Langkommentar
 * 
 * @version 2012-08-09
 * @author J.Hahn <info@buero-hahn.de>
 */
namespace Contentomat;

 class DatabaseTable {

 	protected $tableID;
 	protected $db;
 	protected $settingsCache;
 	protected $cmt;

 	public function __construct($tableID=0) {

 		$this->db = new DBCex();
 		$this->cmt = Contentomat::getContentomat();
 		
 		$this->tableID = intval($tableID);

 	}
 	
  	/**
 	 * public function getSettings()
 	 * Gibt die Daten einer Datenbanktabelle als assoziatives Array zurück.
	 *
 	 * @param number $applicationID Optionale ID der Anwendung (ansonsten wird versucht, die beim Erzeugen des Objekts übergebene ID zu verwenden)
 	 *
 	 * @return array Assoziatives Array mit den Eigenschaftsdaten aus der Tabelle "cmt_tables"
 	 */
 	public function getSettings($tableID=0) {

 		$tableID = intval($tableID);
 	 	if (!$tableID) {
 			$tableID = $this->getID();
 		}
 			
 		if ($this->settingsCache[$tableID]) {
 			
 			return $this->settingsCache[$tableID];
 		} else {
 			
 			$this->db->query("SELECT * FROM cmt_tables WHERE id = '".$this->db->dbQuote($tableID)."'");
 			$r = $this->db->get();
 			
 			$r['cmt_settings'] = $this->cmt->safeUnserialize($r['cmt_tablesettings']);
 			
 			if (!is_array($r['cmt_settings'])) {
 				$r['cmt_settings'] = array();
 			}
 			
 			$this->settingsCache[$tableID] = $r;
 			
 			return $r;
 		}
 		
 	}
 	
 	public function getSettingsByID($tableID=0) {
 		return $this->getSettings(intval($tableID));
 	}

 	/**
 	 * public function getOverviewModeFields()
 	 * Gibt eine Liste der Felder aus, die in der Tabellenübersicht angezeigt werden sollen.
 	 *
 	 * @param number $tableID ID der Datenbanktabelle
 	 *
 	 * @return array Array mit den Namen der Tabellenfelder in der Tabellenübersichtsansicht.
 	 */
 	public function getOverviewModeFields($tableID=0) {
 	 	
 		$settings = $this->getSettings($tableID);
 		
 		if (trim($settings['cmt_showfields']) != '') {
 			$fields = explode ("\n", $settings['cmt_showfields']);
	    	return $this->cmt->arrayTrim($fields);			
 		} else {
 			return $this->getFieldNames($tableID);
 		}
 	}

 	/**
 	 * public / private / protected function getEditModeFields()
 	 * Gibt eine Liste der Felder aus, die in der Eintragsbearbeitungsansicht angezeigt werden sollen.
 	 *
 	 * @param number $tableID ID der Tabelle
 	 *
 	 * @return array Array mit den Namen der Tabellenfelder in der Bearbeitungsansicht.
 	 */
 	public function getEditModeFields($tableID=0) {
 		$settings = $this->getSettings($tableID);
 		
 	 	if (trim($settings['cmt_editstruct']) != '') {
 			$fields = explode ("\n", $settings['cmt_editstruct']);
	    	$fields = $this->cmt->arrayTrim($fields);

	    	$allFields = $this->getFieldNames($tableID);
// var_dump(array_intersect($fields, $allFields));
// TODO: Alle Felder, die nicht in $r['cmt_editstruct'] auftauchen, müssen noch mit einem "{HEAD}Weitere Variablen" angehängt werden!    	
	    	
 		} else {
 			return $this->getFieldNames($tableID);
 		}
 	}
 	
 	/*
 	 * TODO: Diese Methode in FieldHandler einbauen!!!
 	 */
 	public function getFieldNames($tableID=0) {
 	 	
 		$tableID = intval($tableID);
 	 	if (!$tableID) {
 			$tableID = $this->getID();
 		}
 			 	
 		if (is_array($this->settingsCache[$tableID]['fieldNames'])) {
 			return $this->settingsCache[$tableID]['fieldNames'];
 		} else {
 			$tableName = $this->getTableNameFromID($tableID);
	 		$this->db->query("SELECT * FROM cmt_fields 
	 						  WHERE cmt_tablename = '".$this->db->dbQuote($tableName)."' 
	 						  ORDER BY cmt_fieldname"
	 		);
	 		
	 		while ($r = $this->db->get()) {
	 			$this->settingsCache[$tableID]['fieldNames'][$r['cmt_fieldname']] = $r['cmt_fieldname'];
	 			
	 			if ($r['cmt_fieldalias']) {
	 				$this->settingsCache[$tableID]['fieldAliases'][$r['cmt_fieldname']] = $r['cmt_fieldalias'];
	 			} else {
	 				$this->settingsCache[$tableID]['fieldAliases'][$r['cmt_fieldname']] = $r['cmt_fieldname'];
	 			}

	 			$this->settingsCache[$tableID]['fieldTypes'][$r['cmt_fieldname']] = $r['cmt_fieldtype'];	
	 		}
	 		
	 		return $this->settingsCache[$tableID]['fieldNames'];
 		}
 	}

 	/*
 	 * TODO: Diese Methode in FieldHandler einbauen!!!
 	 */
 	/**
 	 * 
 	 * public function getFieldAliases()
 	 * Gibt die Feldnamen-Aliase als assoziatives Array zurück.
 	 *
 	 * @param number $tableID ID der Tabelle
 	 * @return array Assoziatives Array mit Feldname (Schlüssel) und Feldalias (Wert)
 	 */
 	public function getFieldAliases($tableID=0) {

 		$settings = $this->getSettings($tableID);
 		
 		if (!is_array($settings['fieldAliases'])) {
 			$this->getFieldNames($tableID);
 			$settings = $this->getSettings($tableID);
 		}

 		return $settings['fieldAliases'];
 	}
 	
  	public function getID() {
 		return $this->tableID;
 	}
 	
 	public function getTableNameFromID($tableID=0) {
 		$settings = $this->getSettings($tableID);
 		
 		return $settings['cmt_tablename'];
 	}
 	
 	/**
 	 * public function countEntries()
 	 * Zählt entweder alle Einträge einer Tabelle oder ausgewählte. Erwartet Parameter in einem Array.
 	 * 
 	 * @param string tableName	Name der Datenbanktabelle
 	 * @param string condWhere	Optional: WHERE-Teile einer Query
 	 * 
 	 * @return mixed Anzahl der Einträge (integer) oder false
 	 */
 	public function countEntries($params=array()) {
 		
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'condWhere' => $this->condWhere
						);
 		$params = array_merge ($defaultParams, $params);

 		if (!$params['tableName']) return false;

 		$query = 'SELECT COUNT(*) AS entriesCount FROM '.$this->db->dbQuote($params['tableName']);
 		if ($params['condWhere']) $query .= ' WHERE '.$params['condWhere'];

 		$this->db->query($query);
 		$r = $this->db->get();
 		
 		if (!$this->db->getLastErrorNr()) {
 			return intval($r['entriesCount']);
 		} else {
 			return false;
 		}
 	}
 	

 }

?>
