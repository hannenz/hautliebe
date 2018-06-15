<?php

/**
 * class mimemailer
 * Erzeugt E-Mailstextkörper mit optionalem Text- und HTML-Teil, eingebetteten Bildern und Attachments
 * 
 * @author J.Hahn, J.Braun <info@content-o-mat.de>
 * @version 2015-01-27
 * 
 * TODO: Check different charsets and correct utf8 support!
 * 
 */

namespace Contentomat;

class Mimemailer {

	protected $embeddedImages = array();		// internal array: contains image names and content ids for HTML part
	protected $embedHTMLImages = true;			// flag, embed images from HTML part in mailbody or not
	protected $remoteImagePath = '';			// URL prefix for not embeded images in HTML part

	public $attachedFiles = array();			// internal array: contains attached file paths

	protected $boundary;						// string used as part boundary in the mail body
	protected $partboundary;
	protected $partboundary2;

	public $mailBody = '';
	public $mailHeader = '';
	public $mailAttachments = '';

	protected $headers = array();				// internal array for mail headers
	public $headersAdd = array();				// array for additional headers added by the user

	protected $hasText = false;
	protected $hasHtml = false;
	protected $hasImages = false;
	protected $hasAttachments = false;

	public $eol = "\n";							// end of line char

	private $textPartCharset = 'ISO-8859-1';	// charset of text part
	private $htmlPartCharset = 'ISO-8859-1';	// charset of HTML part
	

	
	
	/**
	 * function __construct()
	 * Konstruktor
	 */
	public function __construct() {
		$this->boundary = $this->createBoundary();
		$this->partboundary = $this->createBoundary();
		$this->partboundary2 = $this->createBoundary();
	}

	/**
	 * Die Methode createBoundary() erzeugt einen timestamp.
	 * Der timestamp, der zurückgegeben wird, dient als Abgrenzung zwischen einzelnen Bereichen, wie z.B. HTML und Attachments.
	 */
	public function createBoundary() {
		static $bc = 0;
		return $bc++ . '_' . strtoupper(md5(uniqid('', true)));
	}

	//public function createContentId()
	/**
	 * Die Methode createContentId() erzeugt eine Content_ID als timestamp.
	 * Der timestamp, der zurückgegeben wird, dient als Abgrenzung zwischen den eingebundenen Bildern.
	 */
	public function createContentId() {
		foreach ($this->embeddedImages as $imageName => $imageCid) {
			$this->embeddedImages[$imageName] = md5(uniqid('', true));
		}
		return;
		
	}

	/**
	 * Die Methode extractImagesFromHtml() liest den ImageFileNamen aus dem übergebenen Html-String aus 
	 * Die Methode nimmt den Parameter $html entgegen und gibt das Array $imagesFound zurück
	 * @param string $html
	 * @return array
	 */
	protected function extractImagesFromHtml($html) {
		$pattern = '/<img[^>]* src\s?=("|\'|\s)([^\'"\s]*)("|\'|\s)/Ui';
		$result = preg_match_all($pattern, $html, $match);
		if ($result) {
			$imagesFound = array_flip($match[2]);
			$this->hasImages = true;
			$this->embeddedImages = $imagesFound;

			return $imagesFound;
		}
	}

	/**
	 * Die Methode createTextPart() erstellt die komplette Plaintext-Mail mit der übergebenen Textvariablen 
	 * Die Methode nimmt den Parameter $plainText entgegen und gibt $resulttext zurück
	 * @param string $plainText
	 * @return string
	 */
	function createTextPart($text) {
		if (empty($text))
			return false;

		// Angabe des MIME-type
		$part .= "Content-Type: text/plain;" . $this->eol;
		$part .= "\tcharset=" . '"' . $this->textPartCharset .'"' . $this->eol;

		// Angabe der Codierung        
		$part .= "Content-Transfer-Encoding: quoted-printable" . $this->eol;

		$textEncoding = mb_detect_encoding($text);
		
		// If encoding can't be detected, do nothing.. (since re-encoding can - and will - cause empty and/or garbled strings)!!
		if ($textEncoding !== false){
			if (strtolower($textEncoding) != strtolower($this->textPartCharset)) {
				$convText = iconv($textEncoding, $this->textPartCharset. '//TRANSLIT', $text);
				if (!empty($convText)){
					$text = $convText;
				}
			}
		}
		
		// Textteil: zwei Absätze davor!
		$part .= $this->eol . $this->quotedPrintableEncode(trim($text)) . $this->eol;

		$this->hasText = true;

		return $part;
	}

