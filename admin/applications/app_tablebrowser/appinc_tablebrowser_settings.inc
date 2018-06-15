<?php
// Stand: Gruppeneinstellungen sollten angezeigt werden. Allerdings fehlen ein Ordner 'groups' und 'default_settings.ini' und 'cmt_settings.tpl'!
// ToDo: Gruppeneigenschaften speichern!

/**
 * Tabellenmanager - Reiter "Einstellungen"
 * 
 * Einstellungen für die gewählte Tabelle oder Applikation.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2014-04-15
 * 
 * @package app_tablebrowser.php
 */
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\Contentomat;
use Contentomat\Image;
use Contentomat\ApplicationHandler;
use Contentomat\FileHandler;

//use Contentomat\Form;

	// Übergebene Variablen holen
	$default_vars = array ("action" => "", "cmt_dbtable" => "", "id" => "", 'cmt_uploadicon' => '');
	
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include (PATHTOADMIN.'includes/func_get_vars.php');
	
	$parser = new Parser();
	$db = new DBCex();
	$cmt = Contentomat::getContentomat();
	
	///////////////////////////
	// Menüs erstellen
	$applicationHandler = new ApplicationHandler();
	$selectTable = $applicationHandler->makeApplicationSelect($id);

	$parser->setParserVar('selectTable', $selectTable);
	$parser->setParserVar('cmt_slider', $cmt_slider);
	$parser->setParserVar('tableId', $id);
	
	$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_settings.tpl');
	
	// Men�s erstellen -> Ende	
	//////////////////////////


// Gruppe oder Tabelle/Anwendung
if (strstr($id, 'group_')) {
	$a = explode('_', $id);
	$type = 'group';
	$groupId = intval($a[1]);
	$iconFolderId = $groupId;
} else {
	$type = 'table';
	$iconFolderId = intval($id);
}

