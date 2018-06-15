<?php   
/**
 * app_pages.php
 * Application to edit the page structure and the available page language versions.
 * 
 * @version 2014-04-03
 * @author J.Hahn <info@contentomat.de>
 */
namespace Contentomat;

class ApplicationPages extends ApplicationController {

	protected $cmtNodeID;
	protected $cmtDuplicatedID;
	protected $cmtPages;
	protected $cmtLanguage;
	protected $cmtLayoutApplicationID;
	
	protected $gui;
	protected $pageHandler;
	protected $applicationHandler;
	protected $user;
	protected $dataformat;
	protected $fieldHandler;
	protected $form;
	protected $fileHandler;
	protected $page;
	
	protected $pagesTable;
	protected $pagesTablePrefix;
	protected $languagesTable;
	protected $pagesTemplateRow;
//	protected $pagesTemplateFrame;
	protected $availableLanguages;
	protected $availableCharsets;
	protected $layoutApplicationID;
	protected $defaultCharset;
	protected $dataformatAction;
	protected $languageData;

	/**
	 * public function init()
	 * The "constructor" of the extended class. do some initializing work and call the actions.
	 * 
	 * @param void
	 * @return void
	 */
	public function init() {
		
		$this->defaultCharset = 'utf8';
		
		$this->gui = new Gui();
		$this->pageHandler = new PageHandler();
		$this->applicationHandler = new ApplicationHandler();
		$this->fileHandler = new FileHandler();

		$this->availableLanguages = $this->pageHandler->getLanguages();

		$this->handleContentomatVars(array (
			'vars' => array(
				'cmtNodeID' => 'root',
				'cmtCurrentNodeID' => 'root',
				'cmtPages' => array(),
				'cmtDuplicatedID' => 0,
				'cmtLanguage' =>  $this->cmtLanguage,
				'cmtPages' => array(),
				//'cmt_showlanguage' => $this->cmtLanguage,
				'id[]' => array(),
				'edited_id' => 0,
				'action_performed' => '',
				'cmtDuplicatedID' => 0,
				'cmtCopyMode' => 'copyAll',
				'cmtCopyFromLanguage' => $this->cmtLanguage
			),
			'sessionVars' => array(
				//'cmtNodeID',
				'cmtCurrentNodeID',
				'cmtLanguage'
			)
		));
// ??? TODO!
		if ($this->id[0]) {
			//$this->cmtNodeID = $this->id[0];
		}

		// if no language shortcut is given, take the first of the available languages
		if (!$this->cmtLanguage) {
			$this->cmtLanguage = array_shift(array_keys($this->availableLanguages));
		}
		$this->parser->setParserVar('cmtLanguage', $this->cmtLanguage);
		if (!$this->cmtCurrentNodeID) {
			$this->cmtCurrentNodeID = 'root';
		}
		$this->parser->setParserVar('cmtCurrentNodeID', $this->cmtCurrentNodeID);
		
		$this->templatesPath = $this->templatesPath . 'app_pages/';
		
		$this->pagesTemplateRow = $this->parser->getTemplate($this->templatesPath . 'cmt_pages_overview_row.tpl');
		
		// get the layout aplications ID
		$layoutApp = $this->applicationHandler->getApplicationByFilename('app_layout');
		$layoutAppID = intval($layoutApp[0]['id']);
		$this->layoutApplicationID = $layoutAppID;
		
		// compatibillity things. TODO: delete in next version!
		if ($this->cmt_showlanguage) {
			$this->cmtLanguage = $this->cmt_showlanguage;
		}

		$this->pagesTablePrefix = 'cmt_pages_';
		$this->pagesTable = $this->pagesTablePrefix . $this->cmtLanguage;
		$this->languagesTable = 'cmt_content_languages';
		
		$this->languageData = array();

	}
	