	//public function createHtmlPart($html)
	/**
	 * Die Methode createHtmlPart() setzt die HTML-Mail zusammen
	 * Die Methode nimmt den Parameter $html entgegen und gibt $part zurück
	 * @param string $html
	 * @return string
	 */
	public function createHtmlPart($html='') {

		if (empty($html)) {
			return '';
		}
			
		$textEncoding = mb_detect_encoding($html);
		
		if ($textEncoding !== false){
			if (strtolower($textEncoding) != strtolower($this->htmlPartCharset)) {
				$convHTML = iconv($textEncoding, $this->htmlPartCharset. '//TRANSLIT', $html);
				if (!empty($convHTML)){
					$html = $convHTML;
				}
			}
		}	
		
		// Angabe des MIME-type
		$part .= 'Content-Type: text/html;' . $this->eol;
		$part .= "\tcharset=". $this->htmlPartCharset . $this->eol;

		// Angabe der Codierung
		$part .= 'Content-Transfer-Encoding: quoted-printable' . $this->eol . $this->eol;
		$partHtml = $html;

		// Images in HTML part? Embed them?

		if ($this->hasImages && $this->embedHTMLImages) {
			$partImages = $this->createImages($html);

			foreach ($this->embeddedImages as $imageName => $imageCid) {
				// Innerhalb von $part die Dateinamen austauschen
				$pattern = '/src\s?=("|\'|\s)' . preg_quote($imageName, '/') . '("|\'|\s)/Ui';
				preg_match($pattern, $html, $match);

				//Ersetze im kompletten $match den $imageName durch cid:"$imageCid"
				$imageReplaced = str_replace($imageName, 'cid:' . $imageCid, $match[0]);

				//Ersetze die eben durchgeführte Änderung in dem ursprünglichen $match und weise dies dann $part zu
				$html = str_replace($match[0], $imageReplaced, $html);
			}
		} else if ($this->hasImages) {
			// Images: yes. Embed: no!
			
			if (!$this->remoteImagePath) {
				$this->remoteImagePath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
			}

			foreach ($this->embeddedImages as $imageName => $imageCid) {
				
				// Innerhalb von $part die Dateinamen austauschen
				$pattern = '/src\s?=("|\'|\s)' . preg_quote($imageName, '/') . '("|\'|\s)/Ui';
				preg_match($pattern, $html, $match);
			
				// cleanup image name
				$cleanImageName = preg_replace('/^\/{1,}|(\.\.\/(\/)?){1,}/', '', $imageName);
				$imageReplaced = str_replace($imageName, $this->remoteImagePath . $cleanImageName, $match[0]);
			
				//Ersetze die eben durchgeführte Änderung in dem ursprünglichen $match und weise dies dann $part zu
				$html = str_replace($match[0], $imageReplaced, $html);
			}
		}
		
		// TODO: Ist "quoted-printable richtig, wenn der Zeichensatz auf utf- gesetzt wird???
		$html = $this->quotedPrintableEncode($html);

		return $part . $html . $partImages;
	}

