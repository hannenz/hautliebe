<?php   
/**
 * app_layout.inc
 * Application to edit the page layout.
 * 
 * @version 2016-09-21
 * @author J.Hahn <info@contentomat.de>
 */

namespace Contentomat\Layout;

use Contentomat\ApplicationController;
use Contentomat\PsrAutoloader;
use Contentomat\cmtPage;
use Contentomat\FileHandler;
use Contentomat\Breadcrumbs;
use Contentomat\EvalCode;
use Contentomat\Contentomat;

class ApplicationLayout extends ApplicationController {
	
	protected $pageHandler;
	protected $pageData;
	protected $pageID;
	protected $fileHandler;

	/**
	 * public function init()
	 * Do initialization bogus like get request vars and more...
	 * 
	 * @see \Contentomat\ApplicationController::init()
	 */
	public function init() {

		$this->cmt->setUseTemplate(false);
		
		$this->parser = new LayoutParser();
		$this->pageHandler = new LayoutPageHandler();
		$this->fileHandler = new FileHandler();

		$this->handleContentomatVars(array (
			'vars' => array(
				'cmtPageID' => '',
				'cmtObjectTemplateID' => 0,
				'cmtLanguage' =>  DEFAULTLANGUAGE,
				'cmtDeletedObjectIDs' => '',
				'cmtPath' => '/',
				'cmtBasePath' => '',
				'cmtInternalPageID' => 0,
				'cmtInternalParentID' => 0,
				'cmtInternalLanguage' => DEFAULTLANGUAGE
			),
			'sessionVars' => array(
// TODO: removed 2016-07-17: test this!!!!!!!!!!
//				'cmtBasePath'
			),
			'userVars' => array()
		));
		
		// page's ID
		$this->pageID = intval($this->cmtPageID);
		$this->pageHandler->setLanguage($this->cmtLanguage);

		if (!$this->pageID) {
			$pageData = $this->pageHandler->getStartPage($this->cmtLanguage);
			$this->pageID = $pageData['id'];
		}

		$this->parser->setParserVar('cmtVersion', $this->cmt->getVersion());

		$this->parser->setPagesTable('cmt_pages_' . $this->cmtLanguage);
		$this->parser->setContentsTable('cmt_content_' . $this->cmtLanguage);
		$this->parser->setLinksTable('cmt_links_' . $this->cmtLanguage);
		
		$this->pageData = $this->pageHandler->getPage(array(
			'pageID' => $this->pageID,
			'language' => $this->cmtLanguage
		));
		$this->parser->setPageVars($this->pageData);
		
		$this->parser->setPageId($this->pageID);
		$this->parser->setPageLanguage($this->cmtLanguage);
		$this->parser->setParentId($this->pageData['cmt_parentid']);

		// init language in parser class
		$this->parser->setI18NTable(array(
			'table' => 'cmt_i18n',
			'idField' => 'string_id',
			'textField' => 'string_' . $this->cmtLanguage
		));
		
		$this->parser->setI18NLanguage($this->cmtLanguage);

		/* TODO: change to parser variables! */
		// this is for backward compatibility reasons
		define('SELFURL', SELF.'?sid='.SID);
		define ('CMT_ACTION', $this->action);
		define ('PAGELANG', $this->cmtLanguage);
		define('PAGEID', $this->cmtPageID);
	}
	
	/**
	 * public function actionDefault()
	 * Show the selected page in layoutmode.
	 * 
	 * @see \Contentomat\ApplicationController::actionDefault()
	 */
	public function actionDefault() {

		$l = $this->pageHandler->getLanguages();
		foreach ((array)$l as $shortcut => $name) {
			$languages[] = array(
				'languageShortcut' => $shortcut, 
				'languageName' => $name
			);
		}
		$this->parser->setParserVar('cmtLanguages', $languages);
		$this->parser->setParserVar('cmtPageTitle', $this->pageData['cmt_title']);
		$this->parser->setParserVar('cmtPageVisibility', $this->pageData['cmt_showinnav']);
		
		$pageTemplate = $this->pageHandler->getPageTemplate($this->pageData['cmt_template']);
		$this->parser->setPageTemplateId($this->pageData['cmt_template']);
		
		$this->content = $this->parser->parse($pageTemplate);		
	}


