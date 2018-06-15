<?php

/**
 * class_subscriptionmanager.php
 * Class provides methods to handle newsletter subscriptions
 *
 * @version 2015-02-11
 * @author J.Hahn <info@buero-hahn.de>
 */
namespace Contentomat\Paperboy;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\MimeMailer;

class SubscriptionHandler {

	protected $db;
	
	// ???
	protected $parser;

	protected $errorNr;
	protected $splitString = '{SPLITTDATAHERE}';
	protected $currentHash;
	protected $password;
	
	protected $subscribersTable;
	protected $distributedTable;
	
	/**
	 * Konstruktor
	 */
	public function __construct($params=array()) {

		$this->db = new DBCex();
		$this->parser = new Parser();	// ???
		$this->errorNr = '';
		
		$this->subscribersTable = 'paperboy_subscribers';
		$this->distributedTable = 'paperboy_distributed';
		
		//$this->userTable = $params['userTable'];
	}
	
	/**
	 * function notifySubscriber()
	 * Versendet eine Benachrichtigungsmail an einen User
	 *
	 * @param array $params Folgende Parameter werden per Array �bergeben:
	 * @param string email E-Mailadresse des Empf�nger
	 * @param string template Pfad zum Template
	 *
	 * @return bool true oder false, je nachdem ob die E-Mail versendet werden konnte
	 */
	public function notifySubscriber($params) {
		
		if (!$this->checkEmail($params['email'])) {
			return false;
		}
		if (!$this->checkFile($params['template'])) {
			return false;
		}
		$mailTemplate = file_get_contents($params['template']);
		$mailTemplateParts = (explode($this->splitString, $mailTemplate));
	
		// Sollte ergeben:
		// 0. Absendername
		// 1. Absendermail
		// 2. Anwortadresse
		// 3. Betreffzeile
		// 4. Textk�rper
		// 5. HTML-K�rper
		// Alle Parameter werden an den Parser �bergeben/ durchgeschleift
		$params['password'] = $this->password;
		$this->parser->setMultipleParserVars($params);
		if ($params['hash']) {
			$this->parser->setParserVar('hash', $params['hash']);
		} else {
			$this->parser->setParserVar('hash', $this->currentHash);
		}
	
		// Teile erzeugen/ parsen
		$partSubject = $this->parser->parse($mailTemplateParts[3]);
		$partText = $this->parser->parse(trim($mailTemplateParts[4]));
		$partHtml = $this->parser->parse(trim($mailTemplateParts[5]));
	
		// E-Mail erzeugen und versenden
		require_once(PATHTOADMIN . 'classes/class_mimemailer.php');
	
		$mimeMailer = new MimeMailer();
	
		// KWA Sonderbehandlung
		if (PAGELANG == 'tr') {
			$mimeMailer->setTextPartCharset('utf-8');
		}
	
		$mimeMailer->createMail(array('html' => $partHtml,
			'text' => $partText,
			'attachments' => $params['attachments'],
			'senderMail' => $mailTemplateParts[1],
			'senderName' => $mailTemplateParts[0],
			'replyTo' => $mailTemplateParts[2]
		));
	
	
	
		$check = mail($params['email'], $partSubject, $mimeMailer->mailBody, $mimeMailer->mailHeader, '-f' . $mailTemplateParts[1]);
	
		//XXX: remove from live , test email content
		//return true;
		//file_put_contents('postbox/'.microtime(true) . '-user-.txt', $mimeMailer->mailBody);
	
		if (!$check) {
			$this->errorNr = 3007;
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * function notifyAdministrator()
	 * Versendet eine Benachrichtigungsmail an Administrator
	 *
	 * @param array $params Folgende Parameter werden per Array übergeben:
	 * @param string email E-Mailadresse des Empfänger
	 * @param string template Pfad zum Template
	 *
	 * @return bool true oder false, je nachdem ob die E-Mail versendet werden konnte
	 */
	public function notifyAdministrator($params) {
		if (!$this->checkEmail($params['adminEmail']))
			return false;
		if (!$this->checkFile($params['adminTemplate']))
			return false;
	
		$mailTemplate = file_get_contents($params['adminTemplate']);
		$mailTemplateParts = (explode($this->splittString, $mailTemplate));
	
		// Sollte ergeben:
		// 0. Absendername
		// 1. Absendermail
		// 2. Anwortadresse
		// 3. Betreffzeile
		// 4. Textkörper
		// 5. HTML-Körper
		// Alle Parameter werden an den Parser übergeben/ durchgeschleift
		$this->parser->setMultipleParserVars($params);
	
		// Teile erzeugen/ parsen
		$partSubject = $this->parser->parse($mailTemplateParts[3]);
		$partText = $this->parser->parse(trim($mailTemplateParts[4]));
		$partHtml = $this->parser->parse(trim($mailTemplateParts[5]));
	
		// E-Mail erzeugen und versenden
		require_once(PATHTOADMIN . 'classes/class_mimemailer.php');
		$mimeMailer = new MimeMailer();
		$mimeMailer->eol = "\n";
		$mimeMailer->createMail(array('html' => $partHtml,
			'text' => $partText,
			'attachments' => $params['attachments'],
			'senderMail' => $mailTemplateParts[1],
			'senderName' => $mailTemplateParts[0],
			'replyTo' => $mailTemplateParts[2]
		));
		@$check = mail($params['adminEmail'], $partSubject, $mimeMailer->mailBody, $mimeMailer->mailHeader, '-f' . $mailTemplateParts[1]);
	
		//file_put_contents('postbox/'.microtime(true) . '-admin-.txt', $mimeMailer->mailBody);
	
		if (!$check) {
			$this->errorNr = 3007;
			return false;
		} else {
			return true;
		}
	}

	/**
	 * function deleteSubscriber()
	 * Method to delete a subscriber and all its suscriptions.
	 * 
	 * @param number $subscriberID
	 * @return boolean
	 */
	public function deleteSubscriber($subscriberID=0) {
		
		$subscriberID = intval($subscriberID);
		if (!$subscriberID) {
			return false;
		}
		
		// 1. delete subscriptions
		$check = $this->db->query("DELETE FROM " . $this->distributedTable . " WHERE subscriber_id = '" . $subscriberID . "'");
		
		if ($check) {
			return false;
		}
		
		// 2. delete subscriber
		$check = $this->db->query("DELETE FROM " . $this->subscribersTable . " WHERE id = '" . $subscriberID . "'");
		
		if ($check) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * public function sendNewPasswordNotification()
	 *
	 * send recovery password to client, client must verify the password changing by clicking the
	 * verify link in password recovery email
	 * @param array params
	 * @return boolean
	 */
	public function sendNewPasswordNotification($params=array()) {
		$defaultParams = array(
			'template' => '',
			'email' => ''
		);
	
		$params = array_merge($defaultParams, $params);
		// create action hash
		$this->currentHash = $this->createHash();
	
		//create new password
		$params['newPassword'] = $this->createPassword();
		$params['hash'] = $this->currentHash;
	
		// save a hash, the new password in subscriber table
		$query = "UPDATE paperboy_subscribers 
				  SET action_hash='" . $this->db->dbQuote($this->currentHash) . "', 
				  pass_request='" . $this->db->dbQuote($params['newPassword']) . "' 
				  WHERE email='" . $this->db->dbQuote($params['email']) . "' ";
	
		$check = $this->db->query($query);
		
		if (!$this->db->getLastErrorNr()) {
			$this->notifySubscriber($params);
			return true;
		} else {
			return false;
		}
	}
	
	public function activateNewPassword($params) {
return true;		
		if (!$params['hash']) {
			return false;
		}
		
		$query = "UPDATE paperboy_subscribers 
				  SET pass = pass_request, 
				  action_hash='', 
				  pass_request='' 
				  WHERE action_hash='" . $this->db->dbQuote($params['hash']) . "' ";
		
		$check = $this->db->query($query);
		
		if (!$this->db->getLastErrorNr()) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * function subscribeNewsletter()
	 * Abonniert einen oder mehrere Newsletter f�r einen User. Existiert dieser noch nicht, wird er angelegt.
	 *
	 * @param array $params Folgende Parameter werden per Array �bergeben:
	 * @param string email E-Mailadresse des Empf�nger
	 * @param mixed newsletterID ID(s) des/der Newsletter, die/der abonniert werden soll/en
	 *
	 * @return bool true oder false, je nachdem ob das Abonnement geklappt hat oder nicht.
	 */
	public function subscribeNewsletter($params=array()) {

		// 	 		if (!$this->checkNewsletter($params['newsletterID'])) return false;
		
// 		if (!$params['checkOnlyEmail']) {
			
// 			if (!$this->checkTitle($params['title'])) {
// 				return false;
// 			}
			
// 			if (!$this->checkFirstname($params['firstname'])) {
// 				return false;
// 			}
			
// 			if (!$this->checkLastname($params['lastname'])) {
// 				return false;
// 			}
// 		}
		
		$activateSubscription = false;
		if ($params['activateSubscription']) {
			$activateSubscription = true;
		}
		unset($params['activateSubscription']);


		if (!$this->checkEmail($params['email'])) {
			return false;
		}
		
		if (!$params['newsletterID']) {
			$params['newsletterID'] = array();
		}
		
		if (!is_array($params['newsletterID'])) {
			$params['newsletterID'] = array($params['newsletterID']);
		}
	
		$subscriberID = $this->createSubscriber($params);
	
		if ($subscriberID) {
	
			// 1. Neuen Hash-Wert erzeugen
			$this->createHash();
			$this->db->query("UPDATE paperboy_subscribers SET action_hash = '" . $this->currentHash . "' WHERE id = '" . $subscriberID . "'");
			
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3004;
			} else {
				
				// 2. Prüfen, ob Newsletter schon abonniert sind
				foreach ($params['newsletterID'] as $key => $value) {
					if ($this->newsletterIsSubscribed(array('subscriberID' => $subscriberID, 'newsletterID' => $value))) {
						unset($params['newsletterID'][$key]);
					}
				}
	
				//3. Newsletter abonnieren
				foreach ($params['newsletterID'] as $key => $value) {
					$this->db->query("
						INSERT INTO paperboy_distributed SET 
						subscriber_id = '" . $subscriberID . "', 
						newsletter_id = '" . intval($value) . "', 
						is_active = '" . ($activateSubscription ? "1" : "0") . "'"
					);
					if ($this->db->lastErrorNr) {
						$this->errorNr = 3005;
						return false;
					}
				}
			}
		} else {
			return false;
		}
	
		return true;
	}
	
	/**
	 * function unsubscribeNewsletter();
	 * Erm�glicht das Abbestellen von abonnierten Newslettern
	 *
	 * @param array $params Folgende Parameter werden per Array �bergeben:
	 * @param string email E-Mailadresse des Empf�nger
	 * @param mixed newsletterID ID(s) des/der Newsletter, die/der abonniert werden soll/en
	 *
	 * @return bool true oder false, je nachdem ob der erste Schritt der Abbestellung geklappt hat oder nicht.
	 */
	public function unsubscribeNewsletter($params) {
		
		if (!$this->checkNewsletter($params['newsletterID'])) {
			return false;
		}
		
		if (!$this->checkEmail($params['email'])) {
			return false;
		}
		
		if (!is_array($params['newsletterID'])) {
			$params['newsletterID'] = array($params['newsletterID']);
		}
		$subscriberData = $this->getSubscriberDataFromEmail($params['email']);
	
		if (empty($subscriberData)) {
			$this->errorNr = 4003;
			return false;
		} else {
			$query = "UPDATE paperboy_subscribers 
					  SET newsletter_queue = '" . $this->db->dbQuote(implode(';', $params['newsletterID'])) . "', 
					  action_hash = '" . $this->createHash() . "' 
					  WHERE id = '" . $subscriberData['id'] . "'";
			
			$this->db->query($query);
			
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3000;
				return false;
			} else {
				return true;
			}
		}
	}
	
	/**
	 * function proceedUnsubscription();
	 * Erm�glicht das Abbestellen von abonnierten Newslettern. Falls der letzte Newsletter abbestellt wurde, wird der User gel�scht.
	 *
	 * @param array $params Folgende Parameter werden per Array �bergeben:
	 * @param string hash Hash-Wert zum identifizieren des Users
	 *
	 * @return bool true oder false, je nachdem ob der erste Schritt der Abbestellung geklappt hat oder nicht.
	 */
	function proceedUnsubscription($params) {
	
		if (!$params['hash']) {
			$this->errorNr = 3010;
			return false;
		}
		$subscriberData = $this->getSubscriberDataFromHash($params['hash']);
	
		if (empty($subscriberData)) {
			$this->errorNr = 4003;
			return false;
		} else {
			$newsletterIDs = str_replace(';', ',', $subscriberData['newsletter_queue']);
	
			$this->db->query("
				DELETE FROM paperboy_distributed 
				WHERE newsletter_id IN (" . $newsletterIDs . ") 
				AND subscriber_id = '" . $subscriberData['id'] . "'"
			);
	
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3012;
				return false;
			} else {
	
				$this->db->query("
					SELECT COUNT(id) AS subscribedNewsletters 
					FROM paperboy_distributed 
					WHERE subscriber_id = '" . intval($subscriberData['id']) . "'"
				);
				$r = $this->db->get();
	
	
				// Zählen, wieviele Newsletter vom User noch abonniert sind
				if (!$r['subscribedNewsletters'] && !$subscriberData['is_download_active']) {
	
					// User löschen, wenn er keine Newsletter mehr abonniert hat
					$this->db->query("
						DELETE FROM paperboy_subscribers 
						WHERE id = '" . intval($subscriberData['id']) . "'"
					);
					if ($this->db->lastErrorNr) {
						$this->errorNr = 3014;
						return false;
					} else {
						return true;
					}
				} else {

					// User updaten, wenn er noch Newsletter abonniert hat
					$this->db->query("
						UPDATE paperboy_subscribers 
						SET action_hash = '', 
						newsletter_queue = '' 
						WHERE id = '" . intval($subscriberData['id']) . "'"
					);
					
					if ($this->db->lastErrorNr) {
						$this->errorNr = 3013;
						return false;
					} else {
						return true;
					}
				}
			}
		}
	}

	public function quickUnsubscription($params) {
	
		if (!$this->checkNewsletter($params['newsletterID'])) {
			return false;
		}
		
		if (!$params['subscriberID']) {
			return false;
		} 
	
		$query = "DELETE FROM  " . $this->distributedTable ." 
				  WHERE newsletter_id = '" . intval($params['newsletterID']) . "' 
				  AND subscriber_id = '" . intval($params['subscriberID']) . "'"; 

		$check = $this->db->query($query);
		
		return !boolval($check);
	}
	
	public function quickSubscription($params) {

		if (!$this->checkNewsletter($params['newsletterID'])) {
			return false;
		}
	
		if (!$params['subscriberID']) {
			return false;
		}
	
		// already subscribed?
		if ($this->newsletterIsSubscribed(array('subscriberID' => $params['subscriberID'], 'newsletterID' => $params['newsletterID']))) {
			
			$this->setSubscriptionStatus(array(
				'subscriberID' => $params['subscriberID'], 
				'newsletterID' => $params['newsletterID'],
				'isActive' => $params['isActive']
			));
			return true;
		}

		if (!isset($params['isActive'])) {
			$params['isActive'] = 1;
		}
		
		// no? => new subscription
		$query = "INSERT INTO  " . $this->distributedTable ."
				  SET newsletter_id = '" . intval($params['newsletterID']) . "',
				  subscriber_id = '" . intval($params['subscriberID']) . "', 
				  is_active = '". intval($params['isActive']) . "'";
	
		$check = $this->db->query($query);
	
		return !boolval($check);
	}
	
	public function setSubscriptionStatus($params=array()) {
		
		if (!$params['id'] && (!$params['subscriberID'] && !$params['newsletterID'])) {
			return false;
		}
		
		if ($params['id']) {
			$whereClause = " id = '" . intval($params['id']) ."'";
		} else {
			$whereClause = " subscriber_id = '" . intval($params['subscriberID']) ."' AND newsletter_id = '" . intval($params['newsletterID']) ."'";
		}

		$check = $this->db->query("UPDATE " . $this->distributedTable ." SET is_active = '" . intval($params['isActive']) . "' WHERE " . $whereClause);
		
		return !(bool)$check;
	}
	
	/**
	 * public function subscriberIsActive()
	 * Checks if a subscriber has the state 'active'
	 * 
	 * @param number $subscriberID
	 * @return bool
	 */
// 	public function subscriberIsActive($subscriberID) {
		
// 		$this->db->query("SELECT is_active FROM " . $this->subscribersTable . " WHERE id = '" . intval($subscriberID) . "' LIMIT 1");
// 		$r = $this->db->get();
		
// 		return boolval($r['is_active']);
// 	}
	
	/**
	 * function createSubscriber()
	 * Erstellt einen neuen User/ Abonnenten
	 *
	 * @param array $params Folgende Parameter werden per Array �bergeben:
	 * @param string email E-Mailadresse des Users
	 *
	 * @return bool true oder false, je nachdem ob das Erzeugen geklappt hat oder nicht.
	 */
	public function createSubscriber($params) {
		$this->errorNr = 0;
	
		if (!$this->checkEmail($params['email'])) {
			return 0;
		}
	
		// 1. Checken, ob User schon registriert ist und nur einen weiteren Newsletter abonnieren m�chte
		$this->db->query("SELECT * FROM " . $this->subscribersTable . " WHERE email = '" . $this->db->dbQuote(trim($params['email'])) . "'");
		$r = $this->db->get();

		// Fall 1: E-Mailadresse existiert bereits, aber User ist noch nicht aktiviert, dann Fehler
//		if (!empty($r) && !$r['is_active']) {
		if (!empty($r)) {

			$this->errorNr = 3002;
			return false;
		}


		// basic fields
		$fields = array(
			'email' => $params['email'],
			'date_signed_in' => date('Y-m-d H:i:s')
		);
		unset($params['email']);
		unset($params['newsletterID']);
		
		$fields = array_merge($params, $fields);

		// Fall 2: User existiert noch nicht: anlegen
		$this->db->query("INSERT INTO " . $this->subscribersTable ." SET " . $this->db->makeSetQuery($fields));

		if ($this->db->lastErrorNr) {
			$this->errorNr = 3003;
			return false;
		} else {
			return $this->db->lastInsertedId();
		}
	}
	
	/**
	 * function activateNewsletter()
	 * Setzt den Status eines Abonnements auf 'aktiv'
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $hash Hash-Wert, anhand dessen der User und die Newsletter ermittelt werden k�nnen
	 *
	 * @return bool Liefert true oder false, je nachdem, ob die Aktivierung erfolgreich war
	 */
	function activateNewsletter($params) {
	
		if (!$params['hash']) {
			$this->errorNr = 3010;
			return false;
		}
	
		$subscriberData = $this->getSubscriberDataFromHash($params['hash']);
	
		if (empty($subscriberData)) {
			$this->errorNr = 3008;
			return false;
		}
		
		$subscriberID = intval($subscriberData['id']);

		if (!$subscriberData['is_active']) {
			$check = $this->activateSubscriber(array('subscriberID' => $subscriberID));
			if (!$check) {
				return false;
			}
		}

		// Newsletter aktivieren
		$this->db->query("UPDATE paperboy_distributed SET is_active = '1' WHERE subscriber_id = '" . $subscriberID . "' AND is_active = '0'");
		if ($this->db->lastErrorNr) {
			$this->errorNr = 3010;
			return false;
		}
		$this->deleteHash(array('subscriberID' => $subscriberID));
		return true;
	}
	
	/**
	 * function activateSubscriber()
	 * Setzt den Status eines Abonnenten auf 'aktiv'
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $hash Hash-Wert, anhand dessen der User ermittelt werden kann
	 * @param number $subscriberID ID des Benutzers
	 *
	 * @return bool Liefert true oder false, je nachdem, ob die Aktivierung erfolgreich war
	 */
	function activateSubscriber($params) {
	
		$subscriberID = intval($params['subscriberID']);
	
		if (!$subscriberID) {
			$subscriberID = $this->getsubscriberIDFromHash($params['hash']);
		}
	
		if (!$subscriberID) {
			return false;
		} 
	
// 		if ($params['activationAction'] == 'activateDownload') {
// 			$activeQuery = "is_active = '1', is_download_active='1', ";
// 		} else {
			$activeQuery = "is_active = '1',";
//		}
		$query = "UPDATE paperboy_subscribers SET " . $activeQuery . " action_hash = '' WHERE id = '" . $subscriberID . "'";

		$this->db->query($query);
		if ($this->db->lastErrorNr) {
			$this->errorNr = 3009;
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * function getsubscriberIDFromHash()
	 * Ermittelt die User-ID anhand eines Hash-Wertes
	 *
	 * @param string $hash Hash-Wert als String
	 * @return Number Liefert die Benutzer-ID zur�ck, ansonsten 0
	 */
	public function getsubscriberIDFromHash($hash) {
		$this->db->query("SELECT id FROM paperboy_subscribers WHERE action_hash = '" . $this->db->dbQuote($hash) . "'");
		$r = $this->db->get();
	
		if (empty($r)) {
			$this->errorNr = 3008;
			return 0;
		} else {
			return intval($r['id']);
		}
	}
	
	/**
	 * function getSubscriberDataFromHash()
	 * Ermittelt alle User-Daten anhand eines Hash-Wertes
	 *
	 * @param string $hash Hash-Wert als String
	 * @return Number Liefert die Benutzer-Daten zur�ck, ansonsten false
	 */
	public function getSubscriberDataFromHash($hash) {
		$this->db->query("SELECT * FROM paperboy_subscribers WHERE action_hash = '" . $this->db->dbQuote($hash) . "'");
		$r = $this->db->get();
		if (empty($r)) {
			$this->errorNr = 3008;
			return array();
		} else {
			return $r;
		}
	}
	
	/**
	 * function getSubscriberDataFromId()
	 * Ermittelt alle User-Daten anhand eine ID
	 *
	 * @param number $subscriberID ID des Nutzers
	 *
	 * @return Number Liefert die Benutzer-Daten zur�ck, ansonsten false
	 */
	public function getSubscriberDataFromId($subscriberID) {
		$this->db->query("SELECT * FROM paperboy_subscribers WHERE id = '" . intval($subscriberID) . "'");
		$r = $this->db->get();
		if (empty($r)) {
			$this->errorNr = 3008;
			return false;
		} else {
			return $r;
		}
	}
	
	/**
	 * function getSubscriberDataFromEmail() Ermittelt alle User-Daten anhand eine ID
	 *
	 * @param string $email E-Mailadresse des Nutzers
	 *
	 * @return array Liefert die Benutzer-Daten zur�ck, ansonsten false
	 */
	public function getSubscriberDataFromEmail($email) {
	
		$this->db->query("SELECT * FROM paperboy_subscribers WHERE email = '" . $this->db->dbQuote(trim($email)) . "'");
		$r = $this->db->get();
		if (empty($r)) {
			$this->errorNr = 4003;
			return array();
		} else {
			return $r;
		}
	}
	
	/**
	 * function getSubscriptions()
	 * Returns all newsletter subscriptions of a given user identified by its ID
	 * 
	 * @param number $userID User's ID
	 * @return array List of all subscribed newsletters
	 */
	public function getSubscriptions($userID=0) {
		
		$userID = intval($userID);
		if (!$userID) {
			return array();
		}
		
		$this->db->query("SELECT * FROM " . $this->distributedTable . " WHERE subscriber_id = '" . $userID . "' AND is_active = '1'");
		
		return $this->db->getAll(false);
		
	}
	
	/**
	 * function subscriberIsActive()
	 * Ermittelt ob ein User-Account aktiviert ist
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $hash Hash-Wert...
	 * @param number $subscriberID ...oder ID des Benutzers
	 *
	 * @return mixed 1 oder 0, je nachdem, ob der User aktiviert ist oder nicht, false im Fehlerfall (immer mit === Operator �berpr�fen)
	 */
	public function subscriberIsActive($params) {
	
		$r = array();
	
		if ($params['subscriberID']) {
			$r = $this->getSubscriberDataFromId($params['subscriberID']);
		}
	
		if ($params['hash']) {
			$r = $this->getSubscriberDataFromHash($params['hash']);
		}
	
		if (!empty($r) && $r['is_active']) {
			return 1;
		}
	
		if (!empty($r) && !$r['is_active']) {
			return 0;
		}
	
		return false;
	}
	
	/**
	 * function newsletterIsSubscribed()
	 * Ermittelt, ob ein User einen ausgew�hlten Newsletter bereits abonniert hat
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $newsletterID ID des Newsletters
	 * @param string $subscriberID ID des Benutzers
	 *
	 * @return bool true oder false, je nachdem, ob der Newsletter abonniert ist oder nicht
	 */
	public function newsletterIsSubscribed($params) {
		if (!$params['newsletterID'] || !$params['subscriberID'])
			return false;
	
		$this->db->query("SELECT * FROM paperboy_distributed WHERE newsletter_id = '" . intval($params['newsletterID']) . "' AND subscriber_id = '" . intval($params['subscriberID']) . "'");
		$r = $this->db->get();
	
		if (!empty($r)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * function getNewsletterList()
	 * Liefert ein Array mit den Daten aller verf�gbarer Newsletter
	 *
	 * @param void
	 *
	 * @return array Liste aller Newsletter
	 */
	public function getNewsletterList($overrideStatus = false) {
		if ($overrideStatus) {
			$this->db->query("SELECT * FROM paperboy_newsletters ORDER BY newsletter_name");
		} else {
			$this->db->query("SELECT * FROM paperboy_newsletters WHERE is_active = '1' ORDER BY newsletter_name");
		}
		
		$newsletterList = $this->db->getAll(false);
		
// 		while ($r = $this->db->get()) {
// 			$newsletterList[] = $r;
// 		}
		return $newsletterList;
	}
	
	/**
	 * function createHash ()
	 * Erzeugt einen eindeutigen Hash-Wert/-String
	 *
	 * @param void
	 * @return string Hash-Wert
	 */
	public function createHash() {
		$this->currentHash = md5(uniqid(rand(), true));
		return $this->currentHash;
	}
	
	/**
	 * function deleteHash()
	 * L�scht Hash-Wert/-String eines Users
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param number $subscriberID ID des Benutzers
	 *
	 * @return bool false, wenn keine User-ID �bergeben wurde, ansonsten true
	 */
	public function deleteHash($params) {
		if (!$params['subscriberID'])
			return false;
	
		$this->db->query("UPDATE paperboy_subscribers SET action_hash = '' WHERE id = '" . $params['subscriberID'] . "'");
		return true;
	}
	
	/**
	 * public function createPassword()
	 *
	 * create random pass for registerd users
	 * @return string password
	 */
	public function createPassword($length = 5) {
		$password = "";
		$possible = "2346789bcdfghjkmnpqrtvwxyz";
		$maxlength = strlen($possible);
		if ($length > $maxlength) {
			$length = $maxlength;
		}
		$i = 0;
		while ($i < $length) {
	
			$char = substr($possible, mt_rand(0, $maxlength - 1), 1);
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
	
	/**
	 * function checkEmail()
	 * Pr�ft eine E-Mailadresse auf ihre G�ltigkeit
	 *
	 * @param string email E-Mailadresse
	 * @return boolean true oder false, je nach Ergebnis der Pr�fung
	 */
	public function checkEmail($email) {
		$email = trim($email);
		if (!$email) {
			$this->errorNr = 4000;
			return false;
		}
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if ((is_bool($atIndex) && !$atIndex)) {
			$isValid = false;
			$this->errorNr = 4002;
		} else {
			$domain = substr($email, $atIndex + 1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
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
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
					$isValid = false;
				}
	
				// DNS-Check
				if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
					// domain not found in DNS
					$isValid = false;
				}
			}
			if ($isValid == false) {
				$this->errorNr = 4002;
			}
		}
		return $isValid;
	}
	
	/**
	 * function password()
	 * Prüft eine password auf ihre Gültigkeit
	 *
	 * @param string password
	 * @return boolean true oder false, je nach Ergebnis der Prüfung
	 */
	public function checkPassword($password) {
		$password = trim($password);
		if ($password) {
			return true;
		} else {
			$this->errorNr = 4010;
			return false;
		}
	}
	
	/**
	 * function checkFirstname()
	 * Prüft eine Vorname auf ihre Gültigkeit
	 *
	 * @param string Vorname
	 * @return boolean true oder false, je nach Ergebnis der Prüfung
	 */
	public function checkFirstname($firstname) {
		$firstnamee = trim($firstname);
		if ($firstname) {
			return true;
		} else {
			$this->errorNr = 3015;
			return false;
		}
	}
	
	/**
	 * function checkSurname()
	 * Prüft eine Name auf ihre Gültigkeit
	 *
	 * @param string Name
	 * @return boolean true oder false, je nach Ergebnis der Prüfung
	 */
	public function checklastname($lastname) {
		$surname = trim($surname);
		if ($lastname) {
			return true;
		} else {
			$this->errorNr = 3012;
			return false;
		}
	}
	
	/**
	 * function checkTitle()
	 * Prüft eine Name auf ihre Gültigkeit
	 *
	 * @param string Name
	 * @return boolean true oder false, je nach Ergebnis der Prüfung
	 */
	public function checkTitle($title) {
		$title = trim($title);
		if ($title) {
			return true;
		} else {
			$this->errorNr = 3011;
			return false;
		}
	}
	
	/**
	 * function checkNewsletter()
	 * Pr�ft eine Newsletter-ID-Liste auf Ihre G�ltigkit
	 *
	 * @param mixed $newsletterIDs Newsletter-IDs entewder als Array oder als Number
	 *
	 * @return boolean true oder false, je nach Ergebnis der Pr�fung
	 */
	public function checkNewsletter($newsletterIDs) {
		if (!is_array($newsletterID))
			settype($newsletterIDs, 'array');
	
		if (empty($newsletterIDs)) {
			$this->errorNr = 3000;
			return false;
		}
	
		foreach ($newsletterIDs as $nid) {
			if (intval($nid) != $nid) {
				$this->errorNr = 3000;
				return false;
			}
		}
	
		return true;
	}
	
	/**
	 * function checkFile()
	 * Pr�ft, ob eine Datei vorhanden ist
	 *
	 * @param string $file Dateiname (inkl. Pfad)
	 * @return boolean true oder false, je nach Ergebnis der Pr�fung
	 */
	public function checkFile($file) {
		if (file_exists($file)) {
			return true;
		} else {
			$this->errorNr = 3006;
			return false;
		}
	}
	
	/**
	 * function getErrorNr()
	 * Gibt die interne Nummer eines aufgetretenen Fehlers zur�ck
	 *
	 * Die Fehler in Zusammenhang mit der anmeldung haben Nummern ab 3000. Fehlernummern darunter resultieren aus Datenbankfehlern.
	 *
	 * @param void
	 * @return Number Nummer des Fehlers
	 */
	public function getErrorNr() {
		/*
		 * Fehlernummern:
		* 3000 -> Allgemeiner Schreib-/Lesfehler (Datenbankfehler)
		* 3001 -> E-Mailadresse ung�ltig
		* 3002 -> User will Newsletter abonnieren, ist aber noch nicht aktiviert.
		* 3003 -> User konnte nicht angelegt werden (es fand anschlie�end kein eintrag in die Newsletter-Abotabelle statt)
		* 3004 -> Hash-Wert konnte nicht bei der Newsletterbestellung beim User gespeichert werden
		* 3005 -> Newsletter-Abo konnte nicht in Tabelle paperboy_distributed erzeugt werden.
		* 3006 -> Datei existiert nicht (z.B. Template)
		* 3007 -> Benachrichtigungsmail konnte wegen eines Mailfehlers nicht versendet werden
		* 3008 -> Benutzer existiert nicht
		* 3009 -> Bentuzer konnte wegen eines technischen Fehlers (Datenbank) nicht aktiviert werden
		* 3010 -> Newsletter kann nicht aktiviert werden, da Hash-Wert fehlt oder falsch ist
		* 3011 -> Keine Newsletter ausgew�hlt
		* 3012 -> Newsletter konnten nicht aus paperboy_distributed gel�scht werden (z.B. Abbestellung)
		* 3013 -> Hash-Wert konnte nicht (bei der Abbestellung) gel�scht werden.
		* 3014 -> User konnte (bei der Abbestellung) nicht gel�scht werden
		*/
		return intval($this->errorNr);
	}
	
	/**
	 * function  getRefererIp()
	 * Ermittelt die IP-Adresse des Referers
	 *
	 * @param void
	 * @return string IP des aufrufenden Computers
	 */
	public function getRefererIp() {
		$createdByIp = getenv('HTTP_X_FORWARDED_FOR');
		if (!$createdByIp)
			$createdByIp = getenv('REMOTE_ADDR');
		return $createdByIp;
	}
	
	// TODO!
	public function getNewsletterSubscribers($params) {
		$defaultParams = array(
				'newslettersId' => array(),
				'subscriberIsActive' => true,
				'newsletterSubscriptionIsActive' => true,
				'orderBy' => 'sub.email',
				'exportFields' => "email:E-Mail"
		);
	
		$params = array_merge($defaultParams, $params);
	
		if (empty($params['newslettersId'])) {
			return array();
		}
	
		$subscribers = array();
	
		$selectFields = preg_replace("/[\r]/", "", $params['exportFields']);
		$selectFields = explode("\n", $params['exportFields']);
	
		$querySelectString = '';
	
		foreach ($selectFields as $field) {
			//$explode
			$field = explode(":", $field);
			if ($field[1]) {
				$querySelectString .= "sub." . trim($field[0]) . " as '" . trim($field[1]) . "', ";
			} else {
				$querySelectString .= "sub." . trim($field[0]) . " as '" . trim($field[0]) . "', ";
			}
			$querySelectString .= '';
		}
	
		// Query
		$query = "SELECT " . $querySelectString . " news.newsletter_shortcut as 'Newsletter'";
	
		if (!$params['newsletterSubscriptionIsActive']) {
			$query .=", dis.is_active as 'Aktive'  ";
		}
	
		$query .="FROM paperboy_subscribers sub JOIN paperboy_distributed dis ON(sub.id=dis.subscriber_id) ";
		$query .="JOIN paperboy_newsletters news ON (news.id=dis.newsletter_id) WHERE ";
		$qNewsletters = array();
		foreach ($params['newslettersId'] as $newsletter) {
			$qNewsletters[] = "dis.newsletter_id='" . intval(trim($newsletter)) . "'";
		}
		$query .= " (" . join(" OR ", $qNewsletters) . ") ";
	
		if ($params['subscriberIsActive']) {
			$query .= "AND sub.is_active='1' ";
		}
	
		if ($params['newsletterSubscriptionIsActive']) {
			$query .= "AND dis.is_active='1' ";
		}
	
		$query .= "ORDER BY " . $params['orderBy'];
	
		// Results
		$this->db->query($query);
	
		$newsLettersShourtcuts = array();
	
		while ($res = $this->db->get()) {
			if (!in_array($res['Newsletter'], $newsLettersShourtcuts)) {
				$newsLettersShourtcuts[] = $res['Newsletter'];
			}
			$subscribers[] = $res;
		}
	
		//return values
		if (!empty($subscribers)) {
			return array(
					'fields' => array_keys($subscribers[0]),
					'subscribers' => $subscribers,
					'newsLettersShourtcuts' => $newsLettersShourtcuts
			);
		} else {
			return array();
		}
	}
	
	/**
	 * 	public function getPaperboySettings()
	 *
	 * @param array $params
	 * @return array, paperboy table settings
	 */
	public function getPaperboySettings($params=array()) {
		$defaultSettings = array(
				'adminEmail' => 'test@buero-hahn.de',
				'notifySubscriberTemplate' => '',
				'notifyUnsubscriberTemplate' => '',
				'notifyAdminSubscriberTemplate' => '',
				'notifyAdminUnsubscriberTemplate' => '',
				'notifyAdminSubscriber' => 0,
				'notifyAdminUnsubscriber' => 0,
				'notifySubscriber' => 1,
				'notifyUnsubscriber' => 1,
		);
	
		$params = array_merge($defaultSettings, $params);
	
	
		$query = "SELECT cmt_tablesettings FROM cmt_tables WHERE cmt_showname='Paperboy'";
		$this->db->query($query);
		$res = $this->db->get();
		$paperboySettings = safeUnserialize($res['cmt_tablesettings']);
		$paperboySettings = array_merge($params, $paperboySettings);
		return $paperboySettings;
	}

	public function editSubscriberData($params=array()) {
		
		if (!is_array($params) || !(array)$params['subscriberID']) {
			return false;
		}
		
		$query = "UPDATE " . $this->subscribersTable . " 
				  SET " . $this->db->makeSetQuery((array)$params['data']) . " 
				  WHERE id = '" . intval($params['subscriberID']) . "' 
				  LIMIT 1";
		
		return !(bool)$this->db->query($query);
	}
}
?>
