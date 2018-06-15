<?php

/**
 * Class AppFileBrowserController
 * Application to display and edit files and directories.
 * 
 * @extends Class ApplicationController
 * @author A. Alkaissi, J.Hahn <info@content-o-mat.de>
 * @version 2015-04-27
 */
namespace Contentomat\Filebrowser;
use \Contentomat\Form;
use \Contentomat\FileHandler;
use \Contentomat\Image;
use \Contentomat\ApplicationController;
use \Contentomat\Debug;
use \Exception;

class AppFileBrowserController extends ApplicationController {

	protected $form;
	protected $fileHandler;
	protected $directory;
	protected $currentDirectory;
	protected $rootDirectory;
	protected $iconSize;
	protected $fileIconSize;
	protected $maxExecutionTime;
	protected $formChecked;
	protected $sortBy;
	protected $sortDir;
	protected $fileTypeCategories;
	protected $gui;
	protected $image;

	/**
	 * 
	 */
	public function init() {
		$this->load(PATHTOADMIN . 'classes/class_session.php');

		$this->templatesPath = $this->templatesPath . 'app_filebrowser/';

		$this->form = new Form();
		$this->fileHandler = new fileHandler();
		$this->image = new Image();

		$this->parser->setMultipleParserVars($this->cmtSettings);
		
		$this->initVariables();
		$this->initActions();
	}

	/**
	 * protected function initVariables()
	 * 
	 * 
	 * 
	 */
	protected function initVariables() {
		
		// clean up directories path to avoid missuse (e.g. '../../../show/root/path/')
		$this->directory = $this->cleanUpPath($this->requestvars['cmtDirectory']);

		// 
		$this->rootDirectory = $this->fileHandler->formatDirectory(PATHTOWEBROOT . '/' . $this->cmtSettings['root']);
	
		// OUTDATED?
		//define('ROOT_DIRECTORY', $this->rootDirectory);

		// Aktuelles Verzeichnis
		$this->currentDirectory = $this->preparePath($this->directory);

// 		$this->fileTypeCategories = array(
// 			'image' => array('jpg', 'jpeg', 'gif', 'png'),
// 			'text' => array('txt', 'html', 'htm', 'php', 'inc', 'tpl', 'css', 'js'),
// 			'pdf' => array('pdf'),
// 			'video' => array('avi', 'mov', 'mp4'),
// 			'sound' => array('mp3')
// 		);
		
		$this->imagePreviewExtensions = array(
			'jpg', 
			'jpeg', 
			'gif',
			'png'
		);
		
		$this->fileTypeCategoryWrapper = array(

			// text
			'inc' => 'text',
			'php' => 'text',
			'phtml' => 'text',
			'htm' => 'text',
			'html' => 'text',
			'tpl' => 'text',
			'js' => 'text',
			'pl' => 'text',
			'py' => 'text',
			'sql' => 'text',
			'xml' => 'text',
			'txt' => 'text',
			'csv' => 'text',
			'css' => 'text',
			
			// image
			'jpg' => 'image',
			'jpeg' => 'image',
			'gif' => 'image',
			'png' => 'image'
		);

		$this->formChecked = $this->requestvars['form_checked'];

		$this->sortBy = $this->requestvars['sortBy'];

		$this->sortDir = $this->requestvars['sortDir'];


		// Funktions-Icons
		$this->iconSize = $this->cmtSettings['icon_size'];
		if (!$this->iconSize) {
			$this->iconSize = '24';
		}

		// Icongröße ermitteln
		$this->fileIconSize = $this->cmtSettings['file_icon_size'];

		if (!$this->fileIconSize) {
			$this->fileIconSize = '24';
		}

		// maximale Skriptausführungszeit des Servers
		$this->maxExecutionTime = $this->cmtSettings['max_execution_time'];
		if (!$this->maxExecutionTime) {
			$this->maxExecutionTime = 30;
		}
		

	}

	/**
	 * protected function actionDefault()
	 * Default action: shows the filebrowser
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDefault() {

		// Application title
		$this->parser->setParserVar('appTitle', $this->cmtSettings['show_name']);


		// Upload-Felder
		if ($this->cmtSettings['uploads']) {
			$filesUploadContent = '';

			for ($i = 0; $i < $this->cmtSettings['uploads']; $i++) {
				$this->parser->setParserVar('fieldName', intval($i) + 1);
				$filesUploadContent .= $this->parser->parseTemplate(CMT_TEMPLATE . 'app_filebrowser/cmt_filebrowser_upload_file.tpl');
			}

			$this->parser->setParserVar('uploadDirectory', $this->fileHandler->formatDirectory($this->directory . '/'));
			$this->parser->setParserVar('filesUploadContent', $filesUploadContent);
			$uploadFields = $this->parser->parseTemplate(CMT_TEMPLATE . 'app_filebrowser/cmt_filebrowser_upload_frame.tpl');

			$this->parser->setParserVar('uploadFields', $uploadFields);
			$this->parser->setParserVar('maxUploadFileSize', $this->fileHandler->maxUploadFilesize());
		}


		// create breadcrum navigation
		$this->parser->setParserVar('breadcrumbNavigation', $this->createBreadcrumbs());

		// Dateien ermitteln, für die es eine Vorschau (Klick auf das Icon) geben soll
		$previewFileTypes = explode(',', $this->cmtSettings['preview_file_types']);
		if (is_array($previewFileTypes)) {
			foreach ($previewFileTypes as $key => $value) {
				$previewFileTypes[$key] = strtolower(trim($value));
			}
		}


		// get current directory content
		//$directoryData = $this->getFileStructure($this->directory);

		/*
		 * Create content for left column/ directories overview
		 */