	/**
	 * public function actionDefault()
	 * Default action: Shows the pages list 
	 * 
	 * @params void
	 * @return void
	 * 
	 * @see ApplicationController::actionDefault()
	 */
	public function actionDefault() {
		
		// Execute e.g. after saving the page and some actions for the child pages should be performed like data inheritance. 

		if ($this->action_performed) {
			$actionPerformed = $this->action_performed;
			unset($this->action_performed);
			
			$this->changeAction('after' . ucfirst($actionPerformed));
		}
		
		// 1. Create tabs
		$tabsContent = $this->createTabs();
		$this->parser->setParserVar('tabsContent', $tabsContent);
		
		$parents = $this->pageHandler->getParents(array(
			'pageID' => $this->cmtCurrentNodeID,
			'language' => $this->cmtLanguage
		));
	
		// 2. Create breadcrumb-navi
		$breadcrumbRow = $this->parser->getTemplate($this->templatesPath . 'cmt_pages_breadcrumbs_row.tpl');
		$breadcrumbs = '';
		$breadcrumbsTotal = count($parents);

		$this->parser->setParserVar('cmtLanguage', $this->cmtLanguage);
			
		foreach ($parents as $key => $parent) {
	
			if ($key+1 == $breadcrumbsTotal) {
				$this->parser->setParserVar('isLastBreadcrumb', true);
			} else {
				$this->parser->setParserVar('isLastBreadcrumb', false);
			}
		
			$this->parser->setMultipleParserVars($parent);
			$breadcrumbs .= $this->parser->parse($breadcrumbRow);
		}
		$this->parser->setParserVar('breadcrumbs', $breadcrumbs);
		
		// 3. Show pages
		$pages = $this->pageHandler->getPages(array(
				'parentID' => $this->cmtCurrentNodeID,
				'language' => $this->cmtLanguage,
				'depth' => 1
		));
				
		$pagesContent = $this->pageHandler->showPages(array(
			'pagesData' => $pages,
			'templateRow' => $this->pagesTemplateRow,
			//'templateFrame' => $this->pagesTemplateFrame,
			'templateData' => array(
				'cmtLanguage' => $this->cmtLanguage,
				'cmtApplicationID' => $this->applicationID,
				'cmtReturnToApplicationID' => $this->applicationID,
				'cmtNodeID' => $this->cmtCurrentNodeID,
				'cmtLayoutApplicationID' => $this->layoutApplicationID,
				'cmtPagesTable' => $this->pagesTable
			)
		));
		$this->parser->setParserVar('pagesContent', $pagesContent);
		
		// add some more variables
		$this->parser->setMultipleParserVars(array(
			'cmtLanguage' => $this->cmtLanguage,
			'cmtApplicationID' => $this->applicationID,
			'cmtReturnToApplicationID' => $this->applicationID,
			'cmtNodeID' => $this->cmtNodeID,
			'cmtLayoutApplicationID' => $this->layoutApplicationID,
			'cmtPagesTable' => $this->pagesTable,
			'cmtLanguagesCount' => count($this->availableLanguages)
		));
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_pages_overview.tpl');
		
	}
	
	/**
	 * protected function actionLoadNode()
	 * Shows the content of one ode.
	 *
	 * @params number $nodeID Optional: ID of the node to show
	 * 
	 * @return string Parsed node content
	 */
	protected function actionLoadNode($nodeID=0) {
	
		if (!intval($nodeID)) {
			$nodeID = $this->cmtNodeID; 
		}

		$nodeChildren = $this->pageHandler->getPages(array(
				'parentID' => $nodeID,
				'language' => $this->cmtLanguage,
				'depth' => 1
		));
			
		$this->content = $this->pageHandler->showPages(array(
			'pagesData' => $nodeChildren,
			'templateRow' => $this->pagesTemplateRow,
			//'templateFrame' => $this->pagesTemplateFrame,
			'templateData' => array(
				'cmtLanguage' => $this->cmtLanguage,
				'cmtApplicationID' => $this->applicationID,
				'cmtReturnToApplicationID' => $this->applicationID,
				'cmtNodeID' => $this->cmtNodeID,
				'cmtLayoutApplicationID' => $this->layoutApplicationID,
				'cmtPagesTable' => $this->pagesTable
			)
		));
		$this->isAjax = true;
	}

