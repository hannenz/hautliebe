<?php
/**
 * class fileHandler
 * 
 * Klasse, die Datei- und Ordneroperationen zur Verfügung stellt
 * 
 * Diese Klasse stellt die Grundwerkzeuge für die Behandlung von Dateien auf dem
 * Server zur Verfügung.
 * 
 * @author J.Hahn <info@content-o-mat.de>, J.Braun <info@content-o-mat.de>
 * @version 2016-10-10
 */
 
 // TODO: Möglichkeit einbauen, Pfadtiefe anzugeben: $params['pathDepth'] = 2 => Unterordner der 2. Ebene werden angezeigt.
 // TODO: Fehlermeldungen umstellen auf Fehlernummern!

namespace Contentomat;

class FileHandler {

	private $dirStarttime;
	private $dirStructureIncomplete;
	private $dirStartDirectory;

	public  $maxUploadSize;
	public  $showOnlyFileTypes;
	public  $dontShowFileTypes;
	public  $lastSuccessMessage;
	public  $lastErrorMessage;
	public  $lastErrors;
	public  $lastSuccess;
	
	/**
	 * Konstruktor-Methode
	 * 
	 * @param void Erwartet keine Parameter
	 */
	public function __constuct() {
		$this->resetMessages();
	}
	
	/**
	 * Gibt die Verzeichnisstruktur des übergebenen Pfades in einem Array
	 * zurück
	 * 
	 * Folgende Parameter können per Array übergeben werden (Schlüssel:
	 * Variablenname, Wert: Variablenwert)
	 * 
	 * @param phpDocumentorSucks string Diese Zeile wird einfach ignorriert!
	 * @param directory string Name des Verzeichnisses
	 * @param showSubdirectories boolean Sollen alle Unterverzeichnisse rekursiv ausgelesen werden. Default ist true.
	 * @param showOnlyDirectories boolean Nur Verzeichnisse werden zurückgegeben. Default ist false
	 * @param key string Gibt an, was als Schlüssel des zurückgegebenen Arrays verwendet werden soll:
	 * 1. 'filePath' => kompletter Pfad der Datei
	 * 2. 'fileName' => nur der Name der Datei
	 * 3. '' => (leerer Wert) nichts, es wird ein numerischer Schlüssel verwendet
	 * 4. Default- Wert ist 'filePath';
	 * 
	 * @param value string Bestimmt den Arraywert analog zum Schlüssel. Die drei o.g. Werte sind möglich
	 * @param pathPrefix string String der jeder Pfadangabe im Ausgabearray vorangestellt wird. Default ist leer.
	 * @param directoryPrefix string String der jedem Ordnernamen vorangestellt wird (sinnvoll zum Kennzeichnen von Ordnern/Dateien)
	 * @param filePrefix string String der jedem Dateinamen vorangestellt wird (sinnvoll zum Kennzeichnen von Ordnern/Dateien)
	 * @param maxExecutionTime integer Wert in Sekunden, die zur Ausführung des PHP-Skriptes zur Verfügung stehen. Nach Ablauf dieser Zeit wird die Funktion abgebrochen um einen Seiten-Timeout zu vermeiden. Default ist -1 (unbegrenzte Skriptausführungszeit)
	 * @param showOnlyFileTypes string Zeigt nur Dateien dieses Typs an! Verschiedene Dateitypen k�nnen kommasepariert angegeben werden ("gif, jpg, png")
	 * @param dontShowFileTypes string Zeigt Dateien dieses Typs nicht an! Verschiedene Dateitypen k�nnen kommasepariert angegeben werden ("gif, jpg, png")
	 * @param showDirectories boolean Ist dieser Wert auf false gesetzt, dann werden Unterverzeichnisse nicht angezeigt. Default ist true
	 * @return array Array, welches je nach Parameterübergabe die Verzeichnisstruktur enthält
	 * 
	 */
	 public function showDirectory($params) {
		$defaultParams = array(	'directory' => './', 
								'showSubDirectories' => true, 
								'showOnlyDirectories' => false,
								'key' => 'filePath', 
								'value' => 'fileName', 
								'pathPrefix' => '', 
								'directoryPrefix' => '',
								'filePrefix' => '', 
								'maxExecutionTime' => -1, 
								'showOnlyFileTypes' =>'', 
								'dontShowFileTypes' => '',
								'showDirectories' => true,
								'showHiddenDirectories'=>false 
							);

		$params = array_merge($defaultParams, $params);

		$params['directory'] = $this->formatDirectory($params['directory']);
		$this->dirStartDirectory = $params['directory'];
		$this->dirStarttime = time();
		$this->dirStructureIncomplete = false;

		if ($params['showOnlyFileTypes']) {
			$this->showOnlyFileTypes = $this->arrayToLower(explode(',', $params['showOnlyFileTypes']));
		} else {
			$this->showOnlyFileTypes = '';
		}

		if ($params['dontShowFileTypes']) {
			$this->dontShowFileTypes = $this->arrayToLower(explode(',', $params['dontShowFileTypes']));
		} else {
			$this->dontShowFileTypes = '';
		}

		$files = $this->seekDirectoryStructure($params);
		if (!is_array($files)) {
			$files = array();
		}
	 	$this->dirStarttime = false;

	 	if ($this->dirStructureIncomplete) {
	 		$files[] = 'Weitere Files vorhanden...';	
	 	}
	 	return $files;
	 }
	 
