<?php
/**
 * class_mlog_posts.inc
 * Provides methods to handle posts in MLog blog system.
 * 
 * 
 * @author A. Al Kaissi, J.Hahn <info@contentomat.de>
 * @version 2017-07-24
 * @package MLog
 */
namespace Contentomat\MLog;
use Contentomat\Parser;
use Contentomat\DBCex;
use Contentomat\Contentomat;

Class Posts {

	protected $db;
	protected $parser;
	protected $date;
	protected $postsTable;
	protected $mediaTable;
	public $postDbID;
	public $fieldNameWrapper;
	public $dateTimeFields;
	protected $contentFieldTypes;
	protected $totalPosts;
	protected $session;
	protected $lastQuery;                      // Last Query
	protected $monthNames;
	protected $monthShortNames;
	protected $cmt;
	protected $defaultPostsPerPage = 10;
	protected $mediaBaseHandler;

	public function __construct() {
		
		$this->cmt = Contentomat::getContentomat();
		
		$this->postsTable = 'mlog_posts';
		$this->mediaTable = 'mlog_media';
		$this->mediaTypesTable = 'mlog_media_types';
		$this->db = new DBCex();
		$this->parser = new Parser();
		$this->session = $this->cmt->getSession();
		$this->mediaBaseHandler = new MediaBaseHandler();
		
		$this->date = date('Y-m-d H:i:s');
		
		// Init language specific monthnames
		$this->setMonthNames();
		$this->setMonthShortNames();
		
		$this->initPosts();
	}

	public function initPosts() {
		$this->fieldNameWrapper = array(
			"post" => array(
				'postID' => 'id',
				'postTitle' => 'post_title',
				'postText' => 'post_text',
				'postTeaser' => 'post_teaser',
				'postDate' => 'post_date',
				'postOnlineDate' => 'post_online_date',
				'postOfflineDate' => 'post_offline_date',
				'postTags' => 'post_tags',
				'postStatus' => 'post_status',
				'postCommentStatus' => 'post_comment_status',
				'postUrl' => 'post_url',
				'postImage' => 'post_image',
				'postRelations' => 'post_relations',
				'postFile' => 'post_file',
				'postDateStart' => 'post_date_start',
				'postDateEnd' => 'post_date_end',
				'postUrlAlias' => 'post_url_alias',
				'mediaPositions' => 'mediaPositions',
				'post_category' => 'post_category'
			),
			"media" => array(
				'mediaID' => 'id',
				'mediaTitle' => 'media_title',
				'mediaText' => 'media_text',
				'mediaTeaser' => 'media_teaser',
				'mediaDate' => 'media_date',
				'mediaOnlineDate' => 'media_online_date',
				'mediaOfflineDate' => 'media_offline_date',
				'mediaTypeID' => 'media_type',
				'mediaTypeName' => 'media_type_name_de',
				'mediaTags' => 'media_tags',
				'mediaStatus' => 'media_status',
				'mediaCommentStatus' => 'media_comment_status',
				'mediaUrl' => 'media_url',
				'mediaImage' => 'media_image',
				'mediaFile' => 'media_file',
				'mediaFiles' => 'media_files',
				'mediaStartDate' => 'media_start_date',
				'mediaEndDate' => 'media_end_date',
				'mediaUrlAlias' => 'media_url_alias',
				'postID' => 'media_post_id'
			)
		);
		$this->dateTimeFields = array(
			'post_date_start',
			'post_date_end',
			'post_date',
			'post_online_date',
			'post_offline_date'
		);
	}

	
	public function getTotalPosts(){
		return $this->totalPosts;
	}
	
	public function getLastQuery(){
		return $this->lastQuery;
	}
	
	/**
	 * public function wrapToFormFields()
	 * "Übersetzt" die Datenbanktabellennamen der Kundendaten in Formularfeldnamen. 
	 * 
	 * @param array $data Daten in einem Array (Datenbankfeldname => Wert)
	 * @return array Daten zur Verwendung in Formularen (Feldname => Kundenangabe)
	 */
	public function wrapToFormFields($data, $type) {
		if (!is_array($data))
			return array();

		$formData = array();
		if (!is_array($this->fieldNameWrapper[$type])) {
			$this->fieldNameWrapper[$type] = array();
		}
		$formFieldNameWrapper = array_flip($this->fieldNameWrapper[$type]);

		foreach ($data as $dbField => $dbValue) {

			$formField = $formFieldNameWrapper[$dbField];
			
			// Sonderfall. Datetimefeld
			if (preg_match('/_date(time)?$/i', $dbField)) {
				$formData[$formField . '_year'] = $data[$dbField . '_year'];
				$formData[$formField . '_month'] = $data[$dbField . '_month'];
				$formData[$formField . '_day'] = $data[$dbField . '_day'];
				$formData[$formField . '_hour'] = $data[$dbField . '_hour'];
				$formData[$formField . '_minute'] = $data[$dbField . '_minute'];
				$formData[$formField . '_second'] = $data[$dbField . '_second'];
			} else {
				$formData[$formField] = htmlspecialchars(stripslashes($dbValue));	
			}
			
		}

		return $formData;
	}

	/**
	 * public function wrapToDbFields()
	 * "Übersetzt" die Formularfeldnamen in Datenbankfeldnamen. 
	 * 
	 * @param array $data Daten in einem Array
	 * @return array Daten zur Verwendung in der Datenbank (Feldname => Kundenangabe)
	 */
	public function wrapToDbFields($data, $type) {
		if (!is_array($data))
			return array();

		$dbData = array();
		//$dbFieldNameWrapper = array_flip($this->fieldNameWrapper);

		foreach ($data as $formField => $formValue) {

			if (is_array($formValue)) {
				$formValue = join(",", $formValue);
			}

			// Sonderfall. Datetimefeld
			if (preg_match('/_(year)?$/i', $formField)) {
				
				$realFormField = preg_replace('/_(year)?$/i', '', $formField);
		
				if ($this->fieldNameWrapper[$type][$realFormField]) {
					
					$dbValue = intval($data[$realFormField  . '_year']) . '-' .
							   intval($data[$realFormField  . '_month']) . '-' .
							   intval($data[$realFormField  . '_day']) . ' ' .
							   intval($data[$realFormField  . '_hour']) . ':' .
							   intval($data[$realFormField  . '_minute']) . ':' .
							   intval($data[$realFormField  . '_second']);
							   
					$dbData[$this->fieldNameWrapper[$type][$realFormField]] = $dbValue;
					
//					unset($data[$realFormField  . '_year']);
//					unset($data[$realFormField  . '_month']);
//					unset($data[$realFormField  . '_day']);
//					unset($data[$realFormField  . '_hour']);
//					unset($data[$realFormField  . '_minute']);
//					unset($data[$realFormField  . '_second']);
							   
				}
			} 
			// normales Feld
			else if ($this->fieldNameWrapper[$type][$formField]) {

				$dbData[$this->fieldNameWrapper[$type][$formField]] = addslashes($formValue);
			}
		}

		return $dbData;
	}

	/**
	 * TODO: public function getMlogSettings()
	 * 
	 * get mlog settings
	 * 
	 * get settings from paht (/admin/cmt_mlog_settings.inc) if exists
	 * else
	 * get settings from mlog app settings
	 * 
	 * @return return array(); 
	 */
	public function getMlogSettings() {
		if(file_exists(PATHTOADMIN.'cmt_mlog_settings.inc')){
			include_once(PATHTOADMIN.'cmt_mlog_settings.inc');
			if(is_array($mlogSettings)){
				return $mlogSettings;
			}
		}
		
		$settings = array();
		$query = "SELECT cmt_tablesettings FROM cmt_tables WHERE cmt_showname = 'Mlog' ";
		$this->db->query($query);
		$res = $this->db->get();
		$settings = $res['cmt_tablesettings'];
		$settings = unserialize($settings);
		
		if (!is_array($settings)){
			return array();
		}
		
		foreach ($settings as $key => $value) {
			$optonNameParts = explode("_", $key);
			if ($optonNameParts[0] == 'media') {
				if ($optonNameParts[2] == 'types') {
					$value = explode(",", $value);
				}
				$settings['mediaSettings'][$optonNameParts[1]][$optonNameParts[2]] = $value;
			}
		}
		
		if (!is_array($settings)){
			return array();
		}
		return $settings;
	}
	
	/**
	 * public function getAuthor()
	 * Ermittelt den Autoren eines Beitrags anhand seiner ID
	 *
	 * @param array $params Parameter als assoziatives Array:
	 * 'authorID' => ID des Autors
	 *
	 * @return array Die Autorendaten als assoziatives Array
	 */
	public function getAuthor($params){

		if(!$params['authorID']){
			return array();
		}
		
		$author = array();
		$query = "SELECT * FROM mlog_authors WHERE id='". intval($params['authorID']) ."'";
		$this->db->query($query);
		$res = $this->db->get();
		
		if(is_array($res)){
			$author = $res;
		}
		
		return $author;
	}
	/*	 * *******************************
	 * Posts
	 * ******************************* */

	/**
	 * public function getMediaTypes()
	 *  
	 * Enter description here ...
	 * @return array
	 */
	// OUDATED! Moved to MediaBaseHandler!
	public function getMediaTypes() {

		return $this->mediaBaseHandler->getAvailableMediaTypes();
		
// 		$return = array();

// 		$this->db->query("SELECT * FROM mlog_media_types WHERE media_type_status=1 ORDER BY media_type_position");

// 		while ($res = $this->db->get()) {
// 			$res['media_type_name'] = $res['media_type_name_' . 'de'];
// 			$res['mediaType'] = strtolower($res['media_type_title']);
// 			$return[$res['id']] = $res;
// 		}

// 		return $return;
	}

	/**
	 * public function getPostsCategories()
	 *  
	 * Enter description here ...
	 * @return array
	 */
	public function getPostsCategories() {

		$return = array();

		$this->db->query("SELECT * FROM mlog_category WHERE category_status=1 ORDER BY category_position");

		while ($res = $this->db->get()) {
			$res['category_title'] = $res['category_title_' . 'de'];
			$return[$res['id']] = $res;
		}
		return $return;
	}
	
	public function getPageBelongCategories($pageId){
		$return = array();
		$this->db->query("SELECT id FROM mlog_category WHERE FIND_IN_SET('".$pageId."',category_parent_page)");
		while($res = $this->db->get()){
			$return[]= $res['id'];
		}
		return $return;
	}

	/**
	 *	public function getCategoryData()
	 * 
	 * get category detalils from mlog category table
	 * 
	 * @param type $categoryId
	 * @return type 
	 */
	public function getCategoryData($categoryId){
		if(!$categoryId){
			return array();
		}
		
		$this->db->query("SELECT * FROM mlog_category WHERE id='".$categoryId."'"); 
		$res = $this->db->get();
		return $res;
	}
	
	
	/**
	 *	public function getCategoryData()
	 * 
	 * get category detalils from mlog category table
	 * 
	 * @param type $categoryId
	 * @return type 
	 */
	public function getCategoryDataByName($categoryName){
		if(!$categoryName){
			return array();
		}
		
		$this->db->query("SELECT * FROM mlog_category WHERE category_name='".$categoryName."'"); 
		$res = $this->db->get();
		return $res;
	}
	
	/**
	 * public function getAllPosts()
	 *
	 * get all articles from posts table, default this method returned post of type (article) only.
	 * @param $params
	 * @return array mlog posts (articles)
	 */
	public function getAllPosts($params = array()) {

		$returnPosts = array();
		$defaultParams = array(
			//'postType'=> 1, // get online Article posts
			'orderDir' => 'DESC',
			'orderBy' => 'post_date',
			'postsPerPage' => 0,
			'ignoreStatus' => false,
			'ignoreOnlineDate' => false,
			'ignoreOfflineDate' => false
		);

		$params = array_merge($defaultParams, $params);
		
		$whereArray = array();
		$where = '';
		$limit = '';
		
		// get posts for selected feed
		if ($params['feedId']) {
			$whereArray[] = "FIND_IN_SET(" . intval($params['feedId']) . ", post_feeds)";
		}
		
		// get only posts with status "published" (default)
		if (!(bool)$params['ignoreStatus']) {
			$whereArray[] = "posts.post_status = '3'";
		}
		
		// get only posts with online date in the past 
		if (!(bool)$params['ignoreOnlineDate']) {
			$whereArray[] = "posts.post_online_date <= '" . date('Y-m-d H:i:s') . "'";
		}

		// get only posts with online date in the past
		if (!(bool)$params['ignoreOfflineDate']) {
			$whereArray[] = "(posts.post_offline_date >= '" . date('Y-m-d H:i:s') . "' OR posts.post_offline_date = '0000-00-00 00:00:00')";
		}
		
		// build WHERE clause
		if (!empty($whereArray)) {
			$where = ' WHERE ' . implode(' AND ', $whereArray);
		}
		
		
		// build LIMIT condition
		if ($params['postsPerPage']) {
			$limit = " LIMIT " . intval($params['postsPerPage']);
		}

		$this->db->query("
			SELECT  posts.* 
 			FROM " . $this->db->dbQuote($this->postsTable) . " posts " .
			$where . "   
			ORDER BY posts." . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir']) . " " .
			$limit
		);

		return $this->db->getAll();
	}
	
	/**
	 * public function getLastPosts()
	 * Returns the last valid post or a selectable number of last posts.
	 *
	 * @param array $params Associative array with this key/value pairs:
	 *  - optional: 'posts' => number of posts to show. Default: 1
	 *  - optional: 'categoryID' => id of a category
	 *
	 * @return array Post/sdata in an array.
	 */
	public function getLastPosts($params) {
		
		$entries = intval($params['posts']);
		if (!$entries) {
			$entries = 1;
		}
		
		return $this->search(
			array_merge(
				array(
					'categoryLimit' => $params['categoryID'],
					'postsPerPage' => $entries,
					'orderDir' => 'DESC',
					'orderBy' => 'post_online_date'
				),
				$params
			)	
		);
	}

	/**
	 * public function getSimilarPostsByTags()
	 * 
	 * get posts with first tag similer to current post tags
	 * 
	 * @param array $params
	 * @return array 
	 */
	public function getSimilarPostsByTags($params=array()){
		
		$returnPosts = array();
		
		$defaultParams = array(
			'postsPerPage'=>10,
			'orderBy' => $this->postsTable.".post_online_date DESC"
		);
		
		$params = array_merge($defaultParams,$params);
		

		$condWhere[] = "(".$this->postsTable . ".post_tags REGEXP '^".$this->db->dbQuote($params['searchTag']).",.*' OR ".$this->postsTable . ".post_tags REGEXP '^".$this->db->dbQuote($params['searchTag'])."$')";

		if($params['currentArticleId']){
			$condWhere[] = "id <> ".$params['currentArticleId'];
		}
	
		$posts = $this->search(array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			'condWhere' => $condWhere,
			'postsPerPage' => $params['postsPerPage'],
			));
		
		
		
		if(!is_array($posts)){
			return array();
		}
		return $posts;
		
		
	}
	
	
	
	/**
	 * public function getSimilarPostsByTags()
	 * 
	 * get posts with first tag similer to current post tags
	 * 
	 * @param array $params
	 * @return array 
	 */
	public function getFirstPostByCategory($params=array()){
		
		$returnPosts = array();
		
		$defaultParams = array(
			'postsPerPage'=>1,
			'orderBy' => $this->postsTable.".post_online_date DESC",
			'condWhere' => array(),
		);
		
		$params = array_merge($defaultParams,$params);

		if(!$params['postCategoryId'] && $params['postCategoryName']){
			$categoryData = $this->getCategoryDataByName($params['postCategoryName']);
			$categoryData['category_id'] = $categoryData['id'];
			unset($categoryData['id']);
			$params['postCategoryId'] = $categoryData['category_id'];
		}

		$condWhere = array_merge(array(), $params['condWhere']);
		
		$condWhere[] = "(".$this->postsTable . ".post_category REGEXP '^".$this->db->dbQuote($params['postCategoryId']).",.*' OR ".$this->postsTable . ".post_category REGEXP '^".$this->db->dbQuote($params['postCategoryId'])."$')";

		$posts = $this->search(array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			'condWhere' => $condWhere,
			'postsPerPage' => $params['postsPerPage'],
			));

		if(!is_array($posts)){
			return array();
		}
		
		foreach($posts as $post){
			$post['categoryData'] = $categoryData;
			$returnPosts[] = $post;
			
		}
		return $returnPosts;
		
		
	}
	
	/**
	 * public function getFeedPosts()
	 * 
	 * get posts to create feeds
	 * support creating different feeds by giving specific flag by target value
	 * to create (facebook) feed, target value must be (facebook), and the posts
	 * table must have a field named (post_publish_facebook), then
	 * this function will return posts with checked (post_publish_facebook) field
	 * @param array $params
	 * @return array 
	 */
	public function getFeedPosts($params=array()){
		
		$defaultParams = array(
			'postsPerPage'=>10,
			'target'=>'rss'
		);
		
		$params = array_merge($defaultParams,$params);
		
		$condWhere = array();
		
		if($params['target']=='rss'){
			// Regular RSS Feed
		}elseif($params['target'] != ''){
			$feedTypeField = "post_publish_".trim(strtolower($params['target']));
			$condWhere[] = $feedTypeField."='1'";
		}
		
		$posts = $this->search(array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			'condWhere' => $condWhere,
			'postsPerPage' => $params['postsPerPage'],
			));
		
		if(!is_array($posts)){
			return array();
		}
		return $posts;
	}
	
	
	/**
	 * public function search()
	 * 
	 * Search mlog posts for specific posts, categories and conditions
	 *  
	 * @param array $params
	 * @return array 
	 */
	public function search($params) {
		
		$defaultParams = array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			'hasMediaType'=>'',
			'limitByOnlineOfflineDates'=>1,
			'postsPerPage' => $this->defaultPostsPerPage
		);

		$params = array_merge($defaultParams, $params);


		$whereClause = array();

		if (is_array($params['condWhere'])){
			$whereClause = array_merge($whereClause,$params['condWhere']);
		}
		
		
		 if ($params['limitByOnlineOfflineDates']){
            $whereClause[] = $this->postsTable.".post_online_date <= '".$this->date."' ".
            "AND (".$this->postsTable.".post_offline_date >= '".$this->date."' ".
            "OR ".$this->postsTable.".post_offline_date = '0000-00-00 00:00:00')";
        } 
		
		

		// status
		$whereClause[] = $this->postsTable . ".post_status='3'";


		// select category
		if ($params['categoryLimit']) {

			$categoryLimitList = explode(",", $params['categoryLimit']);
			if (is_array($categoryLimitList) && !empty($categoryLimitList)) {
				$categoryLimitQueryStringFragments = array();
				foreach ($categoryLimitList as $category) {

					$categoryLimitQueryStringFragments[] = "FIND_IN_SET('" . intval(trim($category)) . "'," . $this->postsTable . ".post_category)";
				}
				$categoryLimitQueryString .= "(" . join(" OR ", $categoryLimitQueryStringFragments) . ")";
			}

			if ($categoryLimitQueryString) {
				$whereClause[] = $categoryLimitQueryString;
			}
		}


		$searchClause = array();
		$searchText = (array)$params['searchText'];
		$condition = 'AND';

		// TODO: test this!!!
		switch($params['searchType']) {
			
			case 'like':
				$searchFields = explode(',', $params['searchFields']);
				foreach ($searchFields as $field) {
					$searchClause[] = $this->postsTable . '.' . $field . " LIKE '%" . $this->db->dbQuote($params['searchText']) . "%'";
				}
				$condition = 'OR';
				break;
				
			case 'fulltext':
				$searchClause[] = "MATCH (" . $params['searchFields'] . ") AGAINST ('" . $this->db->dbQuote($params['searchText']) . "')";
				break;
				
			case 'set':
				foreach ($searchText as $key => $text) {
					$searchClause[] = "FIND_IN_SET ('" . $this->db->dbQuote($text) . "', " . $this->postsTable . "." . $params['searchFields'] . ")";
				}
				
				break;
		}
		
		if (!empty($searchClause)) {
			$whereClause[] = "(" . implode(" ". $condition ." ", $searchClause) . ")";
		}
		
		// search by keyword
