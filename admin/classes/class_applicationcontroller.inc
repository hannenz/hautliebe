<?php
namespace Contentomat;

/**
 * class ApplicationController
 * 
 * @author J.Hahn, A.Al Kaissi <info@content-o-mat.de>
 * @version 2017-10-12
 */
class ApplicationController {
	/**
	 * Global Content-o-mat Variables
	 * 
	 * @var string $cmtContent
	 */
	// protected $cmtContent;

	/**
	 * Global Get Variables
	 * 
	 * @var array  $getvars
	 */
	protected $getvars;

	/**
	 * Global Post Variables
	 * 
	 * @var array $postvars
	 */
	protected $postvars;

	/**
	 * Global Request Variables
	 *
	 * @var array $requestvars
	 */
	protected $requestvars;

	/**
	 * Instance of Database Class
	 * 
	 * @var object $db
	 */
	protected $db;

	/*
	 * @var $cmt
	 */
	protected $cmt;

	/*
	 * @var $session
	 */
	protected $session;

	/*
	 * @var $user
	 */
	protected $user;

	/**
	 * Instance of Class Parser
	 * 
	 * @var object $parser
	 */
	protected $parser;

	/**
	 * Content-o-mat application ID
	 * 
	 * @var string $pageId
	 */
	protected $applicationID;

	/**
	 * ID of the application to return to after editing an data entry
	 * 
	 * @var string $returnToApplicationID 
	 */
	protected $returnToApplicationID;

	/**
	 * Set applications default templates path
	 * 
	 * @var string  $templatesPath 
	 */
	protected $templatesPath;

	/**
	 * Set applications default php include path
	 * 
	 * @var string $phpIncludesPath
	 */
	protected $phpIncludesPath;

	/**
	 * Set applications default classes path 
	 * 
	 * @var string $phpClassesPath
	 */
	protected $phpClassesPath;

	/**
	 * Current applications action
	 * 
	 * @var string  $action
	 */
	protected $action;

	/**
	 * Set Default Action Name, default is index
	 * 
	 * @var string  $defaultAction
	 */
	protected $defaultAction;

	/**
	 * Array of error messages
	 * 
	 * @var array $errorMessage 
	 */
	protected $errorMessages = array();

	/**
	 * Array of succeess mesages
	 * @var type 
	 */
	protected $successMessages = array();

	/**
	 * Bool Flag, to output content in ajax format
	 * 
	 * @var bool $isAjax 
	 */
	protected $isAjax;

	/**
	 * Bool Flag, output content as json encoded string
	 * 
	 * @var bool $isJson 
	 */
	protected $isJson;

	/**
	 * Output Content
	 * @var mixed $content
	 */
	protected $content;

	/**
	 * Tab number
	 * @var number cmtTab
	 */
	protected $tab;

	
	protected $debugMode;
	
	protected $debug;
	
	protected $cmtSettings;
	
