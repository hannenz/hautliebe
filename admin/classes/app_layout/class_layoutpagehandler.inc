<?php
/**
 * class pageHandler
 * 
 * Klasse, die Methoden zur Manipulation von Website-Seiten im Content-o-mat bereitstellt
 * 
 * Website-Seite in der Seitenstruktur können mit Hilfe dieser KLasse bearbeitet werden
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2015-11-25
 */

namespace Contentomat\Layout;

use Contentomat\DBCex;
use Contentomat\PageHandler;
	
class LayoutPageHandler extends PageHandler {

	protected $pageID = 0;
	
	/**
	 * Konstruktor-Methode
	 * 
	 * @param void Erwartet keine Parameter
	 */
	public function __construct() {
		parent::__construct();
		$this->parser = new LayoutParser();
	}
	
	/**
	 * public function savePageContent()
	 * Saves the layout objects of a page.
	 * 
	 * @param array Multiple params passed in an associative array:
	 *  'content' => object The content objects as an PHP standard object (parsed from JSON)!!
	 *  'links' => object The links are passed as an object created from JSON data
	 *  'pageID' => number Database id of the page to save
	 *  'language' => string Language shortcut
	 *  
	 * @return boolean
	 */
	
	public function savePageContent($params=array()) {
	
		$defaultParams = array(
			'pageID' => $this->pageID,
			'language' => $this->language
		);

		$params = array_merge($defaultParams, (array)$params);
		
		$contentTable = $this->cmtContentsTablePrefix . $this->db->dbQuote($params['language']);
		$pageID = intval($params['pageID']);

		$returnData = array();
		$returnLinks = array();
		
		$linkData = $params['links'];
		
		
		foreach ((array)$params['content'] as $groupNr => $group) {
			
			foreach((array)$group as $objectPos => $object) {
				
				$objectContent = array();
				$elements = $object->elements;
				
				foreach((array)$elements as $element) {
					
					// sucks, but fieldname in db is "file1" not "script1" :-(
					if ($element->type == 'script') {
						$element->type = 'file';
					}
					
					// handle links
					preg_match_all('/\<cmtlink\:([0-9]+)\>.*\<\/cmtlink\:\1\>/', $element->content, $elementLinks);
					
					foreach ($elementLinks[1] as $linkNr) {

						//$linkID = $this->saveLink($linkData->$linkNr);
						$linkID = $this->saveLink($linkData->$linkNr);
						
						if ($linkID) {
							
							// link saving successful: replace temporary tag with macro "{LINK:linkID}"
							$element->content = str_replace(array(
								'<cmtlink:' . $linkNr .'>',
								'</cmtlink:' . $linkNr .'>'
							), array(
								'{LINK:' . $linkID . '}',
								'{ENDLINK}'
							), $element->content);
							
							$returnLinks[$linkNr] = $linkID;
						} else {
							
							// in case of an saving error, delete link from content
							$element->content = str_replace(array(
								'<cmtlink:' . $linkNr .'>',
								'</cmtlink:' . $linkNr .'>'
							), '', $element->content);
						}
					}

					// cleanup content
					$element->content = $this->cleanupContent($element->content);
					
					$objectContent[$element->type . intval($element->typeNr)] = $element->content;
				}

				$objectID = intval($object->cmtObjectID);
				$objectContent = array_merge($objectContent, array(
					'cmt_visible' => intval($object->visibility),
					'cmt_lastmodified' => date('Y-m-d H:i:s'),
					'cmt_lastmodifiedby' => CMT_USERID,
					'cmt_position' => intval($objectPos) + 1,
					'cmt_objectgroup' => intval($groupNr),
					'cmt_pageid' => $pageID,
					'cmt_objecttemplate' => intval($object->cmtObjectTemplateID)
				));
//var_dump($objectContent);				
				if ($objectID) {
					$query = "UPDATE " . $contentTable . " SET " . $this->db->makeSetQuery($objectContent) . " WHERE id ='" . $objectID . "' LIMIT 1";
					$check = $this->db->query($query);
				} else {
					$query = "INSERT INTO " . $contentTable . " SET " . $this->db->makeSetQuery($objectContent);
					$check = $this->db->query($query);
					$objectID = $this->db->getLastInsertedID();
				}
				$returnData[$groupNr][$objectPos] = $objectID;
			}
		}
		
		return array(
			'objectIDs' => $returnData,
			'linkIDs' => $returnLinks,
			'savingSuccessful' => !$check,
			'DBError' => $this->db->getLastError(),
			'DBErrorNr' => $this->db->getLastErrorNr()
		);
	}
	