// 		if ($params['searchMode']) {
// 			// search a word in like '%%' mode
// 			if ($params['searchText'] != '' && $params['searchType'] == 'like' && $params['searchFields'] != '') {
// 				$searchFields = explode(',', $params['searchFields']);
// 				$fieldsArray = array();
// 				foreach ($searchFields as $field) {
// 					$fieldsArray[] = $this->postsTable . '.' . $field . " LIKE '%" . $this->db->mysqlQuote($params['searchText']) . "%'";
// 				}
// 				if ($fieldsArray) {
// 					$whereClause[] = "(" . join(' OR ', $fieldsArray) . ")";
// 				}
// 			}
// 			// search a word in Fulltext mode
// 			elseif ($params['searchText'] != '' && $params['searchType'] == 'fulltext' && $params['searchFields'] != '') {
// 				$whereClause[] = "MATCH (" . $params['searchFields'] . ") AGAINST ('" . $this->db->mysqlQuote($params['searchText']) . "')";
// 			}
// 		}


		//
		//  Total Serach Results (count) Query
		//
		
		$queryCount = "SELECT count(*) as totalSearchResults FROM " . $this->db->dbQuote($this->postsTable);
		// join where clause conditions
		if (!empty($whereClause)) {
			$queryCount .= ' WHERE ' . join(" AND ", $whereClause);
		}
		
		$this->db->query($queryCount);
		while($total = $this->db->get()){
			$this->totalPosts = $total['totalSearchResults'];
		}
		
		
		// 
		// Current Page Results Query
		//
		
		$query = "SELECT " . $this->postsTable . ".* FROM " . $this->db->dbQuote($this->postsTable);
		//$query = "SELECT DATE_FORMAT(post_online_date, '%Y-%m-%d %H:%i:%s'), " . $this->postsTable . ".* FROM " . $this->db->dbQuote($this->postsTable);
		
		// join where clause conditions
		if (!empty($whereClause)) {
			$query .= ' WHERE ' . join(" AND ", $whereClause);
		}
		

		// Order
		$query .=" ORDER BY " . $this->postsTable . "." . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir']);

		// Paging limit
		if (!$params['currentPage']) {
			$params['currentPage'] = 1;
		}
		if(!$params['postsPerPage']){
			$params['postsPerPage'] = $defaultParams['postsPerPage'];
		}
		$limitFrom = $params['postsPerPage'] * ($params['currentPage'] - 1);

		$query .=" LIMIT " . $limitFrom . "," . $params['postsPerPage'];

		$this->lastQuery = $query;
		
		//echo $query.'</br></br>';
		
		$postsList = array();
		if($query){
			$this->db->query($query);
			$inf = $this->db->getCurrentFieldInfo();
			$this->contentFieldTypes = $inf['type'];
			while ($r = $this->db->get(MYSQLI_ASSOC)) {
				$r = $this->formatFieldTypes($r);
				$r['searchParameters']= $this->getSearchParams();
				$r['currentPage'] = $params['currentPage'];
				$r['linkTitle']= $this->cmt->makeNameWebsave($r['post_title']);
				$r['postID']=$r['id'];
				$list[] = $r;
			}
		}
		
		if(!$list ||empty($list)){
			$list =  array();
		}else{
			if($params['hasMediaType']){
				$params['hasMediaType'] = 'has'.ucfirst($params['hasMediaType']);
			}
			foreach($list as $post){
				$post = array_merge($post,$this->getPostMedia($post['id']));
				if($params['hasMediaType']){
					if(!array_key_exists($params['hasMediaType'],$post['hasMedia'])){
						continue;
					}
				}
				$post = array_merge($post,$this->getPostBelongCategories($post['post_category']));
				$postsList[]=$post;
			}
		}
		unset($list);
		return $postsList;
	}
	
	
	/**
	 * public function getSearchParams()
	 * @param 
	 * @return string
	 */
	public function getSearchParams(){
		$params='';
		foreach($_GET as $param=>$value){
			$noSearchParams = array("cp","pid","lang","aid","action");
			if (!$value || in_array($param, $noSearchParams)) continue;
			$params .= "&".$param."=".$value;
		}
		return $params;
	}
	
	
	/**
	* protected function formatFieldTypes()
	* @param $r array, single post data
	* @return array, modefied post data
	**/
	protected function formatFieldTypes($r){
		foreach ($r as $fieldName => $fieldValue) {
			switch ($this->contentFieldTypes[$fieldName]) {
				case 'datetime':
				case 'date':
					$r = array_merge($r, $this->formatDate($fieldValue, $fieldName.'_'));
					break;

				case 'blob':
				case 'text':
					//$r[$fieldName] = nl2br(stripslashes($fieldValue));
					$r[$fieldName.'_raw'] = stripslashes($fieldValue);
					break;

				case 'string':
					$r[$fieldName] = stripslashes($fieldValue);
					break;
			}
		}
		return $r;
	}
	
	
	
	
	/*
	 * get media of posts
	 */
	public function getPostsByMedia($params = array()){
		
		$mediaTypes = $this->getMediaTypes();
		
		
		$mediaList = array();
		
		$defaultParams = array(
			'orderDir' => 'DESC',
			'orderBy' => '',
			'hasMediaType'=>'',
			'limitByOnlineOfflineDates'=>1
		);
		$params = array_merge($defaultParams,$params);
		
		
		
		$whereClause = array();

		if(is_array($params['condWhere'])){
			$whereClause = array_merge($whereClause,$params['condWhere']);
		}
		
		
		
		foreach($mediaTypes as $index => $data){
			if($params['hasMediaType']==$data['mediaType']){
				$whereClause[] = $this->mediaTable.".media_type=".$index;
			}
		}
		
		
		 if ($params['limitByOnlineOfflineDates']){
            $whereClause[] = $this->postsTable.".post_online_date <= '".$this->date."' ".
            "AND (".$this->postsTable.".post_offline_date >= '".$this->date."' ".
            "OR ".$this->postsTable.".post_offline_date = '0000-00-00 00:00:00')";
        } 
		

		// status
		$whereClause[] = $this->postsTable . ".post_status='3'";
		
		$query = "SELECT ".$this->mediaTable.".*, ".$this->postsTable.".post_title FROM ".$this->mediaTable;
		
		$query .= " JOIN ".$this->postsTable." ON (".$this->postsTable.".id=".$this->mediaTable.".media_post_id)";
		
		// join where clause conditions
		if (!empty($whereClause)) {
			$query .= ' WHERE ' . join(" AND ", $whereClause);
		}
		
		
		// Order
		$query .=" ORDER BY " . $this->mediaTable . "." . $this->db->dbQuote($params['orderBy']) . " " . $this->db->dbQuote($params['orderDir']);

		// Paging limit
		if (!$params['currentPage']) {
			$params['currentPage'] = 1;
		}
		$limitFrom = $params['postsPerPage'] * ($params['currentPage'] - 1);

		$query .=" LIMIT " . $limitFrom . "," . $params['postsPerPage'];
		
		$this->lastQuery = $query;

		if($query){
			$this->db->query($query);
			$inf = $this->db->getCurrentFieldInfo();
			$this->contentFieldTypes = $inf['type'];
			while ($r = $this->db->get(MYSQLI_ASSOC)) {
				$r = $this->formatFieldTypes($r);
				$r['searchParameters']= $this->getSearchParams();
				$r['currentPage'] = $params['currentPage'];
				$r['linkTitle']= $this->cmt->makeNameWebsave($r['post_title']);
				$mediaList[] = $r;
			}
		}
		//echo $query;
		return $mediaList;
	}
	
	
	
   /**
	 * public function formatDate();
	 * @param string $datetime
	 * @return array Array mit mehreren Datumsangabe: 'date' => Datum, 'time' => Uhrzeit, 'timestamp'=>timestamp, 'weekday'=>Wochentag
	 */
	public function formatDate($datetime, $varNamePrefix='') {
		
		// Lokalisierung einstellen
		setlocale(LC_ALL, 'de_DE');
		
		$r = array();
		preg_match('/^(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?$/', $datetime, $match);
		$r[$varNamePrefix.'date'] = $match[3].'.'.$match[2].'.'.$match[1];
		$r[$varNamePrefix.'time'] = $match[5].'.'.$match[6];
		$r[$varNamePrefix.'timestamp'] = mktime($match[5],$match[6],$match[7],$match[2],$match[3],$match[1]);
		$r[$varNamePrefix.'weekday'] = date('l', $r[$varNamePrefix.'timestamp']);
		$r[$varNamePrefix.'weekdayShort'] = substr($r[$varNamePrefix.'weekday'], 0, 3);
		$r[$varNamePrefix.'day'] = $match[3];
		$r[$varNamePrefix.'month'] = $match[2];
		$r[$varNamePrefix.'hour'] = $match[5];
		$r[$varNamePrefix.'minute'] = $match[6];
		$r[$varNamePrefix.'second'] = $match[7];
		
		//$r[$varNamePrefix.'monthName'] = date('F', mktime($match[5], $match[6], $match[7], $match[2], $match[3], $match[1]));
		$r[$varNamePrefix.'monthName'] = $this->monthNames[intval($match[2])];

		//$r[$varNamePrefix.'monthNameShort'] = date('M', $match[2]);
		$r[$varNamePrefix.'monthNameShort'] = $this->monthShortNames[intval($match[2])];
		
		$r[$varNamePrefix.'year'] = $match[1];
		if ($r[$varNamePrefix.'date'] == '..' || $r[$varNamePrefix.'date'] =='00.00.0000') {
			$r[$varNamePrefix.'date'] = '';
		}
		if ($r[$varNamePrefix.'time'] == '.' || $r[$varNamePrefix.'time'] =='00.00') {
			$r[$varNamePrefix.'time'] = '';
		}
		return $r;
	}

	/**
	 * public function setMonthNames()
	 * Stellt die Monatsnamen für Datumsausgaben ein.
	 *
	 * @param array $monthNames Monatsnamen als Array. Achtung Januar hat den Index 1, usw
	 *
	 * @return void
	 */
	public function setMonthNames($monthNames = array()) {
		
		$defaultMonthNames = array(
			0 => '',		// ugly!
			1 => 'Januar',
			2 => 'Februar',
			3 => 'März',
			4 => 'April',
			5 => 'Mai',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'August',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Dezember'
		);
		
		$this->monthNames = array_merge($defaultMonthNames, $monthNames);
	}
	
	/**
	 * public function setMonthShortNames()
	 * TODO: Nicht gut so!!! Stellt die Monatsnamenabkürzungen (3 Buchstaben) für Datumsausgaben ein.
	 *
	 * @param array $monthShortNames Monatsnamenabkürzungen als Array. Achtung Januar hat den Index 1, usw
	 *
	 * @return void
	 */
	public function setMonthShortNames($monthShortNames = array()) {
	
		$defaultMonthShortNames = array(
				0 => '',		// ugly!
				1 => 'Jan',
				2 => 'Feb',
				3 => 'Mrz',
				4 => 'Apr',
				5 => 'Mai',
				6 => 'Jun',
				7 => 'Jul',
				8 => 'Aug',
				9 => 'Sep',
				10 => 'Okt',
				11 => 'Nov',
				12 => 'Dez'
		);
	
		$this->monthShortNames = array_merge($defaultMonthShortNames, $monthShortNames);
	}
	
	public function getPostById($postId) {
		return $this->getPost(array(
			'postId' => intval($postId)
		));
	}
	
	/**
	 * public function getPost()
	 * get an Article post by ID
	 * @param $params array
	 */
	public function getPost($params = array()) {
		
		$defaultParams = array(
			'loggedIn' => $this->session->checkIsLoggedIn(),
			'activeStatus' => 3
		);
		$params = array_merge($defaultParams, $params);

		// Due to backwards compatibility
		if (!$params['postId']) {
			$params['postId'] = $params['postID'];
		}
		if (!$params['postId']) {
			return array();
		}

		//$whereClause = " AND post_status = '3' AND post_online_date <= '".$this->date."' AND (post_offline_date >= '".$this->date."' OR post_offline_date = '0000-00-00 00:00:00')";
		$whereClause = "";
		
		// show active articles only. Ignore this condition if user has logged in.
		if(!$params['loggedIn']){
			$whereClause .= "AND post_status='" . intval($params['activeStatus']) . "' ";
		}
		
		$query = "SELECT * FROM " . $this->postsTable . " WHERE id='" . intval($params['postId']) . "' " . $whereClause;

		$this->db->query($query);
		
		$inf = $this->db->getCurrentFieldInfo();
		$this->contentFieldTypes = $inf['type'];
		
		$r = $this->db->get();

		if (!is_array($r)) {
			return array();
		}
		$r = $this->formatFieldTypes($r);
		$r['searchParameters']= $this->getSearchParams();
		$r['currentPage'] = $params['currentPage'];
		$r['postID'] = $r['id'];
		$r['postId'] = $r['id'];
		$r['post_relations'] = htmlentities($r['post_relations']);
		$r['linkTitle']= $this->cmt->makeNameWebsave($r['post_title']);
		
		
		$r = array_merge($r,$this->getPostMedia($r['id']));
		$r = array_merge($r,$this->getPostBelongCategories($r['post_category']));
				
				

		return $r;
	}


	/**
	 * public function savePost()
	 * save new created or edited post
	 * @param 
	 * @return void
	 */
	public function savePost($params = array()) {
		$excludeFields = array('id', 'redirect', 'mediaPositions');
		$defaultParams = array(
			'redirect' => true,
			'post_offline_date' => '0000-00-00 00:00:00',
			'post_online_date' => $this->date,
			'post_status' => 3,
			'post_category' => '',
			'post_comment_status' => 1
		);

		if (!is_array($params)) {
			$params = array();
		}

		$params = array_merge($defaultParams, $params);
		foreach ($this->dateTimeFields as $dateTimeFieldName) {
			$date = trim($params[$dateTimeFieldName]);
			if ($date && $date != "0000-00-00 00:00:00") {
				$params[$dateTimeFieldName] = $this->dbDateFormat($date);
			}
		}

		// DevMode Only !!
		$queryPart = $this->makeSetQuery($params, $excludeFields);

		if ($params['id']) {
			$query = "UPDATE " . $this->postsTable . " SET " . $queryPart . " WHERE id='" . $params['id'] . "'";
		} else {
			$query = "INSERT INTO " . $this->postsTable . " SET " . $queryPart;
		}

		$this->db->query($query);

		if (!$params['id']) {
			$params['id'] = $this->db->getLastInsertedId();
		}

		// relink new created media with post
		$this->db->query("UPDATE " . $this->mediaTable . " SET media_post_id='" . $params['id'] . "' WHERE media_post_id='0' ");
		if ($params['redirect']) {
			$this->doRedirect();
		}

		$this->updatePostMediaPositions($params['id'], $params['mediaPositions']);
	}

	public function updatePostStatus($postId){
		
		if(!$postId){
			return false;
		}
		
		$this->db->query("UPDATE ".$this->postsTable." SET post_views = post_views+1 WHERE id='".$postId."'");
		if($this->db->getLastError()){
			return false;
		}
		
		return true;
	}
	
	/**
	 * public function savePostMedia()
	 * Speichert die Reihenfolgen der Medien eines Posts und aktiviert sie.
	 *
	 * @param array $params Assoziatives Array (meist Formulardaten) mit dem Element "mediaPositions" (Array), 
	 * welches die IDs und die Reihenfolge der Medien enthält. 
	 *
	 * @return boolean
	 */
	public function savePostMedia($params = array()) {

		if (!is_array($params)) {
			return false;
		}

		$mediaIDs = explode(',', $params['mediaPositions']);
		$c = 1;

		foreach ($mediaIDs as $mediaID) {

			$check = $this->db->query(
					"UPDATE " . $this->mediaTable . " 
				 SET media_is_active = '1', 
				 media_position = '" . $c++ . "' 
				 WHERE id = '" . intval($mediaID) . "'"
			);
			
			// Bei Fehler abbrechen
			if ($check) {
				return false;
			}
			
		}

		return true;
	}

	protected function updatePostMediaPositions($postID, $positions) {
		if (!$postID OR !$positions) {
			return false;
		}

		$positions = explode(",", $positions);

		foreach ($positions as $index => $id) {
			$this->db->query("UPDATE " . $this->mediaTable . " SET media_position='" . $index . "' WHERE media_post_id='" . $postID . "' AND id='" . $id . "' ");
		}
		return true;
	}

	/**
	 * public function deletePost()
	 * delete post
	 * @param unknown_type $params
	 * @return void
	 */
	public function deletePost($params = array()) {
		$defaultParams = array(
			'deletePostsMedia' => 1,
			'postType' => 1,
			'redirect' => 1
		);

		$params = array_merge($defaultParams, $params);

		if (!$params['postID'])
			return false;

		$query = "DELETE FROM " . $this->postsTable . " WHERE id='" . $params['postID'] . " '";
		$this->db->query($query);

		// delete related posts of deleted post
		if ($params['deletePostsMedia'] && !$this->db->getLastErrorNr()) {
			$postsMedia = $this->getAllMedia(array('postID' => $params['postID']));
			if (!empty($postsMedia)) {
				foreach ($postsMedia as $media) {
					$this->deleteMedia(array(
						'mediaID' => $media['id']
					));
				}
			}
		}
	}
	
	/**
	 * public function getRelatedPosts()
	 * Returns all related posts
	 * 
	 * @param number $postId Source post's id
	 * @return array Related posts data
	 */
	public function getRelatedPosts($postId) {
		
		$postId = intval($postId);
		if (!$postId) {
			return array();
		}
		
		$this->db->query("SELECT post_relations FROM " . $this->postsTable . " WHERE id = '" . $postId ."'");
		$r = $this->db->get();
		$relations = $this->cmt->safeUnSerialize(($r['post_relations']));
		
		$relatedPosts = array();
		foreach((array)$relations as $relation) {
			$relatedPosts[] = $this->getPost(array('postID' => $relation[1]));
		}

		return $relatedPosts;
		
	}

	/*	 * **********************
	 * Media
	 * ********************** */

	/**
	 * public function getAllMedia()
	 * 
	 * get all related media to an article
	 * @param  $params
	 * @return array, a list of related media data
	 */
	// OUTDATED!
	public function getAllMedia($params = array()) {
		$defaultParams = array();
		$params = array_merge($defaultParams, $params);
		
		// due to backwards compatibility
		if (!$params['postId']) {
			$params['postId'] = $params['postID'];
		}
		$params['postId'] = intval($params['postId']);
		
		return $this->mediaBaseHandler->getMediaByPost($params['postId']);
		
// 		$query = "SELECT media.*, types.media_type_title, types.media_type_name_de FROM ".$this->mediaTable." media JOIN ".$this->mediaTypesTable." types ON (media.media_type=types.id) WHERE media.media_post_id ='".$params['postID']."' ORDER BY media.media_position";
// 		$this->db->query($query);
// 		$postMedia = array();
// 		$hasMedia = array();
// 		$inf = $this->db->getCurrentFieldInfo();
// 		$this->contentFieldTypes = $inf['type'];
// 		while ($res = $this->db->get()) {
			
// 			$res = $this->formatFieldTypes($res);
		
// 			$postMedia[] = $res;
// 		}
		
// 		return $postMedia;
	}

	/**
	 * public function getMedia()
	 * get media data by ID
	 * 
	 * @param array $params Paramter als assoziatives Array:
	 * - mediaID => ID des Mediums (integer)
	 * 
	 * @return array Datensatz des Mediums als assoziatives Array
	 */
	public function getMedia($params = array()) {
		
		$defaultParams = array();
		$params = array_merge($defaultParams, $params);

		// due to backwards compatibility
		if (!$params['mediaId']) {
			$params['mediaId'] = $params['mediaID'];
		}
		$params['mediaId'] = intval($params['mediaId']);

		return $this->mediaBaseHandler->getMedia($params['postId']);

	}

	/**
	 * Download media file by id. Wrapper for mediaBaseHandler:.downloadMediaById()
	 * 
	 * @param number $mediaId
	 * @return void
	 */
	public function downloadMedia($mediaId) {
		return $this->mediaBaseHandler->downloadMediaById($mediaId);
	}
	
	
	public function getPostMedia($postId) {
		
		$return = array();
		$mediaOfPost = $this->getAllMedia(array('postId'=>$postId));
		$postMedia = array();
		$hasMedia = array();
		
		foreach($mediaOfPost as $media){
			$postMedia[$media['media_type_title']][$media['id']] = $media;
			$hasMedia['has' . $media['media_type_title']] += 1;
		}

		$return['hasMedia'] = $hasMedia;
		$return['postMedia'] = $postMedia;
		$return['commentsCount'] = $this->getCommentsCount($postId);

		$rating = $this->getRating($postId);
		$return['ratingNr'] = $rating['rating'];
		
		// TODO: rating calculation is strang!!
		$return['ratingScore'] = round($rating['avarage'], 1);
		
		return $return;
		
	}
	
	
	 /* public function getPostCategoryById()
 	 *  
 	 * Enter description here ...
 	 * @return array
 	 */
 	public function getPostBelongCategories($categoryId){
		
 		$return['post_categories'] = array();
		
		if(!$categoryId){
			return $return;
		}
		
		$postBelongsCategories = explode(",",$categoryId);
		
		foreach($postBelongsCategories as $categoryId){
			if(!intval(trim($categoryId))){
				continue;
			}
			
			$query = "SELECT * FROM mlog_category WHERE category_status=1 AND id='".$categoryId."'";

			$this->db->query($query);

			$res = $this->db->get();
			
			if($res){
				$res['category_title']= $res['category_title_'.$this->pageLanguage];
				$return['post_categories'][] = $res;
			}
			
		}
	
 		return $return;
 	}
	
	
	
	/**
	 * public function cleanUpMediaTable()
	 * Delete all temporery saved media from media table.
	 * 
	 * TODO: Auch die Dateien müssen gelöscht werden!!!
	 * 
	 * @param void
	 * @return void
	 */
