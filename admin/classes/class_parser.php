<?php
/**
 * class_parser.php
 * 
 * Klasse, die Templates nach Content-o-mat Makros durchsucht uns ersetzt
 * 
 * Die Parser-Klasse durchsucht ein Seitentemplate und ersetzt die darin enthaltenen
 * CMS-Makros und -Algorythmen durch die entsprechenden Werte
 *
 * @package content-o-mat
 * @author J.Hahn <info@content-o-mat.de>, J.Braun <info@content-o-mat.de>
 * @version 2017-04-26
 */

namespace Contentomat;

class Parser {

	////////////////////////////
	//
	// Objekt-Variablen
	//
	////////////////////////////

	public $cmt;
	protected $db;
	public $cmt_dbtable;
	public $dformat;
	public $session;
	public $db_values;
	public $db_values_formatted;
	public $vars;
	public $evalvars;
	public $pagevars;
	public $getvars;
	public $postvars;
	public $var_formats;
	public $navvars;
	public $navlevel;
	public $content_data;
	public $obj_id;
	public $mode;
	public $content_groups;
	public $contentGroup;
	public $contentHiddenFields;
	public $pageHasHtmlObject;
	public $htmlEditorImageFolder;
	public $css_file;
	public $page_links;
	public $page_children;
	public $is_real_parent;
	public $loopFile;
	public $loopFilePath;
	public $alternationFlag;
	public $pageLevel;
	public $navElementPosition;
	
	protected $layoutModeRawData;	// Workaround: If flag is true in layout mode, only the value of a parsed macro is returned without the additional content-o-mat layout mode tags
	
	// i18n
	protected $i18nTextField;
	protected $i18nTable;
	protected $i18nIDField;
	protected $i18nLangField;
	protected $i18nLanguage;
	
	// control macros
	protected $controlMacros = array();
	protected $controlIntermediateMacros = array();
	protected $controlEndMacros = array();
	protected $controlMacrosRegExp = '';
	
	// Tables
	protected $pagesTable = '';
	protected $linksTable = '';
	protected $contentsTable = '';
	
	// Other page relevant data
	protected $pageId = 0;
	protected $parentId = 0;
	protected $pageLanguage = '';
	protected $pathToWebroot = '';
	protected $pathToAdmin = '';
	
	// more
	protected $uniqueid;
	
	public function __construct ($refObj='') {

		// 2015-12-09: Workaround due to compatibility reasons. Override in all scripts!
		$this->pagesTable = defined('CMT_PAGES') ? CMT_PAGES : '';
		$this->linksTable = defined('CMT_LINKS') ? CMT_LINKS : '';
		$this->contentsTable = defined('CMT_CONTENT') ? CMT_CONTENT : '';
		$this->pageId = defined('PAGEID') ? PAGEID : '';
		$this->parentId = defined('PARENTID') ? PARENTID : '';
		$this->pageLanguage = defined('PAGELANG') ? PAGELANG : '';
		$this->pathToWebroot = defined('PATHTOWEBROOT') ? PATHTOWEBROOT : '';
		$this->pathToAdmin = defined('PATHTOADMIN') ? PATHTOADMIN : '';
		$this->pathToTmp = defined('PATHTOTMP') ? PATHTOTMP : '';

		// TODO 2015-11-13: Stupid crap? This throwas an error when $this->cmt->someFunction() is called an $this->cmt is null!???
		if (class_exists(__NAMESPACE__ . '\Contentomat')) {
			$this->cmt = Contentomat::getContentomat();
		} else {
			$this->cmt = null;
		}
		
		if ($refObj) {
			$refObjType = strtolower(get_class($refObj));
		}

		if ($refObj && $refObjType == 'dbcex') {
			$this->db = $refObj;
		} else if (class_exists( __NAMESPACE__ . '\DBCex')) {
			$this->db = new DBCex();
		}

	// TODO!
		// Datenformatierklasse nicht unbedingt nötig für die meisten Parser-Aufgaben
// Wegen Querinstanzierungen muss im Parser die Klasse dataformat entfernt werden! 2012-08-09		
		if ($refObj && $refObjType == 'dataformat') {
			$this->dformat = $refObj;
		} else {
			$this->dformat = null;
		} 

		// Sessionklasse, vorerst nur f�r Makro SESSIONVAR n�tig

		// this causes problems if a session is initialized in more than one script
//		$this->session = SessionHandler::getSession();

		// 2015-10-01: Test on reliance!
		$this->session = $this->cmt->getSession();
		
		$this->getvars = $_GET;
		$this->postvars = $_POST;
		$this->evalvars = array();
		$this->vars = array();
		$this->db_values = array();
		$this->var_formats = array ("DATE" => "date", "TIME" => "time", "DATETIME" => "datetime");
		$this->navlevel = 1;
		$this->mode = "view";
		// Die Vars hier vielleicht erst dann initialisieren, falls es wirklich Content zu bearbeiten gibt?
		$this->content_groups = 0;
		$this->contentHiddenFields = array();
		$this->pageHasHtmlObject = false;
//		$this->pageHTMLObjectIDs = array();
		$this->page_links = array();
		$this->page_children = 0;
		$this->is_folder = false;
		$this->alternationFlag = 0;
		
		$this->layoutModeRawData = false;
		
		// init default language
		$this->setI18NTable(array(
			'table' => 'cmt_i18n',
			'idField' => 'string_id',
			'textField' => 'string_' . $this->pageLanguage
		));
		
		$this->setI18NLanguage($this->pageLanguage);
		
		$this->initControlMacros();
	}
	
	/**
	 * protected function initControlMacros()
	 * Does some initializing work needed with the control macros (e.g. creates the regular expression string which is used when controls are parsed.
	 *
	 * @param void
	 * @return void
	 */
	protected function initControlMacros() {
		
		$this->controlMacros = array(
			'IF',
			'LOOP NAVIGATION',
			'LOOP CONTENT',
			'EVAL',
			'SWITCH',
			'LOOP DIRECTORY',
			'LOOP TABLE',
			'LOOP VAR'
		);
		
		$this->controlIntermediateMacros = array(
			'ELSE' => 'IF',
			'CASE' => 'SWITCH'
		);
		
		foreach($this->controlMacros as $macro) {
			$this->controlEndMacros[] = 'END' . $macro;
		}
		
		$this->controlMacrosRegExp = implode('|', $this->controlMacros) . '|' . implode('|', array_keys($this->controlIntermediateMacros)) . '|' . implode('|', $this->controlEndMacros);
	}
	
	/**
	 * public function addControlMacro()
	 * Compared to simple macros control structure macros need to be registered with this method. For simple macros it is enough to extend the parser class with own methods like "protected function macro_MYMACRO().
	 *
	 * @param array $params Array with following key=>value pairs:
	 * 						- "macro" => "myMacroName" (e.g. "SWITCH")
	 * 						- "internediateMacro" => "optional MACRO" (some control structures need inner macros to separate cases, e.g. macro "SWITCH" intermediate macro "CASE". This value is optional! 
	 * @return void
	 */
	public function addControlMacro($params) {
		
		if (!is_array($params)) {
			$params = array('macro' => $params);
		}
		
		$macro = trim($params['macro']);
		$this->controlMacros[] = trim($macro);
		$this->controlEndMacros[] = 'END' . $macro;
		
		if (isset($params['intermediateMacro'])) {
			$this->controlIntermediateMacros[trim($params['intermediateMacro'])] = $macro;
		}
		
		$this->controlMacrosRegExp = implode('|', $this->controlMacros) . '|' . implode('|', array_keys($this->controlIntermediateMacros)) . '|' . implode('|', $this->controlEndMacros);
	}
	
	/**
	 * public function parse()
	 * Main parser function. Searches for control structures an marks them in the first step with ids (numbers). In the second step control structures and simple macros are parsed.
	 *
	 * @param string $parsedContent The content to parse.
	 * @return string The parsed content.
	 *
	 */
	public function parse($parsedContent='') {
	
		$macroStack = array ();
		$macroCounter = array ();
		$levelCounter = 1;
		$content_temp = '';

		// first parse INCLUDE macros
		$parsedContent = $this->parseIncludes($parsedContent);

		// then prepare all control macros and parse them
		while (preg_match ('/\{(' . $this->controlMacrosRegExp . ')(.*?)\)?\}/s', $parsedContent, $match)) {
		
			$macro = $match[1];
			$macroPosition = strpos($parsedContent, $macro);

			if ($macroPosition === 0) {
				break;
			}
			$macro = trim($macro);

			if (in_array($macro, $this->controlMacros)) {
				
				// control macro starting like 'LOOP NAVIGATION'
				$macroName = $macro;
				$macroCounter[$macroName]++;
				$levelCounter++;
				$macroStack[$macroName][$levelCounter] = $macroCounter[$macroName];
				$newMacro = $macro.$macroCounter[$macroName];
				
			} else if (in_array($macro, $this->controlEndMacros)) {

				// control macro ending like 'ENDLOOP NAVIGATION'				
				$macroName = str_replace ('END', '', trim($macro));
				$newMacro = $macro.$macroStack[$macroName][$levelCounter];;
				unset ($macroStack[$macroName][$levelCounter]);
				$levelCounter--;

				if ($levelCounter == 1) {
					$level0++;
				}
						
			} else if ($this->controlIntermediateMacros[$macro]) {
				
				// intermedia macro in an outer control macro like 'ELSE'
				$newMacro = $macro.$macroStack[$this->controlIntermediateMacros[$macro]][$levelCounter];
			}

		
			$addLength = strlen($newMacro);
			$content_temp .= substr($parsedContent, 0, $macroPosition).$newMacro;
			$parsedContent = substr($parsedContent, $macroPosition+strlen($macro), strlen($parsedContent)+$addLength);
			
		}
		
		$parsedContent = $content_temp.$parsedContent;
		$parsedContent = $this->parseControls($parsedContent);
		
		// finally parse macros outside of control structures
		$parsedContent = $this->parseMacros($parsedContent);

		return $parsedContent;
	}
	
	/**
	 * public function parse_makros()
	 * OUTDATED/DEPRECATED: Old name of method Parser::parseMacros. Will be deleted in next versions. 
	 *
	 * @param string $string
	 * @return string
	 */
	public function parse_makros($string) {
		return $this->parseMacros($string);
	}
	