	public function saveLink($linkData) {

		$linkType = $linkData->cmtLinkType;
		$linkID = intval($linkData->cmtLinkId);
		
		// store different link types data in the correct database fields
		switch($linkType) {
			
			case 'internal':
				$data = array(
					'cmt_lang' => $linkData->cmtInternalLanguage,
					'cmt_page' => $linkData->cmtInternalPageId,
					'cmt_url' => '',
					'cmt_target' => $linkData->cmtInternalTarget,
					'cmt_addhtml' => $linkData->cmtInternalAddHtml,
				);
				break;
				
			case 'external':
				$data = array(
					'cmt_lang' => '',
					'cmt_page' => '',
					'cmt_url' => $linkData->cmtExternalUrl,
					'cmt_target' => $linkData->cmtExternalTarget,
					'cmt_addhtml' => $linkData->cmtExternalAddHtml
				);
				break;
				
			case 'download':
				$data = array(
					'cmt_lang' => '',
					'cmt_page' => '',
					'cmt_url' => $linkData->cmtDownloadUrl,
					'cmt_target' => $linkData->cmtDownloadTarget,
					'cmt_addhtml' => $linkData->cmtDownloadAddHtml
				);
				break;
				
			case 'email':
				$data = array(
					'cmt_lang' => '',
					'cmt_page' => '',
					'cmt_url' => $linkData->cmtEmail,
					'cmt_target' => '',
					'cmt_addhtml' => $linkData->cmtEmailAddHtml
				);
				break;
				
			case 'individual':
				$data = array(
					'cmt_lang' => '',
					'cmt_page' => '',
					'cmt_url' => $linkData->cmtIndividualUrl,
					'cmt_target' => $linkData->cmtIndividualTarget,
					'cmt_addhtml' => $linkData->cmtIndividualAddHtml
				);
				break;
				
			default:
				$data = array();
		}
		
		$data = array_merge($data, array (
			'cmt_linkonpage' => $this->pageID,
			'cmt_type' => $linkData->cmtLinkType,
			'cmt_lastmodified' => date('Y-m-d H:i:s'),
			'cmt_lastmodifiedby' => CMT_USERID
		));
		
		if ($linkID) {
			$check = $this->db->query(
				"UPDATE " . $this->cmtLinksTablePrefix . $this->language . 
				" SET " . $this->db->makeSetQuery($data) .
				" WHERE id = '" . $linkID . "' LIMIT 1"
			);
			
			if (!$check) {
				return $linkID;
			} else {
				return 0;
			}
			
		} else {
			$data = array_merge($data, array(
				'cmt_created' => date('Y-m-d H:i:s'),
				'cmt_createdby' => CMT_USERID
			));
			$this->db->query(
				"INSERT INTO " . $this->cmtLinksTablePrefix . $this->language .
				" SET " . $this->db->makeSetQuery($data)
			);
			return $this->db->getLastInsertedID();
		}
		
		
		
// 		cmt_addhtml 	zusätzliches HTML 	string 	nein 	nein
// 		cmt_created 	Erstellt am 	datetime 	nein 	nein
// 		cmt_createdby 	Erstellt von 	system_var 	nein 	nein
// 		cmt_lang 	Sprache 	string 	nein 	nein
// 		cmt_lastmodified 	Zuletzt aktualisiert 	datetime 	nein 	nein
// 		cmt_lastmodifiedby 	Zuletzt aktualisiert von 	system_var 	nein 	nein
// 		cmt_linkid 	Link-ID 	integer 	nein 	nein
// 		cmt_linkonpage 	Link auf Seite 	string 	nein 	nein
// 		cmt_page 	interner Link: Websiteseite 	select_recursive 	nein 	nein
// 		cmt_target 	Zielfenster 	select 	nein 	nein
// 		cmt_type 	Typ 	select 	nein 	nein
// 		cmt_url 	externer Link: URL 	string
	}
	