// 	public function cleanUpMediaTable($postID) {
// 		$this->db->query("DELETE FROM " . $this->mediaTable . " WHERE media_post_id='" . intval($postID) . "' AND media_is_active = '0' ");
// 	}

	/**
	 * public function saveMedia()
	 * Speichert einen bestehenden oder neuen Medieneintrag.
	 * 
	 * @param array $params Assoziatives Array mit den vom Upload-Formular übergebenen Daten/ Inhalten des Mediums
	 * @return array Übergebene Parameter, falls Neueintrag wird die neue ID des Media-Eintrags aus der Datenbank auch mitübergeben.
	 */
	public function saveMedia($params = array()) {
		
		$excludeFields = array('id', 'redirect');
		$defaultParams = array();
		$params = array_merge($defaultParams, $params);

		$queryPart = $this->makeSetQuery($params, $excludeFields);

		if ($params['id']) {
			$query = "UPDATE " . $this->mediaTable . " SET " . $queryPart . " WHERE id='" . intval($params['id']) . "'";
		} else {
			$query = "INSERT INTO " . $this->mediaTable . " SET " . $queryPart . ", media_is_active = '0' ";
		}

		$this->db->query($query);

		if (!$params['id']) {
			$params['id'] = $this->db->getLastInsertedId();
		}

		return $params;
	}

	public function uploadMediaFile() {
		
	}

	/**
	 * public function deleteMedia()
	 * 
	 * delete media
	 * @param array
	 * @return bolean
	 */