	/**
	 * protected function actionSave()
	 * Saves the page contents.
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionSavePage() {
		
		$content = json_decode($_REQUEST['cmtPageContent']);
		$deletedObjectIDs = json_decode($_REQUEST['cmtDeletedObjectIDs']);
		$links = json_decode($_REQUEST['cmtPageLinks']);
		$pageData = json_decode($_REQUEST['cmtPageData']);

		// Methode muss die Objekt-IDs in der gleichen Struktur zurückgeben, damit diese neuen Objekten zugeordent werden kann!
		$this->pageHandler->setLanguage($this->cmtLanguage);
		$this->pageHandler->setPageID($this->pageID);

		// delete marked content objects
		$deletion = $this->pageHandler->deleteObjects(array(
			'objectIDs' => $deletedObjectIDs,
			'language' => $this->cmtLanguage
		));
		
		// save content objects
		$saving = $this->pageHandler->savePageContent(array(
			'content' => $content,
			'links' => $links,
			'pageID' => $this->pageID,
			'language' => $this->cmtLanguage
		));
		
		// save page data
		$savePageData = $this->pageHandler->savePageData(array(
			'id' => $this->pageID,
			'cmt_title' => $pageData->cmtPageTitle,
			'cmt_showinnav' => intval($pageData->cmtPageVisibility)
		));
		
		$this->content = array(
			'objectIDs' => $saving['objectIDs'],
			'linkIDs' => $saving['linkIDs'],
			'savingSuccessful' => $saving['savingSuccessful'],
			'savingError' => $saving['DBError'],
			'savingErrorNr' => $saving['DBErrorNr'],
			'deletionSuccessful' => $deletion['deletionSuccessful'],
			'pageDataSuccessful' => $savePageData
		);
		
		$this->isJson = true;
		$this->isAjax = true;

	} 

	/**
	 * protected function actionGetObjectTemplate()
	 * Returns a layout object template by its id.
	 * 
	 * @param void
	 * @return string The raw template code
	 *
	 */
	protected function actionGetObjectTemplate() {
		
		$this->isAjax = true;
		$this->isJson = false;
		
		$this->content =  $this->parser->getObjectTemplate($this->cmtObjectTemplateID);

	}
	
	/**
	 * protected function actionLoadPages()
	 * Load and display the page structure e.g. for link target selection in layout mode.
	 * 
	 * @param void
	 * @return string The parsed page structure
	 *
	 */
	protected function actionLoadPages() {

		if ($this->cmtInternalPageID && !$this->cmtInternalParentID) {

			$sp = $this->pageHandler->getPageData(array(
					'pageID' => intval($this->cmtInternalPageID),
					'language' => $this->cmtInternalLanguage
			));
			$parentID = $sp['cmt_parentid'];
			
		} else {

			$parentID = $this->cmtInternalParentID;
		}
		
		$p = $this->pageHandler->getPages(array(
			'parentID' => $parentID,
			'depth' => 1,
			'language' => $this->cmtInternalLanguage
		));
	
		$pages = array();
		foreach((array)$p as $page) {
			$pages[] = $page['data'];
		}

		$this->parser->setParserVar('pages', $pages);
		$this->parser->setParserVar('parentID', $parentID);
		$this->parser->setParserVar('parentTitle', htmlentities($sp['cmt_title']));
		
		if ($parentID == 'root') {
			$this->parser->setParserVar('isRootLevel', true);
		}
	
		$this->content = array(
			'error' => false,
			'html' => $this->parser->parseTemplate('app_layout/cmt_pages_content.tpl')
		);

		$this->isAjax = true;
		$this->isJson = true;
	}

