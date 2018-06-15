<?php
/**
 * content-o-mat - universelle Variablen�bergabe
 * 
 * Include-Datei, die in Arrays definierte Variablen nacheinander in 
 * globalen Arrays und Sessionvariablen sucht. Wird kein Wert gefunden,
 * kann ein Default-Wert definiert werden.
 * Reihenfolge ist: $_POST, $_GET, gespeicherte Applikationsvariablen, 
 * User- und Sessionvariable
 *
 * 2013-06-19: OUTDATED in neuer Version mit class_applicationcontroller.inc
 * 
 * @author J. Hahn <jhahn@buero-hahn.de>
 * @version 2006-09-26
 */
 
 // TODO: Bezeichnungen und Reihenfolge �ndern: $cmt_sessionvars sind bislang keine echten Sessionvariablen. Diese werden bislang im der Murks-Array $cmt_scriptvars gespeichert
 
function decode ($var) {
	if (isset($var)) {
		if (is_array($var)) {
			foreach ($var as $key=>$value) {
				$var[$key] = decode($var[$key]);
			}
			if (isset($var)) {
				return $var;
			}
		} else {
			//$var = stripslashes(urldecode($var));
			$var = stripslashes($var);
			if (isset($var)) {
				return $var;
			}
		}
	}
}			
if (!is_array($save_sessionvars)) {
	$save_sessionvars = array();
}
if (!is_array($save_uservars)) {
	$save_uservars = array();
}
if (!is_array($save_scriptvars)) {
	$save_scriptvars = array();
}

$cmt_sessionvars = $session->GetSessionVar("cmtApplicationVars");
//print_r ($cmt_sessionvars);
$new_vars = array();

// Ermitteln, zu welcher Tabelle/welchem Modul die Variablen geh�ren ($launch)
// $launch = $_GET['launch'];
// if (!isset($launch)) {
// 	$launch = $_POST['launch'];
// }
$launch = CMT_APPID;

// Übergebene Variablen holen
foreach ($default_vars as $key => $default_value) {

	$var_name = str_replace ('[]', '', $key);
	//echo $var_name.'<br>';
	
	//1. Variable per POST gekommen?
	if (isset($_POST[$var_name])) {
		$submitted_var = $_POST[$var_name];
		$submitted_var = decode($submitted_var);
		//echo $submitted_var."<br>";
	}
	
	//2. Variable per GET gekommen?
	if (!isset($submitted_var)) {
		$submitted_var = $_GET[$var_name];
		$submitted_var = decode($submitted_var);
	}

	//3. Variable als Session-Variable vorhanden?	
	if (!isset($submitted_var)&& isset($launch) && isset($cmt_sessionvars[$launch][$var_name])) {
		$submitted_var = $cmt_sessionvars[$launch][$var_name];
		
		//echo "hole \$$var_name aus Session: ".$submitted_var."<br>";
	}
	
	// 4. Variable als Uservariable
	if (!isset($submitted_var)&& isset($launch) && isset($cmt_uservars[$launch][$var_name])) {
		$submitted_var = $cmt_uservars[$launch][$var_name];
	}

	// 5. Variable als richtige Sessionvariable
	if (!isset($submitted_var) && in_array($var_name, $save_scriptvars)) {
		$submitted_var = $session->GetSessionVar($var_name);
	}
	
	// 6. Wenn nirgendwo die Variable ist, dann Default-Wert nehmen
	if (!isset($submitted_var)) {
	
		// ist Variable ein Array?
		if (stristr($key, '[]')) {
			// ... aber $default_value = '', dann wenigsten Variablen-Typ mitnehmen
			if (!$default_value) {
				$default_value = array();
			} 
			// ... aber es gibt nur einen Wert, dann Variablennamen �ndern in $var[0] ) $default_value
			else {
				$key = str_replace('[]', '[0]', $key);
			}
		}
		$new_vars[$key] = $default_value;
	} else {
		// ... ansonsten Variable �bernehmen
		$new_vars[$key] = $submitted_var;
	}
	
	// Variable ggf. in Sessionvars speichern
	if (in_array($key, $save_sessionvars) && isset($launch)) {
		$cmt_sessionvars[$launch][$var_name] = $submitted_var;
	}

	// Variable ggf. in Uservars speichern
	if (in_array($key, $save_uservars) && isset($launch)) {
		$cmt_uservars[$launch][$var_name] = $submitted_var;
	}
	
	// Variable ggf. in Skriptvars speichern
	if (in_array($key, $save_scriptvars)) {
		$session->SetSessionVar($key, $submitted_var);
	}
	unset($submitted_var);
}

// �bergebene Variablen extrahieren: extract funktioniert nicht, da die Schl�ssel des Arrays $new_var auch selbst in Arrayschreibweise
// sein k�nnen: z.B. $new_vars["$search_field[]"]
foreach ($new_vars as $varName=>$value) {
	$varName = str_replace ('[]', '', $varName);
	eval ("\$$varName = \$value;");
}

// Sesisonvariablen speichern
$session->SetSessionVar("cmtApplicationVars", $cmt_sessionvars);

// Skriptvariablen in Session speichern
foreach ($save_scriptvars as $varName) {
	$session->setSessionVar($varName, $new_vars[$varName]);
}
$session->SaveSessionVars();


// Uservariablen speichern
$user->SetUserVar("cmt_uservars", $cmt_uservars);
$user->SaveUserVars();

?>