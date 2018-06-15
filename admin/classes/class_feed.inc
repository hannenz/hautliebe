<?php

/**
 * class_feed.inc
 * Gruppe diverser Feed-klassen: Erstellen RSS1, RSS2 und ATOM Feeds 
 * 
 * 1- class Feed			Generate RSS1, RSS2 and ATOM Feeds
 * 2- class FeedItem			Class for the Feed elements
 * 3- class FeedTag				Class for the Feed items
 * 4- class FeedReader			Read an exesiting Feed in an array
 *
 * @version 2016-09-21
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */

namespace Contentomat\Feed;

use Contentomat\DBCex;
use Contentomat\ApplicationHandler;
use Contentomat\Parser;

class Feed {

	/**
	 * supported Feed formats
	 * @var array $supportedFeedTypes
	 */
	private $supportedFeedTypes;

	/**
	 * all elements to generate the feed
	 * @var array $feedElements
	 */
	private $feedElements;

	/**
	 * Type of Feed Format 
	 * @var string $type 
	 */
	private $type;  // type of feed format
	/**
	 * Holds error messages
	 * @var array $errorMessages
	 */
	private $errorMessages;

	/**
	 * Holds stylesheet associated to the feed
	 * @var string http ://www.w3.org/TR/xml-stylesheet/#dt-xml-stylesheet
	 */
	private $stylesheets = array();

	protected $feedsTableName = 'cmt_rssfeeds';
	
	protected $settings = array();
	protected $db;
	protected $parser;

	/**
	 * public function __construct()
	 * 
	 * Constructor
	 * @param string $type, default feed type is RSS2
	 */
	public function __construct() {

		$this->supportedFeedTypes = array('RSS1', 'RSS2', 'ATOM');
		$this->type = 'RSS2';
		
		$this->db = new DBCex();
		$this->parser = new Parser();
		
		$this->type = $type;
		$this->feedElements = array();

		//default values for channel
		$this->addChannelTag('title', $this->type . 'Feed');
//		$this->addChannelTag('link', 'http://www.buero-hahn.de');

		//Tag elements that we need to CDATA encode
		$this->feedElements['CDATAEncoded'] = array('description', 'content:encoded', 'summary');
	}
	
	public function setFeedType($feedType) {

		if (!in_array($feedType, $this->supportedFeedTypes)) {
			$this->errorMessages[] = 'Feed version no supported';
		} else {
			$this->type = $type;
			$this->feedElements = array();
		
			//default values for channel
			$this->addChannelTag('title', $this->type . 'Feed');
			$this->addChannelTag('link', 'http://www.buero-hahn.de');
		
			//Tag elements that we need to CDATA encode
			$this->feedElements['CDATAEncoded'] = array('description', 'content:encoded', 'summary');
		}
		
	}

	public function setFeed($feedTitle) {
		
		$feedTitle = trim($feedTitle);
		
//		$this->getFeedSettings($feedTitle);
	}
	
	public function getFeedsForTable($tableName) {
		
		$applicationHandler = new ApplicationHandler();
		$tableData = $applicationHandler->getApplicationByTablename($tableName);
		
		$tableId = $tableData['id'];
		
		$this->db->query("SELECT * FROM ". $this->feedsTableName . " WHERE feed_table_id = '" . intval($tableId) . "'");
		return $this->db->getAll();
	}

	public function createFeed($params) {
	
		$this->parser->setParserVar('items', (array)$params['items']);
		$this->parser->setMultipleParserVars((array)$params['feedVars']);

		return $this->parser->parseTemplate($params['templatePath']);
	}
	
	public function convertDate($date) {
		return date('r', strtotime($date));
	}
// -----------
	/**
	 * Get list of error messages
	 * @return string 
	 */
	public function getErrorMessages() {
		$errorMessages = '';
		foreach ($this->errorMessages as $index => $error) {
			$errorMessages .= "[Error:" . $index . '] ' . $error . "\n<br />";
		}
		return $errorMessages;
	}
	
	/**
	 * public function addChannelTag()
	 * 
	 * Adds a channel element
	 * @param string $tag
	 * @param string $content 
	 */
	public function addChannelTag($tag, $content) {
		if (!array_key_exists('channels', $this->feedElements)) {
			$this->feedElements['channels'] = array();
		}

		$this->feedElements['channels'][$tag] = $content;
	}

	/**
	 * public function addChannelTagsArray()
	 * 
	 * Adds an array of  channel elements
	 * @param array $tags 
	 */
	public function addChannelTagsArray($tags) {
		if (is_array($tags)) {
			foreach ($tags as $tag => $content) {
				$this->addChannelTag($tag, $content);
			}
		}
	}

