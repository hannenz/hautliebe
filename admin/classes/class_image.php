<?php
/**
 * image
 * 
 * Klasse, die Grafik- und Bildbearbeitungsfunktionen auf Basis der GD 2 Bibliothek zur Verfügung stellt
 * 
 * Diese Klasse stellt die Grundwerkzeuge für die Behandlung von Grafiken auf dem Server zur Verfügung.
 * 
 * @author J.Hahn <jhahn@content-o-mat.de>
 * @version 2015-09-10
 */

namespace Contentomat;

class Image {

	protected $imageOptions;		// in diesem Array werden alle Informationen über die GD-Bibliothek gespeichert
	protected $errorMessage;
	protected $imageDataCache;		// alle ermittelten Bilddaten werden in diesem Array zwischengespeichert.
	
	// Wrapper
	protected $gdImageTypeSupportWrapper;
	protected $exifImageTypeWrapper;
	protected $exifImageTypeToExtensionWrapper;
	protected $imageTypeToExtensionWrapper;
	protected $extensionToImageTypeWrapper;
	protected $resolutionNrToNameWrapper;
	protected $colorSpaceWrapper;

	/**
	 * Konstruktor-Methode
	 * 
	 * @param void Erwartet keine Parameter
	 */
	public function __construct() {
		
		$this->imageOptions = gd_info();
		
		// Leider sind die Rückgabewerte von gd_info() nicht auf allen Servern gleich (mal "JPG Support", mal "JPEG Support")
		if (array_key_exists('JPEG Support', $this->imageOptions)) {
			$this->imageOptions['JPG Support'] = $this->imageOptions['JPEG Support'];
		} else if (array_key_exists('JPG Support', $this->imageOptions)) {
			$this->imageOptions['JPEG Support'] = $this->imageOptions['JPG Support'];
		}
		
		$this->imageDataCache = array();
		
		$this->exifImageTypeWrapper = array(
			1 => 'IMAGETYPE_GIF',
			2 => 'IMAGETYPE_JPEG',
			3 => 'IMAGETYPE_PNG',
			4 => 'IMAGETYPE_SWF',
			5 => 'IMAGETYPE_PSD',
			6 => 'IMAGETYPE_BMP',
			7 => 'IMAGETYPE_TIFF_II',
			8 => 'IMAGETYPE_TIFF_MM',
			9 => 'IMAGETYPE_JPC',
			10 => 'IMAGETYPE_JP2',
			11 => 'IMAGETYPE_JPX',
			12 => 'IMAGETYPE_JB2',
			13 => 'IMAGETYPE_SWC',
			14 => 'IMAGETYPE_IFF',
			15 => 'IMAGETYPE_WBMP',
			16 => 'IMAGETYPE_XBM'
		);

		$this->exifImageTypeToExtensionWrapper = array(
			'IMAGETYPE_GIF' => 'gif',
			'IMAGETYPE_JPEG' => 'jpg',
			'IMAGETYPE_PNG' => 'png',
			'IMAGETYPE_SWF' => 'swf',
			'IMAGETYPE_PSD' => 'psd',
			'IMAGETYPE_BMP' => 'bmp',
			'IMAGETYPE_TIFF_II' => 'tif',
			'IMAGETYPE_TIFF_MM' => 'tif',
			'IMAGETYPE_JPC' => 'jpc',
			'IMAGETYPE_JP2' => 'jp2',
			'IMAGETYPE_JPX' => 'jpx',
			'IMAGETYPE_JB2' => 'jb2',
			'IMAGETYPE_SWC' => 'swc',
			'IMAGETYPE_IFF' => 'iff',
			'IMAGETYPE_WBMP' => 'bmp',
			'IMAGETYPE_XBM' => 'xbm'
		);
		
		$this->imageTypeToExtensionWrapper = array(
			1 => 'gif',
			2 => 'jpg',
			3 => 'png',
			4 => 'swf',
			5 => 'psd',
			6 => 'bmp',
			7 => 'tif',
			8 => 'tif',
			9 => 'jpc',
			10 => 'jp2',
			11 => 'jpx',
			12 => 'jb2',
			13 => 'swc',
			14 => 'iff',
			15 => 'bmp',
			16 => 'xbm'
		);
		
		$this->extensionToImageTypeWrapper = array_flip($this->imageTypeToExtensionWrapper);
		
		$this->resolutionNrToNameWrapper = array (
			1 => 'none',
			2 => 'inches',
			3 => 'cm',
			4 => 'mm',
			5 => 'um'
		);
		
		$this->gdImageTypeSupportWrapper = array (
			'gif' => 'GIF Create Support', 
			'jpg' => 'JPG Support', 
			'jpeg' => 'JPG Support', 
			'png' => 'PNG Support'
		);
	}
	