	/**
	 * protected function actionSavePagesOrder()
	 * Saves the order of the pages of a node (and all subpages)
	 *
	 * @param void
	 * @return void
	 */
	protected function actionSavePagesOrder() {
		
		$this->pageHandler->setLanguage($this->cmtLanguage);
		$check = $this->pageHandler->savePagesOrder($this->createPagesArray($this->cmtPages), $this->cmtCurrentNodeID); // oder $this->cmtNodeID??

		$this->isAjax = true;
		$this->isJson = true;
		
		if ($check) {
			$this->content = array(
				'cmtAction' => 'savePagesOrder',
				'cmtStatus' => true
			);
		} else {
			$this->content = array(
				'cmtAction' => 'savePagesOrder',
				'cmtStatus' => false
			);
		}
	}
	
	/**
	 * protected function createPagesArray()
	 * Creates an array in the standard pagehandler format out of the array send by the pages javascript 
	 *
	 * @param array $pageData Array received in the request, send by the pages Javascript
	 * @return array Page data array in the structure needed in 
	 *
	 */
	protected function createPagesArray($pageData) {
		
		$pages = array();
		
		if (!is_array($pageData)) {
			return;
		}
		
		foreach ($pageData as $page) {
			$pages[$page['id']]['data']['id'] = $page['id'];
			$pages[$page['id']]['data']['cmt_pagepos'] = $page['position'];
			$pages[$page['id']]['data']['cmt_parentid'] = $page['parentID'];
			
			if (is_array($page['children'])) {
				$pages[$page['id']]['pages'] = $this->createPagesArray($page['children']);
				//$this->createPagesArray($page['children']);
			}
		}
		
		return $pages;
	}

	/**
	 * protected function actionDeleteMultiple()
	 * Deletes multiple pages and their contents and links.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDeleteMultiple() {
		
		if (is_array($this->id)) {
	
			foreach ($this->id as $pageID) {
				$check = $this->pageHandler->deletePage(array(
					'pageID' => $pageID,
					'language' => $this->cmtLanguage
				));
			}
		} else {
			$check = false;
		}
			
		if ($check) {
			$this->parser->setParserVar('multipleDeletionSuccess', true);
		} else {
			$this->parser->setParserVar('multipleDeletionError', true);
		}

		// show pages overview / default view
		$this->action_performed = '';
		$this->changeAction('');
	}
	
	/**
	 * protected function actionDelete()
	 * Deletes a page and its contents and links.
	 *
	 * @params void
	 * @return void
	 */
	protected function actionDelete() {

		$check = $this->pageHandler->deletePage(array(
			'pageID' => $this->edited_id,
			'language' => $this->cmtLanguage
		));
			
		if ($check) {
			$this->parser->setParserVar('deletionSuccess', true);
		} else {
			$this->parser->setParserVar('deletionError', true);
		}
		
		// show pages overview / default view
		$this->action_performed = '';
		$this->changeAction('');
	}
	
	protected function actionNew() {
		
		// remove 'is startpage' flag from other page if this one is set to 'is startpage'
		if ($_REQUEST['save'] && $_REQUEST['cmt_isroot']) {
			
			$check = $this->pageHandler->setStartPage($this->edited_id, $this->cmtLanguage);
		}
		$this->pageHandler->setNextAvailablePageId($this->pageHandler->getNextAvailablePageId());
	}
	