	/**
	 * protected function actionLoadElementScript()
	 * Executes an include file and returns its contents.
	 * 
	 * @param void
	 * @return String The script generated content.
	 *
	 */
	protected function actionLoadElementScript() {
		
		$this->isAjax = true;
		$this->isJson = false;
		
		if (is_file(PATHTOWEBROOT . $this->cmtPath)) {
			
			$codeEvaler = new EvalCode();
			
			// pass content element data to Content-o-mat
			$content = array(
				'head1' => trim($_POST['head1']),
				'head2' => trim($_POST['head2']),
				'head3' => trim($_POST['head3']),
				'head4' => trim($_POST['head4']),
				'head5' => trim($_POST['head5']),
			);
			
			$this->cmt->setVar('cmtContentData', $content);
		
			// execute script
			$this->content = $codeEvaler->evalFile(PATHTOWEBROOT . $this->cmtPath);
		}
	}
	
	/**
	 * protected function actionLoadScripts()
	 * Loads the content of a selected directory to display it.
	 * 
	 * @param void
	 * @return Parsed directory content to display in layout mode gui.
	 *
	 */
	protected function actionLoadScripts() {
		
		$this->changeAction('loadDirectory');
	}

	/**
	 * protected function actionLoadDirectory()
	 * Loads the content of a selected directory to display it.
	 *
	 * @param void
	 * @return Parsed directory content to display in layout mode gui.
	 *
	 */
	protected function actionLoadDirectory() {

		$pathInfo = pathinfo($this->cmtPath);
		$pathToWebRoot = $this->cmt->getPathToWebRoot();
		
		$this->cmtPath = preg_replace('/^' . preg_quote($pathToWebRoot, '/') . '/', '', $this->cmtPath);
		$this->cmtPath = preg_replace('/^' . preg_quote($this->cmtBasePath, '/') . '/', '', $this->cmtPath);
		
		$basePath = $this->fileHandler->cleanPath($pathToWebRoot . $this->cmtBasePath);
		
		if (is_file($basePath . $this->cmtPath)) {
			$this->cmtPath = $pathInfo['dirname'];
			$this->parser->setParserVar('selectedFile', $pathInfo['basename']);
			$this->session->setSessionVar('selectedFile', $pathInfo['basename']);
			$this->session->saveSessionVars();
		} else {
			$this->parser->setParserVar('selectedFile', $this->session->getSessionVar('selectedFile'));
		}		
		
		$breadcrumbsPath = $this->fileHandler->cleanPath($this->cmtPath, true);
		$this->cmtPath =  $this->fileHandler->cleanPath($basePath . '/' . $this->fileHandler->cleanPath($this->cmtPath, true));
		
		// create breadcrumbs
		$b = new Breadcrumbs();
		
		$breadcrumbs = $b->createBreadcrumbs(array('string' => $breadcrumbsPath));
		
		if (empty($breadcrumbs['breadcrumbs'])) {
			$this->parser->setParserVar('isRootLevel', true);
		} else {
			$this->parser->setParserVar('parentTitle', array_pop($breadcrumbs['breadcrumbs']));
			$this->parser->setParserVar('parentPath', array_pop($breadcrumbs['links']));
		}

		$directory = $this->fileHandler->showDirectory(array(
			'directory' => $this->cmtPath,
			'showSubDirectories' => false,
			'maxExecutionTime' => 10
		));

		$directories = array();
		$files = array();
		
		foreach ((array)$directory as $path => $elementName) {
			
			$isDirectory = is_dir($path);
			
			$path = preg_replace('/^' . preg_quote($downloadBasePath, '/') . '/', '', $path);
			$pathInfo = pathinfo($path);
			
			if ($isDirectory) {
				$directories[] = array(
					'directoryPath' => $path,
					'directoryName' => $elementName,
				);
			} else {
				$files[] = array(
					'filePath' => $path,
					'fileName' => $elementName,
					'fileType' => strtolower($pathinfo['extension']),
				);
			}
		}
	
		$this->parser->setParserVar('directories', $directories);
		$this->parser->setParserVar('files', $files);
		
		$this->content = array(
			'error' => false,
			'html' => $this->parser->parseTemplate('app_layout/cmt_directory_content.tpl')
		);

		$this->isAjax = true;
		$this->isJson = true;
		
	}

