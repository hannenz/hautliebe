<?php 
/**
 * app_mlog_codemanager_tag_after_delete.inc
 * Führt Datenverarbeitungen nach dem Speichern eines Artikels/ Posts aus.
 * 
 * @version 2012-08-13
 */

namespace Contentomat\Mlog;
use Contentomat as cmt;
$autoloader = new cmt\PsrAutoloader();
$autoloader->addNamespace('\Contentomat\Mlog', PATHTOADMIN . '/classes/app_mlog');
	/*
	 * Tags aktualisieren
	 */
	$tag = new Tags();
	
	$tag->deleteTag(array(
		'tag' => trim($cmt_tabledata['tag_name'])
	));

?>