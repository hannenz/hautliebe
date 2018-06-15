<?php
/**
 * class_paperboy.inc
 * Provides methods for sending bulk email newsletters (used by app_paperboy.inc).
 * 
 * @author J.Hahn
 * @version 2018-04-13
 *
 */

namespace Contentomat\Paperboy;

use Contentomat\DBCex;
use Contentomat\Contentomat;
//use Contentomat\ApplicationHandler;
use Contentomat\Parser;
//use Contentomat\Mimemailer;
use Contentomat\Debug;

class Paperboy {

	protected $db;
	protected $parser;
	protected $mailer;
	protected $subscriptionHandler;
	protected $cmt;
	protected $deliveryErrors;

	/**
	 * Konstruktor
	 */
	public function __construct($params = array()) {
		$this->db = new DBCex();
		$this->parser = new Parser();
		$this->mailer = new PaperboyMailer();
		$this->subscriptionHandler = new SubscriptionHandler();
		$this->cmt = Contentomat::getContentomat();
		
//		$this->setTransportType($params['transportType']);
	}
	
	/**
	 * public function setMailerConfiguration()
	 * Passes the paperboy configuration through to the mailer class
	 * 
	 * @param array $params Associative Array of configuration values
	 * @return boolean Success or failure of setting action
	 */
	public function setMailerConfiguration($params) {
		
		if (!is_array($params)) {
			return false;
		}
		
		return $this->mailer->setConfiguration($params);
	}
	
	public function getNewsletters($overrideStatus = false) {
		if ($overrideStatus) {
			$this->db->query("SELECT * FROM paperboy_newsletters ORDER BY newsletter_name");
		} else {
			$this->db->query("SELECT * FROM paperboy_newsletters WHERE is_active = '1' ORDER BY newsletter_name");
		}

		$data = $this->db->getAll();
		$list = array();
		
		foreach ($data as $nl) {
			$data[$nl['id']] = $nl['newsletter_name'];
		}
		return $data;
	}
	
	public function getRecipientLists() {
		
		$this->db->query("SELECT * FROM paperboy_newsletters WHERE is_active = 1 ORDER BY newsletter_name");

		$data = $this->db->getAll();
		$list = array();
		
		foreach ($data as $nl) {
			$data[$nl['id']] = $nl['newsletter_name'];
		}
		return $data;
	}
	
	public function getTemplates() {
		$this->db->query("SELECT * FROM paperboy_templates ORDER BY template_name");
	
		$data = $this->db->getAll();
		//$list = array();
	
		foreach ($data as $nl) {
			$data[$nl['id']] = $nl['template_name'];
		}
		return $data;
	}

	public function getTemplate($templateID) {
		
		$templateID = intval($templateID);
		if (!$templateID) {
			return array();
		}
		$this->db->query("SELECT * FROM paperboy_templates WHERE id = '" . $templateID ."'");
	
		$data = $this->db->get();

		return $data;
	}
	
	public function getHTMLTemplate($templateID) {
		$templateID = intval($templateID);
		
		$data = $this->getTemplate($templateID);
		
		if (isset($data['template_html_doc'])) {
			return $data['template_html_doc'];
		} else {
			return '{HTML_DOCUMENT_BODY}';
		}
	}
	
	public function getTestRecipients() {
		
		$cmt = Contentomat::getContentomat();
		$settings = $cmt->getVar('applicationSettings');
		$recipients = str_replace(array(
			"\n",
			"\r",
			','
		), array(
			';',
			'',
			';'
		), 	$settings['newsletterTestrecipient']);
		
		return explode(';', $recipients);
	}
	
	/**
	 * Convenience method: Decides if newsletter should be sent to a bunch of recipients or a subscriber list.
	 * 
	 * @param array $params
	 * @return number	Returns the values of methods Paperboy::sentToRecipient() or Paperboy::sendToList()
	 */
	public function sendMail($params = array()) {

		if (!empty($params['newsletterTo'])) {
			return $this->sendToRecipient($params);
		} else {
			return $this->sendToList($params);
		}

	}
	
