<?php
/*
 * Eigener Service-Include für die Ansicht einer DB-Tabelle
 * Beispiel-Datei mit Kommentaren
 * 
 */
 
	global $_POST;	// Post- und Session-Variablen immer per global in den eigenen Scope holen
	global $session;
	//global $user	// event. User-Klasse auch, falls Rechte gecheckt werden müssen oder über die Konstanten hinaus Daten gebraucht werden
	
$own_var = trim($_POST['post_var']);

$form = new Form();		// Es sind alle Klassen verfügbar

if (!$own_var) {
	$own_war = $session->GetSessionVar('own_var');
}

$content = $form->FormInput("own_var", $own_var);	// HTML, das eingefügt werden soll, muss in die
													// Variable $content geschrieben werden

$session->SetSessionVar('own_var', $own_var);
$session->SaveSessionVars();

$add_query = "WHERE id = '$own_var' ORDER BY plz ASC";	// Die Query wird in der Variablen $add_query
														// übergeben. Erlaubt ist eine Query in der Form
														//
														// WHERE Bedingung ORDER BY Sortierung LIMIT Limitierun
														//
														// (wobei ein LIMIT nur in seltenen Fällen Sinn macht)
?>