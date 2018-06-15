<?php
/**
 * content-o-mat Installer
 * 
 * Erzeugt die benötigten Dateien und Datenbanktabellen f�r den content-o-mat
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2016-09-21
 */
error_reporting(0);
/* Variabeln */
	$version = '1.4';
	$sqlFile = 'contentomat_data.sql';
	
	$type = 'Installation';
	$totalSteps = 3;
	$pathToRoot = getRoot();
	global $_POST;
	
	$step = intval(trim($_POST['step']));
	if (!$step) $step = 1;
	
	$formVars[1] = array ('webroot' => '/',
						'adminpath' => 'admin/',
						'downloadpath' => 'downloads/',
						'webname' => 'Rooster Inc. worldwide',
						'defaultlanguage' => 'de',
						'charset' => 'utf-8',
						'cmt_usecookies' => '1',
						'cmt_forcecookies' => '1',
						'cmt_dberrorlog' => '1',
						'cmt_modrewrite' => '0',
						'cmt_includepath' => trim(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/')),
						);

	$formVars[2] = array ('server' => 'localhost',
						'db' => '',
						'user' => '',
						'pw' => '',
						'webroot' => '/',
						'adminpath' => 'admin/'
						);

	$formVars[3] = array ('webroot' => '/',
						'adminpath' => 'admin/'
						);


/* Funktionen */
	function getRoot () {
		$depth = substr_count (dirname($_SERVER['PHP_SELF']), '/');
		return str_pad('', $depth*3, '../');		
	}
	
	function getFormvars (&$formvars, $step) {
		foreach ($formvars[$step] as $var => $default) {
			if ($_POST[$var]) {
				$formvars[$step][$var] = trim(stripslashes($_POST[$var]));
			}
		}
	}	

	function formatDirectory ($directory) {
		$directory = preg_replace ("/^\.\//", "", $directory);
		$directory = preg_replace ("/\/{2,}/", "/", $directory);
		if (!preg_match("/\/$/", $directory)) {
			$directory .= "/";
		}
		$directory = preg_replace ("/^\//", "", $directory);
		return $directory;
	}
	

	// Erstelle Datenbankstruktur
	switch ($step) {
		
		case '4':
			getFormVars($formVars, 3);
			extract ($formVars[3]);
			
			include(formatDirectory($pathToRoot.$adminpath.'/').'settings_db.inc');
			include(formatDirectory($pathToRoot.$adminpath.'/').'cmt_constants.inc');

			// Zeichesatz auswählen
			$charsetConversionTable = array(
				'utf-8' => 'utf8',
				'iso-8859-1' => 'latin1',
				'iso-8859-2' => 'latin2',
				'iso-8859-9' => 'latin5',
				'iso-8859-13' => 'latin7'
			);
			
			$checkServer = @mysql_connect($server, $user, $pw);
			if (!$checkServer) {
				$errorMessage = 'Es konnte nicht mit dem Datenbankserver "'.$server.'" verbunden werden. Bitte &uuml;berpr&uuml;fen Sie die Zugangsdaten.';
				$messageClass = "error";
				$step = 3;
			} else {
				$checkDb = @mysql_select_db($db, $checkServer);
				if (!$checkDb) {
					$errorMessage = 'Es konnte nicht mit der Datenbank "'.$db.'" verbunden werden. Bitte &uuml;berpr&uuml;fen Sie die Zugangsdaten.';
					$step = 3;
				} else {

					// Zeichensatz setzen
					if ($charsetConversionTable[CMT_DEFAULTCHARSET]) {
						mysql_query("SET NAMES '".$charsetConversionTable[CMT_DEFAULTCHARSET]."'");
					}
					
					// SQL ausführen
					$sql = trim(file_get_contents('sql/'.$sqlFile));
					$sql = str_replace("\r", '', $sql);
					$queryArray = explode (";\n", $sql);
					
					$errors = 0;
					$lines = 0;

					foreach ($queryArray as $query) {
						$result = mysql_query($query);
						
						if (mysql_error()) {
							$dbErrorMessage = mysql_error();
						} else {
							$dbErrorMessage = 'None';
						}
						 
						$protocolFile .= "$query \n-> Error: ". $dbErrorMessage . "\n";
						if (!$result) {
							$errors++;
						}
						$lines++;
					}
					
					$protocolFile = 'Installation am '.date("Y-m-d H:i:s").' -> '.$errors.' Fehler in '.$lines." SQL-Anweisungen.\n\n".$protocolFile;
					$fp = @fopen('installation_protocol.txt', 'w');
					$fwrite = @fwrite($fp, $protocolFile);
					@fclose($fp);
					
					if ($errors) {
						$errorMessage = 'Es sind '.$errors.' Fehler in '.$lines.' SQL-Anweisungen aufgetreten!<br />Bitte lesen Sie die ' .
								'<a href="installation_protocol.txt">Protokoll-Datei</a>.';
						$step = 3;
					} else {
						$headerText = 'abgeschlossen';
						$linkToAdmin = formatDirectory($pathToRoot.$adminpath.'/').'index.php';
					}
				}
			}
			
		break;
		case '3':
			getFormVars($formVars, 2);
			extract ($formVars[2]);

			// Datenbankzugang testen
			$checkServer = mysql_connect($server, $user, $pw);
			if (!$checkServer) {
				$errorMessage .= 'Es konnte nicht mit dem Datenbankserver "'.$server.'" verbunden werden. Bitte &uuml;berpr&uuml;fen Sie die Zugangsdaten.<br />';
				$step = 2;
			} else {
				// Datenbank ausw�hlen und testen
				$checkDb = @mysql_select_db($db, $checkServer);
				if (!$checkDb) {
					$errorMessage .= 'Es konnte nicht mit der Datenbank "'.$db.'" verbunden werden. Bitte �berpr�fen Sie die Zugangsdaten und -rechte.<br />';
					$step = 2;
				} else {
					// Datei schreiben
					$fileSettings = file_get_contents('files/settings_db.tpl');
					
					// Makros durch Variablen austauschen
					foreach ($formVars[2] as $var => $value) {
						$fileSettings = str_replace('{'.strtoupper($var).'}', $value, $fileSettings);
					}		
			
					// Datei schreiben
					$fname = formatDirectory($pathToRoot.$adminpath.'/').'settings_db.inc';
					$fp = @fopen($fname, 'w');
					$fwrite = @fwrite($fp, $fileSettings);
					
					if ($fwrite === false) {
						$step = 2;
						$errorMmessage .= 'Beim Schreiben der Datenbank-Datei "'.$fname.'" ist ein Fehler aufgetreten. Bitte &uuml;berpr&uuml;fen Sie die Verzeichnisse und die Zugriffsrechte.<br />';
					} else {
						$successMessage .= 'Die Datenbankzugangsdaten wurden erfolgreich gespeichert und verifiziert.';
						@fclose($fp);
						$step = 3;
					}
				}
			}
		break;

		case '2':
			// Eingabe der Pfade/ Verzeichnisse
			getFormVars($formVars, 1);
			extract ($formVars[1]);		
	
			$fileConstantsTemplate = file_get_contents('files/cmt_constants.tpl');
			
			// WEBBEREICH: Makros durch Variablen austauschen
			$fileConstants = $fileConstantsTemplate;
			foreach ($formVars[1] as $var => $value) {
				
				// Variabeln ggf. bearbeiten
				switch ($var) {
					// Muss eine .htaccess-Datei kopiert werden?
					case 'cmt_modrewrite':
					
						// .htaccess-Datei f�r mod_rewrite �ndern
						if ($value == 1) {
							$htaccessTemplate = file_get_contents('files/htaccess.tpl');
							
							// anpassen
							$htaccessTemplate = str_replace ('{REWRITE_BASE}', $formVars[1]['webroot'], $htaccessTemplate);
							
							$htaccessFile = formatDirectory($pathToRoot.$webroot.'/').'.htaccess';
							
							// Backup machen
							if (is_file($htaccessFile)) {
								
								// Rewrite schon drin?
								$htaccessContent = file_get_contents($htaccessFile);
								
								if (stristr($htaccessContent, '#### content-o-mat start ####')) {
									$dontWriteInHtaccess = true;
								}
								
								if (!$dontWriteInHtaccess) {
									$backupFile = formatDirectory($pathToRoot.$webroot.'/').'.htaccess_cmt-backup_'.date('Y-m-d_H-i-s');
									if (!copy($htaccessFile, $backupFile)) {
										$errorMessage .= 'Bestehende .htaccess Datei "'.$htaccessFile.'" konnte nicht gesichert werden. Die f&uuml;r mod_rewrite ben&ouml;tigten &Auml;nderungen ' .
													'm&uuml;ssen manuell in die existierende .htaccess &uuml;bertragen werden.<br />';
										$messageClass = "error";
										$backupError = true;
										
										// Kein mod_rewrite in cmt_constants.inc aktivieren!
										$value = 0;
									} else {
										$successMessage .= 'Ein Backup der aktuellen .htaccess-Datei wurde unter dem Namen "'.$backupFile.'" gespeichert.<br />';
										$backupError = false;
									}
								}
							}
							
							if (!$dontWriteInHtaccess && !$backupError) {
								$fp = @fopen($htaccessFile, 'a+');
								$fwrite = @fwrite($fp, $htaccessTemplate);
								if (!$fwrite) {
									$errorMessage .= 'Konnte .htaccess-Datei nicht anlegen oder &auml;ndern. Bitte nehmen Sie die &Auml;nderungen manuell vor.<br />';
									// Kein mod_rewrite in cmt_constants.inc aktivieren!
									$value = 0;
								} else {
									$successMessage .= 'Die Datei ".htaccess" ist erfolgreich erg&auml;nzt/ erstellt worden.<br />';
								}
								@fclose($fp);
							}
						}
					break;
					
					case 'cmt_forcecookies':
						if (!$formVars[1]['cmt_usecookies']) $formVars[1]['cmt_forcecookies'] = 0; 
					break;
				}
				$fileConstants = str_replace('{'.strtoupper($var).'}', $value, $fileConstants);
			}
			
			// Datei schreiben
			$fname = formatDirectory($pathToRoot.$webroot.'/').'cmt_constants.inc';
			$fp = @fopen($fname, 'w');
			$fwrite = @fwrite($fp, $fileConstants);
			
			if ($fwrite === false) {
				//$step = 1;
				$errorMessage .= "Beim Schreiben der Konstanten-Datei $fname in das Webroot-Verzeichnis ist ein Fehler aufgetreten. Bitte �berpr�fen Sie die Verzeichnisse und die Zugriffsrechte.<br />";
				$messageClass = "error";
				@fclose($fp);
				$fileError = 1;
			} else {
				$successMessage .= 'Die Konstanten-Datei "'.$fname.'" ist erfolgreich im Webroot-Verzeichnis erstellt worden.<br />';
			}
		
			// Zweite Datei
			// ADMINBEREICH: Makros durch Variablen austauschen
			$fileConstants = $fileConstantsTemplate;
			foreach ($formVars[1] as $var => $value) {
				
				// Variabeln ggf. bearbeiten
				switch ($var) {
					// Im Adminbereich k�nnen keine Cookies verwendet werden
					case 'cmt_usecookies':
						$value = 0;
					break;

					// Im Adminbereich ist mod_rewrite nicht m�glich/ n�tig
					case 'cmt_modrewrite':
						$value = 0;
					break;
				}
				$fileConstants = str_replace('{'.strtoupper($var).'}', $value, $fileConstants);
			}
			$fnameCopy = formatDirectory($pathToRoot.$adminpath.'/').'cmt_constants.inc';
			$fp = @fopen($fnameCopy, 'w');
			$fwrite = @fwrite($fp, $fileConstants);
			if ($fwrite === false) {
				$step = 1;
				$errorMessage .= 'Die zweite Konstanten-Datei "'.$fnameCopy.'" konnte nicht im Administrationsordner erstellt werden. Bitte &uuml;berpr&uuml;fen Sie die Verzeichnisse und die Zugriffsrechte.<br />';
				$messageClass = "error";
				@fclose($fp);
			} else {
				$step = 2;
				$content = "steps/step2.inc";
				$successMessage .= 'Die zweite Konstanten-Datei "'.$fnameCopy.'" ist im Admin-Verzeichnis erfolgreich erstellt worden.';
				if ($fileError) {
					$errorMessage .= 'Bitte kopieren Sie die Datei "cmt_constants.inc" nach Abschluss der Installation manuell ins Admin-Verzeichnis! Ggf. ' .
								'm&uuml;ssen Einstellungen von Hand vorgenommen werden (z.B. Cookie-Behandlung).';
				}
				$messageClass = "success";
			}
			@fclose($fp);
		break;
		
		// Default/ Startscreen
		default:
		case '1':
			$step = 1;
			getFormVars($formVars, 1);
			extract ($formVars[1]);
		break;
	}
	
	if ($step <= $totalSteps) $headerText = 'Schritt '.$step.' von '.$totalSteps;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>content-o-mat: Installation</title>
	<link rel="stylesheet" type="text/css" href="/installer/style.css">
</head>
<body>
	<div id="content">
		<div id="head"><span class="name">content-o-mat</span>&nbsp;<span class="version"><?php echo $version; ?></span></div>
		<div id="headSubhead">content management system // web application framework</div>
		<div id="contentContainer">
			<div id="headBarImage"> </div>
			<div id="headBar">Installation: <?php echo $headerText; ?></div>
			<?php
				if ($successMessage) echo '<div class="success">'.$successMessage.'</div>';
				if ($errorMessage) echo '<div class="error">'.$errorMessage.'</div>';
			?>
			<div id="contentText"><?php include("steps/step$step.inc"); ?></div>
		</div>
	</div>
</body>
</html>