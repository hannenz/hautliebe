<?php
namespace Contentomat\MLog;

use \Contentomat\PsrAutoloader;
use \Contentomat\Controller;
//use \Contentomat\DBCex;
use \Contentomat\Contentomat;
use \Contentomat\Parser;
//use \Contentomat\Paging;
use \Contentomat\MLog\MLogController;

// sucks sucks sucks
require_once(PATHTOWEBROOT.'phpincludes/mlog/mlog_controller.inc');

class MLogWidgetController extends \Contentomat\MLog\MLogController {

	protected $categoryId = 1;
	protected $posts;
	protected $template = 'mlog_latest.tpl';
	protected $showPosts = 1;
	protected $detailPageId = 10;
	

	/**
	 * protected function actionShowPostsList()
	 * Shows a list of posts in the seleced category
	 * 
	 * @param void
	 * @return void
	 *
	 */
	public function actionDefault() {

		$this->parser->setParserVar('mlogDetailsPageId', $this->detailPageId);
		
		$posts = $this->posts->search(array(
			'orderDir' => 'DESC',
			'orderBy' => 'post_online_date',
			"postsPerPage" => $this->showPosts,
			'currentPage' => 1,
			'categoryLimit' => $this->categoryId
		));

		foreach ((array)$posts as $key => $post) {
			$posts[$key] = $this->formatPostDataForOutput($post);
// 			// Post belongs Categories
// 			$categoryBelongsContent = array();
			
// 			foreach ($post['post_categories'] as $postCategory) {
// 				$this->parser->setMultipleParserVars($postCategory);
// 				$this->parser->setParserVar('categoryName', ucfirst($postCategory['category_name']));
// 				$categoryBelongsContent[] = $this->parser->parseTemplate($this->mlogTemplatePath . 'mlog_category_link.tpl');
// 			}

// 			$post['categoryId'] = intval($post['post_categories'][0]);
// 			$post['categoryNames'] = join(" / ", $categoryBelongsContent);
// 			$posts[$key] = $post;
		}
		
		$this->parser->setParserVar('hasPosts', count($posts));

		$this->parser->setParserVar('posts', $posts);
		$this->content = $this->parser->parseTemplate($this->mlogTemplatePath . $this->template);
	}
	
	/**
	 * public function setTemplate()
	 * Set the widgets (frame)template.
	 * 
	 * @param string $templateName Template's file name.
	 * @return void
	 */
	public function setTemplate($templateName) {
		$this->template = trim($templateName);
	}

	/**
	 * public function setShowPosts()
	 * Set the number of posts to display.
	 * 
	 * @param number $posts
	 * @return boolean
	 */
	public function setShowPosts($posts) {

		if (intval($posts)) {
			$this->showPosts = intval($posts);
			return true;
		} else {
			return false;
		}
	}

	public function setDetailPageId($id) {
	
		if (intval($id)) {
			$this->detailPageId = intval($id);
			return true;
		} else {
			return false;
		}
	}
	
	public function setCategoryId($id) {
		$this->categoryId = intval($id);
	}
	
}
$autoLoad = new PsrAutoloader();
$autoLoad->addNamespace('Contentomat', INCLUDEPATH . 'phpincludes/classes');
$autoLoad->addNamespace('Contentomat\MLog', INCLUDEPATH . 'phpincludes/mlog');
$autoLoad->addNamespace('Contentomat\MLog', INCLUDEPATHTOADMIN . 'classes/app_mlog');

$c = new MLogWidgetController();
$content = $c->work();
?>