	function quotedPrintableEncode($input) {

		$lineMaxLength = 76;
		$lines = preg_split("/\r?\n/", $input);
		$escape = '=';
		$output = '';

		//while (list(, $line) = each($lines)) {
		foreach ($lines as $line) {

			$line = preg_split('||', $line, -1, PREG_SPLIT_NO_EMPTY);
			$linlen = count($line);
			$newline = '';

			for ($i = 0; $i < $linlen; $i++) {
				$char = $line[$i];
				$dec = ord($char);

				if (($dec == 32) AND ($i == ($linlen - 1))) {	// convert space at eol only
					$char = '=20';
				} elseif (($dec == 9) AND ($i == ($linlen - 1))) {  // convert tab at eol only
					$char = '=09';
				} elseif ($dec == 9) {
					; // Do nothing if a tab.
				} elseif (($dec == 61) OR ($dec < 32 ) OR ($dec > 126)) {
					$char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
				} elseif (($dec == 46) AND ($newline == '')) {
					//Bug #9722: convert full-stop at bol
					//Some Windows servers need this, won't break anything (cipri)
					$char = '=2E';
				}

				if ((strlen($newline) + strlen($char)) >= $lineMaxLength) {		// MAIL_MIMEPART_CRLF is not counted
					$output .= $newline . $escape . $this->eol;					// soft line break; " =\r\n" is okay
					$newline = '';
				}
				$newline .= $char;
			}
			$output .= $newline . $this->eol;
		}
		$output = substr($output, 0, -1 * strlen($this->eol)); // Don't want last crlf
		return $output;
	}

	
	/**
	 * public function encodeSubject()
	 * 
	 * encode subject string to quoted printable format
	 * @param string $subject, subject string can be iso or utf-8, with or withour white spaces
	 * @param string $charSet, charset of string to send
	 * @return string 
	 */

	public function encodeSubject($subject, $charSet='ISO-8859-1', $type='quoted') {

		// TODO: Das hier müsste eigentlich rein, wenn aber das template auch nur ein MB-Zeichen enthält
		// aber ISO codiert ist, dann erscheinen statt Sonderzeichen nur "?"
//		 if (mb_detect_encoding($subject, 'UTF-8')){
//			 $subject = utf8_decode($subject);
//		 }
		 
		 if (!$charSet) {
		 	$charSet = 'ISO-8859-1';
		 }
		 
		// Secure Subject Text
		$subject = trim(str_replace(array("\r", "\n"), '', $subject));
		
		switch($type) {
			
			case 'binary':

				$encodedSubject = base64_encode($subject);
				$encodedSubject = trim('=?' . $charSet .'?B?' . $encodedSubject . '?=');
				
				break;
				
			case 'quoted':
			default:
				
				$subject = str_replace(' ', '_', $subject);
		
				//encode Subject 
				$encodedSubject = $this->quotedPrintableEncode($subject);
				$encodedSubject = trim('=?' . $charSet .'?Q?' . $encodedSubject . '?=');
				
				break;
		}

		return $encodedSubject; 
	}
	


