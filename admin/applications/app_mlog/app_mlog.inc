<?php
/**
 * app_mlog.inc
 * Application "MLog" to show and edit blog posts. 
 * 
 * @author A. Al Kaissi, J.Hahn <info@contentomat.de> 
 * @version 2016-03-21
 * @package MLog
 */
namespace Contentomat\MLog;

use Contentomat\ApplicationController;
use Contentomat\PsrAutoloader;
use Contentomat\Feed\Feed;
use Contentomat\Contentomat;

Class MLogController extends ApplicationController {
	
	protected $postId;
	protected $mediaId;
	protected $mediaType;
	protected $availableMediaTypes;
	protected $posts;
	protected $tags;
	protected $feed;
	protected $postCategories;
	protected $mlogSettings;
	protected $mediaSettings = array();
	protected $mediaBaseHandler;
	
	
	public function init() {

		$this->posts = new Posts();
		$this->tags = new Tags();
		$this->mediaBaseHandler = new MediaBaseHandler();
		$this->feed = new Feed();

		// use  with $parser::getTemplate()!
		$this->templatesPath = 'app_mlog/';
		$this->postId = intval($_REQUEST['id'][0]);
		
		$this->mediaId = intval(trim($_REQUEST['mediaId']));
		$this->mediaType = trim($_REQUEST['mediaType']);
		
		// get all allowed media types
		$this->availableMediaTypes = $this->mediaBaseHandler->getAvailableMediaTypes();
		
		// get all post categories
		$this->postCategories = $this->posts->getPostsCategories();
		
		// get MLog Settings
		// TODO: integrate in $cmtSettings!
		// currently only the feeds settings are stored in the mlog_settings.inc. Therefore move the method to Class Feeds!
//		$this->mlogSettings = $this->posts->getMlogSettings();
		$this->mlogSettings = array();
		$this->mediaSettings = $this->mediaBaseHandler->getAllMediaSettings();
		
		$this->parser->setParserVar('postId', $this->postId);
		$this->parser->setParserVar('mediaId', $this->mediaId);
		
	}
	
	protected function initActions($action='') {
		
		parent::initActions();

		$performedAction = $_REQUEST['action_performed'];
		
		if (in_array($performedAction, array('new', 'edit','duplicate'))) {
			$this->action = $performedAction . 'Save';
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Contentomat\ApplicationController::actionDefault()
	 */
	public function actionDefault() {
		
		$query = '';
		if (!$_REQUEST['sort_by'][1]) {
			$query = " ORDER BY post_online_date ";
		}
		
		if (!$_REQUEST['sort_dir'][1]) {
			$query .= " DESC ";
		}
		$this->cmt->setVar('cmtAddQuery', $query);
	}

	/**
	 * function actionEdit()
	 * Called when an existing post entry is edited.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionEdit() {
	
		$postMediaContent = $this->getPostMedia();
		$this->cmt->setVar('postMediaContent', $postMediaContent);
	}

	/**
	 * function actionEditSave()
	 * Called when an existing post entry is saved in edit view.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionEditSave() {
		
		$postId = intval($_REQUEST['edited_id']);

		// Delete deleted media
		$this->deleteDeletedMedia($postId);
		
		$postMedia = $this->mediaBaseHandler->getMediaByPost($postId);

		foreach($postMedia as $media) {
			$this->mediaBaseHandler->markMediaAsSaved($media['id']);
			$this->mediaBaseHandler->activateMedia($media['id']);
		}
		
		// save media positions (maybe outdated in future)
		$this->saveMediaPositions($postId);
		
		// save tags
		$postData = $this->posts->getPostById($postId);

		$this->tags->saveTags(array(
			'tags' => $postData['post_tags'],
			'addFields' => array(
				'tag_creation' => date('Y-m-d H:i:s')
			)
		));
		
		// create RSS feeds
		$this->createFeeds();
		
		// finally proceed default action
		$this->changeAction('default');
	}

	/**
	 * function actionAbortEdit()
	 * Called when the editing of an existing post entry is aborted.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionAbortEdit() {
	
		$postId = intval($_REQUEST['edited_id']);
	
		// Delete discarded media
		$this->deleteDiscardedMedia($postId);
		
		$this->mediaBaseHandler->resetMediaByPost($postId);

	}
	
	/**
	 * function actionNew()
	 * Called before a post entry is created.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionNew() {
		
		$this->changeAction('edit');		
	}

	/**
	 * function actionNewSave()
	 * Called when a new post entry is finally saved.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionNewSave() {
	
		$postId = intval($_REQUEST['edited_id']);
		$post = $this->posts->getPostById($postId);
		
		$mediaIds = explode(',', $post['post_media_positions']);
		$pos = 1;

		foreach ($mediaIds as $mediaId) {
			$this->mediaBaseHandler->updateMediaData(array(
				'mediaId' => $mediaId,
				'mediaData' => array (
					'media_post_id' => $postId,
					// 'media_position' => $pos++
				)
			));
			
			$this->mediaBaseHandler->markMediaAsSaved($mediaId);
			$this->mediaBaseHandler->activateMedia($mediaId);
			
		}
		
		$this->deleteDeletedMedia($postId);
		
		$this->saveMediaPositions($postId);
		
		// create RSS feeds
		$this->createFeeds();
		
		// finally proceed default action
		$this->changeAction('default');
	}

	/**
	 * function actionAbortNew()
	 * Called when the editing of a new post entry is aborted.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionAbortNew() {
	
		$mediaIds = explode(',', $_REQUEST['deleteMediaIds']);
	
		foreach ($mediaIds as $mediaId) {
				
			$mediaData = $this->mediaBaseHandler->getMedia($mediaId);
			
			if (empty($mediaData)) {
				continue;
			}
			$mediaType = $mediaData['mediaType'];
				
			$mediaHandlerName = __NAMESPACE__ . '\Media' . ucfirst($mediaType) . 'Handler';
			$mediaHandler = new $mediaHandlerName();
				
			$mediaHandler->setMediaSettings($this->mediaSettings[$mediaType]);
			$mediaHandler->deleteMedia($mediaId);
				
		}
	
	}
	

	// TODO!!!
	protected function actionDuplicate() {
		
	}

	/**
	 * function actionAbortDuplicate()
	 * Called when the editing of a freshly duplicated post entry is aborted.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionAbortDuplicate() {
		$this->changeAction('abortEdit');
	}

	
	/**
	 * function actionAbortDuplicate()
	 * Called when an existing post entry is deleted finally.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDelete() {
		
		$postIds = (array)$_REQUEST['id'];
	
		foreach ($postIds as $postId) {
			
			$postId = intval($postId);
			$mediaData = $this->mediaBaseHandler->getMediaByPost($postId);
			
			foreach($mediaData as $media) {
				
				$mediaType = $media['mediaType'];
				
				$mediaHandlerName = __NAMESPACE__ . '\Media' . ucfirst($mediaType) . 'Handler';
				$mediaHandler = new $mediaHandlerName();
				
				$mediaHandler->setMediaSettings($this->mediaSettings[$mediaType]);
				$mediaHandler->deleteMedia($media['id']);
					
			}
			
		}
		
		// create RSS feeds
		$this->createFeeds();
	}
	
	/**
	 * function actionDeleteMultiple()
	 * Called when multiple existing post entries are deleted finally.
	 *
	 * @param void
	 * @return void
	 *
	 */
	protected function actionDeleteMultiple() {
		$this->changeAction('delete');
	}
	
	/**
	 * protected function actionMlogNewMedia()
	 * Add a media element to the post.
	 *
	 * @param void
	 * @return void
	 */
	protected function actionMlogNewMedia() {
		
		$this->isAjax = true;
		
		$this->parser->setParserVar('articleRelatedContent', $articleRelatedContent);
		$this->parser->setParserVar('mediaType', $this->mediaType);
		$this->parser->setParserVar('mediaId', '');
		
		$mediaType = '';
		
		foreach ($this->availableMediaTypes as $type) {
			if ($this->mediaType == $type['mediaType']) {
				$mediaType = $type['mediaType'];
				$this->parser->setParserVar('mediaTypeId', $type['id']);
			}
		}
		
		$this->parser->setParserVar('allowedMediaTypes', implode(', ', (array)$this->mediaSettings[$mediaType]['media_type_file_types']));
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_mlog_media_edit.tpl');
	}
	
	/**
	 * protected function actionMlogSaveMedia()
	 * Save a currently added media file
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionMlogSaveMedia() {

		$mediaType = $_REQUEST['mediaType'];
		$uploadOptions = $this->mediaSettings[$mediaType];

		$handlerName = __NAMESPACE__ . '\Media' . ucfirst($mediaType) . 'Handler';
		$mediaHandler = new $handlerName();

		$mediaHandler->setMediaSettings($uploadOptions);
		$mediaData = $mediaHandler->saveMedia();

		$this->parser->setMultipleParserVars($mediaData);

		$this->parser->setParserVar('mediaPath', $this->cmt->getPathToWebRoot() . $uploadOptions['media_type_file_path']);

		$mediaContent = $this->parser->parseTemplate($this->templatesPath . 'cmt_mlog_media.tpl');

		$data = new \stdClass();

		$data->mediaContent = base64_encode($mediaContent);

		if ($media['error']) {
			$data->errorMessage = base64_encode($errorMessage);
		}

		$data->mediaId = $mediaData['id'];
		$data->mediaIsNew = $mediaData['mediaIsNew'];

		echo json_encode($data);
		exit;
		
	}

	/**
	 * protected function actionMlogDeleteMedia()
	 * Mark(!) a post's media as delelted (don't delete it right now)
	 *
	 * @param void
	 * @return void
	 *
	 */	
	protected function actionMlogDeleteMedia() {
		$checkDeactivation = $this->mediaBaseHandler->markMediaToDelete($this->mediaId);

		$this->isAjax = true;
		$this->isJson = true;
		
		$this->content = array(
			'actionResult' => intval($checkDeactivation),
			'mediaId' => $this->mediaId
		);		
	}
	
	/**
	 * protected function actionMlogEditMedia()
	 * Edit an existing media entry
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function actionMlogEditMedia() {
		
		$this->isAjax = true;

		$media = $this->mediaBaseHandler->getMedia($this->mediaId);

		$this->parser->setMultipleParserVars($media);
		$this->parser->setParserVar('postId', $media['media_post_id']);
		$this->parser->setParserVar('action', 'mlogSaveMedia');
		
		$mediaType = $media['mediaType'];
		$this->parser->setParserVar('allowedMediaTypes', implode(', ', (array)$this->mediaSettings[$mediaType]['media_type_file_types']));
		
		$this->content = $this->parser->parseTemplate($this->templatesPath . 'cmt_mlog_media_edit.tpl');
	}
	
	/**
	 * protected function saveMediaPositions()
	 * Save all media positions of a post
	 * 
	 * @param unknown $postId
	 */
	protected function saveMediaPositions($postId) {
		
		$postId = intval($postId);
		$post = $this->posts->getPostById($postId);

		$mediaPositions = explode(',', $post['post_media_positions']);

		if (!empty($mediaPositions)) {
			$this->mediaBaseHandler->setMediaPositions($mediaPositions);
		}
		
	}
	
	
	/**
	 * function getPostMedia()
	 * 
	 * @param number $postId
	 * @return unknown
	 */
	protected function getPostMedia($postId=0) {
		
		if (!intval($postId)) {
			$postId = $this->postId;
		}
		
		$mlogViewContent = '';
		
		$mediaType = $this->mediaBaseHandler->getAvailableMediaTypes();
		
		
		// 1. parse media content
		$allMedia = $this->mediaBaseHandler->getMediaByPost($postId);
		$postMediaContent = '';
		$mediaTemplate = $this->parser->getTemplate($this->templatesPath . 'cmt_mlog_media.tpl');
	
		foreach ((array)$allMedia as $media) {

			$mediaTypeId = $media['media_type'];
			$mediaType = $this->availableMediaTypes[$mediaTypeId]['mediaType'];
			$mediaTypeName = $this->availableMediaTypes[$mediaTypeId]['mediaTypeName'];

			$mediaOptions = $this->mediaSettings[$mediaType];

			$this->parser->setMultipleParserVars($media);

			$this->parser->setParserVar('mediaPath', PATHTOWEBROOT . $mediaOptions['media_type_file_path']);
			
			$postMediaContent .= $this->parser->parse($mediaTemplate);
		}

		$this->parser->setParserVar('postMediaContent', $postMediaContent);
	
		// 2. create media type selection
		$mediaTypeListContent = '';
	
		$this->parser->setParserVar('availableMediaTypes', $this->availableMediaTypes);
		$mlogViewContent = $this->parser->parseTemplate($this->templatesPath . 'cmt_mlog_media_frame.tpl');
		
		return $mlogViewContent;
		
	}
	
	protected function deleteDeletedMedia($postId) {
		
		$postId = intval($postId);
		
		if (!$postId) {
			return false;
		}
		
		// delete all uploaded media
		$mediaToDelete = $this->mediaBaseHandler->getDeletedMedia($postId);

		foreach ($mediaToDelete as $mediaId => $media) {
		
			$mediaTypeData = $this->mediaBaseHandler->getMediaTypeById($media['media_type']);
			$mediaType = $mediaTypeData['mediaType'];
		
			$mediaHandlerName = __NAMESPACE__ . '\Media' . ucfirst($mediaType) . 'Handler';
		
			$mediaHandler = new $mediaHandlerName();
			$mediaTypeSettings = $this->mediaSettings[$mediaType];
			$mediaHandler->setMediaSettings($mediaTypeSettings);
		
			$mediaHandler->deleteMedia($mediaId);
		
		}
		
		return true;
	}

	protected function deleteDiscardedMedia($postId) {
	
		$postId = intval($postId);
	
		if (!$postId) {
			return false;
		}
	
		// delete all uploaded media
		$mediaToDelete = $this->mediaBaseHandler->getDiscardedMedia($postId);
	
		foreach ($mediaToDelete as $mediaId => $media) {
	
			$mediaTypeData = $this->mediaBaseHandler->getMediaTypeById($media['media_type']);
			$mediaType = $mediaTypeData['mediaType'];
	
			$mediaHandlerName = __NAMESPACE__ . '\Media' . ucfirst($mediaType) . 'Handler';
	
			$mediaHandler = new $mediaHandlerName();
			$mediaTypeSettings = $this->mediaSettings[$mediaType];
			$mediaHandler->setMediaSettings($mediaTypeSettings);
	
			$mediaHandler->deleteMedia($mediaId);
	
		}
	
		return true;
	}
	
	/**
	 * protected function createFeeds()
	 * Main method for feed creation.
	 * 
	 * @param void
	 * @return void
	 *
	 */
	protected function createFeeds() {
		
		$feeds = $this->feed->getFeedsForTable($this->posts->getPostsTablename());

		foreach ($feeds as $feed) {
			
			$posts = $this->posts->getAllPosts(array(
				'orderDir' => 'DESC',
				'orderBy' => $feed['feed_order_by'],
				'postsPerPage' => $feed['feed_entries'],
				'feedId' => $feed['id']
			));
			
			$posts = $this->preparePostsDataForFeed($posts);

			$feedText = $this->feed->createFeed(array(
				'items' => $posts,
				'templatePath' => $this->cmt->getPathToWebRoot() . $feed['feed_template_path'],
				'feedVars' => array_merge($feed, array(
					'channelPublicationDate' => $this->feed->convertDate(date('Y-m-d H:i:s')),
					'channelTitle' => $feed['feed_name'],
					'channelDescription' => $feed['feed_description']
				))
			));
			
			$feedFile = fopen($this->cmt->getPathToWebRoot() . $feed['feed_path'], 'w');
			
			$check = fputs($feedFile, $feedText);

		}
	}
	
	/**
	 * protected function preparePostsDataForFeed()
	 * Helper: Prepares the MLog post data for usage in the RSS feed template (e.g. creates date strings and gets category names).
	 * 
	 * @param array $posts Multidimensional array, contains all posts' data
	 * @return array Extended multidimensional array with posts' data 
	 */
	protected function preparePostsDataForFeed($posts) {
		
		foreach ($posts as $key => $post) {
			
			$post['itemTitle'] = $post['post_title'];
			$post['itemDescription'] = $post['post_teaser'];
			$post['itemImageUrl'] = $post['post_image'];
			$post['itemPublicationDate'] = $this->feed->convertDate($post['post_online_date']);
			
			// generate Link
			$mainCategoryId = intval(array_shift(explode(',', $post['post_category'])));
			$post['fileName'] = $this->cmt->makeNameWebsave($post['post_title']);
			
			// get main/ first category. TODO: get all categories of a single post
			$categoryData = $this->posts->getCategoryData($mainCategoryId);

			$post['categoryId'] = $mainCategoryId;
			$post['itemCategories'] = $categoryData['category_title_de'];
			$post['host'] = $_SERVER['HTTP_HOST'];

			$posts[$key] = $post;
		}
		
		return $posts;
	}
	
}
$cmt = \Contentomat\Contentomat::getContentomat();
$cmt->addAutoloadNamespace('\Contentomat\MLog', PATHTOADMIN . 'classes/app_mlog/');
$cmt->addAutoloadNamespace('\Contentomat\Feed', PATHTOADMIN . 'classes/');


// $autoloader = new \Contentomat\PsrAutoloader();
// $autoloader->addNamespace('\Contentomat\MLog', PATHTOADMIN . 'classes/app_mlog/');
// $autoloader->addNamespace('\Contentomat\Feed', PATHTOADMIN . 'classes/');

$MLogController = new MLogController();
$MLogController->work();

?>