	protected function actionDuplicate() {
		
		$this->pageHandler->setNextAvailablePageId($this->pageHandler->getNextAvailablePageId());
		
		// remove 'is startpage' flag from other page if this one is set to 'is startpage'
		if ($_REQUEST['save'] && $_REQUEST['cmt_isroot']) {
			
			$check = $this->pageHandler->setStartPage($this->edited_id, $this->cmtLanguage);
		}
	}
	
	
	protected function actionEdit() {

		// remove 'is startpage' flag from other page if this one is set to 'is startpage'
		if ($_REQUEST['save'] && $_REQUEST['cmt_isroot']) {

			$check = $this->pageHandler->setStartPage($this->edited_id, $this->cmtLanguage);
		}
	}
	/**
	 * protected function actionAfterDuplicate()
	 * Is called after the page is duplicated in the cmt_pages_... table. Action just sets some parser vars 
	 * which show a dialog if the contents of the page should be duplicated too.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionAfterDuplicate() {

		$this->parser->setParserVar('duplicateContents', true);
		$this->parser->setParserVar('cmtEditedID', $this->edited_id);
		$this->parser->setParserVar('cmtDuplicatedID', $this->cmtDuplicatedID);
	
		// show pages overview / default view
		$this->action_performed = '';
		$this->changeAction('');
	}

	/**
	 * protected function actionDuplicateContents()
	 * Duplicates the contents of a page that was duplicated before.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDuplicateContents() {

		$this->user = new User($this->cmt->session->getSessionID());

		$addLinkData = array(
			'cmt_created' => date('Y-m-d H:i:s'),
			'cmt_createdby' => $this->user->getUserID()
		);
			
		$addContentData = array(
			'cmt_created' => date('Y-m-d H:i:s'),
			'cmt_createdby' => $this->user->getUserID(),
			'cmt_lastmodified' => '',
			'cmt_lastmodifiedby' => ''
		);
	
		$check = $this->pageHandler->duplicatePageContents(array(
			'newPageID' => $this->edited_id,
			'duplicatedPageID' => $this->cmtDuplicatedID,
			'language' => $this->cmtLanguage,
			'addLinkData' => $addLinkData,
			'addContentData' => $addContentData
		));
	
		if ($check) {
			$this->parser->setParserVar('duplicateContentsSuccess', true);
		} else {
			$this->parser->setParserVar('duplicateContentsError', true);
		}
		$this->changeAction('');
	}
	
	/**
	 * protected function actionShowLanguages()
	 * Shows all available language versions.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionShowLanguages() {
		
		$tabsContent = $this->createTabs();
		$this->parser->setParserVar('tabsContent', $tabsContent);
		
		$languages = $this->pageHandler->getLanguagesData();
		$this->dataformat = new DataFormat();
		$this->form = new Form();
		
		$languagesContent = '';
		$alternationFlag = 0;
		$templateRow = $this->parser->getTemplate($this->templatesPath . 'cmt_languages_overview_row.tpl');
		
		foreach($languages as $data) {
			
			//$this->parser->setMultipleParserVars($data);
			foreach ($data as $fieldName => $fieldValue) {
				
				$fieldValueFormatted = $this->dataformat->format('cmt_content_languages', $fieldName, $fieldValue, 'view');
				$this->parser->setParserVar($fieldName, $fieldValueFormatted);
			}
			
			$this->parser->setParserVar('id', $data['id']);
			$this->parser->setParserVar('alternationFlag', $alternationFlag++%2);
			$languagesContent .= $this->parser->parse($templateRow);
		}
		
		$this->fieldHandler = new FieldHandler();
		$fields = $this->fieldHandler->getAllFields(array(
			'tableName' => $this->dbTable,
			'getAll' => true
		));

		$this->parser->setParserVar('languagesContent', $languagesContent);
		$this->content .= $this->parser->parseTemplate($this->templatesPath . 'cmt_languages_overview.tpl');
		
	}
	
	/**
	 * protected function actionEditLanguage()
	 * Shows the editable detail view of a language version.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEditLanguage() {
		
		$this->dataformat = new DataFormat();
		
		if (!isset($this->dataformatAction)) {
			$this->dataformatAction = 'edit';
		}

		$this->fieldHandler = new FieldHandler();
		$fields = $this->fieldHandler->getAllFields(array(
				'tableName' => $this->languagesTable,
				'getAll' => true
		));
	
		if (empty($this->languageData)) {
			$this->languageData = $this->pageHandler->getLanguageData(array('id' => intval($this->id[0])));
		}
		
		if (!$this->languageData['cmt_charset']) {
			$this->languageData['cmt_charset'] = $this->defaultCharset;
		}
		
		foreach ($fields as $fieldName => $fieldValue) {
			$newLanguageData[$fieldName] = $this->languageData[$fieldName];
		}

		// create all fields
		foreach ($newLanguageData as $fieldName => $fieldValue) {
	
			$fieldValueFormatted = $this->dataformat->format('cmt_content_languages', $fieldName, $fieldValue, $this->dataformatAction);
			$this->parser->setParserVar($fieldName, $fieldValueFormatted);
			$this->parser->setParserVar($fieldName .'_raw', $fieldValue);
			$this->parser->setParserVar($fieldName .'_description', $this->dataformat->getDescription());
			$this->parser->setParserVar($fieldName .'_alias', $this->dataformat->getAlias());
		}
		
		// select field "charset"
		$this->availableCharsets = $this->db->getAvailableCharsets(true);
		$charsets = array();
	
		foreach ($this->availableCharsets as $charset) {
			$charsets[$charset['Charset']] = $charset['Description'] . ' (' . $charset['Charset'] .')';
		}

		natsort($charsets);
		$values = array_keys($charsets);
		$aliases = array_values($charsets);

		$this->form = new Form();
		$selectCharset = $this->form->select(array(
			'aliases' => $aliases,
			'values' => $values,
			'selected' => $this->languageData['cmt_charset'],
			'optionsOnly' => true
		));
		$this->parser->setParserVar('cmtCharset', $selectCharset);
		
		// select field: "duplicate from language"
		$languages = $this->pageHandler->getLanguages();
		$selectLanguage = $this->form->select(array(
			'aliases' => array_values($languages),
			'values' => array_keys($languages),
			'optionsOnly' => true,
			'selected' => $this->languageData['cmtCopyFromLanguage']
		));
		$this->parser->setParserVar('selectCopyFromLanguage', $selectLanguage);

		// more vars
		$this->parser->setParserVar('id', intval($this->id[0]));
		$this->parser->setParserVar('cmtCopyMode', $this->cmtCopyMode);
		$this->parser->setParserVar('cmtCopyFromLanguage', $this->cmtCopyFromLanguage);
		
		$this->content .= $this->parser->parseTemplate($this->templatesPath . 'cmt_languages_edit.tpl');
	}
	
	
	/**
	 * protected function actionEditLanguageSave()
	 * Saves the language versions data after editing
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEditLanguageSave() {
		
		$fallbackAction = 'editLanguage';
		
		$languageData = $this->pageHandler->getLanguageData(array('id' => ($this->id[0])));

		$this->fieldHandler = new FieldHandler();
		$fieldNames = $this->fieldHandler->getFieldNames($this->languagesTable);
				
		// unset($fieldNames['id']);
		$fieldData = array();
		
		foreach($fieldNames as $fieldName) {
			$fieldData[$fieldName] = trim($_POST[$fieldName]);
			$this->languageData[$fieldName] = $fieldData[$fieldName];
		}
		$fieldData['id'] = $_POST['id'][0];

		$this->parser->setMultipleParserVars($fieldData);

		// Error: no language version shortcut given
		if (!$fieldData['cmt_language']) {

			$this->parser->setParservar('noLanguageNameError', true);
			$this->parser->setParserVar('cmtAction', 'editLanguage');

			$this->changeAction($fallbackAction);
			
			return;
		}
		
		// check on not allowed chars!
		if (!preg_match('/^[0-9,a-z,A-Z$_]+$/', $fieldData['cmt_language'])) {
			
			$this->parser->setParservar('wrongLanguageNameError', true);
			$this->parser->setParserVar('cmtAction', 'editLanguage');
			
			$this->changeAction($fallbackAction);
			return;
		}
		
	
		// start saving
		switch (intval($languageData['id'])) {
			
			// insert new language version
			case 0:
				
				unset($fieldData['id']);
				
				$check = $this->db->query("INSERT INTO " . $this->languagesTable . " SET " . $this->db->makeSetQuery($fieldData));
				if ($check) {
					$this->saveLanguageError('tableLanguages');
					return;
				}
					
				// create tables and fields!
				$languageShortCut = trim($fieldData['cmt_language']);

				// create table cmt_pages_...
				if ($this->cmtCopyMode != 'copyNone') {
					$copyPages = true;
				} else {
					$copyPages = false;
				}
		
				$check = $this->applicationHandler->duplicateApplication(array(
					'tableName' => 'cmt_pages_' . $languageShortCut,
					'tableAlias' => 'Pages (' . $fieldData['cmt_languagename'] .')',
					'tableCharset' => $fieldData['cmt_charset'],
					'sourceTableName' => 'cmt_pages_' . $this->cmtCopyFromLanguage,
					'copyData' => $copyPages
				));
				
				if (!$check) {
					$this->saveLanguageError('tablePages');
					return;
				}
				

				// create table cmt_content_...
				if ($this->cmtCopyMode == 'copyAll') {
					$copyContent = true;
				} else {
					$copyContent = false;
				}
				
				$check = $this->applicationHandler->duplicateApplication(array(
				//$check = $this->applicationHandler->createTableFrom(array(
					'tableName' => 'cmt_content_' . $languageShortCut,
					'tableAlias' => 'Content (' . $fieldData['cmt_languagename'] .')',
					'tableCharset' => $fieldData['cmt_charset'],
					'sourceTableName' => 'cmt_content_' . $this->cmtCopyFromLanguage,
					'copyData' => $copyContent
				));

				if (!$check) {
					$this->saveLanguageError('tableContent');
					return;
				}
				
				// create table cmt_links_...
				$check = $this->applicationHandler->duplicateApplication(array(
				//$check = $this->applicationHandler->createTableFrom(array(
					'tableName' => 'cmt_links_' . $languageShortCut,
					'tableAlias' => 'Links (' . $fieldData['cmt_languagename'] .')',
					'tableCharset' => $fieldData['cmt_charset'],
					'sourceTableName' => 'cmt_links_' . $this->cmtCopyFromLanguage,
					'copyData' => $copyContent
				));
				
				if (!$check) {
					$this->saveLanguageError('tableLinks');
					return;
				}
				
				$this->parser->setParservar('newLanguageSuccess', true);
				break;
				
			// update
			default:
				
				// get old data from languages table
				$this->db->query("SELECT * FROM " . $this->languagesTable . " WHERE id = '" . $fieldData['id'] . "'");
				$oldFieldData = $this->db->get();

				// 1. changes in table "cmt_pages_..."?
				$editTables = array(
					'cmt_pages_',
					'cmt_content_',
					'cmt_links_'
				);
				
				foreach ($editTables as $table) {
					$oldTableData = $this->applicationHandler->getApplicationByTablename($table . $oldFieldData['cmt_language']);

					// break if no old table data is found
					if (!$oldTableData['id']) {
						$this->parser->setParservar('editLanguageOldDataError', true);
						$this->parser->setParserVar('cmtAction', $fallbackAction);
		
						$this->changeAction($fallbackAction);						
					}

					$tableData = array(
						'id' => $oldTableData['id'],
						'cmt_charset' => $fieldData['cmt_charset'],
						'cmt_tablename' => $table . $fieldData['cmt_language']
					);

					$check = $this->applicationHandler->editApplication($tableData);
					
					if (!$check) {
						$this->parser->setParservar('oldTableName', $oldTableData['cmt_tablename']);
						$this->parser->setParservar('newTableName', $tableData['cmt_tablename']);
						$this->parser->setParservar('editLanguageTablesError', true);
						
						$this->parser->setParserVar('cmtAction', $fallbackAction);
						$this->changeAction($fallbackAction);
					}
				}
				
				// save the entry in languages table
				$check = $this->db->query("
					UPDATE " . $this->languagesTable . " 
					SET " . $this->db->makeSetQuery($fieldData) . " 
					WHERE id = '" . intval($languageData['id'])  . "' 
					LIMIT 1
				");
				
				if ($check) {
					$this->parser->setParservar('editLanguageSavingError', true);
					$this->parser->setParserVar('dbError', $this->db->getLastError());
					$this->parser->setParserVar('cmtAction', $fallbackAction);
					$this->parser->setMultipleParserVars($fieldData);
					
					$this->changeAction($fallbackAction);
					return;				
				}
					
				$this->parser->setParservar('editLanguageSuccess', true);
				break;
		}

		// updated languages list
		$this->availableLanguages = $this->pageHandler->getLanguages(true);
		$this->changeAction('showLanguages');
	}
	
	/**
	 * protected function actionNewLanguage()
	 * Shows the details input form when a new language version should be created.
	 *
	 * @param void
	 * 
	 * @return void
	 */
	protected function actionNewLanguage() {
		$this->dataformatAction = 'new';
		$this->changeAction('editLanguage');
	}