	/**
	 * protected function actionLoadImages()
	 * Ajax action: shows only images in the selected directory.
	 * 
	 * @param void
	 * @return string Parsed directory content to display in layout mode gui.
	 *
	 */
	protected function actionLoadImages() {

		$pathInfo = @ pathinfo($this->cmtPath);
		$pathToWebRoot = $this->cmt->getPathToWebRoot();

		if (@ is_file($this->cmtPath)) {
			$this->cmtPath = $pathInfo['dirname'];
			$this->parser->setParserVar('selectedFile', $pathInfo['basename']);
			$this->session->setSessionVar('selectedFile', $pathInfo['basename']);
			$this->session->saveSessionVars();
		} else {
			$this->parser->setParserVar('selectedFile', $this->session->getSessionVar('selectedFile'));
		}
		$this->cmtPath = preg_replace('/^' . preg_quote($pathToWebRoot, '/') . '/', '', $this->cmtPath);
		$this->cmtPath = preg_replace('/^' . preg_quote($this->cmtBasePath, '/') . '/', '', $this->cmtPath);

		$imagesBasePath = $this->fileHandler->cleanPath($pathToWebRoot . $this->cmtBasePath);
	
		$breadcrumbsPath = $this->fileHandler->cleanPath($this->cmtPath, true);
		$this->cmtPath =  $this->fileHandler->cleanPath($imagesBasePath . '/' . $this->fileHandler->cleanPath($this->cmtPath, true));

		// create breadcrumbs
		$b = new Breadcrumbs();
		
		$breadcrumbs = $b->createBreadcrumbs(array('string' => $breadcrumbsPath));
		
		if (empty($breadcrumbs['breadcrumbs'])) {
			$this->parser->setParserVar('isRootLevel', true);
		} else {
			$this->parser->setParserVar('parentTitle', array_pop($breadcrumbs['breadcrumbs']));
			$this->parser->setParserVar('parentPath', array_pop($breadcrumbs['links']));
		}

		// get directory content
		$directory = $this->fileHandler->showDirectory(array(
			'directory' => $this->cmtPath,
			'showSubDirectories' => false,
			'showOnlyFileTypes' => 'jpg,jpeg,png,gif,svg',
			'maxExecutionTime' => 10
		));

		$directories = array();
		$images = array();
	
		foreach ((array)$directory as $path => $elementName) {
				
			$isDirectory = is_dir($path);
			
			$imageSource = $path;
			$path = preg_replace('/^' . preg_quote($imagesBasePath, '/') . '/', '', $path);
			
			if ($isDirectory) {
				$directories[] = array(
					'directoryPath' => $path,
					'directoryName' => $elementName,
				);
			} else {
				
				$imageSizes = getimagesize($imageSource);
				
				$images[] = array(
					'imageSource' => $imageSource,
					'filePath' => $imageSource,
					'fileName' => $elementName,
					'imageWidth' => $imageSizes[0],
					'imageHeight' => $imageSizes[1]
				);
			}
		}
	
		$this->parser->setParserVar('directories', $directories);
		$this->parser->setParserVar('images', $images);
		$this->parser->setParserVar('cmtPath', $this->cmtPath);
	
		$this->content = array(
			'error' => false,
			'html' => $this->parser->parseTemplate('app_layout/cmt_images_content.tpl')
		);
	
		$this->isAjax = true;
		$this->isJson = true;
	
	}

	/**
	 * protected function actionGetImagePlaceholder()
	 * Parses the file 'app_layout/cmt_image_placeholder.tpl' and generates the image placeholder HTML out of it.
	 * 
	 * @param void
	 * @return String HTML code to display.
	 *
	 */
	protected function actionGetImagePlaceholder() {
		
		$this->isAjax = true;
		$this->isJson = false;

		$this->content = '<span class="cmt-element-wrapper" data-element-type="image" data-element-nr="1" data-cmt-is-placeholder="1">'. $this->parser->parseTemplate('app_layout/cmt_image_placeholder.tpl') . '</span>';
	}
	
