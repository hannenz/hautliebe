<?php
/**
 * Fileselector
 * Hilfsapplikation zum Hochladen, Anzeigen und Ausw�hlen von Dateien und Ordnern
 * 
 * @version 2007-03-29
 * @author J.Hahn <info@contentomat.de>
 * 
 */
namespace Contentomat;

 	// Funktionen
 	/**
 	 * function getFileTypeIcon
 	 * ermittelt anhand des Dateinamens das passende Icon
 	 * 
 	 * @param $fileName string Name der Datei
 	 * @return string Name des Icons
 	 */
 	function getFileTypeIcon($fileName) {
 		return 'default.png';
 		
// 		global $cmtFileTypeIcons;

// 		preg_match('/\.([^.]*)$/i', $fileName, $matchFileType);
// 		if (in_array($matchFileType[1].'.png', $cmtFileTypeIcons)) {
// 			return $matchFileType[1].'.png';
// 		} else {
// 			return 'default.png';
// 		}
	}
 
 	/**
 	 * function checkPathDepth
 	 * ermittelt die aktuelle Tiefe des Verzeichnisses und gibt ggf. eine gek�rzte Pfadangabe zur�ck
 	 * 
 	 * @param $path string Pfadangabe
 	 * @param $depth integer Erlaubte Tiefe
 	 * 
 	 * @return string Gek�rzte Pfandangabe
 	 */
 	function checkPathDepth($path, $maxDepth) {
 		$pathParts = explode('/', $path);
 		$path = trim(implode('/', array_slice($pathParts, 0, $maxDepth)));
 		
 		if ($path != '' && !preg_match('/\/$/', $path)) $path .= '/';  
 		
 		return $path;
 	}

 	/**
 	 * function getActDepth
 	 * ermittelt die aktuelle Tiefe des Verzeichnisses und gibt gsie als Zahl zur�ck
 	 * 
 	 * @param $path string Pfadangabe
 	 * @param $depth integer Erlaubte Tiefe
 	 * 
 	 * @return integer Tiefe als Zahl
 	 */
 	function getActDepth($path) {
 		$pathParts = explode('/',$path);
 		return count($pathParts);
 	}
 	// '-> Funktionen: Ende
 	
  	// Objekte
 	$db = new DBCex();
	$parser = new Parser();
	$file = new Filehandler();
	$session = new Session();
  
 	// GET-Variablen
 	$getVars = array ('actDirectory' => '', 'fileIconSize' => '24', 'showOnlyDirContent' => false, 'cmtApplicationID'=> 0,
 						'action' => '', 'cmt_field' => '');
 	foreach ($getVars as $varName => $defaultValue) {
 		if (trim($_GET[$varName]) != '') {
 			$getVars[$varName] = trim($_GET[$varName]);
 		}
 	}

 	extract ($getVars);
 	
 	// Übergeben Pfad säubern: event. './' oder '../' am Anfang löschen!
 	$actDirectory = preg_replace('/(\.\/|\.\.\/)*/', '', $actDirectory);
 	
	// sonstige Variablen
	$templatePath = CMT_TEMPLATE.'general/appinc_fileselector/';
	$incPath = '../';
	$splittTag = '{SPLITTINFORMATIONHERE}';
	
	$directoryContentHtml = '';
	$fileContentHtml = '';
	$breadcrumbs = '';

	// Verfügbare Dateityp-Icons holen
	$cmtFileTypeIcons = $file->showDirectory(
						array('directory' => CMT_TEMPLATE.'general/img/filetypes/'.$fileIconSize.'px/',
						'showSubDirectories' => false,
						'showOnlyFileTypes' => 'png,gif'));

	// Felddaten holen, z.B. Root
	$db->Query('SELECT cmt_tablename FROM cmt_tables WHERE id =  "'.intval($cmtApplicationID).'"');
	$r = $db->Get(MYSQLI_ASSOC);
	$cmtTable = $r['cmt_tablename'];
	
	$db->Query('SELECT cmt_options FROM cmt_fields WHERE cmt_tablename = "'.$cmtTable.'" AND cmt_fieldname = "'.$cmt_field.'"');
	$r = $db->Get(MYSQLI_ASSOC);

	$cmtOptions = Contentomat::safeUnserialize($r['cmt_options']);
	
	// Bei Uploadfeldern hei�t es 'dir' statt 'path' f�r das root-Verzeichnis -> eher doof!
	if (isset($cmtOptions['dir'])) $cmtOptions['path'] = $cmtOptions['dir'];
	 
	$actDirectoryRoot = preg_replace('/\/{2,}/', '/', PATHTOWEBROOT.$cmtOptions['path'].'/');
	
	// Falls eine Datei �bergeben wurde statt einer reinen Pfadangabe!
	if (!is_dir($actDirectoryRoot.$actDirectory)) {
		$actDirParts = pathinfo($actDirectory);
		$actDirectory = preg_replace('/^\.\//', '', $actDirParts['dirname'].'/');
	}
	
	if (trim($actDirectory) != '') {
		$actDirectory = preg_replace('/\/{1,}$/', '', $actDirectory).'/';
	}
	
	// Nur eine bestimmte Verzeichnistiefe erlaubt?
	if ($cmtOptions['depth']) {
		$actDirectory = checkPathDepth($actDirectory, $cmtOptions['depth']);
		$actDepth = getActDepth($actDirectory);
		$allowedDepth = $cmtOptions['depth'];	
	} else {
		$actDepth = 0;	// $actDepth = 0 => Alle Verzeichnistiefen erlaubt
		$allowedDepth = 0;
	}

	// Nur bestimmte Dateitypen anzeigen??
	if ($cmtOptions['show']) {
		$showOnlyFileTypes = trim(implode(',', $cmtOptions['show']));
	}

	// Bestimmte Dateitypen nicht anzeigen??
	if ($cmtOptions['dontshow']) {
		$dontShowFileTypes = trim(implode(',', $cmtOptions['dontshow']));
	}
	/*
	 * Hauptteil: Aktionen
	 */
	 switch ($cmtAction) {
	 	// Verzechnisinhalt anzeigen
	 	default:
	 		// Statt PATHTOWEBROOT muss hier das Root-Verzeichnis des jeweiligen Feldes stehen!
	 		$dirContent = $file->showDirectory(array('directory' => $actDirectoryRoot.$actDirectory, 
													 'showSubDirectories' => false,
													 'showOnlyFileTypes' => $showOnlyFileTypes,
													 'dontShowFileTypes' => $dontShowFileTypes));

	 		// Templates laden
	 		$templateFile = file_get_contents($templatePath.'cmt_overview_file.tpl');
	 		$templateDir = file_get_contents($templatePath.'cmt_overview_directory.tpl');
	 		
	 		// allgemeing�ltige variablen f�r den Parser
	 		$parser->setParserVar('fileIconSize', $fileIconSize);
	 		$parser->setParserVar('actDirectory', urlencode($actDirectory));
	 		$parser->setParserVar('cmt_field', $cmt_field);
	 		
	 		// Verzeichnis durchlaufen und an Parser �bergeben
	 		if (is_array($dirContent)) {

	 			if ($allowedDepth && $actDepth >= $allowedDepth) {
	 				$parser->setParserVar('isClickable', false);
	 			} else {
	 				$parser->setParserVar('isClickable', true);
	 			}
	 			
		 		foreach ($dirContent as $itemPath => $itemName) {
		 			
		 			// Icon ermitteln
		 			if (is_dir($itemPath)) {

		 				$parser->setParserVar('dirURL', urlencode($itemName.'/'));
		 				$parser->setParserVar('dirName', $itemName);
		 				$parser->setParserVar('dirIcon', 'directory');
		 				$parser->setMultipleParserVars($file->getFileInformations($itemPath));
		 				
		 				$directoryContent[$itemName] = $parser->parse($templateDir);
		 			} else if (!$cmtOptions['onlydir']) {
		 				$parser->setParserVar('fileURL', preg_replace('/^'.preg_quote($actDirectoryRoot, '/').'(\.\/)?/', '', $itemPath));
		 				$parser->setParserVar('fileName', $itemName);
		 				$parser->setParserVar('fileIcon', getFileTypeIcon($itemName));
		 				
		 				$parser->setMultipleParserVars($file->getFileInformations($itemPath));
		 				$fileContent[$itemName] = $parser->parse($templateFile);
		 			}
		 		}
	 		}
	 	break;
	}
	// Sortieren und ausgeben: 1. Ordner
	$rowFlag = 0;

	if (is_array($directoryContent) && (!$allowedDepth || $actDepth <= $allowedDepth)) {

		array_flip($directoryContent);
		natcasesort($directoryContent);
		array_flip($directoryContent);
	
		// schwindelig, aber abwechselnde Reihennummern gehen erst, wenn das Ganze geordnet ist.
		/*
		foreach ($directoryContent as $key => $value) {
			$directoryContent[$key] = str_replace('{rowFlag}', $rowFlag%2, $directoryContent[$key]);
			$rowFlag++;
		}
		*/
		$directoryContentHtml = implode("\n", $directoryContent);
		$parser->setParserVar('directoryContent', $directoryContentHtml);
	}
	
	//... 2. Dateien
	if (is_array($fileContent)) {
		array_flip($fileContent);
		natcasesort($fileContent);
		array_flip($fileContent);
		/*
		foreach ($fileContent as $key => $value) {
			$fileContent[$key] = str_replace('{rowFlag}', $rowFlag%2, $fileContent[$key]);
			$rowFlag++;
		}
		*/
		$fileContentHtml = implode("\n", $fileContent);
		$parser->setParserVar('fileContent', $fileContentHtml);
	}
	
	// Seite anzeigen:
	// 1.Breadcrumb-Navigation
	$actDir = preg_replace ('/\/$/', '', $actDirectory);
	
	if (trim($actDir) != '') {
		$dirNames = explode ('/', trim($actDir));
	} else {
		$dirNames = array();
	}
	$dirLinks = $dirNames;

	array_unshift($dirNames, 'root');
	array_unshift($dirLinks, '');
	
	$breadcrumbs = '';
	$i = 0;
	foreach ($dirLinks as $key=>$dirLink) {
		//$folderPath .= preg_replace('/\/{1,}$/', '', $dirLink).'/';
		if ($dirLink != '') {
			$folderPath .= $dirLink.'/';
		}
		//$breadcrumbs .= '<a href="'.SELFURL.sid='.SID.'&cmtApplicationID='.CMT_APPID.'&cmt_extapp=fileselector&actDirectory='.urlencode($folderPath).'&showOnlyDirContent=true&cmt_field='.$cmt_field.'\', \''.urlencode($folderPath).'\')">'.$dirNames[$key].'</a><span class="breadcrumbSeparator">/</span>';
		$breadcrumbs .= '<a href="Javascript:void(0);" data-url="' . SELFURL . '&cmt_extapp=fileselector&actDirectory='
						. urlencode($folderPath)
						. '&showOnlyDirContent=true&cmt_field=' . $cmt_field
						. '&actDirectory=' . urlencode($folderPath) .'" ' 
						. 'class="cmtBreadcrumbLink" ' 
						. 'data-filepath="' . urlencode($folderPath) . '">' 
						. $dirNames[$key]
						. '</a><span class="cmtBreadcrumbSeparator"></span>';
	} 

	if (!$showOnlyDirContent) {
		$parser->setParserVar('breadcrumbNavigation', $breadcrumbs);
		$parser->setParserVar('fsPath', htmlentities($actDirectory.$actDirParts['basename']));
		//$parser->setParserVar('cmtFileSelectorJavascript', $cmtFileSelectorJavascript);
			
		echo $parser->parseTemplate($templatePath.'cmt_fileselector.tpl');
		exit;
	} else {
		
		$returnData = array (
			'breadcrumbContent' => $breadcrumbs,
			'directoryContent' => $directoryContentHtml,
			'fileContent' => $fileContentHtml
		);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode($returnData); 
		//echo $breadcrumbs.$splittTag.$directoryContentHtml.$fileContentHtml.$splittTag;
		exit();
	} 
?>
