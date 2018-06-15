<?php
/**
 * Application Launcher
 * 
 * Grundgerüst des CMS: Startet Applikationen
 * 
 * Der "Application Launcher" startet eingebundene Anwendungen und zeigt Tabellen an. Er erstellt auch s�mtliche
 * im CMS verfügbaren Konstanten und Variablen und bindet die Klassen ein.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2016-10-04
 */

	namespace Contentomat;
	
	use Contentomat\SystemMessage\SystemMessage;
	
	require ("cmt_constants.inc");
	require ('classes/class_psrautoloader.inc');
	$autoloader = new PsrAutoloader();
	$autoloader->addNamespace('', 'classes/');
	$autoloader->addNamespace('Contentomat', 'classes/');
	$autoloader->addNamespace('Contentomat\SystemMessage', 'classes/');

	$cmt = Contentomat::getContentomat();
    //$time_start = getMicrotime();


Class ApplicationLauncher {
	
	protected $db;
	protected $parser;
	protected $user;
	protected $tableName;
	protected $applicationHandler;

	protected $applicationID;
	protected $applicationData;	
	protected $lastApplicationID;
	protected $referingApplicationID;
	protected $navigationApplicationID;
	protected $tabNr;

	protected $launchApplicationFileName;
	protected $defaultApplicationPath;
	protected $templatePath;
	protected $debugMode = false;
	
	
	public function __construct() {

		// protect content-o-mat pages from reload from the browser's history and back button reload after the user loged out.
		if (!defined('CMT_NO_CACHE') || (defined('CMT_NO_CACHE') && CMT_NO_CACHE == true)) {
			header("Cache-Control: no-store, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // expired date!
		}
		
		$this->cmt = Contentomat::getContentomat();
		$this->session = $this->cmt->getSession();
//		$this->session = new Session(); // $this->cmt->getSession();
		$this->db = new DBCex();
		$this->parser = new CMTParser();
		$this->user = new User($this->session->getSessionID());
		$this->applicationHandler = new ApplicationHandler();
		
		// First check if user is logged in!
		if (!$this->session->checkIsLoggedIn()) {
			$this->cmt->userNotLoggedIn();
		}
		
		// init vars: start
		$this->defaultApplicationPath = 'app_showtable/app_showtable.php';
		$this->tableName = trim($_REQUEST['cmtTable']);
		if (!$this->tableName) {
			$this->tableName = trim($_REQUEST['cmt_dbtable']);	// war: $app_table
		}
		
		if ($this->debugMode){
			$this->debug = new Debug();
		}
		
		// Current Content-o-mat page ID
		$this->applicationID = intval(trim($_REQUEST['cmtLaunchApplicationID']));  // 2.0: rename to 'cmtApplicationID'
		if (!$this->applicationID) {
			$this->applicationID = intval(trim($_REQUEST['launch']));
		}
		
		if (!$this->applicationID) {
			$this->applicationID = intval(trim($_REQUEST['cmtApplicationID']));
		}
		if (!$this->applicationID) {
			$this->applicationID = $this->user->getUserStartApp();
		}
		
		// return to application id
		$this->returnToApplicationID = intval($_REQUEST['cmtReturnToApplicationID']); // 2.0: rename to 'cmtReturnToApplicationID'
		if (!$this->returnToApplicationID) {
			$this->returnToApplicationID = intval($_REQUEST['cmt_returnto']);
		}
		
		// is there an external app to handle like the fileselector?
		switch($_REQUEST['cmt_extapp']) {
			
			case 'fileselector':
				$this->launchApplicationFileName = 'appinc_fileselector.php';	// war: $app_launch_file
				break;
				
			// outdated
			case 'relations':
				$this->launchApplicationFileName = 'appinc_relationselector.php';
				break;
			
			default:
				$this->launchApplicationFileName = '';
				break;
		}
		
		// $app_returnto = $this->returnToApplicationID;
		
		// get application data
		if ($this->returnToApplicationID && $this->tableName) {
			$this->applicationData = $this->applicationHandler->getApplicationByTablename($this->tableName);
			$this->applicationID = $this->applicationData['id'];
			
			// show the right application in navigation
			$this->navigationApplicationID = $this->returnToApplicationID;

		} else {
			$this->applicationData = $this->applicationHandler->getApplication($this->applicationID);
			
			$this->navigationApplicationID = $this->applicationID;
		}

		// get application vars stored in session
		$applicationVars = $this->session->getSessionVar('cmtApplicationVars');
//var_dump($applicationVars);
		if (!is_array($applicationVars)) {
			$applicationVars = array($this->applicationID => array());
		} else if (!is_array($applicationVars[$this->applicationID])) {
			$applicationVars[$this->applicationID] = array();
		}
		
		// is there a application file to launch?
		if (!$this->launchApplicationFileName) {
			$this->launchApplicationFileName = $this->applicationData['cmt_include'];
		}
		
		if (!$this->launchApplicationFileName) {
			$this->launchApplicationFileName = $this->defaultApplicationPath;
		}
		
		// Get tabnumber (for compatibility reasons also check 'cmt_slider')
		$this->tabNr = $_REQUEST['cmtTab'];
		if (!$this->tabNr) {
			$this->tabNr = intval($_REQUEST['cmt_slider']);
		}
		if (!$this->tabNr) {
			if (isset($applicationVars[$this->applicationID]['cmtTab'])) {
				$this->tabNr = intval($applicationVars[$this->applicationID]['cmtTab']);
			}
		}
		if (!$this->tabNr) {
			$this->tabNr = 1;
		}
		
		$applicationVars[$this->applicationID]['cmtTab'] = $this->tabNr;
		
		// get settings
// TODO!
		$this->provideVar('cmt_settings', $this->applicationData['cmt_tablesettings']);
		
		// get last applications id
		$this->lastApplicationID = intval($this->session->getSessionVar('cmtLastApplicationID'));
		$this->referingApplicationID = intval($this->session->getSessionVar('cmtReferingApplicationID'));
		
		if ($this->applicationID != $this->lastApplicationID) {
			$this->session->setSessionVar('cmtReferingApplicationID', $this->lastApplicationID);
			$this->referingApplicationID = $this->lastApplicationID;
		}
		$this->session->setSessionVar('cmtLastApplicationID', $this->applicationID);

	
		/* Eigentlich müssten die '&' mittels htmlentities() in &amp; umgewandelt werden, damit der Querystring
		 * XHTML konform ist. Allerdings kann dann die Konstante nicht mehr in Javascript-Anweisungen im
		* HTML-Quelltext verwendet werden!
		*/
		if ($this->tabNr) {
			$addTabNr = '&cmt_slider='.$this->tabNr;
		}

		if ($this->returnToApplicationID && !$this->launchApplicationFileName) {

			$this->provideVar('SELFURL', 
				SELF . 
				'?sid=' . $this->session->getSessionID() . 
				'&cmtApplicationID='.$This->applicationID.
				'&cmtReturnToApplicationID=' . $this->returnToApplicationID .
				$addTabNr, true);
		} else {
			$this->provideVar('SELFURL', 
				SELF .
				'?sid=' . $this->session->getSessionID() . 
				'&cmtApplicationID=' . $this->applicationID . 
				$addTabNr, true);
		}

		$this->provideVar('CMT_APPID', $this->applicationID, true);

		// Falls keine Datei da, Tabellenansicht starten
// 		if (!$this->launchApplicationFileName){
// 			$this->launchApplicationFileName = $this->defaultApplicationPath;
// 		}

		/* VARIABLEN hier: ----------------------
		
		$app_launch_file : Datei, die geladen wird
		$app_vars : Array, alle Variablen, die in cmt_addvars gespeichert sind
		$app_id : ID der geladenen Applikation in 'cmt_tables',
		entspricht $launch, wenn Tabelle nicht von anderer Applikation aufgerufen wurde,
		ansonsten entspricht $app_id $cmt_returnto
		
		$cmt_uservars : alle gespeicherten Variablen aus der User-Klasse
		$cmt_username : Name des Users
		$cmt_userid : ID des Users
		
		$cmt_dbtable : Name der Datenbanktabelle, sofern in cmt_tables_eingetragen
		$cmt_fields : Mehrdimensionales Array mit MySQL-Feldinformationen der aktuellen Tabelle
		$cmt_fieldnames : Feldernamen der ausgesuchten Tabelle
		$cmt_fieldaliases : alle vorhandenen Feldaliase
		$cmt_fieldsformatted : alle Felder -> Kombination aus Aliasen (Werte) und Feledernamen (Schl�ssel),
		falls nicht zu allen Feldern Aliase existieren, sind Schl�ssel und Wert der
		selbe (Feld-)Namen
		$cmt_fieldtypes : Art des Feldes (interne Content-o-mat Bezeichnung)
		$cmt_fieldindex : Ist feld Index? 1 = ja, 0 = nein
		-----------------------------------------*/
		
		/* KONSTANTEN hier: -------------------------
		CMT_SLIDER: Nummer/ Name des aktiven Reiters
		CMT_TAB entspricht CMT_SLIDER
		CMT_RETURNTO: ID der Anwendung, zu der nach der Datenbankbearbeitung zur�ckgekehrt werden soll
		CMT_APPID: eigene Anwendungs ID
		CMT_APPFILE: eigener Anwendungsname
		SELFURL: eigene URL im Stil: cmt_applauncher?sid=sjhdwuqhiuwe&launch=2, hier also mit zus�tzlicher Get-Variable "launch"!
		CHARSET : Zeichensatz der Homepage, z.B. "iso-8859-1"
		WEBROOT : Root-Verzeichnis der Homepage relativ zu www.meinedomain.de, z.B "meinweb/" (ROOT+WEBROOT)
		PATHTOWEBROOT : Pfad von der aktuellen Position zum WEBROOT, also z.B. "../meinweb/"
		PATHTOADMIN : Pfad zum Adminverzeichnis, z.B. "admin/" oder "meinweb/admin/"
		ROOT : Pfadangabe zum ROOT in der Form "../../";
		SID : Session-ID
		SELF : entspricht $PHP_SELF
		WEBNAME : Name des Webs, wie er auch im Kopfframe angezeigt wird
		CMT_APPLAUNCHER: ... ist true. Damit kann zur Sicherheit gecheckt werden , ob die Applikation innerhalb des AppLaunchers l�uft.
		-----------------------------------------*/
		
		// application data
		$this->provideVar('applicationID', $this->applicationID);
		$this->provideVar('applicationData', $this->applicationData);
		$this->provideVar('applicationName', $this->applicationData['cmt_showname']);
		$this->provideVar('applicationSettings', $this->applicationData['cmt_tablesettings']);
		$this->provideVar('cmtTab', $this->tabNr);
		
		// application icon
		if (isset($this->applicationData['cmt_tablesettings']['icon'])) {
			$this->provideVar('applicationIcon', $this->applicationData['cmt_tablesettings']['icon']);
		}
		
		// "constants" => outdated, don't need to be stored in cmt
		$this->provideVar('CMT_REFERINGPAGE', $this->referingApplicationID, true);
		$this->provideVar('CMT_SLIDER', $this->tabNr, true);
		$this->provideVar('CMT_TAB', $this->tabNr, true);
		$this->provideVar('CMT_APPLAUNCHER' , true, true);		
		$this->provideVar('CMT_APPFILE', $this->launchApplicationFileName, true);
		$this->provideVar('CMT_APPID', $this->applicationID, true);
		$this->provideVar('CMT_NAVAPPID', $this->navigationApplicationID, true);

		$this->provideVar('CMT_DBTABLE', $this->applicationData['cmt_tablename'], true);
		$this->provideVar('CMT_RETURNTO' , $this->returnToApplicationID, true);
		
		// at least check user's right to access the application
		$checkUser = $this->user->checkUserAccessRight($this->applicationID);
	
		if (!$checkUser) {
			$this->content =  $this->parser->parse($this->parser->getTemplate('administration/access_violation.tpl'));
			$this->startOutput();
			$this->exitAppLauncher();
		}
		
		// save the application vars in session
		$this->session->setSessionVar('cmtApplicationVars', $applicationVars);
$this->session->saveSessionVars();
		$this->launchApplication();
		$this->startOutput();
		
	}
	
	/**
	 * Gets the navigation (HTML) from navigation generator class and stores it in class var '$navigation'
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function getNavigation() {
		$navigation = new AdministrationNavigation();
		$this->navigation = $navigation->createNavigation();
	}
	
	/**
	 * Get the system messages for the current user and provide them in a template.
	 * 
	 * @param void
	 * @return array	The systemmessages for current user as array
	 *
	 */
	protected function getSystemMessages() {
		
		$systemMessage = new SystemMessage();
		return $systemMessage->getSystemMessages();
		
	}
	
	protected function launchApplication() {
		
		$evalCode = new EvalCode();
		if (file_exists($this->cmt->getPathToWebRoot() . $this->launchApplicationFileName)) {
		
			// Neue Variante: Dateipfad wird direkt gespeichert
			$includeFile = $this->cmt->getPathToWebRoot() . $this->launchApplicationFileName;
		} else {
		
			// alte Variante: Nur Datei-/ Pfadname im Ordner 'admin/applications/' wird gespeichert.
			$includeFile = 'applications/' . $this->launchApplicationFileName;
		}
		
		$this->content = $evalCode->evalFile($includeFile, array(
			'cmt_settings' => $this->applicationData['cmt_tablesettings'],
			'session' => $this->cmt->getSession(),		// OUTDATED!
			'user' => $this->user,
			'cmt_slider' => $this->tabNr
		));

	}
	
	protected function startOutput() {

		$systemMessages = $this->getSystemMessages();
		
		$this->parser->setParserVar('cmtContent', $this->content);
		$this->parser->setMultipleParserVars(array(
			'cmtVersion' => $this->cmt->getVersion(),
			'cmtUserName' => $this->user->getUserName(),
			'cmtUserType' => $this->user->getUserType(),
			'cmtSystemMessages' => $systemMessages,
			'cmtHasSystemMessages' => !empty($systemMessages)
		));
	
		$convertAction = array(
			0 => 'overview_frame',
			'edit' => 'edit_entry',
			'new' => 'edit_entry',
			'duplicate' => 'edit_entry',
			'view' => 'edit_entry'
		);

		$this->getNavigation();
		$this->parser->setParserVar('cmtNavigation', $this->navigation);

		if (!isset($this->applicationData['cmt_templates']['dont_use_templates'])) {
			$this->applicationData['cmt_templates']['dont_use_templates'] = '';
		}
		if ($this->cmt->useTemplate() && (!$this->applicationData['cmt_templates']['dont_use_templates'] && !$cmt_templates[$convert_action[$cmt_action]])) {

			$this->page = $this->parser->parseTemplate('administration/cmt_applauncher.tpl');
		} else {
			$this->page = $this->content;
		}

		$this->page = $this->parser->unprotectMakros($this->page);
				
		$this->ob_start_gzipped();
		echo $this->page;
		ob_end_flush();
		$this->exitApplauncher();
	}
	
	protected function provideVar($name, $value, $constant = false) {
		$this->cmt->setVar($name, $value);
		
		if ($constant && !defined($name)) {
			define($name, $value);
		}
	}
	
	/**
	 * Start buffer output gzipped if possible otherwise start normal buffer output
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function ob_start_gzipped() {
		
return;		$encode = getenv('HTTP_ACCEPT_ENCODING');
		
		if (stristr($encode, 'gzip')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}
	}
	
	protected function exitAppLauncher() {
		$this->session->__destruct();
		exit();
	}
}
$launchApplication = new ApplicationLauncher();
?>