	/**
	 * public function addStylesheetTag()
	 * 
	 * Add stylesheet support to the feed
	 * @param array $htmlOptions 
	 * @return void
	 */
	public function addStylesheetTag($htmlOptions) {
		if (is_array($htmlOptions)) {
			$this->stylesheets[] = '<?xml-stylesheet ' . $this->renderAttributes($htmlOptions) . ' ?>';
		}
	}

	/**
	 * public function setTitle()
	 * 
	 * Property setter of the 'title' channel element
	 * @param string $title 
	 * @return void
	 */
	public function setTitle($title) {
		$this->addChannelTag('title', $title);
	}

	/**
	 * public function getTitle()
	 * 
	 * Property getter 'title'
	 * @return string 
	 */
	public function getTitle() {
		if (array_key_exists('channels', $this->feedElements)) {
			return $this->feedElements['channels']['title'];
		}
		return null;
	}

	/**
	 * public function setDescription()
	 * 
	 * Property setter 'description' channel element
	 * @param string $description 
	 */
	public function setDescription($description) {
		$this->addChannelTag('description', $description);
	}

	/**
	 * public function getDescription() 
	 * 
	 * Property getter 'description'
	 * @return string 
	 */
	public function getDescription() {
		if (array_key_exists('channels', $this->feedElements)) {
			return $this->feedElements['channels']['description'];
		}
		return null;
	}

	/**
	 * public function setLink()
	 * 
	 * Property setter 'link' channel element
	 * @param string $link 
	 */
	public function setLink($link) {
		$this->addChannelTag('link', $link);
	}

	/**
	 * public function getLink()
	 * 
	 * Property getter 'link'
	 * @return string 
	 */
	public function getLink() {
		if (array_key_exists('channels', $this->feedElements)) {
			return $this->feedElements['channels']['link'];
		}
		return null;
	}

	/**
	 * public function setImage()
	 * 
	 * Set the 'image' channel element
	 * 
	 * @param string $title of image, must be as the same as channel title
	 * @param string $link of image
	 * @param string $url  of image
	 */
	public function setImage($title, $link, $url) {
		$this->addChannelTag('image', array('title' => $title, 'link' => $link, 'url' => $url));
	}

	/**
	 * public function getImage() 
	 * 
	 * Property getter image
	 * @return array 
	 */
	public function getImage() {
		if (array_key_exists('channels', $this->feedElements)) {
			return $this->feedElements['channels']['image'];
		}
		return null;
	}

	/**
	 * public function setRSS1ChannelAbout()
	 * 
	 * Property setter the 'about' RSS 1.0 channel element
	 * @param srting $url 
	 */
	public function setRSS1ChannelAbout($url) {
		$this->addChannelTag('ChannelAbout', $url);
	}

	/**
	 * public function getRSS1ChannelAbout()
	 * 
	 * Property getter the 'about' RSS 1.0 channel
	 * @return string 
	 */
	public function getRSS1ChannelAbout() {
		if (array_key_exists('channels', $this->feedElements)) {
			return $this->feedElements['channels']['ChannelAbout'];
		}
		return null;
	}

	/**
	 * public function createNewItem()
	 * 
	 * create an instance of class item
	 * @return item class instance
	 */
	public function createNewItem() {
		return new FeedItem($this->type);
	}

	/**
	 * public function addItem()
	 * 
	 * Add item object to feedElements container
	 * @param object $item , an instance of class FeedItem
	 */
	public function addItem(FeedItem $item) {
		if (!array_key_exists('items', $this->feedElements)) {
			$this->feedElements['items'] = array();
		}

		$this->feedElements['items'][] = $item;
	}

	/**
	 * public function work()
	 * 
	 * Generate Feed parts
	 * @return void 
	 */
	public function work() {
		$feedContent = $this->renderHead();
		$feedContent .=$this->renderChannels();
		$feedContent .=$this->renderItems();
		$feedContent .=$this->renderBottom();
		return $feedContent;
	}

	/**
	 * public function generateFeed()
	 * 
	 * Generate Feed as page
	 * @return void
	 */
	public function generateFeed() {
		header("Content-type: text/xml");
		echo $this->work();
	}

	/**
	 * public function getFeedAsString()
	 * 
	 * Generate and return feed as string
	 * @return string, Feed in XML Format
	 */
	public function getFeedAsString() {
		return $this->work();
	}

