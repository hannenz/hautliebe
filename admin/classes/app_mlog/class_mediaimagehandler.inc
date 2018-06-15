<?php
/**
 * class_mediabase.inc
 * Base class for media files.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */

namespace Contentomat\MLog;

use Contentomat\Image;

class MediaImageHandler extends MediaBaseHandler {

	protected $uploadPath;
	protected $mediaType;
	protected $image;
	protected $defaultThumbnailWidth = 160;
//	protected $uploadFileFieldName = 'media_image';

	protected function init() {
		
		$this->mediaType = 'image';
		
		$this->addMediaDataFields(array(
			$this->uploadFileFieldName => '',
			'media_type' => $this->getMediaTypeByName($this->mediaType)['id']
		));
		
		$this->image = new Image();
	}

	protected function afterSaveMedia($mediaData) {

		$thumbnailSize = explode("x", trim($this->mediaSettings['media_type_setting_1']));
		$thumbnailWidth = intval($thumbnailSize[0]) ? intval($thumbnailSize[0]) : $this->defaultThumbnailWidth;
		$thumbnailHeight = $thumbnailSize[1];
		$fileInfo = pathinfo($mediaData[$this->uploadFileFieldName]);

		$check = $this->image->createThumbnail(array(
			'sourceImage' => $this->uploadPath . '/' . $mediaData['media_internal_filename'],
			'destinationImage' => $this->uploadPath . '/thumbnails/' . $mediaData['media_internal_filename'],
			'width' => $thumbnailWidth,
			'height' => $thumbnailHeight,
			'destinationImageType' => $fileInfo['extension']
		));

		return $mediaData;
	}
	
	public function deleteMedia($mediaId) {
	
		$mediaId = intval($mediaId);
		$r = $this->getMedia($mediaId);

		$check = parent::deleteMedia($mediaId);

		// delete file too
		$check = $this->fileHandler->deleteFile(array(
			'file' => array(
				$this->uploadPath . $r['media_internal_filename'],
				$this->uploadPath . 'thumbnails/' . $r['media_internal_filename'],
			)
		));

	}

}
?>