switch ($action) {
	
	case 'save':
		// Einstellungen holen
		foreach ($_POST as $varName => $varValue) {
			if (preg_match('/^cmtsetting_(.*)/i', $varName, $match)) {
				$settings[$match[1]] = $varValue;
			}
		}
		
		// Wurde individuelles Icon hochgeladen?
		if ($_FILES['cmt_uploadicon']['tmp_name'] && $settings['icon'] == 'otherIcon') {
			$possibleFileTypes = array('jpg', 'jpeg', 'png', 'gif');
			$fileInfo = explode('.', basename($_FILES['cmt_uploadicon']['name']));
			$fileExt = $fileInfo[count($fileInfo)-1];
			if (in_array(strtolower($fileExt), $possibleFileTypes)) { 
				$fileHandler = new fileHandler();
				$folderWrapper = array('group' => 'groups', 'table'=>'items');
				// 2006-08-07: Ort geändert - die Neuen Icons werden im ausgewählten Template-Ordner angelegt
				//$saveIconPath = PATHTOADMIN.'altimages/'.$folderWrapper[$type].'/'.$iconFolderId.'/';
				$saveIconPath = 'altimg/'.$folderWrapper[$type].'/'.$iconFolderId.'/';
				$newFileName = 'icon.'.$fileExt;
				
				if (!is_dir(CMT_TEMPLATE.$saveIconPath)) $fileHandler->createDirectory(array('newDirectoryname' => CMT_TEMPLATE.$saveIconPath, 'chmod'=>'0777'));
				
				$fileHandler->handleUpload(array('uploadFile' => $_FILES,
									'targetDirectory' => CMT_TEMPLATE.$saveIconPath,
									'newFilename' => array('cmt_uploadicon' => $newFileName)));
				$settings['iconPath'] = $saveIconPath.$newFileName;
				
				// Kleinere Grafiken erzeugen
				if (is_file(CMT_TEMPLATE.$saveIconPath.$newFileName)) {
					$image = new image();
					
					$imageSizes = array(16,24,32);
					
					foreach ($imageSizes as $size) {
						$image->createThumbnail(array(
							'sourceImage' => CMT_TEMPLATE.$saveIconPath.$newFileName, 
							'destinationImage' => CMT_TEMPLATE.$saveIconPath.'icon_'.$size.'px.'.$fileExt,
							'maxWidth' => $size,
							'maxHeight' => $size,
							'thumbType' => $fileExt,
							'alternativeThumbType' => 'png'
						));
					}	
				}
			} else {
				$parser->setParserVar('errorMessage', 'Die hochgeladene Datei ist kein g�liges Grafikformat!');
				$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_settings_saveerror.tpl');
			}
		}
		
		// Muss IconPfad gelöscht werden, da kein Icon angezeigt werden soll? (Variable icon = none)
		if ($settings['icon'] == 'none') unset($settings['iconPath']);
		
		if ($type == 'group') {
			$query = "UPDATE cmt_tables_groups SET cmt_groupsettings = '". $cmt->safeSerialize($settings)."' WHERE id = '$groupId'";
		} else {
			$query = "UPDATE cmt_tables SET cmt_tablesettings = '". $cmt->safeSerialize($settings)."' WHERE id = '$id'";
		}
		
		$db->Query($query);
		if ($db->getLastErrorNr()) {
			$parser->setParserVar('dbErrorNr', $db->getLastErrorNr());
			$parser->setParserVar('dbErrorMessage', $db->getLastError());
			$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_settings_saveerror.tpl');	
		} else {
			$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_settings_savesuccess.tpl');
		}
	//break;
	
	case 'edit':
		
		switch ($type) {
			
			case 'group':

				// Db-Abfrage
				$query = "SELECT * FROM cmt_tables_groups WHERE id = '".intval($groupId)."'";
				$db->Query ($query);
				$r = $db->Get();
				
				// Id
				$parser->setParserVar('groupId', $id);
							
				// Ein Template f�r alle Gruppen
				if (file_exists(CMT_TEMPLATE.'app_tablebrowser/appinc_settings_groupsettings.tpl')) {
					$settingsTemplate = 'app_tablebrowser/appinc_settings_groupsettings.tpl';
					$settingsTemplatePath = CMT_TEMPLATE;
				} else {
					$settingsTemplate = 'app_tablebrowser/appinc_settings_notemplatefound.tpl';
					$settingsTemplatePath = CMT_TEMPLATE;
				}
				
				$settings = $cmt-> safeUnserialize($r['cmt_groupsettings']);
				if (!$settings && is_file(CMT_TEMPLATE.'app_tablebrowser/default_settings_groupsettings.ini')) {
			    		$settings = @parse_ini_file(CMT_TEMPLATE.'app_tablebrowser/default_settings_group.ini');
			    }
			    
			    // Icon anzeigen
			    switch ($settings['icon']) {
			    	case 'otherIcon':
			    		$iconPath = $settings['iconPath'];
			    		break;
			    		
			    	case 'none':
			    		$iconPath = '';
			    		break;
			    		
			    	default:
			    		if ($settings['iconPath']) {
			    			$iconPath = $settings['iconPath'];
			    		} else {
			    			$iconPath = 'general/img/cmt_defaultgroupicon.png';
			    		}
			    		$settings['icon'] = 'default';
			    		break;
			    }
			    $parser->SetParserVar('iconPath', $iconPath);
			    $parser->SetParserVar('icon', $settings['icon']);
/*			    
			    if (strstr($settings['icon'], 'default')) {
			    	$pathToIcon = CMT_TEMPLATE.'general/img/';
			    	$parser->SetParserVar('pathToIcon', $pathToIcon);
			    }
*/
			break;
			
			case 'table':		
				
				// DB-Abfrage
				$query = "SELECT * FROM cmt_tables WHERE id = '".intval($id)."'";

				$db->Query ($query);
				$r = $db->Get();
				
				// Name
				if ($r['cmt_showname']) {
					$appName = $r['cmt_showname'];
				} else {
					$appName = $r['cmt_tablename'];
				}
				$parser->setParserVar('appName', $appName);
				
				// Id
				$parser->setParserVar('tableId', $id);
				
				// Template suchen:
				// 1. Tabelle / Anwendung hat ein eigenes Settingstemplate
				$templates = (array)$cmt->safeUnserialize($r['cmt_templates']);
				
// 				if (!is_array($templates)) {
// 					$templates = array();
// 				}
				
				if (isset($templates['settingsTemplate']) && trim($templates['settingsTemplate']) != '') {
					$settingsTemplate = $templates['settingsTemplate'];
					$settingsTemplatePath = '';
				}
		
				// 2. Eintrag ist Tabelle, dann Tabellenstandardtemplate
				if (!$settingsTemplate) {
					if (!$r['cmt_include'] && $r['cmt_type'] == 'table') {
						$settingsTemplate = 'app_showtable/cmt_settings.tpl';
						$settingsTemplatePath = CMT_TEMPLATE;
					}
				}

				// 3. Immernoch kein Template, dann im Templates-Ordner nach 'Anwendungsname/cmt_settings.tpl' suchen
				if (!$settingsTemplate) {

					$fp = pathinfo($r['cmt_include']);
					if ($fp['dirname']) {
						$includePath = $fp['dirname'];
					} else {
						$includePath = $fp['filename'];
					}

					if (file_exists(CMT_TEMPLATE.$includePath.'/cmt_settings.tpl')) {
						$settingsTemplate = $includePath.'/cmt_settings.tpl';
						$settingsTemplatePath = CMT_TEMPLATE;
					}
				}
		
				// Falls es kein Template gibt, dann Fehlermeldung anzeigen
				if (!$settingsTemplate) {
					$settingsTemplate = 'app_tablebrowser/appinc_settings_notemplatefound.tpl';
					$settingsTemplatePath = CMT_TEMPLATE;
				}
				
				// Default-Werte holen, falls es noch keine cmt_tablesettings gibt
				$settings = $cmt->safeUnserialize($r['cmt_tablesettings']);
				
				if (!is_array($settings)) {
					$settings = array();
				}

		    	if ($r['cmt_type'] == 'table') {
		    		$settingsPath = 'app_showtable';
		    	} else {
		    		$a = explode('.', $r['cmt_include']);
		    		$settingsPath = $t[0];
		    		unset ($a);
		    	}
		    	
		    	if (is_file(CMT_TEMPLATE.$settingsPath.'/default_settings.ini')) {
		    		$defaultSettings = @parse_ini_file(CMT_TEMPLATE.$settingsPath.'/default_settings.ini');
		    	}
		    	
				if (!is_array($defaultSettings)) {
					$defaultSettings = array();
				}
		    	
		    	$settings = array_merge($defaultSettings, $settings);

			    // Icon anzeigen
			    switch ($settings['icon']) {
			    	case 'otherIcon':
			    		$iconPath = $settings['iconPath'];
			    		break;
			    		
			    	case 'none':
			    		$iconPath = '';
			    		unset ($settings['iconPath']);
			    		break;
			    		
			    	default:
			    		if (is_file(CMT_TEMPLATE.$includePath.'/'.$settings['iconPath'])) {
			    			$iconPath = $includePath.'/'.$settings['iconPath'];
			    		} else {
			    			$iconPath = 'general/img/'.str_replace('table', $r['cmt_type'], 'cmt_defaulttableicon.png');
			    		}
						$settings['icon'] = 'default';
			    		break;
			    }
			    unset($settings['iconPath']);
			    
			    // 	
			    if ($settings['cmt_ownservice']) {
					$settings['cmt_include_overview'] = $settings['cmt_ownservice'];
				}
			    
			    $parser->SetParserVar('iconPath', $iconPath); 
			break;
		}
		
	    // Einstellung im Parser registrieren
	    $parser->SetMultipleParserVars($settings);

		// Schluss: Template parsen
		$replace .= $parser->parseTemplate($settingsTemplatePath .$settingsTemplate);
	break;
	
	default:
		$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_settings_default.tpl');
	break;	
}

?>