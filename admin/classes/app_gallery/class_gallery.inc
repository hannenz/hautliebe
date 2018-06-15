<?php
/**
 * class_gallery.inc
 * Klasse stellt alle Methoden zur Erstellung und Anzeige einer Bildergalerie zur Verf�gung.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 */
namespace Contentomat\Gallery;

use \Contentomat\Contentomat;
use \Contentomat\Image;
use \Contentomat\DBCex;
use \Contentomat\Parser;
use \Contentomat\FileHandler;

Class Gallery {
 	
 	protected $image;
 	protected $db;
 	protected $parser;
 	protected $fileHandler;

 	protected $imagesBasePath;
 	protected $thumbnailsBasePath;
 	protected $thumbnailPrefix;
 	protected $thumbnailType;
 	protected $thumbnailWidth;
 	protected $thumbnailHeight;
 	protected $imageTitleFieldName;
 	protected $imageFieldName;
 	protected $internalImageFieldName;
 	
 	public function __construct() {
 		
 		// Setup
 		$this->imagesTable = 'gallery_images';
 		$this->categoriesTable = 'gallery_categories';
 		$this->imagesBasePath = PATHTOWEBROOT.'img/gallery/';
 		$this->thumbnailsBasePath = PATHTOWEBROOT.'img/gallery/thumbnails/';
 		$this->thumbnailPrefix = '';
 		$this->thumbnailType = 'jpg';
 		$this->thumbnailWidth = 160;
 		$this->thumbnailHeight = 120;
 		
 		$this->imageFieldName = 'gallery_image_file';
		$this->internalImageFieldName = 'gallery_image_internal_filename';
		$this->imageTitleFieldName = 'gallery_image_title';
 		
		// weitere Klassenvariablen
 		$this->image = new Image();
 		$this->db = new DBCex();
 		$this->parser = new Parser();
 		$this->fileHandler = new FileHandler();
 	}
 	
  	/**
 	 * public  function getCategories()
 	 * Ermittelt alle Daten aller Kategorien und zusätzlich die Anzahl der Bilder in jeder Kategorie und 
 	 * parst diese in ein zuvor übergebenes Reihen-Template. Wird in der Galerieübersicht verwendet.
 	 *
 	 * @param array $params Assoziatives Array mit folgenden Schlüssel-/ Wertpaaren:
 	 * - 'templateRow' => Pfad des Reihentemplates
 	 * - 'orderDir' => Sortierreihenfolge der Ausgabe
 	 * - 'orderBy' => Name des Feldes, nach welchem die Kategorien sortiert werden sollen
 	 *
 	 * @return string Geparste Daten im Template werden zurückgegeben.
 	 */
 	public function getCategories($params=array()) {
 		
 		$defaultParams = array(
 			'orderDir' => 'ASC',
 			'orderBy' => 'gallery_category_title',
 			'where' => ''
 		);
 		$params = array_merge($defaultParams, $params);

 		$whereClause = '';
 		if ($params['where']) {
 			$whereClause = ' WHERE ' . $params['where'] . ' ';
 		}
 		
 		$this->db->query(
 			"SELECT  categories.*, 
 			 (SELECT COUNT(images.id) 
 			 FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			 WHERE images.gallery_image_category_id = categories.id
 			 ) AS imagesInCategory 
 			 FROM " . $this->db->dbQuote($this->categoriesTable) . " categories " .
 			 $whereClause . "
 			 ORDER BY " . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir']));
 		
 		$categoriesData = array();
 		
 		while ($r = $this->db->get()) {
 			$categoriesData[$r['id']] = $r;	
 		}
 		
 		return $categoriesData;
 	}

 	/**
 	 * public function getCategoryData()
 	 * Gets a categories data.
 	 *
 	 * @param number $categoryID Categories database id.
 	 * @param array $params Optional: 'orderDir' => sort direction 'ASC' or 'DESC', 'oderBy' => field name, 'where' => additional WHERE condition (without leading 'WHERE')
 	 *
 	 * @return array Returns an associative array with the categorie's data (category ID => category data in associative array)
 	 */
 	public function getCategoryData($categoryID, $params=array()) {

 		$categoryID = intval($categoryID);
 		
 		$defaultParams = array(
 			'orderDir' => 'ASC',
 			'orderBy' => 'gallery_category_title',
 			'where' => ''
 		);
 		$params = array_merge($defaultParams, $params);
 		
 		$whereClause = '';
 		if ($params['where']) {
 			$whereClause = ' AND ' . $params['where'] . ' ';
 		}
 		
 		 $this->db->query(
 			"SELECT  categories.*, 
 			 (SELECT COUNT(images.id) 
 			 FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			 WHERE images.gallery_image_category_id = categories.id
 			 ) AS imagesInCategory 
 			 FROM " . $this->db->dbQuote($this->categoriesTable) . " categories 
 			 WHERE categories.id = '".$categoryID."' " . 
 		 	 $whereClause . "
 			 ORDER BY " . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir']));

 		
 		return $this->db->get();
 		
 	}
 	
  	/**
 	 * public function getImagesInCategory()
 	 * Gets all images in a category.
 	 *
 	 * @param number $categoryID Categories database id.
 	 * @param array $params Optional: 'orderDir' => sort direction 'ASC' or 'DESC', 'oderBy' => field name, 'where' => additional WHERE condition (without leading 'WHERE'), 'keyAsID' => if set to false the keys in the returned array are not the ids of the images.
 	 *
 	 * @return array Returns an associative array with all image data (image ID => image data in associative array)
 	 */
  	public function getImagesInCategory($categoryID, $params=array()) {

 		$defaultParams = array(
 			'orderDir' => 'ASC',
 			'orderBy' => 'gallery_image_position',
 			'where' => '',
 			'keyAsID' => true
 		);
 		$params = array_merge($defaultParams, $params);
 		
 		$categoryID = intval($categoryID);
 		$params['orderDir'] = substr($params['orderDir'], 0, 4);
 		$params['orderBy'] = trim($params['orderBy']);
 		
 		// Sicherheitsüberprüfung: Unerlaubte Zeichen? SQL-Injection?
 		// TODO: nötig??? Mit "where" - Paramter geht SQL-Injection noch besser ;-)
 		if (preg_match('/[,\s;]/', $params['orderBy'])) {
 			return array();
 		}
 		
 		$whereClause = '';
 		if ($params['where']) {
 			$whereClause = ' AND ' . $params['where'] . ' ';
 		}
 		
 		$this->db->query(
 			"SELECT *  FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			 WHERE images.gallery_image_category_id = '".$categoryID."' " . 
 			 $whereClause . "
 			 ORDER BY " . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir'])
 		);

 		$imageData = array();
 		$c = 0;
 		
 		while ($r = $this->db->get()) {
 			
 			$i = $this->getImageFileInformations($this->imagesBasePath.$r['gallery_image_internal_filename']);
 			
 			if ($params['keyAsID']) {
 				$imageData[$r['id']] = array_merge($r, $i);
 			} else {
 				$imageData[$c++] = array_merge($r, $i);
 			}
 		}

 		return $imageData;
 	}

 	/**
 	 * public function createCategory()
 	 * Erstellt eine neue Kategorie
 	 *
 	 * @param array $params Assoziatives Array mit den Daten für die neue Kategorie:
 	 * - 'categoryTitle' string Pflichtangabe: Name der neuen Kategorie
 	 * - 'categoryDescription' string Optional: Kurztext, Beschreibung der Kategorie
 	 *
 	 * @return number Entweder die ID der neuen Kategorie, 0 im Fehlerfall und -1 wenn eine Pflichtabgabe fehlt. 
 	 */
 	public function createCategory($params=array()) {
 		
 		if (trim($params['categoryTitle']) == '') {
 			return 0;
 		}
 		
 		$this->db->query("
 			SELECT id FROM " . $this->db->dbQuote($this->categoriesTable) . " categories
 			WHERE categories.gallery_category_title = '".$this->db->dbQuote($params['categoryTitle'])."'"
 		);
 		
 		$r = $this->db->get();
 		
 		// Kategorie existiert bereits
 		if ($r['id']) {
 			return -1;
 		}
 		
 		// Neue Kategorie kann angelegt werden
 		$check = $this->db->query("
 			INSERT INTO " . $this->db->dbQuote($this->categoriesTable) . " 
 			SET gallery_category_title = '".$this->db->dbQuote($params['categoryTitle'])."',
 			gallery_category_description = '".$this->db->dbQuote($params['categoryDescription'])."'"
 		);
 		
 		if (!$check) {
 			return $this->db->getLastInsertedID();
 		} else {
 			return 0;
 		}
 	}

 	/**
 	 * public function deleteCategory()
 	 * Löscht eine Kategorie.
 	 *
 	 * @param number $categoryID ID der Kategorie, die gelöscht werden soll.
 	 *
 	 * @return return_type
 	 */
  	public function deleteCategory($categoryID) {
 		
  		$categoryID = intval($categoryID);
  		
  		$imagesInCategory = $this->getImagesInCategory($categoryID);
		
  		$check = array(
  			'image' => true
  		);
  		
		// Bilder löschen
		if (!empty($imagesInCategory)) {
	  		foreach($imagesInCategory as $image) {
	  			$checkImage = $this->deleteImage($image['id']);
	  			
	  			if (in_array(false, $checkImage)) {
	  				$check['image'] = false;
	  			}
			}
		}
  		
  		// Galerie aus DB löschen
 		$this->db->query("
 			DELETE FROM " . $this->db->dbQuote($this->categoriesTable) . "
 			WHERE id = '".intval($categoryID)."'"
 		);

 		if ($this->db->getLastErrorNr()) {
 			$check['database'] = false;
 		} else {
 			$check['database'] = true;
 		}
	
 		return $check;

 	}

 	/**
 	 * ???
 	 *
 	 * @param array $files	Seems to expect the $_FILES global
 	 *
 	 * @return array	TODO: Why return an emtpy array???
 	 * 
 	 * TODO: Methode stark überarbeitungswürdig! Optionen müssten übergeben werden:
 	 * - Größe der Thumbnails
 	 * - Ob das Originalbild verkleinert werden soll
 	 * - ...
 	 */
 	public function createImageFromUpload($files) {
 		
 		foreach ($files as $fieldName => $fileData) {

 			if (preg_match('/^'.preg_quote($this->imageFieldName).'_newfile$/', $fieldName)) {
 				
 				$fileData['sourceFile'] = $fileData['tmp_name'];
 				$fileData['fileName'] = $fileData['name'];
 				
 				return $this->createImage($fileData);
 			}
 		}
 		
 		return array();
 	}
 	
 	public function createImage($fileData) {
		
 		$returnData = array();
 		
		$fileInfo = pathinfo($fileData['fileName']);
		$fileExtension = strtolower($fileInfo['extension']);
	
		$internalName = $this->createInternalFileName();
		
		// neuen Namen auch in DB speichern!
		//$returnData[$this->internalImageFieldName] = $internalName. '.' . $fileExtension;
		$returnData[$this->internalImageFieldName] = $internalName. '.' . $fileExtension;
		
		// Thumbnail erzeugen
		$returnData['thumbnailCreated'] = $this->image->createThumbnail(array(
			'sourceImage' => $fileData['sourceFile'],
			'sourceImageType' => $fileExtension,
			'destinationImage' => $this->thumbnailsBasePath . $this->thumbnailPrefix . $internalName  . '.' . $fileExtension, // $this->thumbnailType,
			'destinationImageType' => $this->thumbnailType,
			'width' => $this->thumbnailWidth,
			'height' => $this->thumbnailHeight
		));
		$returnData['thumbnailCreatedError'] = $this->image->getLastError();

		// Bild umbenennen
			
		$returnData['fileRenamed'] = $this->fileHandler->moveFile(
			array(
				'sourceFile' => $fileData['sourceFile'],
				'newFilename' => $this->imagesBasePath . $returnData[$this->internalImageFieldName] 
			)
		);

		@ chmod($this->imagesBasePath . $returnData[$this->internalImageFieldName], 0777);

		return $returnData;
 	}
 	
 	public function getInternalImageFieldName() {
 		return trim($this->internalImageFieldName);
 	}
 	
 	
 	/**
 	 * 
 	 * public function deleteImage()
 	 * Löscht ein Bild aus der Datenbank UND physikalisch vom Server.
 	 *
 	 * @param number $imageID ID des Bilder
 	 *
 	 * @return array Liefert ein Array mit drei Schlüsseln 'file', 'thumbnail', 'database'. Jedem Schlüssel 
 	 * ist (boolean)true oder false zugeordent, je nach Erfolg der Aktion. Fherl testen mit if (in_array(false, $check))...
 	 */
 	public function deleteImage($imageID) {
 		
 		$imageID = intval($imageID);
 		$imageData = $this->getImageData($imageID, array('getAdditionalInformations' => false));
 		
 		$check = array();
 		
 		$check['file'] = $this->fileHandler->deleteFile(array(
  			'file' => $this->imagesBasePath.$imageData['gallery_image_internal_filename']
  		));
  		
  		$check['thumbnail'] = $this->fileHandler->deleteFile(array(
  			'file' => $this->thumbnailsBasePath.$imageData['gallery_image_internal_filename']
  		));
  		
		$check['database'] = $this->db->query("
			DELETE FROM ".$this->imagesTable." 
			WHERE id = '".$imageID."' 
			LIMIT 1	
		");
		
		if (!$check['database']) {
			$check['database'] = true;
		} else {
			$check['database'] = false;
		}
		
		return $check;
 		
 	}
 	
  	/**
 	 * public function getImageData()
 	 * Gibt die Bilddaten (Exif- und Galerie-Informationen) als assoziatives Array zurück. 
 	 *
 	 * @param number $imageID Bild-ID
 	 * @param array $params Optionale Parameter
 	 * - 'getAdditionalInformations' => Optional: Liest auch die Exif-Dateiinformationen aus. Default: true
 	 *
 	 * @return array Assoziatives Array
 	 */
 	public function getImageData($imageID, $params=array()) {

 		$defaultParams = array(
 			'getAdditionalInformations' => true
 		);
 		$params = array_merge($defaultParams, $params);
 		
 		$imageID = intval($imageID);
 		$this->db->query("
 			SELECT * FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			WHERE images.id = '".$imageID."'
 			LIMIT 1
 		");
 		
 		$r = $this->db->get();
 		
 		if (!$r) {
 			return array();
 		} else {
 			
 			if ($params['getAdditionalInformations']) {
	 			$r['imagesBasePath'] = '/' . preg_replace('/^(..\/)?/', '', $this->getImagesBasePath());
	 			
	 			// hier noch weitere Bilddaten aus class_image.php holen
	 			$i = $this->getImageFileInformations($this->getImagesBasePath().$r[$this->getInternalImageFieldName()]);
	 			
	 			return array_merge($i, $r);
 			} else {
 				return $r;
 			}
 		}
 	}

 	
 	/**
 	 * 
 	 * public function moveImage()
 	 * Verschiebt ein Bild in einen anderen Ordner.
 	 *
 	 * @param number $imageID ID des Bildes
 	 * @param number $categoryID ID der neuen Kategorie
 	 *
 	 * @return boolean
 	 */
 	public function moveImage($imageID, $categoryID) {
 		
 		$imageID = intval($imageID);
 		$categoryID = intval($categoryID);
 		
 		// Zuerst alte Kategorie des Bildes ermitteln
 		$this->db->query("
 			SELECT gallery_image_category_id AS old_category 
 			FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			WHERE images.id = '".$imageID."'
 			LIMIT 1
 		");
 		$r = $this->db->get();
 		$oldCategoryID = $r['old_category'];
 		
 		// Letzte Position in neuer Kategorie ermitteln
 		$this->db->query("
 			SELECT MAX(gallery_image_position) AS last_position 
 			FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			WHERE images.gallery_image_category_id = '".$categoryID."'
 			LIMIT 1
 		");
 		$r = $this->db->get();
 		
 		if ($this->db->getLastErrorNr()) {
 			return false;
 		} else {
 			$newPosition = intval($r['last_position']) + 1;
 			$this->db->query("
	 			UPDATE " . $this->db->dbQuote($this->imagesTable) . " 
	 			SET gallery_image_category_id = '".$categoryID."', 
	 			gallery_image_position = '" . $newPosition ."' 
	 			WHERE id = '".$imageID."'
	 			LIMIT 1
 			");
 			
 			if ($this->db->getLastErrorNr()) {
 				return false;
 			} else {
 				$this->cleanUpImagesOrderInCategory($oldCategoryID);
 				return true;
 			}
 		}
 	}
 	
 	public function cleanUpImagesOrderInCategory($categoryID) {
 		
 		$categoryID = intval($categoryID);
 		$this->db->query("SET @posCounter=0");
 		$this->db->query("
 			UPDATE " . $this->db->dbQuote($this->imagesTable) . " 
 			SET gallery_image_position = @posCounter := @posCounter + 1 
 			WHERE gallery_image_category_id = '" . $categoryID . "' 
 			ORDER BY gallery_image_position ASC"
 		);
 		
 	}
 	
 	/**
 	 * public function countImagesInCategories()
 	 * Ermittelt die Anzahl der Bilder in allen Kategorien.
 	 *
 	 * @param void
 	 * @return array Array mit den Kategorie-IDs und der Anzahl an Bilder in der jeweiligen Kategorie als Schlüssel-/Wert-Paar
 	 */
 	public function countImagesInCategories() {
 		
 		 $this->db->query(
 			"SELECT COUNT(images.id) AS images_in_category, images.gallery_image_category_id
 			 FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			 GROUP BY gallery_image_category_id"
 		);
 		
 		$imagesInCategories = array();
 		
 		while ($r = $this->db->get()) {
 			$imagesInCategories[$r['gallery_image_category_id']] = $r['images_in_category'];
 		}
 		
 		return $imagesInCategories;
 	}

  	/**
 	 * public function countImagesInCategory()
 	 * Ermittelt die Anzahl der Bilder in einer Kategorie.
 	 *
 	 * @param number $categoryID
 	 * @return number Anzahl der Bilder in einer Kategorie
 	 */
 	public function countImagesInCategory($categoryID) {
 		
 		$categoryID = intval($categoryID);
 		
 		$this->db->query(
 			"SELECT COUNT(images.id) AS images_in_category
 			 FROM " . $this->db->dbQuote($this->imagesTable) . " images 
 			 WHERE gallery_image_category_id ='" . $categoryID . "'"
 		);
 		
 		$r = $this->db->get();
 		
 		return intval($r['images_in_category']);
 	}
 	
	/**
	 * OUTDATED / TODO: public function showThumbnail()
	 * Es wird nach einem Thumbnail des Bildes gesucht und ausgegeben.
	 *
	 * @param string $fileName Pfad des Bildes
	 *
	 * @return string Das Bild wird direkt per Header ausgegeben.
	 */
 	public function showThumbnail($fileName) {
 		return $this->image->getEmbeddedThumbnail($fileName);
 	}

 	/**
 	 * public function showThumbnailByID()
 	 * Ermittelt das Thumbnail eines Bildes (in der Reihenfolge integriertes Thumbnail, erzeugtes Thumbnail)
 	 *
 	 * @param number $imageID ID des Bildes
 	 * @param array $params Optionale Parameter
 	 *
 	 * @return return_type
 	 */
  	public function showThumbnailByID($imageID, $params=array()) {

  		$defaultParams = array(
  			'showEmbeddedThumbnail' => false
  		);
 		$params = array_merge($defaultParams, $params);
 		
  		$this->db->query(
  		 	"SELECT * FROM ".$this->db->dbQuote($this->imagesTable) . " " . 
 		 	"WHERE id = '" . intval($imageID) . "' " .
  		 	"LIMIT 1" 
  		 );
  		$r = $this->db->get();

  		// Soll das eingebettete oder das gespeicherte Thumbnail gezeigt werden?
  		if ($params['showEmbeddedThumbnail']) {
 			echo $this->image->getEmbeddedThumbnail($this->getImagesBasePath().$r['gallery_image_file']);
  		} else {
  			
  			$thumbnailFile = $this->thumbnailsBasePath . $r['gallery_image_internal_filename'];
			$fileInfo = pathinfo($thumbnailFile);
			$fileExtension = strtolower($fileInfo['extension']);
		  			
  			header('Content-type: ' . image_type_to_mime_type($this->image->getImageTypeNrFromExtension($fileExtension)));
			echo file_get_contents($thumbnailFile);
  		}
 		exit();
 	}
 
  	/**
 	 * public function saveImagesOrder()
 	 * Speichert die Reihenfolge der Bilder
 	 *
 	 * @param array $imagesOrder Array mit den Bild-IDs
 	 * @param number $offset Offset, Zahl mit welcher die Reihenfolge beginnt (z.B. 20 => Bild 1: 20, Bild 2: 21, bild 3: 22 ...
 	 *
 	 * @return boolean
 	 */
 	public function saveImagesOrder($imagesOrder=array(), $offset=0) {
 		
 		if (!is_array($imagesOrder)) {
 			return false;
 		}
 		
 		// übergebene Werte säubern und ggf. Seiten-Offset zur Position hinzuzählen
 		foreach ($imagesOrder as $imageID => $position) {
 			$imagesOrder[intval($imageID)] = intval($position) + $offset; 
 		}
 		
 		// speichern
 		foreach ($imagesOrder as $imageID => $position) {
 			$this->db->query("
 				UPDATE " . $this->db->dbQuote($this->imagesTable) . "
 				SET gallery_image_position = '" . $position . "' 
 				WHERE id = '". $imageID ."'"
 			);
 			
 			if ($this->db->getLastError()) {
 				return false;
 			}
 		}
 		
 		return true;
 	}

	/**
	 * public function deleteImageFiles()
	 * Löscht die Bilddateien 
	 *
	 * @param string $fileName Interner Name des Bildes
	 *
	 * @return void
	 */
 	public function deleteImageFiles($fileName) {
		
 		// Thumbnail löschen
 		$this->fileHandler->deleteFile(array('file' => $this->thumbnailsBasePath . $fileName));
 		
 		// Bild löschen
 		$this->fileHandler->deleteFile(array('file' => $this->imagesBasePath . $fileName));
 	}
 	
 	/**
 	 * 
 	 * public function createInternalFileName()
 	 * Erzeugt einen nahezu eindeutigen Dateinamen aus Unix-timestamp und Microtime
 	 *
 	 * @param void
 	 * @return string Zahlenreihe verwendbar als Dateiname (wird ohne Dateiendung zurückgegeben!)
 	 */
 	public function createInternalFileName() {
 		return str_replace('.', '', strval(microtime(true)));
 	}
 	
// TODO: Erzeugung der Thumbnails via Thumbnailer ggf. hier einbauen: 2014-03-07 ????
/*
 	public function createThumbnail() {
 		
 	}
 */	
 	public function getImagesBasePath() {
 		return $this->imagesBasePath;
 	}
 	
 	/**
 	 * 
 	 * public function getImageFileInformations()
 	 * Liest die Informationen einer Bilddatei aus. 
 	 *
 	 * @param string $filePath Pfad zur Bilddatei
 	 * @return array Array mit Bildinformationen oder leeres Array bei Fehler
 	 */
 	public function getImageFileInformations($filePath) {
 		return $this->image->getImageInformations($filePath);
 	}
 	
 	
 	/**
 	 * public function setVars()
 	 * Erlaubt das Setzen mehrerer per Array übergebener Klassenvariablen
 	 *
 	 * @param array $vars Assoziatives Array mit den Klassenvariablennamen und neuen Werten als Schlüssel-/ Wertpaar.
 	 *
 	 * @return void
 	 */
 	public function setVars($vars=array()) {
 		
 		foreach ($vars as $key => $value) {
 			if (property_exists($this, $key)) {
 				$this->$key = $value;
 			}
 		}

 	}
 	
 	/**
 	 * public function getCategoriesTableName()
 	 * Liefert den Namen der aktuellen Bilderkategorientabelle (Datenbank)
 	 *
 	 * @param void
 	 * @return string Name der Datenbanktabelle der Bilderkategorien
 	 */
  	public function getCategoriesTableName() {
 		return $this->categoriesTable;
 	}

 	 /**
 	 * public function getImagesTableName()
 	 * Liefert den Namen der aktuellen Bildertabelle (Datenbank)
 	 *
 	 * @param void
 	 * @return string Name der Datenbanktabelle der Bilder
 	 */
  	public function getImagesTableName() {
 		return $this->imagesTable;
 	}
 	
 	/**
 	 * public function getFirstImageInCategory()
 	 * Returns the last image of a given category
 	 *
 	 * @param number $categoryID The category's datasbase id
 	 * @param array $params Optional parameters in an associative array: 'startAt' => start position
 	 *
 	 * @return array The image's dataset
 	 */
 	public function getFirstImageInCategory($categoryID, $params=array()) {
 			
 		if (!is_array($params)) {
 			$params = array();
 		}
 			
 		$params = array_merge(array('limit' => 1), $params);
 			
 		return array_pop($this->getImagesInCategory($categoryID, $params));
 	} 	
 }

?>