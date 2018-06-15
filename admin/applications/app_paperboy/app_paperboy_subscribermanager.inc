<?php
/**
 * Paperboy\SubscriberManager
 * Include for Paperboy's subscriber databasetable. Handles newsletter subscriptions.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2015-01-28
 *
 */
namespace Contentomat\Paperboy;

use \Contentomat\ApplicationController;
use \Contentomat\Parser;
use \Contentomat\User;
use \Contentomat\PsrAutoloader;
// use \Contentomat\Import;
use \Contentomat\Paperboy\Import;

class SubscriberManager extends ApplicationController {
	
	protected $subscriptionHandler;
	protected $newsletterID;
	protected $subscriberID;

	/**
	 * public function init()
	 * Do all initialization here ("second constructor" of controller).
	 * 
	 * @param void
	 * @return void
	 * @see \Contentomat\ApplicationController::init()
	 */
	public function init() {
		$this->templatesPath = '/app_paperboy/';
		
		// due to bad file structure we load the class manually
		$this->load('classes/app_paperboy/class_subscriptionhandler.inc');
		
		$this->subscriptionHandler =  new SubscriptionHandler();
		
		// ???
		$this->newsletterID = intval($_REQUEST['newsletterID']);
		$this->subscriberID = intval($_REQUEST['subscriberID']);
		
	}
	
	/**
	 * protected function initActions()
	 *
	 * set action type, from any GET or POST variable named has name "action"
	 * if not then take the default action name "index" as current action name.
	 *
	 * @param void
	 * @return void
	 */
	protected function initActions($action='') {
	
		if ($this->requestvars['cmtAction']) {

			// 'cmtAction' should be used!!!!
			$this->action = $this->requestvars['cmtAction'];
		} else if ($this->requestvars['cmt_action']) {

			// 'cmt_action' is DEPRECATED!!!!
			$this->action = $this->requestvars['cmt_action'];
		} else if ($this->requestvars['action']) {

			// 'action' should not be used!!!!
			$this->action = $this->requestvars['action'];
		} else {
			$this->action = $this->defaultAction;
		}

		// save action?
		if ($_REQUEST['save']) {
			$this->action .= 'Save';
		}
		
		// after save action?
		if ($this->cmt->getVar('cmtEditedEntryID') && $this->cmt->getVar('cmtActionPerformed')) {
			$this->action = $this->cmt->getVar('cmtActionPerformed') . 'SaveProceed';
		}

	}
	
	/**
	 * public function actionDefault()
	 * Default action display the categories overview page.
	 * 
	 * @param void
	 * @return void
	 * @see \Contentomat\ApplicationController::actionDefault()
	 */
	public function actionDefault() {

		// is the quick search / filter for e-mail address active?
		$email = trim($_REQUEST['subscribersEmail']);
		if (!$email) {
			$email = $this->cmt->session->getSessionVar('subscribersEmail');
		}
		
		if ($email) {
			$this->cmt->setVar('cmtAddQuery', "WHERE email LIKE '%" . $this->db->dbQuote($email) . "%'");
			$this->parser->setParserVar('subscribersEmail', $email);
			$this->cmt->session->setSessionVar('subscribersEmail', $email);
			$this->cmt->session->saveSessionVars();
		}
		
		// show content
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_paperboy_subscribermanager_overview.tpl');
	}
	
	/**
	 * protected function actionResetQuickSearch()
	 * Deletes the e-mail address quick search value from session.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionResetQuickSearch() {
		$this->cmt->session->deleteSessionVar('subscribersEmail');
		$this->cmt->session->saveSessionVars();

		$this->changeAction('default');
	}

	/**
	 * function actionEdit()
	 * Called on subscriber's data editing. Creates a list of subscribed newsletters.
	 * 
	 * @param void
	 * @return void
	 * 
	 */
	protected function actionEdit() {
		
		$subscriberID = intval($_REQUEST['id'][0]);
		$subscriptionList = $this->makeSubscriptionList($subscriberID);
		$this->cmt->setVar('subscriptionList', $subscriptionList);
	}
	
