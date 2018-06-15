<?php
/**
 * class_cmtparser.inc
 * Extends the base parser class and provides advanced functionalities for the administration area.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2014-09-01
 *
 */
namespace Contentomat;

Class CMTParser extends Parser {
	
	// template path
	protected $defaultTemplateBasePath = 'templates/default/';
	protected $userTemplateBasePath = '';
	protected $user;
	
	public function __construct() {
		parent::__construct();
		
		$this->user = new User($this->cmt->session->getSessionID());
		$this->userTemplateBasePath = 'templates/' . $this->user->getUserCMTTemplate();
	}
	
	/**
	 * public function setDefaultTemplateBasePath()
	 * Sets the default template path for the class. This template path will be used if the template file 
	 * generated with the variable $userTemplateBasePath is not found/ does not exist. 
	 * 
	 * @param unknown $path
	 * @return boolean
	 */
	public function setDefaultTemplateBasePath($path) {
		if (is_dir($path)) {
			$this->defaultTemplateBasePath = $path;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function setUserTemplateBasePath()
	 * Sets the user template path for the class.
	 *
	 * @param unknown $path
	 * @return boolean
	 */
	public function setUserTemplateBasePath($path) {
		if (is_dir($path)) {
			$this->userTemplateBasePath = $path;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function getTemplate()
	 * Loads a template from $templatePath. The path must not contain the path to the main templates folder ('/admin/templates/').
	 * First, the method tries to load the template in the set users template path. If this file does not exists, the method tries to load the file from the default templates path.
	 * 
	 * @see \Contentomat\Parser::getTemplate()
	 */
	public function getTemplate($templatePath) {
	
		$templatePath = $this->cleanTemplatePath($this->parse($templatePath));
		
		// file exists => return it
		if (is_file($this->userTemplateBasePath . $templatePath)) {
			return file_get_contents($this->userTemplateBasePath . $templatePath);
		}
	
		// file doesn't exist
		if (is_file($this->defaultTemplateBasePath . $templatePath)) {
			return file_get_contents($this->defaultTemplateBasePath . $templatePath);
		}
	
		return '';
	}

	/**
	 * public function parseTemplate()
	 * Locates, loads and parses a template. Do not prefix the path to main templates folder ('/admin/templates') to $templatesPath.
	 * 
	 * @see \Contentomat\Parser::parseTemplate()
	 */
	public function parseTemplate($templatePath='') {
		
		$templateSource = $this->getTemplate($templatePath);
		return $this->parse($templateSource);
	}
	
	
	/**
	 * protected function macro_CMT_TEMPLATE()
	 * Returns the templates's path whether with the user settings template path or the default path prefixed.
	 *   
	 * @see \Contentomat\Parser::macro_CMT_TEMPLATE()
	 */
	protected function macro_CMT_TEMPLATE($templatePath, $params) {
		
		$templatePathRaw = $templatePath;
	
		$templatePath = $this->cleanTemplatePath($this->parse($templatePath));

		// 1. try user template
		if (is_file($this->userTemplateBasePath . $templatePath)) {
			return $this->userTemplateBasePath . $templatePath;
		}
		
		// 2. try default template
		if (is_file($this->defaultTemplateBasePath . $templatePath)) {
			return $this->defaultTemplateBasePath . $templatePath;
		}

		// 3. due to compatibility reasons: try constant
		if (defined('CMT_TEMPLATE')) {
			if (isset($templatePath)) {
				return CMT_TEMPLATE . $templatePath;
			} else {
				return CMT_TEMPLATE;
			}
		}
		
		return '';
		
	}
	
	/**
	 * protected function cleanTemplatePath()
	 * Eliminates the path to admin and templates folder from $templatePath.
	 * 
	 * @param string $templatePath
	 * @return string Cleaned path string.
	 */
	protected function cleanTemplatePath($templatePath) {
		
		// prepare template path for backwards compatibility reasons
		$adminPath = $this->cmt->getPathToAdmin();
		$templatePath = preg_replace('/^\/?' . preg_quote($adminPath, '/') . '/', '', $templatePath);
		$templatePath = preg_replace('/^\/?' . preg_quote($this->defaultTemplateBasePath, '/') . '/', '', $templatePath);
		$templatePath = preg_replace('/^\/?' . preg_quote($this->userTemplateBasePath, '/') . '/', '', $templatePath);
		
		return $templatePath;
	}
}
?>