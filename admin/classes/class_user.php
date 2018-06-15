<?php    
/**
 * Userklasse
 * 
 * Klasse, die Funktionalitäten und globale Variablen für die Userbehandlung
 * innerhalb des CMS zur Verfügung stellt.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2018-04-12
 */

namespace Contentomat;

class User {

	protected $user_name;
	protected $user_type;
	protected $lastLogin;
	protected $lastPasswordChange;
	protected $creationDate;
	
//	protected $user_protecteds;
//	protected $user_internalprotecteds;
	protected $user_id;
	protected $user_alias;
	protected $user_accessrights;
	protected $user_executionrights;
	protected $group_id;
	protected $group_internalprotecteds;
	protected $db;
	protected $baseRights;
	protected $cmt;
	
	
	
	private $userIsLoggedIn;


	/**
	 * public function __construct()
	 * Kontruktor
	 *
	 * @param string $sid Wird eine Session-ID übergeben, werden die Nutzerdaten ausgelesen und die Zugriffsberechtigungen überprüft.
	 *
	 * @return boolean Nur, wenn eine $sid übergeben wird.
	 */
	public function __construct($sid='') {
		
		$this->cmt = Contentomat::getContentomat();
		$this->db = new DBCex();
		
		$this->baseRights = array(
 			'access',
 			'new',
 			'edit',
 			'duplicate',
 			'delete',
			'view'
 		);
 		
 		$this->userIsLoggedIn = false;
		
		if ($sid) {
			
			// User-ID ermitteln
			$query = "SELECT cmt_userid FROM cmt_sessions WHERE cmt_sessionid = '".$sid."' LIMIT 1";
			$this->db->Query($query);
			$r = $this->db->Get();
			$this->user_id = $r['cmt_userid'];
			
			// Userdaten ermitteln
			$query = "SELECT * FROM cmt_users WHERE id = '".$this->user_id."' LIMIT 1";
			$this->db->Query($query);
			$r = $this->db->get();
			$this->user_internalvars = $r;
			
			$this->user_name = $r['cmt_username'];
			$this->user_alias = $r['cmt_useralias'];
			$this->user_type = $r['cmt_usertype'];
			$this->group_id = $r['cmt_usergroup'];
			$this->lastLogin = $r['cmt_lastlogin'];
			$this->lastPasswordChange = $r['cmt_passchanged'];
			$this->creationDate = $r['cmt_creationdate'];

			// Gruppendaten holen
			$query = "SELECT * FROM cmt_users_groups WHERE id = '".$this->group_id."'";
			$this->db->Query($query);
			$this->group_internalvars = $this->db->Get(MYSQLI_ASSOC);
			
			// 2012-11-26: TODO: Konstanten entfernen!
			define ('CMT_USERID', $this->user_id);
			define ('CMT_USERNAME', $this->user_name);
			define ('CMT_USERTYPE', $this->user_type);
			define ('CMT_USERALIAS', $this->user_alias);
			define ('CMT_GROUPID', $this->group_id);
			define ('CMT_GROUPNAME', $this->group_internalvars['cmt_groupname']);
			define ('CMT_TEMPLATE', 'templates/'.preg_replace('/\/+$/', '', $r['cmt_cmtstyle']).'/');

			// Outdated! Nicht mehr verwenden!
			define ("CMT_USERGROUPID", $this->group_id);
			define ("CMT_USERGROUP", $r['cmt_groupname']);
			define ("CMT_STYLE", $r['cmt_cmtstyle']);
			
			// User-Verzeichnis holen
			define ("CMT_USERDIRECTORY", $this->getUserDirectory());

			// Uservariablen holen
			$this->getAllUserVars();		
			
			if ($this->user_id) {
				$this->userIsLoggedIn = true;
				return true;
			} else {
				return false;
			}
		}


	}

