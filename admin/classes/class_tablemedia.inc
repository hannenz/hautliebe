<?php 
/**
 * class_tablemedia.inc
 * Include for table overview and edit entry view to upload an manage media files like images or document.
 * 
 * @version 2017-10-12
 * @author J.Hahn <info@content-o-mat.de>
 * 
 */
namespace Contentomat;

Class TableMedia {
	
	protected $db;
	protected $entryID;
	protected $mediaTable;
	protected $mediaTableID;
	protected $mediaID;
	protected $mediaTypes;
	protected $uploadPathPrefix = '';
	protected $thumbnailWidth = 250;
	protected $thumbnailHeight = 168;
	protected $mediaFieldNames = array();
	protected $mediaUploadFieldNames = array();
	protected $mediaUploadFieldOptions = array();
	
	public function __construct() {
		
		$this->mediaTable = 'cmt_tables_media';
		$this->mediaTableID = $this->setMediaTable($this->mediaTable);
		$this->mediaTypes = $this->getMediaTypes();
		$this->db = new DBCex();
	
		$this->readMediaFieldData();
	}

	/**
	 * public function getMediaByType()
	 * Returns all media of selected type of a post.
	 *
	 * @param array
	 * @return array Media data as associative array.
	 */
	public function getMedia($params = array()) {
	
		$defaultParams = array(
			'entryID' => 0,
			'tableID' => 0,
			'tableName' => '',
			'mediaStatus' => '',
			'mediaType' => '',
			'mediaID' => '',
			'orderBy' => 'media_position ASC',
			'where' => '',
			'limit' => ''
		);
		$params = array_merge($defaultParams, $params);
		
		$entryID = intval($params['entryID']);
		$tableID = intval($params['tableID']);
		$tableName = $this->db->dbQuote($params['tableName']);
		$mediaID = intval($params['mediaID']);
		$whereClause = array();

		// filter by table ID
		if ($tableName) {
			$tableID = $this->getTableIDFromName($tableName);
		}
		if ($tableID) {
			$whereClause[] = "media_table_id = '" . $tableID . "'";
		}
		
		// filter by entry ID
		if ($entryID && $tableID) {
			$whereClause[] = "media_entry_id = '" . $entryID . "'";
		}

		// filter by media ID
		if ($params['mediaID']) {
			$whereClause[] = "id = '" . $mediaID . "'";
		}
		
		// where clause
		if ($params['where']) {
			$whereClause[] = $this->db->dbQuote($params['where']);
		}
		
		// order
		$orderBy = $this->db->dbQuote($params['orderBy']);

		// limit
		$limit = '';
		if ($params['limit']) {
			$limit = " LIMIT " . $this->db->dbQuote($params['limit']);
		}
		
		// media status
		switch($params['mediaStatus']) {

			case 'all':
				break;

			case 'inactive':
				$whereClause[] = "media_status = '0'";
				break;
				
			case 'deleted':
				$whereClause[] = "media_status = '99'";
				break;
	
			default:
				$whereClause[] = "media_status = '1'";
				break;
		}
		
		// filter by media type
		if (in_array($params['mediaType'], $this->mediaTypes)) {
			$whereClause[] = "media_type = '" . trim($this->db->dbQuote($params['mediaType'])) . "'";
		}
	
		$this->db->query("SELECT * FROM " . $this->db->dbQuote($this->mediaTable) . " WHERE " . implode(' AND ', $whereClause) . " ORDER BY " . $orderBy . $limit);
		return $this->db->getAll(false);
	}

	/**
	 * public function duplicateMedia()
	 * Duplicates an entry in cmt_tables_media and its corresponding files.
	 * 
	 * @param number $mediaID Source media dataset's ID
	 * @param number $entryID New entry's ID to which the duplicated media belongs to.
	 * @return number New media dataset's ID or 0 in case of errors.
	 */
	public function duplicateMedia($params) {
		
		$mediaID = intval($params['mediaID']);
		$entryID = intval($params['entryID']);

		$mediaData = $this->getMediaByID($mediaID);
		if (empty($mediaData)) {
			return 0;
		}
		
		unset($mediaData['id']);
		$mediaData['media_status'] = 0;
		
		$mediaID = $this->saveMedia(array(
			'entryID' => $this->db->dbQuote($entryID),
			'mediaID' => '',
			'tableID' => $mediaData['media_table_id'],
			'mediaType' => $mediaData['media_type'],
			'mediaData' => $mediaData
		));
		
		if (!$mediaID) {
			return 0;
		}
		
		$fileHandler = new FileHandler();
		
		foreach($this->mediaUploadFieldNames as $uploadField) {
			
			$options = $this->getUploadFieldOptions($uploadField);
			$uploadPath = $options['dir'];
			
			$filePath = $this->uploadPathPrefix . $uploadPath . $mediaData[$uploadField . '_internal'];
			$thumbnailPath = $this->uploadPathPrefix . $uploadPath . 'thumbnails/' . $mediaData[$uploadField . '_internal'];
			
			// 1. copy file
			if (is_file($filePath)) {
				
				$pathInfo = pathinfo($filePath);
				$extension = $pathInfo['extension'];
				
				$newFileName = $this->createInternalFileName() . '.' . $extension;
				$newFilePath = $this->uploadPathPrefix . $uploadPath . $newFileName;
				
				$check = $fileHandler->copyFile(array(
					'sourceFile' => $filePath,
					'targetFile' => $newFilePath
				));
				
				if (!$check) {
					return 0;
				}
				
				// finally save the new filename in duplicated dataset
				$this->saveMedia(array(
					'entryID' => $this->db->dbQuote($entryID),
					'mediaID' => $mediaID,
					'tableID' => $mediaData['media_table_id'],
					'mediaType' => $mediaData['media_type'],
					'mediaData' => array(
						$uploadField . '_internal' => $newFileName
					)
				));
			}
			
			// 2. copy thumbnail
			if (is_file($newFilePath)) {
			
				$fileHandler->copyFile(array(
					'sourceFile' => $newFilePath,
					'targetFile' => $this->uploadPathPrefix . $uploadPath . 'thumbnails/' . $newFileName
				));
			}
		}
		
		return $mediaID;
	}
	
	/**
	 * public function getMediaByID()
	 * Returns a media's dataset by its ID.
	 * 
	 * @param number $mediaID Media's ID
	 * @return array The dataset
	 */
	public function getMediaByID($mediaID=0) {
		
		$mediaData = $this->getMedia(array(
			'mediaID' => intval($mediaID),
			'mediaStatus' => 'all'
		));
		$mediaData = array_shift($mediaData);
		if (is_array($mediaData)) {
			return $mediaData;
		} else {
			return array();
		}
	}

	/**
	 * public function activateNewEntriesMediaAfterSave()
	 * Activates the media items of a newly added entry.
	 * 
	 * @param array $params Expects 3 parameters in an associative array: 'entryID' => ID of the new entry, 'tableID' => entry's table id, 'newMediaIndicator' => intermediate value of field media_entry_id (e.g. negative value of CMT_USERID)
	 * @return boolean
	 */
	public function activateNewEntriesMediaAfterSave($params) {
	
		$entryID = intval($params['entryID']);
		$tableID = intval($params['tableID']);
		$indicator = $params['newMediaIndicator'];
	
		if (!$entryID || !$tableID) {
			return false;
		}
		
		$check = $this->db->query("
			UPDATE " . $this->mediaTable . "
			SET media_entry_id = '" . $entryID ."' 
			WHERE media_table_id = '" . $tableID . "' 
			AND media_entry_id = '" . $this->db->dbQuote($indicator) ."'
		");
		
		if ($check) {
			return false;
		}
	
		// change media's status from 0 to 1 (active)
		$check = $this->activateEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
		if (!$check) {
			return false;
		}
	
		// remove as "deleted" marked media and their files
		$check = $this->removeEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
		if (!$check) {
			return false;
		}
		
		return true;
	}

	/**
	 * public function refreshEntriesMediaAfterSave()
	 * Refreshes / activates the media items of an entry after the entry was saved.
	 * 
	 * @param array $params Expects 2 parameters in an associative array: 'entryID' => ID of the new entry, 'tableID' => entry's table id
	 * @return boolean
	 */
	public function refreshEntriesMediaAfterSave($params) {
		
		$entryID = intval($params['entryID']);
		$tableID = intval($params['tableID']);
	
		if (!$entryID || !$tableID) {
			return false;
		}
		
		// change media's status from 0 to 1 (active)
		$this->activateEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
		
		// remove as "deleted" marked media and their files
		$this->removeEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));

	}

	/**
	 * public function refreshEntriesMediaAfterAbort()
	 * Deletes the newly added media items of an entry after the entry editin was aborted.
	 *
	 * @param array $params Expects 2 parameters in an associative array: 'entryID' => ID of the new entry, 'tableID' => entry's table id
	 * @return boolean
	 */
	public function refreshEntriesMediaAfterAbort($params) {
	
		$entryID = intval($params['entryID']);
		$tableID = intval($params['tableID']);

		if (!$entryID || !$tableID) {
			return false;
		}
	
		// restore deleted media's status
		$this->restoreEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
		
		// delete new uploaded media
		return $this->condemnEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
	
	}
	
	/**
	 * protected function activateEntriesMedia()
	 * Changes the status of a media dataset from 'inactive' to 'active' (field 'media_status')
	 * 
	 * @param number $tableID Tables database ID
	 * @param number $entryID Entries database ID
	 * @return boolean
	 */
	protected function activateEntriesMedia($params) {
	
		$entryID = intval($params['entryID']);
		$tableID = intval($params['tableID']);
		
		return !(boolean) $this->db->query(
			"UPDATE " . $this->mediaTable . " 
			 SET media_status = '1' 
			 WHERE media_status = '0' 
			 AND media_entry_id = '" . $entryID . "' 
			 AND media_table_id = '" . $tableID . "'"
		);
	}
	
	/**
	 * public function saveMediaPositions()
	 * Saves the positions of media datasets.
	 * 
	 * @param array $mediaIDs Array containing media ids defining the order of media datasets.
	 * @return boolean
	 */
	public function saveMediaPositions($mediaIDs=array()) {
		
		if (!is_array($mediaIDs)) {
			return false;
		}
		
		$index = 1;
		foreach ($mediaIDs as $id) {
			$check = $this->db->query(
				"UPDATE " . $this->mediaTable . " 
				 SET media_position = '" . $index++ . "' 
				 WHERE id = '" . intval($id) . "' 
				 AND media_status != '99'"
			);
			
			if ($check) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * public function getDeletedMedia()
	 * Returns an array with all media entries marked as "deleted".
	 * 
	 * @param array Expects two mandatory parameters in an associative array: 1. entryID Post's / entry's database ID. 2. tableID: database table's ID
	 * @return array Associative array with all datasets marked as "deleted"
	 */
	public function getDeletedMedia($params) {
		return $this->getMedia($entryID, array(
			'mediaStatus' => 'deleted',
			'entryID' => $params['entryID'],
			'tableID' => $params['tableID']
		));
	}
	
	/**
	 * public function getMediaByType()
	 * Returns all media of selected type of a post.
	 *  
	 * @param array $parram		All params as associative array (param name => value): tableID (integer), entryID (integer), mediaType (string)
	 * @return array			Media data as associative array.
	 */
	public function getMediaByType($params) {

		$defaultParams = array(
			'entryID' => 0,
			'tableID' => 0,
			'mediaType' => 'image'
		);
		$params = array_merge($defaultParams, $params);
		
		if (!in_array($params['mediaType'], $this->mediaTypes)) {
			return array();
		}
		
		return $this->getMedia($params);
	}
	
	/**
	 * public function saveMedia()
	 * Saves a media dataset. Usually the media status is 'inactive' after saving the media and has to be updated to 'active' when saving the corresponding entry.
	 * 
	 * @param array $params Parameters as key=>value pairs in an associate array: 
	 * 		'entryID' => entry's id
	 * 		'tableID' => table's id
	 * 		'mediaType' => number of media type
	 * 		'mediaData' => dataset of media to save
	 * @return number Returns the saved media's ID or 0 in case of an error
	 */
	public function saveMedia($params) {
		$defaultParams = array(
			'entryID' => 0,
			'tableID' => 0,
			'mediaType' => '',
			'mediaStatus' => 0
		);
		$params = array_merge($defaultParams, $params);
		$entryID = intval($params['entryID']);
		$mediaID = intval($params['mediaID']);
		$tableID = intval($params['tableID']);
		$mediaType = $params['mediaType'];
		
		$mediaData = $params['mediaData'];
		
		// get thumbnail size if provided. Otherwise use default values.
		if (intval($params['thumbnailWidth'])) {
			$this->thumbnailWidth = intval($params['thumbnailWidth']); 
		}
		
		if (intval($params['thumbnailHeight'])) {
			$this->thumbnailHeight = intval($params['thumbnailHeight']);
		}
		
		// get fields to save and upload fields
		$fileHandler = new FileHandler();
		$image = new Image();
		
		$fieldNames = $this->mediaFieldNames;
		$uploadFieldNames = $this->mediaUploadFieldNames;
		
		$saveFields = array_intersect_key($mediaData, array_flip($fieldNames));
		
		// handle uploads
		foreach($uploadFieldNames as $name) {
			
			$newFile = $_FILES[$name . '_newfile'];

			if (!empty($newFile['tmp_name'])) {
				
				$fieldOptions = $this->mediaUploadFieldOptions[$name];
				
				$pathInfo = pathinfo($newFile['name']);
				$extension = $pathInfo['extension'];
				
				$newName = $this->createInternalFileName();
				$newFileName = $newName . '.' . strtolower($extension);
				
				$check = $fileHandler->handleUpload(array(
					'targetDirectory' => $this->uploadPathPrefix . $fieldOptions['dir'],
					'uploadFile' => array($name . '_newfile' => $newFile),	// sucks!
					'newFilename' => array($name . '_newfile' => $newFileName) // sucks!
				));
				
				if (!$check) {
					return 0;
				} else {
					$saveFields[$name . '_internal'] = $newFileName;
				}
				
				// create thumbnail
				if ($mediaType == 'image' && is_dir($this->uploadPathPrefix . $fieldOptions['dir'] . 'thumbnails/')) {
					
					$check = $image->createThumbnail(array(
						'sourceImage' => $this->uploadPathPrefix . $fieldOptions['dir'] . $newFileName,
						'sourceImageType' => $extension,
						'destinationImage' => $this->uploadPathPrefix . $fieldOptions['dir'] . 'thumbnails/' . $newName . '.jpg',
						'destinationImageType' => 'jpg',
						'width' => $this->thumbnailWidth, 
						'max-height' => $this->thumbnailHeight
					));
				}
			}
		}
		
		$saveFields['media_entry_id'] = $entryID;
		$saveFields['media_table_id'] = $tableID;
		$saveFields['media_status'] = intval($params['mediaStatus']);
		$saveFields['media_type'] = $this->db->dbQuote($mediaType);

		if ($mediaID) {
			$query = "UPDATE " . $this->mediaTable . " SET " . $this->db->makeSetQuery($saveFields) . " WHERE id = '" . $mediaID . "'";
		} else {
			$query = "INSERT INTO " . $this->mediaTable . " SET " . $this->db->makeSetQuery($saveFields);
		}
		
		$check = $this->db->query($query);
		
		if ($check) {
			return 0;
		} else {
			if ($mediaID) {
				return $mediaID;
			} else {
				return $this->db->getLastInsertedID();
			}
		}
	}
	
	/**
	 * public function deleteMedia()
	 * Deletes an as "deleted" marked media entry from media database table.
	 *
	 * @param number $mediaID ID of the media.
	 * @return number Result of DBCex::query (0 = success, >0 = MySQL error number)
	 */
	public function deleteMedia($mediaID=0) {
		$mediaID = intval($mediaID);
		return !(boolean) $this->db->query("UPDATE " . $this->mediaTable . " SET media_status = '99' WHERE id = '" . $mediaID ."'");		
	}

	/**
	 * public function restoreMedia()
	 * Restores an as "deleted" marked elements in media database table.
	 *
	 * @param number $mediaID ID of the media.
	 * @return number Result of DBCex::query (0 = success, >0 = MySQL error number)
	 */
	public function restoreMedia($mediaID=0) {
		$mediaID = intval($mediaID);
		return $this->db->query("UPDATE " . $this->mediaTable . " SET media_status = '1' WHERE media_status = '0' AND id = '" . $mediaID . "'");
	}

	/**
	 * public function deleteEntriesMedia()
	 * Deletes all media entries of a post (set their status to "0")
	 * 
	 * @param number $entryID ID of the post the media entries belong to.
	 * @return number Result of DBCex::query (0 = success, >0 = MySQL error number)
	 */
	public function deleteEntriesMedia($params) {
		
		$tableID = intval($params['tableID']);
		$entryID = intval($params['entryID']);

		return !(boolean) $this->db->query("
			UPDATE " . $this->mediaTable . " 
			SET media_status = '99' 
			WHERE media_entry_id = '" . $entryID ."'
			AND media_table_id = '" . $tableID ."'
		");
	}

	/**
	 * public function eraseEntriesMedia()
	 * Deletes all as "deleted" marked elements of a post from media database table.
	 * 
	 * @param number $entryID ID of the post.
	 * @return number Result of DBCex::query (0 = success, >0 = MySQL error number)
	 */
	public function removeEntriesMedia($params) {
		
		$tableID = intval($params['tableID']);
		$entryID = intval($params['entryID']);
		
		$mediaData = $this->getMedia(array(
			'tableID' => $tableID,
			'entryID' => $entryID,
			'mediaStatus' => 'deleted'
		));
		
		$check = $this->db->query("
			DELETE FROM " . $this->mediaTable . " 
			WHERE media_status = '99' 
			AND media_entry_id = '" . $entryID . "' 
			AND media_table_id = '" . $tableID . "'
		");

		if ($check) {
			return false;
		}
		
		// delete files
		$fileHandler = new FileHandler();
		$fieldNames = $this->mediaFieldNames;
		$uploadFieldNames = $this->mediaUploadFieldNames;
		
		foreach($mediaData as $media) {
			
			foreach($uploadFieldNames as $name) {
				
				$internalName = $name . '_internal';
				
				if ($media[$internalName]) {
					
					$options = $this->getUploadFieldOptions($name);
					$path = $options['dir'];
					
					$check = $fileHandler->deleteFile(array('file' => $this->uploadPathPrefix . $path . $media[$internalName]));
					
					if (is_file($this->uploadPathPrefix . $path . 'thumbnails/' . $media[$internalName])) {
						$check = $fileHandler->deleteFile(array('file' => $this->uploadPathPrefix . $path . 'thumbnails/' . $media[$internalName]));
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * public function restoreEntriesMedia()
	 * Restores all as "deleted" marked elements of a post in media database table.
	 *
	 * @param number $entryID ID of the post.
	 * @return number Result of DBCex::query (0 = success, >0 = MySQL error number)
	 */
	public function restoreEntriesMedia($params) {
		
		$tableID = intval($params['tableID']);
		$entryID = intval($params['entryID']);
		
		return !(boolean) $this->db->query("
			UPDATE " . $this->mediaTable . " 
			SET media_status = '1' 
			WHERE media_status = '99' 
			AND media_entry_id = '" . $entryID . "' 
			AND media_table_id = '" . $tableID . "'"
		);
	}
	
	/**
	 * public function condemnEntriesMedia()
	 * Deletes all new media and their uploaded files when media was add but the entry wasn't saved (editing was aborted)
	 * 
	 * @param number $tableID Table's database ID
	 * @param number $entryID Entry's database ID
	 * @return boolean
	 */
	public function condemnEntriesMedia($params) {
		
		$tableID = intval($params['tableID']);
		$entryID = intval($params['entryID']);
	
		$this->db->query("
			UPDATE " . $this->mediaTable . "
			SET media_status = '99'
			WHERE media_status = '0'
			AND media_entry_id = '" . $entryID . "'
			AND media_table_id = '" . $tableID . "'"
		);

		return !(boolean) $this->removeEntriesMedia(array('tableID' => $tableID, 'entryID' => $entryID));
	}
	
	/**
	 * public function setMediaTableID()
	 * Sets internal var TableMedia::mediaTableID
	 * 
	 * @param number $id
	 * @return void
	 */
	public function setMediaTableID($id) {
		$this->mediaTableID = intval($id);
	}

	/**
	 * public function setMediaTableID()
	 * Sets internal var TableMedia::mediaTable
	 * 
	 * @param unknown $id
	 * @return void
	 */
	public function setMediaTable($tableName) {
		
		$tableName = trim($tableName);
		
		$applicationHandler = new ApplicationHandler();
		$tablePorperties = $applicationHandler->getApplicationByTablename($tableName);
		
		if ($tablePorperties['id']) {
			$this->mediaTable = $tableName;
			$this->setMediaTableID($tablePorperties['id']);
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * public function getMediaTable()
	 * Returns the name of the internal media table.
	 * 
	 * @param void
	 * @return string
	 */
	public function getMediaTable() {
		return $this->mediaTable;
	}
	
	/**
	 * public function getMediaTypes()
	 * Returns all available media types in an array.
	 * 
	 * @param void
	 * @return array Available media types
	 */
	public function getMediaTypes() {
		
		if (!empty($this->mediaTypes)) {
			return $this->mediaTypes;
		}
		
		$form = new Form();
		$fieldHandler = new FieldHandler();
		
		$fieldData = $fieldHandler->getField(array(
			'tableName' => $this->mediaTable,
			'fieldName' => 'media_type'
		));
		
		//$this->mediaTypes = explode("\n", $fieldData['cmt_option_select_values']);
		$this->mediaTypes = explode("\n", str_replace("\r", '', $fieldData['cmt_option_select_values']));
		return $this->mediaTypes;
	}
	
	/**
	 * public function setUploadPathPrefix()
	 * Sets a path prefix for media paths.
	 * 
	 * @param string $prefix
	 * @return void
	 */
	public function setUploadPathPrefix($prefix='') {
		$this->uploadPathPrefix = trim($prefix);
	}
	
	/**
	 * protected function readMediaFieldData()
	 * Gets and stores Content-o-mat's field informations for all fields in the media table in an internal associative array (fieldName => array())
	 * 
	 * @param void
	 * @return void
	 */
	protected function readMediaFieldData() {
		
		$fieldHandler = new FieldHandler();
		
		$fieldData = $fieldHandler->getAllFields(array('tableName' => $this->getMediaTable()));

		$fieldNames = array();
		$uploadFields = array();
		
		foreach ($fieldData as $field) {
		
			$this->mediaFieldNames[] = $field['cmt_fieldname'];

			if ($field['cmt_fieldtype'] == 'upload') {
				$this->mediaUploadFieldNames[] = $field['cmt_fieldname'];
				$this->mediaUploadFieldOptions[$field['cmt_fieldname']] = unserialize($field['cmt_options']);
			}
		}
	}
	
	/**
	 * public function getImagePath()
	 * Returns the image file path defined for the field 'media_image_file'
	 * 
	 * @param void
	 * @return string
	 */
	public function getImagePath() {
		return $this->uploadPathPrefix . $this->mediaUploadFieldOptions['media_image_file']['dir'];
	}

	/**
	 * public function getDownloadPath()
	 * Returns the downloa file path defined for the field 'media_download_file'
	 *
	 * @param void
	 * @return string
	 */
	public function getDocumentPath() {
		return $this->uploadPathPrefix . $this->mediaUploadFieldOptions['media_document_file']['dir'];
	}
	
	/**
	 * public function getUploadFieldOptions()
	 * Returns all options of an upload field as associative array (optionName => value)
	 * 
	 * @param string $fieldName
	 * @return array
	 */
	public function getUploadFieldOptions($fieldName='') {
		return $this->mediaUploadFieldOptions[$fieldName];
	}
	
	/**
	 * public function createInternalFileName()
	 * Creates an filename (a timestamp). Upload files are renamed for internal storage.
	 * 
	 * @param void
	 * @return string
	 */
	public function createInternalFileName() {
		$newNameParts = explode(' ', microtime());
		return  $newNameParts[1] . substr($newNameParts[0], 2, 5);
	}
	
	public function getTableIDFromName($tableName) {
		$applicationHandler = new ApplicationHandler();
		$tableData = $applicationHandler->getApplicationByTablename($tableName);
		return intval($tableData['id']);
	}
	
	/**
	 * public function setUploadPath()
	 * Sets the upload directory for a media type.
	 * TODO: This is not very elegant but ensures downwards compatibility. In older version the upload directories are defined 
	 * in the settings of the upload fields from table 'cmt_tables_media'.
	 * 
	 * @param unknown $mediaType
	 * @param unknown $directory
	 * @return boolean
	 */
	public function setUploadPath($mediaType, $directory) {
		$mediaType = strtolower(trim($mediaType));
		
		if (!$mediaType) {
			return false;
		}
		
		$this->mediaUploadFieldOptions['media_' . $mediaType . '_file']['dir'] = $directory;
		return true;
		
	}
}
?>