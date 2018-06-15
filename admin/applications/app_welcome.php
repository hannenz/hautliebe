<?php   
/**
 * app_welcome - Starseite
 * 
 * @version 2012-04-20
 * @author J.Hahn <info@contentomat.de>
 */
namespace Contentomat;

use Contentomat\User;
use Contentomat\DBCex;

class AppDashboardController extends ApplicationController {
	
	public function init() {
		setlocale(LC_ALL, strtolower(DEFAULTLANGUAGE));
		
		$this->cmt = Contentomat::getContentomat();
		$this->user = new User(SID);
		$this->session = $this->cmt->getSession();
		$this->db = new DBCex();
		
//		$parser = new Parser();
	}
	
	/**
	 * Default action
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDefault() {

		// Content-o-mat data
		$this->parser->setParserVar('version', $this->cmt->getVersion());
		$this->parser->setMultipleParserVars($this->cmt->getBuildParts());
		
		$this->parser->setParserVar('content', $content);
		
		// user data
		$this->parser->setParserVar('icon', $cmt_icon);
		$this->parser->setParserVar('useralias', $this->user->getUserAlias());
		
		// last login
		$dateTime = $this->session->getSessionVar('cmtUserLastLogin');
		$timestamp = strtotime($dateTime);
		$date  = strftime("%a, %d. %B %Y, %H:%M", $timestamp);
		$this->parser->setParserVar('cmtUserLastLogin', $date);
		
		// password last change
		$dateTime = $this->user->getLastPasswordChange();
		if ($dateTime == '0000-00-00 00:00:00') {
			$dateTime = $this->user->getCreationDate();
		}
		$timestamp = strtotime($dateTime);
		$date  = strftime("%a, %d. %B %Y", $timestamp);
		$this->parser->setParserVar('cmtUserLastPasswordChange', $date);

		
		// password last change in days
		$now = new \DateTime();
		$lastChange = new \DateTime($dateTime);
		
		$interval = date_diff($now, $lastChange);
		$lastPasswordChangeDays = $interval->format('%a');
		
		switch(true) {
			case ($lastPasswordChangeDays > 30 && $lastPasswordChangeDays < 90):
				$this->parser->setVar('cmtUserPasswordWarning', true);
				break;
			
			case ($lastPasswordChangeDays >= 90):
				$this->parser->setVar('cmtUserPasswordError', true);
				break;
		}
		$this->parser->setParserVar('cmtUserLastPasswordChangeDays', $lastPasswordChangeDays);

		$userGroupData = $this->user->getUserGroup();
		$this->parser->setParserVar('cmtUserGroup', $userGroupData['cmt_groupname']);
		$this->parser->setParserVar('cmtUserType', $this->user->getUserType());
		$this->parser->setParserVar('cmtUserName', $this->user->getUserName());
		
		// database informations
		$database = $this->db->getCurrentDatabase();
		$this->parser->setParserVar('cmtDatabaseServerName', $database['server']);
		$this->parser->setParserVar('cmtDatabaseUserName', $database['user']);
		$this->parser->setParserVar('cmtDatabaseName', $database['db']);
		
		// system informations
		$this->parser->setParserVar('phpVersion', str_replace('+', '+&shy;', urldecode(phpversion())));
		$this->parser->setParserVar('serverIp', $_SERVER['SERVER_ADDR']);
		$this->parser->setParserVar('serverPort', $_SERVER['SERVER_PORT']);
		$this->parser->setParserVar('documentRoot', $_SERVER['DOCUMENT_ROOT']);
		
		$this->db->query("SELECT @@version");
		$r = $this->db->get();
		$this->parser->setParserVar('mysqlVersion', array_shift($r));
		
		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE.'app_welcome/cmt_welcome.tpl');
	}
	
	/**
	 * List all installed PHP extension
	 */
	protected function actionShowPhpExtensions() {
		
		$extensions = array();
		
		foreach (get_loaded_extensions() as $i => $ext) {
			$extensions[$ext] = array(
				'extension' => $ext,
				'version' => urldecode(phpversion($ext))
			);
		}
		ksort($extensions);
		$this->parser->setVar('extensions', $extensions);
		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE.'app_welcome/cmt_php_extensions.tpl');
	}
	
	/**
	 * Show phpinfo()
	 */
	protected function actionPhpInfo() {
		ob_start();
		phpinfo();
		$phpInfoConent = ob_get_contents () ;
		ob_end_clean();
		
		$this->parser->setVar('phpInfoContent', $phpInfoConent);
		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE.'app_welcome/cmt_php_info.tpl');
	}
	
	/**
	 * Display page "enter new password"
	 */
	protected function actionNewPassword() {
		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE.'app_welcome/cmt_new_password.tpl');
	}
	
	/**
	 * Perform password change
	 */
	protected function actionChangePassword() {
		
		$this->isAjax = true;
		
		$password1 = trim($_REQUEST['newPassword1']);
		$password2 = trim($_REQUEST['newPassword2']);
		$this->parser->setVar('newPassword1', $password1);
		$this->parser->setVar('newPassword2', $password2);
		$error = false;

		if (strlen($password1) < 8) {
			$this->parser->setParserVar('passwordTooShortError', true);
			$this->parser->setParserVar('passwordError', true);
			$error = true;
		} else if ($password1 != $password2) {
			$this->parser->setParserVar('passwordsNotEqualError', true);
			$this->parser->setParserVar('passwordError', true);
			$error = true;
		} else {
			$check = $this->user->changePassword(CMT_USERID, $password1);
			
			if (!$check) {
				$this->parser->setParserVar('passwordSaveError', true);
				$this->parser->setParserVar('passwordError', true);
			} else {
				$this->parser->setParserVar('passwordSaveSuccess', true);
			}
		}
		
		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE.'app_welcome/cmt_new_password.tpl');
		
	}
	
}
	
$controller = new AppDashboardController();
$replace = $controller->work();
	
	
	
	
//	include('settings_db.inc');
	
	
?>