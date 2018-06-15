<?php
/**
 * Class FeedTag
 * 
 * @version 2016-09-21
 * @author A.Alkaissi <info@buero-hahn.de>
 * 
 * OUTDATED???
 *
 */

namespace Contentomat\Feed;

class FeedTag {

	/**
	 *
	 * @var type 
	 */
	public $name;

	/**
	 *
	 * @var type 
	 */
	public $content;

	/**
	 *
	 * @var type 
	 */
	public $attributes = array();

	/**
	 * function __construct()
	 * 
	 * @param type $name
	 * @param type $content
	 * @param type $attributes 
	 */
	function __construct($name, $content, $attributes = array()) {
		$this->name = $name;
		$this->content = $content;
		$this->attributes = is_array($attributes) ? $attributes : array();
	}

}