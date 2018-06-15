<?php
/**
 * Tabellenmanager - Reiter "Struktur"
 * 
 * Erm�glicht das Eingeben/ Erstellen einer Felderstruktur f�r die �bersicht und Eingabemaske einer Tabelle.
 * 
 * @author J.Hahn <jhahn@content-o-mat.de>
 * @version 2007-01-04
 * 
 * @package app_tablebrowser.php
 */
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\CMTParser;
use Contentomat\ApplicationHandler;


	function protectBrackets($v) {
		$v = str_replace('{', '&#123;', $v);
		return str_replace('}', '&#125;', $v);
	}
	
	$parser = new CMTParser();

	// �bergebene Variablen holen
	$default_vars = array ("action" => "", "id" => "", "cmt_editstruct" => "", "cmt_showfields" => "", "cmt_tablename" => "");
	
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include (PATHTOADMIN.'includes/func_get_vars.php');

	///////////////////////////
	// Menüs erstellen
	$applicationHandler = new ApplicationHandler();
	$selectTable = $applicationHandler->makeApplicationSelect($id);
	$parser->setParserVar('selectTable', $selectTable);
	
	$parser->setParserVar('cmt_slider', $cmt_slider);
	$parser->setParserVar('tableId', $id);
	$parser->setParserVar('cmtTableName', $cmt_tablename);

	// Men�s erstellen -> Ende	
	//////////////////////////		

	$db = new DBCex();
	switch ($action) {
		case 'save':
			//$db->init();
			$query = "UPDATE cmt_tables SET cmt_showfields = '". $db->dbQuote($cmt_showfields)."', cmt_editstruct = '".$db->dbQuote($cmt_editstruct)."' WHERE id = '".$id."'";
			$db->Query($query);
			if ($db->last_errorNr()) {
				$parser->setParserVar('errorSave', true);
				$parser->setParserVar('errorSaveMySQL', $db->last_error()); 
				//$userMessage = \Contentomat\error ("Konnte Tabellenstruktur f&uuml;r '".$cmt_tablename."' nicht speichern: ".$db->last_error(), 0, 'div');
			} else {
				$parser->setParserVar('successSave', true);
				//$userMessage = \Contentomat\success ("Tabellenstruktur f&uuml;r '".$cmt_tablename."' erfolgreich gespeichert.", 0, 'div');
			}
			$action = '';
			break;
	}			

	if ($id) {
		
		$query = "SELECT cmt_type, cmt_tablename, cmt_showfields, cmt_editstruct FROM cmt_tables WHERE id = '".intval($id)."'";

		$db->Query($query);
		$r = $db->Get(MYSQLI_ASSOC);
		$cmt_tablename = $r['cmt_tablename'];

		// Felderinfos erstellen
		if ($r['cmt_type'] == 'table') {
			
			$fieldinfos = $db->GetFieldInfo($r['cmt_tablename']);

			$fields = array();
			$fields = $fieldinfos['name'];
			$max_in_col = ceil(count($fields)/2);
			$c = 1;
			$col = 0;
			foreach ($fields as $field) {
				$cols[$col] .= $field.'<br>';
				$c++;
				if ($c > $max_in_col && $max_in_col > 10) {
					$col = 1;
				}
			}
			$parser->SetParserVar("fieldsCol1", $cols[0]);
			$parser->SetParserVar("fieldsCol2", $cols[1]);
			
			$parser->SetParserVar('showTableFields', true);
		}


		// Overview-Box erstellen
		// Die '{' und '}' m�ssen in ihre Unicode-Entit�ten umgewandelt werden, da das Textfeld zweimal geparst wird (Feld und anschlie�end Rahmen)
		// und daher die Makros im Textfeld beim zweiten Mal auch mitgeparst werden w�rden
		 
		$parser->SetParserVar('structureOverview', protectBrackets($r['cmt_showfields']));

		// Edit-Box erstellen
		$parser->SetParserVar('structureEditview', protectBrackets($r['cmt_editstruct']));
		
		// ID und zus�tzliche Daten �bergeben
		$parser->SetParserVar("id", $id);
		$parser->SetParserVar("tableName", $r['cmt_tablename']);
		$parser->SetParserVar("entryType", $r['cmt_type']);

		// Formularfelder anzeigen
		$parser->SetParserVar('showFormFields', true);	
	} 
// 	else {
// 		$userMessage = \Contentomat\warning('Bitte w&auml;hlen Sie eine Tabelle aus.');
// 	}
	
	// Ausgeben
// 	if ($userMessage) {
// 		$parser->SetParserVar("userMessage", $userMessage);
// 	}
	$replace .= str_replace('{STRUCTUREHEAD}', '{HEAD}', $parser->parseTemplate('app_tablebrowser/appinc_structure.tpl'));
?>