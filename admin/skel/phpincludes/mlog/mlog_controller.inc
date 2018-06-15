<?php
namespace Contentomat\MLog;

use \Contentomat\PsrAutoloader;
use \Contentomat\Controller;
//use \Contentomat\DBCex;
use \Contentomat\Contentomat;
use \Contentomat\Parser;
use \Contentomat\Paging;


/**
 * Class mlogView .. Local Class extends Controller 
 */
class MLogController extends Controller {

	protected $postId;
	protected $categoryId;
	protected $categoryData;
	protected $searchText;
	protected $searchIn;
	protected $mlog;
	protected $posts;
	protected $comments;
	protected $tags;
	protected $mlogTemplatePath;
	protected $commentsTemplatePath;
// 	protected $adminActions;
// 	protected $actionHash;
// 	protected $adminEmail;
// 	protected $commentVars;
// 	protected $showNewCommentForm;
// 	protected $commentsFieldsWrapper;
	protected $currentPage;
	protected $entriesPerPage = 5;
	protected $pagingLinks = 10;
	protected $authors;
	protected $relations;
	
	protected $overviewPageId = 3;
	protected $detailsPageId = 3;
	protected $categorySeparator = ' / ';
	
	protected $cmt;
	
	protected $settings;
	protected $searchTags;
	protected $session;

	public function init() {

		$this->cmt = Contentomat::getContentomat();
		$this->session = $this->cmt->getSession();
		$this->posts = new Posts();
		$this->authors = new Authors();
		$this->tags = new Tags();
		$this->relations = new Relations();
		
		// get settings stored in cmt content element fields (head1 - head5, a.s.o.)
		$layoutObjectParams = $this->cmt->getVar('cmtContentData');
		foreach ($layoutObjectParams as $key => $value) {
			$this->settings[$key] = trim(strip_tags($value));
		}
	
		// templates path
		$this->mlogTemplatePath = $this->templatesPath . 'mlog/';
		$this->commentsTemplatePath = $this->templatesPath . 'comments/';

		$check = setlocale(LC_ALL, PAGELANG, PAGELANG . '_' . strtoupper(PAGELANG));
		
		// get post's id an decide if we are in details view
		$this->postId = intval($_REQUEST['aid']); // post id
		if ($this->postId) {
			$this->action = 'postDetails';
		}

		// get category's id
		$this->categoryId = intval($_REQUEST['cat']); // article category id
		
		if (!$this->categoryId) {
			$this->categoryId = intval($this->settings['head1']);
		}

		// Think about this: on first call of an overview page there is no category id in the URL. We need a default category!
		$this->categoryData = $this->posts->getCategoryData($this->categoryId);

		// handle tags
		$this->searchTags = (array)$this->session->getSessionVar('mlogSearchTags');
		$tag = trim($_REQUEST['tag']);
		if ($tag) {
			if (!in_array($tag, $this->searchTags)) {
				$this->searchTags[] = $tag;
			} else {
				// remove tag from stored search tags
				$key = array_search($tag, $this->searchTags);
				if ($key !== false) {
					unset($this->searchTags[$key]);
				}
			}
			$this->session->setSessionVar('mlogSearchTags', $this->searchTags);
		}
		
		if ($_REQUEST['resetTags']) {
			$this->searchTags = array();
			$this->session->setSessionVar('mlogSearchTags', array());
		}
//		$this->searchText = $_REQUEST['search'];
//		$this->searchIn = $this->getvars['searchIn']; // such begriff
//		$this->adminActions = $this->getvars['act'];
//		$this->actionHash = $this->getvars['hash'];
		
		$this->currentPage = intval($this->getvars['cp']);
		if (!$this->currentPage) {
			$this->currentPage = 1;
		}
		
		// TODO: check this: seems to conflict with parser because {VAR:currentPage} is '' 
		$this->parser->setParserVar('currentPage', $this->currentPage);
		
		// this works
		$this->parser->setParserVar('currentPageNr', $this->currentPage);
//		$this->showNewCommentForm = false;
//		$this->commentsFieldsWrapper = array('author', 'commentTitle', 'email', 'author_url', 'comment', 'notify', 'captchaVarName');
//		$this->errorMessage = array();

		// init main parts
//		$this->initActions();
		$this->initPosts();
//		$this->initRelations();
//		$this->initComments();
	}




	protected function initPosts() {
		
	}