	/**
	 * 
	 * public / private / protected function checkUserAccessRight()
	 * Enter description here ...
	 *
	 * @param number $cmtAppID ID der CMS-Anwendung für welche die Zugriffsrechte des Nutzers überprüft werden sollen.
	 *
	 * @return boolean true = alles in Ordnung, false = Nutzer darf nicht auf die Anwendung/ Tabelle zugreifen!
	 */
	public function checkUserAccessRight ($cmtAppID) {
		
		$cmtAppID = intval($cmtAppID);
		
		if ($this->user_type == "admin") {
			return true;
		} else {
			$this->getUserAccessRights ($cmtAppID);
			
			if ($this->user_accessrights[$cmtAppID]['access']) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * OUTDATED
	 * public function getUserPermissions()
	 * Ermittelt die Bearbeitungsrechte des eingeloggten Nutzers für die gewünschte Anwendung. 
	 *
	 * @param number $cmtAppID ID der CMS-Anwendung für welche die Zugriffsrechte des Nutzers überprüft werden sollen.
	 *
	 * @return array Assoziatives Array mit den Benutzerrechten.
	 */
	public function getUserPermissions ($cmtAppID) {
		if (!is_array($this->user_accessrights)) {
			$this->getUserAccessRights($cmtAppID);
		}

		return $this->user_accessrights[$cmtAppID];

	}
	
	public function getUserRights($userID=0, $appID=0) {
		
		$userID = intval($userID);
		if (!$userID) {
			$userID = $this->user_id;
		}
		
		// Falls keine User-ID übergeben wurde, dann die Daten des aktuellen Users zurückgeben
		if (!$userID) {
			$userID = $this->user_id;
		}
		
		// Rechte des Nutzers aus DB holen
		$this->db->Query("SELECT cmt_usergroup,cmt_showitems FROM cmt_users WHERE id = '". $userID ."' LIMIT 1");
		$r = $this->db->get();

		$userRights = unserialize(stripslashes($r['cmt_showitems']));
		$groupRights = $this->getGroupRights($r['cmt_usergroup']);

		$filteredUserRights = $this->checkUserAgainstGroupRights($userRights, $groupRights);

		// Falls nur die Rechte einer Anwendung angefordert werden, nur diese zurückgeben
		if ($appID) {
			if (is_array($filteredUserRights[$appID])) {  
				return $filteredUserRights[$appID];
			} else {
				return array();
			}
		}

		// Rechte zu User-ID ermitteln
		if (is_array($filteredUserRights)) {
			return $filteredUserRights;
		} else {
			return array();
		} 
	}
	
	
	
	// TODO: Was macht denn diese Methode?????
	public function getPermissions($cmtAppID, $cmtUserID=0) {
			
		$query = "SELECT * FROM cmt_users WHERE id = '".$this->user_id."' LIMIT 1";
		$this->db->Query($query);
		$r = $this->db->get();
		return $this->getUserAccessRights($cmtAppID); 
	}

	/**
	 * public function getAllUserRights()
	 * Liefert alle Zugriffsrechte eines oder des Benutzers als Array zurück. 
	 *
	 * @param number $userID Datenbank-ID des Benutzers. Wird keine ID übergeben, werden die Rechte des aktuell eingeloggten Benutzers ausgelesen.
	 *
	 * @return array Leeres oder multidimensionales Array mit der Struktur array($appID => array('access' => 1, 'new' => 1)) ...
	 */
	public function getAllUserRights($userID=0) {
		
		if (!$userID) {
			$userID = $this->user_id;
		}
		$query = "SELECT cmt_showitems FROM cmt_users WHERE id = '". intval($userID) ."' LIMIT 1";
		$this->db->Query($query);
		$r = $this->db->get();
		
		$userRights = unserialize($r['cmt_showitems']);
		
		if (!is_array($userRights)) {
			return array();
		}  else {
			return $userRights;
		}

	}
	
	/**
	 * public function checkUserPermission()
	 * Prüft, ob der Nutzer ein bestimtes Bearbeitungsrecht hat.
	 *
	 * @param string $action Name des Bearbeitungsrechts, z.B. "duplicate"
	 *
	 * @return boolean
	 */
	public function checkUserPermission($action, $cmtAppID = 0) {
		
		$cmtAppID = intval($cmtAppID);
		if (!$cmtAppID) {
			$cmtAppID = CMT_APPID;
		}
		
		if ($this->user_type=="admin") {
			return true;
		}
		
		if (!is_array($this->user_accessrights)) {
			$this->getUserAccessRights($cmtAppID);
		}
		return (bool)$this->user_accessrights[$cmtAppID][$action];

	}
	
	/**
	 * public function getUserAccessRights()
	 * Ermittelt die Zugangsberechtigungen des Users für alle Tabelle/ Anwendungen
	 *
	 * @param number $cmtAppID ID der CMS-Anwendung für welche die Zugriffsrechte des Nutzers überprüft werden sollen.
	 *
	 * @return array Assoziatives Array mit den Berechtigungsdaten
	 */
	public function getUserAccessRights ($cmtAppID, $cmtUserID=0) {
		
		$cmtUserID = intval($cmtUserID);
		
		if (!$cmtUserID) {
			$cmtUserID = $this->user_id;
		}
		
		$cmtAppID = intval($cmtAppID);
		$this->user_accessrights = array();

		//1. Userrechte holen
		$this->db->Query("SELECT cmt_showitems FROM cmt_users WHERE id = '". $cmtUserID ."' LIMIT 1");
		$r = $this->db->get();
		
		$user_access = unserialize(stripslashes($r['cmt_showitems'])); 

		if (!is_array($user_access)) {
			$user_access = array();
		}
		
		//2. Gruppenrechte holen
		$this->db->Query("SELECT cmt_showitems FROM cmt_users_groups WHERE id = '".$this->group_id."' LIMIT 1");
		$r = $this->db->get();

		$group_access = unserialize(stripslashes($r['cmt_showitems']));
		if (!is_array($group_access)) {
			$group_access = array();
		}
		if (empty($user_access)) {
			$user_access = $group_access;
		}
			

		// compare user's rights against group rights
		$cleanedUserRights = array();
		
		foreach ($group_access as $cmtAppID => $table_permissions) {

			// Nur Userrechte weitergeben, die auch Gruppenrechte sind
			if ($table_permissions['access']) {
				foreach ($table_permissions as $permissionName => $permission) {
					if ($permission && $user_access[$cmtAppID][$permissionName]) {
						$cleanedUserRights[$cmtAppID][$permissionName] = $permission;
					}
				}
			}
		}
		
		$this->user_accessrights = $cleanedUserRights;
		
		return $this->user_accessrights;
	}	

	public function checkUserAgainstGroupRights($userRights=array(), $groupRights=array()) {

		if (!is_array($userRights)) {
			$userRights = array();
		}
		
		if (!is_array($groupRights)) {
			$groupRights = array();
		}

		$filteredUserRights = array_merge($this->arrayKeysToString($groupRights), $this->arrayKeysToString($userRights));
		$filteredUserRights = array_intersect_key($this->arrayKeysToInteger($filteredUserRights), $groupRights);
		
		return $filteredUserRights;
	}

	/**
	 * public function setUserVar()
	 * Speichert eine einzelne Variable des Users.
	 *
	 * @param string $name Name der Variablen
	 * @param mixed $value Inhalte der Variablen
	 *
	 * @return boolean
	 */
	public function setUserVar($name, $value='') {
		if (!$name) {
			return false;
		}
		$this->user_vars[$name] = $value;
		
		return true;
	}

	/**
	 * public function deleteUserVar()
	 * Löscht eine einzelne Variable des Users.
	 *
	 * @param string $name Name der Variablen
	 *
	 * @return boolean
	 */
	public function deleteUserVar($name) {
		if (!$name) {
			return false;
		}
		unset ($this->user_vars[$name]);
		
		return true;
	}

	/**
	 * public function deleteAllUserVars()
	 * Löscht alle Variable des Users.
	 *
	 * @param void
	 *
	 * @return void
	 */
	public function deleteAllUserVars() {
		unset ($this->user_vars);
	}

	/**
	 * public function getUserVar()
	 * Liefert eine einzelne Variable des Users.
	 *
	 * @param string $name Name der Variablen
	 *
	 * @return mixed Wert der Variablen
	 */
	public function getUserVar($name) {
		return $this->user_vars[$name];
	}

	/**
	 * public function getAllUserVars()
	 * Liefert alle Uservariablen zurück.
	 *
	 * @param void
	 * 
	 * @return array Assoziatives Array mit den Uservariablen (Schlüssel = Variablenname, Wert = Variablenwert)
	 */
	public function getAllUserVars() {
		$this->db->query("SELECT cmt_uservars FROM cmt_users WHERE id = '".$this->user_id."'");
		$r = $this->db->get();
		$this->user_vars = unserialize($r['cmt_uservars']);

		return $this->user_vars;
	}

	/**
	 * public function saveUserVars()
	 * Speichert alle Benutzervariablen in der Datenbank. Methode muss zum Abschluss immer ausgeführt werden.
	 *
	 * @params void
	 * @return boolean True = Speicherung hat geklappt, false = Datenbankfehler bei der Speicherung
	 */
	public function saveUserVars() {
		$serialized_vars = serialize($this->user_vars);
		$this->db->Query("UPDATE cmt_users SET cmt_uservars = '".$serialized_vars."' WHERE id = '".$this->user_id."'");
		$result = $this->db->last_ErrorNr();
		if ($result) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * public function getUserGroup()
	 * Ermittelt die Gruppendaten eines Benutzers oder des angemeldeten Benutzers.
	 *
	 * @param number $userID Benutzer-ID. Wird kein Wert übergeben, dann werden die Daten des eingeloggten Users zurückgegeben.
	 *
	 * @return array Datensatz der Gruppe als assoziatives Array 
	 */
	public function getUserGroup($userID = 0) {
		
		$userID = intval($userID);
		
		if (!$userID) {
			$userID = $this->user_id;
		}
		
		if (!$userID) {
			return array();
		}
		
		$this->db->query("SELECT ug.* FROM cmt_users_groups ug 
						  JOIN cmt_users u 
						  ON u.id = '" . $userID . "' 
						  LIMIT 1");
		return $this->db->get();
	}
	
	/**
	 * public function getUserDirectory()
	 * Liefert das persönliche Verzeichnis des Nutzers zurück, sofern vorhanden.
	 *
	 * @param void
	 *
	 * @return string Verzeichnispfad
	 */
	public function getUserDirectory () {

		return preg_replace('/\/{2,}/', '\\', $this->group_internalvars['cmt_groupdirectory']."/".$this->user_internalvars['cmt_userdirectory']);
	}
	
	/**
	 * public function getUserStartPage()
	 * Liefert die persönliche Startseite des Nutzers zurück, sofern vorhanden.
	 *
	 * @param void
	 *
	 * @return string URL der persönlichen Startseite
	 */
	public function getUserStartPage () {
		
		if ($this->user_internalvars['cmt_startpage']) {
			return $this->user_internalvars['cmt_startpage'];
		} else {
			return $this->group_internalvars['cmt_startpage'];
		}
	}
	
	/**
	 * public function getUserStartApp()
	 * Liefert die ID der Startanwendung des Nutzers zurück.
	 *
	 * @param void
	 *
	 * @return number ID der Startseitenanwendung.
	 */	
	public function getUserStartApp () {

		if ($this->user_internalvars['cmt_startapp']) {
			return intval($this->user_internalvars['cmt_startapp']);
		} else {
			return intval($this->group_internalvars['cmt_startapp']);
		}	
	}

	/**
	 * public function getUserCMTTemplate()
	 * Liefert die Einstellung für den template-Stil des users zurück.
	 *
	 * @param void
	 * @return string Ordnerpfad zu den Templates des ausgewählten Stils (z.B. "default/")
	 */
	public function getUserCMTTemplate() {

		if ($this->user_internalvars['cmt_cmtstyle']) {
			$template = trim($this->user_internalvars['cmt_cmtstyle']);
		} else if ($this->group_internalvars['cmt_cmtstyle']) {
			$template = trim($this->group_internalvars['cmt_cmtstyle']);
		} else {
			$template = 'default/';
		}
		
		$pathToAdmin = $this->cmt->getPathToAdmin();
		return preg_replace('/' . preg_quote($pathToAdmin, '/') . '(\/)?(templates)?/', '', $template);
	}
	
	/**
	 * public function getAllUsers()
	 * Liefert alle Benutzerdaten zurück
	 *
	 * @param void
	 * 
	 * @return array Array mit allen Benutzerdaten
	 */
	public function getAllUsers() {
		
		$this->db->query("SELECT * FROM cmt_users ORDER BY cmt_usergroup ASC, cmt_username ASC");
		$users = $this->db->getAll();
		
		if (!is_array($users)) {
			return array();
		} else {
			return $users;
		}
	}

	public function getGroupUsers($groupID) {
		
		$groupID = intval($groupID);
		
		$this->db->query("
			SELECT * FROM cmt_users 
			WHERE cmt_usergroup = '" . $groupID . "' 
			ORDER BY cmt_username ASC"
		);
		$users = $this->db->getAll();

		if (!is_array($users)) {
			return array();
		} else {
			return $users;
		}
	}
	
	/**
	 * public function getAllUserGroups()
	 * Liefert alle Benutzergruppendaten zurück
	 *
	 * @param boolean $orderByName Falls Wert true ist, werden die Gruppen nach Namen geordnet ausgegeben, ansonsten nach id
	 * 
	 * @return array Array mit allen Benutzergruppendaten
	 */
	public function getAllUserGroups($orderByName=false) {
		
		$query = "SELECT * FROM cmt_users_groups ";
		
		if ($orderByName) {
			$query .= "ORDER BY cmt_groupname ASC";
		} else {
			$query .= "ORDER BY id ASC";
		}
		
		
		$this->db->query($query);
		$groups = $this->db->getAll();
		
		if (!is_array($groups)) {
			return array();
		} else {
			return $groups;
		}
	}
	
	/**
	 * public function getAllGroupsAndUsers()
	 * Liefert alle Gruppen- und Benutzerdaten strukturiert als assoziatives Array zurück
	 *
	 * @param void
	 * 
	 * @return array Array mit allen Gruppen- und Benutzerdaten
	 */
	public function getAllGroupsAndUsers() {
		
		$data = array();

		$this->db->query("SELECT g.*, g.id AS groupID FROM cmt_users_groups g ORDER BY g.id ASC");
		$groups = $this->db->getAll();
		
		foreach ($groups as $group) {
			$this->db->query("SELECT u.* FROM cmt_users u 
							  WHERE cmt_usergroup = '" . intval($group['id']) . "' 
							  ORDER BY u.cmt_username ASC");
			while ($r = $this->db->get()) {
			
				$data[$group['id']][] = array_merge($group, $r);
			}	
			
		}
		return $data;
	}

	public function getGroupRights ($groupID, $appID=0) {

		// Gruppenrechte holen
		$this->db->query("SELECT cmt_showitems FROM cmt_users_groups WHERE id = '" . intval($groupID) . "' LIMIT 1");
		$r = $this->db->get();
		
		$groupRights = unserialize(stripslashes($r['cmt_showitems']));
	
		if (is_array($groupRights)) {
			
			$appID = intval($appID);
			
			if ($appID) {
				$groupRights[$appID];
			} else {
				return $groupRights;
			}
		} else {
			return array();
		}
	}
	
	public function saveGroupRights($groupID, $groupRights) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$groupID = intval($groupID);

		if (!$groupID || !is_array($groupRights)) {
			return false;
		}
		
		$cleanedRights = array();
		
		foreach($groupRights as $appID => $appRights) {
			
			foreach($this->baseRights as $right) {
				
				if ($appRights[$right]) {
					$cleanedRights[$appID][$right] = 1;
				} 
			}
		}
		
		$check = $this->db->query("
			UPDATE cmt_users_groups 
			SET cmt_showitems = '" . $this->db->dbQuote(serialize($cleanedRights)) ."' 
			WHERE id = '" . $groupID . "'
		");
		
		if (!$check) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * public function updateUserRightsInGroup()
	 * Aktualisiert die Rechte der Benutzer innerhalb einer Gruppe, wenn die Gruppenrechte geändert wurden.
	 *
	 * @param number $groupID Datenbank-ID der Gruppe.
	 *
	 * @return boolean
	 */
	public function updateUserRightsInGroup ($groupID) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$groupID = intval($groupID);
		if (!$groupID) {
			return false;
		}
		
		$users = $this->getGroupUsers($groupID);
		$groupRights = $this->getGroupRights($groupID);

		foreach ($users as $user) {
			
			$userRights = $this->getAllUserRights($user['id']);
			
			if (!empty($userRights)) {

				$newUserRights = array_merge($this->arrayKeysToString($groupRights), $this->arrayKeysToString($userRights));
//				var_dump($newUserRights);
				$newUserRights = array_intersect_key($this->arrayKeysToInteger($newUserRights), $groupRights);
//				var_dump($newUserRights);
			} else {
				$newUserRights = $groupRights;
			}
			
			return $this->saveUserRights($user['id'], $newUserRights);
		}
		return true;
	}

	/**
	 * protected function arrayKeysToString()
	 * Hilfsfuktion: Wandelt die Schlüssel eines Arrays in Strings um, damit das Array anschließend mit array_merge und anderen, assoziativen Arrays richtig zusammengeführt werden kann (array_merge, kann das nur mit assoziativen, nicht mit numerischen Arrays). 
	 *
	 * @param array $array Array mit numerischen Schlüsseln
	 *
	 * @return array Gleiches Array mit den Schlüsseln als String (dazu muss leider ein Leerzeichen an den Schlüssel gehöngt werden.
	 */
	private function arrayKeysToString($array) {
		
		if (!is_array($array)) {
			$array = array();
		}
		$newArray = array();
		
		foreach ($array as $key => $value) {
			$newKey = strval($key);
			$newArray[$key." "] = $value;
		}
		return $newArray;
	
	
	}

	/**
	 * protected function arrayKeysToInteger()
	 * Gegenmethode zu arrayKeysToString: Wandelt String-Schlüssel zu numerischen um (nur sinnvoll, wenn zuvor arrayKeystoString() auf das Array angewendet wurde!)
	 *
	 * @param array $array Array mit in Strings umgewandelten Schlüsseln
	 *
	 * @return array Array mit numerischen Schlüsseln.
	 */
	private function arrayKeysToInteger($array) {
		
		if (!is_array($array)) {
			$array = array();
		}
		$newArray = array();
		
		foreach ($array as $key => $value) {
			$newArray[(integer)$key] = $value;
		}
		
		return $newArray;
	
	
	}
	
	/**
	 * public function saveAllUserRights()
	 * Speichert ALLE Zugriffsrechte eines Nutzers. Die Rechte müssen in einem assoziativen Array der Form array($appID => array('access' => 1, 'new => '1')) usw. übergeben werden.
	 *
	 * @param number $userID Datenbank-ID des Benutzers.
	 * @param array $userRights Mehrdimensionales Array mit den Rechten
	 *
	 * @return boolean
	 */
	public function saveUserRights($userID, $userRights) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$userID = intval($userID);
		if (!$userID) {
			return false;
		}
		
		$groupID = $this->getUserGroupID($userID);
		$groupRights = $this->getGroupRights($groupID);
		
		// Da nicht ausgewählte Checbox-Formularfelder nicht übermittelt werden, müssen deaktiverte Checkboxen anhand 
		// der Gruppenrechte ermittelt werden.
		foreach ($groupRights as $appID => $rights) {
			
			foreach ($rights as $rightName => $rightStatus) {
				
				if (!isset($userRights[$appID][$rightName])) {
					$userRights[$appID][$rightName] = 0;
				}
			}
		}
//var_dump($userRights);	
		$check = $this->db->query("UPDATE cmt_users SET cmt_showitems = '" . $this->db->dbQuote(serialize($userRights)) . "' WHERE id = '" . $userID . "' LIMIT 1");
		
		return !$check;
		
	}
	
	public function saveUserRightsForApp($appID, $userID, $newUserRights=array()) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$appID = intval($appID);
		$userID = intval($userID);
		
		if (!$userID || $appID) {
			return false;
		}
		
		$this->db->query("SELECT cmt_showitems FROM cmt_users WHERE id = '" . $userID . "' LIMIT 1");
		$userData = $this->db->get();
		
		$userRights = unserialize($userData['cmt_showitems']);
		
		if (!is_array($userRights)) {
			$userRights = array();
		}
		$userRights[$appID] = $newUserRights;
		$check = $this->db->query("UPDATE cmt_users SET cmt_showitems = '" . $this->db->dbQuote(serialize($userRights)) . "' WHERE id = '" . $userID . "' LIMIT 1");
		
		return !$check;
	}
	
	public function deleteUser($userID) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$userID = intval($userID);
		
		if (!$userID) {
			return false;
		}
		
		$check = $this->db->query("DELETE FROM cmt_users WHERE id = '" . $userID . "' LIMIT 1");
		
		if (!$check) {
			return true;
		} else {
			return false;
		}
	}
	
	public function deleteUserGroup($groupID) {

		if (!$this->userIsLoggedIn) {
			return false;
		}
		
		$groupID = intval($groupID);
		
		if (!$groupID) {
			return false;
		}
		
		$users = $this->getGroupUsers($groupID);
		foreach ($users as $user) {
			$this->deleteUser($user['id']);
		}
		
		$check = $this->db->query("DELETE FROM cmt_users_groups WHERE id = '" . $groupID . "' LIMIT 1");
		
		if (!$check) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getUserType() {
		return $this->user_type;
	}
	
	public function getUserAlias() {
		return $this->user_alias;
	}
	
	public function getUserName() {
		return $this->user_name;
	}
	
	public function getUserID() {
		return $this->user_id;
	}
	
	public function getLastLogin() {
		return $this->lastLogin;
	}
	
	public function getLastPasswordChange() {
		return $this->lastPasswordChange;
	}
	
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	/**
	 * public function getUserGroupID()
	 * Ermittelt die Gruppen-ID des eingeloggten Benutzers oder zu einer übergebenen Nutzer-ID.
	 *
	 * @param number $userID Optional: ID des Nutzers
	 *
	 * @return number ID der Gruppe
	 */
	public function getUserGroupID($userID=0) {
		
		$userID = intval($userID);
		
		if (!$userID) {
			return $this->group_id;	
		}
		
		$this->db->query("SELECT * FROM cmt_users WHERE id = '" . $userID . "' LIMIT 1");
		$r = $this->db->get();
		
		return intval($r['cmt_usergroup']);
	}

 	public function getBaseRights() {
 		return $this->baseRights;
 	}
 	
 	public function createPassword($params=array()) {
 		
 		$defaultParams = array(
 			'length' => 8,
 			'case' => 'mixed',
 			'chars' => 'all',
 			'type' => 'random',
 			'specialChars' => array()
 		);
 		$params = array_merge($defaultParams, $params);
 		
 		$conso = array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z");
 		$vocal = array("a","e","i","o","u");
 		$number = array( '1', '2', '3', '4', '5', '6', '7', '8', '9');
 		$char = array('_', '-', '&', '+', '=', '$', '!');
 		
 		$char = array_merge($char, $params['specialChars']);
 		
 		switch($params['chars']) {
 			
 			case 'number':
 				$sourceChars = array(
					$number
 				); 				
 				break;

			case 'chars':
				$sourceChars = array(
					$conso,
					$vocal
				);
				break;
 				
 			default:
 				$sourceChars = array(
 					$conso,
 					$vocal,
 					$number,
 					$char
 				);
 				break;
 		}
 		$password = "";
 		
 		srand ((double)microtime()*1000000);

 		switch ($params['type']) {

 			case 'mnemonic':
 				for ($c=1; $c <= intval($params['length']); $c++) {
 						
 					$password .= $conso[rand(0,count($conso)-1)];
 					$password .= $vocal[rand(0,count($vocal)-1)];
 				}

 				break;
 			
 			default:
		 		for ($c=1; $c <= intval($params['length']); $c++) {
		 			
		 			// don't show two following special chars.
		 			if ($type == 3) {
		 				$type = rand(0, count($sourceChars)-2);
		 			} else {
		 				$type = rand(0, count($sourceChars)-1);
		 			}

		 			$charPos = rand(0, count($sourceChars[$type])-1);
		 			$char = $sourceChars[$type][$charPos];
		 			
		 			switch ($params['case']) {
		 				
		 				case 'upper':
		 					$char = strtoupper($char);
		 					break;
		 					
		 				default:
	 						if (rand(0,1)) {
	 							$char = strtoupper($char);
	 						}
	 						break;
		 			}
		 			$password .= $char;
		 			
		 		}
				break;
				
 		}
 		return $password;
 	}
 	
 	public function changePassword($userId, $password) {
 		
 		$userId = (int)$userId;
 		$password = $this->encryptPassword(trim($password));
 		
 		$check = $this->db->query("
			UPDATE cmt_users 
			SET cmt_pass = '" . $password . "', 
			cmt_passchanged = '" . date('Y-m-d H:i:s') ."' 
			WHERE id = '" . $userId . "'"
 		);
 		
 		return !(bool)$check;
 		
 	}
 	
 	/**
 	 * public function checkUserPassword()
 	 * Check if username and password are correct
 	 * 
 	 * @param string $username	User name as used to login
 	 * @param string $password	Password (plain, not encrypted!)
 	 */
 	public function checkUserPassword($username, $password) {
 		
 		$query = "SELECT COUNT(id) AS userFound FROM cmt_users WHERE cmt_username = '" . $this->db->dbQuote(trim($username)) ."' AND cmt_pass = '" . $this->db->dbQuote($this->encryptPassword($password)) . "' LIMIT 1";
 		$this->db->query($query);
 		
 		$r = $this->db->get();
 		return (bool)$r['userFound'];
 	}
 	
 	/**
 	 * private function encryptPassword()
 	 * Encrypts a password
 	 * 
 	 * @param string $password	The password
 	 * @return string			The encrypted password
 	 */
 	private function encryptPassword($password) {
 		
 		return md5(trim($password));
 	}
}

?>