	//public function createImages()
	/**
	 * Die Methode createImages() verbindet die integrierten Bilder mit dem HTML-Bereich.
	 * Die Methode gibt $part zurück
	 * @return string
	 */
	protected function createImages($html='') {
		// Content-IDs für alle embedded-Files generieren
		$this->createContentId();

		$part = $this->eol . $this->eol;

		//wenn Html und Plaintext existieren, aber keine Attachments
		if ($this->hasText && !$this->hasAttachments) {
			$part .= $this->addBoundary($this->partboundary) . '--' . $this->eol . $this->eol;
			$part .= $this->addBoundary() . $this->eol;
		}

		//wenn Html und Plaintext und Attachments existieren	        
		if ($this->hasText && $this->hasAttachments) {
			$part .= $this->addBoundary($this->partboundary2) . '--' . $this->eol . $this->eol;
			$part .= $this->addBoundary($this->partboundary) . $this->eol;
		}

		//wenn Html existiert aber kein Plaintext 	        
		if (!$this->hasText && $this->hasAttachments) {
			//$part .= $this->addBoundary() . $this->eol;
			$part .= $this->addBoundary($this->partboundary) . $this->eol;
		}

		//wenn nur Html existiert aber kein Plaintext 	        
		if (!$this->hasText && !$this->hasAttachments) {
			$part .= $this->addBoundary() . $this->eol;
		}

		// Bilder einbauen
		$lastItem = end($this->embeddedImages);

		foreach ($this->embeddedImages as $imageName => $imageCid) {
			//Funktionsaufruf zur Überprüfung der Dateiextension
			$extension = $this->getExtension($imageName);

			switch ($extension) {
				case 'gif':
					$part .= 'Content-Type: image/gif' . $this->eol;
					break;

				case 'jpg':
				case 'jpeg':
					$part .= 'Content-Type: image/jpeg' . $this->eol;
					break;

				case 'png':
					$part .= 'Content-Type: image/png' . $this->eol;
					break;
			}

			$part .= 'Content-Transfer-Encoding: base64' . $this->eol;
			$part .= "Content-Disposition: inline;" . $this->eol . "\tfilename=\"embedded_" . basename($imageName) . "\"" . $this->eol;
			$part .= 'Content-ID: <' . $imageCid . '>' . $this->eol . $this->eol;
			$part .= $this->encodeFile($imageName) . $this->eol . $this->eol;


			if ($lastItem == $imageCid) {
				$addBoundary = '--';
			} else {
				$addBoundary = '';
			}
			//Ist das embedded image nicht das letzte des Arrays muss das boundary gesetzt werden
			if ($this->hasText && !$this->hasAttachments) {
				$part .= $this->addBoundary() . $addBoundary . $this->eol;
			}

			// Falls Kein Text (vor HTML-Teil) und keine Attachments (nach HTML-Teil) vorhanden ist,
			// und es sich nicht um die letzte Grafik handelt (abschließendes Boundary wird in der Hauptmethode gesetzt)
			if (!$this->hasText && !$this->hasAttachments && $lastItem != $imageCid) {
				$part .= $this->addBoundary() . $addBoundary . $this->eol;
			}

			if ($this->hasText && $this->hasAttachments) {
				$part .= $this->addBoundary($this->partboundary) . $addBoundary . $this->eol;
			}

			if (!$this->hasText && $this->hasAttachments) {
				$part .= $this->addBoundary($this->partboundary) . $addBoundary . $this->eol;
			}
		}

		return $part;
	}

	/**
	 * Die Methode getExtension() prüft die embedded Images auf die Datei-Extension Die Methode nimmt den Parameter $imageName entgegen und gibt $extension zurück
	 * @param string $imageName
	 * @return string
	 */
	protected function getExtension($imageName) {
		$imageNameParts = explode('.', $imageName);
		return strtolower($imageNameParts[count($imageNameParts) - 1]);
	}

	//public function createAttachments()
	/**
	 * Die Methode createAttachments() erstellt den Content-Type für Attachments und fügt den Dateinamen des Attachments ein.
	 * Die Methode gibt $part zurück.
	 * @return string
	 */
	protected function createAttachments() {
		$lastFile = end($this->attachedFiles);

		foreach ($this->attachedFiles as $fileName) {
			$part .= 'Content-Type: application/octet-stream' . $this->eol;
			$part .= 'Content-Transfer-Encoding: base64' . $this->eol;
			$part .= 'Content-Disposition: attachment;' . $this->eol . "\tfilename=\"" . basename($fileName) . '"' . $this->eol . $this->eol;
			// Jetzt folgt die BASE64-codierte Datei 
			$part .= $this->encodeFile($fileName) . $this->eol;
			//Folgen weitere Attachments, darf das boundary nur die einleitenden "--" haben.
			//Handelt es sich um das letzte Attachment, dann muss vor und hinter boundary ein "--" stehen
			if ($fileName == $lastFile) {
				// Kann man sich event sparen, wenn die Attachments immer am ende stehen
//					$part .= $this->addBoundary() . '--';
			} else {
				if ($this->hasImages) {
					//$part .= $this->addBoundary($this->partboundary) . $this->eol;
					$part .= $this->addBoundary() . $this->eol;
				} else {
					$part .= $this->addBoundary() . $this->eol;
				}
			}
		}
		return $part;
	}