	/**
	 * public function __construct()
	 */
	public function __construct($params = array()) {

// TODO: useless?
		foreach ($params as $paramKey => $paramValue) {
			$this->$paramKey = $paramValue;
		}

		$this->cmt = Contentomat::getContentomat();

		$this->session = $this->cmt->getSession();

// TODO: useless!?
		// Global Get Variables
		$this->getvars = $_GET;

		// Global Post Variables
		$this->postvars = $_POST;

		// Global Request Variables
		$this->requestvars = $_REQUEST;
// TODO End

		// Instance of Database Class
		$this->db = new DBCex();

		// Instance of Class Parser
		$this->parser = new CMTParser();

		// Instance of Class User
		$this->user = new User();
		
//		if ($this->debugMode){
			$this->debug = new Debug();
//		}
		
		// Current Content-o-mat page ID
		$this->applicationID = intval(trim($_REQUEST['cmtApplicationID']));
		if (!$this->applicationID) {
			$this->applicationID = intval(trim($_REQUEST['launch']));  // 2.0: rename to 'cmtApplicationID'
		} else if (!$this->applicationID) {
			$this->applicationID = CMT_APPID;
		}

		// Current Page Language shortcut
		$this->returnToApplicationID = trim($_REUQEST['cmtReturnToApplicationID']); // 2.0: rename to 'cmtReturnToApplicationID'
		if (!$this->returnToApplicationID) {
			$this->returnToApplicationID = trim($_REUQEST['cmt_returnto']);
		}

		// Get tabnumber (for compatibility easons also check 'cmt_slider')
// 		$this->tab = $_REQUEST['cmtTab'];
// 		if (!$this->tab) {
// 			$this->tab = intval($_REQUEST['cmt_slider']);
// 		}
		$this->tab = $this->cmt->getVar('cmtTab');
		
		// Set default frontend templates path
		$this->templatesPath = PATHTOWEBROOT . 'templates/';

		// TODO: Why???
		// Set default frontend php include path
		$this->phpIncludesPath = PATHTOWEBROOT . 'phpincludes/';

		// TODO: Why???
		// Set default frontend classes path
		$this->phpClassesPath = PATHTOWEBROOT . 'phpincludes/classes/';

		// Set Default Action Name, default is index
		$this->defaultAction = 'default';

		// Bool Flag, to output content in ajax format
		$this->isAjax = false;

		// Bool Flag, output content as json encoded string
		$this->isJson = false;

		$this->templatesPath = '';
		
		$appHandler = new ApplicationHandler();
		$this->cmtSettings = $appHandler->getApplicationSettings($this->applicationID);

		// Initialize actions
		$this->initActions();

		// pass some important system vars to parser
		$this->parser->setMultipleParserVars(array(
			'cmtApplicationID' => $this->applicationID,
			'cmtTab' => $this->tab,
			'cmtAction' => $this->action
		));

		// Initialize main component
		$this->init();
	}

	/**
	 * public function init()
	 * 
	 * overrides to extend __construct methode
	 * 
	 */
	public function init() {
		
	}

	
	protected function initDebugMode(){
		
	}
	/**
	 * protected function initActions()
	 * 
	 * set action type, from any GET or POST variable named has name "action"
	 * if not then take the default action name "index" as current action name.
	 * 
	 *  usually overrides in extended controller class 
	 * 
	 * @param string $action 
	 * @retrun void
	 */
	protected function initActions($action = '') {
		
		if (trim($action) != '' && !is_array($action)) {
			$this->action = trim($action);
		} else {
			
			if ($this->requestvars['cmtAction']) {

				// 'cmtAction' should be used!!!!
				$this->action = $this->requestvars['cmtAction'];
			} else if ($this->requestvars['cmt_action']) {
				
				// 'cmt_action' is DEPRECATED!!!!
				$this->action = $this->requestvars['cmt_action'];
			} else if ($this->requestvars['action']) {
				
				// 'action' should not be used!!!!
				$this->action = $this->requestvars['action'];
			} else {
				$this->action = $this->defaultAction;
			}
		}
	}

	/**
	 * protected function load()
	 * load php script files using include_once
	 * @param type $path 
	 */
	protected function load($path) {
		include_once($path);
	}

	/**
	 * Public function work()
	 * 
	 * Run Page action and return/output content
	 * 
	 * @return string 
	 */
	public function work() {
		$this->doAction($this->action);

		// if mixed variable $this->content is an array, must output it as 
		// json string, to not assign an array to cmt page $content variable
		if (is_array($this->getContent())) {
			$this->isJson = true;
		}

		// AJAX + JSON OUTPUT
		// JSON flag set also to true:
		// 1- convert output content to json format 
		// 2- print page content with echo
		// 3- and exit processing the rest of page
		if ($this->isJson) {
			
			$output = ob_get_flush();
			
			if ($output) {
				$this->content['phpOutput'] = $output;
			}
			
			echo json_encode($this->getContent());
			exit;
		}

		// AJAX OUTPUT
		// If Ajax action flag is set to true:
		// 1- print page content with echo
		// 2- and exit processing the rest of page
		elseif ($this->isAjax) {
			
			ob_clean();
			
			echo $this->getContent();
			exit;

			// RETURN STRING
			// return page content as HTML string
		} else {
			return $this->getContent();
		}
	}

