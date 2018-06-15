<?php
/**
 * class_medialinkhandler.inc
 * Handler for document files.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */

namespace Contentomat\MLog;

class MediaLinkHandler extends MediaBaseHandler {

	protected $uploadPath;
	protected $mediaType;
	protected $uploadFileFieldName = '';

	protected function init() {
		
		$this->mediaType = 'link';
		
		$this->addMediaDataFields(array(
			'media_type' => $this->getMediaTypeByName($this->mediaType)['id'],
			'media_url' => '',
			'media_url_alias' => ''
		));
		
	}
	
	public function uploadMedia() {
		return true;
	}
}
?>