		$dirTree = $this->getDirectories($this->rootDirectory);
		$dirContent = $this->getDirectoryContent();

		// Aktuelles Verzeichnis an Parser übergeben
		$this->parser->setParserVar('currentDirectory', $this->fileHandler->formatDirectory($this->directory . '/'));
		$this->parser->setParserVar('currentDirectoryWriteable', is_writeable($this->currentDirectory));

		

		$this->parser->setParserVar('errorMessage', count($this->getErrorMessages()));

		$this->parser->setParserVar('successMessage', count($this->getSuccessMessages()));

		$this->parser->setMultipleParserVars($this->getErrorMessages());

		$this->parser->setMultipleParserVars($this->getSuccessMessages());

		$dirContent .= $this->form->FormHidden('directory', $this->directory);
		$this->parser->setParserVar('dirTree', $dirTree);
		$this->parser->setParserVar('dirContent', $dirContent);

		// Hover-Effekt?
		$this->parser->setParserVar('hoverRow', $this->cmtSettings['hover_row']);

		$this->parser->setParserVar('directory', $this->directory);

		$this->content = $this->parser->parseTemplate(CMT_TEMPLATE . 'app_filebrowser/cmt_filebrowser.tpl');
	}

	/**
	 * protected function createBreadcrumbs()
	 * Creates the breadcrum navigation. The breadcrumb template will be parsed for every single element.
	 * 
	 * @param void
	 * 
	 * @return string 
	 */
	protected function createBreadcrumbs() {
		$dirNames = explode("/", trim(preg_replace('/\/$/', '', $this->directory)));
		$dirLinks = $dirNames;

		if (!$dirNames[0]) {
			$dirNames[0] = $this->cmtSettings['root_name'];
			$dirLinks[0] = '.';
		} else {
			array_unshift($dirLinks, '.');
			array_unshift($dirNames, $this->cmtSettings['root_name']);
		}


		$folderPath = '';
		
		// create breadcrumb-navi
		$breadcrumbRow = $this->parser->getTemplate($this->templatesPath . 'cmt_filebrowser_breadcrumb.tpl');
		$breadcrumbs = '';
		$breadcrumbsTotal = count($dirLinks);
		
		foreach ($dirLinks as $key => $dirLink) {
			if (!$dirLink)
				continue;
			$folderPath .= $dirLink . '/';
			if ($key + 1 == $breadcrumbsTotal) {
				$this->parser->setParserVar('isLastBreadcrumb', true);
			}
			$this->parser->setParserVar('directoryName', $dirNames[$key]);
			$this->parser->setParserVar('directoryPath', $folderPath);
			$breadcrumbs .= trim($this->parser->parse($breadcrumbRow));
		}
		return $breadcrumbs;
	}

	protected function actionShowDirectories($nodeID='') {
		$this->isAjax = true;
		$this->content = $this->getDirectories($nodeID);
	}
	
	protected function actionShowDirectoryContent() {
		$this->isAjax = true;
		$this->content = $this->getDirectoryContent();
	}
	
	protected function getDirectoryContent($directory='') {
		
		if (!$directory) {
			$directory = $this->currentDirectory;
		}
		
		// get current directory content
		$directoryData = $this->getFileStructure($this->directory);
			
		// Aktuelles Verzeichnis an Parser übergeben
		$this->parser->setParserVar('currentDirectory', $this->fileHandler->formatDirectory($this->directory . '/'));
		$this->parser->setParserVar('currentDirectoryWriteable', is_writeable($this->currentDirectory));
		
		/*
		 * Create content for right column/ current directory content
		*/
		$rowFlag = 1;
		$entryNr = 0;
		$directoryContent = '';
		
		// Parse directories first
		if (!empty($directoryData['directories'])) {
		
			$directoryTemplate = file_get_contents(CMT_TEMPLATE . 'app_filebrowser/cmt_overview_directory.tpl');
		
			foreach ($directoryData['directories'] as $path => $value) {
				
				$this->parser->setParserVar('dirName', $directoryData['directoryName'][$path]);
				$this->parser->setParserVar('dirURL', $directoryData['directoryName'][$path]);
				$this->parser->setParserVar('dirDate', $directoryData['directoryDate'][$path]);
				$this->parser->setParserVar('dirTimestamp', $directoryData['directoryTime'][$path]);
				$this->parser->setParserVar('dirSize', $directoryData['directorySize'][$path]);
				$this->parser->setParserVar('dirWriteable', $directoryData['directoryWriteable'][$path]);
				$this->parser->setParserVar('dirReadable', $directoryData['directoryReadable'][$path]);

				$this->parser->setParserVar('dirRights', $this->getFilePermissions($path));
		
				$this->parser->setParserVar('rowFlag', $rowFlag++ % 2);
		
				$this->parser->setParserVar('entryNr', $entryNr++);
		
				// Zeile parsern
				$directoryContent .= $this->parser->parse($directoryTemplate); // right
			}
		}

		// then parse files
		if (!empty($directoryData['files'])) {
		
			$fileTemplate = file_get_contents(CMT_TEMPLATE . 'app_filebrowser/cmt_overview_file.tpl');
		
			foreach ($directoryData['files'] as $path => $name) {
		
				// weitere Variablen
				//$this->parser->setParserVar('fileIcon', $this->getFileTypeIcon(strtolower($directoryData['fileType'][$path])));
				$this->parser->setParserVar('fileName', $directoryData['fileName'][$path]);
				$this->parser->setParserVar('imageWidth', $directoryData['fileImageWidth'][$path]);
				$this->parser->setParserVar('imageHeight', $directoryData['fileImageHeight'][$path]);
				$this->parser->setParserVar('fileReadable', $directoryData['fileReadable'][$path]);
				$this->parser->setParserVar('fileWriteable', $directoryData['fileWriteable'][$path]);
				$this->parser->setParserVar('fileType', $directoryData['fileType'][$path]);
				$this->parser->setParserVar('fileTypeCategory', $directoryData['fileTypeCategory'][$path]);
				$this->parser->setParserVar('filePath', $this->fileHandler->formatDirectory($this->rootDirectory . '/' . $this->directory . '/') . $directoryData['fileName'][$path]);
				$this->parser->setParserVar('fileURL', $directoryData['fileURL'][$path]);
				$this->parser->setParserVar('fileDate', $directoryData['fileDate'][$path]);
				$this->parser->setParserVar('fileTime', $directoryData['fileTime'][$path]);
				$this->parser->setParserVar('fileSize', $directoryData['fileSize'][$path]);
				$this->parser->setParserVar('fileSizeRaw', $directoryData['fileSizeRaw'][$path]);
				$this->parser->setParserVar('fileRights', $this->getFilePermissions($path));
				$this->parser->setParserVar('rowFlag', $rowFlag++ % 2);
				$this->parser->setParserVar('entryNr', $entryNr++);
		
				// Zeile parsern
				$directoryContent .= $this->parser->parse($fileTemplate);
			}
		}
		
		return $directoryContent;
		
	}
	
	/**
	 * protected function actionLoadNode()
	 * Shows the content of one node.
	 *
	 * @params string $directory Optional: Path of the directory to show, 
	 * 
	 * @return string Parsed node content
	 */
	protected function getDirectories($directory='') {

		if (!$directory) {
			$directory = $this->currentDirectory;
		}
//var_dump('getDirectories: ' . $directory);
		$treeData = $this->getFileStructure($directory);

		$rowFlag = 1;
		$entryNr = 0;
		$dirTree = '';
		$parent = trim($this->getvars['parent']);
		
		if (!$parent) {
			$parent = 'root';
		}

		if (!empty($treeData['directories'])) {

			//$dirTemplate = file_get_contents(CMT_TEMPLATE . 'app_filebrowser/cmt_overview_directory.tpl');
			$dirRowTemplate = file_get_contents(CMT_TEMPLATE . 'app_filebrowser/cmt_filebrowser_directory_list_row.tpl');

			foreach ($treeData['directories'] as $path => $value) {

				$this->parser->setParserVar('dirName', $treeData['directoryName'][$path]);
				//$this->parser->setParserVar('dirType', 'Ordner');
				$this->parser->setParserVar('dirURL', $treeData['directoryName'][$path]);
				$this->parser->setParserVar('dirDate', $treeData['directoryDate'][$path]);
				$this->parser->setParserVar('dirTimestamp', $treeData['directoryTime'][$path]);
				$this->parser->setParserVar('dirSize', $treeData['directorySize'][$path]);
				$this->parser->setParserVar('hasChildren', $treeData['directorySubDirs'][$path]);
				$this->parser->setParserVar('directoryPath', htmlentities(preg_replace('/^' . preg_quote($this->rootDirectory, '/') . '/', '', $path)));
				$this->parser->setParserVar('directoryParent', $parent);
				$this->parser->setParserVar('dirWriteable', $treeData['directoryWriteable'][$path]);
				$this->parser->setParserVar('dirReadable', $treeData['directoryReadable'][$path]);
				$nodeID = $parent . '-' . $entryNr;
				$this->parser->setParserVar('nodeID', $nodeID);

				if ((string) $this->getvars['dirID'] == (string) $nodeID) {
					$this->parser->setParserVar('dirSelected', true);
				} else {
					$this->parser->setParserVar('dirSelected', false);
				}
				
				$this->parser->setParserVar('rowFlag', $rowFlag++ % 2);
				$this->parser->setParserVar('entryNr', $entryNr++);

				// Zeile parsern
				$dirTree .= $this->parser->parse($dirRowTemplate); // left
			}
		}
		
		return $dirTree;
	}
	
	/**
	 * protected function preparePath()
	 * Prepares a directory path for usage in own methods. Prefixes the root directory path.
	 *
	 * @param string $path The path of a directory or a file
	 * 
	 * @return string Cleaned and prefixed path to use in own methods.
	 */
	protected function preparePath($path) {
		$path = $this->rootDirectory . $this->cleanUpPath($path);
		$parts = pathinfo($path);
		
		if (!isset($parts['basename'])) {
			$path .= '/';
		}
		return preg_replace('/\/{2,}/', '/', $path);
	}
	
	protected function prepareFileName($fileName) {
		$nameParts = pathinfo($fileName);
		return $this->cmt->makeNameWebsave($nameParts['filename']) . '.' . $nameParts['extension'];
	}
	
	/**
	 * protected function cleanUpPath()
	 * Cleans up a directory path to avoid missusage.
	 *
	 * @param string $directory The path of a directory
	 *
	 * @return string Cleaned directory path.
	 */
	protected function cleanUpPath($directory) {
		$directory = preg_replace(array(
				'/^' . preg_quote($this->rootDirectory, '/') .'/',	// replace leading root path
				'/(\.\.?\/)/'										// replace leading './' and '../'
			),		
			'', 
			$directory
		);
		return preg_replace('/\/{1,}/', '/', $directory);
	}
	
	/**
	 * protected function getFileStructure()
	 * Cleans up a directory path to avoid missusage.
	 *
	 * @param string $directory The path of a directory
	 * @param boolean $dirOnly Indicates wether to return only directory entries (true) or directorys and files (false, default) 
	 *
	 * @return array Mutlidimensional array with all informations about the directories content
	 */	
	protected function getFileStructure($directory, $dirOnly = false) {

		$dirData = array();
		$fileData = array();
	
		// Filestruktur auslesen
		$directory = $this->preparePath($directory);

		$dirStruct = $this->fileHandler->showDirectory(array(
			'directory' => $directory,
			'showSubDirectories' => false,
			'maxExecutionTime' => 10));

		// Trennen nach Ordner und Dateien
		$files = array();
		$directories = array();
		if (!empty($dirStruct)) {
			
			
			foreach ($dirStruct as $path => $name) {
				if (is_dir($path)) {
					$directories[$path] = $name;
				} elseif (!$dirOnly) {
					$files[$path] = $name;
				}
			}
		}


		$parent = $directory;

		if (!$parent) {
			$parent = "root";
		}

		/*
		 * Arrays für Sortierreihenfolgen erstellen
		 */

		// 1. Verzeichnisse
		
		foreach ($directories as $path => $name) {
			$path = str_replace($this->rootDirectory . $this->rootDirectory, $this->rootDirectory, $path);

			// Name
			$dirData['id'][$path] = $this->parseDirectoryID($path);

			$dirData['name'][$path] = $name;

			$dirData['parent'][$path] = $this->parseDirectoryID($parent);

			// Erstellungsdatum
			$fileTempTime = $this->getFiletime($path);
			$dirData['time'][$path] = $fileTempTime['timestamp'];
			$dirData['date'][$path] = $fileTempTime['date'];

			// Größe/Inhalt
			$dirTemp = $this->fileHandler->showDirectory(array('directory' => $path, 'showSubDirectories' => false));
			if (empty($dirTemp)) {
				$dirData['size'][$path] = 0;
			} else {
				$dirData['size'][$path] = count($dirTemp);
			}

			$subDirs = $this->getFileStructure($path, true);
			$dirData['subDirectories'][$path] = count($subDirs['directories']);
			
			// check if file readable
			$dirData['readable'][$path] = is_readable($path);
			
			// check if file is writeable, return parameter to allow edit
			$dirData['writeable'][$path] = is_writeable($path);
		}




		// 2. Dateien
		foreach ($files as $path => $name) {

			// Name
			$fileData['name'][$path] = $name;

			// Erstellungsdatum
			$fileTempTime = $this->getFiletime($path);
			$fileData['time'][$path] = $fileTempTime['timestamp'];
			$fileData['date'][$path] = $fileTempTime['date'];

			// Größe
			$fileTempSize = $this->getFilesize($path);
			$fileData['size'][$path] = $fileTempSize['formatted'];
			$fileData['sizeRaw'][$path] = $fileTempSize['raw'];

			// Typ
			$fileTempInfo = pathinfo($name);
			$fileData['type'][$path] = $fileTempInfo['extension'];
			
			$fileData['fileURL'][$path] = $path;

			$fileTempTypeCategory = '';

			$fileData['typeCategory'][$path] = $this->fileTypeCategoryWrapper[$fileTempInfo['extension']];

			if ($fileData['typeCategory'][$path] == 'image') {
				$imageInfo = $this->image->getImageSize($path);
				
				$fileData['imageWidth'][$path] = $imageInfo[0];
				$fileData['imageHeight'][$path] = $imageInfo[1];
				//$fileData['typeCategory'][$path] = 'image';
			}
			
			// check if file readable
			$fileData['readable'][$path] = is_readable($path);

			// check if file is writeable, return parameter to allow edit
			$fileData['writeable'][$path] = is_writeable($path);
		}


		// Sortierkriterium bestimmen
		switch ($this->sortBy) {
			case 'fileDate':
				$directories = $dirData['time'];
				$files = $fileData['time'];
				break;

			case 'fileSize':
				$directories = $dirData['size'];
				$files = $fileData['sizeRaw'];
				break;

			case 'fileType':
				$directories = $dirData['name'];
				$files = $fileData['type'];
				break;

			case 'fileName':
			default:
				$directories = $dirData['name'];
				$files = $fileData['name'];
				break;
		}


		if (!is_array($directories))
			$directories = array();
		if (!is_array($files))
			$files = array();

		// Sortieren
		uasort($directories, 'strnatcmp');
		uasort($files, 'strnatcmp');

		// Sortierrichtung
		if ($this->sortDir == 'desc') {
			$directories = array_reverse($directories, true); // sortiertes Array umdrehen bei Bewahrung der Schl�ssel
			$files = array_reverse($files, true);
		}


		$directoryParams = array(
			'files' => $files,
			'directories' => $directories,
			'directoryName' => $dirData['name'],
			'directoryTime' => $dirData['time'],
			'directoryDate' => $dirData['date'],
			'directorySize' => $dirData['size'],
			'directorySubDirs' => $dirData['subDirectories'],
			'directoryParent' => $dirData['parent'],
			'directoryID' => $dirData['id'],
			'directoryReadable' => $dirData['readable'],
			'directoryWriteable' => $dirData['writeable'],
			'fileName' => $fileData['name'],
			'fileTime' => $fileData['time'],
			'fileDate' => $fileData['date'],
			'fileSize' => $fileData['size'],
			'fileSizeRaw' => $fileData['sizeRaw'],
			'fileType' => $fileData['type'],
			'fileURL' => $fileData['fileURL'],
			'fileTypeCategory' => $fileData['typeCategory'],
			'fileImageWidth' => $fileData['imageWidth'],
			'fileImageHeight' => $fileData['imageHeight'],
			'fileReadable' => $fileData['readable'],
			'fileWriteable' => $fileData['writeable']
		);

		return $directoryParams;
	}

	/**
	 * protected function parseDirectoryID()
	 * 
	 * return create directory ID from path $path parts
	 *  
	 * @param string $path
	 * @return string
	 */
	protected function parseDirectoryID($path) {
		$path = str_replace($this->rootDirectory, 'root-', $path);
		$path = str_replace("./", '-', $path);
		$path = str_replace("/", '-', $path);
		$path = explode("-", $path);
		$path = join("-", array_filter($path));
		return $path;
	}

	/**
	 * protected function getFilesize()
	 * Returns the size of a file in raw and readable format. 
	 * 
	 * @param string $file Filepath
	 * @return array Returns array with two entries: 'raw' => file size in bytes, 'formatted' => file size in Bytes, KB or MB 
	 */
	protected function getFilesize($file) {
		$bytes = @filesize($file);
		if ($bytes < 1024) {
			$formatted = $bytes . " Bytes";
		} else if ($bytes < 1024 * 1024) {
			$formatted = round($bytes / 1024, 0) . " KB";
		} else {
			$formatted = round($bytes / (1024 * 1024), 2) . " MB";
		}
		return array('raw' => $bytes, 'formatted' => $formatted);
	}

	/**
	 * protected function getFiletime()
	 * 
	 * @param string $file
	 * @return array 
	 */
	protected function getFiletime($file) {
		$timestamp = @filemtime($file);
		$day_names = array("Mon" => "Montag", "Tue" => "Dienstag", "Wed" => "Mittwoch", "Thu" => "Donnerstag", "Fri" => "Freitag", "Sat" => "Samstag", "Sun" => "Sonntag");
		$date = date("d.m.Y  H:i", $timestamp);
		$day = substr($day_names[date("D", $timestamp)], 0, 2);

		return array('date' => $day . ", " . $date, 'timestamp' => $timestamp);
	}

	/**
	 * protected function getFilePermissions()
	 * 
	 * @param string $file
	 * @return string 
	 */
	protected function getFilePermissions($file) {
		return substr(sprintf('%o', @fileperms($file)), -4);
	}

	// ACTIONS //

	/**
	 * public function actionDelete()
	 * 
	 * Action: delete files or/and directories
	 * 
	 */
	protected function actionDelete() {

		if ($this->formChecked && !is_array($this->formChecked)) {
			$selectedFilesAndDirs = explode(",", $this->formChecked);
		} else {
			$selectedFilesAndDirs = $this->formChecked;
		}

		if (empty($selectedFilesAndDirs) || !is_array($selectedFilesAndDirs)) {
			$this->setErrorMessage('deleteErrorNoSelection', true);
			$this->changeAction('default');
			return;
		}

		$dirsToDelete = array();
		$filesToDelete = array();

		// seperate files and directories to delete
		foreach ($selectedFilesAndDirs as $filePath) {

			// protect path names against manipulation
			//$filePath = preg_replace('/^(..\/){1,}/', '', $filePath);
			$filePath = $this->cleanUpPath($filePath);

			$filePath = $this->fileHandler->formatDirectory($this->currentDirectory) . $filePath;
			if (is_dir($filePath)) {
				$dirsToDelete[] = $filePath;
			} else {
				$filesToDelete[] = $filePath;
			}
		}

		// Delete: directories
		if (!empty($dirsToDelete)) {

			$dirsNumber = count($dirsToDelete);
			
			$dirNames = array();
			foreach ($dirsToDelete as $dir) {
				$dir = explode("/", $dir);
				$dirNames[] = array_pop($dir);
			}
			$this->parser->setParserVar('deleteDirsNumber', $dirsNumber);
			$this->parser->setParserVar('deleteDirNames', implode(', ', $dirNames));

			$check = $this->fileHandler->deleteDirectory(array('directory' => $dirsToDelete));
			if (!$check) {
				$this->setErrorMessage('deleteErrorDir', true);
			} else {
				$this->setSuccessMessage('deleteSuccessDir', true);
			}
		}

		// Delete: files
		if (!empty($filesToDelete)) {
			
			$filesNumber = count($filesToDelete);
			$fileNames = array();
			foreach ($filesToDelete as $file) {
				$file = explode("/", $file);
				$fileNames[] = array_pop($file);
			}
			$this->parser->setParserVar('deleteFilesNumber', $filesNumber);
			$this->parser->setParserVar('deleteFileNames', implode(', ', $fileNames));

			$check = $this->fileHandler->deleteFile(array('file' => $filesToDelete));
			if (!$check) {
				$this->setErrorMessage('deleteErrorFile', true);
			} else {
				$this->setSuccessMessage('deleteSuccessFile', true);
			}
		}

		$this->changeAction('default');
	}

	/**
	 * public function actionRename()
	 * Action: Renames a file or directory
	 *
	 * @param void
	 * @return void
	 */
	protected function actionRename() {

		$oldFileName = $this->requestvars['oldFileName'];
		$newFileName = $this->requestvars['newFileName'];


		$this->isJSON = true;

		if ($oldFileName && $newFileName) {

			$fileInfo = pathinfo($newFileName);

			$check = $this->fileHandler->renameFile(array(
				'sourceFile' => $this->currentDirectory . $oldFileName,
				'newFilename' => $newFileName
					));
		}

		$this->content['oldFileName'] = $oldFileName;
		$this->content['newFileName'] = $newFileName;
		$this->content['success'] = $check;
	}

	/**
	 * public function actionMove()
	 * 
	 *  Action: move file or directory to other location
	 */
	protected function actionMove() {
	
		$sourcePath = $this->requestvars['cmtSourcePath'];
		$targetPath = $this->requestvars['cmtTargetPath'];
		
		$this->content['sourcePath'] = basename($sourcePath);
		$this->content['targetPath'] = $targetPath;
		$this->content['success'] = false;
		$this->isJSON = true;
		
		if (!$sourcePath || !$targetPath) {
			return;
		}
		
		$sourcePath = $this->preparePath($sourcePath);
		$targetPath = $this->preparePath($targetPath);

		$check = $this->fileHandler->moveFile(array(
			'sourceFile' => stripslashes($sourcePath),
			'targetDirectory' => stripslashes($targetPath)
		));
	

		$this->content['success'] = $check;

	}

	protected function actionDuplicateFile() {
		$sourceFile = trim($this->requestvars['cmtSourceFile']);
		$targetFile = trim($this->requestvars['cmtTargetFile']);

		if (!$sourceFile || !$targetFile) {
			$this->setErrorMessage('duplicateFileNameMissingError', true);
			$this->changeAction('default');
			return;
		}
		if ($sourceFile == $targetFile) {
			$this->setErrorMessage('duplicateFileSameNameError', true);
			$this->changeAction('default');
			return;
		}


		// prevent path manipulation by extracting the filename
		$targetFileHlp = explode('/', $targetFile);
		$targetFile = array_pop($targetFileHlp);
		
		// clean up filename
		$targetFile = $this->prepareFileName($targetFile);

		// check if file exists
		if (file_exists($this->currentDirectory . $targetFile)) {
			$this->setErrorMessage('duplicateFileExistsError', true);
			$this->changeAction('default');
			return;
		}
		
		$check = $this->fileHandler->copyFile(array(
			'sourceFile' => $sourceFile,
			'targetFile' => $targetFile,
			'directory' => $this->currentDirectory
		));

		if (!$check) {
			$this->setErrorMessage('duplicateFileError', true);
		} else {
			$this->setSuccessMessage('duplicateFileSuccess', true);
		}
		$this->changeAction('default');
	}

	protected function actionDuplicateDirectory() {
		$sourceDirectory = trim($this->requestvars['cmtSourceDirectory']);
		$targetDirectory = trim($this->requestvars['cmtTargetDirectory']);
		
		if (!$sourceDirectory || !$targetDirectory) {
			$this->setErrorMessage('duplicateDirectoryNameMissingError', true);
			$this->changeAction('default');
			return;
		}
		if ($sourceDirectory == $targetDirectory) {
			$this->setErrorMessage('duplicateDirectorySameNameError', true);
			$this->changeAction('default');
			return;
		}
		
		// prevent path manipulation by extracting the filename
		$targetDirectoryHlp = explode('/', $targetDirectory);
		$targetDirectory = array_pop($targetDirectoryHlp);
		
		$targetDirectory = $this->cmt->makeNameWebsave($targetDirectory);
		
		// check if file exists
		if (is_dir($this->currentDirectory . $targetDirectory)) {
			$this->setErrorMessage('duplicateDirectoryExistsError', true);
			$this->changeAction('default');
			return;
		}
		
		$check = $this->fileHandler->copyDirectory(array(
				'sourceDirectory' => $sourceDirectory,
				'targetDirectory' => $targetDirectory,
				'directory' => $this->currentDirectory
		));
		
		if (!$check) {
			$this->setErrorMessage('duplicateDirectoryError', true);
		} else {
			$this->setSuccessMessage('duplicateDirectorySuccess', true);
		}
		$this->changeAction('default');		
	}
	
	/**
	 * public function actionDownload()
	 * 
	 * Action: download a file
	 *  
	 */
	protected function actionDownload() {
		$this->fileHandler->handleDownload(array('downloadFile' => $this->currentDirectory . $this->formChecked[0]));
		exit();
	}

	/**
	 * public function actionMakedir()
	 * 
	 * Action: create new directory in current directory
	 *  
	 */
	protected function actionMakedir() {
		if (trim($this->postvars['cmtNewDirectoryName'])) {

			// Neuen Verzeichnisnamen vor event. Manipulation schützen
			$newDirName = preg_replace('/^(..\/){1,}/', '', trim($this->postvars['cmtNewDirectoryName']));


			// Verzeichnisnamen von kritischen Zeichen säubern
			$newDirName = $this->cmt->makeNameWebSave($newDirName);

			// Verzeichnis erstellen
			$newDirectory = $this->fileHandler->formatDirectory($this->rootDirectory . $this->directory . '/') . $newDirName;
			if (is_dir($newDirectory)) {
				$this->setErrorMessage('mkdirErrorDirExists', TRUE);
			} else {

				$check = $this->fileHandler->createDirectory(array('newDirectoryname' => $newDirectory));

				// Meldungen erzeugen
				if (!$check) {
					$this->setErrorMessage('mkdirError', TRUE);
				} else {
					$this->setSuccessMessage('mkdirSuccess', TRUE);
				}
			}
		}
		$this->parser->setParserVar('newDirName', $newDirName);

		$this->changeAction('default');
	}

	// /**
	//  * public function actionUpload()
	//  * Upload file/s to current directory
	//  * 
	//  * @param void
	//  * @return void
	//  * 
	//  */
	// protected function actionUpload() {
	// 	
	// 	$this->parser->setParserVar('uploadFiles', count($_FILES));
	// 	
	// 	$check = $this->fileHandler->handleUpload(array(
	// 		'uploadFile' => $_FILES, 
	// 		'targetDirectory' => $this->currentDirectory
	// 	));
    //
	// 	if (!$check) {
	// 		$this->setErrorMessage('uploadError', true);
	// 	} else {
	// 		$this->setSuccessMessage('uploadSuccess', true);		
	// 	}
	// 	
	// 	$this->changeAction('default');
	// }


	/**
	 * actionUpload
	 *
	 * Handles upload from multi file input as well as from dropping files
	 * into the filebrowser's file pane
	 *
	 * @return void
	 * @access public
	 */
	public function actionUpload () {
		try {
			// Check if request was AJAX or not
			if (!empty ($_POST['is-ajax'])) {
				$this->isAjax = true;
				$this->isJson = true;
			}

			if (!$this->isAjax) {
				$this->currentDirectory = $_REQUEST['cmtDirectory'];
			}

			if (empty ($_FILES['cmtUploadFiles'])) {
				throw new Exception ();
			}

			// Check if we have one or multiple files to upload
			$mode = (is_array ($_FILES['cmtUploadFiles']['name'])) 
				? 'multi'
				: 'single';

			// Upload parameters for all cases
			$uploadParams = [
				'uploadFile' => $_FILES, // Default to single mode, override in multiple mode
				'targetDirectory' => $this->currentDirectory,
				'makeFilenameWebsave' => true,
				'chmod' => 0644
			];


			switch ($mode) {

				case 'single':
					$success = $this->fileHandler->handleUpload ($uploadParams);
					break;

				case 'multi':

					$filesCount = $mode == 'single' ? 1 : count ($_FILES['cmtUploadFiles']['name']);

					for ($i = 0; $i < $filesCount; $i++) {
						$fileData = [
							'foobar' => [
								'name' => $_FILES['cmtUploadFiles']['name'][$i],
								'type' => $_FILES['cmtUploadFiles']['type'][$i],
								'tmp_name' => $_FILES['cmtUploadFiles']['tmp_name'][$i],
								'error' => $_FILES['cmtUploadFiles']['error'][$i],
								'size' => $_FILES['cmtUploadFiles']['size'][$i],
							]
						];

						$uploadParams['uploadFile'] = $fileData;
						$success = $this->fileHandler->handleUpload ($uploadParams);
					}
					break;

				default:
					$success = false;
					break;
			}

			if ($success === false) {
				throw new Exception ("Upload failed");
			}

			$this->setSuccessMessage ("uploadSuccess", true);
		}
		catch (Exception $e) {
			$this->setErrorMessage ('uploadError', true);
		}

		if ($this->isAjax) {
			$this->content = [
				'success' => $success,
				'directoryContent' => $this->getDirectoryContent ()
			];
			return;
		}

		$this->changeAction ('default');
	}

	/**
	 * public function actionReadFile()
	 * 
	 * Action: Read text file content to preview or edit
	 */
	protected function actionReadFile() {
		$data = new \stdClass();

		$filePath = $this->requestvars['cmtFile'];

		if (!$filePath) {
			$errorMessage = 'No file name given!';
		} else {

			// check if file readable, if not return error message
			$isReadable = is_readable($filePath);

			// check if file is writeable, return parameter to allow edit
			$isWritable = is_writeable($filePath);

			// read file content, return as base64 encoded string, else -->
			if ($isReadable) {
				$fileContent = file_get_contents($filePath);
			} else {
				// return error if cannot read file
				$errorMessage = 'File is not readable';
			}

			$data->isReadable = $isReadable;
			$data->isWritable = $isWritable;
			
			$fileTypeWrapper = array(
				'inc' => 'php',
				'php' => 'php',
				'phtml' => 'php',
				'htm' => 'html',
				'html' => 'html',
				'tpl' => 'html',
				'js' => 'javascript',
				'pl' => 'perl',
				'py' => 'python',
				'sql' => 'sql',
				'xml' => 'xml',
				'txt' => 'text',
				'csv' => 'text',
				'css' => 'css'
			);
			$fileInfos = pathinfo($filePath);
			$data->fileType = $fileTypeWrapper[$fileInfos['extension']];
			if (!$data->fileType) {
				$data->fileType = 'text';
			}

			$data->filePath = $filePath;
			$data->fileName = $fileInfos['basename'];

			$data->fileContent = base64_encode(htmlentities($fileContent));
		}


		if ($errorMessage) {
			$data->errorMessage = base64_encode($errorMessage);
		}

		echo json_encode($data);

		exit;
	}

	/**
	 * public function actionWriteFile()
	 * 
	 * Action: write edited text to file 
	 */
	protected function actionWriteFile() {

		$data = new \stdClass();

		$filename = $this->requestvars['cmtFile'];
		$fileContent = $this->requestvars['cmtFileContent'];

		if (!$filename) {
			$errorMessage = 'No file name given!';
		} else {

			// check if file is writeable
			$isWritable = is_writeable($filename);

			// write file content, else -->
			if ($isWritable) {
				$check = file_put_contents($filename, $fileContent);
				if (!$check) {
					$errorMessage = "Failed to write to file system!";
				} else {
					$data->success = $check;
				}
				// return error if cannot write file
			} else {
				// return error if cannot read file
				$errorMessage = 'File is not writeable';
			}
		}

		if ($errorMessage) {
			$data->errorMessage = base64_encode($errorMessage);
		}

		echo json_encode($data);
		exit;
	}
	
	/**
	 * protected function actionNewFile()
	 * Creates a new file.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionNewFile() {
		$newFileName = trim($this->requestvars['cmtNewFile']);
		
		$nameParts = explode('.', $newFileName);
		$nameParts[0] = $this->cmt->makeNameWebSave($nameParts[0]);
		$newFileName = implode('.', $nameParts);

		$newFilePath = $this->currentDirectory . $newFileName;
		
		$this->parser->setParserVar('newFileName', $newFileName);
		
		// does file already exist? => error
		if (file_exists($newFilePath)) {
			$this->setErrorMessage('newFileExistsError', true);
			$this->changeAction('default');
			return;
		}
		
		$check = $this->fileHandler->createFile(array(
			'file' => $newFilePath
		));
		
		if (!$check) {
			$this->setErrorMessage('newFileCreationError', true);
			$this->changeAction('default');
			return;			
		}
		
		$this->setSuccessMessage('newFileSuccess', true);		
		$this->changeAction('default');
	}

}

$controller = new AppFileBrowserController(); // array("cmtSettings" => $cmt_settings)
$replace = $controller->work();
?>