	/**
	 * Hilfsfunktion: Liest die Verzeichnisse rekursiv aus und prüft, ob die maximale Bearbeitungszeit überschritten wird (timeout)
	 * 
	 * @param $params array Array mit den Parameter, die an showDirectory �bergeben wurden
	 * @return array Das Array mit der Verzeichnisstruktur
	 */
	private function seekDirectoryStructure ($params) {
	
		if ($params['maxExecutionTime'] > 0 && time() >= ($this->dirStarttime + intval($params['maxExecutionTime']))) {
			$this->dirStructureIncomplete = true;
			return;	// Files event. undefiniert
		}
		$varWrapper = array('filePath' => '$params[\'pathPrefix\'].$filePath', 'fileName' => '$fileName');
		
		$actDir = @dir($params['directory']);
		if (!isset($files)) {
			$files = array();
		}

		// Einlesen
		if (!$actDir) {
			return;
		}
		while (($file = $actDir->read()) !== false) {
			if ($file != '.' && $file != '..') {
				$directoryStructure[] = $file;
			}
		}
		$actDir->close();

		// Einträge erzeugen
		if (!is_array($directoryStructure)) {
			return;
		}
		//print_r ($directoryStructure);
		foreach ($directoryStructure as $fileName) {
			$filePath = $params['directory'].$fileName;
			$fileName = preg_replace('/^'.preg_quote($this->dirStartDirectory, '/').'/', '', $filePath);  
			
			// Falls Verzeichnis, dann anzeigen und ggf. rekursiv das Unterverzeichnis auslesen
			if (is_dir($filePath)) {
				if ($params['showSubDirectories']) {
					$filePath .= '/';
					$fileName .= '/';
					// $prefix = $params['directoryPrefix'];

					// skip hidden directories like (.svn)
					if(!$params['showHiddenDirectories'] && preg_match("/\/\.\w+/",$filePath)){
						$skipDirecotry = true;
					} else {
						$skipDirectory = false;
					}

					if(!$skipDirecotry){

						if (!is_array($this->showOnlyFileTypes) && !is_array($this->dontShowFileTypes)) {
							eval ('$files['.$varWrapper[$params['key']].'] = "'.$params['directoryPrefix'].$varWrapper[trim($params['value'])].'";');
						}
						$recParams = $params;

						$recParams['directory'] = $filePath;
						$filesInSubdirectory = $this->seekDirectoryStructure($recParams);
						if (is_array($filesInSubdirectory)) {
							$files = array_merge($files, $filesInSubdirectory);
						}
					} 
					
//					if (!is_array($this->showOnlyFileTypes) && !is_array($this->dontShowFileTypes)) {
//						eval ('$files['.$varWrapper[$params['key']].'] = "'.$params['directoryPrefix'].$varWrapper[trim($params['value'])].'";');
//					}
//					$recParams = $params;
//
//					$recParams['directory'] = $filePath;
//					$filesInSubdirectory = $this->seekDirectoryStructure($recParams);
//					if (is_array($filesInSubdirectory)) {
//						$files = array_merge($files, $filesInSubdirectory);
//					}
				} else if ($params['showDirectories']) {
					eval ('$files['.$varWrapper[$params['key']].'] = "'.$params['directoryPrefix'].$varWrapper[trim($params['value'])].'";');
				}
			} else {
				// Darf Datei mit?
				if (is_array($this->showOnlyFileTypes) || is_array($this->dontShowFileTypes)) {
	
					$pathInfo = @pathinfo($fileName);
	
					if (is_array($this->showOnlyFileTypes) && !in_array(strtolower($pathInfo['extension']), $this->showOnlyFileTypes)) {
						continue;
					} else if (is_array($this->dontShowFileTypes) && in_array(strtolower($pathInfo['extension']), $this->dontShowFileTypes)) {
						continue;
					}
					
				}
								
				if (!$params['showOnlyDirectories']) {
					eval ('$files['.$varWrapper[$params['key']].'] = "'.$params['filePrefix'].$varWrapper[trim($params['value'])].'";');
				}
			}
		}
		return $files;	 	
	 }

	 /**
	  * public function createFile()
	  * Creates a new blank file and writes optionally content in it.
	  *
	  * @param array $params Parameters in associative Array: 
	  * - 'file' (string) => path and name of the file that should be created
	  * - 'content' (string) => optional, content of the new file
	  *
	  * @return boolean
	  */
	 public function createFile($params) {
	 	$defaultParams = array(
	 		'file' => '',
	 		'content' => ''
	 	);
	 	$params = array_merge($defaultParams, $params);

	 	$filePath = trim($params['file']);
	 	$fileContent = trim($params['content']);
	 	
	 	// error when filepath is missing or file already exists
	 	if (!$filePath || file_exists($filePath)) {
	 		return false;
	 	}
	 	
	 	// create new file
 		$fh = @fopen($filePath, 'w');
 		if (!$fh) {
 			return false;
 		}
 		
 		if ($fileContent) {
 			$check = @fwrite($fh, $fileContent);
 			if (!$check) {
 				return false;
 			}
 		}
 		
 		return true;
	 }
	 
