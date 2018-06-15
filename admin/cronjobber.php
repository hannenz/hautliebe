<?php 

namespace Contentomat;

require_once 'cmt_constants.inc';
require_once 'settings_db.inc';
require_once 'classes/class_psrautoloader.inc';

class CronJobController {
	
	protected $cronjob;
	protected $user;
	protected $cliUtils;
	protected $codeEvaler;
	
	private $userName;
	private $userPassword;
	
	public function __construct() {

		$this->cliUtils = new \Contentomat\CLIUtils();
		$this->cronJob = new CronJob();
		$this->user = new User();
		$this->codeEvaler = new EvalCode();

		// TODO: put this in external file?
		$this->userName = 'cron';
		$this->userPassword = 'QjQJsrMYbCp5U8RH2X6N';
		
		// check if script is called from CLI / Cron
		//$this->checkCaller();
		
		// check user
		$this->checkUser();
		
		// do the CronJobs
		$this->doCronJobs();
	}
	
	/**
	 * private function checkCaller()
	 * Check if this file is called from the command line interface (CLI), cron
	 *
	 */
	private function checkCaller() {
		
		if (!$this->cliUtils->isCli()) {
			$this->exitCronJobber('unauthorized caller');
		}
		return true;
	}
	
	/**
	 * function checkUser()
	 *
	 */
	private function checkUser() {
		
		if (!$this->user->checkUserPassword($this->userName, $this->userPassword)) {
			$this->exitCronJobber('user unauthorized');
		}
		
		// TODO: login user
	}
	
	/**
	 * private function doCronJobs()
	 * Get current cron jobs and execute them.
	 * 
	 *  @param void
	 *  @return void
	 *
	 */
	private function doCronJobs() {
		
		$cronJobs = $this->cronJob->find();

		foreach ($cronJobs as $id => $cronJob) {

			$scriptVars = $this->extractScriptVars($cronJob['script_vars']);
			
			switch($cronJob['script_vars_type']) {
			
				case 'post':
					unset($_POST);
					$_POST = $scriptVars;
					break;
					
				case 'get':
					unset($_GET);
					$_GET = $scriptVars;
					break;
					
				default:
					$this->cmt->setVars($scriptVars);
					break;
			}
			$this->codeEvaler->evalCode(file_get_contents(PATHTOWEBROOT . $cronJob['execute_script']));
		}
		
		// TODO: log last execution
		
		$this->exitCronJobber('regular exit');
	}
	
	private function exitCronJobber($reason='') {
		// echo $reason;
		
		// TODO: logout user
		exit();
	}
	
	/**
	 * protected function extractScriptVars()
	 * Extracts the stored vars of the job
	 * 
	 * @param string $scriptVars	Vars as text from the database entry
	 * @return array				Extracted vars as array (key = var name, value = var value)
	 */
	protected function extractScriptVars($scriptVars) {
		
		$vars = explode("\n", $scriptVars);
		$extractedVars = array();
		
		foreach($vars as $v) {
			
			preg_match('/^(.*)=\s?(\'|\"|)?(.*)(\2)+$/', trim($v), $match);
			$extractedVars[trim($match[1])] = trim($match[3]);
		}
		
		return $extractedVars;
	}
}

$autoloader = new PsrAutoloader();
$autoloader->addNamespace('Contentomat', PATHTOADMIN . 'classes/');

$cronJobController = new CronJobController();
