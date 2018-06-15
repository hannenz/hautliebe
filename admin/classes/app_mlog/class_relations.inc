<?php 
/**
 * class Relations
 * @author AKA
 * @version 2010-02-26
 * 
 */
namespace Contentomat\MLog;

use Contentomat\DBCex;
use Contentomat\Contentomat;

class Relations {
	
	public $isValid = false;
	protected $db;
	protected $cmt;
	protected $pageTableWrapper;
	protected $date;
	public $tableTypeWrapper;
	
	/**
	 * Konstruktor
	 */ 
	public function __construct() {
		
		$this->db = new DBCex();
		$this->cmt = Contentomat::getContentomat();
		$this->date = date('Y-m-d H:i:s');
	}
	

	
	/**
	 * protected function getArticleRelations()
	 * return the content of "relations" field in article data
	 * 
	 * @param $pid
	 * @param $aid
	 * @return string, serialized list of relations
	 */
	protected function getArticleRelations($pid, $aid, $tableName='mlog_posts', $relationsField='post_relations'){
		
		if (!$pid || !$aid){
			return array();
		}
		
		$articleRelations = array();

		// get dynamic relations from content tables
		if ( $tableName){
			$query = "SELECT ". $this->db->dbQuote($relationsField) . " FROM ". $this->db->dbQuote($tableName) ." WHERE id = '" . intval($aid) . "'  ";
			$this->db->query($query);
			$r = $this->db->get();
			
			if ($r[$relationsField]){
				$articleRelations[] = $r[$relationsField];
			}
		}
		return $articleRelations;
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
			return '';
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
	 * @param $relationsSource, [table, page, '' (both)]
	 * @param $params
	 * @return html content
	 */
	public function getRelations($pid,$aid,$tableName='',$relationsField='', $relationsType=''){
		
		$articleRelations = $this->getArticleRelations($pid, $aid, $tableName, $relationsField);
		if (empty($articleRelations)) {
			return array();
		}
		$relatedLinks = $this->relatedLinksData($articleRelations);

		if($relationsType && !is_array($relationsType)){
			$relationsType = array(trim($relationsType));
		}
		
		$content = array();

		// main loop			
		foreach ($relatedLinks as $link){
			//ignore if no article id found
			if (!$link['id']) continue;

			
			// give article a type accourding to wrapper array list $this->tableTypeWrapper 
			if (isset($this->tableTypeWrapper[$link['table']])){
				$link['type']=$this->tableTypeWrapper[$link['table']];
				
			}	
			// any other tables take type txt if they are not in wrapper array
			else {
				$link['type']='text';
			}
			
			// if relation type given, return only 
			if(is_array($relationsType)){
				if(!in_array($link['type'], $relationsType)){
					continue;
				}
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
			$linkList = array_merge($linkList,$this->cmt->safeUnserialize($relationGroup));
		}
		
		
		
		foreach($linkList as $link){
			$relatedLinkList[]=array("tableId"=>$link[0],"articleId"=>$link[1]);
		}
		$relatedLinks=array();
		//$whereClause = " AND status = '3' AND online_date <= '".$this->date."' AND (offline_date >= '".$this->date."' OR offline_date = '0000-00-00 00:00:00')";
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
	
// 	function safeUnserialize ($var) {
// 		$var = unserialize($var);
// 		if (is_array($var)) {
// 			$var = safeUnserializeArray($var);
// 		} else {
// 			$var = safeUnserializeCleanUp($var);
// 		}
// 		return $var;
// 	}	
}

?>