	/**
	 * Funktion: Löscht Datei(en)
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param file mixed Array (mehrere Dateien) oder String (einzelne Datei), enthält den/ die Namen, der Datei(en), die gelöscht werden soll(en)
	 * @param directory string Optional: Name des Verzeichnises, das vor den Dateinamen gehängt wird.
	 * @param fileDeletionSuccess string Meldung, die im Returnarray gespeichert wird, wenn die Datei erfolgreich gelöscht wurde. Der Dateiname kann mit '{FILE}' eingesetzt werden.
	 * @param fileDeletionError string Meldung, die im Returnarray gespeichert wird, wenn die Datei nicht gelöscht werden konnte. Der Dateiname kann mit '{FILE}' eingesetzt werden.
	 * @param directoryDeletionSuccess string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis erfolgreich gelöscht wurde. Der Verzeichnissname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @param directoryDeletionError string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis nicht gelöscht werden konnte. Der Verzeichnissname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @return array Erfolg- oder Fehlermeldungen als Array
	 */
	public function deleteFile($params) {
		$defaultParams = array(	'file' => '', 
								'directory' => '', 
								'fileDeletionSuccess' => '{FILE} erfolgreich gelöscht', 
								'fileDeletionError' => '{FILE} konnte nicht gelöscht werden.',
								'directoryDeletionSuccess' => 'Ordner {DIRECTORY} erfolgreich gelöscht',
								'directoryDeletionError' => 'Ordner {DIRECTORY} konnte nicht gelöscht werden.'
								);
		$params = array_merge($defaultParams, $params);
		
		$this->resetMessages();
		$deletionMessage = array();
		
		if (!is_array($params['file'])) {
			 $params['file'] = array(0 => $params['file']);
		}
		
		foreach ($params['file'] as $filepath) {
			$filepath = $params['directory'].$filepath;
			if (is_dir($filepath)) {
				if (!@rmdir ($filepath)) {
					$deletionMessage[] = str_replace('{DIRECTORY}', $filepath, $params['directoryDeletionError']);
				} else {
					$deletionMessage[] = str_replace('{DIRECTORY}', $filepath, $params['directoryDeletionSuccess']);
				}
			} else {
				if (!@unlink ($filepath)) {
					$amTemp = str_replace('{FILE}', $filepath, $params['fileDeletionError']);
					$deletionMessage[] = $amTemp;
					$this->lastErrorMessage[] = $amTemp;
					$this->lastErrors++;
				} else {
					$amTemp = str_replace('{FILE}', $filepath, $params['fileDeletionSuccess']);
					$deletionMessage[] = $amTemp;
					$this->lastSuccessMessage[] = $amTemp;
					$this->lastSuccess++;
				}
			}
		}
		
		return $deletionMessage;
	}



	/**
	 * Funktion: Verschiebt Datei(en)
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param sourceFile String oder Array, der den oder die Quelldateipfade enthält
	 * @param newFilename String oder Array, der den oder die neuen Namen für die Datei/en enthält. Optional. Ansonsten wird der Originalname verwendet.
	 * @param sourceFileAddPath String Optionale Angabe. Es kann ein Pfad angegeben werden, der allen Dateipfaden in 'sourceFile' vorangestellt wird.
	 * @param targetDirectory string Pfad des zielverzeichnisses
	 * @param actionSuccess string Meldung, die im Returnarray gespeichert wird, wenn die Datei erfolgreich verschoben wurde. Der Quell-Dateiname kann mit '{FILE}' eingesetzt werden, der Ziel-Dateiname mitz '{TARGET}'.
	 * @param actionError string Meldung, die im Returnarray gespeichert wird, wenn die Datei nicht gel�scht werden konnte. Der Quell-Dateiname kann mit '{FILE}' eingesetzt werden, der Ziel-Dateiname mitz '{TARGET}'.
	 * @return array Erfolg- oder Fehlermeldungen als Array
	 */
	public function moveFile($params) {
		$defaultParams = array('sourceFile' => '',
								'newFilename' => '',
								'targetDirectory' => '',
								'sourceFileAddPath' => '',
								'actionSuccess' => '{FILE} erfolgreich verschoben', 
								'actionError' => '{FILE} konnte nicht nach {TARGET} verschoben werden.',
							  );
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();

		if (!is_array($params['sourceFile'])) {
			 $params['sourceFile'] = array(0 => $params['sourceFile']);
		}

		if (!is_array($params['newFilename'])) {
			 $params['newFilename'] = array(0 => $params['newFilename']);
		}
		
		$result = true;		
		
		foreach ($params['sourceFile'] as $key => $sourceFile) {
			
			if ($params['newFilename'][$key]) {
				$fileName = $params['newFilename'][$key];
			} else {
				$fileName = basename ($sourceFile);
			}
			
			if ($params['targetDirectory']) {
				$targetFile = $this->formatDirectory($params['targetDirectory']."/").$fileName;
			} else {
				$targetFile = $fileName;
			}

			if ($params['sourceFileAddPath']) {
				$sourceFile = $this->formatDirectory($params['sourceFileAddPath']).$sourceFile;
			}
			
			if (!@rename ($sourceFile, $targetFile)) {
// 				$amTemp = str_replace('{FILE}', $sourceFile, $params['actionError']);
// 				$amTemp = str_replace('{TARGET}', $targetFile, $amTemp);
// 				$actionMessage[] = $amTemp;
// 				$this->lastErrorMessage[] = $amTemp;
// 				$this->lastErrors++;
				
				$result = false;
			} else {
// 				$amTemp = str_replace('{FILE}', $sourceFile, $params['actionSuccess']);
// 				$amTemp = str_replace('{TARGET}', $targetFile, $amTemp);
// 				$actionMessage[] = $amTemp;
// 				$this->lastSuccessMessage[] = $amTemp;
// 				$this->lastSuccess++;
			}
		}
		return $result;
	}