	/**
	 * public function exportFeed()
	 * 
	 * generate and save feed in file
	 * @param string $exportPath, path where feed will save
	 * return void
	 */
	public function saveFeed($file = '') {
		if (!$file) {
			$this->errorMessages[] = "Missing File name/path to save feed ";
			return false;
		}
		file_put_contents($file, $this->work());
	}

	/**
	 * private function renderHead()
	 * 
	 * Prints Feed Head, xml and namespaces
	 *  @return void
	 */
	private function renderHead() {
		$head = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		if (!empty($this->stylesheets))
			$head .= implode(PHP_EOL, $this->stylesheets);

		if ($this->type == 'RSS2') {
			$head .= $this->openTag('rss', array(
						"version" => "2.0",
						"xmlns:content" => "http://purl.org/rss/1.0/modules/content/",
						"xmlns:atom" => "http://www.w3.org/2005/Atom",
						"xmlns:wfw" => "http://wellformedweb.org/CommentAPI/")) . PHP_EOL;
		} elseif ($this->type == 'RSS1') {
			$head .= $this->openTag('rdf:RDF', array(
						"xmlns:rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
						"xmlns" => "http://purl.org/rss/1.0/",
						"xmlns:dc" => "http://purl.org/dc/elements/1.1/"
					)) . PHP_EOL;
		} else if ($this->type == 'Atom') {
			$head .= $this->openTag('feed', array("xmlns" => "http://www.w3.org/2005/Atom")) . PHP_EOL;
		}
		return $head;
	}

	/**
	 * private function renderBottom()
	 * 
	 * Prints xml closing tags
	 * @return void
	 */
	private function renderBottom() {
		$bottom = '';
		if ($this->type == 'RSS2') {
			$bottom .= $this->closeTag('channel');
			$bottom .= $this->closeTag('rss');
		} elseif ($this->type == 'RSS1') {
			$bottom .= $this->closeTag('rdf:RDF');
		} else if ($this->type == 'Atom') {
			$bottom .= $this->closeTag('feed');
		}
		return $bottom;
	}

	/**
	 * private function renderChannels()
	 * 
	 * Prints the channels of the xml document
	 */
	private function renderChannels() {
		$channels = '';
		switch ($this->type) {
			case 'RSS2':
				$channels .= '<channel>' . PHP_EOL;
				break;
			case 'RSS1':
				if ($this->RSS1ChannelAbout)
					$channels .= $this->tag('channel', array('rdf:about' => $this->RSS1ChannelAbout));
				else
					$channels .= $this->tag('channel', array('rdf:about' => $this->link));
				break;
		}

		// Printing channel items
		foreach ($this->feedElements['channels'] as $key => $value) {
			if ($this->type == 'Atom' && $key == 'link') {
				// ATOM prints link element as href attribute
				$channels .= $this->makeNode($key, '', array('href' => $value));
				// And add the id for ATOM
				$channels .= $this->makeNode('id', $this->uuid($value, 'urn:uuid:'));
			} else if ($key == 'atom:link') {
				$channels .= $this->tag($key, array('href' => $value, 'rel' => "self", 'type' => "application/rss+xml"), false, true) . PHP_EOL;
			} else {
				$channels .= $this->makeNode($key, $value);
			}
		}

		// RSS 1.0 have special tag <rdf:Seq> with channel 
		if ($this->type == 'RSS1') {
			if ($this->feedElements['items']) {
				$channels .= "<items>" . PHP_EOL . "<rdf:Seq>" . PHP_EOL;

				foreach ($this->feedElements['items'] as $item) {
					$tag = $item->link;

					if ($tag) {
						$channels .= $this->tag('rdf:li', array('resource' => $tag->content), true) . PHP_EOL;
					}
				}
				$channels .= "</rdf:Seq>" . PHP_EOL . "</items>" . PHP_EOL;
			}
		}
		return $channels;
	}

	/**
	 * private function renderItems()
	 * 
	 * Prints feed items
	 * @return void
	 */
	private function renderItems() {
		$items = '';
		if ($this->feedElements['items']) {
			foreach ($this->feedElements['items'] as $item)
				$items .= $item->getNode();
		}
		return $items;
	}

