<?php
/**
 * class pageHandler
 * 
 * Klasse, die Methoden zur Manipulation von Website-Seiten im Content-o-mat bereitstellt
 * 
 * Website-Seite in der Seitenstruktur können mit Hilfe dieser KLasse bearbeitet werden
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2015-10-25
 */

namespace Contentomat;


 class PageHandler {

	protected $db;
	protected $depth = 0;
	protected $cmtPagesTablePrefix;
	protected $cmtContentsTablePrefix;
	protected $cmtLinksTablePrefix;
	protected $language;
	protected $languages;
	protected $parser;
		
	public function __construct() {
		
		$this->db = new DBCex();
		$this->cmtPagesTablePrefix = 'cmt_pages_';
		$this->cmtContentsTablePrefix = 'cmt_content_';
		$this->cmtLinksTablePrefix = 'cmt_links_';
		$this->language = DEFAULTLANGUAGE;
		$this->languages = array();
		$this->parser = null;
	}
	
	/**
	 * public function getPages()
	 * 
	 * Erwartet Parameter in einem Array. Liest die Seitenstruktur rekursiv aus und gibt sie als mehrdimensionales Array
	 * zurück. Dabei folgt die Rückgabe dem Schema:
	 * 
	 * $pages['pageId']['data'] -> Felder des Datenbankeintrags zur Seite mit der 'pageId'
	 * $pages['pageId']['pages'] -> wiederum mehrdimensionales Array dieser Struktur, falls es Unterseiten gibt
	 *  
	 * @param array $params Erwartet Parameter in einem assoziativen Array:
	 * 'parentID' number ID der Seite, deren Kindelemente als Seitenstrukturbaum zurückgegeben werden. Default ist: 0, bzw. 'root'
	 * 'pagesTableName' string Name der Datenbanktabelle der Seiten
	 * 'depth' number Tiefe, bzw. Anzahl der rekursiven Wiederholungen;
	 * 
	 * @return array Array, welches je nach Parameterübergabe die Verzeichnisstruktur enthält
	 * 
	 */
	 public function getPages($params) {
		
	 	$defaultParams = array(
	 		'parentID' => 'root', 
	 		'language' => $this->language,
	 		'depth' => 0
	 	);
		$params = array_merge($defaultParams, $params);

		if (!$this->checkLanguage($params['language'])) {
			return array();
		}
		
		$params['parentID'] = intval($params['parentID']);
		
		if (!$params['parentID']) {
			$params['parentID'] = 'root';
		}
		
		$params['depth'] = intval($params['depth']);
		
		return $this->getPagesRecursive($params['parentID'], $params['language'], $params['depth']);
	 }
	 
	 protected function getPagesRecursive($pageID, $language, $maxDepth) {
	 	
	 	$pages = array();
	 	static $depth = 0;

	 	
		$db = new DBCex();
		$db2 = new DBCex();
				
	 	$query = "SELECT * FROM " . $this->cmtPagesTablePrefix . $language ." WHERE cmt_parentid = '" . $pageID . "' ORDER BY cmt_pagepos ASC";
	 	$db->Query($query);
	 	
	 	while ($r = $db->get()) {
	 		
	 		$pages[$r['id']]['data'] = $r;
	 			
	 		$params['parentID'] = $r['id'];


	 		$db2->query("SELECT COUNT(id) AS hasChildren FROM " . $this->cmtPagesTablePrefix . $language ." WHERE cmt_parentid = '" . $r['id'] . "'");
	 		$rc = $db2->get();
	 		$pages[$r['id']]['data']['hasChildren'] = $rc['hasChildren'];
	 		
	 		if ($maxDepth == 0 || ++$depth < $maxDepth ) {
	 			$pages[$r['id']]['pages'] = $this->getPagesRecursive($r['id'], $language, $maxDepth);
	 		}
	 	}
	 	$db2->close();
	 	unset($db2);
	 	 
	 	$db->close();
	 	unset ($db);
	 	
	 	return $pages;
	 	
	 }
	 

	 /**
	  * public function deletePage()
	  * Löscht eine Seite mit allen Unterseiten (optional) und allen Inhalten (optional). 
	  * TODO: event. Parameter 'appendChildrenTo' (Seiten-ID) => Kindseiten werden nicht gelöscht, sondern einer anderen Seite angehängt.
	  *
	  * @param array $params Parameter in assoziativem Array:
	  * 'pageID' => ID der Seite die gelöscht werden soll. 
	  *	'language' => Sprachversionskürzel,
	  *	'deleteContents' => Sollen die Inhalte auch gelöscht werden? (boolean, default: true),
	  *	'deleteChildren' => Sollen die Kindseiten auch gelöscht werden? (boolean, default: true)
	  *
	  * @return boolean
	  */
	 public function deletePage($params) {
	 	
	 	if (!is_array($params)) {
	 		return false;
	 	}
	 	
	 	$defaultParams = array(
	 		'pageID' => 0, 
	 		'language' => $this->language,
	 		'deleteContents' => true,
	 		'deleteChildren' => true
	 	);
	 	$params = array_merge($defaultParams, $params);
	 	
	 	if (!$this->checkLanguage($params['language'])) {
	 		return false;
	 	}
	 	
	 	
	 	if ($params['deleteChildren']) {
	 		$pagesToDelete = $this->getChildren(array(
		 		'pageID' => $params['pageID'],
		 		'language' => $params['language']
		 	));
	 	} else {
	 		$pagesToDelete = array();
	 	}
	 	

	 	array_unshift($pagesToDelete, $params['pageID']);
	 	
	 	foreach ($pagesToDelete as $pageID) {
	 		
	 		if ($params['deleteContents']) {

	 			// 1. Inhalte der Seite löschen
		 		$query = "DELETE FROM " . $this->cmtContentsTablePrefix . $params['language'] . " WHERE cmt_pageid = '" . $this->db->dbQuote($pageID) ."'";
		 		$check = $this->db->query($query);
		 		if ($check) {
		 			return false;
		 		}
		 		
		 		// 2. Links der Seite löschen
		 		$query = "DELETE FROM " . $this->cmtLinksTablePrefix . $params['language'] . " WHERE cmt_linkonpage = '" . $this->db->dbQuote($pageID) ."'";
		 		$check = $this->db->query($query);
		 		if ($check) {
		 			return false;
		 		}
	 		}
	 		
	 		// 3. Seite selbst löschen
	 		$query = "DELETE FROM " . $this->cmtPagesTablePrefix . $params['language'] . " WHERE id = '" . $this->db->dbQuote($pageID) ."' LIMIT 1";
	 		$check = $this->db->query($query);
	 		if ($check) {
	 			return false;
	 		}
	 	}
	 	
	 	return true;

	 }
	 
	 /**
	  * public function duplicatePageContents()
	  * Dupliziert die Inhalte und Links einer Seite.
	  * 
	  * TODO: Better move to Class CmtPage!!!
	  *
	  * @param array $params Assoziatives Array mit folgenden Schlüssel-/Wertpaaren:
	  *	'duplicatedPageID' => number ID der Seite, die dupliziert wurde (Quellseite)
 	  *	'newPageID' => number ID der neuen (duplizierten) Seite
 	  *	'language' => string Sprachkürzel der Seite, z.B. 'de
 	  *	'addLinkData' => array Optionales, assoziatives Array, welches mit jedem Link-Datensatz zusammengeführt wird (z.B. "array('cmt_created' => '2013-01-22 19:12:00')", zur individuellen Änderung von Daten)
 	  *	'addContentData' => array Optionales, assoziatives Array, welches mit jedem Link-Datensatz zusammengeführt wird.
	  *
	  * @return boolean
	  */
	 public function duplicatePageContents($params) {
	 	
	 	if (!is_array($params)) {
	 		return false;
	 	}
	 	 
	 	$defaultParams = array(
	 			'duplicatedPageID' => 0,
	 			'newPageID' => 0,
	 			'language' => $this->language,
	 			'addLinkData' => array(),
	 			'addContentData' => array(),
	 	);
	 	$params = array_merge($defaultParams, $params);
	 	 
	 	if (!$this->checkLanguage($params['language'])) {
	 		return false;
	 	}

	 	if (!$params['newPageID'] || !$params['duplicatedPageID'] ) {
	 		return false;
	 	}
	 	
	 	$pageContents = $this->getPageContents(array(
	 		'pageID' => $params['duplicatedPageID'],
	 		'language' => $params['language']
	 	));
	 	
	 	$pageLinks = $this->getPageLinks(array(
 			'pageID' => $params['duplicatedPageID'],
 			'language' => $params['language']
	 	));
	 	
	 	// 1. Inhalte der Seite duplizieren
	 	foreach ($pageContents as $content) {
	 		
	 		$content['cmt_pageid'] = intval($params['newPageID']);
	 		unset($content['id']);
	 		$content = array_merge($content, $params['addContentData']);

	 		$query = "INSERT INTO " . $this->cmtContentsTablePrefix . $params['language'] . " SET " . $this->db->makeSetQuery($content);
	 		$check = $this->db->query($query);
	 		if ($check) {
	 			return false;
	 		}
	 	}

	 	// 2. Links der Seite duplizieren
	 	foreach ($pageLinks as $link) {
	 	
	 		$link['cmt_linkonpage'] = intval($params['newPageID']);
	 		$linkID = $link['id'];
	 		unset($link['id']);
	 		$link = array_merge($link, $params['addLinkData']);
	 		
	 		// Textinhalte der neuen Seite nach alter Link-ID durchsuchen
	 		$linkRegExp = '\{LINK:(' . $linkID . ')\}';
	 		$query = "SELECT * FROM " . $this->cmtContentsTablePrefix . $params['language'] ." 
	 				  WHERE cmt_pageid = '" . intval($params['newPageID']) . "' 
	 				  AND (text1 REGEXP '" . $linkRegExp . "'  
	 				  || text2 REGEXP '" . $linkRegExp . "' 
	 				  || text3 REGEXP '" . $linkRegExp . "' 
	 				  || text4 REGEXP '" . $linkRegExp . "' 
	 				  || text5 REGEXP '" . $linkRegExp . "' 
	 				  || head1 REGEXP '" . $linkRegExp . "' 
	 				  || head2 REGEXP '" . $linkRegExp . "' 
	 				  || head3 REGEXP '" . $linkRegExp . "' 
	 				  || head4 REGEXP '" . $linkRegExp . "' 
	 				  || head5 REGEXP '" . $linkRegExp . "' 
	 				  || image1 REGEXP '" . $linkRegExp . "' 
	 				  || image2 REGEXP '" . $linkRegExp . "' 
	 				  || image3 REGEXP '" . $linkRegExp . "' 
	 				  || image4 REGEXP '" . $linkRegExp . "' 
	 				  || image5 REGEXP '" . $linkRegExp . "')";
	 		
	 		$this->db->query($query);
	 		$contentWithLinks = $this->db->getAll();
	 	
	 		$query = "INSERT INTO " . $this->cmtLinksTablePrefix . $params['language'] . " SET " . $this->db->makeSetQuery($link);
	 		$check = $this->db->query($query);
	 		if ($check) {
	 			return false;
	 		}
	 		
	 		$newLinkID = $this->db->getLastInsertedID();

	 		// Links im Content der duplizierten Seite aktualisieren
	 		foreach ($contentWithLinks as $contentID => $content) {
	 				
	 			foreach($content as $fieldName => $field) {
	 				
	 				$content[$fieldName] = preg_replace('/' . $linkRegExp . '/', '{LINK:' . $newLinkID . '}', $field);
	 			}
	 			
	 			$check = $this->db->query("
	 				UPDATE " . $this->cmtContentsTablePrefix . $params['language'] . " 
	 				SET " . $this->db->makeSetQuery($content) . " 
	 				WHERE id = '" . $content['id'] . "'"
	 			);
	 			
	 		}
	 	}
	 	
	 	return true;
	 	
	 }
	 
	/**
	 * public function getPageContents()
	 * Ermittelt alle Inhalte einer Seite.
	 * 
	 * @param array $params Assoziatives Array:
	 * 'pageID' => number ID der Seite
	 * 'language' => string Sprachkürzel der Seite
	 * 'visibility' => set to 1 (default): get only visible, 0: only not visible, 99: all 
 	 *
	 * @return array
	 */
	 public function getPageContents($params) {	

	 	$contents = array();
	 	
	 	if (!is_array($params)) {
	 		return $contents;
	 	}

	 	$defaultParams = array(
 			'pageID' => 0,
 			'language' => $this->language,
	 		'visibility' => 1				// set to 1 (default): only visible, 0: only not visible, 99: all 
	 	);
	 	$params = array_merge($defaultParams, $params);

	 	if (!$this->checkLanguage($params['language'])) {
	 		return $contents;
	 	}
 	
	 	if (!$params['pageID']) {
	 		return $contents;
	 	}
	 	
	 	$where = array(
	 		"cmt_pageid = '" . intval($params['pageID']) ."'"
	 	);
	 	
	 	switch($params['visibility']) {
	 		
	 		case 1:
	 			$where[] = "cmt_visible = '1'";
	 			break;
	 			
 			case 0:
 				$where[] = "cmt_visible = '0'";
 				break;
 				
 			default:
 				break;
	 	}

	 	$query = "SELECT * FROM " . $this->cmtContentsTablePrefix . $params['language'] . " WHERE " . implode(' AND ', $where) . 
	 			 " ORDER BY cmt_position ASC";
	 	$this->db->query($query);

	 	while ($r = $this->db->get()) {
	 		$contents[$r['id']] = $r;
	 	}
	 	
	 	return $contents;
	 	
	 }


	/**
	 * public function getPageLinks()
	 * Ermittelt alle Links der Seite.
	 *
	 * @param array $params Assoziatives Array:
	 * 'pageID' => number ID der Seite
	 * 'language' => string Sprachkürzel der Seite
 	 *
	 * @return array
	 */	 
	 public function getPageLinks($params) {
	 
	 	$links = array();
	 	
	 	if (!is_array($params)) {
	 		return $links;
	 	}
	 
	 	$defaultParams = array(
	 			'pageID' => 0,
	 			'language' => $this->language
	 	);
	 	$params = array_merge($defaultParams, $params);
	 
	 	if (!$this->checkLanguage($params['language'])) {
	 		return $links;
	 	}
	 	 
	 	if (!$params['pageID']) {
	 		return $links;
	 	}
	 	 
	 	$query = "SELECT * FROM " . $this->cmtLinksTablePrefix . $params['language'] . " WHERE cmt_linkonpage = '" . intval($params['pageID']) ."'";
	 	$this->db->query($query);
	 	 
	 	while ($r = $this->db->get()) {
	 		$links[$r['id']] = $r;
	 	}

	 	return $links;
	 	 
	 }
	
	/**
	 * public function getPage()
	 * Liefert den Datenbanktabelleneintrag / die Seiteneigenschaften zur gewünschten Seite
	 * 
	 * @param array $params Assoziatives Array:
	 * 'pageID' => number ID der Seite
	 * 'language' => string Sprachkürzel der Seite
	 *
	 * @return array Array, welches Seitendaten enthält (Schlüssel => Feldname, Wert => Feldwert)
	 * 
	 */
	public function getPage($params) {
		
		$page = array();
	 	
	 	if (!is_array($params)) {
	 		return $page;
	 	}
	 
	 	$defaultParams = array(
 			'pageID' => 0,
 			'language' => $this->language
	 	);
	 	$params = array_merge($defaultParams, $params);
	 
	 	if (!$this->checkLanguage($params['language'])) {
	 		return $page;
	 	}
	 	 
	 	if (!$params['pageID']) {
	 		return $page;
	 	}		
		
		$query = "SELECT * FROM " . $this->cmtPagesTablePrefix . $this->db->dbQuote($params['language']) . " WHERE id = '" . intval($params['pageID']) ."' LIMIT 1";
		$this->db->query($query);
		
		return $this->db->get();
	}
	
	/**
	 * 
	 * public function getPageData()
	 * DEPRECATED / OUTDATED: Wrapper for PageHandler::getPage(), for backward compatibility! Don#t use this method anymore!
	 *
	 * @param array $params 'pageId' and optional 'pageLanguage' as associative array.
	 *
	 * @return array
	 */
	public function getPageData($params) {
		
		if (!is_array($params)) {
			$params = array('pageId' => $params);
		}
		
		if ($params['pageId']) {
			$params['pageID'] = $params['pageId'];
		}
		
		if ($params['pageLanguage']) {
			$params['language'] = $params['pageLanguage']; 
		}
		
		return $this->getPage($params);
	}

	public function getStartPage($language) {
	
		if (!$language) {
			return array();
		}

		$this->db->query("SELECT * FROM " . $this->cmtPagesTablePrefix . $this->db->dbQuote($language)  . " WHERE cmt_isroot = '1'");
		$pageData = $this->db->get();
	
		$c = 0;
		while ($pageData['cmt_type'] != 'page' && $c++ < 10) {
			
			$this->db->query("SELECT * FROM " . $this->cmtPagesTablePrefix . $this->db->dbQuote($language)  . " WHERE cmt_parentid = '" . intval($pageData['id']) . "'");
			$pageData = $this->db->get();
		}
		return $pageData;
	}
	
	/**
	 * Sets the flag 'is start page' for a selected page or directory
	 * 
	 * @param number $pageID		Page's id
	 * @param unknown $language		Page's language shortcut	
	 * @return boolean
	 */
	public function setStartPage($pageID, $language) {
		
		$this->db->query("UPDATE " . $this->cmtPagesTablePrefix . $this->db->dbQuote($language)  . " SET cmt_isroot = '0' WHERE cmt_isroot = '1'");
		return (bool)!$this->db->query("UPDATE " . $this->cmtPagesTablePrefix . $this->db->dbQuote($language)  . " SET cmt_isroot = '1' WHERE id = '" . (int)$pageID . "'");
	}
	
	public function savePageData($pageData, $language='') {

		$pageID = $pageData['id'];
		unset ($pageData['id']);
		
		if (!$pageID) {
			$queryType = 'insert';
		} else {
			$queryType = 'update';
		}
		
		if (!$language) {
			$language = $this->language;
		}

		$setQuery = $this->db->makeSetQuery($pageData);
		$check = true;

		switch ($queryType) {
			
			case 'insert':
				$query = "INSERT INTO " . $this->cmtPagesTablePrefix . $language . " SET " . $setQuery;
				$check = $this->db->query($query);
				break;
				
			case 'update':
				$query = "UPDATE " . $this->cmtPagesTablePrefix . $language . " SET " . $setQuery . " WHERE id = '" . $pageID . "' LIMIT 1";
				$check = $this->db->query($query);
				break;
		}

		return (boolean)!$check;
		
	}


	public function getParents($params) {
		
		if (!is_array($params)) {
			return $parents;
		}
		
		$defaultParams = array(
			'pageID' => 0,
			'language' => $this->language,
			'parents' => array()
		);
		$params = array_merge($defaultParams, $params);
		
		$parents = $params['parents'];
		
		if (!$this->checkLanguage($params['language'])) {
			return $parents;
		}
		
		if (!$params['pageID']) {
			return $parents;
		}
		
		$page = $this->getPage($params);
		array_unshift($parents, $page);
		
		if ($page['cmt_parentid'] && $page['cmt_parentid'] != 'root') {

			$parents = $this->getParents(array(
				'pageID' =>	$page['cmt_parentid'],
				'language' => $params['language'],
				'parents' => $parents
			));
			
		}

		return $parents;
	}
	
	/**
	 * public function getChildren
	 * 
	 * Erwartet Parameter als Number (pageId) oder Array (array ('pageId' => Wert)). Liefert die IDs aller Kindelemente der gewünschten Seite.
	 * 
	 * @param pageId string ID der Seite, deren Kindelemente zurückgegeben werden sollen
	 * @param depth number Tiefe bis zu welcher die Kindelemente gesucht werden sollen. '0' ist default und bedeutet keine Limitierung
	 * @param language string Optional: Name der Sprachversion
	 * 
	 * @return array Array, welches die Kindelemente ohne hierarchische Struktur enthält!
	 * 
	 */
	public function getChildren($params) {
		
		if (!is_array($params)) {
			$params = array('pageID' => $params);
		}
		
		if ($params['pageId']) {
			$params['pageID'] = $params['pageId'];
		}
		
		$defaultParams = array(
			'pageId' => '1', 
			//'pagesTableName' => CMT_PAGES,
			'language' => $this->language, 
			'depth' => 0
		);
		$params = array_merge($defaultParams, $params);
		
		$db = new DBCex();
		$childIds = array();
		$ancestorIds = array();
		
		$db->query("SELECT * FROM ". $this->cmtPagesTablePrefix . $params['language'] . " WHERE cmt_parentid = '" . $db->dbQuote($params['pageID']) . "'");
		while ($r = $db->get()) {
			$childIds[] = $r['id'];
		}
		unset($db);
		
		$allAncestorIds = $childIds;		
		
		if ($params['depth']) {
			$this->depth++;
		}
		if (($params['depth'] && $this->depth < $params['depth']) || !$params['depth']) { 
			foreach ($childIds as $id) {
				$ancestorIds = $this->getChildren($id);
				$allAncestorIds = array_merge($allAncestorIds, $ancestorIds);
			}
		}
			
		return $allAncestorIds; 
	}

	/**
	 * public function getLanguages()
	 * Returns all available languages as associative array
	 *
	 * @param boolean $nocache Set to true when language data should be read out of database (slower) and not out of internal cache (faster). Use true after a change of entries in database table 'cmt_content_languages'.
	 * @return array Language data in associative array: language shortcut (key) => language name (value) 
	 */
	public function getLanguages($nocache = false) {
		
		if (!$nocache && !empty($this->languages)) {
			return($this->languages);
		} else {
			$this->languages = array();
		}
		
		$this->db->Query("SELECT * FROM cmt_content_languages ORDER BY cmt_position");
		
		while ($r = $this->db->Get()) {
			$this->languages[$r['cmt_language']] = $r['cmt_languagename'];
		}
		
		return $this->languages;
	}
	
	public function getLanguagesData() {
	
		$languages = array();
		$this->db->Query("SELECT * FROM cmt_content_languages ORDER BY cmt_position");
	
		while ($r = $this->db->get()) {
			$languages[] = $r;
		}
	
		return $languages;
	}

	public function getLanguageData($params = array()) {
	
		$defaultParams = array(
			'language' => '',
			'id' => 0
		);
		$params = array_merge($defaultParams, $params);
		$params['id'] = intval($params['id']);
		
		if (!$params['id'] && !$params['language']) {
			return array();
		}
		
		if ($params['id']) {
			$whereClause = " id = '" . $params['id'] . "'";
		} else {
			$whereClause = " cmt_language = '" . $this->dbQuote(trim($params['language'])) . "'";
		}
		$check = $this->db->Query("SELECT * FROM cmt_content_languages WHERE" . $whereClause . " LIMIT 1");
	
		if (!$check) {
			return $this->db->get();
		} else {
			return array();
		} 
	}
	
	public function checkLanguage($language) {
		
		if (in_array($language, array_keys($this->getLanguages()))) {
			return true;
		} else {
			return false;
		}
	}
	
	public function setLanguage($language) {
		
		$language = trim($language);
		$languages = $this->getLanguages();
	
		if (in_array($language, array_keys($languages))) {
			
			$this->language = $language;
			return true;
		} else {
			return false;
		}
	}
	
	public function getLastPositionInNode($nodeID) {
		
	}

	/**
	 * public function showPages()
	 * Parse the pages data recursively
	 *
	 * @param array $params Expects all data in an array:
	 *  'pagesData' => Array with all the pages data
	 *  'templateRow' => String contains HTML template for a row,
	 *	'templateFrame' => String contains HTML template for the frame,
	 *	'templateData' => Optional array with more vars to use while parsing the templates
	 *
	 * @return string parsed HTML content
	 */
	public function showPages($params = array()) {
		
		$defaultParams = array(
			'pagesData' => array(),
			'templateRow' => '',
			'templateFrame' => '',
			'templateData' => array()
		);
		$params = array_merge($defaultParams, $params);
		
		if (!$params['templateFrame']) {
			$params['templateFrame'] = '{VAR:pagesContent}';
		}
		
		if ($this->parser === null) {
			$this->parser = new Parser();
		}
		
		$this->parser->setMultipleParserVars($params['templateData']);

		return $this->processShowPages($params['pagesData'], $params['templateRow'], $params['templateFrame']);
	}
	
	/**
	 *
	 * private function processShowPages()
	 * Parses the pages data recursively
	 *
	 * @param array $pages Pages data
	 * @param string $templateRow Row templates (HTML)
	 * @param string $templateFrame Frame template (HTML)
	 *
	 * @return string parsed HTML content
	 */
	private function processShowPages($pages, $templateRow, $templateFrame) {
	
		$pageNr = 1;
		$nodeElements = count($pages);
	
		// Link-Variablen
		$this->parser->setParserVar('cmtReturnTo', $this->applicationID);
		$this->parser->setParserVar('cmtDBTable', $this->pagesTable);
		$this->parser->setParserVar('cmtPageLanguage', $this->cmtLanguage);
	
		foreach($pages as $node) {
	
			// Eigenschaften des Knotens
			$visibilitiyWrapper = array (
					0 => 'Disabled',
					1 => 'Visible',
					99 => 'Locked'
			);
			$this->parser->setParserVar('visibility', $visibilityWrapper[$node['data']['cmt_showinnav']]);
	
			if ($node['data']['cmt_protected']) {
				$this->parser->setParserVar('protected', 'Protected');
			} else {
				$this->parser->setParserVar('protected', '');
			}
	
			// Ist Element Root-Element?
			if ($node['data']['cmt_isroot']) {
				$this->parser->setParserVar('isRoot', 'Root');
			} else {
				$this->parser->setParserVar('isRoot', '');
			}
	
			$this->parser->setMultipleParserVars($node['data']);
	
			if ($node['pages']) {
				// Seite hat Unterseiten
				$subPagesContent = $this->processShowPages($node['pages'], $templateRow, $templateFrame);
	
				$this->parser->setMultipleParserVars($node['data']);
				$this->parser->setParserVar('subPagesContent', $subPagesContent);
				//$subPagesContent = $parser->parse($templateFrame);
			} else {
				$this->parser->setMultipleParserVars($node['data']);
				$subPagesContent = '';
			}
	
			// Ist Element letztes Element des Knotens?
			if ($pageNr == $nodeElements) {
				$this->parser->setParserVar('isLastPage', true);
			} else {
				$this->parser->setParserVar('isLastPage', false);
			}
			$this->parser->setParserVar('pageNr', $pageNr++);
	
			//$parser->setParserVar('hasChildren', count($node['pages']));
	
			$this->parser->setParserVar('subPagesContent', $subPagesContent);
			$pagesContent .= $this->parser->parse($templateRow);
	
		}
		$this->parser->setParserVar('pagesContent', $pagesContent);
		$pagesContent = $this->parser->parse($templateFrame);
	
		return $pagesContent;
	}
	
	/**
	 * public function savePagesOrder()
	 * saves the order of the pages
	 *
	 * @param array $nodeData Daten der Seiten in einem Array()
	 * @param string $parentNodeID ID des Vaterknotens
	 * @param object $pageHandler Das Pagehandler-Objekt
	 *
	 * @return boolean
	 */
	public function savePagesOrder($nodeData) {
	
		$pagePosition = 1;
	
		foreach ($nodeData as $node) {
	
			if (!$node['data']['cmt_pagepos']) {
				$node['data']['cmt_pagepos'] = $pagePosition++;
			}
			
			$check = $this->savePageData($node['data']);

			if (!$check) {
				return false;
			}
		
			if (is_array($node['pages'])) {
				$this->savePagesOrder($node['pages']);
			}
		}	
		return true;
	}
	
	
	public function getStartPageData($pageLang='') {
	
		if (!$pageLang) {
			if ($this->pageTable) {
				$pageTable = $this->pageTable;
				$pageLang = $this->pageLang;
			} else {
				return array();
			}
		} else {
			$pageTablesNames = $this->getPageTablesNames($pageLang);
			$pageTable = $pageTablesNames['pageTable'];
		}
		$this->db->query("SELECT * FROM ".$this->db->dbQuote($this->pageTable)." WHERE cmt_isroot = '1'");
		$pageData = $this->db->get();
	
		$this->pageDataCache[$pageLang][$pageData['id']] = $pageData;
	
		return $pageData;
	}
	
 	/**
	 * Gets the next free id from all pages tables (all languages) so one can synchronize page ids when creating new pages in a single language version.
	 * 
	 */
	public function getNextAvailablePageId() {
		
		$languageShortcuts = array_keys($this->getLanguages());
		
		$nextId = 0;
		$nextAvailableId = 1;
		$highestIds = array();

		foreach($languageShortcuts as $shortcut) {
			
			$this->db->query("SELECT id FROM " . $this->cmtPagesTablePrefix . $this->db->dbQuote($shortcut) . " ORDER BY id DESC LIMIT 1");
			$r = $this->db->get();
			$nextAvailableId = (int)$r['id'];
			
			$highestIds[] = $nextAvailableId;
			
			if ($nextAvailableId > $nextId) {
				$nextId = $nextAvailableId;
			}
		}
		
		// check if all language versions have the same higest id, then increase next available id (+1) to prevent collisions
		if (count(array_unique($highestIds)) == 1) {
			$nextId++;
		}

		return (int)$nextId;
	}
	
	/**
	 * Sets the auto increment value of a table (field id) to the given value. Performs a check before setting if value is valid.
	 * 
	 * @param number $id
	 * @return boolean
	 */
	public function setNextAvailablePageId($id=0) {
		
		$nextAvailableId = $this->getNextAvailablePageId();
		
		if (!$id) {
			$id = $nextAvailableId;
		}
		
		if ((int)$id < $nextAvailableId) {
			return false;
		}
		
		$languageShortcuts = array_keys($this->getLanguages());
		
		foreach($languageShortcuts as $shortcut) {
			
			$check = !(bool)$this->db->query("ALTER TABLE " . $this->cmtPagesTablePrefix . $this->db->dbQuote($shortcut) . " AUTO_INCREMENT = " . (int)$id);

			if (!$check) { 
				return false;
			}
		}		
	return true;
	}
}
?>