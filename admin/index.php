<?php
/**
 * Content-o-mat - Login-Seite
 * 
 * Username und Passwort der Benutzers werden hier eingegeben und geprüft.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 */
	namespace Contentomat;

	require ("cmt_constants.inc");
	require ('classes/class_psrautoloader.inc');
	
	$autoloader = new PsrAutoloader();
	$autoloader->addNamespace('', PATHTOWEBROOT. '/phpincludes/classes/');
	$autoloader->addNamespace('Contentomat', 'classes/');

	$cmt = Contentomat::getContentomat();
	$parser = new CMTParser();
	$db = new DBCex();
	
	// Logout mit Sicherheitsbafrage gegen SQL-Injection
	if ($_REQUEST['action'] == "logout") {
		$query = "DELETE FROM cmt_sessions WHERE cmt_sessionid = '" . $db->dbQuote(substr($_REQUEST['sid'], 0, 48)) . "'";
	    $db->query($query);
	    unset ($_GET['sid']);
	}
	
	$session = new Session();
	
	$user = trim($_POST["user"]);
	$pw = trim($_POST["pw"]);
	$firstlogin = trim($_POST["firstlogin"]);
		         
	$pw = md5($pw);
	$query = "SELECT * FROM cmt_users WHERE cmt_username = '" . $db->dbQuote($user) ."' AND cmt_pass = '" . $db->dbQuote($pw) . "' LIMIT 1";

	$db->query($query);
	$r = $db->Get();

	if (empty($r)) {
		if ($firstlogin) {
	    	 $parser->setParserVar('error', true);
	    }
	} else {
		if (strstr($r['cmt_username'], $user) && strlen($r['cmt_username']) == strlen($user)) {
	    	// Alles richtig gemacht
	        // temporäre Sessiontabelle updaten
	        $query = "UPDATE cmt_sessions SET cmt_loggedin = '1', cmt_userid = '" . $db->dbQuote($r['id']) . "' WHERE cmt_sessionid = '" . $db->dbQuote(SID) . "'";
	        $db->query($query);
					
			// Userdaten raussuchen
			$user = new User(SID);
			$cmt_startpage = trim($user->getUserStartPage());
			$cmt_startapp = trim($user->getUserStartApp());
			if (!$cmt_startpage) {
				$cmt_startpage = 'cmt_applauncher.php';
			}
			
			if (!$cmt_startapp) {
				$cmt_startapp = 'app_welcome.php';
			}

			$userId = $user->getUserID();
			
			// TODO: In Klasse! Noch aktuelle Zeit bei Benutzers "last loggin" eintragen
			$query = "SELECT cmt_lastlogin FROM cmt_users WHERE id = '" . $userId . "'";
			$db->query($query);
			$r = $db->get();
// 			var_dump($r);
// 			die();
			$session = $cmt->getSession();
			$session->setSessionVar('cmtUserLastLogin', $r['cmt_lastlogin']);
			$session->saveSessionVars();
			
			$query = "UPDATE cmt_users SET cmt_lastlogin = '".date("Y-m-d H:i:s")."' WHERE id = '" . $userId . "'";
			$db->Query($query);
			
			// --> Anmeldung fertig
			$url = $cmt_startpage.'?sid='.SID.'&launch='.$cmt_startapp;
	        header ('Location: '.$url);
	        exit();

		} else {
	        $parser->setParserVar('error', true);
	    }
	}
	
	/*
	 * Ausgabe des Templates
	 */
	$parser->setParserVar('cmtVersion', $cmt->getVersion());
	$content = $parser->parseTemplate('/administration/cmt_login.tpl');
	echo $content;

?>