	/**
	 * function actionEditSave();
	 * Called when an entry is saved.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionEditSave() {
		
		$newsletterID = $_REQUEST['newsletterID'];
		if (!is_array($newsletterID)) {
			$newsletterID = array();
		}
	
		$this->cmt->session->setSessionVar('paperboyNewsletterID', $newsletterID);
		$this->cmt->session->setSessionVar('paperboySubscriberIsActive', boolval($_REQUEST['is_active']));
		$this->cmt->session->saveSessionVars();
	}

	/**
	 * protected function actionEditSaveProceed()
	 * Second saving step (due to old architecture of app_showtable.php): restore some internal vars after CMT saved the entry and subscribe the selected newsletters.
	 * 
	 * @params void
	 * @return void
	 */
	protected function actionEditSaveProceed() {

		$subscriberID = $this->cmt->getVar('cmtEditedEntryID');
		$newsletterID = $this->cmt->session->getSessionVar('paperboyNewsletterID');
		$isActive = $this->cmt->session->getSessionVar('paperboySubscriberIsActive');
		
		if (!$subscriberID) {
			$this->setError('edit');
		} else {
			$this->setSuccess('edit');
		}
		
		if (!is_array($newsletterID)) {
			$newsletterID = array();
		}

		// is edited subscriber active? no => deactivate saved subscriptions
		if (!$isActive) {
			$this->setWarning('editSubscriberNotActive');
		}

		// the form returns only selected checkboxes in the array. The key represents the newsletter id (e.g. "3" => "on")
		$subscribedNewsletters = array_keys($newsletterID);
		
		$check = $this->saveSubscriptions($subscriberID, $subscribedNewsletters, $isActive);
		
		if (!$check) {
			$this->setError('subscription');
		} else {
			$this->setSuccess('subscription');
		}		
		
		$this->changeAction('default');
	}

	/**
	 * protected function actionNew()
	 * Called on creating a new subscriber entry.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionNew() {
		
		$subscriptionList = $this->makeSubscriptionList();
		$this->cmt->setVar('subscriptionList', $subscriptionList);
	}
	
	/**
	 * protected function actionNewSave()
	 * 
	 * @see \SubscriberManager::actionEditSave()
	 *
	 */
	protected function actionNewSave() {
		
		$this->changeAction('actionEditSave');
	}
	
	/**
	 * protected function actionNewSaveProceed()
	 * 
	 * @see \SubscriberManager::actionEditSave()
	 *
	 */
	protected function actionNewSaveProceed() {
		
		$this->changeAction('editSaveProceed');
	}	

	/**
	 * function actionDelete()
	 * Called on deletion one or  more entries.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDelete() {

		$id = $_REQUEST['id'];
		if (is_array($id)) {
			foreach ($id as $subscriberID) {
				
				$check = $this->deleteSubscriber($subscriberID);
				
				if (!$check) {
					// error!
					$this->setError('delete');
					$this->changeAction('default');
				}
			}
			
			// success
			$this->setSuccess('delete');
		}
		
		$this->changeAction('default');
	}
	
	/**
	 * function actionDeleteMultiple()
	 * Changes the action to delete 8multiple deletions are handled in method actionDelete();
	 *
	 */
	protected function actionDeleteMultiple() {
		$this->changeAction('delete');
	}
	
	
	/**
	 * function saveSubscriptions()
	 * Method called on editing a subscirber's data.
	 * 
	 * @param number $subscriberID 
	 * @return boolean
	 */
	protected function saveSubscriptions($subscriberID=0, $subscribedNewsletterIDs=array(), $isActive = true) {

		$subscriberID = intval($subscriberID);
	
		if (!$subscriberID) {
			return false;
		}
		
		if (!is_array($subscribedNewsletterIDs)) {
			$subscribedNewsletterIDs = array();
		}
		
		$newsletters = $this->getAvailableNewsletters();

		foreach ($newsletters as $newsletter) {
			$nid = $newsletter['id'];
			
			if (!in_array($nid, $subscribedNewsletterIDs)) {
				$check = $this->subscriptionHandler->quickUnsubscription(array('subscriberID' => $subscriberID, 'newsletterID' => $nid));
				
			} else {
				$check = $this->subscriptionHandler->quickSubscription(array('subscriberID' => $subscriberID, 'newsletterID' => $nid, 'isActive' => $isActive));
			}
			
			if (!$check) {
				return false;
			}
		}
		
		return true;
//		$this->changeAction('default');
	}
	
	/**
	 * function deleteSubscriber()
	 * Deletes a subscriber and its subscripptions
	 * 
	 * @param number $subscriberID
	 * @return boolean
	 */
	protected function deleteSubscriber($subscriberID=0) {
		$subscriberID = intval($subscriberID);
		if (!$subscriberID) {
			$id = $_REQUEST['id'];
			$subscriberID = intval($id[0]);
		}
		
		if (!$subscriberID) {
			return false;
		}
		
		return $this->subscriptionHandler->deleteSubscriber($subscriberID);
	}
	