	/**
	 * public function renameFile()
	 * Benennt Datei(en) um
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param sourceFile mixed String oder Array, Pfad der Datei(en), die umbenannt werden soll(en)
	 * @param newFilename mixed String oder Array mit den neuen Dateinamen (ohne Pfadangabe!)
	 * @param actionSuccess string Meldung, die im Returnarray gespeichert wird, wenn die Datei erfolgreich umbenannt wurde. Der Quell-Dateiname kann mit '{FILE}' eingesetzt werden, der Ziel-Dateiname mitz '{TARGET}'.
	 * @param actionError string Meldung, die im Returnarray gespeichert wird, wenn die Datei nicht umbenannt werden konnte. Der Quell-Dateiname kann mit '{FILE}' eingesetzt werden, der Ziel-Dateiname mitz '{TARGET}'.
	 * @return boolean
	 */
	public function renameFile($params) {
		$defaultParams = array(	'sourceFile' => '',
								'newFilename' => '',
								'actionSuccess' => '{FILE} erfolgreich in {TARGET} umbenannt', 
								'actionError' => '{FILE} konnte nicht in {TARGET} umbenannt werden.'
							  );
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();

		if (!is_array($params['sourceFile'])) {
			 $params['sourceFile'] = array(0 => $params['sourceFile']);
		}
		if (!is_array($params['newFilename'])) {
			 $params['newFilename'] = array(0 => $params['newFilename']);
		}
		
		$c = 0;
		foreach ($params['sourceFile'] as $sourceFile) {
			$newFilename = $this->formatDirectory(dirname($sourceFile)).$params['newFilename'][$c];

			if (!@rename ($sourceFile, $newFilename)) {
				$amTemp = str_replace('{FILE}', basename($sourceFile), $params['actionError']);
				$amTemp = str_replace('{TARGET}', basename($newFilename), $amTemp);
				$actionMessage[] = $amTemp;
				$this->lastErrorMessage[] = $amTemp;
				$this->lastErrors++;
				
				$return = false;
			} else {
				$amTemp = str_replace('{FILE}', basename($sourceFile), $params['actionSuccess']);
				$amTemp = str_replace('{TARGET}', basename($newFilename), $amTemp);
				$actionMessage[] = $amTemp;
				$this->lastSuccessMessage[] = $amTemp;
				$this->lastSuccess++;
				
				$return = true;
			}
			
			$c++;
		}
		
		return $return;
	}



	/**
	 * public function createDirectory()
	 * erstellt Ordner
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param directory mixed String oder Array, enthält den/die Namen des/der neuen Verzeichnisse
	 * @param newDirectoryname mixed Veraltet: String oder Array, enthält den/die Namen des/der neuen Verzeichnisse
	 * @param chmod octal Dateirechte in Oktalschreibweise!!! Default ist 0777 (ist auch PHP Standard).
	 * @param actionSuccess string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis erfolgreich erstellt wurde. Der Verzeichnisname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @param actionError string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis nicht erstellt werden konnte. Der Verzeichnisname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @return array Erfolg- oder Fehlermeldungen als Array
	 */
	public function createDirectory($params) {
		$defaultParams = array( 'newDirectoryname' => '',
								'directory' => '',
								'chmod' => 0777,
								'actionSuccess' => 'Das Verzeichnis "{DIRECTORY}" wurde erfolgreich erstellt.', 
								'actionError' => 'Das Verzeichnis "{DIRECTORY}" konnte nicht erstellt werden.'
							  );
	  
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();

		if ($params['newDirectoryname']) $params['directory'] = $params['newDirectoryname'];
		
		if (!is_array($params['directory'])) {
			 $params['directory'] = array(0 => $params['directory']);
		}			
		
		foreach ($params['directory'] as $newDirname) {
			$newDir = $this->formatDirectory($newDirname);

			if (!@mkdir ($newDir, $params['chmod'])) {
				$amTemp = str_replace('{DIRECTORY}', $newDirname, $params['actionError']);;
				$actionMessage[] = $amTemp;
				$this->lastErrorMessage[] = $amTemp;
				$this->lastErrors++;
			} else {
				$amTemp = str_replace('{DIRECTORY}', $newDirname, $params['actionSuccess']);
				$this->lastSuccessMessage[] = $amTemp;
				$this->lastSuccess++;
				$actionMessage[] = $amTemp;
			}
		}
		if ($this->lastErrors) {
			return false;
		} else {
			return true;
		}
		//return $actionMessage;
	}