// 	public function deleteMedia($params = array()) {
// 		$defaultParams = array();
// 		$params = array_merge($defaultParams, $params);
// 		if (!$params['mediaID']) {
// 			return false;
// 		}

// 		$deleted = $this->db->query("DELETE FROM " . $this->mediaTable . " WHERE id='" . $params['mediaID'] . "' ");

// 		if ($deleted) {
// 			return false;
// 		} else {
// 			return true;
// 		}
// 	}

	/**
	 * public function restorePostsMediaAfterAbort()
	 * Aktiviert die Medieneinträge, die event. in einem Post "gelöscht" wurden nachdem die Bearbeitung des Posts abgebrochen wurde.
	 *
	 * @param number $postID ID des Posts.
	 * @return void()
	 */
	public function restorePostsMediaAfterAbort($postID) {

		$postID = intval($postID);

		$this->db->query("
			UPDATE " . $this->mediaTable . " 
			SET media_is_active = '1' 
			WHERE media_post_id='" . $postID . "' AND media_is_active = '0'"
		);
	}

	/**
	 * public function activateMedia()
	 * 
	 * delete media
	 * @param array
	 * @return bolean
	 */
// 	public function activateMedia($params = array()) {

// 		$defaultParams = array();
// 		$params = array_merge($defaultParams, $params);

