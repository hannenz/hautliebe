<?php 
namespace Contentomat;

/**
 * class_cmtpage.inc
 * Stellt Methode zur Darstellung einer Content-o-mat Websiteseite zur Verfügung
 * 
 * @author J.Hahn
 * @version 2016-09-21
 * 
 * TODO: Methoden schreiben: 1. Alle Kindelemente suchen, 2. Alle elemente auf einer Ebene
 */

class CmtPage {

	protected $db;
	protected $pageLevel;
	protected $pagesTable;
	protected $linksTable;
	protected $contentsTable;
	protected $pageID;
	protected $pageLang;
	protected $templateID;
	protected $cmt;
	protected $pageFileExtension;
	protected $session;

	protected $pagesTablePrefix = 'cmt_pages_';
	protected $contentsTablePrefix = 'cmt_content_';
	protected $linksTablePrefix = 'cmt_links_';
	
	public $pageDataCache;
	public $langCode;
	
	public function __construct() {

		$this->cmt = Contentomat::getContentomat();
		$this->db = new DBCex();
		
		$this->session = $this->cmt->session;
		
		$this->pageLevel = 1;
		$this->pageDataCache = array();
		
		$this->pageID = 0;
		$this->pageLang = '';
		$this->templateID = 0;
		
		// TODO 2.0: Diesen Pfad konfigurierbar machen, z.B. über cmt_setting.inc
		$this->pathTemplate = '/{PAGELANG}/{PAGEID}/';
		
		$this->initPage();
	}
	
	/**
	 * public function initPage()
	 * Backend-Methode: Ermittelt und erzeugt alle benötigten Variablen und Einstellungen einer Seite
	 */
	public function initPage() {
		
		// Seitendaten ermitteln
		$requestPageVars = $this->getRequestPageVars();
		if (!$this->pageID) {
			$this->pageID = $requestPageVars['pageID'];
		}
		if (!$this->pageLang) {
			$this->pageLang = $requestPageVars['pageLang'];
		}
		if (!$this->templateID) {
			$this->templateID = $requestPageVars['templateID'];
		}
		
		// Wurde eine Sprache gefunden?
		// TODO 2.0: hier auf Globale verzichten und Wert aus Klasse Settings nehmen 
		if (!$this->pageLang) {
			$this->pageLang = constant('DEFAULTLANGUAGE');
		}
		

		$pageTablesNames = $this->getPageTablesNames($this->pageLang);

		$this->pagesTable = $pageTablesNames['pagesTable'];

		$this->linksTable = $pageTablesNames['linksTable'];
		$this->contentsTable = $pageTablesNames['contentsTable'];
		
		// Existiert eine Seiten-ID oder muss die Startseite angezeigt werden?
		if (!$this->pageID) {
			$startPageData = $this->getStartPageData();
			$this->pageID = $startPageData['id'];
		}

		// TODO 2.0: Das aus der Settings-Klasse /$cmt holen
		$this->pageFileExtension = 'html';


	 	$lc = substr($this->pageLang, 0, 2);
	 	$this->langCode = $lc.'_'.strtoupper($lc);
		
		// TODO: 1. Nicht immer UTF8! 2. Prüfen, ob das überhaupt Sinn macht, z.B. wegen Sprachversionen wie 'fom' oder 'gorges'
		setlocale(LC_ALL, array($lc.'.UTF-8', $lc, $this->pageLang));
	}

