<?php
	/**
	 * class_contentomat.inc
	 * Singleton-Klasse stellt Methoden und Variablen zur Verfügung, die global genutzt werden können
	 * 
	 * Aufruf: $cmt = Contentomat::getContentomat();
	 * 
	 * @package content-o-mat
	 * @author J.Hahn, J.Braun <info@contentomat.de>
	 * @version 2018-03-27
	 * 
	 */
	namespace Contentomat;	

	class Contentomat {
		
		protected static $instance = NULL;	// Für erweiterbare Singletons darf $instance nur "protected" sein (nicht "private"!)
		
		public $version = '1.13';
		public $build = '2018-03-27';
		
		public $applicationID;
		
		public $session;
		public $user;
		public $isAdminMode;
		protected $useTemplate = true;
		
		protected $SELF;					// ehemals: Konstanten (alle groß)
		protected $PATHTOWEBROOT;
		
		protected $settings;				// Objekt: enthält die Einstellungsdaten
		protected $vars = array();
		protected $globals = array();		// TODO: wird das genutzt?
		
		protected $autoload = null;

 		/**
		 * protected function __construct()
		 * Kontruktor, kann nicht direkt aufgerufen werden, da Singleton-Klasse. 
		 * Referenzierung stattdessen mit $cmt = Contentomat::getContentomat();
		 * Methode ist nur "protected", damit Klasse ableitbar bleibt!
		 * 
		 *  @param void;
		 *  @return void;
		 */
		protected function __construct() {

			// Error Reporting: default ist "aus"
			$this->setErrorReporting(defined('CMT_ERRORREPORTING_LEVEL') ? CMT_ERRORREPORTING_LEVEL : '');
			
			// Session-Objekt
			// $this->session = SessionHandler::getSessionHandler();
			$this->session = new Session();
			
			// User-Objekt
			//$this->user = new User();

			// TODO für 2.0:Einstellungen holen
//			$this->settings = new CmtSettings();

			// TODO für 2.0: Auf true setzen, falls in Backend angemeldet
			$this->isAdminMode = false;
			
			// "Konstanten"
			$phpSelf = $_SERVER['PHP_SELF'];

			if (!$phpSelf) {
				$phpSelf = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);
			}
			
			if (!$phpSelf) {
				$phpSelf = PHP_SELF; 
			}
			// TODO für 2.0: WEBROOT in class_settings.inc
			$phpSelf = preg_replace('/^'.preg_quote(WEBROOT, '/').'/', '', $phpSelf);
			
			$this->globals = array();

			$this->globals['SELF'] = basename($phpSelf);
			$this->globals['PATHTOWEBROOT'] = $this->calculateRootPath(dirname($phpSelf));
			$this->globals['PATHTOADMIN'] = $this->globals['PATHTOWEBROOT'] . ADMINPATH;
			$this->globals['INCLUDEPATH'] = INCLUDEPATH;
			$this->globals['INCLUDEPATHTOADMIN'] = INCLUDEPATHTOADMIN;
			$this->globals['DOWNLOADPATH'] = DOWNLOADPATH;

			// ID der aktuellen Anwendung ermitteln (wir in Query-Var 'launch' übergeben
			// TODO: Das sollte in neue Klasse ContentomatApplication, die diese hier erweitert
			$this->applicationID = intval($_REQUEST['launch']);
			
			// Braucht diese Klasse einen "User"???
			//$this->user = new User($this->session->getSessionID());
			
			// Zeitzone für date-Funktion setzen (benötigt ab PHP 5.4)
			$this->setDefaultTimezone();
		}
		
		/**
		 * public static function getContentomat()
		 * Liefert Instanz der Contentomat-Klasse.
		 * 
		 *  @param void;
		 *  @return object Referenz auf existierendes oder neues Shop-Objekt (Singleton);
		 */
		public static function getContentomat() {
	
			if (self::$instance === NULL) {
				self::$instance = new self;
				
				// Optional: zusätzlicher Konstruktor zum initialisieren falls Veerbung
				//self::$instance->initContentomat();
			}
			
			return self::$instance;
		}

		/**
		 * private function __clone()
		 * Klonen per 'clone()' von außen verbieten.
		 * 
		 * @param void
		 * @return void
		 */ 
		private function __clone() {}

		
		/**
		 * private function __destruct()
		 * Desktruktor - ist halt so.
		 * 
		 * @param void
		 * @return void
		 */ 
		public function __destruct() {}


		/**
		 * protected function initContentomat();
		 * Zweiter Konstruktor, der von der erbenden Klasse überschrieben werden kann und bei
		 * einer Neuinstanzierung dieser Klasse ausgeführt wird.
		 * 
		 * @param void Erwartet keine Parameter
		 * 
		 * @return void
		 */

		protected function initContentomat() {

		}

		/**
		 * public function setDefaultTimezone()
		 * 
		 * @param string $timezone Optional: String für die Zeitzone, s. http://de3.php.net/manual/de/timezones.php
		 * @return boolean
		 */
		public function setDefaultTimezone($timezone='') {
			
			/*
			if (!$timezone) {
				$timezone = $this->getSetting('defaultTimezone');
			}
			*/
			// TODO: Konstante entfernen, wenn Settings-Mechanismus implementiert 
			if (!$timezone && defined('CMT_DEFAULTTIMEZONE')) {
				$timezone = CMT_DEFAULTTIMEZONE;
			}
			
			if ($timezone) {
				return date_default_timezone_set($timezone);
			} else {
				return false;
			}
			
		}
		
			public function setErrorReporting($type='') {
			
			switch($type) {
				
				case 'all':
					error_reporting(E_ALL);
					break;
					
				case 'warning':
					error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
					break;
					
				case 'strict':
					error_reporting(E_ALL & ~E_NOTICE);
					break;
					
				case 'error':
					error_reporting(E_ERROR);
					break;
					
				default:
					error_reporting(0);
					break;
			}
		}
		
		/**
		 * public function calculateRootPath()
		 * Berechnet den relativen Pfad zum Stammverzeichnis
		 * 
		 * @param string $path Dateipfad
		 * @return string Relativer Pfad zum Stammverzeichnis, z.B. '../../'
		 */
		public function calculateRootPath($path) {
			
			// führendes "/" entfernen
			$path = preg_replace('/^\//', '', $path);
				
			// "/" anhängen und ggf. säubern
			$path = $this->cleanPath($path.'/');
				
			// Pfadtiefe ermitteln und Pfad zurückgeben
			$depth = substr_count ($path, '/');
			return str_repeat("../", $depth);
		}
		
		/**
		 * public function cleanPath()
		 * Säuber einen Dateipfad von Mehrfachen "/" hintereinander
		 * 
		 * @param string $path Dateipfad
		 * @return string gesäuberter Dateipfad
		 */
		public function cleanPath($path) {
			return preg_replace('/\/{2,}/', '/', $path);
		}


		/**
		 * public function formatPath()
		 * Cleans up a path string: replaces double slashes ("//"), replaces leading "./" and adds a slash at the end of the directory path

		 * @param string $path
		 * @return string cleaned path
		 */
		public static function formatPath($path) {
			$path = preg_replace ("/^\.\//", "", $path);
			$path = preg_replace ("/\/{2,}/", "/", $path);
			if (!preg_match("/\/$/", $path)) {
				$path .= "/";
			}
			$path = preg_replace ("/^\//", "", $path);
			return $path;
		}
		
		/**
		 * public function getSession()
		 * GETTER: Gibt die Session-Instanz zurück
		 *
		 * @param void
		 * @return object Session-Instanz
		 */
		public function getSession() {
			return $this->session;
		}
		
		/**
		 * public function getSetting()
		 * GETTER: Gibt eine CMS-Einstellungsvariable zurück
		 * 
		 * @param string $settingName Name der CMS-Einstellungsvariable
		 * @return mixed Wert der angeforderten Variable
		 */
		public function getSetting($settingName) {
			return $this->settings->getSetting($settingName);
		}
		
		/**
		 * OUTDATED?
		 * public function getGlobal()
		 * Gibt eine "globale" Variable/ Konstante zurück
		 * 
		 * @param string $globalName Name der Globalen
		 * @return mixed Wert der Globalen
		 */
		public function getGlobal($globalName) {
			
			if (isset($this->globals[$globalName])) {
				return $this->globals[$globalName];
			} else {
				return null;
			}
		}
		
		/**
		 * OUTDATED?
		 * public function setGlobal()
		 * Setzt eine "globale" Variable, sofern sie nicht schon definiert ist
		 * 
		 * @param string $globalName Name der Globalen
		 * @param mixed $globalValue Wert der Globalen
		 * @return boolean True oder false, je nachdem, ob die "Globale" schon existierte (false) oder definiert werden konnte (true)
		 */
		public function setGlobal($globalName, $globalValue) {
			
			if (!array_key_exists($globalName, $this->globals)) {
				$this->globals[$globalName] = $globalValue;
				//echo $this->globals[$globalName].' => '.$globalValue;
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * public function safeSerialize();
		 * Serialisiert eine Array und ersetzt die kritischen Zeichen '"', ';' und '\' durch Platzhalter.
		 * 
		 * @param mixed $var Variable, die serialisert werden soll
		 * 
		 * @return string Serialiserte Repräsentation/ Zeichenkette des Wertes
		 */
		public static function safeSerialize($var) {
return serialize($var);
			$search = array('"', ';', '\\');
			$replace = array('__quot__', '__scol__', '__bksl__');
			
			if (is_array($var)) {
				foreach ($var as $key=>$value) {
					if (is_array($value)) {
						//$var[$key] = $this->safeSerialize($value);
						$var[$key] = Contentomat::safeSerialize($value);
					} else {
						$var[$key] = str_replace($search, $replace, $value);
					}
				}
			} else {
				$var = str_replace($search, $replace, $var);
			}
			return serialize($var);
		}

		/**
		 * public function safeUnserialize()
		 * Wandelt eine mit der Methode safeSerialize erzeugte Zeichenkette in die ursprüngliche variable zurück.
		 * 
		 * @param string $var Mit safeSerialize erzeugte Zeichenkettenrepräsentation der Variable
		 * @return mixed Entserialiserte variable.
		 */
		public static function safeUnserialize ($var) {

// Old serialization method changed 2018-03-26
if (!is_string($var)) {
	return $var;
} else {
	return unserialize($var);
}
			
			if (!is_string($var)) {
				return $var;
			}
			$var = unserialize($var);
				

			if (is_array($var)) {
				//$var = $this->safeUnserializeArray($var);
				$var = Contentomat::safeUnserializeArray($var);
			} else {
				//$var = $this->safeUnserializeCleanUp($var);
				$var = Contentomat::safeUnserializeCleanUp($var);
			}
			return $var;
		}

		/**
		 * public function safeUnserializeCleanUp()
		 * Hilfsmethode: Ersetzt alle Ersetzungszeichen in einer mit safeSerialize bearbeiteten Zeichenkette durch deren echte Werte.
		 * 
		 * @param string $var Zeichenkette
		 * @return string Zeichenkette mit ersetzten Zeichen
		 */
		public static function safeUnserializeCleanUp($var) {

// Old serialization method changed 2018-03-26
return $var;			
			$search = array('__quot__', '__scol__', '__bksl__');
			$replace = array('"', ';', '\\');
			
			return str_replace($search, $replace, $var);
		}

		/**
		 * public function safeUnserializeArray()
		 * Hilfsmethode: Stellt alle kritischen zeichen in einem Array aus serialisierten Zeichenketten wieder her
		 * 
		 * @param array $array Array aus serialiserten Zeichenketten.
		 * @return array Array mit entserialiserten Werrten
		 */
		public static function safeUnserializeArray($array) {

// Old serialization method changed 2018-03-26
return safeUnserialize($array);			

			foreach ($array as $key=>$value) {
				
				if (is_array ($value)) {
					// $array[$key] = $this->safeUnserializeArray($value);
					$array[$key] = Contentomat::safeUnserializeArray($value);
				} else {
					// unserialize gibt false zurück, wenn es nichts zu unserialisierne gibt, nicht den übergebenen Wert!
					$tmpValue = unserialize($value);
	
					if (is_array($tmpValue)) {
					//	$array[$key] = $this->safeUnserializeArray($tmpValue);
						$array[$key] = Contentomat::safeUnserializeArray($tmpValue);
					} else if ($tmpValue) {
						//$array[$key] = $this->safeUnserializeCleanUp($tmpValue);
						$array[$key] = Contentomat::safeUnserializeCleanUp($tmpValue);
					} else {
						//$array[$key] = $this->safeUnserializeCleanUp($value);
						$array[$key] = Contentomat::safeUnserializeCleanUp($value);
					}
				}
			}
			return $array;
		}
		
		

		/**
		 * public function makeNameWebsave()
		 * Wandelt Sonderzeichen in einer Zeichenkette zur Verwendung in einer URL um. ACHTUNG: Auch Kommas werden entfernt, obwohl sie eigentlich in URLs gültig sind!
		 * 
		 * @param string $name Zeichenkette, die in einer URL genutzt werden soll.
		 * @return string Zeichenkette, in welcher Sonderzeichen ersetzt worden sind
		 * 
		 */
		/**
		 * public function makeNameWebsave()
		 * Wandelt Sonderzeichen in einer Zeichenkette zur Verwendung in einer URL um. ACHTUNG: Auch Kommas werden entfernt, obwohl sie eigentlich in URLs gültig sind!
		 *
		 * @param string $name Zeichenkette, die in einer URL genutzt werden soll.
		 * @return string Zeichenkette, in welcher Sonderzeichen ersetzt worden sind
		 *
		 */
		public function makeNameWebsave($name) {
		
			/*
			 * From CakePHP Utility::Inflector
			 * http://api.cakephp.org/2.0/source-class-Inflector.html#534-554
			 */
		
			$_transliteration = array(
				'/ä|æ|ǽ/' => 'ae',
				'/ö|œ/' => 'oe',
				'/ü/' => 'ue',
				'/Ä/' => 'Ae',
				'/Ü/' => 'Ue',
				'/Ö/' => 'Oe',
				'/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
				'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
				'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
				'/ç|ć|ĉ|ċ|č/' => 'c',
				'/Ð|Ď|Đ/' => 'D',
				'/ð|ď|đ/' => 'd',
				'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
				'/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
				'/Ĝ|Ğ|Ġ|Ģ/' => 'G',
				'/ĝ|ğ|ġ|ģ/' => 'g',
				'/Ĥ|Ħ/' => 'H',
				'/ĥ|ħ/' => 'h',
				'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
				'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
				'/Ĵ/' => 'J',
				'/ĵ/' => 'j',
				'/Ķ/' => 'K',
				'/ķ/' => 'k',
				'/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
				'/ĺ|ļ|ľ|ŀ|ł/' => 'l',
				'/Ñ|Ń|Ņ|Ň/' => 'N',
				'/ñ|ń|ņ|ň|ŉ/' => 'n',
				'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
				'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
				'/Ŕ|Ŗ|Ř/' => 'R',
				'/ŕ|ŗ|ř/' => 'r',
				'/Ś|Ŝ|Ş|Š/' => 'S',
				'/ś|ŝ|ş|š|ſ/' => 's',
				'/Ţ|Ť|Ŧ/' => 'T',
				'/ţ|ť|ŧ/' => 't',
				'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
				'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
				'/Ý|Ÿ|Ŷ/' => 'Y',
				'/ý|ÿ|ŷ/' => 'y',
				'/Ŵ/' => 'W',
				'/ŵ/' => 'w',
				'/Ź|Ż|Ž/' => 'Z',
				'/ź|ż|ž/' => 'z',
				'/Æ|Ǽ/' => 'AE',
				'/ß/' => 'ss',
				'/Ĳ/' => 'IJ',
				'/ĳ/' => 'ij',
				'/Œ/' => 'OE',
				'/ƒ/' => 'f',
			);
		
			$replacement = '-';
			$quotedReplacement = preg_quote($replacement, '/');
		
			$merge = array(
				'/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
				'/\\s+/' => $replacement,
				sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => ''
			);
		
			$map = $_transliteration + $merge;
			$newName = preg_replace(array_keys($map), array_values($map), $name);
		
			return preg_replace('/-{2,}/', '-', trim($newName));
		}
/*
		public function makeNameWebsave($name) {
			
			// Siehe auch http://www.ascii-code.com/
			$substituteChars = array(
				32 => '-',	// Leerzeichen
				33 => '',	// !
				34 => '',	// "
				35 => '',	// #
				36 => '',	// $
				37 => '',	// %
				38 => '-',	// &
				39 => '',	// '
				40 => '-',	// (
				41 => '-',	// )
				42 => '-',	// *
				43 => '-',	// +
				44 => '-',  // ,
				47 => '-',	// /
				46 => '-',	// .
				58 => '-',  // :
				60 => '-',  // >
				62 => '-',  // <
				63 => '',	// ?
				64 => '-',	// @
				91 => '-',  // [
				93 => '-',  // ]
//				95 => '-',	// _
				167 => '',  // §
				169 => '', // ©
				171 => '',	// «
				174 => '', // ®
				180 => '', // ´
				187 => '',	// »
				192 => 'A',	// A with grave (franz.)
				193 => 'A',	// A with acute (franz.)
				194 => 'A',	// A with Circumflex (franz.)
				195 => 'A',	// A mit Tilde (franz.)
				196 => 'AE',	// A mit Umlaut (dt.)
				197 => 'A',	// A mit Kreis (skand.)
				198 => 'AE',	// A mit E (franz.)
				199 => 'C', // C mit Hakenb (franz.)
				200 => 'E', // E with grave (franz.)
				201 => 'E', // E with acute (franz.)
				202 => 'E', // E with circumflex (franz.)
				203 => 'E', // E mit Doppelpunkt (franz.)
				204 => 'I', // I with grave (franz.)
				205 => 'I', // I with acute (franz.)
				206 => 'I', // I with circumflex (franz.)
				207 => 'I', // I mit Doppelpunkt (franz.)
				208 => 'D', // D mit Strich (?)
				209 => 'N', // N mit Tilde (?)
				210 => 'O', // O with grave (franz.)
				211 => 'O', // O with acute (franz.)
				212 => 'O', // O with circumflex (franz.)
				213 => 'O', // O mit Tilde (franz.)
				214 => 'OE', // O mit Umlaut (dt.)
				216 => 'O', // O durchgestrichen (skand.)
				217 => 'U', // U with grave (franz.)
				218 => 'U', // U with acute (franz.)
				219 => 'U', // U with circumflex (franz.)
				220 => 'Ue', //chr(85).chr(69) // U mit Umlaut (dt.)
				223 => 'ss', // scharfes S (dt.)
				224 => 'a', // a with grave (franz.)
				225 => 'a', // a with acute (franz.)
				226 => 'a', // a with circumflex (franz.)
				227 => 'a', // a mit Tilde (franz.)
				228 => 'ae', // a mit Umlaut (dt.)
				229 => 'a', // a mit Kreis (skand.)
				230 => 'ae', // a mit e (franz.)
				231 => 'c', // c mit Haken (franz.)
				232 => 'e', // e with grave (franz.)
				233 => 'e', // e with acute (franz.)
				234 => 'e', // e with circumflex (franz.)
				235 => 'e', // e mit Umlaut (franz.)
				236 => 'i', // i with grave (franz.)
				237 => 'i', // i with acute (franz.)
				238 => 'i', // i with circumflex (franz.)
				239 => 'i', // i mit Umlaut (franz.)
				241 => 'n', // n mit Tilde (?)
				242 => 'o', // o with grave (franz.)
				243 => 'o', // o with acute (franz.)
				244 => 'o', // o with circumflex (franz.)
				245 => 'o', // o mit Tilde (franz.)
				246 => 'oe', // o mit Umlaut (franz.)
				249 => 'u', // u with grave (franz.)
				250 => 'u', // u with acute (franz.)
				251 => 'u', // u with circumflex (franz.)
				252 => 'ue', // u mit Umlaut (dt.)
				253 => 'y', // y with acute (franz.)
				255 => 'y', // y mit Umlaut (franz.)
			);

			if (mb_detect_encoding($name) == 'UTF-8' && mb_check_encoding($name, 'UTF-8')) {
				$name = utf8_decode($name);
			//	echo mb_strlen($name).' - '.mb_strlen(utf8_decode($name));
			}

			$newName = '';
			$nameLength = mb_strlen($name);
			
			for ($c = 0; $c < $nameLength; $c++) {
				$char = mb_substr($name, $c, 1);
				$charNumber = ord($char);

				// Zeichen zwischen ASCII 32 und 126 (normale Zeichen)
				if ($charNumber > 31 || $charNumber < 123) {
					
					// Zeichen im Array zum Wechseln vorhanden?
					if (in_array($charNumber, array_keys($substituteChars))) {
						$newChar = $substituteChars[$charNumber];
					} else {
						$newChar = $char;
					}
				// Zeichen über 123 (event. Sonderzeichen wie Umlaute)
				} else if (in_array($charNumber, array_keys($substituteChars))) {
						$newChar = $substituteChars[$charNumber];
				// Falls Zeichen nicht in Austausch-Array, dann entfernen
				} else {
					$newChar = '';
				}
				$newName .= $newChar;
			}
			
			return preg_replace('/-{2,}/', '-', trim($newName));
		}
*/
		/**
		 * public function getSettingVar()
		 * Liefert eine gewünschte Einstellungsvariable
		 * 
		 * @param string $settingName Name der Einstellungsvariablen
		 * @return mixed Wert der Einstellungsvariablen
		 */
		public function getSettingVar($settingName='') {
			return $this->settings[$settingName];
		}

		/**
		 * public function setSettingVar()
		 * Ändert eine gewünschte Einstellungsvariable
		 * 
		 * @param string $settingName Name der Einstellungsvariablen
		 * @param mixed $settingValue Wert der Einstellungsvariable, welcher gesetzt werden soll
		 * @return void
		 */
		public function setSettingVar($settingName='', $settingValue) {
			$this->settings[$settingName] = $settingValue;
		}

		/**
		 * public function setVar()
		 * Method stores a variable in the object's $var array.
		 *
		 * @param string $varName Name of the variable
		 * @param mixed $varValue Variable's value
		 *
		 * @return boolean 
		 */
		public function setVar($varName = '', $varValue) {
			
			if (!$varName) {
				return false;
			}

			$this->vars[$varName] = $varValue;
			return true;
		}
		
		public function setVars($vars) {

			if (!is_array($vars)) {
				return false; 
			}
			
			$this->vars = array_merge($this->vars, $vars);
			return true;
		}

		/**
		 *
		 * public function getVar()
		 * Gets a previousely in the singleton object's array $var stored variable value.
		 *
		 * @param string $varName Name of the stored variable
		 *
		 * @return mixed If the var exists its value will be returned. Otherwise the method returns null.
		 */
		public function getVar($varName = '') {
		
			if (!$varName) {
				return null;
			}
		
			return $this->vars[$varName];
		}
		
		public function getVars() {
			
			if (!is_array($this->vars)) {
				$this->vars = array();
			}
			return $this->vars;
		}

		public function deleteVars() {
			$this->vars = array();
			return true;
		}

		public function deleteVar($name) {
		
			unset($this->vars[$name]);
		}
		
		public function isVar($name) {
		
			if (isset($this->vars[$name])) {
				return true;
			} else {
				return false;
			}		
		}
		
		public function getPathToWebRoot() {
			return $this->globals['PATHTOWEBROOT'];
		}

		public function getPathToAdmin() {
			return $this->globals['PATHTOADMIN'];
		}
		
		public function PATHTOADMIN() {
			return $this->globals['PATHTOADMIN'];
		}		
		/**
		 * public function getVersion()
		 * Liefert die Version des Content-o-maten
		 *
		 * @param void
		 * @return string Versionsnummer als String
		 */
		public function getVersion() {
			return $this->version;
		}

		/**
		 * public function Build()
		 * Liefert das Datum der Version des Content-o-maten
		 *
		 * @param void
		 * @return string Datum im Format YYYY-MM-DD als String
		 */
		public function getBuild() {
			return $this->build;
		}
		
		public function getBuildParts() {
			$buildParts = explode('-', $this->build);
			
			return array(
				'buildYear' => $buildParts[0],
				'buildMonth' => $buildParts[1],
				'buildDay' => $buildParts[2]
			);
		}

		public function addAutoloadNamespace($name, $path, $priorize = false) {
			
			if (!is_object($this->autoload)) {
				$this->autoload = new PsrAutoloader();
			}
			
			return $this->autoload->addNamespace(trim($name), $this->formatPath($path), $priorize);
		}
		
		/**
		 * public function userNotLoggedIn()
		 * Default action, should be called if a user is not logged in.
		 *
		 * @param void
		 * @return void
		 */
		public function userNotLoggedIn() {
			header('Location: /' . $this->getPathToAdmin() . 'index.php');
			exit;
		}
		
		public function setUseTemplate($status) {
			settype($status, 'bool');
			$this->useTemplate = $status;
		}

		public function useTemplate() {
			return $this->useTemplate;
		}
		
		function obStartGzipped() {
			$encode = getenv('HTTP_ACCEPT_ENCODING');
			if (stristr($encode, 'gzip')) {
				ob_start('ob_gzhandler');
			} else {
				ob_start();
			}
		}
	};  // End of class 
?>