	/**
	 * public function parseMacros()
	 * Prses simple macros in a string.
	 *
	 * @param string $string 	the content to parse.
	 * @param string $macros	search only for this macro(s), e.g. "INCLUDE" or "VAR|GETVAR|USERVAR"
	 * @return string The parsed content. 
	 */
	public function parseMacros($string, $macros='') {
	
		if (!$macros) {
			$regexpMacros = '/\{([^{}]*)\}/is';
		} else {
			$regexpMacros = '/\{(' . trim(strtoupper($macros)) . '[^}]*)\}/is';
		}
//var_dump($regexpMacros);	
		preg_match_all ($regexpMacros, $string, $matches);

		foreach ($matches[1] as $match) {
				
			$params = explode (':', $match);
			$macro = array_shift($params);
			$value = array_shift($params);
			$replaceData = '';
			$returnBoolean = true;
	
			$macroMethod = 'macro_' . $macro;

			if (method_exists($this, $macroMethod)) {
				$replaceData = $this->$macroMethod($value, $params);
				//var_dump($replaceData);
			} else {
				
				// don't replace the macro
				//$replaceData = $this->protectMakros('{'. $match . '}');
				$replaceData = '{'. $match . '}';
				
				// to avoid reparsing unset $matches[1] so the method call Parser::protectmakros() isn't neccessary
				unset($matches[1]);
			}
			$string = preg_replace('/\{'.preg_quote($match, '/').'\}/U', $replaceData, $string, 1);
	
		}

		//if (count($matches[1])) {
		if ($matches[1]) {
			return $this->parseMacros($string);
		}
	
		return $string;
	}

	/**
	 * function parseIncludes()
	 * Parse all includes, means: include all file contents from macros "{INCLUDE:...}" but don't parse them.
	 * 
	 * @param unknown $string
	 * @return mixed
	 */
	public function parseIncludes($string) {

		$regexpMacros = '/\{(INCLUDE[^}]*)\}/is';

		preg_match_all ($regexpMacros, $string, $matches);

		foreach ($matches[1] as $match) {
				
			$params = explode (':', $match);
			$macro = array_shift($params);
			$value = array_shift($params);
			$replaceData = '';
			$returnBoolean = true;
	
			$replaceData = $this->macro_INCLUDE($value, array('dontparse'));
			$string = preg_replace('/\{'.preg_quote($match, '/').'\}/U', $replaceData, $string, 1);
	
		}

		return $string;
	}
	