	/**
	 * function deleteDirectory()
	 * löscht Ordner
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param directory mixed String oder Array, enthält den/die Namen des/der Verzeichnisse, die gelöscht werden sollen
	 * @param actionSuccess string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis erfolgreich erstellt wurde. Der Verzeichnisname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @param actionError string Meldung, die im Returnarray gespeichert wird, wenn das Verzeichnis nicht erstellt werden konnte. Der Verzeichnisname kann mit '{DIRECTORY}' eingesetzt werden.
	 * @return array Erfolg- oder Fehlermeldungen als Array
	 */
	public function deleteDirectory($params) {
		$defaultParams = array(	'directory' => '',
								'actionSuccess' => 'Das Verzeichnis "{DIRECTORY}" wurde erfolgreich gel&ouml;scht.', 
								'actionError' => 'Das Verzeichnis "{DIRECTORY}" konnte nicht gel&ouml;scht werden.',
								'actionErrorIsNoDirectory' => '"{DIRECTORY}" ist kein Verzeichnis und konnte deshalb nicht gel&ouml;scht werden.',
							  );
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();

		if (!is_array($params['directory'])) {
			 $params['directory'] = array(0 => $params['directory']);
		}			
		
		foreach ($params['directory'] as $delDir) {
			$delDir = $this->formatDirectory($delDir);
			
			if (is_dir($delDir)) {
				if (!@rmdir ($delDir)) { 
					$amTemp = str_replace('{DIRECTORY}', $delDir, $params['actionError']);
					$actionMessage[] = $amTemp;
					$this->lastErrorMessage[] = $amTemp;
					$this->lastErrors++;
					
					$return = false;
					
				} else {
					$amTemp = str_replace('{DIRECTORY}', $delDir, $params['actionSuccess']);
					$this->lastSuccessMessage[] = $amTemp;
					$this->lastSuccess++;
					$actionMessage[] = $amTemp;

					$return = true;
				}
			} else {
				$amTemp = str_replace('{DIRECTORY}', $delDir, $params['actionErrorIsNoDirectory']);;
				$actionMessage[] = $amTemp;
				$this->lastErrorMessage[] = $amTemp;
				$this->lastErrors++;

				$return = false;
			}
		}
		
		return $return;
	}

	/**
	 * public function handleDownload()
	 * regelt Dateidownloads
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param downloadFile string Pfad der Datei, die zum Download angeboten werden soll
	 * @param downloadFileAlias string Aliasname, der im Downloaddialog angezeigt wird (und im Header versendet wird)
	 * @param actionError string Fehlermeldung, falls die Datei nicht exisitert. Der Dateiname kann in der Meldung mit '{FILE}' gekennzeichnet werden
	 * @param deleteFile boolean If true the file will be deleted after sending to the browser (download)
	 * @return string Es kann nur eine Fehlermeldung zurückgegeben werden, sofern die Datei nicht existiert.
	 */
	public function handleDownload ($params) {

		$defaultParams = array(
			'downloadFile' => '',
			'downloadFileAlias' => '',
			'actionError' => 'Die Datei \'{FILE}\' existiert nicht',
			'deleteFile' => false
		);
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();		// outdated!

		$filename = $params['downloadFile'];

		// Kein Dateiname oder Datei existiert nicht
		if (!file_exists($filename)) {
			return array(str_replace('{FILE}', $filename, $params['actionError']));
		}
		
		// Aliasname?
		if ($params['downloadFileAlias']) {
			$filenameAlias = $params['downloadFileAlias'];
		} else {
			$filenameAlias = basename($filename); 
		}
		
		// Dateiinformationen
		$file = pathinfo($filename);
		$fileExtension = strtolower($file['extension']);
				
		// benötigt IE, sonst wird Content-disposition ignoriert
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		
		$mimeTypes = array(
			'pdf' => 'application/pdf',
			'exe' => 'application/octet-stream',
			'zip' => 'application/zip',
			'doc' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'jpeg' => 'image/jpg',
			'jpg' => 'image/jpg',
		);
		$defaultDownloadMimeType = 'application/force-download';
		
		if (isset($mimeTypes[$fileExtension])) {
			$ctype = $mimeTypes[$fileExtension];
		} else {
			$ctype = $defaultDownloadMimeType;
		}
		
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=".$filenameAlias.";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		
		readfile($filename);
		
		if ($params['deleteFile']) {
			@unlink ($filename);
		}
		exit(); 
	}