	/**
	 * protected function actionNewLanguageSave()
	 * Method is called when a new language creation form is submitted.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionNewLanguageSave() {
		$this->dataformatAction = 'new';
		$this->changeAction('editLanguageSave');
	}

	/**
	 * protected function actionDeleteLanguage()
	 * Deletes a complete language version
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDeleteLanguage() {
		
		// first, check if there is more than one language
		$languages = $this->pageHandler->getLanguages();
		
		if (count($languages) <= 1) {
			$this->parser->setParservar('deleteLanguageInsufficientNrError', true);
			$this->changeAction('showLanguages');
			return;			
		}
		
		$languageData = $this->pageHandler->getLanguageData(array('id' => intval($this->id[0])));
		$this->parser->setParservar('languageName', $languageData['cmt_languagename']);
		$this->parser->setParservar('languageID', $languageData['id']);
		$this->parser->setParservar('language', $languageData['cmt_language']);
		
		if (empty($languageData)) {
			$this->parser->setParservar('deleteLanguageError', true);
			$this->changeAction('showLanguages');
			
			return;			
		}

		// delete language related applications / tables
		$languageTables = array(
			'cmt_pages_' . $languageData['cmt_language'],
			'cmt_content_' . $languageData['cmt_language'],
			'cmt_links_' . $languageData['cmt_language'],
		);
		
		foreach ($languageTables as $tableName) {
			
			$tableData = $this->applicationHandler->getApplicationByTablename($tableName);

			$check = $this->applicationHandler->deleteApplication($tableData['id']);
			
			if (!$check) {
				$this->parser->setParservar('deleteLanguageError', true);
				$this->parser->setParservar('deleteLanguageTableError', $tableName);
				$this->changeAction('showLanguages');
				return;
			}
		}
		
		// delete entry in cmt_content_languages
		$check = $this->db->query("DELETE FROM cmt_content_languages WHERE id = '" . intval($languageData['id']) . "' LIMIT 1");
		if ($check) {
			$this->parser->setParservar('deleteLanguageEntryError', true);
			$this->changeAction('showLanguages');
			return;
		}
		
		// updated languages list
		$this->availableLanguages = $this->pageHandler->getLanguages(true);
		
		$this->parser->setParservar('deleteLanguageSuccess', true);
		$this->changeAction('showLanguages');
		
	}

	protected function actionGetPageTree() {
		
		$this->isAjax = true;
		$this->isJson = true;
		
		$this->content = $this->pageHandler->getPages(array(
			'parentID' => $this->cmtNodeIDnodeID,
			'language' => $this->cmtLanguage,
			'depth' => 99
		));
	}
	
	protected function actionSavePageToFile() {
		$this->isAjax = true;
		$this->isJson = true;		

		$this->parser = new Parser();
		$autoloader = new PsrAutoloader();
		$autoloader->addNamespace('Contentomat', PATHTOWEBROOT . 'phpincludes/classes/');
		
		// Seitendaten an Parser übergeben
		$this->page = new CmtPage();
		$tableNames = $this->page->getPageTablesNames($this->cmtLanguage);
		
		$this->parser->setPagesTable($tableNames['pageTable']);
		$this->parser->setContentsTable($tableNames['contentTable']);
		$this->parser->setLinksTable($tableNames['linkTable']);
		
		$this->parser->setPathToWebroot('');

		$this->page->getPageData($this->cmtNodeID, $this->cmtLanguage);
		$pageData = $page['data'];
		
		$this->parser->setPageVars($pageData);
		$this->parser->setPageId($pageData['id']);
		$this->parser->setParentId($pageData['cmt_parentid']);
		$this->parser->setPageLanguage($language);

		switch($pageData['cmt_type']) {
		
			case 'folder':
				$check = $this->saveFolderToFile($pageData, $language);
				break;
					
			case 'page':
				$check = $this->savePageToFile($pageData, $language);
				break;
		}
		
		$this->content = array('success' => $check);
	}
	
	protected function actionSavePagesToFiles() {
		
		$this->parser = new Parser();
		$autoloader = new PsrAutoloader();
		$autoloader->addNamespace('Contentomat', PATHTOWEBROOT . 'phpincludes/classes/');

		// Seitendaten an Parser übergeben
		$this->page = new CmtPage();
		$tableNames = $this->page->getPageTablesNames($this->cmtLanguage);
		
		$this->parser->setPagesTable($tableNames['pageTable']);
		$this->parser->setContentsTable($tableNames['contentTable']);
		$this->parser->setLinksTable($tableNames['linkTable']);	
		
		$this->parser->setPathToWebroot('');
		
// 		$this->fileHandler->deleteDirectory(array(
// 			'directory' => PATHTOWEBROOT . 'temp/' . $this->cmtLanguage . '/'
// 		));
		
		$this->savePagesToFiles($this->cmtCurrentNodeID, $this->cmtLanguage);
	}
	
	
	protected function savePagesToFiles($nodeID, $language) {
		
		$pages = $this->pageHandler->getPages(array(
			'parentID' => $nodeID,
			'language' => $language,
			'depth' => 1
		));
		
		foreach ($pages as $pageID => $page) {
			
			$pageData = $page['data'];
			$this->parser->setPageVars($pageData);
			$this->parser->setPageId($pageData['id']);
			$this->parser->setParentId($pageData['cmt_parentid']);
			$this->parser->setPageLanguage($language);

			switch($pageData['cmt_type']) {
				
				case 'folder':
					$this->saveFolderToFile($pageData, $language);
					break;
					
				case 'page':
					$this->savePageToFile($pageData, $language);
					break;
			}

			if ($pageData['hasChildren']) {
				$this->savePagesToFiles($pageID, $language);
			}
		}
	}
	
	protected function savePageToFile($pageData, $language) {
		
		$template = $this->page->getPageTemplate($pageData['id'], $language);
		$pageContent = $this->parser->parse($template);
		
		$dir = PATHTOWEBROOT . 'temp/';
		
		// create directory if needed
		$dir .= $language . '/';
		if (!is_dir($dir)) {
			$check = $this->fileHandler->createDirectory(array(
				'directory' => $dir
			));
		}
		
		$dir .= $pageData['id'] . '/';
		if (!is_dir($dir)) {
			$check = $this->fileHandler->createDirectory(array(
				'directory' => $dir
			));
		}
		
//		var_dump('schreibe: ' . $dir . $this->cmt->makeNameWebsave($pageData['cmt_title']) . '.html');
		$check = $this->fileHandler->createFile(array(
			'file' => $dir . '/' . $this->cmt->makeNameWebsave($pageData['cmt_title']) . '.html',
			'content' => $pageContent
		));
//		var_dump($check);
	}
	
	protected function saveFolderToFile($pageData) {
		
	}
	
	/**
	 * Called if an error occured while creating/ saving a language version.
	 *
	 * @param string $errorName
	 * @return void
	 */
	protected function saveLanguageError($errorName) {
		$this->parser->setParserVar('newLanguageError', true);
		
		// 2018-04-13, JH: Not in use in template right now.
		$this->parser->setParserVar('newLanguageErrorName', $errorName);
		
		$this->changeAction('editLanguage');
	}
	