	/**
	 * Send newsletter mail to a bunch of recipients (manually entered in Paperboy)
	 * 
	 * @param array $params
	 * @return number	0 or 1 ("error" or "sent successfully")
	 */
	public function sendToRecipient($params) {
		
		// create "to" recipients
		(array)$params['newsletterTo'] = $this->createRecipientsArray($params['newsletterTo']);
			
		//create "cc" recipients
		(array)$params['newsletterCC'] = $this->createRecipientsArray($params['newsletterCC']);
			
		//create "bcc" recipients
		(array)$params['newsletterBCC'] = $this->createRecipientsArray($params['newsletterBCC']);

		// add the HTML body to $newsletterHTML
		$params['newsletterHTML'] = $this->addHTMLBody($params['newsletterHTML'], $params['newsletterTemplateID']);
		
		$this->parser->deleteAllParserVars();
			
		$check = $this->mailer->send(array (
			'html' => $this->parser->parse($params['newsletterHTML']),
			'text' => $this->parser->parse($params['newsletterText']),
			'attachments' => $params['newsletterAttachments'],
			'from' => $params['newsletterSenderEmail'],
			'fromName' => $params['newsletterSenderName'],
			'replyTo' => $params['newsletterReplyTo'],
			'embedHTMLImages' => $params['embedHTMLImages'],
			'embedHTMLImagePathPrefix' => $this->cmt->getPathToWebroot(),
			'remoteImagePath' => $params['remoteImagePath'],
			'recipient' => $params['newsletterTo'],
			'cc' => $params['newsletterCC'],
			'bcc' => $params['newsletterBCC'],
			'subject' => $this->parser->parse($params['newsletterSubject']),
			'charset' => 'utf-8'
		));

		// Bei Fehler speichern und weitermachen
		if (!$check) {
			$this->logDeliveryError(array(
				'newsletterID' => intval($params['newsletterID']),
				'subscriberID' => 0,
				'subscriberEmail' => implode('; ', (array)$params['newsletterTo'])
			));
			
			$this->deliveryErrors++;
				
			return 0;
		}
			
		return 1;
		
	}

	/**
	 * Send newsletter to all members of a subscriber list.
	 * 
	 * @param array $params
	 * @return number	Number of successfully sent newsleters in one sending intervall (total recipients in intervall minus errors)
	 */
	public function sendToList($params) {
		
		$this->deliveryErrors = 0;
		$deliveryInterval = count($params['newsletterRecipients']);
		
		// add the HTML body to $newsletterHTML
		$params['newsletterHTML'] = $this->addHTMLBody($params['newsletterHTML'], $params['newsletterTemplateID']);
		
		foreach ((array)$params['newsletterRecipients'] as $newsletterRecipient) {
		
			$recipientData = $this->subscriptionHandler->getSubscriberDataFromEmail($newsletterRecipient);
		
			$this->parser->deleteAllParserVars();
			$this->parser->setMultipleParserVars($recipientData);
		
			$check = $this->mailer->send(array (
					'html' => $this->parser->parse($params['newsletterHTML']),
					'text' => $this->parser->parse($params['newsletterText']),
					'attachments' => $params['newsletterAttachments'],
					'from' => $params['newsletterSenderEmail'],
					'fromName' => $params['newsletterSenderName'],
					'replyTo' => $params['newsletterReplyTo'],
					'embedHTMLImages' => $params['embedHTMLImages'],
					'embedHTMLImagePathPrefix' => $this->cmt->getPathToWebroot(),
					'remoteImagePath' => $params['remoteImagePath'],
					'recipient' => $newsletterRecipient,
					'subject' => $this->parser->parse($params['newsletterSubject']),
					'charset' => 'utf-8'
			));
		
			// Bei Fehler speichern und weitermachen
			if (!$check) {
				$this->logDeliveryError(array(
						'newsletterID' => intval($params['newsletterID']),
						'subscriberID' => $recipientData['id'],
						'subscriberEmail' => $recipientData['email']
				));
		
				$this->deliveryErrors++;
			}
		
		}
		
		return $deliveryInterval - $this->deliveryErrors;
		
	}	
	
	public function getDeliveryErrors() {
		return $this->deliveryErrors;
	}
	
	/**
	 * public function createRecipientsArray()
	 * Creates an array out of a string of e-mail addresses. Due to backwards compatibility reasons the addresses can be passed separated by a ";" or a line break;
	 * 
	 * @param string $recipients
	 * @return array
	 */
	public function createRecipientsArray($recipients) {
		
		if (is_array($recipients)) {
			return $recipients;
		}
		$recipients = str_replace(array("\n", "\r", ' '), array(";", '', ''), trim($recipients));
		
		$recipients = explode(';', $recipients);
		
		// explode fills the first element of an array with an empty string: $recipients[0] => '' so return a realy empty array if no adresses are found.
		if (empty($recipients[0])) {
			return array();
		} else {
			return $recipients;
		}
	}
	
