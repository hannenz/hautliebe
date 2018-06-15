<?php
/**
 * app_paperboy.inc
 * Application for sending bulk email newsletters.
 * 
 * @author J.Hahn
 * @version 2016-09-21
 *
 */

namespace Contentomat\Paperboy;

use Contentomat\ApplicationController;
//use Contentomat\Parser;
//use Contentomat\User;
use Contentomat\Form;
use Contentomat\PsrAutoloader;
use Contentomat\dataformat;
use Contentomat\Debug;
//use Contentomat\Mimemailer;
use Contentomat\CmtPage;
use Contentomat\EvalCode;
use Contentomat\FileHandler;

class PaperboyController extends ApplicationController {

	protected $form;
	protected $dataformat;
	protected $paperboy;
	protected $changeAction;
	protected $applicationSettings;
	protected $pageLanguage;
	protected $attachedFiles;
	protected $attachmentsNr;
	protected $fileHandler;
	protected $cmtPage;
	protected $autoloader;

	/**
	 * public function init()
	 * Do all initialization here ("second constructor" of controller).
	 *
	 * @param void
	 * @return void
	 * @see \Contentomat\ApplicationController::init()
	 */
	public function init() {
		
		$this->fileHandler = new FileHandler();
		
		$this->autoloader = new PsrAutoloader();
		$this->autoloader->addNamespace('\Contentomat\Paperboy', 'classes/app_paperboy');
		//$autoloader->addNamespace('\PHPMailer', 'classes/PHPMailer');
		
		$this->templatesPath = CMT_TEMPLATE . '/app_paperboy/';
		$this->pageLanguage = 'de';	// TODO: get from the settings!
		
		$this->applicationSettings = $this->cmt->getVar('applicationSettings');

		$vars = array(
			'newsletterHTML' => '',
			'newsletterText' => '',
			'newsletterName' => '',
			'newsletterSubject' => '',
			'newsletterID' => 0,
			'newsletterPageID' => 0,
			'newsletterSenderEmail' => '',
			'newsletterSenderName' => '',
			'newsletterTo' => '',
			'newsletterCC' => '',
			'newsletterBCC' => '',
			'newsletterTemplateID' => '',
			'newsletterAttachments[]' => array(),
			'newsletterTestRecipients[]' => array(),
			'newsletterDeliveryCurrentPos' => 0,
			'resumeDelivery' => 0				// Value 1 is send by Javascript Ajax call when a user resumes the delivery after a server errror
		);
		
		$formVars = array (
			'newsletterHTML',
			'newsletterText',
			'newsletterSubject',
			'newsletterName',
			'newsletterID',
			'newsletterPageID',
			'newsletterSenderEmail',
			'newsletterSenderName',
			'newsletterTo',
			'newsletterCC',
			'newsletterBCC',
			'newsletterTemplateID',
			'newsletterAttachments'
//			'newsletterDeliveryCurrentPos'
		);

		$this->handleContentomatVars(array (
			'vars' => $vars,
			'scriptVars' => $formVars	// stupid Content-o-mat convention: 'scriptVars' are real session vaables, 'sessionVars' are stored depending of the tab nr of the parent application!
		));

		$this->newsletterHTML = $this->parser->protectMacros($this->newsletterHTML);
		$this->newsletterText = $this->parser->protectMacros($this->newsletterText);
		
		// pass all vars to the parser
		foreach (array_keys($vars) as $var) {
			if (isset($this->$var)) {
				$this->parser->setParserVar($var, $this->$var);
			}
		}

		// pass other var to parser
		$this->parser->setParserVar('pageLanguage', $this->pageLanguage);
		
		// handle attachments
		$this->handleAttachments();
		
//		$this->debug->log($this->newsletterAttachments);

		
		// TODO: "paperoyAction"???
		$this->parser->setParserVar('paperboyAction', $this->action);
		$this->parser->setParserVar('cmtTab', $this->tabNr);

		$this->parser->setParserVar('newsletterID', $this->newsletterID);
		
		// Anzahl der bereits versendeten Newsletter im Intervall, enspricht 'pos'
//		$actNewsletterInterval = intval(trim($_POST['pos']));
		
		$this->form = new Form();
		$this->dataformat = new Dataformat();
		
		$this->paperboy = new Paperboy();
		$this->paperboy->setMailerConfiguration($this->applicationSettings);
		
		$this->changeAction = $_REQUEST['cmtChangeAction'];
	
	
	}
	