	/**
	 * protected function actionShowPostsList()
	 * Shows a list of posts in the seleced category
	 * 
	 * @param void
	 * @return void
	 *
	 */
	public function actionDefault() {

		if ($this->categoryData){
			define('POSTSCATEGORY',': '.$this->categoryData['category_title_' . PAGELANG] );
		}


		// init search mode
		$condWhere = array();
		$searchFields = '';
		
		// search in tags
		if (!empty($this->searchTags)) {
			$searchFields = 'post_tags';
			$searchText = $this->searchTags; //implode(',', $this->searchTags);
			$searchType = 'set';
			$this->parser->setParserVar('activeTagsString', implode(',', $searchText));
		}
		
// 		$searchFields = 'post_text,post_tags,post_title';
// 		$rowTemplate = 'mlog_article_row.tpl';
// 		$frameTemplate = 'mlog_article_frame.tpl';

		// does a search term exist?
// 		if ($this->searchTextRequest != '') {
// 			$searchText = $this->searchTextRequest;
// 			$searchMode = true;
// 		}


// 		if ($this->searchIn != '') {

// 			switch ($this->searchIn) {
// 				case 'tags':
// 					$searchFields = 'post_tags';
// 					break;
// 				case 'authors':
// 					$searchFields = 'post_author';
// 					$condWhere[] = "post_author='" . $this->searchTextRequest . "'";
// 					break;
// 			}
// 		}

		$posts = $this->posts->search(array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			'condWhere' => $condWhere,
			"searchText" => $searchText,
//			"searchMode" => $searchMode,
			"searchFields" => $searchFields,
			"searchType" => $searchType,
			"teaserFieldPriority" => 'post_text',
			"postsPerPage" => $this->entriesPerPage,
			'pagingShowLinks' => $this->pagingLinks,
			'currentPage' => $this->currentPage,
			'categoryLimit' => $this->categoryId		// comma spearated category ids
		));

		$this->parser->setParserVar('postsTotal', count($posts));
		
		foreach ($posts as $key => $post) {
			$posts[$key] = $this->formatPostDataForOutput($post);
		}

		$this->parser->setParserVar('posts', $posts);
		
		// get tags
		$tags = $this->tags->getTagsList(array('categoryId' => $this->categoryId));
		$this->parser->setParserVar('tags', $tags);
		
//		$this->parser->setMultipleParserVars($this->mlog->paging);	// ???
//		$this->parser->setParserVar('lastCommentsList', $lastCommentsList);
//		$this->parser->setParserVar('search', $this->searchTextRequest);
//		$this->parser->setParserVar('searchIn', $this->searchIn);
//		$this->parser->setParserVar('categoryId', $this->categoryId);
		
		$this->parser->setParserVar('pagingContent', $this->createPaging());

		
		$template = 'mlog_overview.tpl';
		if ($this->settings['head2']) {
			$template = $this->settings['head2'];
		}
		
