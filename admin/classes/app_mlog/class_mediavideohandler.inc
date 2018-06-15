<?php
/**
 * class_mediavideohandler.inc
 * Handler for video files.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */

namespace Contentomat\MLog;

class MediaAudioHandler extends MediaBaseHandler {

	protected $uploadPath;
	protected $mediaType;
	protected $uploadFileFieldName = 'media_file';

	protected function init() {
		
		$this->mediaType = 'video';
		
		$this->addMediaDataFields(array(
			$this->uploadFileFieldName => '',
			'media_type' => $this->getMediaTypeByName($this->mediaType)['id']
		));
		
	}
}
?>