	public function getPageToDisplay($pageID=0, $pageLang='') {
		
		// Seite initalisieren
		//$this->initPage();
				
		if ($pageID) {
			$this->pageID = intval($pageID);
		} else {
			$pageID = $this->pageID;
		}

		if ($pageLang && preg_match('/^[a-z0-9_-]+$/i', $pageLang)) {
			$this->pageLang = $pageLang;
		} else {
			$pageLang = $this->pageLang;
		}
		
		// Prüfen, ob die Seite unter der richtigen Domain auferufen wurde und leitet ggf. weiter
		$domainData = $this->checkDomain();

		if (!$domainData['domainCheck']) {
			$this->redirectToURL($domainData['redirectURL']);
		}

		// Prüft, ob die Seite sichtbar oder gesperrt ist und gibt ggf. ein "404 - not found" zurück
		$visibilityData = $this->checkVisibility();
		if ($visibilityData['status'] == 99) {
			$this->redirectToURL('', 404);
		}
		
		// Die nächstmögliche Seite zum Anzeigen ermitteln, z.B. wenn aufgerufene ID ein Ordner ist
		$showPageID = $this->getPageIDToShow();

		if ($showPageID != $pageID) {
			$this->redirectToPage($showPageID, $pageLang);
		}

		// Prüft, ob die Seite unter dem korrekten Seitennamen aufgerufen wurde und leitet ggf. weiter
//		$fileNameData = $this->checkFileName();

//		if (!$fileNameData['fileNameCheck']) {
//			$this->redirectToURL($fileNameData['redirectURL']);
//		}
		
		// Passwortschutz noch einbauen
		$protectionData = $this->checkProtection();

		if ($protectionData['isProtected'] && !$this->session->getSessionVar($protectionData['protectionVarName']) && !$this->cmt->isAdminMode) {
			// TODO: Macht es Sinn, hier ein "401 unauthorized" zu senden?
			
			$this->sendHeader('Cache-Control: no-cache, must-revalidate');
			$this->sendHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
			$this->redirectToURL($protectionData['redirectURL']);
		} 
		
		// Seitentyp feststellen und dann ggf. Aktion durchführen
		// Im Augenblick wird bei Seitentyp "link" weitergeleitet
		$pageTypeData = $this->checkPageType();
		if ($pageTypeData['pageType'] == 'link' && $pageTypeData['redirectURL']) {
			$this->redirectToURL($pageTypeData['redirectURL']);
		}
		
		// Tempalte holen und zurückgeben
		return $this->getPageTemplate();
	
	}
	
	/**
	 * protected function redirectToPage()
	 * Ermittelt die URL-Daten zu ID und Sprachkürzel einer Seite und leitet per 301 Header dorthin weiter.
	 * 
	 * @param number $pageID ID der neuen Seite
	 * @param string $pageLang Sprachkürzel der neuen Seite
	 */
	public function redirectToPage($pageID, $pageLang) {
		$newPageData = $this->getPageData($pageID, $pageLang);
		
		$newPageTitle = $this->makePageFileName($pageID, $pageLang);
		$newPagePath = $this->makePageFilePath($pageID, $pageLang);
	
		$this->redirectToURL($newPagePath.$newPageTitle);
  		exit();
	}
	
	/**
	 * public function getPageIDToShow()
	 * Ermittelt anhand ID und Sprachkürzel die nächstmögliche anzeigbare Seite. Dabei gelten folgende Regeln:
	 * # Ist ID ein sichtbarer Ordner und das erste Kindelement ebenfalls ein Ordner, wird in dem Kindordner weitergesuch
	 * # Ist ID ein sichtbarer Ordner und das erste Kindelement eine Seite, wird innerhalb des Ordner der Reihenfolge nach die erste sichtbare Seite angezeigt
	 * # Ist ID eine Seite (egal welcher Status!) wird ID zurückgegeben
	 * 
	 * @param number $pageID Optional: ID der Seite 
	 * @param string $pageLang Optional: Sprachkürzel
	 * @return number ID der anzuzeigenden Seite
	 */
	public function getPageIDToShow($pageID=0, $pageLang='') {

		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		$newPageID = $pageID;
		
		$pageData = $this->getPageData($pageID, $pageLang);
		
		// Ist das ein Ordner?
		if ($pageData['cmt_type'] == 'folder') {
			$childrenData = $this->getChildren($pageID, $pageLang);

			foreach ($childrenData as $pos => $dataArray) {
			
				// Eine Seite gefunden
				if ($dataArray['cmt_showinnav'] == 1 && $dataArray['cmt_type'] != 'folder') {
					$newPageID = $dataArray['id'];
					$this->pageID = $newPageID;
					return $newPageID;
				}
				
				// Einen Ordner gefunden
				if ($dataArray['cmt_showinnav'] == 1 && $dataArray['cmt_type'] == 'folder') {
					$newPageID = $this->getPageIDToShow($dataArray['id'], $pageLang);
					$this->pageID = $newPageID;
					return $newPageID;
				}
				
			}
		}
		
		return $newPageID;
	}

