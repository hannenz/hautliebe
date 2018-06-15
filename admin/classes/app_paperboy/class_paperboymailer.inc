<?php
/**
 * class PaperboyMailer
 * 
 * Contentomat Class to handle E-Mail sending.
 * Based upon [PHPMailer](http://phpmailer.github.io/)
 * 
 * Wraps the old mimemailer class on API level but calls PHPMailer for sending mails
 * 
 * Erzeugt E-Mailstextkörper mit optionalem Text- und HTML-Teil, eingebetteten Bildern und Attachments
 * 
 * @author J.Hahn, J.Braun <info@content-o-mat.de>
 * @version 2018-04-13
 */
namespace Contentomat\Paperboy;

use \Exception;
use \PHPMailer;
use \Contentomat\Debug;
use \Pelago\Emogrifier;


class PaperboyMailer  {

	/**
	 * @var Object 		PHPMailer instance
	 */
	protected $Mail;

	/**
	 * @var Array
	 * 
	 * Mail Setup / Configuration object global, can be overriden by config file `settings_mail.inc` and/or by params passed to Mail constructor.
	 * 
	 * Fields:
	 * charset 				string 			The charset to use, defaults to "utf-8", as passed to PHPMailer::CharSet
	 * senderMail 			String 			defaults to "info@contentomat.de"
	 * senderName 			string 			defaults to "Content-O-Mat Mailer"
	 * replyTo 				string | array	
	 * transport 			string 			"mail" or "smtp", defaults to "mail"
	 * smtpHost 			string 			The SMTP host as passed to PHPMailer::Host
	 * smtpAuth 			boolean 		Whether to use smtp authentication, defaults to true
	 * smtpUsername 		string 			The SMTP username
	 * smtpPassword 		string 			The SMTP password
	 * smtpPort 			integer 		The SMTP port to use, defaults to 25
	 * smtpSecure 			string 			The SMTP secure method, "ssl" or "tls", defaults to "ssl"
	 * 
	 * embedHTMLImages		boolean 		whether or not to embed HTML images
	 * embedHTMLImagesPath 	string 			absolute path to the images, defaults to Content-O-Mat constant INCLUDEPATH
	 */
	protected $config;

	/**
	 * @var String 	Error message, if any
	 */
	protected $errorMessage;

	protected $Debugger;

	public function __construct(){

		//$this->Debugger = new Debug();
		
		// Setup default configuration 
		$this->config = array(
			'charset' => 'utf-8',

			'senderMail' => '',
			'senderName' => '',
			'replyTo' => array(),
			'cc' => array(),
			'bcc' => array(),
			'recipient' => array(),
			'transport' => 'mail',

			// Make sure every key exists, even if not plausible in this configuration (Empty fields)
			'smtpHost' => '',
			'smtpSecure' => 'tls',
			'smtpPort' => 465,
			'smtpAuth' => true,
			'smtpUsername' => '',
			'smtpPassword' => '',

			'embedHTMLImages' => true,
			'embedHTMLImagesPath' => ''
		);

		// Create an instance of the PHPMailer class.
		require_once (INCLUDEPATHTOADMIN . 'classes/PHPMailer/src/Exception.php');
		require_once (INCLUDEPATHTOADMIN . 'classes/PHPMailer/src/SMTP.php');
		require_once (INCLUDEPATHTOADMIN . 'classes/PHPMailer/src/POP3.php');
		require_once (INCLUDEPATHTOADMIN . 'classes/PHPMailer/src/PHPMailer.php');
		$this->Mail = new PHPMailer\PHPMailer\PHPMailer();
		
		// get Emogrifier 
		require_once (INCLUDEPATHTOADMIN . 'classes/Emogrifier/Emogrifier.inc');
		
		
		// prevent PHPMailer from using "X-Mailer: PHPMailer"-Header
		$this->Mail->XMailer = " ";
		
		/*
		 * MIME type definition (TODO: make editable?)
		 * <http://www.w3.org/Protocols/rfc1341/4_Content-Type.html>
		 * 
		 * multipart:
		 *  data consisting of multiple parts of independent data types. Four initial subtypes are defined, 
		 *  including the primary "mixed" subtype, "alternative" for representing the same data in multiple 
		 *  formats, "parallel" for parts intended to be viewed simultaneously, and "digest" for multipart 
		 *  entities in which each part is of type "message".
    	*/ 
		
		$this->Mail->ContentType = "multipart/alternative";
	}
	
	public function setConfiguration($params) {
		
		if (!is_array($params)) {
			return false;
		}
		
		$this->config = array_merge($this->config, $params);
		return true;
		
	}

	/**
	 * protected function sendMail()
	 * Versendet eine E-Mail.
	 *
	 * @param array $params Erwartet die Mailparameter in einem Array:
	 * 'text' => Text-Teil der E-Mail
	 * 'html' => HTML-Teil der E-Mail
	 * 'attachements' => Pfade des/ der Attachements (String oder Array)
	 * 'senderMail' => Absender: Email-Adresse
	 * 'senderName' => Absender: Name
	 * 'replyTo' => E-Mailadresse für Antworten oder Array
	 * 'recipient' => Empfänger: E-Mailadresse oder Array
	 * 'subject' => Betreffzeile
	 *
	 *  Added
	 * 'cc' => E-Mailadresse für Kopie oder Array
	 * 'bcc'=> E-Mailadresse für Blindkopie oder Array
	 *
	 * @return boolean True oder false, je nach Mailversanderfolg.
	 */

