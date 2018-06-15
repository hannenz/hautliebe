<?php
/**
 * class_authors.inc
 * Provides methods to get informations about the author of a mlog post.
 * 
 * @version 2016-09-21
 * @author A.Alkaisi, J.Hahn <info@contentomat.de>
 *
 */
namespace Contentomat\MLog;

use Contentomat\DBCex;

class Authors{
	protected $db;
	protected $authorsTable = 'mlog_authors';
	
	public function __construct() {
		$this->db = new DBCex();
	}
	
	/**
	 * public function getAuthor()
	 * Gets all data of an author.
	 *
	 * @param array $params associatve array:
	 *  - 'authorID' => number, database id of author
	 *  
	 * @return array Data of author or empty array.
	 */
	public function getAuthor($params){
		
		$authorId = intval($params['authorId']);
		
		// due to backwards compatibility
		if(!$authorId) {
			$authorId = intval($params['authorID']); 
		}
		
		if (!$authorId){
			return array();
		}
		
		$query = "SELECT * FROM " . $this->authorsTable . " WHERE id='" . $authorId . "'";
		$this->db->query($query);
		
		return $this->db->get();
		
	}
	
	/**
	 * function getAuthors()
	 *
	 */
	public function getAuthors(){
		
		$query = "SELECT * FROM ' . $this->authorsTable . ' WHERE id='" . $authorId . "' ORDER BY author_name ASC";
		$this->db->query($query);
		
		return $this->db->getAll();
	}
}


?>