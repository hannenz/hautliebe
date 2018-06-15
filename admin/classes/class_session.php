<?php    
/**
 * class Session
 * 
 * Singleton SessionHandler: Provides methods to handle sessions and session variables.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2017-03-30
 */

namespace Contentomat;

class SessionHandler {

	private $sid;
	private $sessionVars;
	private $sessionVarsLoaded;
	private $sessionLifeTime = 3600;
	private $cookieOptionSecure = null;
	private $cookieOptionHttpOnly = true;
	private $cookieOptionDomain = '';
	private $db;
	
	private static $instance = NULL;

	/**
	 * public function getSession()
	 * Method returns an instance of the SessionHandler Singleton.
	 *
	 * @return object SessionHandler
	 */
	public static function getSession() {
	
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		
		// call "constructor" / initialisation method every time an instance is created
		self::$instance->initSession();
			
		return self::$instance;
	}
	

	/**
	 * private function __construct()
	 * Construtor is used to initialize object variables.
	 *
	 * @param string $check_loggedin
	 *
	 * @return return_type
	 */
	private function __construct($check_loggedin = false) {

		$this->sessionVars = array();
		$this->db = new DBCex();
		
		$this->cookieOptionSecure = $_SERVER["HTTPS"] ? true : false;

	}
	
	/**
	 * private function initSession()
	 * Alternative "constructor": Is called every time an instance of the singleton "SessionHandler" is created. Method checks and refreshes the session data in database.
	 * 
	 * @param void
	 * @return void
	 */
	private function initSession() {
		
		$time = time();

		$exptime = $time + $this->sessionLifeTime;
		$cookieExpTime = $this->sessionLifeTime + 100;

		// Aktive Sessions aufräumen
		$this->db->Query("DELETE FROM cmt_sessions WHERE cmt_exptime < '".$time."'");


		/*
		 * Session-ID prüfen.
		 * Reihenfolge wichtig!
		 * 1. SID defined: ein $session-Objekt wurde irgendwo in einem Skript auf der Seite erzeugt
		 * 2. SID per GET: im Adminbereich werden keine Cookies genutzt da Konflikte mit Frontend
		 * 3. SID per POST, s. 2.
		 * 4. SID als Cookie: Wir befinden uns auf einer Website-Seite (Frontend)
		 */
		if (defined('SID') || (CMT_USECOOKIES == '1' && isset ($_COOKIE['sid'])) || isset ($_GET['sid']) || isset ($_POST['sid'])) {

			if (defined('SID')) {
				$this->sid = SID;
			} else if (isset($_GET['sid'])) {
				$this->sid = trim($_GET['sid']);
			} else if (isset ($_POST['sid'])) {
				$this->sid = trim($_POST['sid']);
			} else if (CMT_USECOOKIES == '1' && isset($_COOKIE['sid'])) {
				$this->sid = trim($_COOKIE['sid']);
			}
			
			// Schutz vor SQL-Injection über sid-Parameter in Querystring
			$this->sid = $this->db->dbQuote(substr($this->sid, 0, 36));
			
			$this->db->Query("SELECT cmt_sessionid FROM cmt_sessions WHERE cmt_sessionid = '" . $this->sid . "' AND cmt_exptime >= '" . $time . "' LIMIT 1");
			$r = $this->db->get();

			if ($r['cmt_sessionid']) {
				$query = "UPDATE cmt_sessions SET cmt_exptime = '".$exptime."' WHERE cmt_sessionid = '" . $this->sid . "' LIMIT 1"; // AND cmt_exptime >= '$time'";
				$this->db->query($query);
				
				if (!$this->db->getLastErrorNr()) {
					$this->getAllSessionVars();
				} else {
					$this->sid = false;
				}
				
				// Cookie erneuern
				if (CMT_USECOOKIES == '1') {
					setcookie ('sid', '', time() - $cookieExpTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
					setcookie ('sid', $this->sid, time() + $this->sessionLifeTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
				}
			} else {
				// Cookie l�schen
				if (CMT_USECOOKIES == '1') {
					setcookie ('sid', '', time() - $cookieExpTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
				}
				// delete system cookies every time a session expires!
				$this->deleteSystemCookies();
				$this->sid = false;
			}
		} else {
			// Cookie löschen
			if (CMT_USECOOKIES == '1') {
				setcookie ('sid', '', time() - $cookieExpTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
			}
			// delete system cookies every time a session expires!
			$this->deleteSystemCookies();
			$this->sid = false;
		}

		/*
		 * Neue Session-ID vergeben falls nötig
		 */
		if (!$this->sid) {
			// Neue Session-ID
			$this->sid = $this->createSID();

			// Daten in "aktive Session" speichern
			$query = "INSERT INTO cmt_sessions SET cmt_sessionid = '".$this->db->dbQuote($this->sid)."', cmt_exptime = '".$exptime."'";
			$this->db->Query($query);
			

			// Session-ID als Cookie speichern
			if (CMT_USECOOKIES == '1') {
				setcookie ('sid', '', time() - $cookieExpTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
				setcookie ('sid', $this->sid, time() + $this->sessionLifeTime, '/', $this->cookieOptionDomain, $this->cookieOptionSecure, $this->cookieOptionHttpOnly);
			}
		}
		
	
		// Zum Schluss noch die Session-Variablen einlesen!
		$this->sessionVarsLoaded = false;

		// Globale Variablen $sid und $GLOBALS['SELFURL'] erstellen
		if (!defined('SID')) define ('SID', $this->sid);

		return;
	}

	/**
	 * public function checkIsLoggedIn()
	 * Checks if the current session ID ist marked as "logged in" (for backend administration)
	 *
	 * @param void
	 *
	 * @return boolean
	 */
	public function checkIsLoggedIn() {
		$this->db->Query("SELECT cmt_loggedin FROM cmt_sessions WHERE cmt_sessionid = '".$this->db->dbQuote($this->sid)."' LIMIT 1");
		$check = $this->db->Get(MYSQLI_NUM);
		if ($check[0]) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * private function createSID()
	 * Creates a unique session ID
	 *
	 * @return string A new SessionID as string.
	 */
	private function createSID() {
		return md5(uniqid(rand()));
	}

	/**
	 * public function getSessionID()
	 * Returns the session ID.
	 *
	 * @return string Session ID as string.
	 */
	public function getSessionID() {
		return $this->sid;
	}
	
	/**
	 * 
	 * public function checkSidCookie()
	 * Check if the session ID is available in a cookie (cookie variable name: 'sid')
	 *
	 * @return boolean
	 *
	 * @return boolean
	 */	
	public function checkSidCookie() {
		// Wenn keine Cookies benutzt werden sollen, dann sofort zur�ck mit false
//		if (CMT_USECOOKIES != '1') return false;
		
		// ... ansonsten checken ob Cookie da.
		if (!isset($_COOKIE['sid'])) {
			return false;
		} else {
			return true;
		} 
	}

	/**
	 * private function loadAllSessionVars()
	 * Loads all session variables in objects cache array. Method is usually called once at the first instanciation of the SessionHandler object.
	 *
	 * @return array Array with all stored session variables (or empty)
	 */
	private function loadAllSessionVars() {

		//$this->db = new DBCex();
		$this->db->Query("SELECT cmt_vars FROM cmt_sessions WHERE cmt_sessionid = '" . $this->db->dbQuote($this->sid) . "'");
		$r = $this->db->get();
		$this->sessionVars = Contentomat::safeUnserialize(stripslashes($r['cmt_vars']));
		
		if (!is_array($this->sessionVars)) {
			$this->sessionVars = array();
		}
		
		$this->db->close();
		$this->sessionVarsLoaded = true;
		
		return $this->sessionVars;
	}

	/**
	 * public function setSessionVar()
	 * Sets a session variable.
	 *
	 * @param string $name Name of the session variable
	 * @param mixed $value Values
	 *
	 * @return boolean
	 */
	public function setSessionVar($name, $value = null) {
		if (!$name) {
			return false;
		}
		
		$this->sessionVars[$name] = $value;
		return true;
	}

	/**
	 * public function setMultipleSessionVars()
	 * Sets more than one session variables.
	 *
	 * @param array $var Variables stored in an associative array (key (var name) => value (var value). 
	 *
	 * @return boolean
	 */
	public function setMultipleSessionVars($var) {
		
		if (!is_array($var)) {
			return false;
		}
		
		foreach ($var as $name => $value) {
			$this->sessionVars[$name] = $value;
		}
		return true;
	}

	/**
	 * public function deleteSessionVar()
	 * Deletes a session variable.
	 *
	 * @param string $name Name of the session variable
	 *
	 * @return boolean Returns false if no variable name is given as parameter.
	 */
	public function deleteSessionVar($name) {
		if (!$name) {
			return false;
		}
		unset ($this->sessionVars[$name]);
		return true;
	}

	/**
	 * public function deleteAllSessionVars()
	 * Deletes all session variables in objects internal cache array. The variables will be deleted finally when the script ends and the (empty) variable cache is saved in __destruct) method.
	 *
	 * @param void
	 * @return void
	 */
	public function deleteAllSessionVars() {
		unset ($this->sessionVars);
	}

	/**
	 * public function getSessionVar()
	 * Returns a session variable.
	 *
	 * @param string $name Name of the session variable
	 *
	 * @return mixed Value of the session variable
	 */
	public function getSessionVar($name) {
		return $this->sessionVars[$name];
	}

	/**
	 * public function getAllSessionVars()
	 * Gets all session variables, first time from the database, after that allways from the objects cache array. 
	 *
	 * @param boolean $forceReload Set to 'true' if the variables should be loaded from database abyway.
	 *
	 * @return array All session variables as associative array.
	 */
	public function getAllSessionVars($forceReload=false) {

		if (!$this->sessionVarsLoaded || $forceReload) {
			$this->loadAllSessionVars();
		}
		return $this->sessionVars;
	}

	/**
	 * public function saveSessionVars()
	 * Saves all session variables in the database. Method is called automatically in class method __destruct().
	 *
	 * @param void
	 *
	 * @return boolean
	 */
	public function saveSessionVars() {
		$serialized_vars = Contentomat::safeSerialize($this->sessionVars);
		$this->db = new DBCex();
//		$this->db->Query("LOCK TABLES cmt_sessions");
		$this->db->Query("UPDATE cmt_sessions SET cmt_vars = '".addslashes($serialized_vars)."' WHERE cmt_sessionid = '".$this->db->dbQuote($this->sid)."'");
//		$this->db->Query("UNLOCK TABLES cmt_sessions");
		$result = $this->db->last_ErrorNr();

		if ($result) {
			return false;
		} else {
			return true;
		}
		$this->db->Close();
	}
	
	/**
	 * public function getSID()
	 * Returns the curent session ID
	 *
	 * @param void
	 * @return string Session ID string.
	 */
	public function getSID() {
		return $this->sid;
	}
	
	// Session speichern bei __destruct()
	public function __destruct() {
		$this->saveSessionVars();
	}
	
	/**
	 * Deletes all cookies set by the CMT for the admin path
	 * 
	 * @param void
	 * @return void
	 *
	 */
	public function deleteSystemCookies() {
		
		$systemCookies = array(
			'cmtMenuPinned',
			'cmtNodeStatus',
			'cmtSystemMessagesShown',
			'pinnedElements',
			'currentElement'
		);
		
		
		foreach ($systemCookies as $cookieName) {
			setcookie($cookieName, null, -1, '/' . ADMINPATH);
		}
	}
}


/**
 * Workaround: Session is now a singleton, so the common instantion via "new Session()" is no more possible.
 * Therefor this workaround caches all old function calls to a Session object and forwards them to the singleton 
 * object named "SessionHandler".
 *  
 * @author Hahn
 *
 */
class Session {

	protected $sessionHandler;
	
	public function __construct() {
		$this->sessionHandler = SessionHandler::getSession();
		return $this->sessionHandler; 
	}
	
	/**
	 * public function __call($name, $params)
	 * Magic mehthod: wird aufgerufen, wenn von extern versucht wird eine Klassenmethode von "Session" aufzurufen.
	 *
	 * @param string $name Name der Klassenmethode, die aufgerufen wurde
	 * @param array $params Array mit allen Argumenten, die an die Klassenmethode übergeben wurden
	 *
	 * @return mixed Rückgabewert der mit $name angesprochenen Klassenmethode
	 */
	public function __call($name, $params) {

		// Leider keine bessere Lösung um eine variable Anzahl von Parametern an die funktionen zu übergeben.
		return $this->sessionHandler->$name($params[0], $params[1], $params[2], $params[3]);
	}
}
?>
