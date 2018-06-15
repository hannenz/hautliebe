<?php

/**
 * OUTDATED
 * class_subscriptionmanager.php
 * Klasse zur Verwaltung von Newsletterabonnenten
 *
 * Die Klasse stellt grundlegende Methoden zur Verf�gung, um das Ab- und Anmelden von Nutzern f�r beliebig viele Newsletter zu handhaben.
 *
 * @version 2009-05-04
 * @author J.Hahn <info@buero-hahn.de>
 */
class SubscriptionManager {

// 	protected $userTable;
// 	protected $userTable;
// 	protected $subscriberId;
	// Datenbankobjekt
	protected $db;
	// Parserobjekt
	protected $parser;
	// Fehlernummer
	protected $errorNr;
	// Templateteilmarkierung
	protected $splittString = '{SPLITTDATAHERE}';
	// Hashwert
	protected $currentHash;
	protected $password;

	/**
	 * Konstruktor
	 */
	public function __construct($params=array()) {
		$this->db = new DBCex();
		$this->parser = new Parser();
		$this->errorNr = '';
		//$this->userTable = $params['userTable'];
	}

	/**
	 * Destruktor
	 */
	public function __destruct() {
		
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
		
		if (!$this->checkEmail($params['email']))
			//echo "<-- Email -->";
			return false;
		if (!$this->checkFile($params['template']))
			//echo "<-- Template -->";
			return false;

		require_once(PATHTOADMIN . 'classes/class_mimemailer.php');
		$mimeMailer = new MimeMailer();
			
		$mailTemplate = file_get_contents($params['template']);
		
// TODO: Das hier sollte eigentlich durchgeführt werden.
//		if (mb_detect_encoding($mailTemplate, 'UTF-8')){
//			 $mailTemplate = utf8_decode($mailTemplate);
//		}
		
		$mailTemplateParts = (explode($this->splittString, $mailTemplate));
		
		foreach($mailTemplateParts as $key => $value) {
			$mailTemplateParts[$key] = trim($value);
		}

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

		//$senderName =  $mimeMailer->encodeSubject($mailTemplateParts[0]);
		//$senderName =  $mailTemplateParts[0];
//		$senderName = "=?ISO-8859-1?B?" . base64_encode ( $mailTemplateParts[0] ) . "?=";  
		$senderName = $mimeMailer->encodeSubject($mailTemplateParts[0], 'ISO-8859-1', 'binary');
		$partSubject = $mimeMailer->encodeSubject($partSubject, 'ISO-8859-1', 'binary');
		
		// E-Mail erzeugen und versenden
		$mimeMailer->createMail(array('html' => $partHtml,
			'text' => $partText,
			'attachments' => $params['attachments'],
			'senderMail' => $mailTemplateParts[1],
			'senderName' => $senderName,
			'replyTo' => $mailTemplateParts[2]
		));
		$check = @mail($params['email'], $partSubject, $mimeMailer->mailBody, $mimeMailer->mailHeader, '-f' . $mailTemplateParts[1]);

		//var_dump($params['email']);
//var_dump($partSubject);
//var_dump($mimeMailer->mailBody);
//var_dump($mimeMailer->mailHeader);
//var_dump($mailTemplateParts[1]);
//var_dump($check);

		//XXX: remove from live , test email content
		//return true;
		//file_put_contents('postbox/'.microtime(true) . '-user-.txt', $mimeMailer->mailBody);
		
		if (!$check) {
			$this->errorNr = 3007;
			//echo "<-- 3007 -->";
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
		
//		if (mb_detect_encoding($mailTemplate, 'UTF-8')){
//			 $mailTemplate = utf8_decode($mailTemplate);
//		}
		
		$mailTemplateParts = (explode($this->splittString, $mailTemplate));
		
		foreach($mailTemplateParts as $key => $value) {
			$mailTemplateParts[$key] = trim($value);
		}
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
		$check = @mail($params['adminEmail'], $partSubject, $mimeMailer->mailBody, $mimeMailer->mailHeader, '-f' . $mailTemplateParts[1]);

		//file_put_contents('postbox/'.microtime(true) . '-admin-.txt', $mimeMailer->mailBody);

		if (!$check) {
			$this->errorNr = 3007;
			return false;
		} else {
			return true;
		}
	}

	/**
	 * public function sendNewPasswordNotification()
	 *
	 * send recovery password to client, client must verify the password changing by clicking the
	 * verify link in password recovery email
	 * @param array params
	 * @return boolen
	 */
	public function sendNewPasswordNotification($params=array()) {
		$defaultParams = array(
			'template' => '',
			'email' => '',
		);

		$params = array_merge($defaultParams, $params);
		// create action hash
		$this->currentHash = $this->createHash();

		//create new password
		$params['newPassword'] = $this->createPassword();
		$params['hash'] = $this->currentHash;

		// save a hash, the new password in subscriber table
		$query = "UPDATE paperboy_subscribers SET action_hash='" . $this->currentHash . "', pass_request='" . $params['newPassword'] . "' WHERE email='" . $params['email'] . "' ";

		$check = $this->db->query($query);
		if (!$this->db->getLastErrorNr()) {
			$this->notifySubscriber($params);
			return true;
		} else {
			return false;
		}
	}

	public function activateNewPassword($params) {
		if (!$params['hash'])
			return false;
		$query = "UPDATE paperboy_subscribers SET pass=pass_request, action_hash='', pass_request='' WHERE action_hash='" . $params['hash'] . "' ";
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
	 * @param mixed newsletterId ID(s) des/der Newsletter, die/der abonniert werden soll/en
	 *
	 * @return bool true oder false, je nachdem ob das Abonnement geklappt hat oder nicht.
	 */
	public function subscribeNewsletter($params=array()) {
// 	 		if (!$this->checkNewsletter($params['newsletterId'])) return false;
		if (!$params['checkOnlyEmail']) {
			if (!$this->checkTitle($params['title']))
				return false;
			
			//if (!$this->checkFirstname($params['firstname']))
			//	return false;
			if (!$this->checkLastname($params['lastname']))
				return false;
		}
		if (!$this->checkEmail($params['email']))
			return false;
		if (!$params['newsletterId']) {
			$params['newsletterId'] = array();
		}
		if (!is_array($params['newsletterId']))
			$params['newsletterId'] = array($params['newsletterId']);

		$subscriberId = $this->createSubscriber($params);


		if ($subscriberId) {

			// 1. Neuen Hash-Wert erzeugen
			$this->createHash();
			$this->db->query("UPDATE paperboy_subscribers SET action_hash = '" . $this->currentHash . "' WHERE id = '" . $subscriberId . "'");
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3004;
			} else {
				// 2. Pr�fen, ob Newsletter schon abonniert sind
				foreach ($params['newsletterId'] as $key => $value) {
					if ($this->newsletterIsSubscribed(array('subscriberId' => $subscriberId, 'newsletterId' => $value))) {
						unset($params['newsletterId'][$key]);
					}
				}

				//3. Newsletter abonnieren
				foreach ($params['newsletterId'] as $key => $value) {
					$this->db->query("INSERT INTO paperboy_distributed SET " .
							"subscriber_id = '" . $subscriberId . "', " .
							"newsletter_id = '" . intval($value) . "', " .
							"is_active = '0'");
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
	 * @param mixed newsletterId ID(s) des/der Newsletter, die/der abonniert werden soll/en
	 *
	 * @return bool true oder false, je nachdem ob der erste Schritt der Abbestellung geklappt hat oder nicht.
	 */
	public function unsubscribeNewsletter($params) {
		if (!$this->checkNewsletter($params['newsletterId']))
			return false;
		if (!$this->checkEmail($params['email']))
			return false;

		if (!is_array($params['newsletterId']))
			$params['newsletterId'] = array($params['newsletterId']);
		$subscriberData = $this->getSubscriberDataFromEmail($params['email']);

		if (empty($subscriberData)) {
			$this->errorNr = 4003;
			return false;
		} else {
			$query = "UPDATE paperboy_subscribers " .
					"SET " .
					"newsletter_queue = '" . $this->db->mysqlQuote(implode(';', $params['newsletterId'])) . "', " .
					"action_hash = '" . $this->createHash() . "' " .
					"WHERE id = '" . $subscriberData['id'] . "'";
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
			$newsletterIds = str_replace(';', ',', $subscriberData['newsletter_queue']);

			$this->db->query("DELETE FROM paperboy_distributed " .
					"WHERE newsletter_id IN (" . $newsletterIds . ")" .
					"AND subscriber_id = '" . $subscriberData['id'] . "'"
			);

			if ($this->db->lastErrorNr) {
				$this->errorNr = 3012;
				return false;
			} else {

				$this->db->query("SELECT COUNT(id) AS subscribedNewsletters " .
						"FROM paperboy_distributed " .
						"WHERE subscriber_id = '" . $subscriberData['id'] . "'"
				);
				$r = $this->db->get(MYSQLI_ASSOC);


				// Z�hlen, wieviele Newsletter vom User noch abonniert sind
				if (!$r['subscribedNewsletters'] && !$subscriberData['is_download_active']) {

					// User l�schen, wenn er keine Newsletter mehr abonniert hat
					$this->db->query("DELETE FROM paperboy_subscribers " .
							"WHERE id = '" . $subscriberData['id'] . "'"
					);
					if ($this->db->lastErrorNr) {
						$this->errorNr = 3014;
						return false;
					} else {
						return true;
					}
				} else {



					// User updaten, wenn er noch Newsletter abonniert hat
					$this->db->query("UPDATE paperboy_subscribers " .
							"SET " .
							"action_hash = '', " .
							"newsletter_queue = '' " .
							"WHERE id = '" . $subscriberData['id'] . "'"
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

		if (!$this->checkEmail($params['email'])) {
			return false;
		} else {

			// 1. Checken, ob User schon registriert ist und nur einen weiteren Newsletter abonnieren m�chte
			$this->db->query("SELECT * FROM paperboy_subscribers WHERE email = '" . $this->db->mysqlQuote(trim($params['email'])) . "'");
			$r = $this->db->get(MYSQLI_ASSOC);

			// Fall 1: E-Mailadresse existiert bereits, aber User ist noch nicht aktiviert, dann Fehler
			if (!empty($r) && !$r['is_active']) {
				/*
				  if($params["subscribeScope"]=="subscribeNewsletter"){
				  return $r['id'];
				  }else{
				 */
				$this->errorNr = 3002;
				return false;
				//}
			}

			
			if ($params["subscribeScope"] != "subscribeNewsletter") {
				// download
				if(!empty($r) && $r['is_download_active']){
					$this->errorNr = 3008;
					return false;
				}
				$this->password = $this->createPassword();
			}else{
			
				// newletter
			}

			// Fall 2: User existiert noch nicht: anlegen
			if (empty($r)) {
				$this->db->query("INSERT INTO paperboy_subscribers SET " .
						"email = '" . $this->db->mysqlQuote(trim($params['email'])) . "', " .
						"title = '" . $this->db->mysqlQuote(trim($params['title'])) . "', " .
						"company = '" . $this->db->mysqlQuote(trim($params['company'])) . "', " .
						"addinfo = '" . $this->db->mysqlQuote(trim($params['addinfo'])) . "', " .
						"firstname = '" . $this->db->mysqlQuote(trim($params['firstname'])) . "', " .
						"lastname = '" . $this->db->mysqlQuote(trim($params['lastname'])) . "', " .
						"pass = '" . $this->password . "', " .
						"date_signed_in = '" . date('Y-m-d H:i:s') . "', " .
						"referer_signed_in = '" . $this->getRefererIp() . "', " .
						//		 							"action_hash = '".$this->createHash()."', " .
						// "is_download_active='0', " .
						"is_active = '0'"
				);

				if ($this->db->lastErrorNr) {
					$this->errorNr = 3003;
					return false;
				} else {
					return $this->db->lastInsertedId();
				}
			} else {
				if ($params["subscribeScope"] == "subscribeNewsletter") {
					return $r['id'];
				} else {
					$query = "UPDATE paperboy_subscribers SET  " .
							//"is_download_active='".$is_download_active."', ".
							"pass = '" . $this->password . "' " .
							"WHERE id='" . $r['id'] . "'";

					$this->db->query($query);
					// Noch kein return, dann existiert USer und ist aktiviert
					return $r['id'];
				}
			}
		}
	}

	/**
	 * function activateNewsletter()
	 * Setzt den Status eines Abonnenten auf 'aktiv'
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
		} else {
			$subscriberId = $subscriberData['id'];

			// Ggf. Benutzer aktivieren
			if ($params['activationAction'] == 'activateDownload') {

				if (!$subscriberData['is_download_active']) {
					$check = $this->activateSubscriber(array('subscriberId' => $subscriberId, 'activationAction' => $params['activationAction']));
					if (!$check)
						return false;
				}
			}
			else {
				if (!$subscriberData['is_active']) {
					$check = $this->activateSubscriber(array('subscriberId' => $subscriberId, 'activationAction' => $params['activationAction']));
					if (!$check)
						return false;
				}
			}

			

			// Newsletter aktivieren
			$this->db->query("UPDATE paperboy_distributed SET is_active = '1' WHERE subscriber_id = '" . $subscriberId . "' AND is_active = '0'");
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3010;
				return false;
			} else {
				$this->deleteHash(array('subscriberId' => $subscriberId));
				return true;
			}
		}
	}

	/**
	 * function activateSubscriber()
	 * Setzt den Status eines Abonnenten auf 'aktiv'
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $hash Hash-Wert, anhand dessen der User ermittelt werden kann
	 * @param number $subscriberId ID des Benutzers
	 *
	 * @return bool Liefert true oder false, je nachdem, ob die Aktivierung erfolgreich war
	 */
	function activateSubscriber($params) {
		
		$subscriberId = intval($params['subscriberId']);

		if (!$subscriberId) {
			$subscriberId = $this->getSubscriberIdFromHash($params['hash']);
		}

		if (!$subscriberId) {
			return false;
		} else {

			if ($params['activationAction'] == 'activateDownload') {
				$activeQuery = "is_active = '1', is_download_active='1', ";
			} else {
				$activeQuery = "is_active = '1',";
			}
			$query = "UPDATE paperboy_subscribers SET " . $activeQuery . " action_hash = '' WHERE id = '" . $subscriberId . "'";

			$this->db->query($query);
			if ($this->db->lastErrorNr) {
				$this->errorNr = 3009;
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * function getSubscriberIdFromHash()
	 * Ermittelt die User-ID anhand eines Hash-Wertes
	 *
	 * @param string $hash Hash-Wert als String
	 * @return Number Liefert die Benutzer-ID zur�ck, ansonsten 0
	 */
	public function getSubscriberIdFromHash($hash) {
		$this->db->query("SELECT id FROM paperboy_subscribers WHERE action_hash = '" . $this->db->mysqlQuote($hash) . "'");
		$r = $this->db->get(MYSQLI_ASSOC);

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
		$this->db->query("SELECT * FROM paperboy_subscribers WHERE action_hash = '" . $this->db->mysqlQuote($hash) . "'");
		$r = $this->db->get(MYSQLI_ASSOC);
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
	 * @param number $subscriberId ID des Nutzers
	 *
	 * @return Number Liefert die Benutzer-Daten zur�ck, ansonsten false
	 */
	public function getSubscriberDataFromId($subscriberId) {
		$this->db->query("SELECT * FROM paperboy_subscribers WHERE id = '" . intval($subscriberId) . "'");
		$r = $this->db->get(MYSQLI_ASSOC);
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

		$this->db->query("SELECT * FROM paperboy_subscribers WHERE email = '" . $this->db->mysqlQuote($email) . "'");
		$r = $this->db->get(MYSQLI_ASSOC);
		if (empty($r)) {
			$this->errorNr = 4003;
			return false;
		} else {
			return $r;
		}
	}

	/**
	 * function subscriberIsActive()
	 * Ermittelt ob ein User-Account aktiviert ist
	 *
	 * @param array $params Parameter werden per Array �bergeben
	 * @param string $hash Hash-Wert...
	 * @param number $subscriberId ...oder ID des Benutzers
	 *
	 * @return mixed 1 oder 0, je nachdem, ob der User aktiviert ist oder nicht, false im Fehlerfall (immer mit === Operator �berpr�fen)
	 */
	public function subscriberIsActive($params) {

		$r = array();

		if ($params['subscriberId']) {
			$r = $this->getSubscriberDataFromId($params['subscriberId']);
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
	 * @param string $newsletterId ID des Newsletters
	 * @param string $subscriberId ID des Benutzers
	 *
	 * @return bool true oder false, je nachdem, ob der Newsletter abonniert ist oder nicht
	 */
	public function newsletterIsSubscribed($params) {
		if (!$params['newsletterId'] || !$params['subscriberId'])
			return false;

		$this->db->query("SELECT * FROM paperboy_distributed WHERE newsletter_id = '" . intval($params['newsletterId']) . "' AND subscriber_id = '" . intval($params['subscriberId']) . "'");
		$r = $this->db->get(MYSQLI_ASSOC);

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
		$newsletterList = array();
		while ($r = $this->db->get(MYSQLI_ASSOC)) {
			$newsletterList[] = $r;
		}
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
	 * @param number $subscriberId ID des Benutzers
	 *
	 * @return bool false, wenn keine User-ID �bergeben wurde, ansonsten true
	 */
	public function deleteHash($params) {
		if (!$params['subscriberId'])
			return false;

		$this->db->query("UPDATE paperboy_subscribers SET action_hash = '' WHERE id = '" . $params['subscriberId'] . "'");
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
	 * @param mixed $newsletterIds Newsletter-IDs entewder als Array oder als Number
	 *
	 * @return boolean true oder false, je nach Ergebnis der Pr�fung
	 */
	public function checkNewsletter($newsletterIds) {
		if (!is_array($newsletterId))
			settype($newsletterIds, 'array');

		if (empty($newsletterIds)) {
			$this->errorNr = 3000;
			return false;
		}

		foreach ($newsletterIds as $nid) {
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

		while ($res = $this->db->get(MYSQLI_ASSOC)) {
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
	 * @param type $params
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


		$query = "SELECT cmt_tablesettings FROM cmt_tables WHERE cmt_include='app_paperboy.php'";
		$this->db->query($query);
		$res = $this->db->get();
		$paperboySettings = safeUnserialize($res['cmt_tablesettings']);

		if (is_array($paperboySettings)) {
			$paperboySettings = array_merge($params, $paperboySettings);
		} else {
			$paperboySettings = $params;
		}
		
		if (is_array($paperboySettings)) {
			return $paperboySettings;
		} else {
			return array();
		}
	}

}

?>
