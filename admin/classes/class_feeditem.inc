<?php
/**
 * Class FeedItem
 * 
 * Class for the Feed elements
 *
 * @version 2016-09-21
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */

namespace Contentomat\Feed;

class FeedItem {

	/**
	 *
	 * @var type 
	 */
	protected $tags;

	/**
	 *
	 * @var type 
	 */
	protected $type;

	/**
	 *
	 * @var type 
	 */
	protected $CDATAEncoded = array('description', 'content:encoded', 'summary');

	/**
	 * public function __construct()
	 * 
	 *
	 * @param type $type 
	 */
	public function __construct($type = 'RSS2') {
		$this->tags = array();
		$this->type = $type;
	}

	/**
	 * public function addTag()
	 * 
	 * @param type $tag
	 * @param type $content
	 * @param type $attributes 
	 */
	public function addTag($tag, $content, $attributes = array()) {
		$this->tags[] = new FeedTag($tag, $content, (!is_array($attributes) ? array() : $attributes));
	}

	/**
	 * public function getTag()
	 * 
	 * @param type $name
	 * @return type 
	 */
	public function getTag($name) {
		return $this->tags[$name];
	}

	/**
	 * public function setTitle()
	 * 
	 *
	 * @param type $title 
	 */
	public function setTitle($title) {
		$this->addTag('title', $title);
	}

	/**
	 * public function getTitle()
	 * 
	 * @return type 
	 */
	public function getTitle() {

		return $this->tags['title'];
	}

	/**
	 * public function setDescription()
	 * 
	 * @param type $description 
	 */
	public function setDescription($description) {
		switch ($this->type) {
			case 'RSS1':
			case 'RSS2':
				$this->addTag('description', $description);
				break;
			case 'ATOM':
				$this->addTag('summary', $description);
				break;
		}
	}

	/**
	 * public function getDescription()
	 * 
	 * @return type 
	 */
	public function getDescription() {
		switch ($this->type) {
			case 'RSS1':
			case 'RSS2':
				return $this->tags['description'];
				break;
			case 'ATOM':
				return $this->tags['summary'];
				break;
		}
	}

	/**
	 * public function setLink()
	 * 
	 * @param type $link 
	 */
	public function setLink($link) {
		switch ($this->type) {
			case 'RSS1':
			case 'RSS2':
				$this->addTag('link', $link);
				break;
			case 'ATOM':
				$this->addTag('link', '', array('href' => $link));
				$this->addTag('id', Feed::uuid($link, 'urn:uuid:'));
				break;
		}
	}

	/**
	 * public function getLink()
	 *
	 * @return type 
	 */
	public function getLink() {
		return $this->tags['link'];
	}

	/**
	 * public function setDate()
	 * 
	 * @param type $date 
	 */
	public function setDate($date) {
		switch ($this->type) {
			case 'RSS1':
				if (!is_numeric($date))
					$date = strtotime($date);
				$date = date("Y-m-d", $date);
				$this->addTag('dc:date', $date);
				break;
			case 'RSS2':
				if (!is_numeric($date))
					$date = strtotime($date);
				$date = date(DATE_RSS, $date);
				$this->addTag('pubDate', $date);
				break;
			case 'ATOM':
				if (!is_numeric($date))
					$date = strtotime($date);
				$date = date(DATE_ATOM, $date);
				$this->addTag('updated', $date);
				break;
		}
	}

	/**
	 * public function getDate()
	 * 
	 * @return type 
	 */
	public function getDate() {
		switch ($this->type) {
			case 'RSS1':
				return $this->tags['dc:date'];
				break;
			case 'RSS2':
				return $this->tags['pubDate'];
				break;
			case 'ATOM':
				return $this->tags['updated'];
				break;
		}
	}

	
	public function setCategory($categories){
		if(!$categories){
			return;
		}

		if(!is_array($categories)){
			$categories = explode(",",$categories);
		}
		$rssCategories = array();
		foreach($categories as $category){
			$this->addTag('category', $category);
		}
	}
	
	public function getCategory(){
		return $this->tags['category'];
	}

	/**
	 * public function setEncloser()
	 * 
	 * @param type $url
	 * @param type $length
	 * @param type $type 
	 */
	public function setEncloser($url, $length, $type) {
		$attributes = array('url' => $url, 'length' => $length, 'type' => $type);

		$this->addTag('enclosure', '', $attributes);
	}

	/**
	 * public function getNode()
	 *
	 * @return null|string 
	 */
	public function getNode() {
		switch ($this->type) {
			case 'RSS1':
				if ($this->link && $this->link->content) {
					$node = Feed::openTag('item', array('rdf:about' => $this->link->content)) . PHP_EOL;
					foreach ($this->tags as $tag) {
						$node .= $this->getElement($tag);
					}
					$node .= Feed::closeTag('item');

					return $node . PHP_EOL;
				}

				return null;

				break;
			case 'RSS2':
				$node = Feed::openTag('item') . PHP_EOL;
				foreach ($this->tags as $tag) {
					$node .= $this->getElement($tag);
				}
				$node .= Feed::closeTag('item');
				return $node . PHP_EOL;
				break;
			case 'ATOM':
				$node = Feed::openTag('entry') . PHP_EOL;

				foreach ($this->tags as $tag) {
					$node .= $this->getElement($tag);
				}

				$node .= Feed::closeTag('entry') . PHP_EOL;

				return $node;
				break;
		}
	}

	/**
	 * private function getElement()
	 *
	 * @param FeedTag $tag
	 * @return string 
	 */
	private function getElement(FeedTag $tag) {
		$element = '';
		if (is_array($tag->content) && $this->type == 'RSS1') {
			$tag->attributes['rdf:parseType'] = "Resource";
		}

		if (in_array($tag->name, $this->CDATAEncoded)) {
			$element .= Feed::openTag($tag->name, $tag->attributes);
			$element .= '<![CDATA[';
		} else {
			$element .= Feed::openTag($tag->name, $tag->attributes);
		}
		//$element .= PHP_EOL;

		if (is_array($tag->content)) {
			foreach ($tag->content as $tag => $content) {
				$tmpTag = new FeedTag($tag, $content);

				$element .= $this->getElement($tmpTag);
			}
		} else {
			$element .= (in_array($tag->name, $this->CDATAEncoded)) ? $tag->content : Feed::encode($tag->content);
		}

//		$element .= (in_array($tag->name, $this->CDATAEncoded)) ? PHP_EOL . ']]>' : "";
		$element .= (in_array($tag->name, $this->CDATAEncoded)) ? ']]>' : "";

		$element .= Feed::closeTag($tag->name) . PHP_EOL;

		return $element;
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
