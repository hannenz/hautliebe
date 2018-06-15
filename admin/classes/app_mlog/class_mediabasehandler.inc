<?php
/**
 * class_mediabase.inc
 * Base class for media files.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2017-07-24
 * @package MLog
 *
 */

namespace Contentomat\MLog;
use Contentomat\FileHandler;
use Contentomat\Contentomat;
use Contentomat\DBCex;

class MediaBaseHandler {

	protected $cmt;
	protected $db;
	protected $fileHandler;
//	protected $uploader;
	protected $locale = 'de_DE' ;
	
	/**
	 * @var string $uploadFileFieldName
	 * Store uploaded files (original filenames) in this database field.
	 */
	protected $uploadFileFieldName = 'media_file';
	
	/**
	 * @var string $uploadPath
	 * Destination path for file uploads
	 */
	protected $uploadPath;
	
	/**
	 * @var string $uploadedFileName
	 * Created file name after upload,
	 */
	protected $uploadedFileName = '';

	/**
	 * @var string $mediaType
	 * Name of media type, e.g. 'image', 'document'. Must be set in function init() of extending class.
	 */
	protected $mediaType;
	
	/**
	 * @var string $mediaTableName
	 * Database table name in which media entries are stored
	 */
	protected $mediaTableName = 'mlog_media';
	
	/**
	 * @var string $mediaTypesTableName
	 * Database table name in which available media types and media type settings are stored
	 */
	protected $mediaTypesTableName = 'mlog_media_types';
	
	/**
	 * @var $mediaSettings
	 * Array to store media specific settings
	 */
	protected $mediaSettings = array();
	
//	protected $mediaFieldName;
	
	/**
	 * @var $fileTypes
	 * List of allowed file types for the media file
	 */
	protected $fileTypes = array();
	
	/**
	 * @var array $mediaDataFields
	 * database field names for the specific media type
	 */
	protected $mediaDataFields = array();
	
	/**
	 * @var array $availableMediaTypes
	 * Available media types are stored in this array.
	 */
	protected $availableMediaTypes = array();

	
	public function __construct() {
		
		$this->cmt = Contentomat::getContentomat();
		$this->fileHandler = new FileHandler();
		$this->db = new DBCex();
		
		$this->mediaTableName = 'mlog_media';
		$this->mediaTypesTableName = 'mlog_media_types';

		$this->fileTypes = array();
		$this->mediaType = '';
//		$this->mediaFieldName = '';
		
		$this->setMediaDataFields(array(
			'media_date' => date('Y-m-d H:i:s'),
			'media_start_date' => date('Y-m-d H:i:s'),
			'media_end_date' => '0000-00-00 00:00:00',
			'media_status' => 0,							// 0 = new uploaded file, 99 = deleted file
			'media_post_id' => 0,
			'media_title' => ''
		));
		
		$this->availableMediaTypes = $this->getAvailableMediaTypes();
		
		$this->init();
	}
	
	protected function init() {
	
	}

	/**
	 * public function setFileTypes()
	 * Sets the allowed file types for upload
	 * 
	 * @param array $fileTypes
	 * @return boolean
	 */
	public function setFileTypes($fileTypes) {
		
		if (!is_array($fileTypes)) {
			return false;
		} else {
			$this->fileTypes = $fileTypes;
		}
		
		return true;
	}
	