	/**
	 * protected function createTabs()
	 * Creates the language version selection tabs for the website structures overview.
	 *
	 * @param void
	 *
	 * @return string HTML of the tabs for output.
	 */
	protected function createTabs() {
		
		$tabsContent = '';
		
		$this->gui->setTemplate('tab', $this->templatesPath.'gui_tab.tpl', false);
		
		$tabCounter = 1;
		
		foreach ($this->availableLanguages as $shortcut => $name) {
			if (!stristr($this->action, 'language') && $shortcut ==  $this->cmtLanguage) {
				$activeTab = true;
			} else {
				$activeTab = false;
			}
			$tabsContent .= $this->gui->makeTab(array('vars' => array(
					'languageName' => $name,
					'languageShortcut' => $shortcut,
					'currentTab' => $tabCounter++,
					'cmtActiveTab' => $activeTab
			)));
		}
		
		if (stristr($this->action, 'language')) {
			$activeTab = true;
		} else {
			$activeTab = false;
		}
		
		$this->gui->setTemplate('tab', $this->templatesPath.'gui_tab_edit_language.tpl', false);
		$tabsContent .= $this->gui->makeTab(array('vars' => array(
				'currentTab' => $tabCounter,
				'cmtActiveTab' => $activeTab
		)));
		
		return $tabsContent;
	}
	

}

$controller = new ApplicationPages();
$replace =  $controller->work();

?>