	/**
	 * private function makeNode()
	 * 
	 * Creates a single node as xml format
	 * 
	 * @param string, name of the tag
	 * @param mixed, tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	 * @param array, Attributes(if any) in 'attrName' => 'attrValue' format
	 * @return string, formatted xml tag
	 */
	private function makeNode($tagName, $tagContent, $attributes = array()) {
		$node = '';

		if (is_array($tagContent) && $this->type == 'RSS1')
			$attributes['rdf:parseType'] = "Resource";

		if (in_array($tagName, $this->feedElements['CDATAEncoded'])) {
			if ($this->type == 'ATOM')
				$attributes['type'] = "html";
			$node .= $this->openTag($tagName, $attributes) . '<![CDATA[';
		}
		else
			$node .= $this->openTag($tagName, $attributes);

		if (is_array($tagContent)) {
			foreach ($tagContent as $tag => $content)
				$node .= $this->makeNode($tag, $content);
		}
		else
			$node .= in_array($tagName, $this->feedElements['CDATAEncoded']) ? $tagContent : $this->encode($tagContent);

		$node .= in_array($tagName, $this->feedElements['CDATAEncoded']) ? PHP_EOL . ']]>' : '';

		$node .= $this->closeTag($tagName);

		return $node . PHP_EOL;
	}

	/**
	 * 
	 * 
	 * 
	 * 			OTHER METHODS
	 * 
	 * 
	 * 
	 */
	
		private $dateTags       = array('UPDATED','PUBDATE','DC:DATE');  
	private $multiValueDataTags = array('CATEGORY');
	private $itemTags       = array('ITEM','ENTRY');    // List of tag names which holds a feed item
	private $channelTags    = array('CHANNEL','FEED');  // List of tag names which holds all channel elements
	private $hasSubTags     = array('IMAGE','AUTHOR');  // List of tag names which have sub tags


	
	public function readFeed($url) {
		$xmlParser = xml_parser_create();

		// if no url
		if (!$url || trim($url == '')) {
			$this->errorMessages[] = "URL to parse is empty!.";
			return false;
		}

		$URLContent = $this->getUrlContent($url);

		if ($URLContent) {
			xml_parse_into_struct($xmlParser, $URLContent, $valueals, $itemIndex);
			xml_parser_free($xmlParser);
		} else {
			$this->errorMessages[] = 'Sorry! cannot load the feed url.';
		}
		$feedContent = $this->characterData($valueals);
		
		$this->praseFeedContent($feedContent);
		return $feedContent;
	}
	
	
	private function praseFeedContent($feedContent){
		$this->feedElements = array();
		
		if(!$feedContent){
			return null;
		}
		
		
		$channels = $feedContent['RSS']['CHANNEL'];
		$items = $feedContent['RSS']['CHANNEL']['ITEM'];
		
		if($channels){
			foreach($channels as $channelTag=>$channelValue){
				echo $channelTag.'<br />';
				if(in_array($channelTag,$this->itemTags)){
					continue;
				}
				if($channelTag == 'IMAGE'){
					foreach($channelValue as $image){
						$this->setImage($image['TITLE'],$image['LINK'],$image['URL']);
					}
				}else{
					$this->addChannelTag(strtolower($channelTag), $channelValue);
				}
						
			}
			
			if($this->feedElements['channels']['atom:link']===null){
				$this->addChannelTag('atom:link', $this->feedElements['channels']['link']);
			}
		}
		
		if($items){
			
		}
		
		
	}

	/**
	 * Load the whole contents of a RSS/ATOM page
	 * 
	 * @access   private
	 * @return   string
	 */
	private function getUrlContent($url) {
		// if no url
		if (!$url || trim($url == '')) {
			$this->errorMessages[] = "URL to parse is empty!.";
			return false;
		}

		// if url is local file
		if ($content = @file_get_contents($url)) {
			return $content;
		}
		// if url is online path, getting it with curl
		else {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$content = curl_exec($ch);
			$error = curl_error($ch);

			curl_close($ch);

			if (empty($error)) {
				return $content;
			} else {
				$this->errorMessages[] = "Erroe occured while loading url by cURL. <br />\n" . $error;
				return false;
			}
		}
	}

