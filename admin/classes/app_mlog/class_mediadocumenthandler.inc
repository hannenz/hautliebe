<?php
/**
 * class_mediadocumenthandler.inc
 * Handler for document files.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 * @package MLog
 *
 */

namespace Contentomat\MLog;

class MediaDocumentHandler extends MediaBaseHandler {

	protected $uploadPath;
	protected $mediaType;
	protected $uploadFileFieldName = 'media_file';

	protected function init() {
		
		$this->mediaType = 'document';
		
		$this->addMediaDataFields(array(
			$this->uploadFileFieldName => '',
			'media_type' => $this->getMediaTypeByName($this->mediaType)['id']
		));
		
	}
}
?>