	public function deleteObjects($params) {
		
		$defaultParams = array(
			'language' => $this->language
		);
		
		$params = array_merge($defaultParams, (array)$params);
		
		//$contentTable = 'cmt_content_' . $params['language'];
		$objects = array();
		$success = true;

		foreach((array)$params['objectIDs'] as $objectID) {

			//$check = $this->deleteObject(array('objectID' => $objectID, 'language' => $language));
			$check = $this->deleteObject($objectID, $params['language']);
			
			if ($check) {
				$object[] = $objectID;
			} else {
				$success = false;
			}
		}
		
		return array(
			'deletedObjectIDs' => $objects,
			'deletionSuccessful' => $success
		);
	}
	
	public function cleanupContent($content) {
		
		$content = str_replace(array("\n", "\t", "\r"), '', $content);
		return $content;
	}
	
// 	public function deleteObject($params) {
		
// 		$defaultParams = array(
// 			'language' => $this->language
// 		);
// 		$params = array_merge($defaultParams, (array)$params);

// 		$objectID = intval($params['objectID']);
// 		$contentTable = 'cmt_content_' . $params['language'];
		
// 		return (bool)!$this->db->query("DELETE FROM " . $contentTable . " WHERE id = '" . $objectID . "'");
// 	}

	
	/**
	 * public function createObject()
	 * Method creates a new layout object.
	 * 
	 * @param array $params All variables as an associative array
	 * @return string parsed HTML
	 */
	public function createObject($params=array()) {

		$defaultParams = array(
			'pageID' => 0,
			'language' => $this->language,
			'objectGroup' => 0,
			'objectTemplateID' => 0,
			'objectPosition' => 0,
			'objectContent' => array()
		);
		$params = array_merge($defaultParams, $params);
	

		$pageID = intval($params['pageID']);
		$language = $this->db->dbQuote($params['language']);
		$objectTemplateID = intval($params['objectTemplateID']);
		$objectGroup = $params['objectGroup'];
	
		if (!$pageID || !$objectTemplateID || !$objectGroup) {
			return '';
		}
	
		$params['objectPosition'] = intval($params['objectPosition']);
		 
		if (!$params['objectPosition']) {

			// get last position in object group
			$this->db->query("SELECT MAX(cmt_position) AS lastPosition FROM " . $this->cmtContentsTablePrefix . $language . " WHERE cmt_pageid = '" . $pageID . "' AND cmt_objectgroup = '" . $objectGroup ."'");
			$r = $this->db->get();
			$newPosition = intval($r['lastPosition']) + 1;
		} else {
			$newPosition = $params['objectPosition'];
		}
	
		// Felder eintragen
		if (is_array($params['objectContent'])) {
			$newFields = $params['objectContent'];
			
			// remove links until there is no logic to duplicate them to!
			foreach ($newFields as $fieldName => $value) {
				$newFields[$fieldName] = preg_replace(array('/\{LINK:([0-9]+)\}/', '/\{ENDLINK\}/'), '', $value);
			}
		} else {
			$newFields = array();
		}

		$newFields['cmt_pageid'] = $pageID;
		$newFields['cmt_objectgroup'] = $objectGroup;
		$newFields['cmt_position'] = $newPosition;
		$newFields['cmt_visible'] = 0;
		$newFields['cmt_objecttemplate'] = $objectTemplateID;
		$newFields['cmt_created'] = date('Y-m-d H:i:s');
		$newFields['cmt_createdby'] = CMT_USERID;
			
		// neues Objekt speichern
		$check = $this->db->query("INSERT INTO " . $this->cmtContentsTablePrefix . $language . " SET " . $this->db->makeSetQuery($newFields));
		if ($check) {
			return '';
		}

		$newID = $this->db->getLastInsertedID();
		$this->parser->obj_id = $newID;
	
		// neues Objekt für die Anzeige erzeugen
		$objectTemplateSource = $this->getObjectTemplate($objectTemplateID);
	
		// ... und Content parsen
		$this->parser->content_data = $newFields;
	
		$this->parser->mode = 'layout';
		$objectContent = $this->parser->parse($objectTemplateSource);
	
		if ($this->parser->pageHasHtmlObject) {
			$this->parser->setParserVar('objectType', 'html');
		}
			
		// Objekt-Service-Template holen
		$objectTemplate = $this->parser->getTemplate('app_layout/cmt_layout_object.tpl');
			
		// Variablen für Service-Kasten übergeben
		$this->parser->setParserVar('id', $newID);
		$this->parser->setParserVar('cmt_position', $newPosition);
		$this->parser->setParserVar('cmt_obj_group', $objectGroup);
		$this->parser->setParserVar('object_content', $objectContent);
		$this->parser->setParserVar('objectEnabled', false);
			
		// TODO: Was ist "<cmtid />"-Tag?
		// Objekt-Bearbeitungsrahmen parsen
		return '<cmtid>cmtContentObject_'.$newID.'</cmtid>'.$this->parser->parse($objectTemplate);
	
	}
	