	/**
	 * 	public function doAction()
	 * 
	 * Call page current action
	 * 
	 * @param string $action
	 * @return void 
	 */
	public function doAction($action) {
		if (!method_exists($this, 'action' . ucfirst($action))) {
			$action = $this->defaultAction;
		}

		$actionMethod = 'action' . ucfirst($action);

		$this->$actionMethod();

	}

	/**
	 * 	protected function changeAction()
	 * 
	 * redirect to action with/without reseting of resent output content
	 * @param type $action
	 * @param type $resetContent 
	 */
	protected function changeAction($action, $resetContent = true) {
		if ($resetContent) {
			$this->content = '';
		}
		$this->doAction($action);
	}

	/**
	 * public function getContent() 
	 * 
	 * get the content of page as HTML string
	 * 
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
	

	// Default Action
	/**
	 * public function actionIndex()
	 * 
	 * Default page action
	 * overrides in extended controllers  
	 * simulate the default: case, in switch statement
	 */
	protected function actionDefault() {
		//
	}

	/**
	 * protected function handleContentomatVars()
	 * Method to be called in extending(!) class: Gets the application vars out of the request and optionally 
	 * from the session or the user vars and assigns them as class variables.
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	protected function handleContentomatVars($params = array()) {

		$defaultParams = array(
			'vars' => array(),
			'sessionVars' => array(),
			'userVars' => array(),
			'scriptVars' => array()  // Is this option realy needed??? YES! Stupid solution!
		);

		$params = array_merge($defaultParams, $params);

		$cmtSessionVars = $this->session->getSessionVar('cmtApplicationVars');

		if(!is_array($cmtSessionVars)){
			$cmtSessionVars = array();
		}
		

		$cmtUserVars = $this->user->getUserVar('cmtApplicationVars');

		$classVars = array();

		// Get variables
		foreach ($params['vars'] as $varNameRaw => $varValue) {

		
			$varName = str_replace('[]', '', $varNameRaw);

			// 1. Is variable in REQUEST?
			if (isset($this->requestvars[$varName])) {
				$var = $this->requestvars[$varName];
				
			}

// TODO: 2016-08-17: makes only sense if the variable is in_array($params['sessionVars']
			// 2. Was variable stored as session variable before?
			if (!isset($var) && isset($cmtSessionVars[$this->applicationID][$this->tab][$varName])) {
				$var = $cmtSessionVars[$this->applicationID][$this->tab][$varName];
			}
// TODO: 2016-08-17: makes only sense if the variable is in_array($params['userVars']
			// 3. Was variable stored as uservariable before?
			if (!isset($var) && isset($cmtUserVars[$this->applicationID][$this->tab][$varName])) {
				$var = $cmtUservars[$this->applicationID][$this->tab][$varName];
			}

			// 4. Is variable stored as "real/ own" session variable (not in session array $cmtSessionVars)
			// Is this option realy needed???
			if (!isset($var) && $this->session->getSessionVar($varName)) {
				$var = $this->session->getSessionVar($varName);
			}

			// 5. Set default value if variable is not found anywhere
			if (!isset($var)) {
				if (stristr($varNameRaw, '[]') && !is_array($varValue)) {
					$var = array($varValue);
				} else {
					$var = $varValue;
				}
			}

			/*
			 * Store variables
			 */
			// store in application vars
			if (in_array($varName, $params['sessionVars'])) {

				$cmtSessionVars[$this->applicationID][$this->tab][$varName] = $var;
			}

			// store in user vars
			if (in_array($varName, $params['userVars'])) {
				$cmtUserVars[$this->applicationID][$this->tab][$varName] = $var;
			}

