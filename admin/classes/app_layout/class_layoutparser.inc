<?php      
/**
 * class_layout_parser.inc
 * 
 * Class extends the template parser and provides additional and specific methods for the layout mode of the cms.
 * 
 * @package content-o-mat
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2017-06-29
 */

namespace Contentomat\Layout;

use \Contentomat\CMTParser;
use \Contentomat\Dataformat;
use \Contentomat\Contentomat;
use \Contentomat\Form;


class LayoutParser extends CMTParser {

	protected $layoutModeRawData;	// Woraround: If flag is true in layout mode, only the value of a parsed macro is returned without the additional content-o-mat layout mode tags
	protected $frameTemplate = '';
	protected $objectTemplate;		// TODO: Move object template to base parser in CMT 2.0!!!
	protected $pageTemplateId = 0;
	protected $form;

	public function __construct($refObj='') {
		parent::__construct($refObj);

		$this->layoutModeRawData = false;
		$this->mode = 'layout';
		$this->frameTemplate = $this->getTemplate('app_layout/cmt_object.tpl');
		
		$this->objectTemplate = new ObjectTemplate();
		$this->dformat = new Dataformat();	// not neccessary
		$this->form = new Form();
	}
	
	protected function control_LOOP_CONTENT($condition, $content='', $controlNr=0) {
		
		// Neue Content-Spalte/ -Gruppe
		$this->content_groups++;
			
		$objectParser = new LayoutParser;
		
		$objectParser->setPagesTable($this->pagesTable);
		$objectParser->setContentsTable($this->contentsTable);
		$objectParser->setLinksTable($this->linksTable);
		
		$objectParser->setPageVars($this->pageData);
		$objectParser->setPageId($this->pageId);
		$objectParser->setPageLanguage($this->cmtLanguage);
		$objectParser->setParentId($this->pageData['cmt_parentid']);
		
		$objectParser->evalvars = &$this->evalvars;
//		$objectParser->pagevars = &$this->pagevars;
		$objectParser->content_data = &$this->content_data;
			
		$submatch = explode(":", $condition);
		$objectGroup = $this->db->dbQuote($submatch[0]);
			
		$objectParser->mode = $this->mode;
		$obj_position = 1;

		$obj_firstgroup = 1;
		$obj_lastgroup = $this->content_groups;
		
		$contentObjects = array();
		$contentObject = '';
					
		// Content holen
// 		$query = "SELECT c.*, o.cmt_source, o.cmt_name AS cmtObjectTemplateName 
// 				  FROM ". $this->contentsTable . " c 
// 				  JOIN cmt_templates_objects o 
// 				  ON o.id = c.cmt_objecttemplate 
// 				  WHERE cmt_pageid = '" . $this->pageId . "' 
// 				  AND cmt_objectgroup = '" . $objectGroup . "' 
// 				  ORDER BY c.cmt_position";
		
		$query = "SELECT * FROM ". $this->contentsTable . " 
				  WHERE cmt_pageid = '" . $this->pageId . "'
				  AND cmt_objectgroup = '" . $objectGroup . "'
				  ORDER BY cmt_position";
					
		// Los geht's mit der Ausgabe
		$this->db->query($query);

		while ($r = $this->db->get()) {

			// get object template
			$t = $this->objectTemplate->getById($r['cmt_objecttemplate']);
			$r['cmt_source'] = $t['cmt_source'];
			$r['cmtObjectTemplateName'] = $t['cmt_name'];
			
			// Objekttyp HTML-Flag zurücksetzen
			$objectParser->vars['objectType'] = '';
	
			$objectParser->content_data = $r;
			$objectParser->obj_id = $r['id'];
	
			// Objekt-Inhalt parsen
			$obj_content = $objectParser->parse(stripslashes($r['cmt_source']));
	
			$objectParser->setMultipleParserVars($r);
 			$objectParser->setParserVar('objectContent', $obj_content);
	
			// Objekt-Bearbeitungsrahmen parsen
			$contentObject = $objectParser->parse($this->frameTemplate);
	
			// Positionszähler für Service-Zeile erhöhen
			$obj_position++;
			$contentObjects[] = $contentObject;

		}

		// Gruppe Ende
		$replaceData = '<div class="cmt-group" data-cmt-group-nr="' . $objectGroup . '">';
		$replaceData .= implode($content, $contentObjects);
		$replaceData .= '</div>';
	
		// HTML Variable holen
		if ($objectParser->pageHasHtmlObject) {
			$this->pageHasHtmlObject = $objectParser->pageHasHtmlObject;
			$this->htmlEditorImageFolder = $objectParser->htmlEditorImageFolder;
		}
	
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_HEAD()
	 *
	 * Type: Layout object macro
	 * Usage: {HEAD:1-5}
	 * Parameters: 1 to 5 to address the different available head fields in a layout object.
	 * Example: <h1>{HEAD:1}</h1>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_HEAD($value, $params) {

		switch ($this->mode) {
				
			case 'layout':
				$replaceData = $this->makeContentEditable(array(
					'objectID' => $this->obj_id,
					'elementType' => 'head',
					'elementNr' => $value,
					'content' => $this->protectMakros($this->removeTrailingBrTags($this->content_data['head'.$value])),
					'params' => (array)$params
				));
				
				break;
		
			case 'preview':
			// 2012-02-06: stripslashes entfernt
			// $replaceData = stripslashes($this->content_data['head'.$value]);
			$replaceData = $this->content_data['head'.$value];
		}
	
		if ($params[1]) {
			array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_TEXT()
	 *
	 * Type: Layout object macro
	 * Usage: {TEXT:1-5}
	 * Parameters: 1 to 5 to address the different available text fields in a layout object.
	 * Example: <div>{TEXT:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_TEXT($value, $params) {

		$replaceData = $this->makeContentEditable(array(
			'objectID' => $this->obj_id,
			'elementType' => 'text',
			'elementNr' => $value,
			'content' => $this->protectMakros($this->content_data['text'.$value]),
			'params' => (array)$params
		));

		
		if ($params[1]) {
			array_shift($params);
			$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
		}
		return $replaceData;
	}
	
	/**
	 * protected function macro_HTML()
	 *
	 * Type: Layout object macro
	 * Usage: {HTML:1}
	 * Parameters: 1 to address the available html field in a layout object.
	 * Example: <div>{HTML:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_HTML($value, $params) {

		$replaceData = $this->makeContentEditable(array(
			'elementType' => 'html',
			'elementNr' => $value,
			'content' => $this->protectMakros($this->content_data['html'.$value]),
			'params' => (array)$params
		));
		
		// add editor
		if (!$params[1]) {
			$editorType = 'cmt';
		} else {
			$editorType = $this->cmt->makeNameWebsave(trim($params[1]));
		}
		
		$replaceData .= $this->getTemplate('app_layout/tiny_mce_config_editor_'. $editorType .'.tpl');
	
		return $replaceData;
		
	}
	
	/**
	 * protected function macro_IMAGE()
	 *
	 * Type: Layout object macro
	 * Usage: {IMAGE:1-5:imgdir}
	 * Parameters: 1 to 5 to address the different available image fields in a layout object. Optional parameter root path
	 * Example: <div>{IMAGE:1}</div>
	 * Description: Must be wrapped in a block element when used in a template!
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_IMAGE($value, $params) {
	
		$attributes = array();
		$isPlaceholder = 0;
		$placeHolder = $this->parse($this->getTemplate('app_layout/cmt_image_placeholder.tpl'));

		$imageTag = trim($this->content_data['image'.$value]);
		if (!$imageTag) {
			$imageTag = $placeHolder;
			$isPlaceholder = 1;
		}
		
// 		preg_match_all('/([a-z]*)=["|\'](.*)["|\']/Uis', trim($imageTag), $match);
// 		$attributes = array_combine($match[1], $match[2]);

		$attributes = $this->getTagAttributes($imageTag);

		if (!is_file($this->parse($attributes['src']))) {
			$imageTag = $placeHolder;
			$isPlaceholder = 1;
			$attributes = $this->getTagAttributes($imageTag);
		}
		
		// standard attributes
		$standardAttributes = array(
			'src',
			'alt',
			'width',
			'height',
			'class'
		);
		$extractAttributes = array();
		
		foreach ($standardAttributes as $a) {		
			$extractAttributes[$a] = $attributes[$a];
			unset($attributes[$a]);
		}
		extract ($extractAttributes);

		// additional attributes
		$additionalAttributes = array();
		foreach ($attributes as $a) {
			$additionalAttributes[] = $a . '="'. $attributeValues[$a] . '"';
		}
		
		$imageSize = getimagesize($this->parse($src));

		$originalWidth = intval($imageSize[0]);
		$originalHeight = intval($imageSize[1]);
		
		if ($isPlaceholder) {
			$width = $originalWidth;
			$height = $originalHeight;
			$src = '/';
		}
		$replaceData = '
			<span class="cmt-element-wrapper" 
			data-element-type="image" 
			data-element-nr="' . intval($value) . '" 
			data-cmt-image-base-path="' . $params[0] . '"
			data-cmt-image-path="' . $src . '" 
			data-cmt-image-alt-text="' . $alt . '" 
			data-cmt-image-add-html = "' . htmlentities(implode(' ', $additionalAttributes)) . '"
			data-cmt-image-width="' . $width . '" 
			data-cmt-image-height="' . $height . '" 
			data-cmt-image-original-width="' . $originalWidth . '" 
			data-cmt-image-original-height="' . $originalHeight . '" 
			data-cmt-is-placeholder="' . $isPlaceholder . '" 
			data-image-class="cmt-image ' . $class . '">'
			. $imageTag . 
			'</span>';
	
		return $replaceData;
	}
	
	//public function getTagAttributes($tag, $filterTags=array()) {
	public function getTagAttributes($tag) {

//		preg_match_all('/([a-z]*)=["|\'](.*)["|\']/Uis', trim($tag), $matches);
		
//		$dontFilter = true;
// 		if (!is_array($filterTags)) {
// 			$filterTags = array();
// 		} else if (!empty($filterTags)) {
// 			$dontFilter = false;
// 		}
		
		$attributes = array();
		preg_match_all('/([a-z]*)=["|\'](.*)["|\']/Uis', trim($tag), $match);
		$attributes = array_combine($match[1], $match[2]);
		
// 		foreach ($matches[0] as $key => $match) {
			
// 			$attributeName = $matches[1][$key];
// 			$attributeValue = $matches[3][$key];
			
// 			$attributes[$attributeName] = trim($attributeValue);
			
// 			if ($dontFilter) {
// 				$attributes[$attributeName] = trim($attributeValue);
// 			} else if (in_array($attributeName, $filterTags)) {
// 				$attributes[$attributeName] = trim($attributeValue);
// 			}
//		}
		
		return $attributes;
	}
	
	/**
	 * protected function macro_SCRIPT()
	 *
	 * Type: Layout object macro
	 * Usage: {SCRIPT:1:rootPath}
	 * Parameters: 1 to address the available script field in a layout object. Optional parameter: root path
	 * Example: <div>{HTML:1:phpincludes}</div>
	 * Description: Includes a script file (e.g. PHP).
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_SCRIPT($value, $params) {
// 		$replaceData = '';
// 		$src_path = $this->format_directory(PATHTOWEBROOT.$params[0]);

// 		$cmtId = 'cmtContentElement_'.$this->obj_id.'_file'.$value;
// 		$this->contentHiddenFields[$cmtId] = $this->content_data['file'.$value];
		
		// avoid using class Dataformat!
		//$replaceData = $this->dformat->format_link($this->content_data['file'.$value], 'edit', $cmtId.'_select', '', $src_path, '', '', 1, 'Datei ausw&auml;hlen', 0, array('html' => ' onChange="document.forms[\'cmtStoreContent\'][\''.$cmtId.'\'].value = this.value;"'));

		$this->setParservar('cmtScriptPath', $this->content_data['file'.$value]);
		$this->setParservar('cmtScriptBasePath', $params[0]);

		$replaceData = 	$this->parseTemplate(CMT_TEMPLATE . 'app_layout/cmt_object_element_script.tpl');
//		$replaceData = '<input type="text" value="' . $this->content_data['file'.$value] .'" name="' . $cmtId . '_select" id="' . $cmtId . '" />';
//		$replaceData .= "<br />";

		return $replaceData;
	}
	
	/**
	 * protected function macro_LINK()
	 *
	 * Type: Layout object macro
	 * Usage: {LINK:linkID}
	 * Parameters: linkID, id of link in corresponding link table (eg. cmt_links_en)
	 * Example: {LINK:12}This text is linked{ENDLINK}
	 * Description: Wraps the link with the given id around the text.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_LINK($value, $params) {
		if (empty($this->page_links)) {
			$this->page_links = $this->getAllPageLinks();
		}
		$l = $this->page_links[$value];
// var_dump($l);
// die();
		$data = '';
		
		switch($l['cmt_type']) {

			case 'internal':
				$data = 'data-cmt-internal-page-id = "' . $l['cmt_page'] . '" 
						 data-cmt-internal-target = "' . $l['cmt_target'] . '" 
						 data-cmt-internal-language = "' . $l['cmt_lang'] .'"';
				break;
			
			case 'external':
				$data = 'data-cmt-external-url = "' . $l['cmt_url'] . '" 
						 data-cmt-external-target = "' . $l['cmt_target'] . '" ';
					break;
					
			case 'download':
				$data = 'data-cmt-download-url = "' . $l['cmt_url'] . '" 
						 data-cmt-download-target = "' . $l['cmt_target'] . '" ';
					break;
					
			case 'email':
			case 'mailto':	// due to compatibility reasons
				$data = 'data-cmt-email = "' . $l['cmt_url'] . '" ';
				break;
											
			case 'individual':
			case 'script':	// due to compatibility reasons
				$data = 'data-cmt-individual-url = "' . $l['cmt_url'] . '" 
						 data-cmt-individual-target = "' . $l['cmt_target'] . '" ';
				break;
		}
		
		$replaceData = '<span class="cmt-link-wrapper" 
						data-cmt-link-id="' . $l['id'] . '" 
						data-cmt-link-type="' . $l['cmt_type'] . '" 
						data-cmt-' . $l['cmt_type'] . '-add-html = "' . htmlentities($l['cmt_addhtml']) . '" '
						. $data .'>
						<a href="" ' . $l['cmt_addhtml'] . '>';

		return $replaceData;
		
		
// 		array (size=13)
// 		'id' => string '1' (length=1)
// 		'cmt_linkid' => string '1' (length=1)
// 		'cmt_type' => string 'internal' (length=8)
// 		'cmt_page' => string '84' (length=2)
// 		'cmt_url' => string 'http://www.buero-hahn.de' (length=24)
// 		'cmt_target' => string '_blank' (length=6)
// 		'cmt_addhtml' => string '' (length=0)
// 		'cmt_linkonpage' => string '9' (length=1)
// 		'cmt_created' => string '2015-05-24 13:25:41' (length=19)
// 		'cmt_createdby' => string '1' (length=1)
// 		'cmt_lastmodified' => string '2015-05-24 13:26:34' (length=19)
// 		'cmt_lastmodifiedby' => string '1' (length=1)
// 		'cmt_lang' => string 'de' (length=2)
		
	}
	
	/**
	 * protected function macro_ENDLINK()
	 *
	 * Type: Layout object macro
	 * Usage: {ENDLINK}
	 * Parameters: none
	 * Example: {LINK:12}This text is linked{ENDLINK}
	 * Description: Will be replaced by a simple </a> tag
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string
	 */
	protected function macro_ENDLINK($value, $params) {
		return '</a></span>';
	}
	
	/**
	 * protected function macro_LAYOUTMODE()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE}
	 * Parameters: none
	 * Example: {IF ({LAYOUTMODE})}<div class="message">Template is in layoutmode.</div>{ENDIF}
	 * Description: Returns 'true' or 'false' whether the page is displayed in the layout mode of the backend or in the frontend view.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string 'true'
	 */
	protected function macro_LAYOUTMODE($value, $params) {
		if ($this->mode == 'layout') {
			$replaceData = 'true';
		} else {
			$replaceData = 'false';
		}
		return $replaceData;
	}

	protected function macro_PREVIEWMODE($value, $params) {
// 		if ($this->mode == 'preview') {
// 			$replaceData = 'true';
// 		} else {
// 			$replaceData = 'false';
// 		}
		return '';
	}
	
	/**
	 * protected function macro_NAVIGATION()
	 *
	 * Type: Template macro
	 * Usage: {NAVIGATION:parameter:optional parameters}
	 * Parameters:	id => looped page id.
	 * 				title => page title,
	 * 				link => page URL,
	 * 				children => number of children,
	 * 				isancestor => is current page ancenstor of looped page (boolean),
	 * 				target => link target as entered in table cmt_page_(language) for looped page,
	 * 				position => position of looped page under the looped node.
	 * 				optional parameters for 'title' are possible
	 *
	 * Example: {NAVIGATION:title}
	 * Description: Works only in a {LOOP NAVIGATION()}-Loop!! Returns selected data of the current page in the loop.
	 *
	 * @param string $value Content
	 * @param array $params Parameters for method Parser::processMacroValue()
	 *
	 * @return string
	 */
	protected function macro_NAVIGATION($value, $params) {
	
		switch ($value) {
			case 'id':
				$replaceData = $this->navvars[$this->navlevel]['id'];
				break;
	
			case 'title':
				$replaceData = $this->navvars[$this->navlevel]['cmt_title'];
				if ($params[0]) {
					$replaceData = $this->processMacroValue(array_shift($params), $replaceData, $params);
				}
				break;
	
			case 'link':
				// Externer Link oder normaler
				if ($this->navvars[$this->navlevel]['cmt_type'] == 'link') {
					$replaceData = $this->navvars[$this->navlevel]['cmt_link'];
				} else {
					$replaceData = SELFURL.'&amp;cmtPageID='.$this->navvars[$this->navlevel]['id'].'&amp;&amp;cmtLanguage='.$this->navvars[$this->navlevel]['cmt_language'];
				}
				break;
	
			case 'children':
				$this->db->Query('SELECT COUNT(id) AS children FROM '.$this->pagesTable.' WHERE cmt_parentid = \''.$this->navvars[$this->navlevel]['id'].'\' AND cmt_showinnav = \'1\'');
	
				$r = $this->db->Get(MYSQLI_ASSOC);
				$replaceData = intval($r['children']);
				break;
	
			case 'isancestor':
				if ($this->navvars[$this->navlevel]['id'] == $this->pageId || $this->navvars[$this->navlevel]['id'] == $this->parentId) {
					$replaceData = 'true';
				} else {
					$replaceData = $this->get_ancestors($this->pageId, $this->navvars[$this->navlevel]['id']);
				}
				break;
	
			case 'target':
				if ($this->navvars[$this->navlevel]['cmt_link_target']) {
					$replaceData = ' target="'.$this->navvars[$this->navlevel]['cmt_link_target'].'" ';
				}
				break;
	
			case 'position':
				$replaceData = $this->navElementPosition;
				break;
		}
	
		return $replaceData;
	}
	
	/**
	 * protected function macro_LAYOUTMODE_STARTSCRIPT()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE_STARTSCRIPT}
	 * Parameters: none
	 * Example: <body>{LAYOUTMODE_STARTSCRIPT} ...
	 * Description: Includes neccessary scripts and HTML in layout mode of the backend. Place this macro allways directly after the opening <body> tag.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Internal scripts for layout mode in backend.
	 */
	protected function macro_LAYOUTMODE_STARTSCRIPT($value, $params) {
		
		$replaceData = $this->parseTemplate('app_layout/cmt_head.tpl');
		return $replaceData;
	}
	
	/**
	 * protected function macro_LAYOUTMODE_ENDSCRIPT()
	 *
	 * Type: Template macro
	 * Usage: {LAYOUTMODE_ENDSCRIPT}
	 * Parameters: none
	 * Example: ... {LAYOUTMODE_ENDSCRIPT}</body>
	 * Description: Includes neccessary scripts and HTML in layout mode of the backend. Place this macro allways directly before the closeing </body> tag.
	 *
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string Internal scripts for layout mode in backend.
	 */
	protected function macro_LAYOUTMODE_ENDSCRIPT($value, $params) {

		// create object select
		$objectTemplates = $this->objectTemplate->getList(array(
			'pageTemplateId' => $this->pageTemplateId
		));

		$selectObject = $this->form->select(array(
			'values' => array_keys($objectTemplates),
			'aliases' => array_values($objectTemplates),
			'optionsOnly' => true
		));
		
		// options for second template selection ("change template")
		$changeObject = $selectObject;
		
		$this->setParserVar('selectObject', $selectObject);
		$this->setParserVar('changeObject', $changeObject);
		$replaceData = $this->parseTemplate('app_layout/cmt_menu.tpl');

		return $replaceData;
	}

	/**
	 * protected function makeContentEditable()
	 * Wraps the layout element content and adds some CSS selectors and data to the wrapper.
	 * 
	 * @param array Parameters in an ssociative array:
	 * DEPRECATED: 'objectID' => number Object's id,
	 * 'elementType' => string Element's type, e.g. 'text', 'image'
	 * 'elementNr' => number Element's number, e.g. 1-5 for text or head elemnts ,
	 * 'content' => string HTML content of the element,
	 * 'params' => string Additional settings and parameters for the element
	 * 
	 * @see \Contentomat\Parser::makeContentEditable()
	 *
	 * @return string Parsed HTML content 
	 */
	protected function makeContentEditable($params) {
	
		if ($this->layoutModeRawData) {
			return $text;
		}
		
		switch ($params['params'][0]) {
			case 'character':
			case 'paragraph':
			case 'all':
				$menuType = 'extended';
				break;
				
			default:
				$menuType = '';
				break;
		}
		$replaceData = '<span class="cmt-element-wrapper" data-element-type="'. $params['elementType'] . '" data-element-nr="' . $params['elementNr'] . '" data-menu-type="' . $menuType . '">' . $params['content'] . '</span>';
		
		return $replaceData;	

	}

	public function makeHTMLEditorInitScript($params = array()) {
	
		$defaultParams = array(
			'editorType' => 'cmthtmleditor',
			'editorID' => '',
			'editorCSS' => '',
			'editorImagePath' => '',
			'editorCharset' => ''
		);
		$params = array_merge($defaultParams, $params);
	
		if (!$params['editorID']) {
			return '';
		}
	
		$editorTemplate =  $this->getTemplate('app_layout/tiny_mce_config_editor_'.$params['editorType'].'.tpl');
		$parser = new \Contentomat\Parser();
		$parser->setMultipleParserVars($params);
		return $parser->parse($editorTemplate);
	}
	
	/**
	 * public function protectMakros()
	 * Ersetz gechweifte Klammern durch nummerische Entitäten und "schützt" Makros so davor, ggf. ein zweiter Mal
	 * geparst zu werden. Ist z.B. bei Feldern sinnvoll, die zweimal geparst werden aber Makrobezeichnungen als Inhalt
	 * haben sollen.
	 * 
	 * @param String $str Zeichenkette
	 * @return String Gibt Zeichenkette mit ersetzen "{" und "}" zurück
	 * 
	 */
	public function protectMakros($str) {
		
		// some macros like LINK and ENDLINK need no protection and must be parsed also in layout mode! 
		$dontProtectMacros = 'LINK|ENDLINK';
		$str = preg_replace('/\{(' . $dontProtectMacros .')(:\d+)?\}/', '[[[[$1$2]]]]', $str);

		$str = str_replace(
			array( chr(123), chr(125)),
			array( '&#123;', '&#125;'),
			$str
		);
		
		return str_replace(
 			array( '[[[[', ']]]]'),
 			array( chr(123), chr(125)), 
			$str
		);
	}

	/**
	 * public function unprotectMakros()
	 * Gegenteil zu protectMakros: &#123; und &#125; werden durch { und } ersetzt
	 * 
	 * @param String $str Zeichenkette
	 * @return String Gibt Zeichenkette mit ersetzen Entitäten zurück
	 * 
	 */
	public function unprotectMakros($str) {
		return str_replace(
 			array( '&#123;', '&#125;', '&amp;#123;', '&amp;#125;'),
 			array( chr(123), chr(125), chr(123), chr(125)), 
			$str
		);
	}
	
	// DEPRECATED
// 	public function setMode($mode) {
// 		if ($mode == 'preview') {
// 			$this->mode = 'preview';
// 		} else {
// 			$this->mode = 'layout';
// 		}
// 	}
	
	// DEPRECATED
// 	public function getMode($mode) {
// 		return $this->mode;
// 	}

	/**
	 * public function getObjectTemplate()
	 * Gets the object's template and parses it into the frame of a layout mode object.
	 * 
	 * @param number $templateID
	 * @return string Parsed layout object frame template with contents.
	 */
	public function getObjectTemplate($templateID) {
		
		$r = $this->objectTemplate->getById($templateID);
		
		$this->setParserVar('cmt_objecttemplate', $r['id']);
		
		$this->setParserVar('objectContent', $this->parse(stripslashes($r['cmt_source'])));
		return $this->parse($this->frameTemplate);
	}

	/**
	 * public function removeTrailingBrTags()
	 * Helper: Removes "<br>" and "<br/>" tags at the end of a content string.
	 * 
	 * @param string $str	The content string (usually the content of a HEAD field)
	 * @return string		Cleaned string
	 */
	public function removeTrailingBrTags($str) {
		
		return preg_replace('/\<br\s?\/?\>$/i', '', $str);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Contentomat\Parser::getAllPageLinks()
	 */
	public function getAllPageLinks() {
		$this->db->Query("SELECT * FROM ".$this->linksTable." WHERE cmt_linkonpage = '" . $this->pageId . "'");
		return $this->db->getAll();
	}

	// TODO: Deprecated?
	public function setPageTemplateId($pageTemplateId) {
		$this->pageTemplateId = intval($pageTemplateId);
	}
}
?>
