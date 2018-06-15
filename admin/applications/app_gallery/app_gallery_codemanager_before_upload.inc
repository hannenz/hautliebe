<?php
/**
 * app_gallery_codemanager_before_upload.inc
 * Codemanager include file: Creates a thumbnail of the uploaded gallery picture
 * 
 * @author J.Hahn <info@buero-hahn.de>
 * @version 2016-09-21
 */
namespace Contentomat\Gallery;

use \Contentomat\ApplicationHandler;
use \Contentomat\Image;
use \Contentomat\FileHandler;
use \Contentomat\Contentomat;

	require_once(PATHTOADMIN.'classes/app_gallery/class_gallery.inc');
	
	$img = new Image();
	$gallery = new Gallery();
	$fileHandler = new FileHandler();
	$appHandler = new ApplicationHandler();
	$cmt = Contentomat::getContentomat();
	
	$cmt_settings = $appHandler->getApplicationSettings(CMT_RETURNTO);

	$tableData = $cmt->getVar('cmtTableDataRaw');

	// Variablen aus Einstellungen übergeben
	$gallery->setVars(array(
		'thumbnailWidth' => $cmt_settings['thumbnail_width'],
		'thumbnailHeight' => $cmt_settings['thumbnail_height'],
		'imagesBasePath' => PATHTOWEBROOT.$cmt_settings['images_base_path'],
		'thumbnailsBasePath' => PATHTOWEBROOT.$cmt_settings['thumbnails_base_path'],
		'imageFieldName' => $cmt_settings['thumbnail_from_field']
	));
	
	if ($cmt_settings['table_images']) {
		$gallery->setVars(array(
			'imagesTable' => $cmt_settings['table_images']
		));
	}
	
	if ($cmt_settings['table_categories']) {
		$gallery->setVars(array(
			'categoriesTable' => $cmt_settings['table_categories']
		));
	}

	$returnData = $gallery->createImageFromUpload($_FILES);

	// hier noch ein && $cmt_settings['delete_old_file'] == true einbauen beim nächsten Update der Einstellungen
	if ($returnData['thumbnailCreated'] && $returnData['fileRenamed']) {
		$gallery->deleteImageFiles($tableData[$gallery->getInternalImageFieldName()]);
	}

	// Neu erzeugten Dateinamen in Tabelle speichern
	$tableData[$gallery->getInternalImageFieldName()] = $returnData[$gallery->getInternalImageFieldName()];

	// new method: does not work properly!
	//$cmt->setVar('cmtTableDataRaw', $tableData);
	$cmtTableDataRaw = $tableData;
	// Datei nach Upload nicht kopieren, da dies die class_gallery.inc bereits erledigt hat
	$cmt_copynewfile = false;
	$cmt->setVar('cmtCopyNewFile', $cmt_copynewfile);
	
?>