<?php
/**
 * class_mediadatehandler.inc
 * Handler for date entries.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */

namespace Contentomat\MLog;

class MediaDateHandler extends MediaBaseHandler {

	protected $uploadPath;
	protected $mediaType;
	protected $uploadFileFieldName = '';

	protected function init() {
		
		$this->mediaType = 'date';
		
		$this->addMediaDataFields(array(
			'media_type' => $this->getMediaTypeByName($this->mediaType)['id'],
			'media_start_date' => '',
			'media_end_date' => '',
		));
		
	}
	
	public function uploadMedia() {
		return true;
	}
	
}
?>