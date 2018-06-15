<?php
/*
 * Service-Include: cmt_tables_setting
 * 
 * letzte Änderung: 01.06.2005
 */


$session = new Session();	// eigene Variablen, z.B. Formulareinstellungen sollten als Sessionvariable gespeichert werden
$form = new Form();
$db = new DBCex();


global $_POST;
$cmt_app = trim($_POST['cmt_app']);

if (!$cmt_app) {
	$cmt_app = $session->GetSessionVar('cmt_app');
}


$query = "SELECT cmt_appname, cmt_appfile FROM cmt_applications";
$db->Query ($query);

while ($r = $db->Get(MYSQLI_ASSOC)) {
	$values[] = $r['cmt_appfile'];
	$aliases[] = $r['cmt_appname']." (".$r['cmt_appfile'].")";
}

//$formfield = $dformat->format_link($cmt_app, "edit", "cmt_app", "", "applications/");

$formfield = $form->FormSelect ("cmt_app", $values, $aliases, $cmt_app);
$content = "<font class=\"service_text\">Einstellungen für Anwendung:</font>&nbsp;&nbsp;".$formfield;
$content .= "&nbsp;&nbsp;&nbsp;".$form->FormSubmit("neu anzeigen");

$session->SetSessionVar('cmt_app', $cmt_app);
$session->SaveSessionVars();

$add_query = "WHERE cmt_application = '$cmt_app' ORDER BY cmt_settingvarname";	// Die Query wird in der Variablen $add_query
														// übergeben. Erlaubt ist eine Query in der Form
														//
														// WHERE Bedingung ORDER BY Sortierung LIMIT Limitierun
														//
														// (wobei ein LIMIT nur in seltenen Fällen Sinn macht)
?>