	/**
	 * public function getPageTemplate()
	 * Ermittelt das Template einer Seite.
	 * 
	 * @param number $pageID Optional: ID der Seite
	 * @param string $pageLang Optional: Sprachkürzel der Seite
	 * @param number $templateID optional: ID des Templates
	 * @return string Quelltext des Templates
	 */
	public function getPageTemplate($pageID=0, $pageLang='', $templateID=0) {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		if (!$templateID) {
			$templateID = $this->templateID;
		}
		
		if ($templateID) {
			$this->db->Query("SELECT * FROM cmt_templates_pages 
							  WHERE id = '".intval($templateID)."' 
							  LIMIT 1");
		} else {
			$pageData = $this->getPageData($pageID, $pageLang);
			
			$this->templateID = intval($pageData['cmt_template']);
			
			$this->db->Query("SELECT * FROM cmt_templates_pages 
							  WHERE id = '".$this->templateID."' 
							  LIMIT 1");
		}
		
		$r = $this->db->get();
		return stripslashes($r['cmt_source']);
	}
	/**
	 * public function getChildren()
	 * Ermittelt alle Kindelemente (1. Ebene) eines Eintrags
	 * 
	 * @param number $pageID Eintrags-ID
	 * @param string $pageLang Eintragssprache
	 * @return array Mehrdimensionales Array mit den Seitendaten der Kindelemente
	 */
	public function getChildren($pageID, $pageLang) {
		
		if (!$pageID) {
			$pageID = $this->pageID;
			$pagesTable = $this->pagesTable;
		}
		
		if (!$pageLang) {
			$pagesTable = $this->pagesTable;
		}  else {
	 		$pageTablesNames = $this->getPageTablesNames($pageLang); 
	 		$pagesTable = $pageTablesNames['pagesTable'];
	 	}

	 	$childrenData = array();
	 	
		$this->db->query("
			SELECT * FROM ".$this->db->dbQuote($pagesTable)." 
			WHERE cmt_parentid = '".$this->db->dbQuote($pageID)."' 
			ORDER BY cmt_pagepos ASC");
		
		while ($r = $this->db->get()) {
			$childrenData[$r['cmt_pagepos']] = $r;
			$this->pageData[$pageLang][$r['id']] = $r;
		}
		
		return $childrenData;
		
	}
	
	/**
	 * public function getRequestPageVars()
	 * Methode liest alle relevanten Seitenparameter aus der URL, z.B. pid (Seiten-ID), lang (Sprachversion), etc.
	 * 
	 * @param void
	 * @return array Array mit den Seitenparametern
	 */
	public function getRequestPageVars() {
		
		$requestVars = array (
			'pid' => '',
			'lang' => '',
			'tid' =>''
		);
		$returnVars = array();
		
		foreach ($requestVars as $key => $value) {
			$returnVars[$key] = trim(urldecode($_REQUEST[$key]));
		}
		
		// Variablen nachbearbeiten
		$returnVars['pageID'] = intval($returnVars['pid']);
		$returnVars['templateID'] = intval($returnVars['tid']);
		
		$returnVars['pageLang'] = preg_replace('/^[^a-z0-9_-]+$/i', '', $returnVars['lang']);
		
		return $returnVars;
	}
	
 	/**
	 * public function getLevel()
	 * 
	 * Ermittelt die Tiefe (Level) einer Seite
	 * 
	 * @param pageID string ID der Seite, deren Ebene gesucht werden soll
	 * 
	 * @return number Ebene der Seite
	 * 
	 */
	public function getLevel($pageID=0, $lang='') {

		if (!$lang) {
	 		if ($this->pagesTable) {
	 	 		$pagesTable = $this->pagesTable;
	 		} else {
	 			return array();
	 		}
	 	} else {
	 		$pagesTable = $this->makePageTableName($lang);
	 	}

	 	$pageID = intval($pageID);

	 	$pageData = $this->getPageData($pageID, $lang);

	 	if (empty($pageData)) return 0;
	 	
		if ($pageData['cmt_parentid'] == 'root') {
			return $this->pageLevel;
		}
		
		$this->pageLevel++;
		$this->getLevel($pageData['cmt_parentid'], $lang);

		return $this->pageLevel;
	}

	/**
	 * public function getAncestors()
	 * Ermittelt alle übergeordneten Elemente (Vater, Großvater, etc) einer Seite.
	 * 
	 * @param number $pageID ID der Seite
	 * @param string $lang Optional: Name der Sprachversion
	 * 
	 * @return array Liefert alle übergeordneten Einträge in einem multidimensionalen Array: array[Ebenennummer] => array(Seitendaten)
	 */
	public function getAncestors($pageID=0, $lang='') {
		$pageLevel = $this->pageLevel;
		$ancestorsData = array();
		
		while ($pageData['cmt_parentid'] != 'root') {
			$pageData = $this->getPageData($pageID, $lang);
			$ancestorsData[$pageLevel--] = $pageData;
			$pageID = $pageData['cmt_parentid'];
		}

		return $ancestorsData;
	}
	
	/**
	 * public function getPageData()
	 * 
	 * Erwartet Parameter als Number (pageId) oder Array (array ('pageId' => Wert)). Liefert den Datenbanktabelleneintrag / die Seiteneigenschaften zur gewünschten Seite
	 * 
	 * @param number $pageID string ID der Seite, deren Daten zurückgegeben werden sollen
	 * @param string $lang Optional: Sprachversion der Seite
	 *
	 * @return array Array, welches Seitendaten enthält (Schlüssel => Feldname, Wert => Feldwert) oder leer ist
	 * 
	 */
	public function getPageData($pageID=0, $pageLang='') {

		if (!$pageID) {
			$pageID = $this->pageID;
			$pageLang = $this->pageLang;
			$pagesTable = $this->pagesTable;
			
		}
		
		if (!$pageLang) {
	 		
			if ($this->pageLang) {
	 	 		$pagesTable = $this->pagesTable;
	 	 		$pageLang = $this->pageLang;
	 		} else {
	 			return array();
	 		}
	 	} else {
	 		$pageTablesNames = $this->getPageTablesNames($pageLang); 
	 		$pagesTable = $pageTablesNames['pagesTable'];
	 	}

	 	// Bereits im Cache?
	 	if (is_array($this->pageDataCache[$pageLang][$pageID])) {
	 		return $this->pageDataCache[$pageLang][$pageID];
	 	}
	
		$this->db->query("SELECT * FROM ".$this->db->dbQuote($pagesTable)." WHERE id = '".$this->db->dbQuote($pageID)."'");
		$pageData = $this->db->get();

		$pageData['cmtPageLevel'] = $this->pageLevel;
		
		$this->pageDataCache[$pageLang][$pageID] = $pageData;

		return $pageData;
	}

	public function checkProtection($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$pageData = $this->getPageData($pageID, $pageLang);
		$result = array(
			'isProtected' => $pageData['cmt_protected'],
			'redirectURL' => '',
			'protectionVarName' => ''
		);

		if ($pageData['cmt_protected']) {
			$result['redirectURL'] = $this->makePageFilePath($pageData['cmt_protected_loginpage'], $pageLang) . 
									 $this->makePageFileName($pageData['cmt_protected_loginpage'], $pageLang) .
									 '?refpid='.intval($pageID);
			
			$qs = preg_replace('/pid=\d*|lang=[a-z0-9]*|cmtURI=[^&]*/i', '', $_SERVER['QUERY_STRING']);
			$result['redirectURL'] .= '&'.$qs;
				
			$result['redirectURL'] = preg_replace('/&{2,}/U', '&', $result['redirectURL']);
			$result['redirectURL'] = preg_replace('/&$/', '', $result['redirectURL']);
									 
			$result['protectionVarName'] = $pageData['cmt_protected_var'];
		}

		return $result;

	} 
	
	/**
	 * public function checkDomain()
	 * Prüft, ob die aktuelle Seite unter der richtigen Domain aufgerufen wird.
	 * 
	 * @param number $pageID ID der zu prüfenden Seite
	 * @param string $lang Sprachversion der zu prüfenden Seite
	 * @return array Array mit folgenden Schlüssel-Wert-Paaren: 'domainCheck' (true/false), 'hasOwnDomain' (page/lang), 'redirectURL (leer/URL zum Weiterleiten)
	 */
	public function checkDomain($pageID=0, $lang='') {

		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$requestDomain = getenv('HTTP_HOST');
		$requestURI = getenv('REQUEST_URI');

		$pageData = $this->getPageData($pageID, $lang);
		$result = array(
			'domainCheck' => true,
			'redirectURL' => '',
			'hasOwnDomain' => false
		);
		$redirectURL = '';

		// 1. Hat Seite eigene Domain?
		if ($pageData['cmt_domain_id']) {
			
			$pageDomain = $this->getPageDomain($pageData['cmt_domain_id']);

			if ($pageDomain) {
				
				$result['hasOwnDomain'] = 'page';
				
				if ($pageDomain != $requestDomain) {
							
					$redirectURL = $pageDomain.$requestURI;
					if (!preg_match('/^[a-z]+:\/\//i', $redirectURL)) {
						$redirectURL = 'http://'.$redirectURL;
					}
				}
			}
		} 
		// 2. Hat Sprachversion eigene Domain
		else {
			$languageDomain = $this->getLanguageDomain($lang);

			if ($languageDomain) {
				
				$result['hasOwnDomain'] = 'lang';
				if ($languageDomain != $requestDomain) {
			

					$redirectURL = $languageDomain.$requestURI;
					if (!preg_match('/^[a-z]+:\/\//i', $redirectURL)) {
						$redirectURL = 'http://'.$redirectURL;
					}
				}
			}			
		}
		
		if ($redirectURL) {
			$result = array(
				'domainCheck' => false,
				'redirectURL' => $redirectURL
			);
		}
		
		return $result;
	}

	/**
	 * public function redirectToURL()
	 * Führt einen Redirect per Header auf die übergene URL aus, es sollte immer auch ein Status-Code (Nr) übergeben werden.
	 * 
	 * @param string $redirectURL URL auf die weitergeleitet werden soll.
	 * @param number $redirectNr Nr des HTTP Status-Codes (aktuell werden unterstützt: 300, 301, 304, 307, 401, 402, 403, 404)
	 * @return void
	 */
	public function redirectToURL($redirectURL='', $redirectNr=301) {
		
		// Siehe auch: <http://www.google.com/support/webmasters/bin/answer.py?hl=de&answer=40132>
		$redirectTypes = array (
			300 => 'HTTP/1.1 300 Multiple Choices',
			301 => 'HTTP/1.1 301 Moved Permanently',
			304 => 'HTTP/1.1 304 Not Modified',
			307 => 'HTTP/1.1 307 Temporary Redirect',
			401 => 'HTTP/1.1 401 Unauthorized',
			402 => 'HTTP/1.1 402 Payment Required',
			403 => 'HTTP/1.1 403 Forbidden',
			404 => 'HTTP/1.1 404 Not Found'
		);

		if ($redirectTypes[$redirectNr]) {
		//	echo $redirectTypes[$redirectNr];
			header ($redirectTypes[$redirectNr]);
		}
		if ($redirectURL) {
			header ('Location: '.$redirectURL);
		}
		exit();
	}
	
	/**
	 * public function sendHeader()
	 * Sendet einen Header.
	 *
	 * @param string $header Der Text des Headers
	 *
	 * @return boolean
	 */
	public function sendHeader($header='') {
	
		$header = trim($header);
	
		if (!$header) {
			return false;
		}
	
		header($header);
		return true;
	}
	
	/**
	 * public function checkVisibility()
	 * Prüft die eingestellte Sichtbarkeit einer Seite (DB-Feld 'cmt_showinnav') und gibt ein Array mit Ergebnissen zurück.
	 * 
	 * @param number $pageID Optional: ID der Seite
	 * @param string $pageLang Optional: Sprachversion der Seite
	 * @return array Array mit folgenden Schlüssel-/Wert-Paaren: 'isVisible' (true/false), 'status' (0,1,99: interner 
	 * Content-o-mat Wert für Sichtbarkeit), 'redirectURL' (optionale URL für Umleitungen, wird noch nicht genutzt), 'redirectType' 
	 * (Umleitungs-Typ)
	 */
	public function checkVisibility($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$result = array(
			'isVisible' => false,
			'status' => 0,
			'redirectURL' => '',
			'redirectType' => 0
		);
		
		$pageData = $this->getPageData($pageID, $pageLang);
		
		switch($pageData['cmt_showinnav']) {
			
			// Wird in Navigation nicht angezeigt, kann aber per direkter URL aufgerufen werden
			case 0:
				// Default-Werte werden übergeben
				break;

			// Seite mit normaler Sichtbarkeit
			case 1:
				$result['isVisible'] = true;
				$result['status'] = 1;

				break;
			
			// Gesperrte Seite
			case 99:
				$result['isVisible'] = false;
				$result['status'] = 99;
				$result['redirectType'] = 404;
				break;
		}
		
		return $result;
	}

	/**
	 * protected function checkPageType()
	 * Prüft den Seiten-/Eintragstyp und führt ggf eine Aktion durch (derzeit nur für Seitentyp "Link" 301-Weiterleitung zu Linkziel).
	 * 
	 * @param number $pageID Optional: ID der Seite
	 * @param string $pageLang Optional: Sprachversion der Seite
	 * @return void
	 */
	protected function checkPageType($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$pageData = $this->getPageData($pageID, $pageLang);
		$result = array(
			'pageType' => $pageData['cmt_type'],
			'redirectURL' => $pageData['cmt_link']
		);
		
//		switch ($pageData['cmt_type']) {
//			
//			// Links werden weitergeleitet
//			case 'link':
//				
//				if ($pageData['cmt_link']) {
//					header("HTTP/1.1 301 Moved Permanently"); 
//					header('Location: '.$pageData['cmt_link']);
//					exit();
//				}
//				break;
//		}
		
		return;
	}
	
	/**
	 * public function checkFileName()
	 * Ermittelt, ob der Dateiname dem in der eingegebenen URL entspricht und gibt im Fehlerfall den 
	 * richtigen Dateinamen und -pfad zurück.
	 * 
	 * @param number $pageID ID der Seite (optional)
	 * @param string $pageLang Sprachkürzel, optional
	 * @return array Array mit folgenden Schlüssel-/Wertpaaren: 'fileNameCheck' (true/false), 'redirectURL' (leer oder Pfad zur richtigen Seite)
	 */
	public function checkFileName($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		// TODO 2.0: Keine Globale! Seitennamen nur überprüfen, wenn URL-Umschreibung mit mod_rewrite eingeschaltet ist
		if (!CMT_MODREWERITE) {
			return true;
		}

		$result = array(
			'fileNameCheck' => true,
			'redirectURL' => ''
		);
		
		//Dateinamen auswerten:
		// $match[1] = Dateiname, z.B. "customer.html"
		// $match[2] = Zeichen ? oder #
		// $match[3] = restlicher Querystring
		preg_match('/([^?#]*)(\?|#)?(.*)/', $_SERVER['REQUEST_URI'], $match);
		$fileName = basename($match[1]);
		$requestParams = $match[2].$match[3];

		// Seitennamen ermitteln
		$pageData = $this->getPageData($pageID, $pageLang);

		if ($pageData['cmt_urlalias']) {
			$pageTitle = $pageData['cmt_urlalias'];
		} else {
			$pageTitle = $pageData['cmt_title'];
		}
		$pageName = $this->cmt->makeNameWebSave($pageTitle).'.'.$this->pageFileExtension;

		// Falls der aufgerufene Dateiname nicht mit dem Seitennamen übereinstimmt, wird 
		// per 301 Header weitergeleitet.
		if ($pageName != $fileName) {
			
			$redirectFileName = $pageName.$requestParams;
			$redirectPath = $this->makePageFilePath($pageID, $pageLang);

			$result = array(
				'fileNameCheck' => false,
				'redirectURL' => $redirectPath.$redirectFileName
			);
		}
		
		return $result;

	}

	public function makePageFilePath($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$path = str_replace('{PAGEID}', $pageID, $this->pathTemplate);
		$path = str_replace('{PAGELANG}', $pageLang, $path);
		return $path;
	}
	
	/**
	 * public function makePageFileName()
	 * Erzeugt aus den Daten einer Seite einen Dateinamen zur Verwendung in einer URL.
	 * 
	 * @param number $pageID Optional: ID der Seite
	 * @param string $pageLang Optional: Sprachkürzel der Seite
	 * @return string Dateiname, z.B. "Startseite.html"
	 */
	public function makePageFileName($pageID=0, $pageLang='') {
		
		if (!$pageID) {
			$pageID = $this->pageID;
		}
		
		if (!$pageLang) {
			$pageLang = $this->pageLang;
		}
		
		$pageData = $this->getPageData($pageID, $pageLang);
		if ($pageData['cmt_urlalias']) {
			$fileName = $pageData['cmt_urlalias'];
		} else {
			$fileName = $pageData['cmt_title'];
		}
		
		$fileName = $this->cmt->makeNameWebSave($fileName).'.'.$this->pageFileExtension;
		
		return $fileName;
	}
	/**
	 * public function getStartPageData()
	 * Ermittelt die Daten der als Startseite markierten Seite
	 * 
	 * @param string $lang Optional: Name (Kürzel) der Sprachversion
	 * @return array Liefert alle Daten der Startseite zurück
	 */
	public function getStartPageData($pageLang='') {
		
		if (!$pageLang) {
	 		if ($this->pagesTable) {
	 	 		$pagesTable = $this->pagesTable;
	 	 		$pageLang = $this->pageLang;
	 		} else {
	 			return array();
	 		}
	 	} else {
	 		$pageTablesNames = $this->getPageTablesNames($pageLang); 
	 		$pagesTable = $pageTablesNames['pagesTable'];
	 	}
		$this->db->query("SELECT * FROM ".$this->db->dbQuote($this->pagesTable)." WHERE cmt_isroot = '1'");
		$pageData = $this->db->get();
		
		$this->pageDataCache[$pageLang][$pageData['id']] = $pageData;
		
		return $pageData;
	}

	
	public function getPageDomain($domainID=0)	{
		
		$domainID = intval($domainID);
		if (!$domainID) return '';
	 	
	 	$this->db->query("SELECT * FROM cmt_domains WHERE id = '".$domainID."'");
		$domainData = $this->db->get();
		
		return $domainData['cmt_domain'];
	}

	public function getLanguageDomain($lang='')	{
		
		$lang = trim($lang);
		if (!$lang) {
			$lang = $this->pageLang;
		}
		
		if (!$lang) {
			return '';
		}
	 	
	 	$this->db->query("
	 		SELECT cl.cmt_domain_id, cd.cmt_domain AS cmt_domain 
	 		FROM cmt_content_languages cl 
	 		JOIN cmt_domains cd 
	 		ON cd.id = cl.cmt_domain_id
	 		WHERE cl.cmt_language = '".$this->db->dbQuote($lang)."'"
	 	);
		$languageData = $this->db->get();
		
		return $languageData['cmt_domain'];
	}
	
	public function setPageID($pageID) {
		$this->pageID = intval($pageID);
	}
	
	public function setPageLanguage($pageLanguage) {
		$this->pageLang = trim(str_replace(array(' ', ';'), '', ($pageLanguage)));
	}
	
	/**
	 * public function getPageTablesNames()
	 * Erzeugt aus dem Sprachkürzel die Namen der benötigten Content-Tabellen einer Seite
	 * 
	 * @param string $lang Sprachversionskürzel, wird dieses nicht übergeben, wird das von der Klasse ermittelte Kürzel benutzt (im Frontend meist das richtige)
	 * @return boolean Im erfolgsfall wird ein Array mit den Schlüüseln 'pageTable', 'linksTable' und 'contentTabel' mit den korrespondierenden Tabellennamen zurückgegeben.
	 */
	public function getPageTablesNames($lang='') {

		if (!$lang) {
			$lang = $this->pageLang;
		}
		
		if (preg_match('/^[a-z0-9_-]+$/i', $lang)) {
			
			return array(
				'pagesTable' => $this->pagesTablePrefix . $lang,
				'linksTable' => $this->linksTablePrefix . $lang,
				'contentsTable' => $this->contentsTablePrefix . $lang
			);
		} else {
			return array();
		}
	}

	public function makePageTableName($lang) {
	
		$tableNames = $this->getPageTablesNames($lang);
	
		return $tableNames['pagesTable'];
	}
	
	/**
	 * DEPRECATED: public function getPageTable()
	 */
	public function getPageTable() {
		return $this->getPagesTable();
	}

	/**
	 * public function getPagesTable()
	 * Returns the currently stored CMT pages table name, e.g. cmt_pages_en
	 * 
	 * @param void
	 * @return string
	 */
	public function getPagesTable() {
		return $this->pagesTable;
	}

	/**
	 * public function setPagesTable()
	 * Sets the internal name for the pages database table
	 *
	 * @param string $language The language shortcut/ name e.g. 'en'
	 * @return boolean
	 */
	public function setPagesTable($language) {

		if (!$this->checkLanguageName($language)) {
			return false;
		}
	
		$this->pagesTable = $this->pagesTablePrefix . $language;
		
// 		var_dump('set: ' . $this->pageTable);
		return true;
	}

	/**
	 * DEPRECATED: public function getContentTable()
	 */
	public function getContentTable() {
		return $this->getContentsTable();
	}

	/**
	 * public function getContentsTable()
	 * Returns the content table name.
	 * 
	 * @param void
	 * @return string
	 */
	public function getContentsTable() {
		return $this->contentsTable;
	}
	
	/**
	 * public function setContentsTable()
	 * Sets the internal name for the contents database table
	 * 
	 * @param string $language The language shortcut/ name e.g. 'en'
	 * @return boolean
	 */
	public function setContentsTable($language) {
		
		if (!$this->checkLanguageName($language)) {
			return false;
		}
		
		$this->contentsTable = $this->contentsTablePrefix . $language;
		return true;
	}
	
	/**
	 * DEPRECATED: public function getLinkTable()
	 */
	public function getLinkTable() {
		return $this->getLinksTable();
	}
	
	public function getLinksTable() {
		return $this->linksTable;
	}
	
	/**
	 * public function setLinksTable()
	 * Sets the internal name for the pages database table
	 *
	 * @param string $language The language shortcut/ name e.g. 'en'
	 * @return boolean
	 */	
	public function setLinksTable($language) {
	
		if (!$this->checkLanguageName($language)) {
			return false;
		}
	
		$this->linksTable = $this->linksTablePrefix . $language;
		return true;
	}
		
	public function getPageID() {
		return $this->pageID;
	}
	
	/**
	 * public function getParentID()
	 * Returns the parent page's id of a given page.
	 * 
	 * @param number $pageId Page id
	 * @param string $language Optional parameter: language shortcut, default is the current language shortcut
	 * @return mixed A number or the string 'root'
	 */
	public function getParentID($pageId=0, $language='') {
		
		$pageId = intval($pageId);
		
		if (!$language) {
			$language = $this->pageLang;
		} else if (!$this->checkLanguageName($language)) {
			return 0;
		}
		
		if (!$pageId) {
			$pageId = $this->pageID;
		}
		
		$this->db->query("SELECT cmt_parentid FROM " . $this->pagesTablePrefix . $this->db->dbQuote($language) . " WHERE id = '" . $pageId . "' LIMIT 1");
		$r = $this->db->get();
		return $r['id'];
	}
	
	/**
	 * public function getPageLang()
	 * Returns the current language shortcut
	 * 
	 * @param void
	 * @return string
	 */
	public function getPageLang() {
		return $this->pageLang;
	}
	
	public function getTemplateID() {
		return $this->templateID;
	}
	
	/**
	 * public function checkLanguageName()
	 * Checkes the syntax of a language name (only chars a-z, _- and numbers are allowed, no spaces or other whitespace characters! 
	 * 
	 * @param string $language The language name/ shortcut
	 * @return boolean
	 */
	public function checkLanguageName($language) {
		
		if (!$language || !preg_match('/^[a-z0-9_-]+$/i', $language)) {
			return false;
		}
		
		return true;
	}
}
?>