	/**
	 * function macro_CONTENT()
	 *
	 * Type: Layout object macro
	 * Usage: {CONTENT:id}
	 * Parameters: fieldname.
	 * Example: <p>Object's id is {CONTENT:id}</p>
	 * Description: Displays the content of a object dataset field.
	 *
	 * @param string $value fieldname
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_CONTENT($value, $params) {
			
		$replaceData = $this->content_data[$value];
	
		if ($params[1]) {
			array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_HEAD()
	 *
	 * Type: Layout object macro
	 * Usage: {HEAD:1-5}
	 * Parameters: 1 to 5 to address the different available head fields in a layout object.
	 * Example: <h1>{HEAD:1}</h1>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_HEAD($value, $params) {
			
		$replaceData = $this->content_data['head'.$value];

		if ($params[0]) {
			//array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	protected function makeContentEditable($text) {
		return $text;
	}
	
	/**
	 * protected function macro_TEXT()
	 *
	 * Type: Layout object macro
	 * Usage: {TEXT:1-5}
	 * Parameters: 1 to 5 to address the different available text fields in a layout object.
	 * Example: <div>{TEXT:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_TEXT($value, $params) {
		$replaceData = nl2br($this->parse($this->content_data['text'.$value]));
			
		if ($params[0]) {
			//array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}

		return $replaceData;
	}
	
	/**
	 * protected function macro_HTML()
	 *
	 * Type: Layout object macro
	 * Usage: {HTML:1}
	 * Parameters: 1 to address the available html field in a layout object.
	 * Example: <div>{HTML:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_HTML($value, $params) {
		return $this->content_data['html'.$value];
	}
	
	/**
	 * protected function macro_IMAGE()
	 *
	 * Type: Layout object macro
	 * Usage: {IMAGE:1-5:imgdir}
	 * Parameters: 1 to 5 to address the different available image fields in a layout object. Optional parameter root path
	 * Example: <div>{IMAGE:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_IMAGE($value, $params) {

//		if (!stristr($this->content_data['image'.$value], 'cmt_no_image.gif')) {

			// TODO: Hier die XHTML valide Schreibweise anders integrieren!
//			$replaceData = str_replace('>', '/>', $this->content_data['image'.$value]);
			$replaceData = $this->content_data['image'.$value];
//			preg_match('/(src=("|\')?)(.){1}/is', $replaceData, $match);


// 			if (CMT_MODREWRITE == '1' && $match[2] != '/' && $match[3] != '/') {
// 				$replaceData = str_replace ($match[1], $match[1].'/', $replaceData);
// 			}
			if ($params[1]) {
				$replaceData = str_replace ('<img', '<img '.$params[1], $replaceData);
			}
//		} else {
//			$replaceData = '';
//		}

		return $replaceData;
	}

	/**
	 * public function macro_IMAGESRC()
	 * 
	 * Type: Layout object macro
	 * Usage: {IMAGESRC:1-5:myMethod}
	 * Parameters: 1 to 5 to address the different available image fields in a layout object. Optional parameter functionname
	 * Example: <a href="{IMAGESRC:1}</a>
	 * 
	 * @param number $imageNr Image's number
	 * @param array $params additional parameters separated by ':'
	 */
	public function macro_IMAGESRC($imageNr, $params) {
	
		$image = $this->content_data['image' . intval($imageNr)];
		preg_match('/src\s?=\s?("|\')?([^\s"\']+)/', $image, $match);
		$replaceData = $match[2];
	
		if ($params[1]) {
			array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_SCRIPT()
	 *
	 * Type: Layout object macro
	 * Usage: {SCRIPT:1:rootPath}
	 * Parameters: 1 to address the available script field in a layout object. Optional parameter: root path
	 * Example: <div>{HTML:1:phpincludes}</div>
	 * Description: Includes a script file (e.g. PHP).
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_SCRIPT($value, $params) {
		
		$replaceData = '';
		$src_path = $this->format_directory($params[0]);

		if ($this->content_data['file'.$value]) {
			$replaceData .= $this->exec_external_file('"'. INCLUDEPATH . $src_path . $this->content_data['file'.$value] . '"');
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_LINK()
	 *
	 * Type: Layout object macro
	 * Usage: {LINK:linkID}
	 * Parameters: linkID, id of link in corresponding link table (eg. cmt_links_en)
	 * Example: {LINK:12}This text is linked{ENDLINK}
	 * Description: Wraps the link with the given id around the text.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_LINK($value, $params) {
		if (empty($this->page_links)) {
			$this->page_links = $this->getAllPageLinks();
		}
		//var_dump($this->page_links[$value]);
		return $this->page_links[$value];
	}
	
	/**
	 * protected function macro_ENDLINK()
	 *
	 * Type: Layout object macro
	 * Usage: {ENDLINK}
	 * Parameters: none
	 * Example: {LINK:12}This text is linked{ENDLINK}
	 * Description: Will be replaced by a simple </a> tag
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_ENDLINK($value, $params) {
		return '</a>';
	}
	
	/**
	 * protected function macro_NAVIGATION()
	 *
	 * Type: Template macro
	 * Usage: {NAVIGATION:parameter:optional parameters}
	 * Parameters:	id => looped page id.
	 * 				title => page title,
	 * 				link => page URL,
	 * 				children => number of children,
	 * 				isancestor => is current page ancenstor of looped page (boolean),
	 * 				target => link target as entered in table cmt_page_(language) for looped page,
	 * 				position => position of looped page under the looped node.
	 * 				optional parameters for 'title' are possible
	 *
	 * Example: {NAVIGATION:title}This text is linked{ENDLINK}
	 * Description: Works only in a {LOOP NAVIGATION()}-Loop!! Returns selected data of the current page in the loop.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_NAVIGATION($value, $params) {
	
		switch ($value) {
			case 'id':
				$replaceData = $this->navvars[$this->navlevel]['id'];
				break;
				
			case 'title':
				$replaceData = $this->navvars[$this->navlevel]['cmt_title'];
				if ($params[0]) {
					$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
				}
				break;
	
			case 'link':
				// Externer Link oder normaler
				if ($this->navvars[$this->navlevel]['cmt_type'] == 'link') {
					$replaceData = $this->parse($this->navvars[$this->navlevel]['cmt_link']);
				} else {
					// mod_rewerite aktiviert?
					if (CMT_MODREWRITE != '1') {
						$replaceData = SELF.'?pid='.$this->navvars[$this->navlevel]['id'].'&amp;cmtLanguage='.$this->navvars[$this->navlevel]['cmt_language'];
						if (CMT_FORCECOOKIES != '1' && defined('ADDSID') && ADDSID != '') {
							$replaceData .= '&amp;'.ADDSID;
						}
					} else {
						$replaceData = WEBROOT.$this->navvars[$this->navlevel]['cmt_language'].'/'.$this->navvars[$this->navlevel]['id'].'/';
						//$replaceData = PATHTOWEBROOT.PAGELANG.'/'.$this->navvars[$this->navlevel]['id'].'/';
						if ($this->navvars[$this->navlevel]['cmt_urlalias']) {
							$replaceData .= $this->navvars[$this->navlevel]['cmt_urlalias'];
						} else {
							$replaceData .= $this->formatFilename($this->navvars[$this->navlevel]['cmt_title']).'.html';
						}

						if (defined('ADDSID') && ADDSID != '') {
							$replaceData .= '?'.ADDSID;
						}
					}
				}
				break;
	
				// TODO: {NAVIGATION:children} etc funktioniert noch nicht in einer Schleife {LOOP NAVIGATION (2:en)}...{ENDLOOP NAVIGATION},
				// welche eine Navigation in einer anderen Sprachversion der Website darstellt.
			case 'children':
				$this->db->Query('SELECT COUNT(id) AS children FROM '.$this->pagesTable.' WHERE cmt_parentid = \''.$this->navvars[$this->navlevel]['id'].'\' AND cmt_showinnav = \'1\'');
	
				$r = $this->db->Get(MYSQLI_ASSOC);
				$replaceData = intval($r['children']);
				break;
	
			case 'isancestor':
				//echo "Bin auf Seite: ".PAGEID.", soll testen: ".$this->navvars[$this->navlevel]['id'].'<br>';
				if ($this->navvars[$this->navlevel]['id'] == $this->pageId || $this->navvars[$this->navlevel]['id'] == $this->parentId) {
					$replaceData = 'true';
					//echo "Direkte Verwandschaft<br>";
				} else {
					$replaceData = $this->get_ancestors($this->pageId, $this->navvars[$this->navlevel]['id']);
					//echo "<p>Teste: ".PAGEID." mit ".$this->navvars[$this->navlevel]['id'].": ".$replaceData.'<br>';
				}
				// echo "Ist: $replaceData<br>";
				break;
	
			case 'target':
				if ($this->navvars[$this->navlevel]['cmt_link_target']) {
					$replaceData = ' target="'.$this->navvars[$this->navlevel]['cmt_link_target'].'" ';
				}
				break;
	
			case 'position':
				$replaceData = $this->navElementPosition;
				break;

			default:
				$replaceData = $this->navvars[$this->navlevel][$value];
				break;
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_NAVPAGEVAR()
	 *
	 * Type: Template macro
	 * Usage: {NAVPAGEVAR:fieldName:optionalParameters}
	 * Parameters: name of field in cmt_pages_(language).
	 * Example: <h2>{NAVPAGEVAR:my_subtitle:strtolower}</h2>
	 * Description: Returns the value of the chosen field of the current loooped page entry in cmt_tables_(language). Works only in an {LOOP NAVIGATIO()} loop!
	 *
	 * Update 2016-01-13: Now allows parameter recursive as in macro_PAGEVAR (see there)
	 * 
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Variable value
	 */
	protected function macro_NAVPAGEVAR($value, $params) {
		$replaceData = "";

		// Interpret first param as "command" / special use case...
		$command = strtolower($params[0]);
		switch ($command) {
			
			// Is command the magic string "recursive"? If yes then do the complex stuff
			case 'recursive':
				// discard "recursive" flag
				array_shift($params);
				try {
					$replaceData = $this->getPageVarRecursive($value, $this->navvars[$this->navlevel]['id'], $this->pageLanguage);
				}
				catch (\Exception $e) {
					die ($e->getMessage());
				}
				break;

			default:
				$replaceData = $this->navvars[$this->navlevel][$value];
				break;
		}

		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_PAGEID()
	 *
	 * Type: Template macro
	 * Usage: {PAGEID}
	 * Parameters: none
	 * Example: <p>My id is: {PAGEID}</p>
	 * Description: Returns the value id of the current page.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return integer The id of the current page
	 */
	protected function macro_PAGEID($value, $params) {
		return $this->pageId;
	}
	
	/**
	 * protected function macro_PAGELANG()
	 *
	 * Type: Template macro
	 * Usage: {PAGELANG}
	 * Parameters: none
	 * Example: <a href="/{PAGELANG}/{PAGID}/myPage.html">click me!</a>
	 * Description: Returns the language shortcut of the current page.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string The language shortcut of the current page
	 */
	protected function macro_PAGELANG($value, $params) {
		return $this->pageLanguage;
	}
	
	/**
	 * protected function macro_PARENTID()
	 *
	 * Type: Template macro
	 * Usage: {PARENTID}
	 * Parameters: none
	 * Example: <p>My parent is: {PARENTID}</p>
	 * Description: Returns the id of the current page's parent page.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return integer The id of the current page's parent page.
	 */
	protected function macro_PARENTID($value, $params) {
		if (!$value) {
			$replaceData = $this->parentId;
		} else {
			$value = intval($value);
			$parent = $this->parentId;
	
			for ($i = 0; $i < $value; $i++) {
				$this->db->query('SELECT cmt_parentid FROM '.$this->pagesTable.' WHERE id = \''.$parent.'\'');
				$r = $this->db->get();
				$parent = $r['cmt_parentid'];
				if (!$r['cmt_parentid'] || $i > 100) {
					break;
				}
			}
			$replaceData = $parent;
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_PAGEURL()
	 *
	 * Type: Template macro
	 * Usage: {PAGEURL}
	 * Parameters: none
	 * Example: <a href="{PAGEURL}">click me!</a>
	 * Description: Returns the complete URL of the current page depending on the global settings.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string URL of the page.
	 */
	protected function macro_PAGEURL($value, $params) {
		if (!$value) {
			$replaceData = PAGEURL;
		} else {
	
			$pageID = intval($value);
	
			// Sprache angegeben?
			if (!$params[0]) {
				$pagesTable = $this->pagesTable;
				$pageLang = $this->pageLanguage;
			} else {
				$pageLang = substr(str_replace(' ', '', $params[0]), 0, 16);
				$pagesTable = 'cmt_pages_'.$pageLang;
			}
	
			$this->db->query('SELECT * FROM '.$pagesTable.' WHERE id = \''.$pageID.'\'');
			$r = $this->db->get();
	
			// Das hier gibt es so schon unter "NAVLINK" => in Methode verarbeiten!!!
			// Externer Link oder normaler
			if ($r['cmt_type'] == 'link') {
				$replaceData = $r['cmt_link'];
			} else {
	
				// TODO: In Klasse LayutParser: normaler oder Layout-Modus?
				if ($this->mode == 'view') {
	
					// mod_rewerite aktiviert?
					if (CMT_MODREWRITE != '1') {
						$replaceData = SELF.'?pid='.$r['id'].'&amp;lang='.$pageLang;
	
						if (CMT_FORCECOOKIES != '1' && defined('ADDSID') && ADDSID != '') {
							$replaceData .= '&amp;'.ADDSID;
						}
						if ($params[1]) {
							$replaceData .= '&amp;'.urlencode($params[1]);
						}
					} else {
						$replaceData = WEBROOT.$pageLang.'/'.$pageID.'/';
	
						if ($r['cmt_urlalias']) {
							$replaceData .= $r['cmt_urlalias'];
						} else {
							$replaceData .= $this->formatFilename($this->parse($r['cmt_title'])).'.html';
						}
						if (defined('ADDSID') && ADDSID != '') {
							$replaceData .= '?'.ADDSID;
							if ($params[1]) {
								$replaceData .= '&amp;'.urlencode($params[1]);
							}
						} else {
							if ($params[1]) {
								$replaceData .= '?'.urlencode($params[1]);
							}
						}
					}
				} else {
					$replaceData = SELFURL.'&amp;pid='.$pageID.'&amp;lang='.$pageLang;
					if ($params[1]) {
						$replaceData .= '&amp;'.urlencode($params[1]);
					}
				}
			}
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_PAGECHILDREN()
	 *
	 * Type: Template macro
	 * Usage: {PAGECHILDREN}
	 * Parameters: none
	 * Example: <p>This page has {PAGECHILDREN} subpages!</p>
	 * Description: Returns the number of children/ subpages of the current node/ page.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return integer Number of children.
	 */
	protected function macro_PAGECHILDREN($value, $params) {
		$this->db->query('SELECT COUNT(id) AS children FROM '.$this->pagesTable.' WHERE cmt_parentid = \''.$this->pageId.'\' AND cmt_showinnav = \'1\'');
		$r = $this->db->get();
		return intval($r['children']);
	}
	
	/**
	 * protected function macro_PAGESIBLINGS()
	 *
	 * Type: Template macro
	 * Usage: {PAGESIBLINGS}
	 * Parameters: none
	 * Example: <p>There are {PAGESIBLINGS} on this level!</p>
	 * Description: Returns the number of siblings of the current node/ page.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return integer Number of pages on the same level / under the same node.
	 */
	// TODO: Sollten nicht -1 Siblings zurückgegeben werden?
	protected function macro_PAGESIBLINGS($value, $params) {
		$this->db->query('SELECT COUNT(id) AS siblings FROM '.$this->pagesTable.' WHERE cmt_parentid = \''.$this->parentId.'\' AND cmt_showinnav = \'1\'');
		$r = $this->db->get();
		return intval($r['siblings']);
	}
	
	/**
	 * protected function macro_PAGELEVEL()
	 *
	 * Type: Template macro
	 * Usage: {PAGELEVEL}
	 * Parameters: none
	 * Example: <p>Depth: {PAGELEVEL}</p>
	 * Description: Returns the depth of the current node/ page in the pages tree.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return integer Depth of current page in pages tree.
	 */
	protected function macro_PAGELEVEL($value, $params) {
		if ($this->pageLevel) {
			$replaceData = $this->pageLevel;
		} else {
			$parent = $this->parentId;
			$c = 0;
			do {
				$this->db->query('SELECT cmt_parentid FROM '.$this->pagesTable.' WHERE id = \''.$parent.'\'');
				$r = $this->db->get();
				$parent = $r['cmt_parentid'];
				$c++;
			}	while ($r);
			$replaceData = $c;
			$this->pageLevel = $c;
		}
		return intval($replaceData);
	}
	
	/**
	 * protected function macro_PAGETITLE()
	 *
	 * Type: Template macro
	 * Usage: {PAGETITLE:ucfirst}
	 * Parameters: Optional parameters possible
	 * Example: <title>{PAGETITLE:ucfirst}</title>
	 * Description: Returns the title of the current page.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Page title
	 */
	protected function macro_PAGETITLE($value, $params) {
		
		$replaceData = $this->pagevars['cmt_title']; //PAGETITLE;

		if ($value) {
			$replaceData = $this->processMacroValue($value, $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_PAGEVAR()
	 *
	 * Type: Template macro
	 * Usage: {PAGEVAR:fieldName:[recursive[:PAGEID[:PAGELANG]][:USERFUNC]}
	 * Parameters: fieldName => name of the requested field in table cmt_pages_(language), optional parameters possible
	 * 		"recursive": if the second param is "recursive" (case-insensitive), then the page tree is recursively traveresd upwards
	 * 		until the first page has a non-empty fieldName. In this case PAGEID is the ID of the page where to start and defaults to the
	 * 		current one, PAGELANG is the lang version of the page tree to use or the current language one by default.
	 * 		In all cases, the last param USERFUNC can be passed a callable function/method which the resulting field value will be
	 * 		passed to and returns the final/processed, valua, e.g. think of nl2br etc.
	 * 
	 * Example 1: <p>I'm a protected page: {PAGEVAR:cmt_protected}</p>
	 * Example 2: <img src="/img/{PAGEVAR:moodpic:{PARENTID}:{PAGELANG}" alt="" />
	 * 
	 * Description: Returns the value of the named table field for the current page or from the first ancestor page that has 
	 * a non empty value for this table field.
	 *
	 * @param string $value Content
	 * @param array $params see above
	 *
	 * @return mixed Page property
	 */
	protected function macro_PAGEVAR($value, $params) {


		$replaceData = "";

		// Interpret first param as "command" / special use case...
		$command = strtolower($params[0]);
		switch ($command) {
			
			// Is command the magic string "recursive"? If yes then do the complex stuff
			case 'recursive':
				// discard "recursive" flag
				array_shift($params);
				// extract PAGEID
				$pageID = (int)array_shift($params) or $pageID = $this->pageId;
				// extract PAGELANG
				$pageLang = array_shift($params) or $pageLang = $this->pageLanguage;

				try {
					$replaceData = $this->getPageVarRecursive($value, $pageID, $pageLang);
				}
				catch (\Exception $e) {
					Logger::log ($e->getMessage(), LOG_LEVEL_WARNING);
				}
				break;

			// Or else, relax and return the current page's value
			default:
				$replaceData = $this->pagevars[$value];
				break;
		}

		// After all, it is still possible to pipe the retrieved value through a user func
		if ($params[0]) {
		 	$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}

	/**
	 * Recursively traverse up the page tree of a given pagelang and search for 
	 * a non-empty value in the table's fieldName field.
	 * 
	 * Used by macro_PAGEVAR and macro_NAVPAGEVAR
	 * 
	 * @param string $fieldName 	The name of the field in cmt_pages_xx to search for
	 * @param int    $pageID 		The ID of the page to start from
	 * @param string $pageLang 		The language version of the page tree to use
	 * 
	 * @return string 				The value or empty string in case of an error or no value found		
	 * 
	 * @throws Exception  			In case of a DB error, an Exception is thrown
	 */
	protected function getPageVarRecursive($fieldName, $pageID, $pageLang) {

		/* Break out if at ROOT level (or beyond...) */
		if ($pageID <= 0) {
			return "";
		}
		// Read field value (and parent_id) from current pageID page in current pageLang
		if ($this->db->query(sprintf('SELECT `%s`,cmt_parentid FROM cmt_pages_%s WHERE id=%u', $fieldName, $pageLang, (int)$pageID)) !== 0) {
			throw new \Exception('DB Query failed: ' . $this->db->getLastError());
		}
		$result = $this->db->get();

		// Do we have a non empty field? Then set return value and break from recursion, else get recurse with parentID
		return (!empty($result[$fieldName]))
			? $result[$fieldName]
			: $this->getPageVarRecursive($fieldName, (int)$result['cmt_parentid'], $pageLang)
		;
	}
	
	/**
	 * protected function macro_SELFURL()
	 *
	 * Type: Template macro
	 * Usage: {SELFURL}
	 * Parameters: none
	 * Example: <a href="{SELFURL}">Me, myself and I</a>
	 * Description: Returns the raw URL to the current page. Is used in backend.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Raw URL of the page.
	 */
	protected function macro_SELFURL($value, $params) {
		return SELFURL;
	}
	
	/**
	 * protected function macro_SELF()
	 *
	 * Type: Template macro
	 * Usage: {SELF}
	 * Parameters: none
	 * Example: <p>my filename: {SELF}I</p>
	 * Description: Returns the name of the current file.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Name of the current file.
	 */
	protected function macro_SELF($value, $params) {
		return SELF;
	}
	
	/**
	 * protected function macro_SID()
	 *
	 * Type: Template macro
	 * Usage: {SID}
	 * Parameters: none
	 * Example: <input type="hidden" name="sid" value="{SID}" />
	 * Description: Returns the current session id
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Current session id.
	 */
	protected function macro_SID($value, $params) {
		return SID;
	}
	
	// Session-ID
	// TODO: ????
	protected function macro_ADDSID($value, $params) {
		//if (((CMT_USECOOKIES == '1' && CMT_FORCECOOKIES != '1') || CMT_USECOOKIES == '0') && $this->addSid) {
		if (defined('ADDSID')) {
			if (!$value) {
				$replaceData = '&amp;'.ADDSID;
			} else {
				$replaceData = $value.ADDSID;
			}
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_PATHTOADMIN()
	 *
	 * Type: Template macro
	 * Usage: {PATHTOADMIN}
	 * Parameters: none
	 * Example: <a href="{PATHTOADMIN}index.php">go to cms login</a>
	 * Description: Returns the path to the admin directory depending on the position of the current page in the file system.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Path to admin directory
	 */
	protected function macro_PATHTOADMIN($value, $params) {
		return PATHTOADMIN;
	}
	
	/**
	 * protected function macro_PATHTOWEBROOT()
	 *
	 * Type: Template macro
	 * Usage: {PATHTOWEBROOT}
	 * Parameters: none
	 * Example: <script src="{PATHTOWEBROOT}javascripts/jquery.js">
	 * Description: Returns the path to the web's root directory depending on the position of the current page in the file system.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Path to web root directory
	 */
	protected function macro_PATHTOWEBROOT($value, $params) {
	
		if (CMT_MODREWRITE == '1' && $this->mode == 'view') {
			$replaceData = '../../' . $this->pathToWebroot;
		} else {
			$replaceData = $this->pathToWebroot;
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_CMT_TEMPLATE()
	 *
	 * Type: Template macro
	 * Usage: {CMT_TEMPLATE}
	 * Parameters: none
	 * Example: <link rel="stylesheet" src="{CMT_TEMPLATE}app-paperboy/css/style.css" />
	 * Description: Only available in the backend: Returns the path to the cms templates.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Path to cms templates
	 */
	protected function macro_CMT_TEMPLATE($value, $params) {
		return CMT_TEMPLATE;
	}
	
	/**
	 * protected function macro_CONSTANT()
	 *
	 * Type: Template macro
	 * Usage: {CONSTANT:constantName}
	 * Parameters: none
	 * Example: <p>This is a global constant named "KLAUS": {CONSTANT:KLAUS}
	 * Description: Returns the value of the named constant (or '' if it is not defined).
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Value of the Constant or '' if not defined.
	 */
	protected function macro_CONSTANT($value, $params) {
		if (defined($value)) {
			$replaceData = constant($value);
		} else {
			$replaceData = '';
		}
		
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	// OUTDATED??? Wird nur im Layoutmodus genutzt für HTML-Editoren
	protected function macro_INHERITCLASS($value, $params) {
		return '';
	}
	
	/**
	 * protected function macro_FIELD()
	 *
	 * Type: Template macro
	 * Usage: {FIELD:fieldName:FORMATTED:optionalParameters}
	 * Parameters: FORMATTED => formats the field value to "view" with Dataformat::format()
	 * Example: <td>{FIELD:mlog_post_title:FORMATTED}</td>
	 * Description: Only available in the backend or in a {LOOP TABLE()} loop: Returns the value of the named database table field.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of the field.
	 */
	protected function macro_FIELD($value, $params) {
		if ($params[0] == 'FORMATTED') {
			if (isset($this->db_values_formatted[$value])) {
				$replaceData = $this->db_values_formatted[$value];
			} else {
				$replaceData = $this->db_values[$value];
			}
			array_shift($params);
		} else {
			$replaceData = $this->db_values[$value];
		}
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_POSTVAR()
	 *
	 * Type: Template macro
	 * Usage: {POSTVAR:varName:optionalParameters}
	 * Parameters: optional parameters
	 * Example: <input type="text" name="myVar" value="{POSTVAR:htmlspecialchars}" />
	 * Description: Returns the value of the named variable in $_POST.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of post variable.
	 */
	protected function macro_POSTVAR($value, $params) {
		$replaceData = trim(urldecode($this->postvars[$value]));
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_GETVAR()
	 *
	 * Type: Template macro
	 * Usage: {GETVAR:varName:optionalParameters}
	 * Parameters: optional parameters
	 * Example: {IF ({ISSET:myVar:GETVAR})}myVAR is set in $_GET{ENDIF}
	 * Description: Returns the value of the named variable in $_GET.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of get variable.
	 */
	protected function macro_GETVAR($value, $params) {
		$replaceData = trim(urldecode($this->getvars[$value]));
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	
	}
	
	/**
	 * protected function macro_USERVAR()
	 *
	 * Type: Template macro
	 * Usage: {USERVAR:varName:optionalParameters}
	 * Parameters: optional parameters
	 * Example: {EVAL} $myUserVar = 1; {ENDEVAL} <p>myUserVar got from PHP-Script: {USERVAR:myUserVar}
	 * Description: Returns the value of a variable created in a PHP-Script/ {EVAL} bloc before.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of a PHP variable.
	 */
	//protected function macro_EVALVAR':
	protected function macro_USERVAR($value, $params) { // sollte abgeschafft werden!
		$replaceData = $this->getVariable($value, $this->evalvars);
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_VAR()
	 *
	 * Type: Template macro
	 * Usage: {VAR:varName:optionalParameters}
	 * Parameters: optional parameters
	 * Example: {EVAL} $myUserVar = 1; {ENDEVAL} <p>myUserVar got from PHP-Script: {USERVAR:myUserVar}
	 * Description: Returns the value of a variable created in a PHP-Script/ {EVAL} bloc before.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of a PHP variable.
	 */
	protected function macro_VAR($value, $params) {
		
		$replaceData = $this->getVariable($value, $this->vars);
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}

		return $replaceData;
	}

	/**
	 * Get a variable value from a given "variable vault"
	 * (either $this->vars or $this->evalvars at the moment)
	 * Allows access to Array indices (numeric or keys in associative arrays)
	 * or object properties with dot notation
	 * 
	 * @example {VAR:foo.bar.bz:VAR}
	 * would dereference either $this->vars['foo']['bar']['baz'] 
	 * or 						$this->vars['foo']->bar->baz
	 * or even s.th. like 		$this->vars['foo']->bar['baz']
	 * 
	 * @param string $identifier  	The identifier string ("foo.bar.baz" in the example)
	 * @param Array $varArray 		The varArray to use 
	 * 
	 * @return string 			The stringified value of the referenced variable (empty string in case of non-existing vars)
	 */
	private function getVariable($identifier, $varArray) {

		$value = '';

		$parts = explode('.', $identifier);
		$value = $varArray[$parts[0]];
		array_shift($parts);

		foreach ($parts as $part) {
			if (is_array($value) && isset($value[$part])) {
				$value = $value[$part];
			}
			else if (is_object($value) && isset($value->{$part})) {
				$value = $value->{$part};
			}
			else {
				return '';
			}
		}
		return (string)$value;
	}

	
	/**
	 * protected function macro_SESSIONVAR()
	 *
	 * Type: Template macro
	 * Usage: {SESSIONVAR:varName:optionalParameters}
	 * Parameters: optional parameters
	 * Example:<p>myVar stored in current session: {SESSIONVAR:myVar:ucfirst}
	 * Description: Returns the value of a variable created in a PHP-Script and stored in the session before.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of a session variable.
	 */
	protected function macro_SESSIONVAR($value, $params) {
		$replaceData = $this->session->getSessionVar($value);
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_IN()
	 *
	 * Type: Template macro
	 * Usage: {IN:needle:haystackString:optionalDelimiter:optionalFunctionCall}
	 * Parameters: needle => the search string, e.g. "42", "en" or "{PAGEID}", haystackString => a delimiter separated walues string, e.g. "1,5,12,42" or "de,en,fr", optionalDelimiter => a delimiter character, e.g. "|" or ";", default is ","
	 * Example: {IF ({IN:{PAGELANG}:{NAVPAGEVAR:show_page_in_country}})}
	 * 				<a href="{NAVIGATION:link}">{NAVIGATION:title}</a>
	 * 			{ENDIF}
	 * Description: Returns true if a value is in the value string else returns false
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of a session variable.
	 */
	protected function macro_IN($value, $params) {
	
		$needle = $this->parseMacros($value);
		$values = $this->parseMacros(array_shift($params));
		$delimiter = trim($this->parseMacros(array_shift($params)));
		if (!$delimiter) {
			$delimiter = ',';
		}
	
		$haystack = explode($delimiter, $values);
		if (in_array($needle, $haystack)) {
			$replaceData = 'true';
		} else {
			$replaceData = 'false';
		}
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
		
	/**
	 * protected function macro_ISSET()
	 *
	 * Type: Template macro
	 * Usage: {ISSET:varName:varType}
	 * Parameters: optional parameter varType (VAR, USERVAR, PAGEVAR, POST, GET, CONTENT, FIELD, SESSION, CONSTANT, NAVPAGEVAR)
	 * Example: {IF ({ISSET:submit:POST})}<p>Form was submitted</p>{ENDIF}
	 * Description: Use this macro in {IF} statements to check wether a variable exists (is 0 or false) nor not.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string 'true' or 'false'
	 */
	protected function macro_ISSET($value, $params) {
	
		switch ($params[0]) {
			case 'VAR':
				if ($this->getVariable($value, $this->vars)) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
					
				//case 'EVALVAR':
			case 'USERVAR':
				if ($this->getVariable($value, $this->evalvars)) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
	
				break;
	
			case 'PAGEVAR':
				if ($this->pagevars[$value]) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
					
			case 'POST':
				if ($this->postvars[$value]) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
	
			case 'GET':
				if ($this->getvars[$value]) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
	
			case 'CONTENT':
				if (trim($this->content_data[$value]) != '' && $this->content_data[$value] != '&nbsp;') {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
					
			case 'FIELD':
				if ($this->db_values[$value] || $this->db_values[$value] === 0) {
					$replaceData = 'true';
				} else {
					$replaceData = "false";
				}
				break;
	
			case 'SESSION':
				if ($this->session->getSessionVar($value)) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
	
			case 'CONSTANT':
				if (defined($value)) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
	
			case 'NAVPAGEVAR':
				if ($this->navvars[$this->navlevel][$value]) {
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
	
				// TODO: $this->session->getSessionVar($value) verhindert, dass Parser nicht Standalon lauff�hig ist, wenn Makro {ISSET:var} verwendet wird
			default:
				if (
					$this->db_values[$value] 
					|| $this->getVariable($value, $this->vars)
					|| $this->getVariable($value, $this->evalvars)
					|| $this->postvars[$value]
					|| $this->getvars[$value]
					|| $this->pagevars[$value]
					|| $this->session->getSessionVar($value)
					|| defined($value)
					|| $this->navvars[$this->navlevel][$value]) {
						
					$replaceData = 'true';
				} else {
					$replaceData = 'false';
				}
				break;
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_PARENT()
	 *
	 * Type: Template macro
	 * Usage: {PARENT:varName:optionalParameters}
	 * Parameters: varName => name of a field in table cmt_pages_(language), optional parameters
	 * Example:<b>Page category: {PARENT:cmt_title:ucfirst}
	 * Description: Returns the value of a variable created in a PHP-Script and stored in the session before.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Value of a named property of the parent page.
	 */
	protected function macro_PARENT($value, $params) {
	
		$this->db->query("
			SELECT * FROM ".$this->pagesTable."
			WHERE id = '". $this->pagevars['cmt_parentid'] ."'"
				//AND id = '" . $this->pagevars['id'] ."'"
		);
	
		$r = $this->db->get();
	
		$replaceData = $r[$value];
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_INCLUDE()
	 *
	 * Type: Template macro
	 * Usage: {INCLUDE:filePath}
	 * Parameters: filePath => path to the include file.
	 * Example: {INCLUDE:PATHTOWEBROOT.'phpincludes/class_test.inc'}
	 * Description: Includes a file.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Output of the included file.
	 */
	protected function macro_INCLUDE($value, $params) {

		//$params = (array)$params;
		
		// eval is neccessary to allow constants in string / $value
		eval ('$fileName = ' . $value . ';');
 
		$file = file_get_contents($fileName);
			
		$file = preg_replace(
				array(
						'/^\<\?(php)?/i',
						'/\?\>$/'
				),
				array(
						'{EVAL}',
						'{ENDEVAL}'
				),
				trim($file)
		);

		switch ($params[0]) {
			case 'dontparse':
				$replaceData = $file;
				break;
				
			default:
				$replaceData = $this->parse($file);
				break;
		}
		
		return $replaceData;
		
	}
	
	/**
	 * protected function macro_ALTERNATIONFLAG()
	 *
	 * Type: Template macro
	 * Usage: {ALTERNATIONFLAG:startWith}
	 * Parameters: startWith => 0 or 1, alternation starts with this value.
	 * Example: <td class="row_{ALTERNATIONFLAG}">my table data</td>
	 * Description: Returns value '0' or '1' alternating on every call.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Alternating '0' and '1'
	 */
	protected function macro_ALTERNATIONFLAG($value, $params) {
		if (trim($value) != '') {
			$this->alternationFlag = intval($value)%2;
		}
		return ($this->alternationFlag++)%2;
	}
	
	/**
	 * protected function macro_LAYOUTMODE()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE}
	 * Parameters: none
	 * Example: {IF ({LAYOUTMODE})}<div class="message">Template is in layoutmode.</div>{ENDIF}
	 * Description: Returns 'true' or 'false' whether the page is displayed in the layout mode of the backend or in the frontend view.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string 'true' or 'false'
	 */
	protected function macro_LAYOUTMODE($value, $params) {
		return 'false';
	}
	
	/**
	 * protected function macro_LAYOUTMODE_STARTSCRIPT()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE_STARTSCRIPT}
	 * Parameters: none
	 * Example: <body>{LAYOUTMODE_STARTSCRIPT} ...
	 * Description: Includes neccessary scripts and HTML in layout mode of the backend. Place this macro allways directly after the opening <body> tag.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Internal scripts for layout mode in backend.
	 */
	protected function macro_LAYOUTMODE_STARTSCRIPT($value, $params) {
		return '';
	}
	
	/**
	 * protected function macro_LAYOUTMODE_ENDSCRIPT()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE_ENDSCRIPT}
	 * Parameters: none
	 * Example: ... {LAYOUTMODE_ENDSCRIPT}</body>
	 * Description: Includes neccessary scripts and HTML in layout mode of the backend. Place this macro allways directly before the closeing </body> tag.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Internal scripts for layout mode in backend.
	 */
	protected function macro_LAYOUTMODE_ENDSCRIPT($value, $params) {
		return '';
	}
	
	protected function macro_PREVIEWMODE($value, $params) {
		return 'false';
	}
	
	/**
	 * protected function macro_FILE()
	 *
	 * Type: Template macro
	 * Usage: {FILE:optionalParameters}
	 * Parameters: optional parameters
	 * Example: <li>Filename: {FILE}</li>
	 * Description: Returns a filename in a {LOOP PATH()} loop.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Filename
	 */
	protected function macro_FILE($value, $params) {
		$replaceData = $this->loopFile;
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_FILEPATH()
	 *
	 * Type: Template macro
	 * Usage: {FILEPATH:optionalParameters}
	 * Parameters: optional parameters
	 * Example: <li>Filepath: {FILEPATH}</li>
	 * Description: Returns a complete filepath (filename and its path) in a {LOOP PATH()} loop.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Filepath (filename and path)
	 */
	protected function macro_FILEPATH($value, $params) {
		$replaceData = $this->loopFilePath;
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_I18N()
	 *
	 * Type: Template macro
	 * Usage: {I18N:optionalParameters}
	 * Parameters: optional parameters
	 * Example: <td class="head">{I18N:projects.costs}</td><td class="data">1.000 EUR</td>
	 * Description: Gets the value for a string id from a translation table. Translation table must be first initialized with Parser::setI18NTable()
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string Filepath (filename and path)
	 */
	protected function macro_I18N($value, $params) {
		if ($this->i18nTable) {
	
			$lang = trim(array_shift($params));
			if (!$lang) {
				$lang = $this->i18nLanguage;
			}
				
			$addWhere = '';
			if (!$lang) {
				$addWhere = " AND " . $this->i18nLangField ." = '" . $lang . "'";
			}
				
			$this->db->query("
				SELECT " . $this->i18nTextField . "
				AS translation
				FROM " . $this->i18nTable . "
				WHERE " . $this->i18nIDField . " = '" . $this->db->dbQuote($value) . "'" .
				$addWhere
			);
				
			$r = $this->db->get();
			$replaceData = trim($r['translation']);
		} else {
			$replaceData = $value;
		}
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}

	/**
	 * public function macro_COUNT()
	 * Returns the amount of items in an array
	 *
	 * @param string $value Name of variable to count
	 * @param array $params void
	 *
	 * @return string 	String representation of the number of items in this array
	 */
	public function macro_COUNT ($value, $params) {
	
		if (!isset($this->vars[$value])) {
			$replaceData = '0';
		}
		else if (!is_array($this->vars[$value])) {
			$replaceData = '0';
		}
		else {
			$replaceData = sprintf('%s', count((array)$this->vars[$value]));
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_DEBUG
	 * 
	 * Output expression as debug output
	 * @param $value: The expression to debug
	 * @param $params: unused
	 * 
	 * @return void
	 */
	protected function macro_DEBUG($value, $params) {
		return Debug::debug($this->vars[$value]);
	}

	/**
	 * public function macro_UNIQUEID()
	 * Generates a new unique id or returns a previousely generated one
	 *
	 * @param string $value		Optional: 'new' generates a new id, leave this parameter empty to use the previousely generated one.
	 * @param array $params		Additional parameters in associative array
	 *
	 * @return string			Unique id as string
	 */
	protected function macro_UNIQUEID ($value, $params) {
	
		switch($value) {
				
			case 'new':
				$replaceData = uniqid();
				$this->uniqueId = $replaceData;
				break;
	
			default:
				$replaceData = $this->uniqueId;
				break;
		}
	
		if ($params[0]) {
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function processMacroValue()
	 * Calls a userdefined method or function for a value. This method is used to optionally process macro values.
	 *
	 * @param string $functionName Name of the parser method or global function that should be called.
	 * @param mixed $value Value/ content created by the macro before
	 * @param array $params Additional parameters in associative array
	 *
	 * @return mixed Return value of chosen method/ function. Will be written in output template.
	 */
	protected function processMacroValue($functionName, $value, $params=array()) {
	
		$functionName = trim($functionName);
	
		// call a parser method?
		if (method_exists($this, $functionName)) {
			if (!empty($params)) {
				return $this->$functionName($value, $params);
			} else{
				return $this->$functionName($value);
			}
		}
	
		// call a global function?
		if (is_callable($functionName)) {
			if (!empty($params)) {
				return $functionName($value, $params);
			} else{
				return $functionName($value);
			}
		}
	
		return $value;
	}	

	/**
	 * public function parse_controls()
	 * OUTDATED/DEPRECATED: Old method call, don't use this, use instead Parser:parseControls().
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function parse_controls($string) {
		return $this->parseControls($string);
	}
	
	/**
	 * public function parseControls()
	 * Parses control structures like {IF ()}...{ELSE}...{ENDIF}
	 *
	 * @param string $string Content to parse.
	 * @return string Parsed content.
	 *
	 */
	public function parseControls($string) {

		$regexpControls =  '/\{(([A-Z\s]+)([0-9]*))' .	// Makro mit Zahl
							'\s*' .		// event. Leerzeichen
							'(\((.*?)\))?\}(.*)\{END(\1)\}/s';
		preg_match_all ($regexpControls, $string, $matches);
		
		$counted_makros = count($matches[0]);
		$mc = 0;

		while ($mc <= $counted_makros) {
			$string = str_replace ($matches[0][$mc], '{now parsing}', $string);

		
			$control = $matches[2][$mc];
			$controlNumber = $matches[3][$mc];
			$condition = $matches[5][$mc];
			$contentToParse = $matches[6][$mc];
			
			$mc++;

			$controlMethod = 'control_' . str_replace(' ', '_', $control);
			if (method_exists($this, $controlMethod)) {
				$content .= $this->$controlMethod($condition, $contentToParse, $controlNumber);
			}

			$string = preg_replace ('/{now parsing}/', $content, $string);

			$content = '';
		}
		return $string;
	}

	/**
	 * protected function control_LOOP_CONTENT()
	 *
	 * Type: Template macro
	 * Usage: {LOOP CONTENT(colNumber)}<p>This is optional output between the layout objects!</p>{ENDLOOP CONTENT}
	 * Parameters: Mandatory "colNumber", a number of the content column/ group.
	 * Example: see usage
	 * Description: Generates the content output.
	 *
	 * @param string $condition The condition/ parameter in brackets (here: colNumber)
	 * @param string $content Optional content that will be inserted between the content elements
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Output content
	 */
	protected function control_LOOP_CONTENT($condition, $content='', $controlNr=0) {
		// Objekte werden im Formular wie folgt gekennzeichnet:
		// Gruppe_Position_Feld
		// Bsp: "1_4_text1" -> Gruppe 1, Position 4, Feld: "text1"
			
		// Neue Content-Spalte/ -Gruppe
		$this->content_groups++;
			
		// TODO: wird der separate $obj_parser noch benötigt??? 2014-04-08: Ja. Klären ob auch ohne möglich.
		$obj_parser = new Parser();
		
		$obj_parser->evalvars = $this->evalvars;
		$obj_parser->setPageVars($this->pagevars);
		$obj_parser->content_data = $this->content_data;
		
		$obj_parser->setPagesTable($this->pagesTable);
		$obj_parser->setContentsTable($this->contentsTable);
		$obj_parser->setLinksTable($this->linksTable);
	
		$obj_parser->setPageId($this->pageId);
		$obj_parser->setParentId($this->parentId);
		$obj_parser->setPathToWebroot($this->pathToWebroot);
		
		$submatch = explode(":", $condition);
		$obj_group = $this->db->dbQuote($submatch[0]);
		$replaceData = '';
			
		// Ansichts-Modus
		$query = "SELECT c.*, o.cmt_source 
				  FROM ".$this->contentsTable." c
				  LEFT JOIN cmt_templates_objects o
				  ON o.id = c.cmt_objecttemplate 
				  WHERE cmt_pageid = '" . $this->pageId . "' 
				  AND cmt_visible = '1' 
				  AND cmt_objectgroup = '". $obj_group . "' 
				  ORDER BY cmt_position";
	
		$this->db->Query($query);
	
//		$c = 0;

		
		$contentObjects = array();
		
		while ($r = $this->db->get()) {
			$obj_parser->content_data = $r;
			$obj_parser->contentGroup = $obj_group;
			$contentObjects[] = $obj_parser->parse(stripslashes($r['cmt_source']));
		}
		$this->evalvars = $obj_parser->evalvars;
		return implode($content, $contentObjects);
	}
	
	/**
	 * protected function control_LOOP_NAVIGATION()
	 *
	 * Type: Template macro
	 * Usage: {LOOP NAVIGATION(parentPageID)}<li><a href="{NAVIGATION:link}">{NAVIGATION:title}</a></li>{ENDLOOP NAVIGATION}
	 * Parameters: Optional but usefull "parentPageID". The macro loops all child pages of the page with the "parentPageID". If it is not given, the children of the page defined as "homepage" are looped.
	 * Example: see usage
	 * Description: Generates the navigation output.
	 *
	 * @param string $condition The condition/ parameter in brackets (here: parentPageID)
	 * @param string $content Navigation content to parse
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */	
	protected function control_LOOP_NAVIGATION($condition, $content, $controlNr=0) {
				
		$db = new DBCex();
		$db->setCharset(CHARSET);
		
		$condition = $this->parseMacros($condition);
			
		$params = explode (":", $condition);
		$parentid = $params[0];
			
		// andere Sprachversion?
		if ($params[1]) {
			$tableLanguage = trim($params[1]);
			$tablePages = $db->dbQuote('cmt_pages_' . $db->dbQuote($params[1]));
		} else {
			$tableLanguage = $this->pageLanguage;
			$tablePages = $this->pagesTable;
		}
			
		// Look for "cmt_isroot = '1'" when no root page id is given in parameters!
		if (!$parentid) {
			$db->query("SELECT id FROM ". $tablePages . " WHERE cmt_isroot='1' ORDER BY cmt_pagepos ASC LIMIT 1");
			$r = $db->get();
			$parentid = $r['id'];
		}
			
		$db->query("SELECT * FROM " . $tablePages . " WHERE cmt_parentid='" . $parentid . "' AND cmt_showinnav = '1' ORDER BY cmt_pagepos ASC");
			
		$this->navlevel++;
		$this->navElementPosition = 1;
			
		while ($r = $db->get()) {
			$r['cmt_language'] = $tableLanguage;
			$this->navvars[$this->navlevel] = $r;
			$replaceData .= $this->parseControls($content);
			$replaceData = $this->parseMacros($replaceData);
	
			$makros_not_parsed = false;
			$this->navElementPosition++;
				
		}
		$this->navlevel--;
		unset ($db);
		return $replaceData;		
	}
	
	/**
	 * function control_LOOP_VAR()
	 * 
	 * Type: Template macro
	 * Usage: {LOOP VAR(varName)}<li>{VAR:myVar1} - {VAR:myVar2}</li>{ENDLOOP VAR}
	 * Parameters: - 
	 * Example: 
	 * <?php 
	 * 		$this->parser->setParserVar('list', array(
	 * 			array('myVar1' => 'row one', 'myVar2' => 1),
	 * 			array('myVar1' => 'row two', 'myVar2' => 22) 
	 * 		));
	 * 		$content = $parser->parseTemplate('template_with_loop_var.tpl');
	 * ?>
	 * Description: Loops an array that was stored before as a parser variable.  
	 * 
	 * @param string $condition The condition/ parameter in brackets (here: parentPageID)
	 * @param string $content Navigation content to parse
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */
	protected function control_LOOP_VAR ($condition, $content, $controlNr=0) {
	
		$condition = $this->parseMacros($condition);
		$submatch = explode(":", $condition);
		
		$varName = $submatch[0];
		$cacheVars = $this->vars;
		$var = $this->vars[$varName];

		if (!empty($submatch[1])) {
			switch ($submatch[1]) {
				case 'USERVAR':
					$var = $this->evalvars[$varName];
					break;
				case 'PAGEVAR':
					$var = $this->pagevars[$varName];
					break;
				case 'POST':
					$var = $this->postvars[$varName];
					break;
				case 'GET':
					$var = $this->getvars[$varName];
					break;
				case 'FIELD':
					$var = $this->db_values[$varName];
					break;
				case 'SESSION':
					$var = $this->session->getSessionVar($varName);
					break;
				case 'CONTENT':
					$var = $this->content_data[$varName];
					break;
				case 'NAVPAGEVAR':
					$var = $this->navvars[$this->navlevel][$varName];
					break;
				default:
					$var = $this->vars[$varName];
					break;
			}
		}

		$replaceData = '';
		
		foreach((array)$var as $v) {
			$this->setMultipleParserVars((array)$v);
			$replaceData .= $this->parse($content);

			// delete vars
			foreach ((array)array_keys($v) as $key) {
				unset($this->vars[$key]);
			}
		}

		// restore vars (do we need this???)
		$this->vars = $cacheVars;
		
		return $replaceData;
	}

	/**
	 * protected function control_SWITCH()
	 *
	 * Type: Template macro
	 * Usage: {SWITCH ("{VAR:test}")}
	 * 			{CASE ("1")}
	 * 				<p><b>Var 'test' is 1</b></p>
	 * 				{BREAK}
	 * 			{CASE ()}
	 * 				<p>Default without condition in brackets: 'test' is not 1 but something else.</p>
	 * 				{BREAK}
	 * 		  {ENDSWITCH}
	 * Parameters: SWITCH(condition), e.g. "1" or "{VAR:myVar}", CASE(condition), e.g. different conditions for different cases. No condition given means that the case is the default case.
	 * Example: see usage
	 * Description: switch/case algorythm
	 *
	 * @param string $condition The SWITCH condition!
	 * @param string $content content to parse
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */
	protected function control_SWITCH($condition, $content, $controlNr=0) {
		$condition = $this->parseMacros($condition);
			
		preg_match_all('/\{CASE' . $controlNr . '\s?\(([^)]*)\)}(.*?)\{BREAK\}/s', $content, $match);
		$conditions = $match[1];
		$contents = $match[2];
			
		$evalContent = '';
		foreach ($conditions as $key => $case) {
	
			$case = $this->parseMacros($case);
			$case = preg_replace(array('/^"/', '/"$/') , '', trim($case));

			if ($case) {
				$evalContent .= 'case "' . $case . '": ';
			} else {
				$evalContent .= 'default: ';
			}
			$evalContent .= '$replaceData .= "' . addslashes($contents[$key]) .'"; break;';
	
		}
		//var_dump ('switch (' . $condition . '){' . $evalContent .'}');
		eval('switch (' . $condition . '){' . $evalContent .'}');
		return $this->parse(stripslashes($replaceData));
	}

	
	/**
	 * protected function control_IF()
	 *
	 * Type: Template macro
	 * Usage: {IF ("{VAR:myVar}" == "1")}<b>my var is 1!</b>{ELSE}<i>my var is not 1!</i>{ENDIF}
	 * Parameters: case to decide
	 * Example: see usage
	 * Description: if/else algorythm
	 *
	 * @param string $condition The if/else condition 
	 * @param string $content content to parse
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */	
	protected function control_IF($condition, $content, $controlNr=0) {
		
		$level = 0;
		$else = explode('{ELSE'.$controlNr.'}', $content);
			
		$this->layoutModeRawData = true;
		$condition = trim($this->parseMacros($condition));
		$this->layoutModeRawData = false;
			
		if (!$condition) {
			$condition = 'false';
		}
		
		eval ("if ($condition) {\$content = \$else[0];} else {\$content = \$else[1];}");
		
		$replaceData = $this->parseControls($content);
		return $this->parseMacros($replaceData);		
	}

	/**
	 * protected function control_EVAL()
	 *
	 * Type: Template macro
	 * Usage: {EVAL}
	 * 				$content = "<p>This text is shown where the the eval bloc is in the source code of the template.</p>";
	 * 				echo "But this text is shown before the HTML output! Bad idea.";
	 * 				$myVar = 1; // $myVar is available after this eval bloc in the template as "{USERVAR:myVar}" and in following eval blocs as PHP variable $myVar. 
	 * 		  {ENDIF}
	 * Parameters: void
	 * Example: see usage
	 * Description: Executes PHP code.
	 *
	 * @param string $condition void
	 * @param string $content PHP content to execute
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */	
	protected function control_EVAL($condition, $content, $controlNr=0) {
		return $this->eval_user_code ($content);
	}

	/**
	 * protected function control_LOOP_TABLE()
	 *
	 * Type: Template macro
	 * Usage: {LOOP TABLE(tableName:queryAdd)}<tr><td>{VAR:id}</td><td>{VAR:field_name}</td></tr>{ENDLOOP TABLE}
	 * Parameters: "tableName" = guess what, "queryAdd" = optional query part, e.g. "WHERE id > '12' ORDER BY position LIMIT 10"
	 * Example: see usage
	 * Description: Shows the content of a database table
	 *
	 * @param string $condition Table name and optional query part
	 * @param string $content Row content to fill in table's data.
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */	
	protected function control_LOOP_TABLE($condition, $content, $controlNr=0) {

		$condition = $this->parseMacros($condition);
		$submatch = explode(":", $condition);
			
		$tableName = $this->parseMacros($submatch[0]);
		$where_clause = $this->parseMacros(trim($submatch[1]));
		
		$replaceData = '';
		
		if ($tableName == 'all') {
			$allTables = $this->db->getAllTables();
			sort($allTables);
		
			if (is_array($allTables)) {
				foreach($allTables as $tableName) {
					$this->db->query("SELECT * FROM cmt_tables WHERE cmt_tablename = '".$tableName."' AND cmt_type = 'table'");
					$r = $this->db->get();
					$r['cmt_tablename'] = $tableName;
					$this->db_values = $r;
					$content_bit = $this->parseControls($content);
					$this->db_values = $r;
					$replaceData .= $this->parseMacros($content_bit);
				}
			}
		} else {
			$this->cmt_dbtable = $tableName;
		
			$query = 'SELECT * FROM '.$this->cmt_dbtable.' '.$where_clause;
			$this->db->query($query);
		
			while ($r = $this->db->get()) {
				$this->db_values = $r;
				$content_bit = $this->parseControls($content);
				$this->db_values = $r;
				$replaceData .= $this->parseMacros($content_bit);
			}
		}
		
		return $replaceData;
	}

	/**
	 * protected function control_DIRECTORY()
	 *
	 * Type: Template macro
	 * Usage: {LOOP DIRECTORY(directoryPath:additionalParams)}<li>{FILEPATH}: <b>{FILE}</b></li>{ENDLOOP DIRECTORY}
	 * Parameters: "directoryPath" = guess again, "additionalParams" = optional, ':' spearated parameters as used in method FileHandler::showDirectory(), e.g. "showDirectories=false"
	 * Example: see usage
	 * Description: Shows the content of a directory
	 *
	 * @param string $condition Directory name and optional query part
	 * @param string $content Row content to fill in directory's data.
	 * @param number $controlNr Optional and internal number of the current control/ loop
	 *
	 * @return string Navigation output content
	 */	
	protected function control_LOOP_DIRECTORY($condition, $content, $controlNr=0) {
		
		require_once(PATHTOADMIN.'classes/class_filehandler.php');
		$fh = new fileHandler();
			
		$condition = $this->parseMacros($condition);
		$submatch = explode(':', $condition);
			
		$dirParams['directory'] = $submatch[0];
			
		preg_match_all ('/([^=;]*)=([^;]*)/is', $submatch[1], $match);
		
		if (is_array($match[1])) {
			foreach ($match[1] as $key => $p) {
				$dirParams[trim($p)] = trim($match[2][$key]);
			}
		}
		
		$directoryStructure = $fh->showDirectory($dirParams);
		unset($dirParams);

		$replaceData = '';
		
		if (is_array($directoryStructure)) {
			foreach ($directoryStructure as $filePath => $file) {
				$this->loopFile = &$file;
				$this->loopFilePath = &$filePath;
				$content_bit = $this->parseControls($content);
				$replaceData .= $this->parseMacros($content_bit);
					
			}
		}
		
		return $replaceData;
	}
	
	/**
	 * public / private / protected function eval_user_code()
	 * Enter description here ...
	 *
	 * @param unknown $cmt_evalcode
	 *
	 * @return return_type
	 */
	public function eval_user_code ($cmt_evalcode) {
	
		$evalCode = new evalCode();

		// IMPORTANT: Due to backwards compatibility the old vars with underscore (e.g. '$cmt_tabledata') are provides for own scripts.
		// But changes can only be made in the new vars/ arrays with the camelized notation (e.g. '$cmtTableData', '$cmtContent')!!! 
		$vars = array (
			'cmtTableDataRaw' => $this->db_values,
			'cmtContentData' => $this->content_data,
			'cmtPageData' => $this->pagevars,
			'cmtNavigationData' => $this->navvars[$this->navlevel],
			'cmtTableData' => $this->db_values_formatted,
			'cmtContentGroup' => $this->contentGroup,

			'cmt_tabledata' => $this->db_values,
			'cmt_tabledata_formatted' => $this->db_values_formatted,
			'cmt_content' => $this->content_data,
			'cmt_pagevars' => $this->pagevars,
			'cmt_navvars' => $this->navvars[$this->navlevel],
			'cmt_contentgroup' => $this->contentGroup
		);	
		//var_dump($this->evalvars);			
		$vars = array_merge($this->evalvars, $vars);
		$this->cmt->setVars($vars);

//		var_dump($vars['cmtTableDataRaw']['cmt_pass']);
	
		$content = $evalCode->evalCode($cmt_evalcode);
		$vars = $this->cmt->getVars();
		
		$this->db_values_formatted = $vars['cmtTableData'];
		$this->db_values = $vars['cmtTableDataRaw'];

		$this->content_data = $vars['cmtContentData'];
		$this->pagevars = $vars['cmtPageData'];
		$this->navvars[$this->navlevel];
		
		// TODO: bad workaround! Scripts must communicate via Contentomat::setVar and ::getVar!
		unset($vars['cmtTableData']);
		unset($vars['cmtTableDataRaw']);
		unset($vars['cmtContentData']);
		unset($vars['cmtPageData']);
		unset($vars['cmtNavigationData']);
		unset($vars['cmtTableDataFormatted']);
		unset($vars['cmtContentGroup']);
		
		if (is_array($vars)) {
			$this->evalvars = array_merge($this->evalvars, $vars);
		}
		// --> workaround end
		
		// return generated content
		return $content;

	}
	
	////////////////////////////////////////////
	// Parservariablen speichern
	////////////////////////////////////////////


	/**
	 * public function setVar()
	 * Alias for DEPRECATED Parser::setParserVar()
	 *
	 * @param string $name Variable's name
	 * @param mixed $value Variable's value
	 *
	 */
	public function setVar($name, $value) {
		return $this->setParserVar($name, $value);
	}
	
	/**
	 * public function setVars()
	 * Alias for DEPRECATED Parser::setMultipleParserVar()
	 *
	 * @param array $name Variable's in an array (key = var name, value = var value)
	 * @param mixed $value Variable's value
	 *
	 */
	public function setVars($vars) {
		return $this->setMultipleParserVars($vars);
	}
	
	public function setUserVar($name='', $value='') {
		$this->evalvars[$name] = $value;
		return;
	}

	public function setMultipleUserVars($vars) {
		if (!is_array($vars)) {
			$vars[0] = $vars;
		}
		$this->evalvars = array_merge($this->evalvars, $vars);
		return;
	}
	
	public function unsetUserVar($name='') {
		$value = $this->vars[$name];
		unset($this->evalvars[$name]);
		return $value;
	}
	
	public function setParserVar($name='', $value='') {
		$this->vars[$name] = $value;
	}
	
	public function getParserVar($name='') {
		return $this->vars[$name];
	}

	public function getUserVar($name='') {
		return $this->evalvars[$name];
	}
	
	public function unsetParserVar($name="") {
		$value = $this->vars[$name];
		unset($this->vars[$name]);
		return $value;
	}

	public function deleteParserVar($name) {
		if (!$name) return;
		$this->unsetParserVar($name);
	}

	public function setMultipleParserVars($vars) {
		if (!is_array($vars)) {
			$vars[0] = $vars;
		}
		$this->vars = array_merge($this->vars, $vars);
		return;
	}

	public function deleteAllParserVars() {
		$this->vars = array();	
	}
	
	public function setPageVars($pageVars) {
		
		if (!is_array($pageVars)) {
			return false;
		}
		$this->pagevars = $pageVars;
	}
	
	public function setPageVar($varName, $varValue) {
		
		if (!$varName) {
			return false;
		}
		$this->pagevars[$varName] = $varValue;
		
	}

	
	////////////////////////////////////////////
	// Dateien parsen
	////////////////////////////////////////////
	
	public function parseTemplate ($file='') {
		if ($file) {
			$filedata = implode ('', file($file));
			return $this->Parse($filedata);
		} else {
			return false;
		}
	}		

	public function getAllPageLinks() {
		
		$links = array();
		$linkData = array();

		$this->db->Query("SELECT * FROM ".$this->linksTable." WHERE cmt_linkonpage = '".$this->pageId."'");
		$linkData = $this->db->getAll();
		
		foreach ($linkData as $r) {
			
			// 2015-12-23: Do this for backwards compatibility reasons
			if ($r['cmt_type'] == 'mailto') {
				$r['cmt_type'] = 'email';
			}

			switch ($r['cmt_type']) {
				case 'internal':

					$this->db->query("SELECT cmt_title, cmt_urlalias FROM cmt_pages_".$r['cmt_lang']." WHERE id = '".$r['cmt_page']."'");
					$p = $this->db->get();
					$r['cmt_title'] = $p['cmt_title'];
					$r['cmt_urlalias'] = $p['cmt_urlalias'];
					
					$link = '<a href="';
					
					if (CMT_MODREWRITE == '1') {
						if ($r['cmt_urlalias']) {
							$link .= '/'.$r['cmt_lang'].'/'.$r['cmt_page'].'/'.$r['cmt_urlalias'];
						} else {
							$link .= '/'.$r['cmt_lang'].'/'.$r['cmt_page'].'/'.$this->formatFilename($r['cmt_title']).'.html';
						}
						if (CMT_USECOOKIES == '0' && CMT_FORCECOOKIES == '0') {
							$link .= '?sid='.SID;
						}
					} else {
						$link .= SELF.'?pid='.$r['cmt_page'].'&amp;lang='.$r['cmt_lang'].'"';
						if (CMT_USECOOKIES == '0' && CMT_FORCECOOKIES == '0') {
							$link .= '&amp;sid='.SID;	
						}
					}
					$link .= '"';
				break;
	
				case 'external':
					$protocol = '';
					if (!preg_match('/^https?\:\/\//', trim($r['cmt_url']))) {
						$protocol = 'http://';
					}
					$link = '<a href="' . $protocol .trim($r['cmt_url']) . '"';
				break;

				case 'individual':
				case 'script':	// deprecated name! Use instead 'individual'!
					$link = '<a href="'.$r['cmt_url'].'"';
				break;

				case 'email':
					$link = '<a href="mailto:'.$r['cmt_url'].'"';
				break;
				
				case 'download':
					$link = '<a href=';
					if (CMT_MODREWRITE == '1') {
						$link .= '"/'.PATHTODOWNLOADS.$r['cmt_url'].'"';
					} else {
						$link .= '"'.PATHTODOWNLOADS.$r['cmt_url'].'"';
					}
				break;						
			}

			// Rest des Link-Tags erzeugen 
			if ($r['cmt_target']) {
				$link .= ' target="'.$r['cmt_target'].'"';
			}
			
			if ($link) {
				$link = trim($link.' '.$r['cmt_addhtml']).'>';
				$links[$r['id']] = $link;
			}
			
			//var_dump($r['id'] . ': ' .$link);
		}

		return $links;
	}
	
	// Überprüft rekursiv, ob eine Seite direkt mit einer darüberliegenden verwandt ist.
	public function get_ancestors($pageid, $ancestorid) {
		$this->db->Query('SELECT cmt_parentid FROM '.$this->pagesTable.' WHERE id = \''.$pageid.'\'');
		$r = $this->db->Get(MYSQLI_ASSOC);
		$pageid = intval($r['cmt_parentid']);
		//echo $pageid.'<br>';
		if ($pageid) {
			if ($pageid != $ancestorid) {
				//echo $pageid.'<br>';
				return $this->get_ancestors($pageid, $ancestorid);
				
			} else {
				//echo 'Treffer: '.$pageid.' ist verwandt mit '.PAGEID.'<br>';
				return 'true';
			}
		} else {
			return 'false';
		}
	}

	public function exec_external_file($filepath) {
		eval ('$file = '.$filepath.';');
		$file_to_parse = file_get_contents($file);
		
		$file_to_parse = preg_replace("/\<\?(php)?/i", "{EVAL}", $file_to_parse);
		$file_to_parse = preg_replace("/\?\>/", "{ENDEVAL}", $file_to_parse);
		
		return $this->parse($file_to_parse);		
	}

	/*TODO: Kurzzeitig eingebaut, da in index5.php diese Funktion wohl fehlt ==> ändern (2x im Parser verwendet)*/
	public function format_directory ($directory) {
		$directory = preg_replace ("/^\.?\//", "", $directory);
		$directory .= "/";
		$directory = preg_replace ("/\/{2,}/", "/", $directory);
		return $directory;
	}

	/**
	 * public function formatFilename()
	 * Formatiert/ s�ubert einen Dateinamen ('_' und Umlaute werden entfernt)
	 * 
	 * @param string Dateiname
	 * @return string ges�uberter Dateiname
	 */
	 
	public function formatFilename($name) {

		// Falls Klasse 'Contentomat' vorhanden, dann Luxus-Variante nutzen
		if ($this->cmt) {
			return $this->cmt->makeNameWebsave($name);
		} 
	}

	/**
	 * public function flattenArray()
	 * Reduziert ein multidimensionales auf ein eindimensionales Array zur Übergabe an den Parser
	 * 
	 * @param array $array
	 * 
	 * @return array
	 */
    public function flattenArray($array) {
		if (!is_array($array)) $array = array();

		$arrayResult = array();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$subArray = $this->flattenArrayProcess($value, $key);
				$arrayResult = array_merge($arrayResult, $subArray);
			} else {
				$arrayResult[$key] = $value;
			}
		}
		return $arrayResult;
	}
	
	/**
	 * public function flattenArrayProcess()
	 * Hilfsmethode für Methode flattenArray()
	 * 
	 * @param array $array
	 * @param string $keyPrefix Prefix welcher dem Array-Schlüssel vorangestellt wird. 
	 * 
	 * @return array
	 */		
	protected function flattenArrayProcess($array, $keyPrefix='') {

		$arrayResult = array();
		foreach ($array as $key => $value) {
			$keyName = $keyPrefix.'['.$key.']';
			
			if (is_array($value)) {
				$subArray = $this->flattenArrayProcess($value, $keyName);
				$arrayResult = array_merge($arrayResult, $subArray);
			} else {
				$arrayResult[$keyName] = $value;
			}
		}

		return $arrayResult;
	}
	
	/**
	 * public function protectMacros()
	 * Ersetz gechweifte Klammern durch nummerische Entitäten und "schützt" Makros so davor, ggf. ein zweiter Mal
	 * geparst zu werden. Ist z.B. bei Feldern sinnvoll, die zweimal geparst werden aber Makrobezeichnungen als Inhalt
	 * haben sollen.
	 * 
	 * @param String $str Zeichenkette
	 * @return String Gibt Zeichenkette mit ersetzen "{" und "}" zurück
	 * 
	 */
	public function protectMakros($str) {
		return $this->protectMacros($str);
	}
	
	public function protectMacros($str) {
		return str_replace(
			array( chr(123), chr(125)),
			array( '&#123;', '&#125;'),
			$str
		);
	}

	/**
	 * public function unprotectMacros()
	 * Gegenteil zu protectMakros: &#123; und &#125; werden durch { und } ersetzt
	 * 
	 * @param String $str Zeichenkette
	 * @return String Gibt Zeichenkette mit ersetzen Entitäten zurück
	 * 
	 */
	public function unprotectMakros($str) {
		return $this->unprotectMacros($str);
	}
	
	public function unprotectMacros($str) {
		return str_replace(
 			array( '&#123;', '&#125;', '&amp;#123;', '&amp;#125;'),
 			array( chr(123), chr(125), chr(123), chr(125)),
			$str
		);
	}
	
	/**
	 * public function getTemplate()
	 * Liest eine Templatedatei und gibt sie zurück.
	 *
	 * @param string $tplPath Dateipfad
	 *
	 * @return string Das Template
	 */
	public function getTemplate($tplPath) {
	
		return (string) file_get_contents($tplPath);
	
	}
	
	public function setI18NTable($params) {
		
		if (
			 !isset($params['table']) || 
			 !isset($params['idField']) ||
			 !isset($params['textField'])
//			|| !isset($params['languageField'])
		) {
			return false;
		}
		
		$this->i18nTable = $this->db->dbQuote($params['table']);
		$this->i18nIDField = $this->db->dbQuote($params['idField']);
		$this->i18nTextField = $this->db->dbQuote($params['textField']);
		$this->i18nLangField = $this->db->dbQuote($params['languageField']);
		
		return true;
	}
	
	public function setI18NLanguage($lang='') {
		$lang = trim($lang);
		if (!$lang) {
			return false;
		}
		$this->i18nLanguage = $this->db->dbQuote($lang);
		return true;
	}

	/*
	 * Setter, setter, more setter
	 */
	public function setPagesTable($tableName) {
		$tableName = str_replace(' ', '', trim($tableName));
		
		if (!$tableName) {
			return false;
		}
		
		$this->pagesTable = $tableName;
		return true;
	}
	
	public function setContentsTable($tableName) {
		$tableName = str_replace(' ', '', trim($tableName));
	
		if (!$tableName) {
			return false;
		}
	
		$this->contentsTable = $tableName;
		return true;
	}
	
	public function setLinksTable($tableName) {
		$tableName = str_replace(' ', '', trim($tableName));
	
		if (!$tableName) {
			return false;
		}
	
		$this->linksTable = $tableName;
		return true;
	}
	
	public function setPageId($pageId) {

		if (!intval($pageId) && $pageId != 'root') {
			return false;
		}
		
		if ($pageId == 'root') {
			$this->pageId = 'root';
		} else {
			$this->pageId = intval($pageId);
		}
		
		return true;
	}

	public function setParentId($parentId) {
	
		if (!intval($parentId) && $parentId != 'root') {
			return false;
		}
	
		if ($parentId == 'root') {
			$this->parentId = 'root';
		} else {
			$this->parentId = intval($parentId);
		}
		return true;
	}
	
	public function setPageLanguage($language) {
		
		$language = str_replace(' ', '', trim($language));
		
		if (!$language) {
			return false;
		}
		
		$this->pageLanguage = $language;
		return true;
		
	}
	
	public function setPathToWebroot($path) {
		$this->pathToWebroot = trim($path);
	}
	
	public function setPathToAdmin($path) {
		$this->pathToAdmin = trim($path);
	}
	
	/**
	 * protected function makeNameWebsave()
	 * Passes the string in $name to Contentomat::makeNameWebsave() method.
	 *
	 * @param string $name A name or something string linke
	 * @return string Returns a string with websave characters for usage in an URL.
	 */
	protected function makeNameWebsave($name) {
		return $this->cmt->makeNameWebsave($name);
	}
	
	public function cmt_htmlentities ($string) {
		$convert_special_chars['"'] = '&quot;';
		$convert_special_chars['<'] = '&lt;';
		$convert_special_chars['>'] = '&gt;';
		$convert_special_chars['&'] = '&amp;';	// TODO: Das hier aber nochmal genau testen!!!
		$convert_special_chars['\\'] = '&bsol;';
		// $convert_special_chars['�'] = '&iexcl;';
		
		return strtr ($string, $convert_special_chars);
	}

}
?>