	/**
	 * public function deleteObject()
	 * 
	 * @param number $objectID ID of the object to delete
	 * @param language $language Language hortcut of the object to delete
	 * 
	 * @return boolean
	 */
	public function deleteObject($objectID, $language) {
		
		$language = $this->db->dbQuote($language);
		$objectID = intval($objectID);
		
		if (!$objectID || !$language) {
			return false;
		}

		$check = $this->db->query("DELETE FROM " . $this->cmtContentsTablePrefix . $language . " WHERE id = '" . $objectID . "' LIMIT 1");
		
		return !$check;
	}

	/**
	 * public function duplicateObject()
	 * Duplicates a page object. 
	 * TODO: Links are not duplicated yet!!!
	 * 
	 * @param number $objectID id of the object
	 * @param string $language Language shortcut, e.g. 'en'
	 * 
	 * @return string The duplicated and parsed new content object.
	 */
	public function duplicateObject($objectID, $language) {
	
		$language = $this->db->dbQuote($language);
		$objectID = intval($objectID);
	
		if (!$objectID || !$language) {
			return array();
		}
		$objectData = $this->getObjectData($objectID, $language);
		
		$oldID = $objectData['id'];
		unset($objectData['id']);
	
		$newContent = $this->createObject(array(
			'pageID' => intval($objectData['cmt_pageid']),
			'language' => $language,
			'objectGroup' => intval($objectData['cmt_objectgroup']),
			'objectTemplateID' => $objectData['cmt_objecttemplate'],
			'objectPosition' => $objectData['cmt_position'],
			'objectContent' => $objectData
		));
	
		if ($newContent) {
			$this->db->updatePositions(array(
				'table' => $this->cmtContentsTablePrefix . $language,
				'field' => 'cmt_position',
				'startAt' => intval($objectData['cmt_position']) + 1,
				'where' => " (cmt_objectgroup = '" . $objectData['cmt_objectgroup'] ."' AND cmt_pageid = '" . intval($objectData['cmt_pageid']) . "' AND id <> '" . $oldID . "' AND cmt_position >= '" . intval($objectData['cmt_position']) ."')"
			));
		}

		return $newContent;
	}
	
	/**
	 * public function getPageTemplate()
	 * Returns the template source of a given templateID.
	 * 
	 * @param number $pageTemplateID
	 * @return string The source (e.g. HTML)
	 */
	public function getPageTemplate($pageTemplateID) {
		$pageTemplateID = intval($pageTemplateID);
		
		$this->db->query("SELECT cmt_source FROM cmt_templates_pages WHERE id = '" . $pageTemplateID . "'");
		$r = $this->db->get();
		
		return trim(stripslashes($r['cmt_source']));
	}

	/**
	 * public function getObjectTemplate()
	 * Returns the template source of a given templateID.
	 *
	 * @param number $objectTemplateID
	 * @return string The source (e.g. HTML)
	 */
	public function getObjectTemplate($objectTemplateID) {
		$objectTemplateID = intval($objectTemplateID);
	
		$this->db->query("SELECT cmt_source FROM cmt_templates_objects WHERE id = '" . $objectTemplateID . "'");
		$r = $this->db->get();
	
		return trim(stripslashes($r['cmt_source']));
	}
	

	public function getObjectData($objectID, $language) {
		$language = $this->db->dbQuote($language);
		$objectID = intval($objectID);
		
		if (!$objectID || !$language) {
			return array();
		}
				
		$this->db->query("SELECT * FROM " . $this->cmtContentsTablePrefix . $language . " WHERE id = '" . $objectID . "' LIMIT 1");
		return $this->db->get();
	}

	public function setPageID($pid) {
		$this->pageID = intval($pid);
	}

}
?>