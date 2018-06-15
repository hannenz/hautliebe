<?php 
/**
 * class_systemmessage.php
 * Provides methods to handle messages for the backend.
 * 
 * @author J.Hahn <j.hahn@agentur-halma.de>
 * @version 2016-03-30
 * 
 */

namespace Contentomat\SystemMessage;

use Contentomat\DBCex;
use Contentomat\Contentomat;

class SystemMessage {
	
	protected $db;
	protected $userGroupId;
	protected $userId;
	protected $systemMessagesTable;
	protected $cmt;
	
	public function __construct() {
		
		$this->systemMessagesTable = 'cmt_systemmessages';

		$this->userGroupId = defined('CMT_GROUPID') ? CMT_GROUPID : 0;
		$this->userId = defined('CMT_USERID') ? CMT_USERID : 0;

		$this->db = new DBCex();
		$this->cmt = Contentomat::getContentomat();
	}
	
	/**
	 * Returns a set of active messages depending on the given options.
	 * 
	 * @param array $params		Options/ parameters in an associative array
	 * @return array			The messages datasets as an multidimensional array.
	 */
	public function getSystemMessages($params=array()) {
		
		$defaultParams = array(
			//'onlyActive' => true,
			'forUserId' => $this->userId,
			'forUserGroupId' => $this->userGroupId,
			'forAllOnly' => false,
			'permanentOnly' => false,
			'limit' => 0
		);
		
		$params = array_merge($defaultParams, $params);
		
		$where = array();
		
		// for Group and User
		if (!$params['forAllOnly']) {
// 			$where[] = "(for_usergroupid = '" . intval($params['forUserGroupId']) . " OR for_usergroupid = '0')";
// 			$where[] = "(for_userid = '" . intval($params['forUserId']) . " OR for_userid = '0')";

			$where[] = "((FIND_IN_SET('" . intval($params['forUserGroupId']) . "', for_usergroupid)) OR " . 
					   "(FIND_IN_SET('" . intval($params['forUserId']) . "', for_userid)) OR " . 
					   "(for_usergroupid = '' AND for_userid = ''))";
			
		}
		
		// only active
		$where[] = "is_active = '1'";
		
		// only in time or permanent
		if (!$params['permanentOnly']) {
			$where[] = "(datetime_start <= NOW() OR datetime_start = '0000-00-00 00:00:00')";
			$where[] = "(datetime_end >= NOW() OR datetime_end = '0000-00-00 00:00:00')";
		} else {
			$where[] = "datetime_start = '0000-00-00 00:00:00'";
			$where[] = "datetime_end = '0000-00-00 00:00:00'";
		}
		
		// limit amount
		$limit = '';
		
		if ($params['limit']) {
			$limit = "LIMIT " . intval($params['limit']);
		}
		
		// order
		$orderBy = "ORDER BY datetime_start DESC";
		
		$query = trim("SELECT * FROM " . $this->systemMessagesTable . " WHERE " . implode(' AND ', $where) . " " . $orderBy . " " . $limit);
// echo $query;
// die();
		$this->db->query($query);
		return $this->prepareMessages($this->db->getAll());
	}
	
	protected function prepareMessages($messages) {
		
		$session = $this->cmt->getSession();
		
		// unset previousely clicked messages that are not pinned.
		//$clickedMessages = (array)$session->getSessionVar('cmtSystemMessagesShown');
		$shownMessages = (object)json_decode($_COOKIE['cmtSystemMessagesShown']);

		foreach ($messages as $messageId => $message) {
			
			if ($shownMessages->$messageId && !$message['is_pinned']) {
				unset($messages[$messageId]);
			}
		}
		return $messages;
	}
	
	/**
	 * Helper: Gets ONLY the messages for all users, not that defined for a user or a user group. 
	 *
	 * @param array $params		Options parameters in an associative array
	 * @return array			The messages datasets as an multidimensional array.
	 */
	public function getSystemMessagesForAll() {
		
		return $this->getSystemMessages(array(
			'forAllOnly' => true
		));
	}
	
	/**
	 * Helper: Gets ONLY the messages for all users, not that defined for a user or a user group.
	 *
	 * @param array $params		Options/ parameters in an associative array
	 * @return array			The messages datasets as an multidimensional array.
	 */
	public function getPermanentSystemMessages($params=array()) {
	
		if (!is_array($params)) {
			$params = array();
		}
		
		$params['permanentOnly'] = true;
		
		return $this->getSystemMessages($params);
	}
	
}
?>