		$this->content = $this->parser->parseTemplate($this->mlogTemplatePath . $template);
	}

	/**
	 * protected function formatPostDataForOutput()
	 * Helper: Aggregate and format post's data 
	 * 
	 * @param array $post	Post data as delivered by class \Mlog\Posts
	 * @return array		Formatted and aggregated data in an array.
	 */
	protected function formatPostDataForOutput($post) {
		
		if (!is_array($post)) {
			return array();
		}
		$timestamp = strtotime($post['post_online_date']);
		$post['dateYear'] = strftime('%Y', $timestamp);
		
		$post['dateMonthLongName'] = strftime('%B', $timestamp);
		$post['dateMonthShortName'] = strftime('%b', $timestamp);
		$post['dateMonth'] = strftime('%m', $timestamp);
		
		$post['dateDayLongName'] = strftime('%A', $timestamp);
		$post['dateDayShortName'] = strftime('%a', $timestamp);
		$post['dateDay'] = strftime('%d', $timestamp);
		
		$post['dateHour'] = strftime('%H', $timestamp);
		$post['dateMinute'] = strftime('%M', $timestamp);
		
		// post categories
		$categories = array();
		$categoryNames = array();
		
		foreach ((array)$post['post_categories'] as $postCategory) {
			$categoryName = ucfirst($postCategory['category_title_de']);
			$this->parser->setParserVar('categoryName', $categoryName);
			
			$categories[] = $this->parser->parseTemplate($this->mlogTemplatePath . 'mlog_category_link.tpl');
			$categoryNames[] = $categoryName;
		}
		
		// get author data
		if ($post['post_author_id']) {
			
			$authorData = $this->authors->getAuthor(array('authorId' => $post['post_author_id']));
			$post['author_name'] = $authorData['author_name'];
			$post['author_firstname'] = $authorData['author_firstname'];
			$post['authorId'] = $authorData['id'];
			$post['authorLinkName'] = $this->cmt->makeNameWebsave($authorData['author_firstname'] . ' ' . $authorData['author_name']);
		}

		$post['categories'] = implode($this->categorySeparator, $categories);
		
		$post['post_text'] = preg_replace(
			array(
				'/src=(\'|")(\.\.\/)+/',
				'/(width=(\'|")[0-9]+(\'|"))/',
				'/(height=(\'|")[0-9]+(\'|"))/'
			), 
			array(
				'src=$1/',
				'',
				''
			),
			$post['post_text']
		);
		
		// at least parse a littel bit
		$post['post_text'] = $this->parser->parse($post['post_text']);
		$post['post_teaser'] = $this->parser->parse($post['post_teaser']);
		$post['post_title'] = $this->parser->parse($post['post_title']);
		$post['post_subtitle'] = $this->parser->parse($post['post_subtitle']);
		
		// more stuff
		$this->parser->setParserVar('categoryId', $this->categoryId);
		$this->parser->setParserVar('detailsPageId', $this->detailsPageId);
		
		return $post;		
		
	}
	
	/**
	 * protected function createPaging()
	 * Creates the paging for MLog overview
	 * 
	 * @param void
	 * @return void
	 */
	protected function createPaging() {
		$pagingContent = '';
		$paging = new Paging();
		$pagingData = $paging->makePaging(array(
			'pagingLinks' => $this->pagingLinks,
			'entriesPerPage' => $this->entriesPerPage,
			'totalEntries' => $this->posts->getTotalPosts(),
			'currentPage' => $this->currentPage
		));
		$pagingContent = '';
		$pagingTemplate = file_get_contents($this->mlogTemplatePath . 'mlog_paging.tpl');
		$pagingTemplate = explode("{SPLITTEMPLATEHERE}", $pagingTemplate);

		if ($this->categoryData && !empty($this->categoryData)) {
			$this->parser->setParserVar('pageId', $this->pageId);
			$this->parser->setParserVar('categoryId', $this->categoryData['id']);
			$this->parser->setParserVar('categoryName', ucfirst($this->categoryData['category_name']));
		} else {
			$this->parser->setParserVar('pageId', PAGEID);
			$this->parser->setParserVar('categoryId', '0');
			$this->parser->setParserVar('categoryName', '');
		}

		if ($pagingData['prevPage']) {
			$this->parser->setParserVar('prevPage', $pagingData['prevPage']);
			$pagingContent .= $this->parser->parse($pagingTemplate[0]);
		}

		foreach ($pagingData['pages'] as $page) {
			$this->parser->setParserVar('listItem', $page);

			$this->parser->setParserVar('selected', $page == $this->currentPage);
			$pagingContent .= $this->parser->parse($pagingTemplate[1]);
		}

		if ($pagingData['nextPage']) {
			$this->parser->setParserVar('nextPage', $pagingData['nextPage']);
			$pagingContent .= $this->parser->parse($pagingTemplate[2]);
		}
		
		return $pagingContent;
	}

	/**
	 * protected function actionPostDetails()
	 * Display a detail article.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionPostDetails() {


		$this->parser->setParserVar('search', $this->searchTextRequest);
		$this->parser->setParserVar('searchIn', $this->searchIn);
		
		$actionContent = '';

		// show article details
		$post = $this->posts->getPost(array('postID' => $this->postId));
		$post = $this->formatPostDataForOutput($post);
		
		// ??
		define('POSTTITLE', htmlspecialchars($post['post_title']));

		$this->categoryData = $this->posts->getCategoryData($this->categoryId);
		
// 		if($this->categoryData){
// 			define('POSTSCATEGORY',$this->categoryData['category_title_de'].' - ');
// 		}
		
		// handle post media
		foreach ((array)$post['postMedia'] as $mediaName => $mediaArray) {
	
			$this->parser->setParserVar('post'.ucfirst($mediaName), $mediaArray);
			$this->parser->setParserVar('has'.ucfirst($mediaName), true);
		}
		$this->parser->setMultipleParserVars($post['hasMedia']);
		$this->parser->setParserVar('postMedia', $post['postMedia']);

		// get Tags
		$tags = $this->tags->getTags(array('postID' => $this->postId));
		$postTags = array();
	
		foreach((array)$tags as $tag) {
			$postTags[] = array(
				'tag' => $tag,
				'tagLink' => ''
			);
		}
		$this->parser->setParserVar('postTags', $postTags);
		$this->parser->setParserVar('hasTags', (bool)count($postTags));
		
// 		switch ($post['post_options']) {
// 			case '1':
// 				$this->parser->setParserVar('mediaBarTop', 1);
// 				break;
// 			case '2':
// 				$this->parser->setParserVar('mediaBarBottom', 1);
// 				break;
// 			case '3':
// 				$this->parser->setParserVar('mediaBarTop', 1);
// 				$this->parser->setParserVar('mediaBarBottom', 1);
// 				break;
// 			default:
// 				break;
// 		}

		// Post belongs Categories
		$categoryBelongsContent = array();

		
		foreach ($post['post_categories'] as $postCategory) {
			$this->parser->setMultipleParserVars($postCategory);
			$this->parser->setParserVar('categoryName', ucfirst($postCategory['category_name']));
			$categoryBelongsContent[] = $this->parser->parseTemplate($this->mlogTemplatePath . 'mlog_category_link.tpl');
		}

		$this->parser->setParserVar('postCategories', join(", ", $categoryBelongsContent));

		if ($this->categoryData && !empty($this->categoryData)) {
			$this->parser->setParserVar('categoryId', $this->categoryData['id']);
			$this->parser->setParserVar('categoryInternalName', $this->categoryData['category_name']);
			$this->parser->setParserVar('categoryName', $this->categoryData['category_title_de']);
		} else {
			$this->parser->setParserVar('categoryId', 0);
			$this->parser->setParserVar('categoryInternalName', '');
			$this->parser->setParserVar('categoryName', '');
		}

		// get relations
		$relatedPosts = $this->posts->getRelatedPosts($this->postId);
		$this->parser->setParserVar('hasRelatedPosts', (bool)count($relatedPosts));
		$this->parser->setParserVar('relatedPosts', $relatedPosts);
		
		$this->parser->setMultipleParserVars($this->formatPostDataForOutput($post));

//		$this->parser->setParserVar('furtherInformation', $furtherInformation);


		// Parse Details Template
		$actionContent .= $this->parser->parseTemplate($this->mlogTemplatePath . 'mlog_article_details.tpl');



		// show comments
// 		if ($this->comments->commentable && $post['post_comment_status'] == '1') {

			
// 			$actionContent .= $this->comments->showCommentsList();

// 			if ($this->errorMessage[$post['id']]) {
// 				$actionContent .= $this->errorMessage[$post['id']];
// 			}
			
// 			if ($this->errorMessage['commentMessage']) {
// 				$actionContent .= $this->errorMessage['commentMessage'];
// 			}
			

// 			if ($this->showNewCommentForm) {
// 				$this->parser->setMultipleParserVars($this->commentVars);
// 				$this->parser->setParserVar('articleId', $post['id']);
// 				$actionContent .= $this->parser->parseTemplate($this->commentsTemplatePath . 'comments_form.tpl');
// 			} else {
// 				$this->parser->setParserVar('articleId', $commentPostId);
// 				// $actionContent .= $this->parser->parseTemplate($this->commentsTemplatePath . 'comments_confirm.tpl');
// 			}
// 		}

		$this->content = $actionContent;
		
		// update Article's total views status
		$this->posts->updatePostStatus($this->postId);
		
	}

	

	// TODO: refactor!
	// 	protected function initRelations() {
	// 		if (!$this->postId) {
	// 			return false;
	// 		}
	
	// 		$this->relations = new Relations();
	
	// 		// table id => shortname
	// 		$this->relations->tableTypWrapper = array(
	// 			"25" => "mlog"
	// 		);
	// 		$this->postRelations = $this->relations->getRelations($this->pageId, $this->postId, 'mlog_posts', 'post_relations');
	// 	}
	
	
	
	// TODO: refactor!
	// 	protected function initComments() {
	
	// 		$commentPostId = intval($this->postvars['comment_post_ID']);
	
	// 		foreach ($this->commentsFieldsWrapper as $field) {
	// 			$this->commentVars[$field] = strip_tags($this->postvars[$field]);
	// 		}
	// 		$this->commentVars['comment_post_ID'] = $commentPostId;
	
	// 		// comments instance
	// 		$this->comments = new Comments();
	
	// 		// the default avatar for anonymouse users comments
	// 		$default_avatar_path = 'http://' . $_SERVER['HTTP_HOST'] . '/img/default_avatar.png';
	
	// 		// admin email, used to notification or activation
	// 		//$this->adminEmail = "test@buero-hahn.de";
	
	// 		// comments needing admin activation
	// 		$commentsRequestAdminActivation = false;
	
	// 		//default show add new comment form
	// 		$showNewCommentForm = true;
	
	// 		$showCommentsInListView = false;
	
	
	// 		$this->comments->init(
	// 				array(
	// 					"comments_table" => "mlog_comments",
	// 					"page_id" => $this->pageId,
	// 					"article_id" => $this->postId,
	// 					"template_row" => $this->commentsTemplatePath . 'comments_row.tpl',
	// 					// "template_frame" => $this->commentsTemplatePath . 'comments_frame.tpl',
	// 					"default_avatar_path" => $default_avatar_path,
	// 					"commentsRequestAdminActivation" => $commentsRequestAdminActivation,
	// 		));
	
	// 		// admin actions to delete or activate comments with one click link
	
	// 		if ($this->postvars['submit_form']) {
	// 			$this->saveComment();
	// 		}
	
	// 		if ($this->adminActions == 'activate' && $this->actionHash) {
	// 			$this->activateComment();
	// 		}
	
	// 		if ($this->adminActions == 'delete' && $this->actionHash) {
	// 			$this->deleteComment();
	// 		}
	
	// 		if($this->comments->errorNr){
	// 			$this->parser->setParserVar('errorNr',$this->comments->errorNr);
	// 			$this->errorMessage['commentMessage'] = $this->parser->parseTemplate($this->commentsTemplatePath.'error_messages.tpl');
	// 		}
	
	// 	}
	
	
	// TODO: refactor!
	// 	protected function saveComment() {
	
	// 		if ($this->postvars['submit_form']) {
	// 			$check = $this->comments->isValid($this->commentVars);
		
	// 			if ($check) {
	
	// 				// notify admin about new comment, to activated or delete it (activate only if $commentsRequestAdminActivation is true)
	// 				$this->comments->notifyAdministrator(array("adminEmail" => $this->adminEmail, "template" => $this->commentsTemplatePath . 'notify_admin.tpl'));
	
	// 				// notify authors about comment answers if you checked the option (notify) in form
	// 				$this->comments->notifyFollowers(array("userEmail"=>$this->commentVars['email'],"template" => $this->commentsTemplatePath . 'notify_followers.tpl'));
	
	// 				$this->showNewCommentForm = false;
	
	// 				$this->comments->resetCaptcha();
	
	// 				$this->errorMessage = array();
	
	// 			} else {
	// 				$this->parser->setParserVar('errorNr', $this->comments->errorNr);
	// 				$this->parser->setMultipleParserVars($this->postvars);
	// 				$this->errorMessage['commentMessage'] = $this->parser->parseTemplate($this->commentsTemplatePath . 'error_messages.tpl');
	// 			}
	
	// 		}
	// 	}
	
	// TODO: refactor!
	/*
	 * one click link, activate comment by clicking activate link sended to admin
	 *
	 */
	
	// 	protected function activateComment() {
	// 		$this->comments->activateComment(array('hash' => $this->actionHash));
	// 	}
	
	// TODO: refactor!
	/*
	 *  one click link, delete comment by clicking a link sended to admin
	 */
	// 	protected function deleteComment() {
	// 		$this->comments->deleteComment(array('hash' => $this->actionHash));
	// 	}
}

$autoLoad = new PsrAutoloader();
$autoLoad->addNamespace('Contentomat', INCLUDEPATH . 'phpincludes/classes');
$autoLoad->addNamespace('Contentomat\MLog', INCLUDEPATH . 'phpincludes/classes');
$autoLoad->addNamespace('Contentomat\MLog', INCLUDEPATHTOADMIN . 'classes/app_mlog');

$controller = new MLogController();
$content .= $controller->work();
?>