	/**
	 * public function handleUpload ()
	 * regelt Dateiuploads
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param uploadFile array Array, Angaben zur hochgeladenen Datei wie in $_FILES. üblicherweise muss hier die Superglobale $_FILE übergeben werden
	 * @param targetDirectory string Pfad des Verzeichnisses, in welches die hochgeladenen Dateien kopiert werden sollen
	 * @param newFilename array Array mit einem/ den neuen Namen, die die Datei(en) nach dem Upload bekommen sollen.
	 * Achtung: sollen mehrere Dateien hochgeladen werden, muss in dem Array als Schlüssel der Name des Uploadfeldes (Schlüssel von $_FILE[]) stehen, als 
	 * korrespondierender Wert der neue Dateiname. Sofern kein targetDirectory angegeben wird, muss hier eine komplette 
	 * Pfadangabe stehen. Ist targetDirectory gesetzt, kann hier auch nur der Dateiname stehen. Alle Dateien werden dann in targetDirectory hochgeladen.
	 * @param newFilenameAppendix string Optional: dieser String wird jedem Namen einer erfolgreich hochgeladenen Datei angehängt
	 * @param makeFilenameWebsave boolean Tauscht "webunsichere" Zeichen im Dateinamen aus (z.B. Leerzeichen => '_', Umlaute => 'ae', 'ue', etc). Default-Wert ist true
	 * @param chmod string Der Chmod-Wert der für die hochgeladene Datei gesetzt wird. Default ist '0755' (oktal)
	 * @param actionSuccess string Erfolgsmeldung, falls die Datei hochgeladen wurde. Der alte und der neue Dateiname können in der Meldung mit '{FILE}', bzw. '{NEWFILE}' ersetzt werden
	 * @param actionError string Fehlermeldung, falls die Datei nicht exisitert. Der alte und der neue Dateiname können in der Meldung mit '{FILE}', bzw. '{NEWFILE}' ersetzt werden
	 * 
	 * @return boolean
	 */
	public function handleUpload ($params) {
		$defaultParams = array(	'uploadFile' => '',
								'targetDirectory' => '',
								'newFilename' => '',
								'newFilenameAppendix' => '',
								'makeFilenameWebsave' => true,
								'chmod' => 0666,
//								'actionSuccess' => 'Die Datei \'{FILE}\' wurde erfolgreich hochgeladen.',
//								'actionError' => 'Die Datei \'{FILE}\' konnte nicht hochgeladen werden.'
							  );
		$params = array_merge($defaultParams, $params);
		$this->resetMessages();
		
/*
		if (!is_array($params['uploadFile'])) {
			 $params['uploadFile'] = array(0 => $params['uploadFile']);
		}
*/
		if (!is_array($params['newFilename'])) {
			$pt = $params['uploadFile'];
			$pt = array_shift($pt);
			$params['newFilename'] = array($pt['name'] => $params['newFilename']);
		}
		
		$filesCount = count($params['uploadFile']);
		
		$c = 0;
		
		$errors = 0;
		
		//Dateien einzeln verabreiten
		foreach ($params['uploadFile'] as $uploadFieldName => $fileDetails) {

			// Daten ermitteln
			$fileSource = $fileDetails['tmp_name'];
			$fileName = $fileDetails['name'];
			$fileSize = $fileDetails['size'];

			if (trim($fileName) != '') {			
				$ft = pathinfo($fileName);
				$fileExtension = $ft['extension'];
				$fileNameWithoutExtension = str_replace('.'.$fileExtension, '', $ft['basename']);
				
				
				if ($params['makeFilenameWebsave'] == true) {
					// Sonderzeichen entfernen
					$fileNameWithoutExtension = $this->makeNameWebSave($fileNameWithoutExtension);
				}
				
				if ($params['newFilename'][$uploadFieldName]) {
					if ($params['targetDirectory']) {
						$newFile = $this->formatDirectory($params['targetDirectory']).$params['newFilename'][$uploadFieldName];
					} else {
						$newFile = $params['newFilename'][$uploadFieldName];
					}
				} else {
					$newFile = $this->formatDirectory($params['targetDirectory']).$fileNameWithoutExtension.$params['newFilenameAppendix'];
					if (!empty ($fileExtension)){
						$newFile .= '.'.$fileExtension;
					}
				}
					
				//Datei ins Verzeichnis kopieren
				if (!@copy($fileSource, $newFile)) {
					$amTemp = str_replace('{FILE}', $fileName, $params['actionError']);
					$amTemp = str_replace('{NEWFILE}', $newFile, $amTemp);
					$actionMessage[] = $amTemp;
					$this->lastErrorMessage[] = $amTemp;
					$this->lastErrors++;
					
					$errors++;
				} else {
					$amTemp = str_replace('{FILE}', $fileName, $params['actionSuccess']);
					$amTemp = str_replace('{NEWFILE}', $newFile, $amTemp);
					$actionMessage[] = $amTemp;
					$this->lastSuccessMessage[] = $amTemp;
					$this->lastSuccess++;
					@chmod ($newFile, $params['chmod']);
				}
			}
		}
		
		return !(boolean) $errors;
	}

	/**
	 * public protected function copyFile()
	 * Copies (duplicates) a file.
	 *
	 * @param array $params All parameters in associative array (key=>value pair):
	 * - sourceFile string Name or path of the file to copy
	 * - targetFile string Name or path of the new file
	 * - directory string Optional: directory path, will be prefixed to sourceFile and targetFile
	 * 
	 * @return boolean
	 */
	public function copyFile($params = array()) {
		
		$defaultParams = array(
			'sourceFile' => '',
			'targetFile' => '',
			'directory' => ''
		);
		$params = array_merge($defaultParams, $params);
		
		$source = trim($params['sourceFile']);
		$target = trim($params['targetFile']);
		
		if (!$source || !$target) {
			return false;
		}
		
		if ($params['directory']) {
			
			$source = $this->formatDirectory($params['directory'].'/') . $source;
			$target = $this->formatDirectory($params['directory'].'/') . $target;
		}

		return (boolean) @copy($source, $target);
	}
	
	public function copyDirectory($params = array()) {
		
		$defaultParams = array(
			'sourceDirectory' => '',
			'targetDirectory' => '',
			'directory' => ''
		);
		$params = array_merge($defaultParams, $params);

		$source = trim($params['sourceDirectory']);
		$target = trim($params['targetDirectory']);

		if ($params['directory']) {
			$source = $this->formatDirectory($params['directory'].'/' . $source);
			$target = $this->formatDirectory($params['directory'].'/' . $target);
		}
		
		return $this->copyDirectoryContent($source, $target);
	}
	
	protected function copyDirectoryContent($sourceDirectory, $targetDirectory) {
		
		$dir = opendir($sourceDirectory);
		$check = @ mkdir($targetDirectory);
		
		if (!$check) {
			return false;
		}
		$check = false;
		
		while (($file = readdir($dir)) !== false) {

			if ($file != '.' && $file != '..') {
				if (is_dir($sourceDirectory . '/' . $file)) {
					$check = $this->copyDirectoryContent($sourceDirectory . '/' . $file,$targetDirectory . '/' . $file);
				} else {
					$check = @ copy($sourceDirectory . '/' . $file,$targetDirectory . '/' . $file);
				}
				
				if (!$check) {
					return false;
				}
			}
		}
		closedir($dir);

		return $check;
	}
	
