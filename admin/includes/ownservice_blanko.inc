<?php
/*
 * Eigener Service-Include f�r die Ansicht einer DB-Tabelle
 * Beispiel-Datei mit Kommentaren
 * 
 */
 
	global $_POST;	// Post- und Session-Variablen immer per global in den eigenen Scope holen
	global $session;
	//global $user	// event. User-Klasse auch, falls Rechte gecheckt werden m�ssen oder �ber die Konstanten hinaus Daten gebraucht werden
	
$own_var = trim($_POST['post_var']);

$form = new Form();		// Es sind alle Klassen verf�gbar

if (!$own_var) {
	$own_war = $session->GetSessionVar('own_var');
}

$content = $form->FormInput("own_var", $own_var);	// HTML, das eingef�gt werden soll, muss in die
													// Variable $content geschrieben werden

$session->SetSessionVar('own_var', $own_var);
$session->SaveSessionVars();

$add_query = "WHERE id = '$own_var' ORDER BY plz ASC";	// Die Query wird in der Variablen $add_query
														// �bergeben. Erlaubt ist eine Query in der Form
														//
														// WHERE Bedingung ORDER BY Sortierung LIMIT Limitierun
														//
														// (wobei ein LIMIT nur in seltenen F�llen Sinn macht)
?>