	public function addHTMLBody($content, $newsletterTemplateID) {
// 		$frameTemplate = $this->getHTMLTemplate(intval($newsletterTemplateID));
// 		return $frameTemplate;
// 		return str_replace('{HTML_DOCUMENT_BODY}', $content, $frameTemplate);
		
		$frameTemplate = $this->getHTMLTemplate(intval($newsletterTemplateID));
		
		$this->parser->setParserVar('content', $content);
		return $this->parser->parse($frameTemplate);
	}
		
	protected function logDeliveryError($data=array()) {
		
		$defaultData = array(
			'subscriberID',
			'newsletterID',
			'subscriberEmail'
		);
		
		//Ermittlung der Newsletter-Tabellen-ID, bei der abgebrochen wurde
		$subid = $email['subscriber_id'];
		$query = "INSERT INTO paperboy_errorlog SET subscriber_id = '" . intval($data['subscriberID']) . "', " .
				"newsletter_id = '" . intval($data['newsletterID']) . "', " .
				"error_email = '" . $this->db->dbQuote($data['subscriberEmail']) . "'," .
				"error_datetime = '" . date('Y-m-d H:i:s') . "'";
		$check = $this->db->query($query);

		return !(boolean)$check;
	}
	
	public function getNewsletterRecipients($newsletterID, $startAt = 0, $nr = 0) {
		
		$newsletterID = intval($newsletterID);
		$startAt = intval($startAt);
		$nr = intval($nr);
		
		$addQuery = '';
		
		if ($nr) {
			$addQuery = ' LIMIT ' . $startAt . ',' . $nr;
		}
		
		$query =	"SELECT paperboy_distributed.newsletter_id, paperboy_subscribers.*, paperboy_distributed.id, paperboy_distributed.subscriber_id
					FROM paperboy_distributed
					INNER JOIN paperboy_subscribers ON paperboy_distributed.subscriber_id = paperboy_subscribers.id
					WHERE paperboy_distributed.newsletter_id='" . $newsletterID . "'
					AND paperboy_distributed.is_active = '1'
					GROUP BY paperboy_subscribers.email
					ORDER BY paperboy_distributed.id ASC " .
					$addQuery;
		
		$this->db->query($query);
		$recipients = array();
		
		while($r = $this->db->get()) {
			$recipients[] = $r['email'];
		}
		return $recipients;
	}
	
	public function getTotalSubscribers($newsletterID) {
		
		$query = "SELECT COUNT(*) AS newsletterSubscribers FROM paperboy_distributed " .
				"WHERE newsletter_id = '" . intval($newsletterID) . "' AND is_active = '1'";
		
		$this->db->query($query);
		$r = $this->db->get();
		
		return intval($r['newsletterSubscribers']);
	}
// neccessary?



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

	public function getNewsletter($newsletterID) {
		
		$newsletterID = intval($newsletterID);
		$query = "SELECT * FROM paperboy_newsletters WHERE id = '" . $newsletterID . "'";
	
		$this->db->query($query);
		return $this->db->get();
	}

	/**
	 * 	public function getPaperboySettings()
	 * 
	 * @param array $params
	 * @return array, paperboy table settings
	 */
	public function getPaperboySettings($params = array()) {
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

	public function getTotalNewsletterDistributed($nid) {

		$query = "SELECT count(*) as cr 
					FROM paperboy_distributed 
					WHERE newsletter_id = '$nid' AND is_active = '1'";
		// Ausf�hren der Abfrage
		$this->db->Query($query);

		//Ergebnis zuweisen
		$r = $this->db->Get(MYSQLI_ASSOC);


		//Ergebnisarray der Variablen $totalNewsletters zuweisen
		$totalNewsletters = $r['cr'];

		return $totalNewsletters;
	}

	/**
	 * return archived newsleter (to resend)
	 * 
	 * @param number $id
	 * @return array 
	 */
	public function getArchivedNewsletter($id) {
		$query = "SELECT * FROM paperboy_archived	WHERE id ='" . $id . "' ";

		$this->db->Query($query);

		$res = $this->db->Get(MYSQLI_ASSOC);

		if (is_array($res) && !empty($res)) {
			return $res;
		} else {
			return array();
		}
	}

}

?>