	/**
	 * public function getFileSize ()
	 * liefert eine formatierte Dateigröße
	 * 
	 * @param $params array Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * @param sourceFile mixed Array oder String mit Dateipfad(en)
	 * @param sizeBase string Basis der Größenangaben: B, KB, MB oder GB
	 * @param nameBytes string Bezeichnung für "Bytes"
	 * @param nameKilobytes string Bezeichnung für "Kilobytes"
	 * @param nameMegabytes string Bezeichnung für "Megabytes"
	 * @param nameGigabytes string Bezeichnung für "Gigabytes"
	 * @param addSizeName boolean Flag, wenn true (default) wird die Größenbezeichnung zum Wert hinzugefügt
	 * @param roundSize number Anzahl der Nachkommastellen beim Runden
	 * 
	 * @return mixed Falls mehere Dateinpfade in einem Array übergeben worden sind, wird ein Array zurückgegeben, ansonsten ein String
	 */
	public function getFileSize ($params) {
		$defaultParams = array(	'sourceFile' => '',
								'sizeBase' => 'MB',
								'nameBytes' => 'Bytes',
								'nameKilobytes' => 'KB',
								'nameMegabytes' => 'MB',
								'nameGigabytes' => 'GB',
								'addSizeName' => true,
								'roundSize' => 2
							  );
		$params = array_merge($defaultParams, $params);

		$sizeNames = array (	'B' => $params['nameBytes'],
								'KB' => $params['nameKilobytes'],
								'MB' => $params['nameMegabytes'],
								'GB' => $params['nameGigabytes']
		);
		
		$sizeQuotients = array ('B' => 1, 'KB' => 1024, 'MB' => pow(1024, 2), 'GB' => pow(1024, 3));
		$sQ = $sizeQuotients[$params['sizeBase']];
		if (!$sQ) {
			$sQ = 1;
		}
		
		if (!is_array($params['sourceFile'])) {
			$sourceFiles = array($params['sourceFile']);
		} else {
			$sourceFiles = $params['sourceFile'];
		}
		
		$fileSizes = array();
		
		foreach ($sourceFiles as $sourceFile) {
			$fs = intval(filesize($sourceFile));
			$fileSize = round($fs/$sQ, $params['roundSize']);

			if ($params['addSizeName']) {
				$fileSize .= ' '.$sizeNames[$params['sizeBase']];
			}
			$fileSizes[$sourceFile] = $fileSize;
		}
		
		if (!is_array($params['sourceFile'])) {
			return array_shift($fileSizes);
		} else {
			return $fileSizes;
		}
	}
	
	/**
	 * public function getFileInformations()
	 * Liefert Informationen wi eGröße, Besitzer oder Änderungsdatum zu einer Datei zurück. Die Daten kommen 
	 * hauptsächlich aus der PHP-Funktionen stat()
	 *
	 * @param string $filePath Dateipfad
	 * @param array $params Array mit weiteren, optionalen Parametern
	 *
	 * @return array Assoziatives Array mit ausführlichen Dateiinformationen.
	 */
	public function getFileInformations($filePath, $params=array()) {
		
		if (!is_array($params)) {
			$params = array();
		}
		
		$defaultParams = array(
			'dateFormat' => 'd.m.Y',
			'timeFormat' => 'H:i',
			'round' => 2,
			'baseUnit' => 'KB'
		);

		$params = array_merge($defaultParams, $params);
		
		
		$fileInfo = pathinfo($filePath);
		
		if (!is_array($fileInfo) || empty ($fileInfo)) {
			return array();
		} else {
			$fileInfo = array_merge($fileInfo, stat($filePath));
			$fileInfo = array_merge($fileInfo, $this->formatFileSize($fileInfo['size']));
			
			// Das hier muss noch konfigurerbar gemacht werden
			$dateFormat = 'd.m.Y';
			$timeFormat = 'H:i';
			
			// Letzte Änderung
			$fileInfo['lastChangeDate'] = date($dateFormat, $fileInfo['mtime']);
			$fileInfo['lastChangeTime'] = date($timeFormat, $fileInfo['mtime']);
			
			// Letzter Zugriff
			$fileInfo['lastAccessDate'] = date($dateFormat, $fileInfo['atime']);
			$fileInfo['lastAccessTime'] = date($timeFormat, $fileInfo['atime']);
			
		}

		return $fileInfo;
	}
	
	/**
	 * public function formatFileSize()
	 * Formatiert eine Dateigröße in Bytes in eine darstellbare Zahl.
	 * 
	 * @param number $fileSie Dateigröße in Bytes
	 * @param string $baseUnit Grundeinheit für die zu formatierende Zahl. Möglich sind 'KB', 'MB' und 'GB', Default ist 'KB'.
	 * @param number $round Nachkommastellen bei Rundung der Zahl. Default ist 2. 
	 */
	public function formatFileSize($fileSize, $baseUnit='KB', $round=2) {

		$sizeQuotients = array (
			0 => 1, // Bytes
			1 => 1024, // Kilobytes
			2 => pow(1024, 2), // Megabytes
			3 => pow(1024, 3) // Gigabytes
		);
		
		$sizeNames = array(
			0 => 'Bytes',
			1 => 'KB',
			2 => 'MB',
			3 => 'GB',
		);
		
		$startUnit = array_search($baseUnit, $sizeNames);
		$startUnit = intval($startUnit);
		
		$sizeQuotientsLength = count($sizeQuotients);

		if ($startUnit > $sizeQuotientsLength) {
			$c = 0;
		} else {
			$c = $startUnit;
		}
		
		while ($fileSize > 1024 && $c < $sizeQuotientsLength) {
			$fileSize = $fileSize / $sizeQuotients[$c];
			$c++;
		}
		
		// Falls Schleife mind. 1Mal durchlaufen wurde, muss der Zähler wieder zurückgesetzt werden
		if ($c >= 1) {
			--$c;
		}
		
		return array(
			'fileSize' => round($fileSize, 2),
			'fileSizeUnit' => $sizeNames[$c]
		);
	}
	
