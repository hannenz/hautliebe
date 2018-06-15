<?php 
/**
 * class Relations
 * @author AKA
 * @version 2010-02-26
 * 
 */

namespace Contentomat;

class Relations{
	public $isValid = false;
	protected $currentArticleRelations;
	protected $db;
	protected $pageTableWrapper;
	protected $date;
	public $tableTypWrapper;
	
	/**
	 * Konstruktor
	 */ 
	public function __construct($pid,$aid) {
		
		// return if no article id found
		if (!$pid ){
			return false;
		}
		$this->db = new DBCex();
		$this->date = date('Y-m-d H:i:s');
		
		// if article has relations 
		if ($this->getCurrentArticleRelations($pid,$aid)){
			$this->isValid = true;
		}
		
	}
	

	/**
	 * protected function getCurrentArticleRelations()
	 * return the content of "relations" field in article data
	 * 
	 * @param $pid
	 * @param $aid
	 * @return string, serialized list of relations
	 */
	protected function getCurrentArticleRelations($pid,$aid){
		// get static relations from page 
		$query = "SELECT cmt_relations FROM cmt_pages_de WHERE id = '".$pid."' ";
		$this->db->Query($query);
		$r = $this->db->get();
		if ($r['cmt_relations']){
			$this->currentArticleRelations[] =$r['cmt_relations'];
		}
		
		// get dynamic relations from content tables
		if ( $tableName = $this->pageTableWrapper[$pid]['cmt_tablename']){
			$query = "SELECT cmt_relations FROM ".$tableName." WHERE id = '".$aid."' AND  status='3' ";
			$this->db->Query($query);
			$r = $this->db->get();
			if ($r['cmt_relations']){
				$this->currentArticleRelations[] =$r['cmt_relations'];
			}
		}	
	
		if ($this->currentArticleRelations){
			return true;
		}
		return false;
	}
	
	/**
	 * protected function getTableName()
	 * 
	 * welche tablele geh�rt welche Seite
	 * 
	 * @param $pid
	 * @return string, table name
	 */
	protected function getTableName($pid){
		if (!isset($this->pageTableWrapper[$pid])){
			return false;
		}
		$this->db->Query("SELECT cmt_tablename FROM cmt_tables WHERE id='".$this->pageTableWrapper[$pid]."' ");
		$r = $this->db->get();
		if (!$r['cmt_tablename']){
			return false;
		}
		return $r['cmt_tablename'];
	}
	
	/**
	 * public function showRelations()
	 * 
	 * @param $params
	 * @return html content
	 */
	public function getRelations(){
		
		if (!$this->currentArticleRelations) {
			return false;
		}

		$relatedLinks = $this->relatedLinksData($this->currentArticleRelations);

		$content = array();

		// main loop			
		foreach ($relatedLinks as $link){
			//ignore if no article id found
			if (!$link['id']) continue;

			
			// give article a type accourding to wrapper array list $this->tableTypWrapper 
			if (isset($this->tableTypWrapper[$link['table']])){
				$link['type']=$this->tableTypWrapper[$link['table']];
				
			}	
			// any other tables take type txt if they are not in wrapper array
			else {
				$link['type']='text';
			}
			$content[]=$link;
		}
		return $content;
	}
	
		
	/**
	 * function relatedLinksData()
	 * 
	 * @param list of article with table id
	 * @return array of article and content
	 */
	protected function relatedLinksData($relations){
		$relatedLinkList=array();
		$linkList = array();
		foreach($relations as $relationGroup){
			$linkList = array_merge($linkList,$this->safeUnserialize($relationGroup));
		}
		
		
		foreach($linkList as $link){
			$relatedLinkList[]=array("tableId"=>$link[0],"articleId"=>$link[1]);
		}
		$relatedLinks=array();
		$whereClause = " AND status = '3' AND online_date <= '".$this->date."' AND (offline_date >= '".$this->date."' OR offline_date = '0000-00-00 00:00:00')";
		foreach ($relatedLinkList as $linkItem){
			$this->db->query("SELECT cmt_tablename FROM cmt_tables WHERE id='".$linkItem['tableId']."' ");
			$tableName = $this->db->get();
			$query = "select * from ".$tableName['cmt_tablename']." where id='".$linkItem['articleId']."' ".$whereClause;
			$this->db->query($query);
			$r =$this->db->get();
			$r['table']=$linkItem['tableId'];
			$r['tableName']=$tableName['cmt_tablename'];
			$relatedLinks[]=$r;
		}
		return $relatedLinks;
	}
	
	//////////////////////////////////////////////////////////////
	// -> sicheres entserialisieren: Auch " und ; gehen
	//////////////////////////////////////////////////////////////
	
	function safeUnserialize ($var) {
		$var = unserialize($var);
		if (is_array($var)) {
			$var = safeUnserializeArray($var);
		} else {
			$var = safeUnserializeCleanUp($var);
		}
		return $var;
	}	
}

?>