	/**
	 * Handle character data of a tag while parsing
	 * 
	 * @access   private
	 * @param    object  the xmlParser object
	 * @param    string  tag value
	 * @return   void
	 */
	private function characterData($valueals) {
		foreach ($valueals as $keyey => $valueal) {
			if ($valueal['type'] != 'cdata') {
				$item[$keyey] = $valueal;
			}
		}

		$i = 0;

		foreach ($item as $key => $value) {
			if ($value['type'] == 'open') {

				$i++;
				$itemame[$i] = $value['tag'];
			} elseif ($value['type'] == 'close') {

				$feed = $values[$i];
				$item = $itemame[$i];
				unset($values[$i]);
				$i--;

				if (count($values[$i]) > 1) {
					$values[$i][$item][] = $feed;
				} else {
					$values[$i][$item] = $feed;
				}
			} else {
				if (in_array($value['tag'], $this->dateTags)) {
					$tagValue = strtotime($value['value']);
				} else {
					$tagValue = $value['value'];
				}

				if (in_array($value['tag'], $this->multiValueDataTags)) {
					$values[$i][$value['tag']][] = $tagValue;
				} else {
					$values[$i][$value['tag']] = $tagValue;
				}
			}
		}

		return $values[0];
		/*
		$feedContent = $values[0];
		$items = $this->xmlContent['RSS']['CHANNEL']['ITEM'];
		$this->channels = $this->xmlContent['RSS']['CHANNEL'];
		 * 
		 */
	}

	/**
	 * public function tag()
	 * 
	 * Generates tag XHTML element
	 * 
	 * @param string $tag, tag name
	 * @param array $htmlOptions, tag attributes
	 * @param mixed $content, in line content of tag
	 * @param bool $closeTag, create close tag
	 * @return string 
	 */
	public function tag($tag, $htmlOptions = array(), $content = false, $closeTag = true) {
		$html = '<' . $tag . self::renderAttributes($htmlOptions);
		if ($content === false)
			return $closeTag ? $html . ' />' : $html . '>';
		else
			return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
	}

	/**
	 * public function openTag()
	 * 
	 * Generates open HTML element
	 * 
	 * @param string $tag
	 * @param mixed $htmlOptions
	 * @return string 
	 */
	public function openTag($tag, $htmlOptions = array()) {
		return '<' . $tag . self::renderAttributes($htmlOptions) . '>';
	}

	/**
	 * public function closeTag()
	 * 
	 * Generates a close HTML element.
	 * @param string $tag the tag name
	 * @return string the generated HTML element tag
	 */
	public function closeTag($tag) {
		return '</' . $tag . '>';
	}

	/**
	 * public function renderAttributes()
	 * 
	 * generate html tags attributs
	 * 
	 * @param array $htmlOptions
	 * @return string 
	 */
	public function renderAttributes($htmlOptions) {
		$specialAttributes = array(
			'checked' => 1,
			'declare' => 1,
			'defer' => 1,
			'disabled' => 1,
			'ismap' => 1,
			'multiple' => 1,
			'nohref' => 1,
			'noresize' => 1,
			'readonly' => 1,
			'selected' => 1,
		);

		if ($htmlOptions === array())
			return '';
		$html = '';
		if (isset($htmlOptions['encode'])) {
			$raw = !$htmlOptions['encode'];
			unset($htmlOptions['encode']);
		}
		else
			$raw = false;


		if ($raw) {
			foreach ($htmlOptions as $name => $value) {
				if (isset($specialAttributes[$name])) {
					if ($value)
						$html .= ' ' . $name . '="' . $name . '"';
				}
				else if ($value !== null)
					$html .= ' ' . $name . '="' . $value . '"';
			}
		}
		else {
			foreach ($htmlOptions as $name => $value) {

				if (isset($specialAttributes[$name])) {
					if ($value)
						$html .= ' ' . $name . '="' . $name . '"';
				}
				else if ($value !== null)
					$html .= ' ' . $name . '="' . self::encode($value) . '"';
			}
		}
		return $html;
	}

	/**
	 * public function encode()
	 * 
	 * encode string in UTF-8 Format
	 * @param string $text
	 * @return string 
	 */
	public function encode($text) {
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * public static function uuid()
	 * 
	 * Generates an UUID
	 * 
	 * @param      string  an optional prefix
	 * @return     string  the formated uuid
	 */
	public static function uuid($key = null, $prefix = '') {
		$key = ($key == null) ? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid = substr($chars, 0, 8) . '-';
		$uuid .= substr($chars, 8, 4) . '-';
		$uuid .= substr($chars, 12, 4) . '-';
		$uuid .= substr($chars, 16, 4) . '-';
		$uuid .= substr($chars, 20, 12);

		return $prefix . $uuid;
	}

	/**
	 * public function __get()
	 * 
	 * PHP magic method __get 
	 * @param type $name
	 * @return type 
	 */
	public function __get($name) {
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
	}

	/**
	 * public function __set()
	 * 
	 * PHP magic method __set
	 * @param type $name
	 * @param type $value
	 * @return type 
	 */
	public function __set($name, $value) {
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			return $this->$setter($value);
	}

}