	/**
	 * public function formatDirectory()
	 * Hilfsfunktion: Formatiert einen Verzeichnispfad richtig 
	 * 
	 * @param $directory string Pfadname
	 * @return string Es wird ein korrekter Pfadname (z.B. 'meinordner/meinunterordner/') zur�ckgegeben
	 */
	public function formatDirectory ($directory) {
		//$directory = preg_replace ('/^\.?\//', '', $directory);
		$directory = preg_replace ('/^\.\//', '', $directory);
		$directory .= '/';
		$directory = preg_replace ('/\/{2,}/', '/', $directory);

		return $directory;
	}

	/**
	 * OUTDATED: public function makeNameWebSave()
	 * Tauscht alle ungültigen/kritischen Zeichen in einem Datei-/Verzeichnisnamen/-pfad aus 
	 * 
	 * @param $name string Datei-/Verzeichnis-/Pfadname
	 * @return string Es wird ein korrekter Name zurückgegeben
	 */
	public function makeNameWebSave($name) {
		$search = array(' ', 'ß', 'ä', 'ü', 'ö', 'Ä', 'Ö', 'Ü', '&', '/', "'", ',', '+', '?', '!', '"', 'á', 'à', 'é', 'è', 'ç', 'ô','Á', 'À', 'É', 'È', 'Ç', ':', '%');
		$replace = array('-', 'ss', 'ae', 'ue', 'oe', 'Ae', 'Oe', 'Ue', '-', '-', '', '-', '-', '', '', '', 'a', 'a', 'e', 'e', 'c', 'o', 'A', 'A', 'E', 'E', 'C', '', '');
		return str_replace ($search, $replace, $name);
	}
	
	/**
	 * public function cleanPath()
	 * Säuber einen Dateipfad von Mehrfachen "/" hintereinander
	 *
	 * @param string $path Dateipfad
	 * @return string gesäuberter Dateipfad
	 */
	public function cleanPath($path) {
		return preg_replace('/\/{2,}/', '/', $path);
	}
	
	/**
	 * public function maxUploadFilesize ()
	 * Gibt die max. mögliche Größe von Dateiuploads aus
	 * 
	 * @param void Erwartet keine Parameter
	 * @return string Formatierte Größenangabe
	*/
	public function maxUploadFilesize () {
		$this->maxUploadSize = ini_get('upload_max_filesize');
		$sizeWrapper = array('K' => ' Kilobyte', 'M' => ' Megabyte', 'G' => ' Gigabyte');
	 	
		preg_match('/([A-Za-z]*)$/', $this->maxUploadSize, $match);
		if ($sizeWrapper[$match[1]]) {
			return str_replace($match[1], $sizeWrapper[$match[1]], $this->maxUploadSize);
		} else {
			return $this->maxUploadSize;
		}
	}

	
	/**
	 * public function arrayToLower ()
	 * Wandelt alle Elemente eines Arrays in Kleinbuchstaben um
	 * 
	 * @param $inputArray array Das Array, welches umgewandelt werden soll
	 * @return array Array mit kleingeschriebenen Elementen
	*/
	public function arrayToLower ($inputArray) {
		if (!is_array($inputArray)) return;
		
		foreach ($inputArray as $key => $value) {
			$inputArray[$key] = strtolower(trim($value));
		}
		return $inputArray;
	}

	/**
	 * private function resetMessages()
	 * löscht alle Meldungen
	 * 
	 * @param void
	 * @return void
	*/	
	private function resetMessages() {
		$this->lastSuccessMessage = array();
		$this->lastErrorMessage = array();
		$this->lastErrors = 0;
		$this->lastSuccess = 0;
		return;	
	}

	/**
	 * private function resetVars()
	 * Hilfsfunktion: löscht alle Variablen
	 * 
	 * @param void
	 * @return void
	 */	
	private function resetVars() {
		$this->dirStartDirectory = '';
		$this->showOnlyFileTypes = '';
		$this->dontShowFileTypes = '';
		return;	
	}
	
	/**
	 * public function getLastSuccessMessages()
	 * gibt nur Erfolgsmeldungen zurück
	 * 
	 * @param void
	 * @return void
	*/	
	public function getLastSuccessMessages() {
		return $this->lastSuccessMessage;
	}

	/**
	 * public function getLastErrorMessages()
	 * gibt nur Fehlermeldungen zurück
	 * 
	 * @param void
	 * @return void
	*/	
	public function getLastErrorMessages() {
		return $this->lastErrorMessage;
	}
	
	/**
	 * public function __destruct()
	 * Destruktor
	 * 
	 */
	public function __destruct() {
		
	}
}
?>