	/**
	 * public function actionDefault()
	 * Default action: shows the newsletter creation form
	 * 
	 * @params void
	 * @return void
	 * @see \Contentomat\ApplicationController::actionDefault()
	 */
	public function actionDefault() {
		
		// create available templates list
		$templateList = $this->paperboy->getTemplates();
 		
 		$templateSelect = $this->form->select(array(
 			'values' => array_keys($templateList),
 			'aliases' => array_values($templateList),
 			'selected' => $this->newsletterTemplateID,
 			'optionsOnly' =>true
 		));
 		$this->parser->setParserVar('templateSelect', $templateSelect);
 		
 		// create recipients list select
 		$recipientLists = $this->paperboy->getRecipientLists();
		
		$newsletterSelect = $this->form->select(array(
			'values' => array_keys($recipientLists),
			'aliases' => array_values($recipientLists),
			'selected' => $this->newsletterID,
			'optionsOnly' =>true
		));
		$this->parser->setParserVar('newsletterSelect', $newsletterSelect);

		// create "to", "cc", "bcc" recipient selects
		$newsletterListSelect = $this->form->select(array(
			'values' => array_keys($recipientLists),
			'aliases' => array_values($recipientLists),
			'optionsOnly' =>true
		));
		$this->parser->setParserVar('newsletterListSelect', $newsletterListSelect);
		
		// create page list
		$this->parser->setParserVar('pageSelect', $this->makePageList());
		
		// create attachments panel content
		$this->attachmentsNr = count($this->newsletterAttachments);
		$this->parser->setParserVar('attachmentsNr', $this->attachmentsNr);
		$tplAttachment = $this->parser->getTemplate($this->templatesPath . 'cmt_paperboy_attach_file.tpl');
		$tplAttachedFile = $this->parser->getTemplate($this->templatesPath . 'cmt_paperboy_attached_file.tpl');
		$contentAttachments = '';
		
		$maxAttachments = $this->applicationSettings['maxAttachments'];
		$usedAttachmentsNr = count($this->newsletterAttachments);
		$unusedAttachmentsNr = $maxAttachments - $usedAttachmentsNr;
		$attachmentNr = 1;

		for ($attachmentNr = 1; $attachmentNr <= $maxAttachments; $attachmentNr++) {

			$this->parser->setParservar('attachmentNr', $attachmentNr);
			
			if (isset($this->newsletterAttachments[$attachmentNr])) {
				$this->parser->setParservar('attachedFileName', $this->newsletterAttachments[$attachmentNr]['name']);
				
				$fileSizeParams =  $this->fileHandler->formatFileSize($this->newsletterAttachments[$attachmentNr]['size']);
				$this->parser->setMultipleParserVars($fileSizeParams);
				
				$contentAttachments .= $this->parser->parse($tplAttachedFile);
			} else {
				$this->parser->deleteParservar('attachedFilename');
				$contentAttachments .= $this->parser->parse($tplAttachment);
			}
		}
		
		$this->parser->setParserVar('contentAttachments', $contentAttachments);

		// show page
		$this->showPage($this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_editmail.tpl'));
	}
	
	/**
	 * protected function actionSaveEditNewsletter()
	 * Saves the newsletter edit form. Is called in tab 1 when an other tab is selected.
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionSaveEditNewsletter() {

		if ($this->changeAction) {
			$this->changeAction($this->changeAction, true);
		} else {
			$this->changeAction('', true);
		}
	}
	
	/**
	 * protected function actionPreviewNewsletter()
	 * Main function to display the newsletter preview.
	 * 
	 * @param void;
	 * @return void;
	 *
	 */
	protected function actionPreviewNewsletter() {
		
		$this->parser->setParserVar('newsletterHtml', html_entity_decode($this->newsletterHTML));
		$this->parser->setParserVar('newsletterText', nl2br($this->newsletterText));
		
		$this->showPage($this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_preview.tpl'));
		
	}

	/**
	 * protected function actionPreviewNewsletterHTML()
	 * Helper: Displays only the HTML version of a newsletter. Is called in an iframe.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionPreviewNewsletterHTML() {

		$this->isAjax = true;
		$this->isJson = false;
		
		//$this->content = $this->newsletterHTML;
		$this->content = $this->paperboy->addHTMLBody($this->newsletterHTML, $this->newsletterTemplateID);
//		$this->content = $this->parser->parse($this->paperboy->addHTMLBody($this->newsletterHTML, $this->newsletterTemplateID));
		return;
		
// 		$frameTemplate = $this->paperboy->getHTMLTemplate($this->newsletterTemplateID);	
// 		$this->parser->setParserVar('content', $this->newsletterHTML);
		
// 		$this->isAjax = true;
// 		$this->isJson = false;
		
// 		$this->content = $this->parser->parse($frameTemplate);
// 		return;
	}
	
	/**
	 * protected function actionTestNewsletter()
	 * Shows the test recipient selection page.
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionTestNewsletter() {

		$recipients = $this->paperboy->getTestRecipients();
		$tplRow = $this->parser->getTemplate($this->templatesPath . 'cmt_paperboy_test_recipient_row.tpl');
		$recipientsContent = '';
		
		foreach ($recipients as $key => $recipient) {
			$this->parser->setParserVar('counter', $key);
			$this->parser->setParserVar('recipient', $recipient);
			$recipientsContent .= $this->parser->parse($tplRow);
		}
		$this->parser->setParserVar('recipientsContent', $recipientsContent);
		
		$this->showPage($this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_test_overview.tpl'));

	}
	
	/**
	 * protected function actionSendTestNewsletter()
	 * Does the test newsletter delivering and show the results after sending.
	 * 
	 * @param void
	 * @return void
	 */
	protected function actionSendTestNewsletter() {

		$addParams = '';
		if ($this->applicationSettings['knownUserUseOption']) {
			$addParams = '-f ' . $this->applicationSettings['knownUserEmail'];
		}
		
		$remoteImagePath = $this->applicationSettings['remoteImagePath'];
		if (boolval($this->applicationSettings['attachImages']) && !$remoteImagePath) {
			$remoteImagePath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
		}
		
		$newsletterSent = $this->paperboy->sendMail(array(
			'newsletterID' => $this->newsletterID,
			'newsletterTemplateID' => $this->newsletterTemplateID,
			'newsletterSenderName' => $this->newsletterSenderName,
			'newsletterSenderEmail' => $this->newsletterSenderEmail,
			'newsletterSubject' => $this->newsletterSubject,
			'newsletterHTML' => $this->parser->unprotectMacros($this->newsletterHTML),
			'newsletterText' => $this->parser->unprotectMacros($this->newsletterText),
			'newsletterRecipients' => $this->paperboy->createRecipientsArray($this->newsletterTestRecipients),
			'deliveryAddParams' => $addParams,
			'newsletterAttachments' => $this->prepareAttachmentsArray($this->newsletterAttachments),
			'embedHTMLImages' => boolval($this->applicationSettings['embedHTMLImages']),
			'remoteImagePath' => $remoteImagePath
		));

		$failedRecipients = count($this->newsletterTestRecipients) - $newsletterSent;
		$succeededRecipients = $newsletterSent;

		$this->parser->setParserVar('failedRecipients', $failedRecipients);
		$this->parser->setParserVar('succeededRecipients', $succeededRecipients);

		$this->showPage($this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_test_send.tpl'));
	}

	protected function actionLoadTemplate() {

		$this->isAjax = true;
		$this->isJson = true;
		
		$r = $this->paperboy->getTemplate($this->newsletterTemplateID);

		$this->content = array(
			'newsletterID' => intval($r['template_linkwithnewsletter']),
			'newsletterSenderName' => $r['template_sendername'],
			'newsletterSenderEmail' => $r['template_sendermail'],
			'newsletterSubject' => $r['template_subject'],
			'newsletterHTML' => $r['template_html'],
			'newsletterText' =>$r['template_text']
		);

	}
	
	protected function actionLoadPage() {
		
		$this->isAjax = true;
		$this->isJson = true;

 		$newsletterHTML = '';
 		$newsletterText = '';
 		
 		// handle include file if exists.
 		// include file: pass the variables "newsletterHTML" and "newsletterText" to the Contentomat Singleton to use them as newsletter content
 		if ($this->applicationSettings['includeFile']) {
			
 			$this->cmt->setVars(array(
				'newsletterHTML' => '',
				'newsletterText' => '',
 				'pageId' => $this->newsletterPageID,
 				'pageLanguage' => $this->pageLanguage
			));

  			$codeEvaler = new EvalCode();
 			$check = $codeEvaler->evalFile(
 				PATHTOWEBROOT. $this->applicationSettings['includeFile'], 
 				array(
	 				'pageId' => $this->newsletterPageID,
	 				'pageLanguage' => $this->pageLanguage
 				)
 			);
 			
			$newsletterHTML = $this->cmt->getVar('newsletterHTML');
			$newsletterText = $this->cmt->getVar('newsletterText');
			
 		} else {
 			
 			// ugly but we need this for included scripts
 			$this->autoloader->addNamespace('Contentomat', INCLUDEPATH . 'phpincludes/classes');
 			
 			// ... or read the page content and pass it to Paperboy
 			$page = new CmtPage();
 			$page->setPageLanguage($this->pageLanguage);
 			$page->setPageID($this->newsletterPageID);
 			$page->setPagesTable($this->pageLanguage);
 			$page->setContentsTable($this->pageLanguage);
 			$page->setLinksTable($this->pageLanguage);
 			
 			$this->parser->setPageId($this->newsletterPageID);
			$this->parser->setParentId($page->getParentId($this->newsletterPageID, $this->pageLanguage));
			$this->parser->setPageLanguage($this->pageLanguage);
			$this->parser->setPagesTable($page->getPageTable());
			$this->parser->setLinksTable($page->getLinkTable());
			$this->parser->setContentsTable($page->getContentTable());
			$this->parser->setPathToWebroot(PATHTOWEBROOT);
 			
			$pageTemplate = $page->getPageTemplate($this->newsletterPageID, $this->pageLanguage);
 			$pageContent = $this->parser->parse($pageTemplate);
 			
	 		preg_match('/\<body[^>]*\>(.*)\<\/\s?body\>/is', $pageContent, $match);
	 		$newsletterHTML = $match[1];
	 		$newsletterText = strip_tags($newsletterHTML);
 			
 		}
		
		$this->content = array(
			'newsletterHTML' => $newsletterHTML,
			'newsletterText' => $newsletterText
		);
		
	}
	
	protected function actionStartNewsletterDelivery() {
		
		$this->resetDeliveryVars();
		
		$this->parser->setParserVar('maxExecutionTime', ini_get('max_execution_time'));
		
		$newsletterData = $this->paperboy->getNewsletter($this->newsletterID);
		$this->parser->setMultipleParserVars($newsletterData);

		$recipients = array();
		
		if (!empty($this->newsletterTo)) {
			$recipients['to'] = count($this->paperboy->createRecipientsArray($this->newsletterTo));
			$recipients['cc'] = count($this->paperboy->createRecipientsArray($this->newsletterCC));
			$recipients['bcc'] = count($this->paperboy->createRecipientsArray($this->newsletterBCC));
		}
		$this->parser->setMultipleParserVars($recipients);
		
		if (!empty($this->newsletterTo)) {
			$totalNewsletters = array_sum($recipients);
			$deliveryInterval = 1;
		} else {
			$totalNewsletters = $this->paperboy->getTotalSubscribers($this->newsletterID);
			$deliveryInterval =  intval($this->applicationSettings['sendInSteps']);
		}
		$this->parser->setParserVar('totalNewsletters', $totalNewsletters);
		$this->parser->setParserVar('deliveryInterval', $deliveryInterval);

		// save number of total recipients
		$this->session->setSessionVar('totalNewsletters', $totalNewsletters);
		$this->session->saveSessionVars();
		
		$this->showPage($this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_send.tpl'));
	}
	
	protected function actionSendNewsletter() {
		
		$this->isAjax = true;
		$this->isJson = true;
		
		// send to recipient list or to an individual recipient
		if (!empty($this->newsletterTo)) {

			$deliveryInterval =  1;
			$totalRecipients = 1;
		} else {
			$deliveryInterval =  intval($this->applicationSettings['sendInSteps']);
			$totalRecipients = $this->paperboy->getTotalSubscribers($this->newsletterID);
		}
		
		$deliveryErrors = intval($this->session->getSessionVar('newsletterDeliveryErrors'));
		$deliverySuccessful = intval($this->session->getSessionVar('newsletterDeliverySuccessful'));
		
		// Jump to next delivery interval when a delivery is resumed after a server error
		if ($this->resumeDelivery) {
			$this->newsletterDeliveryCurrentPos += $deliveryInterval;
		}
		
		if ($this->applicationSettings['knownUserUseOption']) {
			$addParams = '-f ' . $this->applicationSettings['knownUserEmail'];
		} else {
			$addParams = '';
		}

		// set remote image path if images are not included in email
		$remoteImagePath = $this->applicationSettings['remoteImagePath'];
		if (boolval($this->applicationSettings['attachImages']) && !$remoteImagePath) {
			$remoteImagePath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
		}
		
		$newsletterSent = $this->paperboy->sendMail(array(
			'newsletterID' => $this->newsletterID,
			'newsletterTemplateID' => $this->newsletterTemplateID,
			'newsletterSenderName' => $this->newsletterSenderName,
			'newsletterSenderEmail' => $this->newsletterSenderEmail,
			'newsletterSubject' => $this->newsletterSubject,
			'newsletterHTML' => $this->parser->unprotectMacros($this->newsletterHTML),
			'newsletterText' => $this->parser->unprotectMacros($this->newsletterText),
			'newsletterAttachments' => $this->prepareAttachmentsArray($this->newsletterAttachments),
			'deliveryInterval' => $deliveryInterval,
			'deliveryCurrentPos' => $this->newsletterDeliveryCurrentPos,
			'deliveryAddParams' => $addParams,
			'newsletterRecipients' => $this->paperboy->getNewsletterRecipients($this->newsletterID, $this->newsletterDeliveryCurrentPos, $deliveryInterval),
			'newsletterTo' => $this->newsletterTo,
			'newsletterCC' => $this->newsletterCC,
			'newsletterBCC' => $this->newsletterBCC,
			'embedHTMLImages' => boolval($this->applicationSettings['embedHTMLImages']),
			'remoteImagePath' => $remoteImagePath
		));

		$deliveryErrors += $this->paperboy->getDeliveryErrors();
		$deliverySuccessful += $newsletterSent;
		
		// Das nächste Adressintervall durchgehen oder Versand beenden

		
		// prepare next step
		$this->newsletterDeliveryCurrentPos += intval($deliveryInterval);

		if ($totalRecipients) {
			$currentPercent = round(intval($this->newsletterDeliveryCurrentPos) / $totalRecipients * 100);
		} else {
			$currentPercent = 0;
		}		

		// all newsletters sent?
		if ($this->newsletterDeliveryCurrentPos < $totalRecipients) {
		
			$currentPosition = $this->newsletterDeliveryCurrentPos;

			$this->content = array(
				'proceedDelivery' => true,
				'deliveryComplete' => false,
				'currentPercent' => $currentPercent,
				'currentPosition' => $currentPosition,
				'nextPosition' => intval($this->newsletterDeliveryCurrentPos),
				'deliveryErrors' => $deliveryErrors,
				'deliverySuccessful' => $deliverySuccessful,
				'deliveryInterval' => $deliveryInterval,
				//'totalNewsletters' => $totalRecipients
				'totalNewsletters' => $this->session->getSessionVar('totalNewsletters')
			);

			$this->session->setSessionVar('newsletterDeliveryErrors', $deliveryErrors);
			$this->session->setSessionVar('newsletterDeliverySuccessful', $deliverySuccessful);
			$this->session->saveSessionVars();
			
		} else {
			$this->content = array(
				'proceedDelivery' => false,
				'deliveryComplete' => true,
				'currentPercent' => 100,
				'currentPosition' => intval($this->newsletterDeliveryCurrentPos),
				'deliveryErrors' => $deliveryErrors,
				'deliverySuccessful' => $deliverySuccessful,
				'deliveryInterval' => $deliveryInterval,
				//'totalNewsletters' => $totalRecipients
				'totalNewsletters' => $this->session->getSessionVar('totalNewsletters')
			);
			
			$this->resetDeliveryVars();
		}
	}
	
	/**
	 * protected function actionDeleteAttachment()
	 * Deletes an attachment.
	 * 
	 * @params void();
	 * @return void();
	 *
	 */
	protected function actionDeleteAttachment() {
		
		$fileNr = intval($_REQUEST['attachmentNr']);
		
		$this->fileHandler->deleteFile(array('file' => $this->newsletterAttachments[$fileNr]['path']));
		unset($this->newsletterAttachments[$fileNr]);
		
		$this->session->setSessionVar('newsletterAttachments', $this->newsletterAttachments);
		$this->session->saveSessionVars();
		
		$this->parser->setParserVar('successMessage', true);
		$this->parser->setParserVar('attachmentDeleted', true);
		
		$this->changeAction('default');
	}
	
	protected function actionLoadRecipientList() {
		
		$this->isAjax = true;
		$this->isJson = true;
		
		$list = $this->paperboy->getNewsletterRecipients($this->newsletterID);

		$this->content = array('list' => implode("\r\n", $list));
	}
	
	
	protected function uploadAttachments() {
		
		$this->changeAction('default');
	}
	
	protected function resetDeliveryVars() {
		$this->session->deleteSessionVar('newsletterDeliveryErrors');
		$this->session->deleteSessionVar('newsletterDeliverySuccessful');
		$this->session->deleteSessionVar('newsletterDeliveryCurrentPos');
		$this->session->deleteSessionVar('totalNewsletters');
		$this->session->saveSessionVars();
	}
	
	/**
	 * protected function makePageList()
	 * Creates a select field for pages
	 *
	 * @param void
	 * @return string Returns the <options /> part of a select field.
	 *
	 */
	protected function makePageList() {

		// create page list ... a little bit ugly...
		$pageSelect = $this->dataformat->format('cmt_pages_' . $this->pageLanguage, 'cmt_parentid', $this->newsletterPageID, 'edit');
		
		$pageSelect = preg_replace(array(
			'/\<select[^>]+\>/',
			'/\<\/select\>/'
		), '', $pageSelect);
		
		return $pageSelect;
	}
	
	/**
	 * function showPage()
	 * Parses the application content into the frame template and writes it into the $content variable of the application class.
	 * 
	 * @param string $pageContent
	 * @return void
	 */
	protected function showPage($pageContent='') {
	
		$this->parser->setParserVar('cmtTab', $this->cmt->getVar('cmtTab'));
		$this->parser->setParserVar('pageContent', $pageContent);
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy.tpl');
	}	

	protected function handleAttachments() {
		
		if (!is_array($this->newsletterAttachments)) {
			$this->newsletterAttachments = array();
		}

		if (!is_array($_FILES['newsletterAttachmentFile'])) {
			return;
		}

		$attachmentBasePath = $this->cmt->formatPath(PATHTOWEBROOT . '/' . $this->applicationSettings['uploadDirectory']) . '/';
		$errors = 0;
		$errorName = '';

		for ($fileNr = 1; $fileNr <= intval($this->applicationSettings['maxAttachments']); $fileNr++) {

			if ($_FILES['newsletterAttachmentFile']['name'][$fileNr]) {
				
				$fileNameParts = pathinfo($_FILES['newsletterAttachmentFile']['name'][$fileNr]);
				
				$fileName = $this->cmt->makeNameWebsave($fileNameParts['filename']) . '.' . $fileNameParts['extension'];
				
				if (!$this->checkFileIsAttachment($attachmentBasePath . $fileName)) {
				
					$this->newsletterAttachments[$fileNr] = array(
						'name' => $_FILES['newsletterAttachmentFile']['name'][$fileNr],
						'tmp_name' => $_FILES['newsletterAttachmentFile']['tmp_name'][$fileNr],
						'size' => $_FILES['newsletterAttachmentFile']['size'][$fileNr],
						'path' => $attachmentBasePath . $fileName
					);
					
					// copy file to paperboy's temp path
					$check = $this->fileHandler->copyFile(array(
						'sourceFile' => $this->newsletterAttachments[$fileNr]['tmp_name'],
						'targetFile' => $this->newsletterAttachments[$fileNr]['path']
					));
					
					$errors += intval(!$check);
				} else {
					
					// Error: file already attached
					//$this->parser->setParservar('errorMessage', true);
					//$this->parser->setParservar('duplicateAttachment', true);
					$errorName = 'duplicateAttachment';
					$errors += 1;
				}
				
			}
		}

		// check errors and display messages:
		// Error: empty upload form
		if (!array_filter($_FILES['newsletterAttachmentFile']['name'])) {
//			$this->parser->setParservar('errorMessage', true);
//			$this->parser->setParservar('noAttachments', true);
			$errorName = 'noAttachments';
			$errors += 1;
		} 
		
		if ($errors) {
			$this->parser->setParservar('errorMessage', true);
			if ($errorName) {
				$this->parser->setParservar($errorName, true);
			} else {
				$this->parser->setParservar('uploadError', true);
			}
		} else {
			$this->parser->setParservar('successMessage', true);
			$this->parser->setParservar('uploadSuccess', true);
		}
		
		$this->session->setSessionVar('newsletterAttachments', $this->newsletterAttachments);
		$this->session->saveSessionVars();
	}
	
	protected function prepareAttachmentsArray($attachmentsArray) {
		
		$a = array();
		if (!is_array($attachmentsArray)) {
			return $a;
		}
		
		foreach($attachmentsArray as $attachment) {
			$a[] = $attachment['path'];
		}
		
		return $a;
	}
	
	protected function checkFileIsAttachment($path) {
		
		foreach ($this->newsletterAttachments as $attachment) {
			
			if ($path == $attachment['path']) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * addHtmlBody
	 * Erzeugt aus dem Inhalt des TinyMCE-HTML-Editors und des Seitenrahmens eine fertige HTML-Seite
	 *
	 * @param $template Number ID der E-Mailvorlage
	 * @param $htmlBody String HTML-Quelltext (body)
	 * @param $htmlTitle String Titel der Seite (optional)
	 * @return String Kompletter HTML-Seitenquelltext
	 */
	
	protected function addHtmlBody($template, $htmlBody, $htmlTitle='') {
		global $db;
		if (!empty ($template))
		{
			$db->Query("SELECT template_html_doc FROM paperboy_templates WHERE id='$template'");
			$r = $db->Get();
			$htmlFrame = stripslashes(trim($r['template_html_doc']));
				
			if ($htmlFrame) {
				$htmlFrame = str_replace('{HTML_DOCUMENT_TITLE}', $htmlTitle, $htmlFrame);
				return str_replace('{HTML_DOCUMENT_BODY}', $htmlBody, $htmlFrame);
			} else {
				return '<html><head><title>'.$htmlTitle.'</title></head><body>'.$htmlBody.'</body></html>';
			}
		}
		else
		{
			return '<html><head><title>'.$htmlTitle.'</title></head><body>'.$htmlBody.'</body></html>';
		}
	}
	

	/**
	 * function storeNewsletterInArchive()
	 * Speichert die Newsletterdaten im Newsletter-Archiv. Anhand eines Versendestatus können
	 * fehlerhafte Sendedurchläufe gekennzeichnet werden.
	 *
	 * @param $nid integer Newsletter-ID
	 * @param $status integer Sendestatus: 	0 -> alle Mails korrekt versendet,
	 * 	Wert -> ID des Datensatzes in paperboy_distributed, bei welchem der Fehler aufgetreten ist
	 * @return void
	 */
	protected function storeNewsletterInArchive ($nid, $status) {
	
		// benötige Variablen in die Funktion holen
		global $db, $newsletterSenderName, $newsletterSubject, $newsletterHtml, $newsletterText, $attachedFiles, $reSend, $splitWord;
	
		$query = 	"SELECT newsletter_name
					FROM paperboy_newsletters
					WHERE id = '".$nid."'";
		$db->Query($query);
		$r = $db->Get(MYSQLI_ASSOC);
		$newsletterName = $r['newsletter_name'];
		$datetime = date('Y.m.d H:i:s');
		$query = 	"INSERT INTO paperboy_archived(newsletter_archived_id, newsletter_archived_name, newsletter_archived_sender, newsletter_archived_subject, newsletter_archived_html, newsletter_archived_text, newsletter_archived_attachment, newsletter_archived_status, newsletter_archived_date)
					VALUES ('".$nid."','".addslashes($newsletterName)."','".addslashes($newsletterSenderName)."','".addslashes($newsletterSubject)."','".addslashes($newsletterHtml)."','".addslashes($newsletterText)."','".addslashes(implode("\r\n", $attachedFiles))."','".$status."','".$datetime."')";
		$db->Query($query);
	
		// Falls der Versand wiederaufgenommen wurde, wird der alte/betroffene Datensatz wieder
		// aus der Tabelle paperboy_archived gelöscht
		if ($reSend) {
			$query = 	"DELETE from paperboy_archived
						WHERE id = '".$reSend."'";
			$db->Query($query);
		}
	}

/*	
	protected function getStoredSessionVars() {
		global $session;
		$getSessionVars = array (
				'subaction',
				'update',
				'subscriberEmailDelete',
				'subscriberEmailEdit',
				'newsletterHtml',
				'newsletterText',
				'newsletterSubject',
				'newsletterName',
				'nid',
				//									'unid',
		//									'importNid',
				'newsletterSenderMail',
				'newsletterSenderName',
				'newsletterTemplate',
				'reSend'
		);
		foreach ($getSessionVars as $sessionVar) {
			$getSessionVars[$sessionVar] = $session->GetSessionVar($sessionVar);
			//echo "<br>$sessionVar ist: ".$getSessionVars[$sessionVar];
		}
		return $getSessionVars;
	}
*/
	
	
	
	
	
	/**
	 * function checkEmail()
	 * Prüft E-Mail Adresse
	 * @param $email, string email adresse
	 * @return bol, true oder false (wenn email ist nicht gültig)
	 */
	protected function checkEmail($email) {
		$email = trim($email);
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+\.[A-Za-z]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
	
		}
		return $isValid;
	}
	
}

$paperboyController = new PaperboyController();
$content = $paperboyController->work();

?>