<?php
/**
 * app_gallery.inc
 * Kurzbeschreibung
 * 
 * @author J.Hahn <info@buero-hahn.de>
 * @version 2014-05-19
 */
namespace Contentomat\Gallery;

use \Contentomat\Contentomat;
use \Contentomat\Image;
use \Contentomat\Parser;
use \Contentomat\FileHandler;

	include_once ('classes/class_image.php');
	include_once ('classes/app_gallery/class_gallery.inc');
	
	$gallery = new Gallery();
	$parser = new Parser();
	$fileHandler = new FileHandler();
	$cmt = Contentomat::getContentomat();
	$cmt_settings = $cmt->getVar('cmt_settings');

	$tplPath = PATHTOADMIN.'templates/default/app_gallery/';
	$tplOverview = 'cmt_gallery.tpl';
	$tplOverviewImage = 'cmt_gallery_overview_image.tpl';

	// Variablen aus Einstellungen übergeben
	if (!$cmt_settings['thumbnail_title_from_field']) {
		$cmt_settings['thumbnail_title_from_field'] = 'gallery_image_title';
	}
	$gallery->setVars(array(
		'thumbnailWidth' => $cmt_settings['thumbnail_width'],
		'thumbnailHeight' => $cmt_settings['thumbnail_height'],
		'imagesBasePath' => PATHTOWEBROOT . $cmt_settings['images_base_path'],
		'thumbnailsBasePath' => PATHTOWEBROOT . $cmt_settings['thumbnails_base_path'],
		'imagesTable' => $cmt_settings['table_images'],
		'categoriesTable' => $cmt_settings['table_categories'],
		'imageFieldName' => $cmt_settings['thumbnail_from_field'],
		'imageTitleFieldName' => $cmt_settings['thumbnail_title_from_field']
	));
	
	// Falls Formulardaten übermittelt wurden (bei Ajax-Requests)
	$dataParts = explode('&', $_REQUEST['data']);
	foreach ($dataParts as $dataPart) {
		preg_match('/(.*)=(.*)/', $dataPart, $match);
		$data[urldecode($match[1])] = urldecode($match[2]);
	}

	// Parameter: Aktion
	if ($data['galleryAction']) {
		$galleryAction = trim($data['galleryAction']);
	} else {
		$galleryAction = trim($_REQUEST['galleryAction']);
		//echo "Request: $galleryAction";
	}

	// Parameter: Bild-ID
	if ($data['galleryImageID']) {
		$imageID = intval($data['galleryImageID']);
	} else {
		$imageID = trim($_REQUEST['galleryImageID']);
	}
	$parser->setParserVar('galleryImageID', $imageID);
	
	// Parameter: Kategorie-ID
	if ($data['galleryCategoryID']) {
		$categoryID = intval($data['galleryCategoryID']);
	} else {
		$categoryID = trim($_REQUEST['galleryCategoryID']);
	}
	if (!$categoryID && $imageID) {
		$imageData = $gallery->getImageData($imageID);
		$categoryID = $imageData['gallery_image_category_id'];
	}
	$parser->setParserVar('galleryCategoryID', $categoryID);
	
	$fileName = urldecode(trim($_REQUEST['fileName']));

	// Seitentitel
	$parser->setParserVar('tableTitle', $cmt_settings['cmt_showname']);
	

	/*
	 * AJAX-Aktionen
	 */
	switch ($galleryAction) {
		
		/*
		 *  Neue Kategorie erstellen
		 */
		case 'newCategory':
//			die('Location: '.SELFURL.'&cmt_returnto='.CMT_APPID.'&cmt_returnto_params='.urlencode('galleryAction=showGallery&galleryCategoryID=0').'&action=new&cmt_dbtable=' . $gallery->getCategoriesTableName(). '&id[]='.$categoryID.'&cmtDialog=true');
			header('Location: '.SELFURL.'&cmt_returnto='.CMT_APPID.'&cmt_returnto_params='.urlencode('galleryAction=showGallery&galleryCategoryID=0').'&action=new&cmt_dbtable=' . $gallery->getCategoriesTableName(). '&id[]='.$categoryID.'&cmtDialog=true');
			exit();
		
			break;

		/*
		 * Kategorie bearbeiten
		 */ 
		case 'editCategory':
			// die('Location: '.SELFURL.'&cmt_returnto='.CMT_APPID.'&cmt_returnto_params='.urlencode('galleryAction=showGallery&galleryCategoryID=0').'&action=edit&cmt_dbtable=' . $gallery->getCategoriesTableName(). '&id[]='.$categoryID.'&cmtDialog=true');
			header('Location: '.SELFURL.'&cmt_returnto='.CMT_APPID.'&cmt_returnto_params='.urlencode('galleryAction=showGallery&galleryCategoryID=0').'&action=edit&cmt_dbtable=' . $gallery->getCategoriesTableName(). '&id[]='.$categoryID.'&cmtDialog=true');
			exit();
			break;

		/*
		 * Kategorie löschen: Kategorie wirklich löschen nach Nachfrage
		 */ 
		case 'deleteCategory':
			$categoryDeleted = $gallery->deleteCategory($categoryID);

			if (!in_array(false, $categoryDeleted)) {
				$messageType = 'success';
			} else {
				$messageType = 'error';
			}
			header('Location: '.
				SELFURL . 
				'&galleryAction=showCategory&galleryMessage=categoryDeleted&galleryMessageType=' . $messageType
			);
			
			exit();
			break;


		/*
		 * Neues Bild 
		 */
		case 'newImage':
			header('Location: ' . 
				SELFURL .
				'&cmt_returnto=' . CMT_APPID .
				'&cmt_returnto_params=' . urlencode('galleryAction=showCategory&galleryCategoryID='.intval($categoryID)) . 
				'&action=new&cmt_dbtable=' . $gallery->getImagesTableName(). '&gallery_image_category_id='.intval($categoryID) .
				'&cmtDialog=true'
			);
			exit();
			break;
			
		/*
		 * Bilddaten bearbeiten
		 */ 
		case 'editImage':
			header('Location: '.
				SELFURL . 
				'&cmt_returnto=' . CMT_APPID . 
				'&cmt_returnto_params=' . urlencode('galleryAction=showCategory&galleryCategoryID=' . intval($categoryID)) . 
				'&action=edit&cmt_dbtable=' . $gallery->getImagesTableName(). '&id[]='.intval($imageID) . 
				'&cmtDialog=true');
			exit();
			break;
			
		/*
		 *  Bild löschen: Bild wirklich löschen nach Nachfrage
		 */
		case 'deleteImage':
			$imageDeleted = $gallery->deleteImage($imageID);
			if (!in_array(false, $imageDeleted)) {
				$messageType = 'success';
			} else {
				$messageType = 'error';
			}
			header('Location: '.
				SELFURL . 
				'&galleryAction=showCategory&galleryCategoryID=' . intval($categoryID) . 
				'&galleryMessage=imageDeleted&galleryMessageType=' . $messageType
			);
			exit();
			break;
			
		/*
		 * Verschiebt ein Bild in eine andere Kategorie
		 */
		case 'moveImage':
			
			$check = $gallery->moveImage($imageID, $categoryID);
			$imagesInCategories = $gallery->countImagesInCategories();
			
			echo json_encode(array('imageMoved' => $check, 'imagesInCategories' => $imagesInCategories));
			exit();
			break;
			
		/*
		 * Thumbnail anzeigen (wird direkt als src-Attribut eines <img>-Tags aufgerufen
		 */ 
		case 'showThumbnail':

			echo $gallery->showThumbnailByID($imageID);
			exit();
			break;

		/*
		 * Bild anzeigen
		 */
		case 'showImage':
			$imageData = $gallery->getImageData($imageID, $params=array());
			$imageData['imageTitle'] = $imageData[$cmt_settings['thumbnail_title_from_field']];
			
			$parser->setMultipleParserVars($imageData);
			
			$parser->setParserVar('imagesBasePath', $gallery->getImagesBasePath());
			
			echo utf8_encode($parser->parseTemplate($tplPath.'cmt_gallery_image_show.tpl'));
			exit();
			break;

		/*
		 * Bildinformationen anzeigen
		 */ 
		case 'showImageInfos':
			$imageInformations = $gallery->getImageData($imageID);
			$parser->setMultipleParserVars($imageInformations);
			
			echo utf8_encode($parser->parseTemplate($tplPath.'cmt_gallery_image_show_informations.tpl'));
			exit();
			break;

		/*
		 * Kategorien anzeigen
		 */ 
		// TODO: Template-Anzeige aus Klasse entfernen!!
		case 'showCategories':
			echo $gallery->getCategories(array(
				'templateRow' => CMT_TEMPLATE.'app_gallery/cmt_gallery_category.tpl'
			));

			exit();
			break;

		/*
		 * Bilder einer Kategorie anzeigen
		 */ 
		case 'showImages':
			$categoryImagesData = $gallery->getImagesInCategory($categoryID);
			$imageTemplate = file_get_contents(CMT_TEMPLATE.'app_gallery/cmt_gallery_image.tpl');
			$imagesContent = '';
			
			foreach ($categoryImagesData as $imageData) {
				
				$imageData['imageTitle'] = $imageData[$cmt_settings['thumbnail_title_from_field']];
				$parser->setMultipleParserVars($imageData);
				
				$imagesContent .= $parser->parse($imageTemplate);
			}
			
			echo utf8_encode($imagesContent);
		
			exit();
			break;

		/*
		 * Reihenfolge der Bilder in einer Kategorie speichern
		 */
		case 'saveImagesOrder':
			$check = $gallery->saveImagesOrder($_REQUEST['galleryImageIDs']);
			
			if ($check) {
				$messageType = 'success';
			} else {
				$messageType = 'error';
			}
			
			// Das hier, falls das per Ajax funktionieren soll.
			echo json_encode(array('galleryMessageType' => $messageType, 'galleryMessage' => 'categorySaved'));
			
//			header('Location: '.
//				SELFURL . 
//				'&galleryAction=showCategory&galleryCategoryID=' . intval($categoryID) . 
//				'&galleryMessage=categorySaved&galleryMessageType=' . $messageType
//			);
			exit();
			break;

		/*
		 * Bild herunterladen
		 */
		case 'downloadImage':
			
			$imageInformations = $gallery->getImageData($imageID);
			$basePath = $gallery->getImagesBasePath();
			
			$fileHandler->handleDownload(
				array(
					'downloadFile' => $basePath . $imageInformations['gallery_image_internal_filename'],
					'downloadFileAlias' => $imageInformations['gallery_image_file']
				)
			);
			exit;
			break;
			
		default:
			/*
			 * Entweder Galerie-�bersicht oder Bilder einer Galerie ausgeben
			 */
			if (!$categoryID) {

				$parser->setParserVar('galleryView', 'galleryOverview');
								
				$categoriesData = $gallery->getCategories();
				$categoryTemplate = file_get_contents(CMT_TEMPLATE.'app_gallery/cmt_gallery_category.tpl');
				$galleryContent = '';

				foreach ($categoriesData as $categoryData) {
					$parser->setMultipleParserVars($categoryData);
					
					$galleryContent .= $parser->parse($categoryTemplate);
				}
				
				
			} else {
				
				// 1. Bilderdaten holen und parsen
				$categoryImagesData = $gallery->getImagesInCategory($categoryID);
				$imageTemplate = file_get_contents(CMT_TEMPLATE.'app_gallery/cmt_gallery_image.tpl');

				$parser->setParserVar('galleryView', 'categoryOverview');
				$parser->setParserVar('categoryID', $categoryID);

				$galleryContent = '';
				
				foreach ($categoryImagesData as $imageData) {
					
					$imageData['imageTitle'] = $imageData[$cmt_settings['thumbnail_title_from_field']];
					$parser->setMultipleParserVars($imageData);
					
					$galleryContent .= $parser->parse($imageTemplate);
				}
				
				$categoryData = $gallery->getCategoryData($categoryID);
				$parser->setMultipleParserVars($categoryData);
				
				// 2. Kategorien für Sidebar holen und parsen
				$categoriesData = $gallery->getCategories();
				$categoryTemplate = file_get_contents(CMT_TEMPLATE.'app_gallery/cmt_gallery_category.tpl');
				$categoryList = '';

				foreach ($categoriesData as $categoryData) {
					$parser->setMultipleParserVars($categoryData);
					
					$categoryList .= $parser->parse($categoryTemplate);
				}
				
				$parser->setParserVar('categoryList', $categoryList);

				// 3. Kategoriedaten an Parser
				$categoryData = $gallery->getCategoryData($categoryID);
				$parser->setMultipleParserVars($categoryData);
				$parser->setParserVar('categoryID', $categoryID);
				
			}

			$parser->setParserVar('galleryContent', $galleryContent);
			$replace = $parser->parseTemplate($tplPath.$tplOverview);			
			break;
			
	}
	


?>