			// store as real session var
			// Is this option realy needed???
			if (in_array($varName, $params['scriptVars'])) {
				$this->session->setSessionVar($varName, $var);
			}

			$classVars[$varName] = $var;
			unset($var);
		}
		
		//var_dump($cmtSessionVars);
		// Create class vars
		$this->createClassVariables($classVars);

		// Save application variables in session
		$this->session->setSessionVar('cmtApplicationVars', $cmtSessionVars);
		
		
		// ToDo: brauchen wir das noch ?
		$this->session->saveSessionVars();

		// Save application variables in users variables
		$this->user->setUserVar('cmtApplicationVars', $cmtUserVars);

		$this->user->saveUserVars();
		
	}

	protected function createClassVariables($params = array()) {

		// assign name=>value parameters to global public variables in class
		if (!empty($params)) {
			foreach ($params as $varName => $value) {
				if (!is_numeric($varName)) {
					//$varName = str_replace ('[]', '', $varName);
					$this->$varName = $value;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	protected function setError($errorType) {
		$this->parser->setParserVar('cmtError', true);
		$this->parser->setParserVar('cmtErrorType', $errorType);
		
	}

	protected function setSuccess($successType) {
		$this->parser->setParserVar('cmtSuccess', true);
		$this->parser->setParserVar('cmtSuccessType', $successType);
	
	}
	
	protected function setWarning($warningType) {
		$this->parser->setParserVar('cmtWarning', true);
		$this->parser->setParserVar('cmtWarningType', $warningType);
	
	}

	protected function setInfo($infoType) {
		$this->parser->setParserVar('cmtInfo', true);
		$this->parser->setParserVar('cmtInfoType', $infoType);
	
	}
	
	/**
	 * public function setErrorMessage()
	 * 
	 * set an error message
	 * 
	 * @param string $errorTitle
	 * @param mixed $errorMessage 
	 */
	// 2015-01-26: unused???
	public function setErrorMessage($errorTitle, $errorMessage){
		$this->errorMessages[$errorTitle] = $errorMessage;
	}
	
	/**
	 * public function getErrorMessage()
	 * 
	 * return error message by error title $errorTitle
	 * 
	 * @param string $errorTitle
	 * @return mixed 
	 */
	// 2015-01-26: unused???
	public function getErrorMessage($errorTitle){
		$errorMessage = '';
		
		if(!$errorTitle){
			$errorMessage = $this->errorMessages[$errorTitle];
		}
		return $errorMessage;
	}
	
	
	/**
	 * public function getErrorMessages()
	 * 
	 * return all error messages
	 * 
	 * @return array 
	 */
	// 2015-01-26: unused???
	public function getErrorMessages(){
		return $this->errorMessages;
	}
	
	/**
	 * public function setSuccessMessage()
	 * 
	 * set a success message 
	 * 
	 * @param string $successTitle
	 * @param mixed $successMessage 
	 */
	// 2015-01-26: unused???
	public function setSuccessMessage($successTitle, $successMessage){
		$this->successMessages[$successTitle] = $successMessage;
	}
	
	/**
	 * public function getSuccessMessage()
	 * 
	 * return success message by titlle $successTitle
	 * 
	 * @param string $successTitle
	 * @return mixed 
	 */
	// 2015-01-26: unused???
	public function getSuccessMessage($successTitle){
		$successMessage = '';
		if($successTitle){
			$successMessage	= $this->successMessages[$successTitle];
		}
		return $successMessage;
	}
	
	/**
	 * public function getSuccessMessages()
	 * 
	 * return all success messages
	 * 
	 * @return array 
	 */
	// 2015-01-26: unused???
	public function getSuccessMessages(){
		return $this->successMessages;
	}
	
	/**
	 * public function log()
	 * 
	 * release firephp log if debugMode is active
	 * do nothing if debugMode is not active
	 * 
	 * @param type $message
	 * @param type $title 
	 */
	public function log($message, $title){
		if($this->debugMode){
			$this->debug->log($message,$title);
		}else{
			
		}
	}
}

?>