	/**
	 * public function checkFileType()
	 * check a file type (e.g. 'jpg') against the list of allowed file types
	 * 
	 * @param string $fileType
	 * @return boolean
	 */
	public function checkFileType($fileType) {
		
		if (in_array(strtolower(trim($fileType)), $this->fileTypes)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * public function setUploadPath()
	 * Sets the upload (destination) path for the uploaded file
	 * 
	 * @param string $path
	 * @return string the cleaned path
	 */
	public function setUploadPath($path) {
		
		$this->uploadPath = $this->fileHandler->cleanPath($this->cmt->getPathToWebRoot() . $path);
		return $this->uploadPath;
	}
	
	/**
	 * public function saveMedia()
	 * Saves a media file
	 * 
	 * @return number
	 */
	public function saveMedia($params=array()) {
		
		$defaultParams = $this->mediaDataFields;
		$params = array_merge($defaultParams, $params);
		
		$mediaData = $params;

		// some prework
		foreach($this->mediaDataFields as $key => $value) {
				
			if ($_REQUEST[$key]) {
				$mediaData[$key] = $_REQUEST[$key];
			}
		}

		if (!$mediaData['media_post_id']) {
			$mediaData['media_post_id'] = intval($_REQUEST['postId']);
		}
		
		if (!$mediaData['id']) {
			$mediaData['id'] = intval($_REQUEST['mediaId']);
		}
		
		// do upload if neccessary;
		$check = $this->uploadMedia();

		// an error occured? return error code
		if ($check && is_numeric($check)) {
			$mediaData['error'] = $check;
			return $mediaData;
		}
		
		// if 0 is returned and an uploaded file's name exists, it is the new filename!
		$uploadedFileName = $this->getUploadedFileName();
		if ($uploadedFileName) {
			//$mediaData['media_' . $this->mediaType] = $uploadedFileName;
			$mediaData['media_internal_filename'] = $uploadedFileName;
		}
		
		$mediaData = $this->beforeSaveMedia($mediaData);

		/* J.Braun: 2016-09-22: Fix for compat. with MySQL 5.7, */
		if ($mediaData['media_end_date'] == '0000-00-00 00:00:00') {
			$mediaData['media_end_date'] = '2099-12-31 00:00:00';
		}
		if (empty($mediaData['media_position'])) {
			$mediaData['media_position'] = 0;
		}



		// no error? then store the media in database
		$id = intval($mediaData['id']);
		unset($mediaData['id']);
		
		if ($id) {
			$query = "UPDATE " . $this->mediaTableName . " SET " . $this->db->makeSetQuery($mediaData) . " WHERE id='" . $id . "'";
		} else {
			$query = "INSERT INTO " . $this->mediaTableName . " SET " . $this->db->makeSetQuery($mediaData) . ", media_is_active = '0' ";
		}

		$this->db->query($query);
		
		if (!$id) {
			$mediaData['id'] = $this->db->getLastInsertedId();
			$mediaData['mediaIsNew'] = true;
		} else {
			$mediaData['id'] = $id;
			$mediaData['mediaIsNew'] = false;
		}
		
		$mediaData['error'] = 0;
		$mediaData['mediaType'] = $this->availableMediaTypes[$mediaData['media_type']]['media_type_title'];

		$mediaData = $this->formatMediaData($mediaData);
		$mediaData = $this->afterSaveMedia($mediaData);

		return $mediaData;
	}
	
	public function updateMediaData($params) {
		
		$mediaId = intval($params['mediaId']);
		$mediaData = (array)$params['mediaData'];
		
		$this->db->query("SELECt * FROM mlog_media WHERE id = '" . $mediaId . "'");
// 		var_dump("SELECt * FROM mlog_media WHERE id = '" . $mediaId . "'");
// 		var_dump ($this->db->getAll());
// 		die();
		
		$query = "UPDATE " . $this->mediaTableName . " SET " . $this->db->makeSetQuery($mediaData) . " WHERE id='" . $mediaId . "'";

		$check = $this->db->query($query);
		return !(bool)$check;
	}

	/**
	 * protected function beforeSaveMedia()
	 * Method is called before media is saved, override in extending media classe. The $mediaData array MUST be returned from this method.
	 *
	 * @param array $mediaData
	 * @return array
	 */
	protected function beforeSaveMedia($mediaData) {
		$mediaData['media_start_date'] = strftime('%Y-%m-%d %H:%M:S',
			mktime(
				(int)$_POST['media_start_date_hour'],
				(int)$_POST['media_start_date_minute'],
				0,
				(int)$_POST['media_start_date_month'],
				(int)$_POST['media_start_date_day'],
				(int)$_POST['media_start_date_year'] - 2000
			)
		);

		$mediaData['media_end_date'] = strftime('%Y-%m-%d %H:%M:S',
			mktime(
				(int)$_POST['media_end_date_hour'],
				(int)$_POST['media_end_date_minute'],
				0,
				(int)$_POST['media_end_date_month'],
				(int)$_POST['media_end_date_day'],
				(int)$_POST['media_end_date_year'] - 2000
			)
		);

		return $mediaData;
	}
	
	/**
	 * protected function afterSaveMedia()
	 * Method is called after media is saved, override in extending media classe. The $mediaData array MUST be returned from this method.
	 * 
	 * @param array $mediaData
	 * @return array
	 */
	protected function afterSaveMedia($mediaData) {
		
		return $mediaData;
	}

	public function resetMediaByPost($postId) {
		
		$check = $this->db->query("UPDATE " . $this->mediaTableName . " SET media_status = '3' WHERE (media_status = '0' OR media_status = '99') AND media_post_id = '" . intval($postId) . "' ");
		 return !(bool)$check;
	}
	
	public function getMediaToResetByPost($postId) {
		
		$this->db->query("SELECT * FROM " . $this->mediaTableName . " WHERE (media_status = '0' OR media_status = '99') AND media_post_id = '" . intval($postId) . "' ");
		return $this->db->getAll();
	}
	
	public function markMediaToDelete($mediaId) {

		return $this->setMediaStatus($mediaId, 99);
	}

	public function markMediaAsSaved($mediaId) {
	
		return $this->setMediaStatus($mediaId, 3);
	}
	
	public function setMediaStatus($mediaId, $status) {
		
		$mediaId = intval($mediaId);
		$status = intval($status);
		
		if (!$mediaId) {
			return false;
		}
		
		$check = $this->db->query("UPDATE " . $this->mediaTableName . " SET media_status = '" . $status ."' WHERE id = '" . $mediaId . "' ");
		
		return !(bool)$check;
	}
	
	
	public function activateMedia($mediaId) {
		
		$mediaId = intval($mediaId);
		$check = $this->db->query("UPDATE " . $this->mediaTableName . " SET media_is_active = '1' WHERE id = '" . $mediaId . "' ");
		
		return !(bool)$check;		
	}
	
	public function deactivateMedia($mediaId) {
	
		$mediaId = intval($mediaId);
		$check = $this->db->query("UPDATE " . $this->mediaTableName . " SET media_is_active = '0' WHERE id = '" . $mediaId . "' ");
	
		return !(bool)$check;
	}
	
	/**
	 * public function setMediaDataFields()
	 * Set the specific database field names for the media type 
	 * 
	 * @param array $fieldNames
	 * @return boolean
	 */
	public function setMediaDataFields($fieldNames = array()) {
		
		if (!is_array($fieldNames)) {
			return false;
		}
		
		$this->mediaDataFields = $fieldNames;
	}

	public function addMediaDataFields($fields = array()) {
		
		if (!is_array($fields)) {
			return false;
		}
		
		$this->mediaDataFields = array_merge($this->mediaDataFields, $fields);
		return true;
		
	}
	
	/**
	 * protected function createHash()
	 * Creates a "hash" used for the filenames
	 * 
	 * @param void
	 * @return string
	 *
	 */
	protected function createHash() {
		
		return str_replace('.', '', (string)microtime(true));
	}
	

	public function getAvailableMediaTypes($all=false) {
		
		$return = array();
		$addQuery = '';
		
		if (!$all) {
			$addQuery = " WHERE media_type_status = 1 ";
		}
		
		$this->db->query("SELECT * FROM mlog_media_types " . $addQuery ." ORDER BY media_type_position");
		
		while ($res = $this->db->get()) {
			$res['mediaTypeName'] = $res['media_type_name_de'];
			$res['mediaType'] = strtolower($res['media_type_title']);
			$return[$res['id']] = $res;
		}
		
		return $return;
	}

	/**
	 * public function getMediaTypeByName()
	 * Returns 
	 * 
	 * @param unknown $name
	 */
	public function getMediaTypeByName($name) {
	
		$this->db->query("SELECT * FROM mlog_media_types WHERE media_type_title = '" . $this->db->dbQuote(trim($name)) ."' LIMIT 1");
	
		$res = $this->db->get();
		$res['mediaTypeName'] = $res['media_type_name_de'];
		$res['mediaType'] = strtolower($res['media_type_title']);

		return $res;
	}
	
	/**
	 * public function getMediaTypeById()
	 * Returns the name(!) of a media type id.
	 * 
	 * @param number $id Database id of the media type
	 * @return string Media type name e.g. 'image', 'document', ...
	 */
	public function getMediaTypeById($id) {
		
		$types = $this->getAvailableMediaTypes(true);
		
		return $types[intval($id)];
	}
	
	/**
	 * public function getAllMediaByPost()
	 * Returns all media of a post
	 * 
	 * @param number $postId Database id of the post
	 * @return array Multidimensional array with all post's media data
	 */
	public function getMediaByPost($postId) {

		$postId = intval($postId);
		$postMedia = array();
		$hasMedia = array();
		
		$query = "SELECT media.*, types.media_type_title, types.media_type_name_de 
				  FROM " . $this->mediaTableName . " media 
				  JOIN " . $this->mediaTypesTableName . " types 
				  ON (media.media_type = types.id) 
				  WHERE media.media_post_id ='" . $postId . "' 
				  ORDER BY media.media_position";
		
		$this->db->query($query);
		
		
// 		$inf = $this->db->getCurrentFieldInfo();
// 		$this->contentFieldTypes = $inf['type'];

		while ($res = $this->db->get()) {
			$res = $this->formatMediaData($res);
			$postMedia[] = $res;
		}
	
		return $postMedia;
	}

	public function getMedia($mediaId) {

		$mediaId = intval($mediaId);
		
		$query = "SELECT media.*, types.media_type_title, types.media_type_name_de
				  FROM " . $this->mediaTableName . " media 
				  JOIN " . $this->mediaTypesTableName . " types 
				  ON (media.media_type = types.id) 
				  WHERE media.id ='" . $mediaId . "'
				  LIMIT 1";
	
		$this->db->query($query);
		$res = $this->db->get();
		
		if (!empty($res)) {
			$res = $this->formatMediaData($res);
		}
		
		return $res;
	}
	
	/**
	 * public function uploadMedia()
	 * Handle upload of a media file.
	 *
	 * @param void
	 * @return void
	 */
	public function uploadMedia() {
		
		//$fieldName = 'media_' . $this->mediaType . '_newfile';
		$fieldName = $this->uploadFileFieldName . '_newfile';
		$file = $_FILES[$fieldName];
		
		// verify file, return error code "1" if not existing
		if (!$file['name']) {
			return 0;
		}

		// get file info
		$fileInfo = @ pathinfo($file['name']);
		$fileExtension = strtolower($fileInfo['extension']);
		
		// verify file type, return error code "2" if not supported
		if (!$this->checkFileType($fileExtension)) {
			return 2;
		}
				
		// verify file size
// 		if($file['size'] > 1048576*$this->settings['maxSize']){
// 			return 3;
// 		}
		
		// save uploaded file
		$newFilename = $this->createHash() . '.' . strtolower($fileExtension);
		$check = $this->fileHandler->handleUpload(array(
			'uploadFile' => array($fieldName => $file),
			'newFilename'=> array($fieldName => $newFilename),
			'targetDirectory'=> $this->uploadPath
		));
		
		if (!$check) {
			return 99;
		}
		
		$this->uploadedFileName = $newFilename; 
		
		return 0;
	}
	
	/**
	 * public function getUploadedFileName()
	 * Getter: returns the new filename of the uploaded file
	 * 
	 * @param void
	 * @return void
	 */
	public function getUploadedFileName() {
		return $this->uploadedFileName;
	}
	
	/**
	 * public function setMediaSettings()
	 * Setter. store media settings in an array.
	 * 
	 * @param array $settings The settings as an associative array.
	 * @return boolean
	 * 
	 */
	public function setMediaSettings($settings=array()) {
		
		if (!is_array($settings)) {
			return false;
		}
		
		$this->mediaSettings = $settings;

		// set upload settings for some media types
		$this->setUploadPath($this->mediaSettings['media_type_file_path']);
		
		// set possible file types
		$this->setFileTypes($this->mediaSettings['media_type_file_types']);
		
		return true;
	}

	public function getAllMediaSettings() {
		
		$query = "SELECT * FROM " . $this->mediaTypesTableName . " ORDER BY media_type_position ASC";
		$this->db->query($query);
		
		$allMediaSettings = array();
		
		while ($r = $this->db->get()) {

			$types = $r['media_type_file_types'];
		
			$types = str_replace(
				array(' ', "\n", "\r"),
				array('', ',', ''),
				$types
			);
			$r['media_type_file_types_raw'] = $r['media_type_file_types'];
			$r['media_type_file_types'] = explode(',', $types);
			
			$allMediaSettings[$r['media_type_title']] = $r;
		}

		return $allMediaSettings;
		
	}
	
	public function getMediaSettings($mediaType) {
		
		$allMediaSettings = $this->getAllMediaSettings();

		return (array)$allMediaSettings[$mediaType];
	}

	public function formatMediaData($data) {
		
		$data = array_merge($data, $this->formatDate($data['media_start_date'], 'media_start_date_'));
		$data = array_merge($data, $this->formatDate($data['media_end_date'], 'media_end_date_'));
		$data = array_merge($data, $this->formatDate($data['media_date'], 'media_date_'));
		
		$data['mediaTags'] = explode(',', $data['media_tags']);
		
		$data['media_title'] = $this->formatText($data['media_title']);
		$data['media_teaser'] = $this->formatText($data['media_teaser']);
		$data['media_text'] = $this->formatText($data['media_text']);
		
		$data['mediaId'] = $data['id'];
		$data['mediaPostId'] = $data['media_post_id'];
		$data['mediaType'] = strtolower($this->availableMediaTypes[$data['media_type']]['media_type_title']);
		$data['mediaTypeName'] = $this->availableMediaTypes[$data['media_type']]['mediaTypeName'];
		$data['mediaTypeId'] = $data['media_type'];
		
		$pathData = pathinfo($data['media_file']);
		$data['mediaFileType'] = $pathData['extension'];

		return $data;
	}
	
	/**
	 * public function formatDate()
	 * Helper: Format a date (source format YY-mm-dd H:i:s) in different ways
	 * 
	 * @param string $datetime Datetime in database format
	 * @param string $varNamePrefix Optional prefix for return array keys
	 * 
	 * @return array
	 */
	public function formatDate($datetime, $varNamePrefix='') {
	
		// Lokalisierung einstellen
		setlocale(LC_ALL, $this->locale);
	
		$r = array();
		preg_match('/^(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?$/', $datetime, $match);
		$r[$varNamePrefix.'date'] = $match[3].'.'.$match[2].'.'.$match[1];
		$r[$varNamePrefix.'time'] = $match[5].'.'.$match[6];
		$r[$varNamePrefix.'timestamp'] = mktime($match[5],$match[6],$match[7],$match[2],$match[3],$match[1]);
		$r[$varNamePrefix.'weekday'] = date('l', $r[$varNamePrefix.'timestamp']);
		$r[$varNamePrefix.'weekday_short'] = substr($r[$varNamePrefix.'weekday'], 0, 3);
		$r[$varNamePrefix.'day'] = $match[3];
		$r[$varNamePrefix.'month'] = $match[2];
		$r[$varNamePrefix.'hour'] = $match[5];
		$r[$varNamePrefix.'minute'] = $match[6];
		$r[$varNamePrefix.'second'] = $match[7];
	
		$r[$varNamePrefix.'month_name'] = date('F',$r[$varNamePrefix.'timestamp']);
		//$r[$varNamePrefix.'monthName'] = $this->monthNames[intval($match[2])];
	
		$r[$varNamePrefix.'month_name_short'] = date('M', $r[$varNamePrefix.'timestamp']); //intval($match[2]));
		//$r[$varNamePrefix.'monthNameShort'] = $this->monthShortNames[intval($match[2])];
	
		$r[$varNamePrefix.'year'] = $match[1];
		if ($r[$varNamePrefix.'date'] == '..' || $r[$varNamePrefix.'date'] =='00.00.0000') {
			$r[$varNamePrefix.'date'] = '';
		}
		if ($r[$varNamePrefix.'time'] == '.' || $r[$varNamePrefix.'time'] =='00.00') {
			$r[$varNamePrefix.'time'] = '';
		}
		return $r;
	}
	
	public function formatText($text) {
		
		return stripslashes($text);
	}

	/**
	 * public function getAllDiscardedMedia()
	 *
	 */
	public function getDiscardedMedia($postId) {
		
		$postId = intval($postId);
		
		$this->db->query("SELECT * FROM " . $this->mediaTableName . " WHERE media_status = '0' AND media_post_id = '" . intval($postId) . "'");
		$r = $this->db->getAll();
		
		return $r;

	}

	public function getDeletedMedia($postId) {
	
		$this->db->query("SELECT * FROM " . $this->mediaTableName . " WHERE media_status = '99' AND media_post_id = '" . intval($postId) . "'");
		$r = $this->db->getAll();
	
		return $r;
	
	}
	
	
	public function deleteMedia($mediaId) {
		
		$mediaId = intval($mediaId);
		$check = $this->db->query("DELETE FROM " . $this->mediaTableName . " WHERE id = '" . $mediaId . "'");
		
		return !(bool)$check;
	}
	
	public function deleteMediaByPost($postId) {
		
		$postId = intval($postId);
		$mediaData = $this->getMediaByPost($postId);
		
		foreach($mediaData as $media) {
			$mediaHandlerName = __NAMESPACE__ . '\Media' . ucfirst($media['mediaType']) . 'Handler';
			$mediaHandler = new $mediaHandlerName();
			$mediaHandler->deleteMedia($media['id']);
			
			unset($mediaHandler);
		}
	}
	
	public function setMediaPositions($mediaPositions=array()) {
		
		if (!is_array($mediaPositions)) {
			return false;
		}
		
		$pos = 1;
		$check = 0;
		
		foreach ($mediaPositions as $mediaId) {
			$check += $this->db->query("UPDATE " . $this->mediaTableName . " SET media_position = '" . $pos++ . "' WHERE id = '" . intval($mediaId) . "'");
		}
		
		return !(bool)$check;
		
	}
	
	/**
	 * Download a media by its id
	 * 
	 * @param number $mediaId
	 */
	public function downloadMediaById($mediaId) {
		
		$this->handleMediaDownload($this->getMedia($mediaId));
	}
	
	/**
	 * Handles a media download.
	 * 
	 * @param array $media	the media data array
	 * @return boolean		Returns false in case of missing data.
	 */
	public function handleMediaDownload($media) {

		if (!is_array($media) || empty($media)) {
			return false;
		}
		
		$mediaSettings = $this->getMediaSettings($media['mediaType']);

		$this->fileHandler->handleDownload(array(
			'downloadFile' => PATHTOWEBROOT . $mediaSettings['media_type_file_path'] . $media['media_internal_filename'],
			'downloadFileAlias' => $media['media_file']
		));
		exit();
	}
	
	/**
	 * Gets a media dataset by its internal file name
	 * 
	 * @param string $internalFileName
	 * @return array
	 */
	public function getMediaByInternalFileName($internalFileName) {
		
		$this->db->query("SELECT * FROM " . $this->mediaTableName . " WHERE media_internal_filename = '" . $this->db->dbQuote($internalFileName) . "'");
		return (array)$this->db->get();
	}
}
?>