	/**
	 * Die Methode encodeFile() liest die embedded Imagedatei bzw das Attachment ein und prüft, ob sie lesbar ist
	 * Die Methode nimmt den Parameter $fileName entgegen und gibt im Erfolgsfall $resultfile zurück
	 * @param string $fileName
	 * @return string
	 */
	protected function encodeFile($fileName) {
		//basename extrahiert den Namen einer Datei aus einer vollständigen Pfadangabe 
		//$filename = basename( $imageName ); 
		// ...existiert die Datei überhaupt und ist sie lesbar?
		
		if (is_file($fileName) && is_readable($fileName)) {
			
			// ja, also wird sie geöffnet... 
			$fp = fopen($fileName, 'rb');
			if ($fp) {

				// ...und in eine Variable ($buffer) eingelesen, 
				$buffer = fread($fp, filesize($fileName));
				

				// dann BASE64 codiert, weil es sich um eine binäre Daten handelt  
				$buffer = base64_encode($buffer);
				// und auf 72 Zeichen pro Zeile gestutzt. 
				$buffer = chunk_split($buffer, 72, $this->eol);
				// Die Datei wird wieder geschlossen 
				fclose($fp);

				return $buffer;
			}
		}
	}

	/**
	 * protected function addAttachment() Fügt eine Abschnittsmarkierung (boundary) an den Maillörper
	 * 
	 * @param mixed $attachment Pfad oder Pfade (dann array) der dateien, die angehängt werden sollen
	 * @return void
	 */
	public function addAttachment($a) {
		if (!is_array($a)) {
			$attachments = array($a);
		} else {
			$attachments = $a;
		}
		if (!empty($attachments)) {
			foreach ($attachments as $a) {
				$a = trim($a);
				if ($a) {
					$this->attachedFiles[] = $a;
					$this->hasAttachments = true;
				}
			}
		}
	}

	/**
	 * protected function addBoundary() 
	 * Fügt eine Abschnittsmarkierung (boundary) an den Maillörper
	 * 
	 * @param string $boundary Optional: Abschnittstrennungszeichenfolge
	 * @return void
	 */
	public function addBoundary($boundary='') {
		if (!$boundary)
			$boundary = $this->boundary;
		return '--' . $boundary;
	}

	/**
	 * protected function addStandardHeader()
	 * Fügt dem Array $header einen Eintrag hinzu. Diese Methode wird nur von der Klasse verwendet
	 * 
	 * @param string $headerName Name des Headers, z.B. "From"
	 * @param string $headerValue Wert des Headers
	 * @return void
	 */
	protected function addStandardHeader($headerName, $headerValue) {
		$this->headers[trim($headerName)] = trim($headerValue);
	}

	/**
	 * public function addHeader() 
	 * Fügt dem Array $headerAdd einen Eintrag hinzu. Diese Methode kann vom Nutzer verwendet werden
	 * 
	 * @param string $headerName Name des Headers, z.B. "From"
	 * @param string $headerValue Wert des Headers
	 * @return boolean true oder false
	 */
	public function addHeader($headerName, $headerValue) {
		$h = strtolower(trim($headerName));

		// 'To' wird in der PHP-Mailfunktion abgefrühstückt
		if ($headerName == 'to')
			return false;

		// Suhosin-Patch erlaubt keine zwei Absatzzeichen hintereinander "Double Newline Error", daher gleich ausschließen
		if (preg_match("/(\r\n|\n){2,}/", array($headerName, $headerValue)))
			return false;

		// hinzufügen
		$this->headersAdd[trim($headerName)] = trim($headerValue);
	}

	/**
	 * public function createHeaders()
	 * Erzeugt den kompletten header-Teil einer Mail
	 * 
	 * @param array() $headers Optionaler Parameter: hier kann ein Header-Array übergeben werden. Fehlt der Parameter, wird die Objekt-Variable $this->headers verwendet
	 */
	public function createHeaders($headers=array()) {
		if (empty($headers))
			$headers = $this->headers;
		$h = '';

		foreach ($headers as $headerName => $headerValue) {
			$h .= $headerName . ': ' . $headerValue . $this->eol;
		}

		return $h;
	}

	/**
	 * public function cleanHeaderData() 
	 * Entfernt überschüsige Leerzeichen und Zeilenumbrüche aus Headerzeilen
	 * 
	 * @param string $headerData Textzeile für einen Header
	 */
	public function cleanHeaderData($headerData='') {
		return trim(preg_replace('/(\r?\n)*/', '', $headerData));
	}

