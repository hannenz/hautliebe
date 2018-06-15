<?php
/*
 * TODO:
 * 
 * - altes Medium löschen, wenn ein neues hochgeladen wird (vielleicht neuer Status "98"?
 * - Was pasiert bei Aktion "saveAndShowNext"?
 * - alle Medien einrichten (Dokument, Link, etc)
 */

/**
 * app_showtable_media_controller.inc 
 * Application "MLog" to show and edit blog posts. 
 * 
 * @author J.Hahn <info@contentomat.de> 
 * @version 2015-09-22
 * @package app_showtable
 */
namespace Contentomat;

Class TableMediaController extends ApplicationController {
	
	protected $entryID = 0;
	protected $mediaID = 0;
	protected $tableID = 0;
	protected $mediaType;
	protected $mediaTypes;
	protected $form;
	protected $fieldHandler;
	protected $mediaTypeListContent = '';
	protected $tableMedia;
	protected $applicationSettings;
	
	public function init() {

		$this->templatesPath = 'app_showtable/';
		
		$this->tableMedia = new TableMedia();
		$this->mediaTypes = $this->tableMedia->getMediaTypes();
		$this->tableMedia->setUploadPathPrefix(PATHTOWEBROOT);
		$this->parser = new CMTParser();
		
		// Get entry ID from app_showtable
		$entryID = $_REQUEST['id'];
		if (is_array($entryID)) {
			$this->entryID = intval($entryID[0]);
		} else {
			$this->entryID = intval($entryID);
		}
		
		// or get entry ID from app_showtable_media
		if (!$this->entryID) {
			$this->entryID = intval($_REQUEST['cmtEntryID']);
		}
		
		// or get entry ID after editing from app_showtable
		if (!$this->entryID) {
			$this->entryID = intval($_REQUEST['edited_id']);
		}
		
		// or get entry ID from Content-o-mat (when a new entry was saved)
		if (!$this->entryID) {
			$this->entryID = $this->cmt->getVar('cmtLastInsertedID');
		}
		
		$this->mediaID = intval(trim($_REQUEST['cmtMediaID']));
		$this->mediaType = trim($_REQUEST['cmtMediaType']);
		
		$this->tableID = intval(trim($_REQUEST['cmtTableID']));
		if (!$this->tableID) {
			$this->tableID = CMT_APPID;
		}
		
		//  get action
		$this->action = trim($_REQUEST['cmtAction']);
		if (!$this->action) {
			$this->action = trim($_REQUEST['action']);
		}
		
// 		if (!$this->action) {
// 			$this->action = $this->cmt->getVar('cmtActionPerformed');
// 			$this->entryID = $this->cmt->getVar('cmtEditedEntryID');
// 		}
		
		if ($_REQUEST['action_performed']) {
			$this->action = $_REQUEST['action_performed'] . 'Save';
		}

		
		$this->parser->setMultipleParserVars(array(
			'cmtMediaType' => $this->mediaType,
			'cmtMediaID' => $this->mediaID,
			'cmtTableID' => $this->tableID,
			'cmtEntryID' => $this->entryID
		));
		
		// read upload directories from table settings
		$appHandler = new ApplicationHandler();
		
		$this->applicationSettings = $appHandler->getApplicationSettings($this->tableID);
		$this->tableMedia->setUploadPath('image', $this->applicationSettings['media_image_path']);
		$this->tableMedia->setUploadPath('document', $this->applicationSettings['media_document_path']);
		
	}	
	
	protected function actionDefault() {

	}

	/**
	 * protected function actionNew()
	 * Action: add new table entry
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionNew() {
		$this->parser->setParserVar('mediaTypeListContent', $this->createMediaList());
		$this->showContent($this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_frame.tpl'));
	}

	/**
	 * protected function actionEdit()
	 * Action: edit existing table entry
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEdit() {

		$this->parser->setParserVar('mediaTypeListContent', $this->createMediaList());
		$mediaContent = $this->showMedia(array(
			'entryID' => $this->entryID,
			'tableID' => $this->tableID
		));

		$this->parser->setParserVar('mediaContent', $mediaContent);
		$this->showContent($this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_frame.tpl'));
	}

	/**
	 * protected function actionEditSave()
	 * Action: called when existing table entry was edited.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEditSave() {
		$this->tableMedia->refreshEntriesMediaAfterSave(array('tableID' => $this->tableID, 'entryID' => $this->entryID));
	}

	/**
	 * protected function actionNewSave()
	 * Action: called when a new table entry is saved.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionNewSave() {

		$this->tableMedia->activateNewEntriesMediaAfterSave(array(
			'tableID' => $this->tableID, 
			'entryID' => $this->entryID,
			'newMediaIndicator' => $this->createIntermediateID()
		));
	}

	/**
	 * protected function protected function actionDuplicate()
	 * Action: called when an existing table entry is duplicated.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDuplicate() {
		
		$mediaData = $this->tableMedia->getMedia(array(
			'tableID' => $this->tableID,
			'entryID' => $this->entryID
		));
		
		foreach($mediaData as $media) {
			$intermediateID = $this->createIntermediateID();
			$check = $this->tableMedia->duplicateMedia(array(
				'mediaID' => $media['id'], 
				'entryID' => $intermediateID
			));
		}

		// show duplicated media
		$newMediaData = $this->tableMedia->getMedia(array(
			'tableID' => $this->tableID,
			'entryID' => $intermediateID,
			'mediaStatus' => 'all'
		));
	
		$mediaContent = '';
		foreach ($newMediaData as $media) {
			
			$mediaContent .= $this->showMedia(array('mediaID' => $media['id'], 'mediaStatus' => 'all'));
		}
		
		$this->parser->setParserVar('mediaContent', $mediaContent);
		$this->parser->setParserVar('mediaTypeListContent', $this->createMediaList());
		
		$this->showContent($this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_frame.tpl'));
	}
	
	/**
	 * protected function actionDuplicateSave()
	 * Action: called when a duplicated table entry is saved.
	 *
	 * @param void
	 * @return void
	 */	
	protected function actionDuplicateSave() {

		$check = $this->tableMedia->activateNewEntriesMediaAfterSave(array(
			'tableID' => $this->tableID,
			'entryID' => $this->entryID,
			'newMediaIndicator' => $this->createIntermediateID()
		));	
	}

	/**
	 * protected function actionDelete()
	 * Action: called when a table entry is deleted.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDelete() {
		
		// mark media entries as deleted...
		$check = $this->tableMedia->deleteEntriesMedia(array(
			'tableID' => $this->tableID, 
			'entryID' => $this->entryID
		));
		
		// ... and remove them immediately
		$check = $this->tableMedia->removeEntriesMedia(array(
			'tableID' => $this->tableID,
			'entryID' => $this->entryID
		));
	}

	/**
	 * protected function actionAbortEdit()
	 * Action: called when editing a table entry is aborted.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionAbortEdit() {
		$this->actionAbort();
	}

	/**
	 * protected function actionAbortDuplicate()
	 * Action: called when duplicating a table entry is aborted.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionAbortDuplicate() {
		$this->actionAbort();
	}
	/**
	 * protected function actionAbort()
	 * Called when user aborts entry editing. Does some cleanup actions.
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionAbort() {
		$check = $this->tableMedia->refreshEntriesMediaAfterAbort(array(
			'tableID' => $this->tableID, 
			'entryID' => $this->entryID
		));
// 		$this->isAjax = true;
// 		$this->isJson = true;
	}
	
	/**
	 * protected function actionDeleteMedia()
	 * Called when a media entry should be deleted. Media entry is marked as "to delete" but not deleted yet.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDeleteMedia() {
		
		$this->isAjax = true;
		$this->isJson = true;
		
		$check = $this->tableMedia->deleteMedia($this->mediaID);
		
		$response = array(
			'mediaDeleted' => $check,
			'cmtMediaID' => $this->mediaID
		);
		
		$this->content = $response;
		
	}

	/**
	 * protected function actionNewMedia()
	 * Called when a new media entry ist created.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionNewMedia() {
		
		$this->parser->setParserVar('cmtMediaType', $this->mediaType);
		$this->parser->setParserVar('cmtMediaID', '');
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_edit.tpl');
		$this->isAjax = true;
	}

	/**
	 * protected function actionEditMedia()
	 * Displays the editing form for the selected media.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEditMedia() {
		
		$mediaData = $this->tableMedia->getMediaByID($this->mediaID);

		$this->parser->setMultipleParserVars($mediaData);
		$this->parser->setParserVar('mediaID', $this->mediaID);
		$this->parser->setParserVar('mediaType', $this->mediaType);
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_edit.tpl');
		$this->isAjax = true;	
	}

	/**
	 * protected function actionSaveMedia()
	 * Saves a new media or edited media data.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionSaveMedia($params=array()) {

		$response = array();
		$this->isJson = true;
		$this->isAjax = true;

		$saveMediaParams = array(
			'mediaID' => $this->mediaID,		// mandatory for UPDATE, optional for INSERT
			'tableID' => $this->tableID,		// mandatory
			'entryID' => $this->entryID,		// mandatory
			'mediaType' => $this->mediaType,	// mandatory
			'mediaData' => $_REQUEST,
			'mediaStatus' => 0,
			'imageWidth' => $this->applicationSettings['media_image_width'],			// TODO: implement
			'imageHeight' => $this->applicationSettings['media_image_height'],			// TODO: implement
			'thumbnailWidth' => $this->applicationSettings['media_thumbnail_width'],
			'thumbnailHeight' => $this->applicationSettings['media_thumbnail_height']
		);
		
		// Media is edited: get the status and the entry's id from the media dataset
		$mediaData = $this->tableMedia->getMediaByID($this->mediaID);
		
		if (!empty($mediaData)) {
			$saveMediaParams['mediaStatus'] = intval($mediaData['media_status']);
			$saveMediaParams['entryID'] = intval($mediaData['media_entry_id']);
			$saveMediaParams = array_merge($saveMediaParams, $params);
		}	
		
		// is it a new media for a new entry?
		if (!$this->entryID) {
			$this->entryID = $this->createIntermediateID();
			$saveMediaParams['entryID'] = $this->entryID; 
			$saveMediaParams['mediaStatus'] = 0;
		}
		
		$mediaID = $this->tableMedia->saveMedia($saveMediaParams);
		
		// media saved successfully?
		if (!$mediaID) {
			
			$this->parser->setParserVar('error', 'saveError');
			
			$response['saveError'] = true;
			$response['errorMessage'] = $this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_error_messages.tpl');
			$response['mediaContent'] = '';
		} else {
			$this->parser->setParserVar('mediaImagePath', $this->tableMedia->getImagePath());

			$response['saveError'] = false;
			$response['successMessage'] = $this->parser->parseTemplate($this->templatesPath . 'cmt_table_media_success_messages.tpl');
			$response['mediaContent'] = base64_encode($this->showMedia(array('mediaID' => $mediaID, 'mediaStatus' => 'all')));
		}
		
		// is media new?
		if (!$this->mediaID) {
			$response['mediaIsNew'] = true;
		}
		
		// pass more variables
		$response['mediaID'] = $mediaID;
		$this->content = $response;
// 		$h = fopen('../bug.txt', 'w');
// 		fwrite($h, $response);
// 		fclose($h);
// $d = new Debug();
// $d->debug($response);
		//$this->content = array();

	}
	
	protected function actionSaveMediaPositions() {
		
		$this->isAjax = true;
		$this->isJson = true;
		
		$mediaIDs = explode(',', $_REQUEST['cmtMediaPositions']);
		$check = $this->tableMedia->saveMediaPositions($mediaIDs);
		
		$this->content = array('mediaPositionsSaved' => $check, 'ids' => $mediaIDs); 
	}
	
	public function showMedia($params = array()) {

		$mediaData = $this->tableMedia->getMedia($params);

		$this->parser->setParserVar('mediaImagePath', $this->tableMedia->getImagePath());
		$this->parser->setParserVar('mediaDocumentPath', $this->tableMedia->getDocumentPath());
		$this->parser->setparserVar('mediaType', $this->mediaType);

		$tpl = $this->parser->getTemplate($this->templatesPath . 'cmt_table_media.tpl');
		$mediaContent = '';
		
		foreach ($mediaData as $data) {

			// file type
			$fileName = '';
			switch($data['media_type']) {
					
				case 'image':
					$fileName = $data['media_image_file'];
					break;
			
				case 'document':
					$fileName = $data['media_document_file'];
					break;
			}

			$fileInfo = pathinfo($fileName);
			$this->parser->setParserVar('mediaFileExtension', $fileInfo['extension']);

			$this->parser->setMultipleParserVars($data);
			$mediaContent .= $this->parser->parse($tpl);
		}

		return $mediaContent;
	} 
	
	protected function createMediaList() {
		
		if ($this->mediaTypeListContent) {
			return $this->mediaTypeListContent;
		}
		
		$form = new Form();
		$fieldHandler = new FieldHandler();
		
		$fieldData = $fieldHandler->getField(array(
			'tableName' => $this->tableMedia->getMediaTable(),
			'fieldName' => 'media_type'
		));

		$this->mediaTypeListContent = $form->select(array(
			'values' => explode("\n", $fieldData['cmt_option_select_values']),
			'aliases' => explode("\n", $fieldData['cmt_option_select_aliases']),
			'optionsOnly' => true
		));

		return $this->mediaTypeListContent;

	}
	
	public function createIntermediateID() {
		return 0 - intval(CMT_USERID);
	}
	
	protected function showContent($content) {
		$this->cmt->setVar('cmtMediaContent', $content);
	}

}
$tablesMediaController = new TableMediaController();
$tablesMediaController->work();
?>