<?php

namespace Contentomat;

Class TableDataLayoutController extends ApplicationController {

	protected $objectTemplate;
	protected $dataLayout;
	protected $tableId;
	protected $entryId;
	
	public function init() {

		$this->templatesPath = 'app_showtable/';
		
		$this->parser = new CMTParser();
		$this->dataLayout = new \Contentomat\TableDataLayout();
		
		// Get entry ID from app_showtable
		$this->entryId = is_array($_REQUEST['id']) ? intval($_REQUEST['id'][0]) : $_REQUEST['edited_id'];
		$this->tableId = intval(CMT_APPID);

		$this->parser->setParserVar('cmtEntryId', $this->entryId);
		$this->parser->setParserVar('cmtTableId', $this->tableId);

		// terrible workaround for actions "new" and "duplicate": stored template orders have to be "activated" 
		// because the $_POST data is no longer available when this methods are called.
		if ($_REQUEST['action_performed'] == 'new' || $_REQUEST['action_performed'] == 'duplicate') {
			$this->action = $_REQUEST['action_performed'] . 'Save';
		}
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

		// generate templates list
		$this->showDataLayoutContent();
	}

	/**
	 * protected function actionEdit()
	 * Action: edit existing table entry
	 *
	 * @param void
	 * @return void
	 */
	protected function actionEdit() {
		
		// generate templates list
		$this->showDataLayoutContent();
	}

	/**
	 * protected function actionEditSave()
	 * Action: called when existing table entry was edited.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionSaveDataLayout() {
		
		$this->tableId = intval($_REQUEST['cmtTableId']);
		$this->entryId = intval($_REQUEST['cmtEntryId']);
		
		$this->dataLayout->save($this->tableId, $this->entryId, $_REQUEST['cmtTemplatePositions']);
		exit();
	}

	/**
	 * protected function actionNewSave()
	 * Action: called when a new table entry is saved.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionNewSave() {
	
		$this->dataLayout->activate($_REQUEST['cmtApplicationID'], $_REQUEST['edited_id']);
		$this->dataLayout->cleanUpDataLayoutTable();
		
		$this->changeAction('default');
	}

	/**
	 * protected function protected function actionDuplicate()
	 * Action: called when an existing table entry is duplicated.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDuplicate() {
		
		// delete origin id in parser
		$this->parser->setParserVar('cmtEntryId', 0);
		
		// generate templates list
		$this->showDataLayoutContent();
	}
	
	/**
	 * protected function actionDuplicateSave()
	 * Action: called when a duplicated table entry is saved.
	 *
	 * @param void
	 * @return void
	 */	
	protected function actionDuplicateSave() {
		
		$this->dataLayout->activate($_REQUEST['cmtApplicationID'], $_REQUEST['edited_id']);
		$this->changeAction('default');
	}

	/**
	 * protected function actionDelete()
	 * Action: called when a table entry is deleted.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionDelete() {
		
		foreach ((array)$_REQUEST['id'] as $entryId) {
			$this->dataLayout->delete($_REQUEST['cmtApplicationID'], $entryId);
		}
	}
	
	protected function actionDeleteMultiple() {
	
		$this->changeAction('delete');
		return;
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

	}
	

	protected function showDataLayoutContent() {
		
// 		$list = $this->objectTemplate->get(array(
// 			'where' => array(
// 					"'" . CMT_APPID . "' IN(ot.cmt_table_ids)"
// 			)
// 		));
// 		$this->parser->setParserVar('templates', $list);

		// get stored templates
		$dataLayout = $this->dataLayout->getWithTemplates($this->tableId, $this->entryId);
		$templates = $this->dataLayout->getAvailableTemplates($this->tableId);
// var_dump($dataLayout);
// die();
		//$this->parser->setParserVar('cmtTemplatePositions', $dataLayout['cmt_template_positions']);
		$this->parser->setParserVar('cmtSelectedTemplates', $dataLayout['templates']);
		$this->parser->setParserVar('cmtTemplates', $templates);
		
		$this->showContent($this->parser->parseTemplate($this->templatesPath . 'cmt_table_datalayout_frame.tpl'));
		
	}
	
	protected function showContent($content) {
		$this->cmt->setVar('cmtDataLayoutContent', $content);
	}

}

$autoload = new PsrAutoloader();
$autoload->addNamespace('Contentomat\Layout', 'classes/app_layout');

$tablesMediaController = new TableDataLayoutController();
$tablesMediaController->work();
?>