	/**
	 * function makeSubscriptionList()
	 * Creates the HTML list of all newsletters (subscribed newsletters are selected)
	 * 
	 * @param number $subscriberID The subscriber's database id.
	 * @return string Newsletter lsit as HTML
	 * 
	 */
	protected function makeSubscriptionList($subscriberID=0) {
		
		$newsletters = $this->getAvailableNewsletters();

		$tplRow = $this->parser->getTemplate($this->templatesPath . 'subscribermanager_edit_row.tpl');
		$subscriptionList = '';
		
		foreach ($newsletters as $newsletter) {
			$this->parser->setMultipleParserVars(array(
				'newsletterID' => $newsletter['id'],
				'newsletterName' => $newsletter['newsletter_name']
			));
			
			$this->parser->setParserVar('isSubscribed', $this->subscriptionHandler->newsletterIsSubscribed(array('subscriberID' => $subscriberID, 'newsletterID' => $newsletter['id'])));
			$subscriptionList .= $this->parser->parse($tplRow);
		}
		
		$this->parser->setParserVar('subscriptionList', $subscriptionList);
		return $this->parser->parseTemplate($this->templatesPath . 'subscribermanager_edit_frame.tpl');

	}
	
	protected function getUsersSubscriptions($subscriberID) {
		
		return $this->subscriptionHandler->getSubscriptions($subscriberID);
	}
	
	protected function getAvailableNewsletters() {
		return $this->subscriptionHandler->getNewsletterList();
	}

	public function actionImportCSV() {

		$subscribersImported= 0;
		$subscribersToImport = 0;
		
		try {
			if (empty($_POST['newsletter'])) {
				throw new \Exception('import-no-newsletter');
			}
			if (empty($_FILES) || empty($_FILES['import_csv_file'])) {
				throw new \Exception('import-no-upload');
			}

			if (!is_uploaded_file($_FILES['import_csv_file']['tmp_name'])) {
				throw new \Exception('import-illegal-upload');
			}

			$Import = new \Contentomat\Paperboy\CsvImport(
				$_POST['delimiter'],
				$_POST['enclosure'],
				'\\',
			   	$_POST['skipFirstLine'],
				$_POST['newsletter']
			);

			$Import->import_from_csv($_FILES['import_csv_file']['tmp_name']);
			$subscribersImported = $Import->getSubscribersImported();
			$subscribersToImport = $Import->getSubscribersToImport();
		}
		catch (\Exception $e) {
			$this->parser->setParserVar ('cmtError', true);
			$this->parser->setParserVar ('cmtErrorType', $e->getMessage());
			$this->changeAction('default');
			return;
		}

		$this->parser->setParserVar('subscribersImported', $subscribersImported);
		$this->parser->setParserVar('subscribersToImport', $subscribersToImport);

		if ($subscribersImported > 0 && $subscribersImported == $subscribersToImport) {
			$this->parser->setParserVar('cmtSuccess', true);
			$this->parser->setParserVar('cmtSuccessType', 'import');
		}
		else {
			$this->parser->setParserVar('cmtWarning', true);
			$this->parser->setParserVar('cmtWarningType', 'import');
		}

		$this->changeAction('default');
	}

	// public function actionTest() {
    //
	// 	$email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : 'me@hannenz.de';
	// 	$newsletters = array();
	// 	$availableNewsletters = $this->subscriptionHandler->getNewsletterList();
	// 	$subscriber = $this->subscriptionHandler->getSubscriberDataFromEmail($email);
	// 	$subscriptions = $this->subscriptionHandler->getSubscriptions($subscriber['id']);
    //
	// 	foreach ($availableNewsletters as $newsletter) {
	// 		foreach ($subscriptions as $subscription) {
	// 			if ($subscription['newsletter_id'] == $newsletter['id']) {
	// 				$newsletters[] = $newsletter;
	// 				break;
	// 			}
	// 		}
	// 	}
    //
	// 	echo "<pre>";
	// 	print_r($subscriber);
	// 	print_r($subscriptions);
	// 	print_r($newsletters);
	// 	die();
	// }
}

$autoLoad = new PsrAutoloader();
$autoLoad->addNamespace('Paperboy', PATHTOADMIN . 'classes/app_paperboy/');
$autoLoad->addNamespace('Contentomat\Paperboy', PATHTOADMIN . 'classes/app_paperboy/');

$subscriptionManager = new SubscriberManager();
$content .= $subscriptionManager->work();
?>
