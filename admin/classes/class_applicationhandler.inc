<?php
namespace Contentomat;

/**
 * class_applicationhandler.in
 * Klasse stellt Methoden zur behandlung der Anwendungs- und Tabelleinträge im Content-o-Mat zur Verfügung.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2014-04-15
 *
 */
Class ApplicationHandler {

	// protected $appID;
	protected $db;
	protected $settingsCache;
	protected $cmt;
	protected $form;
// 	protected $baseRights;
	protected $dataCache;
	protected $defaultIcon;
	protected $iconSizes;
	protected $defaultIconSize;
	protected $applicationsTable;
	protected $fieldHandler;
	protected $lastError;

	public function __construct() {

		$this->db = new DBCex();
		$this->cmt = Contentomat::getContentomat();
		$this->fieldHandler = new FieldHandler();
		$this->form = new Form();

		$this->applicationsTable = 'cmt_tables';
		$this->groupsTable = 'cmt_tables_groups';

// 		$this->baseRights = array(
// 			'access',
// 			'new',
// 			'edit',
// 			'duplicate',
// 			'delete'
// 		);

// ??? start		
		$this->defaultIcon = array(
			'table' => 'administration/img/default_table_icon{SIZE}.png',
			'application' => 'administration/img/default_application_icon{SIZE}.png',
			'group' => 'administration/img/default_group_icon{SIZE}.png'
		);

		$this->iconSizes = array(
			'small' => '_small',
			'medium' => '_medium',
			'large' => '_large',
			'xlarge' => '_xlarge',
		);

		$this->defaultIconSize = 'xlarge';

		$this->clearDataCache();
// ??? end

// 		$this->appID = intval($tableID);
	}

	/**
	 * public getApplications()
	 * Ermittelt alle Anwendungen und Einträge mitsamt ihrer Datensätze.
	 *
	 * @param array $params Assoziatives Array mit folgenden (optionalen) Parametern
	 * 'sortBy' => Sortierung nach 'name' (alphabetisch) oder 'position' (default)
	 * 'groups' => true/ false (default), Einträge werden um die Daten ihrer zugehörigen Gruppe erweitert.
	 * 'groupID' => Nur die einträge der angegebenen Gruppe werden ermittelt.
	 * 'applicationID' => Nur die Daten des einen Eintrags werden ermittelt.
	 *
	 * @return array Assoziatives Array mit den Anwendungsdaten.
	 */
	public function getApplications($params = array()) {

		$defaultParams = array(
			'sortBy' => 'position',
			'groups' => false, // soll das assoziative Array um Gruppendaten erweitert werden? ACHTUNG: Falls nicht, werden leere Gruppen nicht ermittelt!
			'groupID' => 0,
			'applicationID' => 0,
			'isVisible' => null
		);

		$params = array_merge($defaultParams, $params);

		if ($params['sortBy'] == 'name') {
			$queryOrder = 'ORDER BY t.cmt_tablename ASC';
		} else {
			$queryOrder = 'GROUP BY t.cmt_group ORDER BY t.cmt_itempos ASC';
		}
		
		// Nur Anwendungen
		$whereParts = array();
		$onParts = array();
		
		if (!$params['groups']) {

			$applicationID = $params['applicationID'];
			$groupID = $params['groupID'];

			if ($applicationID) {
				$onParts[] = "tg.id = t.cmt_group";
				$whereParts[] = "t.id = '" . $applicationID . "'";
				
// 				$queryWhere = "ON tg.id = t.cmt_group WHERE t.id = '" . $applicationID . "'";
// 				$queryOrder = '';
			}

			if ($groupID) {
				$onParts[] = "tg.id = '" . $groupID . "'";
				$whereParts[] = "t.cmt_group = '" . $groupID . "'";
// 				$queryWhere = "ON tg.id = '" . $groupID . "' WHERE t.cmt_group = '" . $groupID . "'";
 				$queryOrder = 'ORDER BY t.cmt_itempos ASC';
			}
			
			if ($params['isVisible']) {
				$onParts[] = "tg.cmt_visible = '1'";
				$whereParts[] = "t.cmt_itemvisible = '1'";
			} else if ($params['isVisible'] === 0) {
				$onParts[] = "tg.cmt_visible = '0'";
				$whereParts[] = "t.cmt_itemvisible = '0'";
			} 

			if (!empty($whereParts)) {
				$queryWhere = " WHERE " . implode(" AND ", $whereParts);
			}
			if (!empty($onParts)) {
				$queryOn = " ON " . implode(" AND ", $onParts);
			}
			
			$apps = array();
			$query = "SELECT 
 				tg.*,
				t.*,
				tg.id AS groupID,
				tg.cmt_visible AS groupVisible,
				t.cmt_itemvisible AS applicationVisible 
				FROM cmt_tables t 
				JOIN cmt_tables_groups tg " .
				$queryOn . " " .
				$queryWhere . " " . 
				$queryOrder;
			
			$this->db->query($query);

			while ($r = $this->db->get()) {
				$r['cmt_tablesettings'] = $this->cmt->safeUnserialize($r['cmt_tablesettings']);
				$r['cmt_groupsettings'] = $this->cmt->safeUnserialize($r['cmt_groupsettings']);
				$r['cmt_templates'] = (array)$this->cmt->safeUnserialize($r['cmt_templates']);

				$apps[] = $r;
				$this->dataCache['applications'][$r['id']] = $r;
			}

			return $apps;
		}

		// 2. Anwendungen in Gruppen strukturiert
		if ($params['groups']) {

			$groups = $this->getApplicationGroups(array(
				'groupID' => intval($params['groupID']),
				'isVisible' => $params['isVisible']
			));
// 			if ($params['groupID']) {
// 				$groups = $this->getApplicationGroup($params['groupID']);
// 			} else {
// 				$groups = $this->getApplicationGroups();
// 			}

			if ($params['isVisible']) {
				$visibility = " AND cmt_itemvisible = '1'";
			} else if ($params['isVisible'] === 0) {
				$visibility = " AND cmt_itemvisible = '0'";
			} else {
				$visibility = '';
			}
			
			$structured = array();

			foreach ($groups as $group) {

				$appsInGroup = array();

				$this->db->query("
					SELECT *,
					cmt_itemvisible AS applicationVisible 
					FROM cmt_tables 
					WHERE cmt_group = '" . intval($group['id']) . "' " . $visibility . " 
					ORDER BY cmt_itempos ASC"
				);

				while ($r = $this->db->get()) {
					$r['cmt_tablesettings'] = $this->cmt->safeUnserialize($r['cmt_tablesettings']);
					$appsInGroup[] = $r;

					$this->dataCache['applications'][$r['id']] = $r;
				}

				$group['cmtApplications'] = $appsInGroup;
				$group['cmt_groupsettings'] = $this->cmt->safeUnserialize($group['cmt_groupsettings']);

				$this->dataCache['groups'][intval($group['id'])] = $group;

				$structured[] = $group;
			}

			return $structured;
		}
	}

	/**
	 * public function getApplicationGroups()
	 * Enter description here ...
	 *
	 * @param array $params Parameter werden per assoziativem Array übergeben
	 *
	 * @return array Assoziatives Array mit den Datensätzen der Gruppen.
	 */
	public function getApplicationGroups($params = array()) {

		$defaultParams = array(
			'sortBy' => 'position',
			'groupID' => 0,
			'isVisible' => null
		);

		$params = array_merge($defaultParams, $params);

		$groupID = intval($params['groupID']);
		$queryWhere = '';
		$whereParts = array();
		
		if ($params['isVisible']) {
			$whereParts[] = "cmt_visible = '1'";
		} else if ($params['isVisible'] === 0) {
			$whereParts[] = "cmt_visible = '0'";
		}
		
		if ($groupID) {
			$whereParts[] = "id='" . $groupID . "'";
		}
		
		if (!empty($whereParts)) {
			$queryWhere = " WHERE " . implode(" AND ", $whereParts);
		}

		if ($params['sortBy'] == 'name') {
			$queryOrder = 'ORDER BY cmt_groupname ASC';
		} else {
			$queryOrder = 'ORDER BY cmt_grouppos ASC';
		}

		$groups = array();

		$this->db->query("SELECT * FROM cmt_tables_groups " . $queryWhere . " " . $queryOrder);
		while ($group = $this->db->get()) {

			$group['cmt_groupsettings'] = $this->cmt->safeUnserialize($group['cmt_groupsettings']);
			$this->dataCache['groups'][intval($group['id'])] = $group;

			$groups[] = $group;
		}
		return $groups;
	}

	/**
	 * public function getApplicationGroup()
	 * Liefert die Daten einer Anwendungsgruppe.
	 *
	 * @param number $groupID Datenbank-ID der Gruppe
	 *
	 * @return array Assoziatives Array mit den Daten der Gruppe.
	 */
	public function getApplicationGroup($groupID) {

		if ($this->dataCache['groups'][$groupID]) {
			return $this->dataCache['groups'][$groupID];
		} else {
			return $this->getApplicationGroups(array('groupID' => $groupID));
		}
	}

	/**
	 * public function getImportApplicationGroupId()
	 * 
	 * get table group id, where new applications will imported 
	 * 
	 * @return int 
	 */
	public function getImportApplicationGroupID() {
		$query = "SELECT id FROM cmt_tables_groups WHERE cmt_isimportgroup = 1";
		$this->db->Query($query);
		$res = $this->db->get();
		if (is_array($res) && !empty($res)) {
			return $res['id'];
		} else {
			return 0;
		}
	}

	/**
	 * public function getApplication()
	 * Liefert die Daten einer Anwendung.
	 *
	 * @param number $applicationID Datenbank-ID der Anwendung
	 *
	 * @return array Assoziatives Array mit den Daten der Anwendung.
	 */
	public function getApplication($applicationID) {

		if ($this->dataCache['applications'][$applicationID]) {
			return $this->dataCache['applications'][$applicationID];
		} else {
			$appData = $this->getApplications(array('applicationID' => $applicationID, 'groups' => false));
			return $appData[0];
		}
	}

	/**
	 * public function getApplicationIcon()
	 * Liefert den Pfad des Icons der Anwendung.
	 *
	 * @param number $applicationID Datenbank-ID der Anwendung
	 * @param string $size Größenbezeichnung. Mögliche Werte xlarge, large, medium und small (small liefert derzeit medium)
	 *
	 * @return string Pfad zum Icon
	 */
	public function getApplicationIcon($applicationID, $size = 'xlarge') {

		if (!in_array($size, array_keys($this->iconSizes))) {
			$size = $this->defaultIconSize;
		}

		if ($this->dataCache['applications'][$applicationID]) {
			$applicationData = $this->dataCache['applications'][$applicationID];
		} else {
			$applicationData = $this->getApplication($applicationID);
		}

		if (isset($applicationData['cmt_tablesettings']['icon']) && !stristr($applicationData['cmt_tablesettings']['icon'], 'default')) {
// TODO: Icon-Größen auch bei individuellen Icons ermöglichen
			return $applicationData['cmt_tablesettings']['icon'];
		} else {
			return str_replace('{SIZE}', $this->iconSizes[$size], $this->defaultIcon[$applicationData['cmt_type']]);
		}
	}

	/**
	 * public function getGroupIcon()
	 * Liefert den Pfad des Icons der Gruppe.
	 *
	 * @param number $groupID Datenbank-ID der Anwendung
	 * @param string $size Größenbezeichnung. Mögliche Werte xlarge, large, medium und small (small liefert derzeit medium)
	 *
	 * @return string Pfad zum Icon
	 */
	public function getGroupIcon($groupID, $size = 'xlarge') {

		if (!in_array($size, array_keys($this->iconSizes))) {
			$size = $this->defaultIconSize;
		}

		if ($this->dataCache['groups'][$groupID]) {
			$groupData = $this->dataCache['group'][$groupID];
		} else {
			$groupData = $this->getGroup($groupID);
		}

		if ($groupData['cmt_groupsettings']['iconPath'] && !stristr($groupData['cmt_groupsettings']['iconPath'], 'default')) {
// TODO: Icon-Größen auch bei individuellen Icons ermöglichen
			return $groupData['cmt_groupsettings']['icon'];
		} else {
			return str_replace('{SIZE}', $this->iconSizes[$size], $this->defaultIcon['group']);
		}
	}

// 	public function getBaseRights() {
// 		return $this->baseRights;
// 	}

	public function getApplicationByFilename($applicationName) {

		$this->db->query("SELECT * FROM cmt_tables WHERE cmt_include LIKE '%" . $this->db->dbQuote(trim($applicationName)) . "%'");

		$apps = array();

		while ($r = $this->db->get()) {
			$r['cmt_tablesettings'] = $this->cmt->safeUnserialize($r['cmt_tablesettings']);
			$r['cmt_templates'] = (array)$this->cmt->safeUnserialize($r['cmt_templates']);
			$apps[] = $r;

			$this->dataCache['applications'][$r['id']] = $r;
		}

		return $apps;
	}

	public function getApplicationByTablename($tableName) {

		$this->db->query("SELECT * FROM cmt_tables WHERE cmt_tablename = '" . $this->db->dbQuote(trim($tableName)) . "' LIMIT 1");

		$apps = array();

		$r = $this->db->get();
		if (!empty($r)) {
			$r['cmt_tablesettings'] = (array)$this->cmt->safeUnserialize($r['cmt_tablesettings']);
			$r['cmt_templates'] = (array)$this->cmt->safeUnserialize($r['cmt_templates']);
			$this->dataCache['applications'][$r['id']] = $r;
			return $r;
		} else {
			return array();
		}
	}

	public function clearDataCache() {

		$this->dataCache = array(
			'groups' => array(),
			'applications' => array()
		);
	}

	/**
	 * public function createApplication()
	 * 
	 * create Application or table
	 * 
	 * @param array $params
	 * @return int ID of new created application or table in database table 'cmt_tables'
	 */
	public function createApplication($params = array()) {
		
		if (!is_array($params)) {
			return 0;
		}

		if (!$params['tableName'] && !$params['applicationFile']) {
			return 0;
		}

		$defaultParams = array(
			'tableData' => array(),
			'tableFields' => array(),
		);

		$params = array_merge($defaultParams, $params);


		// 1.1 create table
		$check = $this->db->query("CREATE TABLE " . $params['tableName'] . " (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY ( id ))");
		if ($check) {
			return 0;
		}


		// 1.2 alter charset (MySQL uses default collation)
		if ($params['tableCharset'] && ($params['tableCharset'] != $params['tableData']['charSet'])) {

			$check = $this->db->query("ALTER TABLE " . $params['tableName'] . " CONVERT TO CHARACTER SET " . $params['tableCharset']);
			if ($check) {
				return 0;
			}
		} else {
			$check = $this->db->query("ALTER TABLE " . $params['tableName'] . " CONVERT TO CHARACTER SET " . $params['tableData']['charSet']);
			if ($check) {
				return 0;
			}
		}

		// 1.3 Import Table data
		$tableDataHlp = $params['tableData'];
		$tableData = array(
			'cmt_tablename' => $tableDataHlp['tableName'],
			'cmt_showname' => $tableDataHlp['displayName'],
			'cmt_charset' => $tableDataHlp['charSet'],
			'cmt_collation' => $tableDataHlp['collation'],
			'cmt_include' => $tableDataHlp['applicationFile'],
			'cmt_itempos' => $tableDataHlp['position'],
			'cmt_addvars' => $tableDataHlp['addVars'],		// OUTDATED????
			'cmt_showfields' => $tableDataHlp['fieldsOverview'],
			'cmt_editstruct' => $tableDataHlp['fieldsEditMode'],
			'cmt_group' => $tableDataHlp['group'],
			'cmt_ownservice' => $tableDataHlp['ownServiceInclude'],
			'cmt_type' => $tableDataHlp['type'],
			'cmt_templates' => (array)$tableDataHlp['templates'],
			'cmt_itemvisible' => $tableDataHlp['itemVisible'],
			'cmt_target' => $tableDataHlp['target'],		// OUTDATED????
			'cmt_queryvars' => $tableDataHlp['queryVars'],
			'cmt_systemtable' => $tableDataHlp['isSystemTable'],
			'cmt_tablesettings' => $tableDataHlp['tableSettings']
		);

		// check charset and collation
		if ($params['tableCharset']) {
			$tableData['charSet'] = $params['tableCharset'];
		}

		if ($params['tableCollation']) {
			$tableData['collation'] = $params['tableCollation'];
		}



		// Save data in cmt_tables
		$check = $this->db->query('INSERT INTO cmt_tables SET ' . $this->db->makeSetQuery($tableData));
		if ($check) {
			return 0;
		}



		// 1.4 Import & Add Fields 
		if (!empty($params['tableFields'])) {
			foreach ($params['tableFields'] as $fieldName => $fieldData) {

				$check = $this->fieldHandler->addField($fieldData);

				if ($check) {
					return 0;
				}
			}
		}

		$applicationData = $this->getApplicationByTablename($params['tableName']);
		return $applicationData['id'];
	}

	/**
	 * public function editApplication()
	 * Edit the data and the table properties of an existing application entry.
	 *
	 * @param array $newApplicationData The data set of the entry / application as provided from method "ApplicationHandler::getApplication" (associative array: fieldnames  in cmt_tables => values in cmt_tables 
	 *
	 * @return boolean
	 */
	public function editApplication($newApplicationData = array()) {

//		$debug = new Debug();

		if (!is_array($newApplicationData) || !$newApplicationData['id']) {
			return false;
		}

		$oldApplicationData = $this->getApplication(intval($newApplicationData['id']));
		$tableName = '';
		$collation = '';
		$charset = '';
		
		// Rename table if neccesarry
		if ($newApplicationData['cmt_tablename'] && $newApplicationData['cmt_tablename'] != $oldApplicationData['cmt_tablename']) {
			
			$check = $this->db->renameTable($oldApplicationData['cmt_tablename'], $newApplicationData['cmt_tablename']);
			if (!$check) {
				return false;
			}
//$debug->info('Table name changed to: ' . $newApplicationData['cmt_tablename']);
				
			// rename fields
			$check = $this->fieldHandler->changeFieldsTable($oldApplicationData['cmt_tablename'], $newApplicationData['cmt_tablename']);
			if (!$check) {
				return false;
			}
//$debug->info('Field names changed for: ' . $newApplicationData['cmt_tablename']);

			$tableName = $newApplicationData['cmt_tablename'];
		} else {
			$tableName = $oldApplicationData['cmt_tablename'];
		}
		
//$debug->info('Current table name: ' . $tableName);
		
		// Alter charset if neccessary
		if ($newApplicationData['cmt_charset'] && $newApplicationData['cmt_charset'] != $oldApplicationData['cmt_charset']) {

			if ($newApplicationData['cmt_collation']) {
				$collation = $newApplicationData['cmt_collation'];
			} else {
				$collation = $this->db->getDefaultCollation($newApplicationData['cmt_charset']);
			}

			$check = $this->db->changeCharset($newApplicationData['cmt_tablename'], $newApplicationData['cmt_charset'], $collation);
			
			if (!$check) {
				return false;
			}
			
			$charset = $newApplicationData['cmt_charset'];
			
//$debug->info('Charset changed: ' . $charset . ', collation: ' . $collation);
		} else {
			$charset = $oldApplicationData['cmt_charset']; 
		}

		// Alter collation if neccessary
		if (!$collation && $newApplicationData['cmt_collation'] && $newApplicationData['cmt_collation'] != $oldApplicationData['cmt_collation']) {
		
			if ($newApplicationData['cmt_collation']) {
				$collation = $newApplicationData['cmt_collation'];
				
				if (!$this->db->isValidCollation($collation, $charset)) {
					$collation = $this->db->getDefaultCollation($charset);
				}
			} else {
				$collation = $this->db->getDefaultCollation($newApplicationData['cmt_charset']);
			}
		
			
			$check = $this->db->setCollation($newApplicationData['cmt_tablename'], $newApplicationData['cmt_charset'], $collation);
			
			if (!$check) {
				return false;
			}
			//$debug->info('Collation changed: ' . $collation);
		}
		
		// Insert new application data in 'cmt_tables'
		$applicationID = intval($newApplicationData['id']);
		unset($newApplicationData['id']);
		
		//$debug->info("UPDATE " . $this->applicationsTable . " SET " . $this->db->makeSetQuery($newApplicationData) . " WHERE id = '" . $applicationID ."'");

		$check = $this->db->query("UPDATE " . $this->applicationsTable . " SET " . $this->db->makeSetQuery($newApplicationData) . " WHERE id = '" . $applicationID ."'");

		return !(boolean)$check;
	}
	
	
	// ErrorNr: 4401 "Konnte Tabelle nicht löschen: 
	// ErrorNr: 4002 "Konnte Feldinformationen (cmt_fields) für Tabelle nicht löschen
	// ErrorNr: 4003 "Konnte Tabelleninformationen (cmt_tables) für Eintrag nicht löschen
	// ErrorNr: 4004 "Der Eintrag wurde gelöscht, die übrigen Einträge in der Gruppe konnten allerdings nicht richtig sortiert werden
	
	
	/**
	 * public function deleteApplication()
	 * 
	 * Delete application or table from system. Deletes all related entries of table in cmt_tables, cmt_fields
	 * 
	 * @param type $applicationID Id of application as in database table "cmt_tables"
	 * @return boolean 
	 */
	public function deleteApplication($applicationID) {


		if ($applicationID) {
			$applicationData = $this->getApplication($applicationID);
		} else {
			return false;
		}

		// Drop table if application is or has one
		if ($applicationData['cmt_type'] == 'table') {

			// 2. delete fields
			$check = $this->fieldHandler->deleteAllFields($applicationData['cmt_tablename']);
				
			if (!$check) {
				$this->lastError = '4002';
				return false;
			}
// $debug->info('Delete fields: ' . $applicationData['cmt_tablename']);
			
			// 1. delete table
			$this->db->query("DROP TABLE " . $applicationData['cmt_tablename']);

			if ($this->db->last_errorNr()) {
				$this->lastError = '4001';
				return false;
			} 
// $debug->info('Delete table: ' . $applicationData['cmt_tablename']);			

		}

		// Delete entry in table 'cmt_tables' 
		$this->db->query("DELETE FROM cmt_tables WHERE id = '" . intval($applicationData['id']) . "'");

		if ($this->db->last_errorNr()) {
			
			$this->lastError = "4003"; 
			return false;
		} else {
// $debug->info('Delete from cmt_table: ' . intval($applicationData['id']));			
			// Reorder positions in group
			$this->db->query("SET @rownumber = 0");
			$this->db->query("
				UPDATE cmt_tables 
				SET cmt_itempos = (@rownumber:=@rownumber+1) 
				WHERE cmt_group = '" . $applicationData['cmt_group'] . "'
				ORDER BY cmt_itempos ASC
			");
			
			if ($this->db->last_errorNr()) {
				$this->lastError = "4004"; 
				return false;
			}
// $debug->info('cmt_tables reorderer.');
		}

		return true;
	}

	public function getApplicationSettings($applicationID) {
		
		$applicationID = intval($applicationID);
		$appData = $this->getApplication($applicationID);
		return $appData['cmt_tablesettings'];
		
	}

	public function createApplicationGroup() {
		
	}

	public function deleteApplicationGroup() {
		
	}

	public function getApplicationGroupSettings() {
		
	}

// TODO: checken!!!	
	/**
	 * public function duplicateApplication()
	 * 
	 * Duplicate (create) table based on other table
	 * 
	 * @param array $params
	 * @return int 
	 */
	// ToDo rename in duplicateApplication
	
	public function duplicateApplication($params) {

		//$debug = new Debug();
		if (!is_array($params)) {
			return 0;
		}

		if (!$params['sourceTableName'] || !$params['tableName']) {
			return 0;
		}
//$debug->log($params);

		$defaultParams = array(
			'copyData' => true
		);
		$params = array_merge($defaultParams, $params);

		$tableData = $this->getApplicationByTablename($params['sourceTableName']);
//$debug->log($tableData);
		// 1.1 create table
		$check = $this->db->query("CREATE TABLE " . $params['tableName'] . " LIKE " . $params['sourceTableName']);
		if ($check) {
			return false;
		}
//$debug->log('table created: ' . $params['tableName']);

		// 1.2 alter charset (MySQL uses default collation)
		if ($params['tableCharset'] != $tableData['cmt_charset']) {

			$check = $this->db->query("ALTER TABLE " . $params['tableName'] . " CONVERT TO CHARACTER SET " . $params['tableCharset']);
			if ($check) {
				return false;
			}
//$debug->log('charset changed: ' . $params['tableCharset']);
		}

		// 1.3 duplicate contents
		if ($params['copyData']) {
			$check = $this->db->query("INSERT INTO " . $params['tableName'] . " SELECT * FROM " . $params['sourceTableName'] . "");
			if ($check) {
				return false;
			}
//$debug->log('content copied from: ' . $params['sourceTableName'] . ' ' . $params['tableName']);
		}

		// 2.1 Insert into cmt_tables
		$tableData = $this->getApplicationByTablename($params['sourceTableName']);

		unset($tableData['id']);
		$tableData['cmt_tablename'] = $params['tableName'];
		$tableData['cmt_showname'] = $params['tableAlias'];
		$tableData['cmt_tablesettings'] = $this->cmt->safeSerialize($tableData['cmt_tablesettings']);

		// check charset and collation
		if (!$params['tableCharset']) {
			$tableData['cmt_charset'] = $this->db->getTableCharset($params['sourceTableName']);
		} else {
			$tableData['cmt_charset'] = $params['tableCharset'];
		}

		if (!$params['tableCollation']) {
			$tableData['cmt_collation'] = $this->db->getTableCollation($params['sourceTableName']);
		} else {
			$tableData['cmt_collation'] = $params['tableCollation'];
		}

		// Save data in cmt_tables
		$check = $this->db->query('INSERT INTO cmt_tables SET ' . $this->db->makeSetQuery($tableData));
		if ($check) {
			return 0;
		}
		$this->cleanUpOrder($tableData['cmt_group']);
//$debug->log($tableData);
//$debug->log('New table created in cmt_tables');

		// 3. duplicate fields in cmt_fields
		$fields = $this->fieldHandler->getAllFields(array(
			'tableName' => $params['sourceTableName'],
			'getAll' => true
		));
//$debug->log($fields);		
		
//  		* @param number id Eindeutige ID des Feldes in cmt_fields
//  		* @param string tableName Tabellenname. Falls id nicht vorhanden, wird Tabellen- und Feldname benötigt
//  		* @param string fieldName Datenbankfeldname
//  		* @param string fieldType Feldtyp (CMS)
//  		* @param string fieldAlias Optional: Aliasname des Feldes
//  		* @param string fieldDefaultValue Optional: Defaultwert des Feldes bei Neueintrag
//  		* @param string fieldDescription Optional: Feldbeschreibung
//  		* @param string fieldOptions Optional: Feldoptionen

		foreach ($fields as $fieldName => $fieldData) {
//$debug->log($fieldName);

//$debug->log($fieldData);
			$check = $this->fieldHandler->addField(array(
				'tableName' => $params['tableName'],
				'fieldName' => $fieldName,
				'fieldType' => $fieldData['cmt_fieldtype'],
				'fieldAlias' => $fieldData['cmt_fieldalias'],
				'fieldDefaultValue' => $fieldData['cmt_default'],
				'fieldDescription' => $fieldData['cmt_fielddesc'],
				'fieldOptions' => $this->cmt->safeUnserialize($fieldData['cmt_options'])
			));

			if (!$check) {
// $debug->log('ERROR: ' . $fieldName);
// $debug->log($check);
				return false;
			}
//$debug->log('Passed: ' . $fieldName);
		}

		$applicationData = $this->getApplicationByTablename($params['tableName']);
//$debug->log($applicationData);
		return intval($applicationData['id']);
	}

	/**
	 * public function cleanUpOrder()
	 * Cleans up the order of items in a group (e.g. when one item was deleted)
	 * 
	 * @param number $groupID ID of the group
	 * @return boolean
	 */
	public function cleanUpOrder($groupID) {
		$groupID = intval($groupID);

		$this->db->query("SET @newPos = 0");

		$check = $this->db->query("UPDATE " . $this->applicationsTable . " SET cmt_itempos = (@newPos :=  @newPos + 1) WHERE cmt_group = '" . $groupID . "' ORDER BY cmt_itempos");

		return (boolean) !$check;
	}

	/**
	 * public function getImportGroupID()
	 * Returns the ID of the group defined as import group.
	 * 
	 * @params void
	 * @return number ID of import group
	 */
	public function getImportGroupID() {

		$this->db->query("SELECT id FROM cmt_tables_groups WHERE cmt_isimportgroup = '1' ORDER BY id ASC LIMIT 1 ");
		$r = $this->db->get();

		return intval($r['id']);
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	/**
	 * public function getCodeToExecute()
	 * Returns all code snippets to be exected for the application.
	 * 
	 * @param number $applicationID ID of application
	 * @return array returns an associative array (key = execution time/ event, value = code snippet)
	 */
	public function getCodeToExecute($applicationID) {

		$executeCode = array();
		
		$query = "SELECT * FROM cmt_execute_code WHERE cmt_tablename = '" . intval($applicationID) . "'";
		$this->db->query($query);
		
		while ($r = $this->db->get()) {
			
			$executeCode[$r['cmt_executiontime']] .= stripslashes($r['cmt_code']);
			//$executeCode[$r['cmt_executiontime']] .= $r['cmt_code'];
		}
		
		return $executeCode;
	} 
	
	public function makeApplicationSelect($selected='', $name='id', $type='all') {
		
		$form = new Form();
		$data = $this->getApplications(array('groups' => true));
		$structuredData = array();
		$c = 0;
		$ca = 0;

		foreach ($data as $group) {
			$structuredData[$c]['label'] = $group['cmt_groupname'];
			$apps = array();
			
			foreach ($group['cmtApplications'] as $application) {
				
				switch($type) {
					case 'table':
						if ($application['cmt_type'] == 'table') {
							$apps[$ca]['alias'] = $application['cmt_showname'];
							$apps[$ca]['value'] = $application['id'];
						}
						break;
						
					case 'application':
						if ($application['cmt_type'] == 'application') {
							$apps[$ca]['alias'] = $application['cmt_showname'];
							$apps[$ca]['value'] = $application['id'];
						}
						break;
						
					default:
						$apps[$ca]['alias'] = $application['cmt_showname'];
						$apps[$ca]['value'] = $application['id'];
						
						break;
				}
				
				$ca++;
			}
			$structuredData[$c]['options'] = $apps;
			$c++;
			$ca = 0;
		}
//var_dump($structuredData);		
		return $form->select(array(
			'nestedData' => $structuredData,
			'name' => $name,
			'selected' => $selected
		));
	}

}
?>