// 		if (!$params['mediaID']) {
// 			return false;
// 		}

// 		$activated = $this->db->query("UPDATE " . $this->mediaTable . " SET media_is_active = '1' WHERE id='" . intval($params['mediaID']) . "' ");

// 		if ($activated) {
// 			return false;
// 		} else {
// 			return true;
// 		}
// 	}

	/**
	 * public function deactivateMedia()
	 * 
	 * delete media
	 * @param array
	 * @return bolean
	 */
// 	public function deactivateMedia($params = array()) {
// 		$defaultParams = array();
// 		$params = array_merge($defaultParams, $params);
// 		if (!$params['mediaID']) {
// 			return false;
// 		}

// 		$deactivated = $this->db->query("UPDATE " . $this->mediaTable . " SET media_is_active = '0' WHERE id='" . intval($params['mediaID']) . "' ");

// 		if ($deactivated) {
// 			return false;
// 		} else {
// 			return true;
// 		}
// 	}

	/*	 * ************************************
	 * General use functions
	 * ************************************ */

	/**
	 * protected function dbDateFormat()
	 * 
	 * Enter description here ...
	 * @param $date
	 */
	protected function dbDateFormat($date) {
		$date = date('Y-m-d H:i:s', strtotime($date));
		return $date;
	}

	/**
	 * public function makeSetQuery()
	 * 
	 * @param array $dbData Ein assoziatives Array mit Datenbankdaten (Schlüssel = Feldname, Wert = Feldwert) 
	 * @return string Teil einer Datenbank-Query zur Verwendung nach dem "SET" (myField1='1', myField2='bla'...) 
	 */
	public function makeSetQuery($dbData, $excludeFields = array()) {
		if (!is_array($dbData)) {
			return '';
		}
		$queryParts = array();
		foreach ($dbData as $fieldName => $fieldValue) {
			if (in_array($fieldName, $excludeFields)) {
				continue;
			}

			if ($fieldName) {
				$queryParts[] = $fieldName . "='" . $this->db->dbQuote($fieldValue) . "'";
			}
		}
		return implode(', ', $queryParts);
	}

	/**
	 * public function formatDateForDB()
	 * Formatiert eine Datumsangabe entsprechend eines Formatierschemas um.
	 *
	 * @param string $date Datumsangabe, z.B. "24.12.2012"
	 * @param string $dateFormat Format der Datumsangabe, in diesem Fall z.B. "dd.mm.yyyy"
	 * @param string $convertToFormat Zielformat des Datums, z.B. "yyyy-mm-dd"
	 *
	 * @return string Zum Zielformat umformatierte Datumsangabe.
	 * 
	 * TODO: Uhrzeiten auch implementieren!
	 */
	public function formatDateForDB($date, $dateFormat, $convertToFormat = 'yyyy-mm-dd') {

		// Datumsangaben umformatieren
		$dateFormat = strtolower($dateFormat);
		$yearPos = strpos($dateFormat, 'yyyy');
		$monthPos = strpos($dateFormat, 'mm');
		$dayPos = strpos($dateFormat, 'dd');

		$year = substr($date, $yearPos, 4);
		$month = substr($date, $monthPos, 2);
		$day = substr($date, $dayPos, 2);

		$newDate = str_replace(array('yyyy', 'mm', 'dd'), array($year, $month, $day), $convertToFormat);

		return $newDate;
	}
	
	public function splittDatetime($date, $fieldName) {
		
		$d = array();
		preg_match('/^(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?/', $date, $match);
		
		$d[$fieldName .'_year'] = $match[1];
		$d[$fieldName .'_month'] = $match[2];
		$d[$fieldName .'_day'] = $match[3];
		$d[$fieldName .'_hour'] = $match[5];
		$d[$fieldName .'_minute'] = $match[6];
		$d[$fieldName .'_second'] = $match[7];

		return $d;
	}

	/**
	 * public function getCommentsCount();
	 * Liefert die Anzahl der Kommentare zurück
	 * 
	 * @params number $postID ID des Artikels
	 * @params string $postsTable Optionaler Datenbankname der Artikeltabelle. Falls nicht angegeben, wird 'mlog_comments' verwendet
	 * 
	 * @return number Anzahl der ferigeschalteten Kommentare zu diesem Artikel
	 */
	public function getCommentsCount($postID, $postsTable = 'mlog_comments') {

		$this->db->query("SELECT COUNT(id) as comments FROM " . $this->db->dbQuote($postsTable) . " 
						  WHERE comment_aid = '" . intval($postID) . "' 
						  AND comment_approved = '1'");
		$r = $this->db->get();

		return intval($r['comments']);
	}
	
	/**
	 * public function getRating();
	 * Liefert die Anzahl der Kommentare zurück
	 * 
	 * @params number $postID ID des Artikels
	 * @params string $postsTable Optionaler Datenbankname der Artikeltabelle. Falls nicht angegeben, wird 'mlog_comments' verwendet
	 * 
	 * @return number Anzahl der ferigeschalteten Kommentare zu diesem Artikel
	 */
	public function getRating($postID, $postsTable = 'cmt_rating') {

return array();
// TODO: new cmt_rating table has field rating_entry_id and rating_table_id for global usage

// 		$this->db->query("SELECT COUNT(id) as rating, AVG(rating_value) as avarage FROM " . $this->db->dbQuote($postsTable) . " 
// 						  WHERE rating_row_id = '" . intval($postID) . "'  ");
// 		$r = $this->db->get();

// 		return $r;
	}
	
	public function getPostsTablename() {
		return $this->postsTable;
	}
	
	
}
?>
