<?php
/**
 * class_evalcode.inc
 * Executes external user scripts and handles the variables of this scripts for global usage.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 *
 */
namespace Contentomat;
 
Class EvalCode {
	
	private $cmt;
	private $evalVars = array();
	
	public function __construct() {
		$this->cmt = Contentomat::getContentomat();
	}
	
	public function evalCode($script) {
	
		$script = preg_replace(array('/\<\?(php)?/', '/\?\>/'), '', $script);
		// vars are available for non-classes (procedural scripts). 
		// This is OUTDATED: In external scripts variables should be passed to other scripts with the "setVar()" and the "setVars()" methods
		// of an instance of the Contentomat Singleton!
		extract ($this->cmt->getVars());	// eliminate this!
		extract ($this->evalVars);			// use this!
		
		// execute script
		eval($script);
		//var_dump(preg_replace(array('/\<\?(php)?/i', '/\?\>/'), '', $script));
			
		// save the vars genereated by the script for usage in following templates ({USERVAR:myVar}) and include scripts 
		$vars = get_defined_vars();

		unset($vars['this']);
		unset($vars['script']);
		unset($vars['content']);
		
		// TODO: isn't it better to save the vars here in EvalCode class?
		// 2015-12-14: YES IT IS!!! Old scripts won't work without this:
		$this->cmt->setVars($vars);

		if (is_array($vars)) {
			$this->evalVars = $vars;
		}

		return $content;
	}
	
	public function evalFile($file, $vars = array()) {
	
		// vars are available for non-classes (procedural scripts).
		// This is OUTDATED: In external scripts variables should be passed to other scripts with the "setVar()" and the "setVars()" methods
		// of an instance of the Contentomat Singleton!
		extract ($vars);
	
		// execute script
		include($file);

		if (!$content && $replace) {
			$content = $replace;
		}
		// save the vars genereated by the script for usage in following templates ({USERVAR:myVar}) and include scripts
// 		$vars = get_defined_vars();
	
// 		unset($vars['this']);
// 		unset($vars['script']);
// 		unset($vars['content']);
// 		$this->cmt->setVars($vars);

		return $content;
	}
	
	public function getVar($varname) {
		
		if (isset($this->evalVars[$varname])) {
			return $this->evalVars[$varname];
		} else {
			return null;
		}
	}

	public function getVars() {
		return $this->evalVars;
	}

	public function setVar($varname, $var) {
		$this->evalVars[$varname] = $var;
		true;
	}
	
	public function setVars($vars) {
		
		if (!is_array($vars)) {
			return false;
		} else {
			$this->evalVars = array_merge($this->evalVars, $vars);
			return true;
		}
	}
	
	
}
?>