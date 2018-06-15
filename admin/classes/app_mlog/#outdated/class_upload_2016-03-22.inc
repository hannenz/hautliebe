<?php
/**
 * class_upload.inc
 * Handles a media file upload in MLogs backend.
 * 
 * @author A. Al Kaissi <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */
namespace Contentomat\MLog;
use Contentomat as cmt;

class Upload {

	protected $settings;
	protected $errorMessage;

	public function __construct($params) {
		$this->settings = $params;
		
	}

	protected function createThumbnail($file, $fileInfo) {
// var_dump($this->settings);
// exit();
		$img = new cmt\Image();
		$thumbnailSize = explode("x", $this->settings['thumbnailSize']);
		$thumbnailWidth = $thumbnailSize[0];
		$thumbnailHeight = $thumbnailSize[1];
		
		// Gibt's ne Datei?
		if ($file['name']) {
			$img->createThumbnail(array('sourceImage' => $file['tmp_name'],
				'sourceImageType' => $fileInfo['extension'],
				'destinationImage' => PATHTOWEBROOT . $this->settings['path'] . 'thumbnails/' . $file['name'],
				'width' => $thumbnailWidth,
				'height' => $thumbnailHeight,
				'destinationImageType'=>$fileInfo['extension']
			));
		}
	}

	protected function moveFile($fieldName,$file) {
		$fh = new cmt\FileHandler();
		$fh->handleUpload(
				array(
			'uploadFile'=>array($fieldName=>$file),
					'newFilename'=>$file['name'],
			'targetDirectory'=>PATHTOWEBROOT . $this->settings['path']
		));
	}

	public function work() {

		foreach ($_FILES as $fieldName => $file) {
			
			if(!$file['name']){
				continue;
			}
			// get file info
			$fileInfo = @ pathinfo($file['name']);
			$fileExtension = strtolower($fileInfo['extension']);

			// verify file type
			if (!in_array($fileExtension, $this->settings['types'])) {
				$this->errorMessage = 'This file type not supported';
				return false;
			}
			
			// verify file size
			if($file['size'] > 1048576*$this->settings['maxSize']){
				$this->errorMessage = 'Allowd maximum file size is '.$this->settings['maxSize'].' MB!';
				return false;
			}

			// create images thumbnail
			if ($this->settings['thumbnailSize'] && $this->settings['path']) {
				$this->createThumbnail($file, $fileInfo);
			}
			
			// save uploaded file
			$this->moveFile($fieldName,$file);
			
		}
		return true;
	}
	
	public function getLastErrorMessage(){
		return $this->errorMessage;
	}

}
?>