	/**
	 * protected function actionNewObject()
	 * Ajax action: Creates a new page object and returns it as parsed template in the content.
	 *  
	 *  @param void
	 *  @return void
	 */
	protected function actionNewObject() {
		
		$this->isAjax = true;
		$this->content = $this->pageHandler->createObject(array(
			'pageID' => $this->pageID,
			'language' => $this->cmtLanguage,
			'objectGroup' => $this->cmt_obj_group,
			'objectTemplateID' => $this->cmtObjectTemplateID,
			'objectContent' => array()
		));
// TODO: do something with error messages!		
		if (!$this->content) {
			$this->parser->setParserVar('errorMessage', true);
			$this->parser->setParserVar('errorCreateObject', true);
		} else {
			$this->parser->setParserVar('successMessage', true);
			$this->parser->setParserVar('successCreateObject', true);
		}
	}

	/**
	 * protected function actionDeleteObject()
	 * Deletes a layout object.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDeleteObject() {
		
		$this->isAjax = true;
		$check = $this->pageHandler->deleteObject($this->cmt_obj_id, $this->cmtLanguage);
		
		// TODO: do something with error messages!
		if (!$this->content) {
			$this->parser->setParserVar('errorMessage', true);
			$this->parser->setParserVar('errorCreateObject', true);
		} else {
			$this->parser->setParserVar('successMessage', true);
			$this->parser->setParserVar('successCreateObject', true);
		}
	}

	/**
	 * protected function actionDuplicateObject()
	 * Duplicates a layout object.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDuplicateObject() {
	
		$this->isAjax = true;
		$this->content = $this->pageHandler->duplicateObject($this->cmt_obj_id, $this->cmtLanguage);
	
		// TODO: do something with error messages!
		if (!$this->content) {
			$this->parser->setParserVar('errorMessage', true);
			$this->parser->setParserVar('errorCreateObject', true);
		} else {
			$this->parser->setParserVar('successMessage', true);
			$this->parser->setParserVar('successCreateObject', true);
		}
	}
	
	/**
	 * private function getHomepageData()
	 * Helper: searches the homepage when no page ID is given
	 * 
	 * @param void
	 * @return array Returns the dataset of the homepage.
	 */
	private function getStartPage() {
		
		if (!$this->cmtLanguage) {
			return array();
		}
		
		$this->db->query("SELECT * FROM cmt_pages_" . $this->cmtLanguage . " WHERE cmt_isroot = '1'");
		$pageData = $this->db->get();
		
		if ($pageData['cmt_type'] == 'page') {		
			return $pageData;
		}
		
		// Homepage is a folder
		$this->db->query("
			SELECT * FROM cmt_pages_" . $this->cmtLanguage . " 
			WHERE cmt_parentid = '". $pageData['id'] ."' 
			AND cmt_type = 'page' 
			ORDER BY cmt_position 
			LIMIT 1
		");
		
		$pageData = $this->db->get();
		return $pageData;
	}

	public function actionUploadFiles () {
		try {
			$uploadParams = [
				'uploadFile' => $_FILES, 
				'targetDirectory' => INCLUDEPATHTOADMIN . $_POST['cmtUploadPath'],
				'makeFilenameWebsave' => true,
				'chmod' => 0644
			];

			$success = $this->fileHandler->handleUpload ($uploadParams);
			if (!$success) {
				$message = $this->fileHandler->lastErrorMessage;
				var_dump ($message);
				die ();
				throw new \Exception (join ("\n", $this->fileHandler->lastErrorMessage));
			}
		}
		catch (\Exception $e) {
			die (json_encode ([
				"error" => true,
				"message" => $e->getMessage ()
			]));
		}

		$this->cmtPath = $_POST['cmtUploadPath'];
		$this->changeAction ("loadImages");
	}
}

$autoLoad = new PsrAutoloader();
$autoLoad->addNamespace('Contentomat\Layout', INCLUDEPATHTOADMIN . 'classes/app_layout');
$autoLoad->addNamespace('Contentomat', INCLUDEPATH . 'phpincludes/classes');


$controller = new ApplicationLayout();
$content =  $controller->work();