	/**
	 * Outdated: function createMail()
	 * Nur noch zu Kompatibilitätszwecken implementiert
	 * Die Methode createMimemail() setzt das zu versendende Mail aus den einzelnen Komponenten zusammen.
	 * 
	 * @param string $html
	 * @param string $plainText
	 * @param array $attachments
	 * @param string $newsletterSenderMail
	 * @param string $newsletterSenderName
	 * @return string
	 */
	function createMimemail($html='', $plainText='', $attachments=array(), $senderMail='', $senderName='', $replyTo='') {
		return $this->createMail(array('html' => $html,
					'text' => $plainText,
					'attachments' => $attachments,
					'senderMail' => $senderMail,
					'senderName' => $senderName,
					'replyTo' => $replyTo));
	}

	/**
	 * function createMail()
	 * Erzeugt den gesamten Textkörper einer Mime-Mail (aktuelle Methode, createMimemail ist veraltet)
	 * 
	 * @param array $params
	 * string $html optionaler HTML-Teil
	 * string $plainText optionaler Textteil
	 * array $attachments optionale Attachments als Array
	 * string $newsletterSenderMail Absendermail
	 *  string $newsletterSenderName Absendername
	 * 
	 * @return string Gibt den fertigen Textkörper zurück
	 */
	public function createMail($params) {
		$defaultParams = array('html' => '',
			'text' => '',
			'attachments' => '',
			'senderMail' => '',
			'senderName' => '',
			'replyTo' => '',
			'embedHTMLImages' => $this->embedHTMLImages,
			'remoteImagePath' => $this->remoteImagePath
		);
		$params = array_merge($defaultParams, $params);
		
		if (isset($params['embedHTMLImages'])) {
			$this->embedHTMLImages = $params['embedHTMLImages'];
		}

		$this->remoteImagePath = $params['remoteImagePath'];
		
		// Headerdaten säubern
		$params['senderMail'] = $this->cleanHeaderData($params['senderMail']);
		$params['senderName'] = $this->cleanHeaderData($params['senderName']);
		$params['replyTo'] = $this->cleanHeaderData($params['replyTo']);

		extract($params);
		
		// extend HTML links
		$html = $this->prepareLinks(array_merge($params, array(
			'text' => $html,
			'type' => 'html'
		)));

		// extend links in text part
		$text = $this->prepareLinks(array_merge($params, array(
			'text' => $text,
			'type' => 'text'
		)));

		// Bei neuer E-Mail: Attachments leeren
		$this->attachedFiles = array();
		
		/*
		 * Zuerst  Header erzeugen
		 */
		if (!$senderName) {
			$senderName = $senderMail;
		}

		$this->addStandardHeader('MIME-Version', '1.0');
		$this->addStandardHeader('From', $senderName . ' <' . $senderMail . '>');
		
		$this->addStandardHeader('Reply-To', $replyTo);

		/**
		 * Mailkörper erstellen
		 */
		$body = '';
		if ($text)
			$this->hasText = true;
		if ($html)
			$this->hasHtml = true;
		$this->addAttachment($attachments);
		$this->extractImagesFromHtml($html);

		/*
		 * Nur Plaintext
		 */
		if ($this->hasText && !$this->hasHtml && !$this->hasAttachments) {
			$this->addStandardHeader('Content-Type', 'multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');
			$body .= $this->addBoundary() . $this->eol;
			$body .= $this->createTextPart($text);
		}

		/*
		 * HTML mit Grafiken, kein Text, keine Attachments
		 */
		if (!$this->hasText && $this->hasHtml && !$this->hasAttachments) {

			$body .= $this->addBoundary() . $this->eol;

			//prüft, ob embedded Images vorhanden sind		
			if ($this->hasImages) {
				$this->addStandardHeader('Content-Type', 'multipart/related;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');
				$body .= $this->createHtmlPart($html) . $this->eol;
			} else {
				$this->addStandardHeader('Content-Type', 'multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');
				$body .= $this->createHtmlPart($html) . $this->eol;
			}
		}


		/*
		 * HTML mit Grafiken, Text 
		 */
		if ($this->hasText && $this->hasHtml && !$this->hasAttachments) {

			// Images in HTML-Teil
			if ($this->hasImages) {
				$this->addStandardHeader('Content-Type', 'multipart/related;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;

				$body .= 'Content-Type: multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->partboundary . '"' . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . $this->eol;
				$body .= $this->createTextPart($text);
				$body .= $this->addBoundary($this->partboundary) . $this->eol;
				$body .= $this->createHtmlPart($html);
				$body .= $this->eol;
			} else {
				$this->addStandardHeader('Content-Type', 'multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;
				$body .= $this->createTextPart($text) . $this->eol;

				$body .= $this->addBoundary() . $this->eol;
				$body .= $this->createHtmlPart($html) . $this->eol . $this->eol;
			}
		}



		//Plaintext + Attachment (eins und mehrere)
		if ($this->hasText && !$this->hasHtml && $this->hasAttachments) {
			$this->addStandardHeader('Content-Type', 'multipart/mixed;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

			$body .= $this->addBoundary() . $this->eol;

			$body .= $this->createTextPart($text) . $this->eol;

			$body .= $this->addBoundary() . $this->eol;

			$body .= $this->createAttachments();
		}


		//HTML + Attachment
		if (!$this->hasText && $this->hasHtml && $this->hasAttachments) {
			if ($this->hasImages) {
				$this->addStandardHeader('Content-Type', 'multipart/mixed;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;

				$body .= 'Content-Type: multipart/related;' . $this->eol . "\tboundary=" . '"' . $this->partboundary . '"' . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . $this->eol;

				$body .= $this->createHtmlPart($html) . $this->eol;
				$body .= $this->addBoundary() . $this->eol;

				$body .= $this->createAttachments();
			} else {
				$this->addStandardHeader('Content-Type', 'multipart/mixed;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;

				$body .= $this->createHtmlPart($html) . $this->eol;

				$body .= $this->addBoundary() . $this->eol;

				$body .= $this->createAttachments();
			}
		}


		//Plaintext + HTML + Attachment
		if ($this->hasText && $this->hasHtml && $this->hasAttachments) {
			//prüft, ob embedded Images vorhanden sind		
			if ($this->hasImages) {
				$this->addStandardHeader('Content-Type', 'multipart/mixed;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;

				$body .= 'Content-Type: multipart/related;' . $this->eol . "\tboundary=" . '"' . $this->partboundary . '"' . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . $this->eol;

				$body .= 'Content-Type: multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->partboundary2 . '"' . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary2) . $this->eol;

				$body .= $this->createTextPart($text) . $this->eol;

				$body .= $this->addBoundary($this->partboundary2) . $this->eol;

				$body .= $this->createHtmlPart($html) . $this->eol;
				$body .= $this->addBoundary() . $this->eol;

				$body .= $this->createAttachments();
			} else {
				$mimemail .= "boundary=" . '"' . $this->boundary . '"';
				$this->addStandardHeader('Content-Type', 'multipart/mixed;' . $this->eol . "\tboundary=" . '"' . $this->boundary . '"');

				$body .= $this->addBoundary() . $this->eol;

				$body .= 'Content-Type: multipart/alternative;' . $this->eol . "\tboundary=" . '"' . $this->partboundary . '"' . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . $this->eol;

				$body .= $this->createTextPart($text) . $this->eol . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . $this->eol;

				$body .= $this->createHtmlPart($html) . $this->eol;

				$body .= $this->addBoundary($this->partboundary) . '--' . $this->eol;

				$body .= $this->addBoundary() . $this->eol;

				$body .= $this->createAttachments();

				$body .= $this->addBoundary() . $this->eol;
			}
		}

		// Abschließendes Boundary
		$body .= $this->addBoundary() . '--';
		$this->mailBody = $this->eol . $body;

		$this->headers = array_merge($this->headers, $this->headersAdd);
		$this->mailHeader = $this->createHeaders();

		return;
	}
	
	/**
	 * public function setTextpartCharset()
	 * Setter: Setzt den Zeichensatz des Textteils der Mail auf den übergebenen Parameter.
	 *
	 * @param string $charset Gültiger Name eines Zeichensatzes
	 *
	 * @return void
	 */
	public function setTextpartCharset($charset) {
		$this->textPartCharset = $charset;
	}
	
	public function setEndOfLineChar($char) {
		$this->eol = $char;
	}
		
	/**
	 * protected function sendMail()
	 * Versendet eine E-Mail.
	 *
	 * @param array $params Erwartet die Mailparameter in einem Array:
	 * 'text' => Textkörper der E-Mail
	 * 'attachements' => Pfade des/ der Attachements (String oder Array)
	 * 'senderMail' => Absender: Email-Adresse
	 * 'senderName' => Absender: Name
	 * 'replyTo' => E-Mailadresse für Antworten
	 * 'recipient' => Empfänger: E-Mailadresse
	 * 'subject' => Betreffzeile
	 *
	 * @return boolean True oder false, je nach Mailversanderfolg.
	 */
	public function sendMail($params=array()) {
	
		// Mail erzeugen:
		if (strtolower($this->textPartCharset) != 'utf-8') {
			// 1. Codierung prüfen, falls UTF-8, dann ändern in ISO
			if (mb_check_encoding($params['text'], 'UTF-8')) {
				$params['text'] = utf8_decode($params['text']);
			}
		
			if (mb_check_encoding($params['subject'], 'UTF-8')) {
				$params['subject'] = utf8_decode($params['subject']);
			}
		}
		
		if (isset($params['additionalParams'])) {
			$additionalParams = $params['additionalParams'];
		} else {
			$additionalParams = '';
		}

		// 2. Mail erzeugen
		$this->createMail(array(
			'text' => $params['text'],
			'attachments' => $params['attachments'],
			'senderMail' => $params['senderMail'],
			'senderName' => $params['senderName'],
			'replyTo' => $params['replyTo'],
			'embedHTMLImages' => $params['embedHTMLImages'],
			'remoteImagePath' => $params['remoteImagePath']
		));
	
		$mailStatus = @ mail (
			$params['recipient'],
			$params['subject'],
			$this->mailBody,
			$this->mailHeader,
			$additionalParams
		);
	
		return $mailStatus;
	
	}
	
	/**
	 * function prepareLinks()
	 * Extends optionally the links in the mailpart with additional parameters.
	 * 
	 * @param array $params
	 * @return string The extended mailpart as string.
	 */
	public function prepareLinks($params) {
		
		$defaultParams = array(
			'text' => '',
			'queryString' => '',
			'separator' => '?',
			'type' => 'HTML'
		);
		
		$params = array_merge($defaultParams, $params);
		
		// nothing to substitute => return
		if (!$params['queryString']) {
			return $params['text'];
		}
		
		// search in HTML or in text?
		switch(strtolower($params['type'])) {
			
			case 'text':
				preg_match_all('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?/i', $params['text'], $match);
				$tagFragments = $match[0];
				$links = $match[0];
				break;
				
			default:
			case 'html':
				preg_match_all('/\<a.*href\=[\'"\s]?([^\'"\s]+)/i', $params['text'], $match);
				$tagFragments = $match[0];
				$links = $match[1];
				break;
				
		}
	
		$extendedFragments = array();
		
		// extend links
		foreach ($tagFragments as $key => $tagFragment) {
			
			if (strstr($links[$key], '?')) {
				$separator = '&';
			} else {
				$separator = $params['separator'];
			}
			
			$extendedFragments[$key] = str_ireplace($links[$key], $links[$key] . $separator . $params['queryString'], $tagFragment);
		}
		
		$text = $params['text'];
		foreach ($tagFragments as $key => $tagFragment) {
			$text = str_ireplace($tagFragment, $extendedFragments[$key], $text);
		}
		
		return $text;
		
	}

}

?>