	public function send($params = array()) {

		if (!is_array($params)) {
			Debug::log("kein Array");
			return false;
		}

		$params = array_merge($this->config, $params);
		
//		$this->Mail->ContentType = '';

		if (trim(strtolower($params['transport'])) == 'smtp'){
			$this->Mail->isSMTP();
			$this->Mail->Host = $params['smtpHost'];
			$this->Mail->SMTPAuth = $params['smtpAuth'];
			$this->Mail->Username = $params['smtpUsername'];
			$this->Mail->Password = $params['smtpPassword'];
			$this->Mail->SMTPSecure = $params['smtpSecure'];
			$this->Mail->Port = intval($params['smtpPort']);
			
			// @see: https://stackoverflow.com/questions/35604159/phpmailer-not-sending-mails-tls-error
			$this->Mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			); 
		}

		$this->Mail->clearAllRecipients();
		$this->Mail->clearAttachments();

		$this->Mail->From = $params['from'];
		$this->Mail->FromName = $params['fromName'];

		// add recipients
		foreach ((array)$params['recipient'] as $recipient){
			$this->Mail->addAddress($recipient);
		}
		
		// add CC-recipients
		foreach ((array)$params['cc'] as $cc){
			$this->Mail->addCC($cc);
		}
		
		// add BCC-recipients
		foreach ((array)$params['bcc'] as $bcc){
			$this->Mail->addBCC($bcc);
		}
		
		// add "reply to" adress and name
		$this->Mail->addReplyTo($params['replyTo']);

		// add attachments
		foreach ((array)$params['attachments'] as $attachment){
			$this->Mail->addAttachment($attachment);
		}

		// Search all IMG tags, extract the SRC attribute, 
		$imagePaths = $this->extractImagesFromHTML($params['html']);
	
		// embed images?
		if ($params['embedHTMLImages']) {

			// replace them with a CID (content-ID) reference, and add the image as embedded image
			foreach ((array)$imagePaths as $imagePath) {
				$cid = $this->createCID($imagePath);
				
				$params['html'] = str_replace($imagePath, 'cid:'.$cid, $params['html']);
				$this->Mail->addEmbeddedImage($params['embedHTMLImagePathPrefix'].$imagePath, $cid);
				
			}
		} else {
			
			// add URL to image paths
			foreach ((array)$imagePaths as $imagePath) {
				$params['html'] = str_replace($imagePath, $params['remoteImagePath'] . $imagePath , $params['html']);
			}
		}
		
		// inline CSS
		$params['html'] = $this->cleanHTML($params['html']);
		
		$css = $this->getCSS($params['html']);
	
		$emogrifier = new Emogrifier();
		$emogrifier->setHtml($params['html']);
		$emogrifier->setCss($css);
		$emogrifier->disableStyleBlocksParsing();
		$params['html'] = $emogrifier->emogrify();

// 		print_r($params['html']);
// die();
		$this->Mail->CharSet = $params['charset'];
		$this->Mail->Subject = $params['subject'];
		$this->Mail->isHTML(!empty($params['html']));
		
		if (!empty($params['html'])) {
			$this->Mail->Body = $params['html'];
		} else {
			$this->Mail->Body = $params['text'];
		}
		$this->Mail->AltBody = $params['text'];

//		print_r($params['html']);
//		die();
		// Finally send email, catch errors and return success code
		try {
			$success = $this->Mail->send();

			if (!$success){
				throw new Exception ($this->Mail->ErrorInfo);
			}
		}
		catch (Exception $e) {
			$this->errorMessage = $e->getMessage();
			Debug::log($this->errorMessage);
		}

		return $success;
	}

	/**
	 * public getErrorMessage
	 * 
	 * get the message of the last error
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	
	protected function extractImagesFromHTML($html) {
		$pattern = '/<img[^>]* src\s?=("|\'|\s)([^\'"\s]*)("|\'|\s)/Ui';
		$result = preg_match_all($pattern, $html, $match);
		
		if ($result) {
			$imagesFound = $match[2];
	
			return $imagesFound;
		} else {
			return array();
		}
	}
	
	/**
	 * protected function createCID()
	 * Creates a md5 hash of a string for usage as Content-ID in HTML e-mails.
	 * 
	 * @param string $hashBaseString base string for md5 hash
	 * @return string md5 represantion of base string 
	 */
	protected function createCID($hashBaseString) {
		return hash('md5', $hashBaseString);
	}
	
	protected function getCSS($html) {
		
		$regexp = "/\<style.*\>(.*)\<\/style>/Uis";
		preg_match_all($regexp, $html, $matches);
	
		$css = '';
		
		foreach((array)$matches[1] as $match) {
			$css .= $match . "\n";	
		}

		$css = str_replace(array(
			'<!--',
			'-->'
		), array(
			'',
			''
		), $css);
		return trim($css);

	}
	
	protected function cleanHTML($html) {
		
		$html = preg_replace(array('/\<style\>\s?\<\!--/', '/--\>\s?\<\/style\>/'), array('<style>', '</style>'), $html);
		
		return $html;
	}
}