	/**
	 * Erzeugt ein Thumbnail
	 * 
	 * Folgende Parameter können per Array übergeben werden (Schlüssel: Variablenname, Wert: Variablenwert)
	 * 
	 * @param $params array Beinhaltet die Parameter in einem Array. Schl�ssel ist der Parameter-Name, Wert der -Wert
	 * @param sourceImage string Name/Pfad der Grafik von der ein Thumbnail erzeugt werden soll
	 * @param destinatonImage string Name/ Pfad der erzeugten Thumbnailgrafik
	 * @param destinationImageType string Typ der zu erzeugenden Grafikdatei: jpg, png oder gif. Wenn ein gew�hlter Typ nicht vom Server unterst�tzt wird, wird false zur�ckgegeben und eine Fehlermeldung gespeichert.
	 * @param alternativedestinationImageType string Alternativer Thumbnailtyp, wenn das gew�nschte Format vom Server nicht unterst�tzt wird
	 * @param width integer Optional: Feste Breite der erzeugten Thumbnaildatei in Pixeln, H�he wird im Verh�ltnis berechnet wenn 'preserveRatio' nicht auf 'false' gesetzt ist
	 * @param height integer Optional: Feste H�he der erzeugten Thumbnaildatei in Pixeln, Breite wird im Verh�ltnis berechnet wenn 'preserveRatio' nicht auf 'false' gesetzt ist
	 * @param maxWidth integer Optional in Verbindung mit maxHeight: Maximale Breite der erzeugten Grafik, abh�ngig vom Seitenverh�ltnis der Quelldatei. Thumbnailgr��e bewegt sich im von maxWidth und maxHeight gesetzten Rahmen und erh�lt dabei das Seitenverh�ltnis.
	 * @param maxHeight integer Optional in Verbindung mit maxWidth: Maximale H�he der erzeugten Grafik, abh�ngig vom Seitenverh�ltnis der Quelldatei.
	 * @param preserveRatio boolean Ist dieser Wert false, wird das Seitenverh�ltnis nicht beibehalten, sondern die neue Grafik gem�� den sizeX, sizeY Paarametern erstellt. Default ist true 
	 * @param sizePercent float Optional: Gr��e der erzeugten Thumbnaildatei in Prozent des Originalbildes
	 * @param backgroundColor string Hintergrundfarbe falls ein Quellbild mit Transparenz (z.B. PNG, Gif) in ein JPG-Bild gewandelt werden soll.
	 * @return array Array, welches je nach Parameter�bergabe die Verzeichnisstruktur enth�lt
	 * 
	 */
	 public function createThumbnail($params) {
		$defaultParams = array(	'sourceImage' => '', 'sourceImageType' => '', 'destinationImage' => '', 'sizePercent' => false,
								'maxWidth' => false, 'maxHeight' => false, 'width' => false, 'height' => false,
								'preserveRatio' => true, 'destinationImageType' => 'jpg', 'alternativeDestinationImageType' => 'jpg', 
								'backgroundColor' => '255,255,255','jpgQuality'=>'75');
		$params = array_merge($defaultParams, $params);
		$this->errorMessage = false;
		
		// F�r interne Zwecke Dateiendungen klein schreiben
		$sourceImageType = strtolower($params['sourceImageType']);
		$destinationImageType = strtolower($params['destinationImageType']);
		$alternativeDestinationImageType = strtolower($params['alternativeDestinationImageType']);
		
		// Pr�fen, ob es die Quelldatei gibt
		if (!file_exists($params['sourceImage']) || !is_readable($params['sourceImage'])) {
			$this->errorMessage = 'Die Quelldatei '.$params['sourceImage'].'existiert nicht oder kann nicht gelesen werden.';
			return false;
		}
		
		// Prüfen, ob das gewünschte thumbnailformat unterst�tzt wird


		if (!$this->imageOptions[$this->gdImageTypeSupportWrapper[$destinationImageType]]) {
			// ... wird nicht unterst�tzt, dann alternativen Type nehmen
			//if (!$this->imageOptions[$imageTypeWrapper[$params['alternativedestinationImageType']]]) {
			if (!$this->imageOptions[$this->gdImageTypeSupportWrapper[$alternativedestinationImageType]]) {
				// ... alternativer Type wird auch nicht unterst�tz, dann abbrechen
				$this->errorMessage = 'Sowohl das '.$params['destinationImageType'].'-Format, als auch das alternative '.$params['alternativeDestinationImageType'].'-Format werden vom Server nicht unterst�tzt. Es wurde keine Grafik erzeugt.';
				return false;
			} else {
				// ... alternativer Typ wird unterst�tzt
				$params['destinationImageType'] = $params['alternativeDestinationImageType'];
				$destinationImageType = $alternativeDestinationImageType;
			}
		}
		
		// Pr�fen, ob es sich bei der Quelldatei um ein Gif handelt. In diesem Fall die Serverunterst�tzung pr�fen
		//if (!$params['sourceImageType']) {
		if (!$sourceImageType) {
			$imageInfo = pathinfo($params['sourceImage']);
			$imageExt = strtolower($imageInfo['extension']);
		} else {
			$imageExt = strtolower($params['sourceImageType']);
		}
		
		if (($imageExt == 'gif' && !$this->imageOptions['GIF Read Support'])) {
			$this->errorMessage = 'Der Server unterstützt das Einlesen von Gif-Grafiken nicht! Es wurde kein neues Bild erzeugt.';
			return false;
		}

		// Neuen Dateinamen erstellen, changed 2014-12-04 JH
//		$newImageName = preg_replace('/[A-Za-z0-9]{3,4}$/', $params['destinationImageType'], $params['destinationImage']);
		$newImageName = $params['destinationImage'];
		
		// Daten des Originalbildes ermitteln
		$imageSize = getimagesize($params['sourceImage']);
		$sourceWidth = $imageSize[0];
		$sourceHeight = $imageSize[1];
		
		// Thumbnail-Gr��e ermitteln: Abh�ngig von den �bergebenen Parametern
		// Seitenverh�ltnisse sollen beibehalten werden
		if ($params['preserveRatio']) {
			
			// Prozentuale Gr��enver�nderung?
			if ($params['sizePercent']) {
				$ratio = $params['sizePercent']/100;
				$thumbWidth = intval($sourceWidth * $ratio);
				$thumbHeight = intval($sourceHeight * $ratio);
			} else 
			
			// Definierte Thumbnailbreite
			if ($params['width']) {
				$thumbWidth = $params['width'];
				$ratio = $params['width'] / $sourceWidth;
				$thumbHeight = intval($sourceHeight * $ratio);
			} else
			
			// Definierte Thumbnailh�he
			if ($params['height']) {
				$thumbHeight = $params['height'];
				$ratio = $params['height'] / $sourceHeight;
				$thumbWidth = intval($sourceWidth * $ratio);
			} else
			
			// Maximale H�he und Maximale Breite
			if ($sourceWidth >= $sourceHeight) {
			    $ratio = $params['maxWidth'] / $sourceWidth;
			    $thumbWidth = $params['maxWidth'];
			    $thumbHeight = intval($sourceHeight * $ratio);
			} else {
			    $ratio = $params['maxHeight'] / $sourceHeight;
			    $thumbWidth = intval($sourceWidth * $ratio);
			    $thumbHeight = $params['maxHeight'];
			}			
		} else {
			// Seitenverh�ltniss interessiert nicht
			$thumbWidth = $params['width'];
			$thumbHeight = $params['height'];
		}

		// Bilddaten einlesen
		switch (strtolower($imageExt)) {
			case 'png':
				$imageSource = imagecreatefrompng($params['sourceImage']);
				break;
			
			case 'jpg':
			case 'jpeg':
				$imageSource = imagecreatefromjpeg($params['sourceImage']);
				break;
			
			case 'gif':
				$imageSource = imagecreatefromgif($params['sourceImage']);
				break;
			
			default:
				$this->errorMessage ='Unbekanntes Format der Quelldatei ('.$params['sourceImage'].'). Es wurde kein neues Bild erzeugt.';
				return false;
				break;
		}
		
		// Bilddaten ausgeben
		switch (strtolower($destinationImageType)) {
			case 'png':
				$thumbImage = imagecreatetruecolor ($thumbWidth, $thumbHeight);
				imagepalettecopy ($thumbImage, $imageSource);

				imagealphablending($thumbImage, false);
				$alpha = imagecolorallocatealpha($thumbImage, 0, 0, 0, 127);
				imagefilledrectangle($thumbImage, 0, 0, $thumbWidth, $thumbHeight, $alpha);
				imagealphablending($thumbImage, true);
				imagecopyresampled ($thumbImage, $imageSource, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
				imagealphablending($thumbImage, false);
				imagesavealpha($thumbImage, true);

				$saveThumb = @imagepng ($thumbImage, $newImageName);				
			break;

			case 'jpg':
			case 'jpeg':
				$thumbImage = imagecreatetruecolor ($thumbWidth, $thumbHeight);
				
				// Hintergrundfarbe
				$bg = explode(',', $params['backgroundColor']);
				$imageBackground = imagecolorallocate($thumbImage, intval($bg[0]), intval($bg[1]), intval($bg[2]));
				imagefill($thumbImage, 0, 0, $imageBackground);
				imagecopyresampled ($thumbImage, $imageSource, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
				$saveThumb = @imagejpeg ($thumbImage, $newImageName, intval($params['jpgQuality']));				
			break;

			case 'gif':
				$thumbImage = imagecreate ($thumbWidth, $thumbHeight);

				if ($imageExt != 'gif') {
					imagetruecolortopalette ($imageSource, false, 255);
				} else {
					imagepalettecopy ($thumbImage, $imageSource);
					$alpha = imagecolorexactalpha ($imageSource, 0,0,0, 127);
					imagecolortransparent ($thumbImage, $alpha);					
				}

				imagecopyresampled ($thumbImage, $imageSource, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
				$saveThumb = @imagegif ($thumbImage, $newImageName);
			break;
			
			default:
				$this->errorMessage = 'Unbekanntes Format der Ausgabedatei ('.$params['destinationImageType'].'). Es wurde kein neues Bild erzeugt.';
				imagedestroy($thumbImage);
				return false;
			break;			
			
		}
		
		// Pr�fen, ob Thumbnail erstellt werden konnte
		if (!$saveThumb) {
			$this->errorMessage = 'Neues Bild '.$params['destinationImage'].' konnte nicht aus Bilddatei '.$params['sourceImage'].' erzeugt werden.';
			imagedestroy($thumbImage);
			return false;
		} else {
			imagedestroy($thumbImage);
			return true;
		}
	 }

  /**
      * createSquareThumbnail
      *
      * creates a square thumbnail cropped to the centered part
      * @param $filename String         Path to input image file
      * @param $destination string         Path to output file (thumbnail/destination)
      * @param $thumbSize Int             The square size in px.
      * @return void
      */
    public function createSquareThumbnail($filename, $destination, $thumbSize=160 ){

        $srcFile = $filename;
        $thumbFile = $destination;

         /* Determine the File Type */
         $_img = getImageSize($filename);
         $type = $_img['mime'];
         /* Create the Source Image */
         switch( $type ){
             case 'image/jpeg':
             $src = imagecreatefromjpeg( $srcFile ); break;
             case 'image/png' :
             $src = imagecreatefrompng( $srcFile ); break;
             case 'image/gif' :
             $src = imagecreatefromgif( $srcFile ); break;
         }

        list($oldW, $oldH) = getimagesize($filename);

        // Work out what offset to use
        if ($oldH < $oldW) {
            $offH = 0;
            $offW = ($oldW-$oldH)/2;
            $oldW = $oldH;
        }
        elseif ($oldH > $oldW) {
            $offW = 0;
            $offH = ($oldH-$oldW)/2;
            $oldH = $oldW;
        }
        else {
            $offW = 0;
            $offH = 0;
        }

        // Resample the image into the new dimensions.
        $new = imagecreatetruecolor($thumbSize, $thumbSize);
        imagecopyresampled($new, $src, 0, 0, $offW, $offH, $thumbSize, $thumbSize, $oldW, $oldH);
         switch( $type ){
             case 'image/jpeg' :
             imagejpeg( $new , $thumbFile ); break;
             case 'image/png' :
             imagepng( $new , $thumbFile ); break;
             case 'image/gif' :
             imagegif( $new , $thumbFile ); break;
         }
         imagedestroy( $new );
         imagedestroy( $src );
     }

	/**
	 * function createTextImage
	 * 
	 * Funktion, die aus Text/ einem String eine Grafik erzeugt
	 * @param $params array
	 * @return
	 */
	 function createTextImage($params) {
		$defaultParams = array ('text' => '', 
								'textSize' => 24,
								'imageWidth' => 300,
								'imageHeight' => 25,
								'lineHeight' => 1.7,
								'xPos' => 0,
								'textColor' => '0,0,0',
								'backgroundColor' => '255,255,255',
								'backgroundTransparent' => false,
								'fontPath' => '',					// der Pfad zur TTF-Datei muss relativ angegeben werden!
								'textAngle' => 0,
								'textAlign' => false, 
								'saveImageFileName' => '',			// saveImageFileName und destinationImage beschreiben beide die Zieldatei
								'destinationImage' => '',			// der Pfad relativ angegeben werden!
								'destinationImageType' => 'png',
								'antiAliasing' => true,
								'errorMessage' => 'Fehler beim Schreiben der erzeugten Datei.');
		$params = array_merge($defaultParams, $params);
		$this->errorMessage = false;
		
		// zwecks Abw�rtskompatibilit�t
		if ($params['saveImageFileName']) $params['destinationImage'] = $params['saveImageFileName']; 
		
		$lineHeight = floatval($params['lineHeight']);
		$groundLine = $lineHeight*0.8;
		$textSize = intval($params['textSize']);
		
		// Gibt es eine Zeichensatzdatei?
		if (trim($params['fontPath']) == '') {
			$this->errorMessage = 'Pfad zur Schriftartdatei fehlt!';
			return false;
		}

		// Y-Position berechnen
		$yPos = round($textSize*$groundLine);
		// X-Position		
		$xPos = $params['xPos'];
		
		/*
		 * Text ggf. ausrichten
		 */
		if ($params['textAlign']) {
			switch ($params['textAlign']) {
				case 'left':
					$params['xPos'] = '0';
				break;
				
				case 'center':
					$textWidthArray = imagettfbbox ($params['textSize'], 0, $params['fontPath'], $params['text']);
					$textWidth = $textWidthArray[4] - $textWidthArray[0];
					
					// X-Position anhand der Textbreite berechnen
					$xPos = intval(($params['imageWidth'] - $textWidth) / 2);
				break;
				
				case 'right':
					$textWidthArray = imagettfbbox ($params['textSize'], 0, $params['fontPath'], $params['text']);
					$textWidth = $textWidthArray[4] - $textWidthArray[0];
					
					// X-Position anhand der Textbreite berechnen
					$xPos = intval($params['imageWidth'] - $textWidth);				
				break;	
			}	
		}
		
		// Grafkh�he berechnen
		$imageHeight = round($textSize*$lineHeight);
		
		/*
		 * Bild erstellen: Unterschiede je nach gew�hltem Format
		 */
		$params['destinationImageType'] = trim(strtolower($params['destinationImageType']));
		$bg = explode(',', $params['backgroundColor']);
		$tc = explode(',', $params['textColor']);
				
		switch ($params['destinationImageType']) {	
			case 'gif':
				$im = imagecreate (intval($params['imageWidth']), $imageHeight);
				
				// Alphakanal f�r transparenten Hintergrund
				if ($params['backgroundTransparent']) {
					$bgColor = imagecolorallocatealpha ($im, $bg[0], $bg[1], $bg[2], 127);
				} else {
					$bgColor = imagecolorallocate ($im, $bg[0], $bg[1], $bg[2]);
				}
				break;
			
			case 'png':
				$im = imagecreatetruecolor (intval($params['imageWidth']), $imageHeight);

				// Alphakanal f�r transparenten Hintergrund
				if ($params['backgroundTransparent']) {
					imagesavealpha($im, true);
					imagealphablending($im, false);
					$tlo = imagecolorallocatealpha($im, $bg[0], $bg[1], $bg[2] , 127);
					imagefill($im, 0, 0, $tlo);
				} else {
					// sonst normale Hintergrundfarbe
					$bgColor = imagecolorallocate ($im, $bg[0], $bg[1], $bg[2]);
					imagefill($im, 0, 0, $bgColor);
				}
				break;

			case 'jpeg':	
			case 'jpg':
			default:
				$im = imagecreate (intval($params['imageWidth']), $imageHeight);
				$bgColor = imagecolorallocate ($im, $bg[0], $bg[1], $bg[2]);
				break;
		}

		// Textfarbe
		$textColor = imagecolorallocate ($im, $tc[0], $tc[1], $tc[2]);
		if (!$params['antiAliasing']) {
			$textColor = '-'.$textColor;
		}

		// Text erzeugen: Bildabmessungen,Texth�he, Drehung des Textes in Grad, Textbeginn x, Textbeginn y, Textfarbe, Pfad zum Font, auszugebender Text
		imagettftext ($im, intval($params['textSize']), intval($params['textAngle']), $xPos, $yPos, $textColor, $params['fontPath'], $params['text']);
		
		/*
		 * Grafik ausgeben
		 */
		switch ($params['destinationImageType']) {	
			case 'png':
				if (trim($params['destinationImage']) != '') {
					$check = imagepng ($im, $params['destinationImage']);
				} else {
					header('Content-type: image/png');
					$check = imagepng($im);
				}
			break;

			case 'gif':
				if (trim($params['destinationImage']) != '') {
					$check = imagegif ($im, $params['destinationImage']);
				} else {
					header('Content-type: image/gif');
					$check = imagegif($im);
				}				
				break;
			
			case 'jpeg':	
			case 'jpg':
			default:
				if (trim($params['destinationImage']) != '') {
					$check = imagejpeg ($im, $params['destinationImage']);
				} else {
					header('Content-type: image/jpg');
					$check = imagejpeg($im);
				}
			break;
		}		 
		if (!$check)
		imagedestroy ($im);
		return $check;
	 }

	/**
	 * function createCaptchaImage
	 * 
	 * Methode erzeugt eine Captcha-Textgrafik
	 * @param $params array
	 * @return
	 */
	public function createCaptchaImage ($params=array()) {
		$defaultParams = array ('captchaCode' => '', 
								'captchaCodeLength' => 6,
								'imageWidth' => 120,
								'imageHeight' => 20,
								'imageType' => 'png',
								'font' => PATHTOWEBROOT.'fonts/dreamofme.gdf',
								'fontSize' => 20,
								'fontColor' => '0,0,0',
								'fontXOffset' => 0,
								'fontYOffset' => 0,
								'backgroundColor' => '255,255,255',
								'backgroundImage' => '',
								'lineColor' => '128,128,128'
								);
		$params = array_merge($defaultParams, $params);
		$this->errorMessage = false;
		
		$params['imageType'] = strtolower($params['imageType']);

		// Wird Code übergeben?
		if (!$params['captchaCode']) {
			$code = $this->createCaptchaCode();
		} else {
			$code = trim($params['captchaCode']);
		}

		// Gibt es ein Hintergrundbiild?
		$bgImageType = $this->getImageType($params['backgroundImage']);

		// Ist der angegebene Font korrekt?
		$fontType = $this->getFontType($params['font']);

		// Grundbild erzeugen
		switch ($bgImageType) {
			case 'jpg':
				$img = imagecreatefromjpeg($params['backgroundImage']);
				break;

			case 'png':
				$img = imagecreatefrompng($params['backgroundImage']);
				break;

			case 'gif':
				$img = imagecreatefromgif($params['backgroundImage']);
				break;

			default:
				$img = ImageCreateTrueColor(intval($params['imageWidth']), $params['imageHeight']);
				$bgColorArray = $this->stringToColor($params['backgroundColor']);
				$bgColor = imagecolorallocate ($img, $bgColorArray[0], $bgColorArray[1], $bgColorArray[2]);
				imagefill($img, 0, 0, $bgColor);
				break;
		}

		// Farben
		$bgColorArray = $this->stringToColor($params['backgroundColor']);
		$fontColorArray = $this->stringToColor($params['fontColor']);
		$lineColorArray = $this->stringToColor($params['lineColor']);

		//Farben definieren
		$bgColor = imagecolorallocate ($img, $bgColorArray[0], $bgColorArray[1], $bgColorArray[2]);
		$fontColor = imagecolorallocate ($img, $fontColorArray[0], $fontColorArray[1], $fontColorArray[2]);
		$lineColor = imagecolorallocate ($img, $lineColorArray[0], $lineColorArray[1], $lineColorArray[2]);

		switch ($fontType) {
			case 'gdf':
				$font = imageloadfont($params['font']);

				imagestring($img, $font, intval($params['fontXOffset']), intval($params['fontYOffset']), $code, $fontColor);
				break;

			case 'ttf':
				$angle = rand(0,5);
				$t_x = rand(5,30) + intval($params['fontXOffset']);
				$t_y = $angle + $params['fontSize'] + intval($params['fontYOffset']);
				imagettftext($img, intval($params['fontSize']), $angle, $t_x, $t_y, $fontColor, $params['font'], $code);
				break;
		}
		
		//im Browser anzeigen (png oder jpeg)
		switch($params['imageType']) {
			case 'jpg':
			case 'jpeg':
				Header ('Content-type: image/jpeg');
				imagejpeg ($img);
		
			case 'png':
				Header ('Content-type: image/png');
				imagepng ($img);
				break;

			case 'gif':
				Header ('Content-type: image/gif');
				imagegif ($img);
				break;
		}
				
		//Ressourcen wieder freigeben
		ImageDestroy($img);
	} 

	/**
	 * public function createCaptchaCode()
	 * Erstellt einen Code aus Buchstaben und Zahlen. Erwartet Parameter in einem Array (Schlüssel = Name, Wert = Wert)
	 *
	 * @param number codeLength Länge des Captcha Codes
	 * @return string Zeichenfolge/ Code
	 */
	public function createCaptchaCode($params=array()) {
		$defaultParams = array ('codeLength' => 6);
		$params = array_merge($defaultParams, $params);
		$chars = '';
		
		// Großbuchstaben
		for ($i = 65; $i <= 90; $i++) {
			$chars .= chr($i);
		}
		
		// Kleinbuchstaben
		for ($i = 97; $i <= 122; $i++) {
			$chars .= chr($i);
		}
		
		// Zahlen
		for ($i = 48; $i <= 57; $i++) {
			$chars .= chr($i);
		}

		// "1" und "l" (Klein-L) wegen zu großer Ähnlichkeit rausnehmen
		$chars = str_replace(array(chr(49), chr(108)), '', $chars);
		$charsLength = strlen($chars)-1;
		
		// Erstellen
		$code = '';
		for ($i = 0; $i < $params['codeLength']; $i++) {
			$code .= substr($chars, intval(rand(0, $charsLength)), 1);
		}
		
		return $code;
	}

	/**
	 * public function stringToColor()
	 * Wandelt String in Array mit Farbangaben um
	 *
	 * @param string $color String im Format '128,255,40' (RGB-Farbe)
	 * @return array Gibt Array mit RGB-Infos zurück
	 */
	public function stringToColor($string) {
		$colors = explode(',', $string);

		foreach ($colors as $k => $c) {
			$colors[$k] = intval(trim($c));
		}

		return $colors;
	}

	/**
	 * public function getImageType()
	 * Ermittel den Bildtyp anhand des Dateianhangs
	 *
	 * @param string $imagePath PFad zum Bild
	 * @return mixed Gibt entweder Bildtyp als String zurück oder false, falls keine Bilddatei
	 */
//	public function getImageType($imagePath) {
//		$pf = pathinfo($imagePath);

//
//		$pfExt = strtolower($pf['extension']);

//
//		switch ($pfExt) {
//			case 'jpg':
//			case 'jpeg':
//				return 'jpg';
//				break;

//
//			case 'png':
//				return 'png';
//				break;

//
//			case 'gif':
//				return 'gif';
//				break;

//
//			default:

//				return false;
//				break;

//		}
//	}

	/**
	 * public function getFontType()
	 * Ermittel den Schriftartentyp anhand des Dateianhangs
	 *
	 * @param string $fontPath Pfad zum Bild
	 * @return mixed Gibt entweder Fonttyp als String zurück oder false, falls keine Fontdatei
	 */
	public function getFontType($fontPath) {
		$pf = pathinfo($fontPath);

		$pfExt = strtolower($pf['extension']);

		switch ($pfExt) {
			case 'gdf':
				return 'gdf';
				break;

			case 'ttf':
				return 'ttf';
				break;

			default:
				return false;
				break;
		}
	}

	
	/**
	 * public function getColorSpace()
	 * Ermittelt, ob es sich ein Bild im RGB- oder CMYK-Farbformat vorliegt.
	 * 
	 * @param string $filePath Pfad der Bilddatei
	 * @return string Name des Farbformates ('rgb' oder 'cmyk') oder leerer String bei Fehler.
	 */
	public function getColorSpace($filePath) {
		
		$imageData = $this->getCachedImageInformations($filePath);
		
		// Falls benötigte Daten nicht im Cache, dann holen
		if (!$imageData['cmtImageSize']['channels']) {
			$imageData = @getimagesize($filePath, $iptcInfo);
		}
		
		// ermittelte Daten im Cache speichern
		if (is_array($imageData)) {
			$this->setCachedImageInformations($filePath, 'cmtImageSize', $imageData);
 		} else {
 			$this->setCachedImageInformations($filePath, 'cmtImageSize', array());
 		}
		
 	 	// Zusätzlich IPTC-Daten speichern
		if (isset($iptcInfo["APP13"])) {
			$this->setCachedImageInformations($filePath, 'cmtIptc', iptcparse($iptcInfo["APP13"]));
		}		
		
		
		switch($imageData['channels']) {
			case '3':
				return 'rgb';
				break;
				
			case '4':
				return 'cmyk';
				break;
				
			default:
				return '';
		}
	}

	/**
	 * public function getIptcData()
	 * Ermittelt den IPTC-Datenteil eines Bildes (sofern vorhanden)
	 *
	 * @param string $filePath Pfad zur Bilddatei
	 *
	 * @return array Assoziatives Array mit den Daten oder leeres Array
	 */
	public function getIptcData($filePath) {

		$imageData = $this->getCachedImageInformations($filePath);
		
		// Falls benötigte Daten nicht im Cache, dann holen
		if ($imageData['cmtIpct']) {
			return $imageData['cmtIpct'];
		} else {
			$imageData = @getimagesize($filePath, $iptcInfo);
		
			// auch Größendaten im Cache speichern
			if (is_array($imageData)) {
				$this->setCachedImageInformations($filePath, 'cmtImageSize', $imageData);
	 		} else {
	 			$this->setCachedImageInformations($filePath, 'cmtImageSize', array());
	 		}
			
	 	 	// nach IPTC-Daten suchen
			if (isset($iptcInfo["APP13"])) {
				$iptcData = iptcparse($iptcInfo["APP13"]);
				$this->setCachedImageInformations($filePath, 'cmtIptc', $iptcData);
				
				return $iptcData;
			} else {
				return array();
			}
		}
	}
	
	/**
	 * public function getCachedImageInformations()
	 * Gibt ggf. gecachte Bildinformationen (Größe, Exif, etc.) zurück 
	 *
	 * @param string $filePath Pfad zur Bilddatei
	 *
	 * @return array Array mit Daten oder leeres Array
	 */
	public function getCachedImageInformations($filePath) {
		
		if ($this->imageDataCache[$filePath]) {
 			return $this->imageDataCache[$filePath];
 		} else {
 			return array();
 		}
	}

	/**
	 * public function setCachedImageInformations()
	 * Speichert Daten im internen Bilderdaten-Cache (der interne Cache ist ein multidimensionales Array, der erste Schlüsselname ist der Bildpfad)
	 *
	 * @param string $filePath Pfad zur Bilddatei
	 * @param string $index Name des Schlüssel / des Wertes
	 * @param mixed $data Alle Arten von Daten 
	 *
	 * @return void
	 */
	public function setCachedImageInformations($filePath, $index='', $data='') {
		
		if (is_array($this->imageDataCache[$filePath][$index]) && is_array($data)) {
			$this->imageDataCache[$filePath][$index] = array_merge($this->imageDataCache[$filePath][$index], $data);
			
		} else {
			$this->imageDataCache[$filePath][$index] = $data;

		}

	}
	
 	/**
 	 * 
 	 * public function getImageInformations()
 	 * Liest die Informationen einer Bilddatei aus. 
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 * @return array Array mit Bildinformationen oder leeres Array bei Fehler
 	 */
 	public function getExifData($filePath) {
 		
 		$imageData = $this->getCachedImageInformations($filePath);
 		$exifData = $imageData['cmtExif'];
 		
 		if (is_array($exifData)) {
 			return $exifData;
 		} else {
	 		// http://www.php.net/manual/de/function.exif-read-data.php
	 		// http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html
	 		// Falls andere formate als JPG oder TIFF gelesen werden, gibt PHP sonst einen Fehler aus
	 		$exifData = @ exif_read_data($filePath, 0, true);
 		}
 		 		
 		if (is_array($exifData)) {
 			$this->setCachedImageInformations($filePath, 'cmtExif', $exifData);
 			return $exifData;
 		} else {
 			// Falls keine Exif-Daten extrahiert werden konnten, dann leeres Array speichern, 
 			// damit nicht zweimal Daten gelesen werden. 
 			$this->setCachedImageInformations($filePath, 'cmtExif', array());
 			return $exifData;
 		}
 	}
 	
 	/**
 	 * public function getImageSize()
 	 * Ermittelt die Bildgröße
 	 *
 	 * @param string $filePath Bildpfad
 	 *
 	 * @return array Datenarray mit den Schlüsseln 'width', 'height' und 'html'
 	 */
 	public function getImageSize($filePath) {
 		
 		$imageInformations = $this->getCachedImageInformations($filePath);
 		
 		// Es wurden Bilddaten im Cache gefunden
 		if (!empty($imageInformations)) {
 			
 			// Bildgröße in internem Array suchen
 			if (is_array($imageInformations['cmtImageSize'])) {
 				
 				return array(
 					'width' => $imageInformations['cmtImageSize']['width'],
 					'height' => $imageInformations['cmtImageSize']['height'],
 					'html' => 'width="'.$imageInformations['cmtImageSize']['width'].'" height="'.$imageInformations['cmtImageSize']['height'].'"'
 				);
 			} else if (is_array($imageInformations['cmtExif'])) {
 				
 				$exif = $imageInformations['cmtExif'];
 				
 				// In den Exif-Informationen an verschiedenen Stellen suchen
 				if (isset($exif['COMPUTED'])) {
 					
 					return array(
	 					'width' => $exif['COMPUTED']['Width'],
	 					'height' => $exif['COMPUTED']['Height'],
	 					'html' => $exif['COMPUTED']['html']
	 				);
 				} else if (isset($exif['EXIF'])) {
 					
 					return array(
	 					'width' => $exif['EXIF']['ExifImageWidth'],
	 					'height' => $exif['EXIF']['ExifImageLength'],
	 					'html' => 'width="'.$exif['EXIF']['ExifImageWidth'].'" height="'.$exif['EXIF']['ExifImageLength'].'"'
	 				);
 				}
 			}
 		}
 		
 		// Wenn bis hierhin immer noch nichs zurückgegeben wurde, dann direkt auf Datei zugreifen!
		$size = @getimagesize($filePath, $iptcInfo);
 	 	
		// 
		if (is_array($size)) {
			$this->setCachedImageInformations($filePath, 'cmtImageSize', $size);
 		} else {
 			$this->setCachedImageInformations($filePath, 'cmtImageSize', array());
 		}
		
 	 	// Zusätzlich IPTC-Daten speichern
		if (isset($iptcInfo["APP13"])) {
			$this->setCachedImageInformations($filePath, 'cmtIptc', iptcparse($iptcInfo["APP13"]));
		}
		
		return $size;
		
 	}

 	// TODO: Recherchieren, ob das Sinn macht. Var "Orientation in [EXIF][IFDO] war bislang immer 1
 	/**
 	 * public function getOrientation()
 	 * Gibt die Ausrichtung des Bildes zurück (Hoch- oder Querformat)
 	 *
 	 * @param
 	 * @return return_type
 	 */
// 	public function getOrientation() {
// 		
// 	}

 	/**
 	 * 
 	 * public / private / protected function hasThumbnail()
 	 * Enter description here ...
 	 *
 	 * @param unknown_type $filePath
 	 *
 	 * @return return_type
 	 */
 	
 	public function hasEmbeddedThumbnail($filePath) {
 		
 		$imageData = $this->getCachedImageInformations($filePath);
 		
 		if (empty($imageData['cmtExif']['THUMBNAIL'])) {
 			$exifData = $this->getExifData($filePath);
 			$thumbnailData = $exifData['THUMBNAIL'];
 		} else {
 			$thumbnailData = $imageData['cmtExif']['THUMBNAIL'];
 		}
 		
 		if (is_array($thumbnailData)) {
 			return true;
 		} else {
 			return false;
 		}
 	}
 	
 	/**
 	 * public function getEmbeddedThumbnail()
 	 * Ermittelt - sofern vorhanden - das in die Grafik eingebettete Vorschaubild und gibt es entweder gleich in den Ausgabepuffer aus oder als String zurück. 
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 * @param boolean $outputThumbnail Soll das Bild gleich an den Browser gesendet werden oder als String zurückgegeben? Default=> true, Bild wird gesendet
 	 *
 	 * @return string Entweder leerer String oder Bilddatei in String
 	 */
 	public function getEmbeddedThumbnail($filePath, $outputThumbnail = true) {
 		
		if (!$this->hasEmbeddedThumbnail($filePath)) {
 			return '';
 		}
 		
		$thumbnail = exif_thumbnail($filePath, $width, $height, $type);

		if ($thumbnail !== false) {
    		
			if ($outputThumbnail) {
				header('Content-type: ' .image_type_to_mime_type($type));
	    		echo $thumbnail;
	    		exit;
			} else {
				return $thumbnail;
			}
		} else {
			return '';
		}
 	}

 	/**
 	 * public function getEmbeddedThumbnailSize()
 	 * Ermittelt die Dimensionen einer eingebetteten Vorschaugrafik.
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 *
 	 * @return array Array mit folgenden Schlüssel-/Wertpaaren: 'width'=>Breite (Zahl), 'height'=>Höhe(Zahl), 'html'=>Dimensionen als HTML-Tag-Attribute, 'mimeType'=>Mime-Typ(Text)
 	 */
 	public function getEmbeddedThumbnailSize($filePath) {
 		
 		if (!$this->hasEmbeddedThumbnail($filePath)) {
 			return array();
 		}
 		
		$thumbnail = exif_thumbnail($filePath, $width, $height, $type);

		if ($thumbnail !== false) {
    		return array (
    			'width' => $width,
    			'height' => $height,
    			'html' => 'width="'.$width.'" height="'.$height.'"',
    			'mimeType' => image_type_to_mime_type($type)
    		);
		} else {
			return array();
		}
 	}
 	
 	/**
 	 * public function getGpsData()
 	 * Gibt alle GPS-Daten - sofern vorhanden - aus den Exif-Daten eines Bildes zurück
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 *
 	 * @return array Assoziatives, multidimensionales Array mit den standardisierten GPS-Daten oder leeres Array
 	 */
 	public function getGpsData($filePath) {
 		
 		$imageData = $this->getCachedImageInformations($filePath);
 
 		if ($imageData['cmtExif']) {
 			$exifData = $imageData['cmtExif'];
 		} else {
 			$exifData = $this->getExifData($filePath);
 		}

 		// keine GPS-Daten gefunden
 		if (!$exifData['GPS']) {
 			return array();
 		} else {
 			return $exifData['GPS'];
 		}
 		
 	}

 	/**
 	 * 
 	 * public / private / protected function getGpsCoordinates()
 	 * Ermittelt aus den GPS-Koordinaten im Bild den Längen- und Breitengrad als Dezimalwerte (z.B. 51.5747222222,  6.29194444444).
 	 * Von: http://www.quietless.com/kitchen/extract-exif-data-using-php-to-display-gps-tagged-images-in-google-maps/
 	 *
 	 * @param string $filePath Pfad der Bilddatei
 	 *
 	 * @return array Array mit den ermittelten Daten (oder leeres Array): Schlüssel 0/'latitude' => Breitengrad, 1/'longitude' => Längengrad
 	 */
 	// gehört eigentlich in eine eigene GPS-Klasse!
 	public function getGpsCoordinates($filePath) {
 		
 		$gpsData = $this->getGpsData($filePath);
 
 		if (empty($gpsData)) {
 			return array();
 		}

		$lat = $gpsData['GPSLatitude']; 
		$long = $gpsData['GPSLongitude'];
     	
		if (!$lat || !$long) {
			return array();
		}

		// Breitengrad
		$latDegrees = $this->stringDivisionToDecimal($lat[0]);
		$latMinutes = $this->stringDivisionToDecimal($lat[1]);
		$latSeconds = $this->stringDivisionToDecimal($lat[2]);
		$latHemisphere = $gpsData['GPSLatitudeRef'];
 
		// Längengrad
		$longDegrees = $this->stringDivisionToDecimal($long[0]);
		$longMinutes = $this->stringDivisionToDecimal($long[1]);
		$longSeconds = $this->stringDivisionToDecimal($long[2]);
		$longHemisphere = $gpsData['GPSLongitudeRef'];
 
		$latDecimal = $this->gpsCoordinatesToDecimal($latDegrees, $latMinutes, $latSeconds, $latHemisphere);
		$longDecimal = $this->gpsCoordinatesToDecimal($longDegrees, $longMinutes, $longSeconds, $longHemisphere);
 
		$gpsData = array(
			0 => $latDecimal, 
			1 => $longDecimal,
			'latitude' => $latDecimal,
			'longitude' => $longDecimal,
			'latitudeDegrees' => $latDegrees,
			'latitudeMinutes' => $latMinutes,
			'latitudeSeconds' => $latSeconds,
			'latitudeHemisphere' => $latHemisphere,
			'longitudeDegrees' => $longDegrees,
			'longitudeMinutes' => $longMinutes,
			'longitudeSeconds' => $longSeconds,
			'longitudeHemisphere' => $longHemisphere,
			'coordinates' => $latDegrees.'°'.$latMinutes."'".$latSeconds.'"'.$latHemisphere . ', ' .
							 $longDegrees.'°'.$longMinutes."'".$longSeconds.'"'.$longHemisphere
		);
		
		$this->setCachedImageInformations($filePath, 'cmtGpsCoordinates', $gpsData);
		return $gpsData;
	}

	/**
	 * private function gpsCoordinatesToDecimal()
	 * Wandelt GPS-Koordinatenteile in einen Dezimalwert um
	 *
	 * @param float $deg Gradangabe der Koordinate
	 * @param float Minute der Koordinate
	 * @param float Sekunde der Koordinate
	 * @param string Hemisphäre der Koordinate ('S', 'N', 'E', 'W')
	 *
	 * @return float Koordinate als dezimale Fließkommazahl
	 */
	private function gpsCoordinatesToDecimal($deg, $min, $sec, $hemi) {
		$d = $deg + $min/60 + $sec/3600;
		return ($hemi=='S' || $hemi=='W') ? $d*=-1 : $d;
	}

 	// Meta- und Dateidaten zurückliefern
 	public function getImageInformations ($filePath, $params=array()) {
 		
 		$exifData = $this->getExifData($filePath);
 		$gps = $this->getGpsCoordinates($filePath);
 		// Parameter verarbeiten
 		$defaultParams = array(
 			'fileDateFormat' => 'd.m.Y',
 			'fileTimeFormat' => 'H:i',
 			'imageDateFormat' => 'd.m.Y',
 			'imageTimeFormat' => 'H:i',
 		);
 		
 		$params = array_merge($defaultParams, $params);
 		
 		$f = $exifData['FILE'];
 		$c = $exifData['COMPUTED'];
 		$i = $exifData['IFD0'];
 		$e = $exifData['EXIF'];
 		
  		$imageInformations = array(
 			
 			// Dateidaten
 			'fileName' => $f['FileName'],
 			'fileDate' => date($params['fileDateFormat'], $f['FileDateTime']),
 			'fileTime' => date($params['fileTimeFormat'], $f['FileDateTime']),
 			'imageType' => $this->imageTypeToExtensionWrapper[$f['FileType']],
 			'imageTypeNr' => $f['FileType'],
 			'mimeType' => $f['MimeType'],
 			'fileSizeBytes' => $f['FileSize'],
 			'fileSizeKiloBytes' => round($f['FileSize']/1024, 2),
 			'fileSizeMegaBytes' => round($f['FileSize']/1024/1024, 2),
 			
 			// Errechnete Dateidaten
 			'height' => $c['Height'],
 			'width' => $c['Width'],
 			'dimensionsHtml' => $c['html'],
 			'isColor' => $c['IsColor'],
 			
 			// IFDO-Daten
 			'cameraType' => trim($i['Make']),
 			'cameraModel' => trim($i['Model']),
 			'imageXResolution' => $this->stringDivisionToDecimal($i['XResolution']),
 			'imageYResolution' => $this->stringDivisionToDecimal($i['YResolution']),
 			'resolutionUnitName' => $this->resolutionNrToNameWrapper[$i['ResolutionUnit']],
 			'resolutionUnitNr' => $i['ResolutionUnit'],
 			'imageDate' => date($params['imageDateFormat'], $this->dateTimetoTimeStamp($i['DateTime'])),
 			'imageTime' => date($params['imageTimeFormat'], $this->dateTimetoTimeStamp($i['DateTime'])),
 		
 			// Exif-Daten
			'apertureValue' => round($this->stringDivisionToDecimal($e['FNumber']), 2),	// Blende
 			'apertureValueApex' => round($this->stringDivisionToDecimal($e['ApertureValue']), 2),
 			'focalLength' => round($this->stringDivisionToDecimal($e['FocalLength']), 1),		// Brennweite
 		);

 		// GPS-Daten
 		if (!empty($gps)) {
 			$imageInformations = array_merge($gps, $imageInformations);
 			$imageInformations['hasGPS'] = 1;
 		} else {
 			$imageInformations['hasGPS'] = 0;
 		}
 		
 		$this->setCachedImageInformations($filePath, 'cmtInformations', $imageInformations);
		return $imageInformations;
 	}
 	
 	/**
 	 * public function getImageType()
 	 * Ermittelt den Bildtyp mit Hilfe der PHP-Funktion exif_imagetype()
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 *
 	 * @return string Abkürzung des Bildtyps
 	 */
 	public function getImageType($filePath) {
 		
 		$imageType = exif_imagetype($filePath);

 		$this->setCachedImageInformations($filePath, 'cmtInformations', array(
 			'exifImageTypeNr' => $imageType,
 			'imageType' => $this->imageTypeToExtensionWrapper[$imageType]
 		));

 		return $this->imageTypeToExtensionWrapper[$imageType];
 	}

	/**
	 * private function stringDivisionToDecimal()
	 * Wandelt einen in den EXIF-Daten gespeicherten Teil einer Koordinate (z.B. "54/1") in eine Fließkommazahl um.
	 *
	 * @param string $coordPart Der Teil einer in den EXIF-Daten gespeicherten Koordinate ("51/1")
	 *
	 * @return float In dezimale Fließkommazahl umgerechnete Koordinate.
	 */
	private function stringDivisionToDecimal($coordPart) {
		// evaluate the string fraction and return a float //	
		$e = explode('/', $coordPart);
		
		// prevent division by zero //
		if (!$e[0] || !$e[1]) {
      		return 0;
		} else{
			return $e[0] / $e[1];
		}
	}

	public function getImageTypeNrFromExtension($extension='') {
		return $this->extensionToImageTypeWrapper[$extension];
	}
	
	
	/**
	 * private function dateTimeToTimeStamp()
	 * Hilfsfunktion: Erzeugt einen Timestamp aus einem Date(-time)-String.
	 *
	 * @param string $dateTime Das Datum im Datetime-Format (Date geht auch): 2011-05-24 13:06:54.
	 *
	 * @return number Unix-Timestamp
	 */
	private function dateTimeToTimeStamp($dateTime) {
		
		preg_match('/^([0-9]{4})[-:]([0-9]{2})[-:]([0-9]{2})\s?(([0-9]{2}):([0-9]{2}):([0-9]{2}))?$/', trim($dateTime), $match);
		
		// Stunde, Minute, Sekunde, Monat, Tag, Jahr
		return mktime($match[5], $match[6], $match[7], $match[2], $match[3], $match[1]);
	}

	/**
	 * Hilfsfunktion: Gibt Fehler der letzten ausgef�hrten Bildaktion zur�ck
	 * 
	 * @param void Erwartet keine Parameter
	 * @return string Gibt die gespeicherte Fehlermeldung zur�ck
	 */
	 
	 function getLastError() {
	 	return $this->errorMessage;
	 }
	 
	 /**
	  * public function getTextImageDimensions()
	  * Ermittelt die Dimensionen eines Textes (Koordinaten aller Ecken)
	  *
	  * @param array $params Array mit folgenden Schlüssel-/Wert-Paaren:
	  * textSize => Textgröße in Punkt
	  * fontPath => Pfad zur TTF-Datei
	  * textAngle => Drehwinkel in Grad
	  * text => Der Text, dessen Dimensionen ermittelt werden sollen
	  *
	  * @return array Gibt das von der GD-Funktion imagettfbox() gelieferte Array zurück
	  */
	 public function getTextImageDimensions($params) {
	 	return imagettfbbox ($params['textSize'], $params['textAngle'], $params['fontPath'], $params['text']);
	 }
	 
	 /**
	  * public function getTextImageWidth()
	  * Hilfsfunktion: Ermittelt aus getTextImageDimensions() die Breite eines Textes aus dem eine Grafik erstellt werden soll
	  *
	  * @param array $params
	  * @return number
	  */
	 public function getTextImageWidth($params) {
	 	$dimensions = $this->getTextImageDimensions($params);
	 	return intval($dimensions[4]) - intval($dimensions[6]);
	 }
	 
	 /**
	  * public function getTextImageHeight()
	  * Hilfsfunktion: Ermittelt aus getTextImageDimensions() die Höhe eines Textes aus dem eine Grafik erstellt werden soll
	  *
	  * @param array $params
	  * @return number
	  */	
	  public function getTextImageHeight($params) {
		  $dimensions = $this->getTextImageDimensions($params);
		  return intval($dimensions[0]) - intval($dimensions[7]);
	  }

	public function getErrorMessage() {
		return $this->errorMessage;
	}
}
?>
