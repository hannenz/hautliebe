<?php
/**
 * class Mail
 * 
 * Contentomat Class to handle E-Mail sending.
 * Based upon [PHPMailer](http://phpmailer.github.io/)
 * 
 * Wraps the old mimemailer class on API level but calls PHPMailer for sending mails
 * 
 * Erzeugt E-Mailstextkörper mit optionalem Text- und HTML-Teil, eingebetteten Bildern und Attachments
 * 
 * @author J.Hahn, J.Braun <info@content-o-mat.de>
 * @version 2016-09-21
 */
namespace Contentomat;

use \Exception;
use \PHPMailer;
use \Contentomat\Debug;

class Mail  {

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

	public function __construct($config = array()){

		$this->Debugger = new Debug();

		$this->Debugger->log($mailConfig);

		// Setup default configuration 
		$this->config = array(
			'charset' => 'utf-8',

			'senderMail' => 'info@contentomat.de',
			'senderName' => 'Content-O-Mat Mailer',
			'replyTo' => array(),
			'cc' => array(),
			'bcc' => array(),

			'transport' => 'mail',

			// Make sure every key exists, even if not plausible in this configuration (Empty fields)
			'smtpHost' => '',
			'smtpSecure' => 'tls',
			'smtpPort' => 25,
			'smtpAuth' => true,
			'smtpUsername' => '',
			'smtpPassword' => '',

			'embedHTMLImages' => true,
			'embedHTMLImagesPath' => INCLUDEPATH
		);

		// Read configuration from config file, if present;
		$mailConfig = array();
		if (file_exists(INCLUDEPATHTOADMIN . 'settings_mail.inc')){
			@include (INCLUDEPATHTOADMIN . 'settings_mail.inc');
		}
		// override with settings from config file
		$this->config = array_merge($this->config, $mailConfig);
		// override with user settings passed to constructor
		$this->config = array_merge($this->config, $config);

		// Create an instance of the PHPMailer class.
		require_once (INCLUDEPATHTOADMIN . 'classes/PHPMailer/PHPMailerAutoload.php');
		$this->Mail = new PHPMailer;
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

	public function send($params = array()){

		$config = array_merge($this->config, $params);
		$config = array_merge(array(
			'cc' => array(),
			'bcc' => array(),
			'replyTo' => array(),
			'recipient' => array(),
		), $config);

		if (trim(strtolower($config['transport'])) == 'smtp'){
			$this->Mail->isSMTP();
			$this->Mail->Host = $config['smtpHost'];
			$this->Mail->SMTPAuth = $config['smtpAuth'];
			$this->Mail->Username = $config['smtpUsername'];
			$this->Mail->Password = $config['smtpPassword'];
			$this->Mail->SMTPSecure = $config['smtpSecure'];
			$this->Mail->Port = $config['smtpPort'];
			
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

		$this->Mail->From = $config['senderMail'];
		$this->Mail->FromName = $config['senderName'];

		foreach ((array)$config['recipient'] as $recipient){
			$this->Mail->addAddress($recipient);
		}
		foreach ((array)$config['cc'] as $cc){
			$this->Mail->addCC($cc);
		}
		foreach ((array)$config['bcc'] as $bcc){
			$this->Mail->addBCC($bcc);
		}
		foreach ((array)$config['replyTo'] as $replyTo){
			$this->Mail->addReplyTo($replyTo);
		}
		foreach ((array)$config['attachments'] as $attachment){
			$this->Mail->addAttachment($attachment);
		}

		if ($config['embedHTMLImages']){

			/* Search all IMG tags, extract the SRC attribute, replace them with a CID (content-ID) reference,
			 * and add the image as embedded image */
			$config['html'] = preg_replace_callback(
				'/img\s+src\s*\=\s*[\'\"](.*?)[\'\"]/i', 
				function($match){

					$imageTag = $match[0];
					$imagePath = $match[1];

					$cid = hash('md5', $imagePath);

					$this->Mail->addEmbeddedImage($config['embedHTMLImagesPath'] . $imagePath, $cid);
					return str_replace($imagePath, 'cid:'.$cid, $imageTag);
				},
				$config['html']
			);
		}

		$this->Mail->CharSet = $config['charset'];
		$this->Mail->Subject = $config['subject'];
		$this->Mail->isHTML(!empty($config['html']));
		$this->Mail->Body = !empty($config['html']) ? $config['html'] : $config['text'];
		$this->Mail->AltBody = $config['text'];


		// Finally send email, catch errors and return success code
		try {
			$success = $this->Mail->send();
			if (!$success){
				throw new Exception ($this->Mail->ErrorInfo);
			}
		}
		catch (Exception $e) {
			$this->errorMessage = $e->getMessage();
		}
		return $success;
	}


	/**
	 * public sendMail
	 * 
	 * Backwards compatibility to MimeMailer class
	 * 
	 * @see \Contentomat\Mail::send
	 */
	public function sendMail($params=array()) {
		$this->send($params);
	}

	/**
	 * public getErrorMessage
	 * 
	 * get the message of the last error
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}
}
