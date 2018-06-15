<?php
/**
 * appinc_rightsmanager_overview.inc
 * Zeigt eine Übersicht der Gruppen und Benutzer an.
 * 
 * @version 2012-11-30
 */

namespace Contentomat\RightsManager;
use \Contentomat\Parser;
use \Contentomat\User;

	$parser = new Parser();
	$user = new User($cmt->session->getSessionID());

	$tplPath = CMT_TEMPLATE.'app_rightsmanager/';
	
	switch ($action) {
		
		case 'deleteGroup':
			
			$check = $user->deleteUserGroup($id[0]);
			
			$parser->setParserVar('userMessage', true);
			
			if ($check) {
				$parser->setParserVar('userMessageType', 'Success');
				$parser->setParserVar('userMessageText', 'groupDeleted');
			} else {
				$parser->setParserVar('userMessageType', 'Error');
				$parser->setParserVar('userMessageText', 'groupNotDeleted');
			}
			break;
			
		case 'deleteUser':
			
			$check = $user->deleteUser($id[0]);
		
			$parser->setParserVar('userMessage', true);
			
			if ($check) {
				$parser->setParserVar('userMessageType', 'Success');
				$parser->setParserVar('userMessageText', 'userDeleted');
			} else {
				$parser->setParserVar('userMessageType', 'Error');
				$parser->setParserVar('userMessageText', 'userNotDeleted');
			}
			break;
	}

	/*
	 * Gruppen- und Nutzerübersicht anzeigen.
	 */
	$templateUser = $parser->getTemplate($tplPath . 'cmt_rightsmanager_users_overview_user.tpl');
	$templateGroup = $parser->getTemplate($tplPath . 'cmt_rightsmanager_users_overview_group.tpl');
	
	//$userData = $user->getAllGroupsAndUsers();
	
	$parser->setParserVar('cmtUserTable', 'cmt_users');
	$parser->setParserVar('cmtUserGroupTable', 'cmt_users_groups');
	$parser->setParserVar('cmtReturnToID', CMT_APPID);

	$contentGroups = '';
	$contentUsers = '';
	$alternationFlag = 0;

	$groups = $user->getAllUserGroups();
	
	// Gruppen parsen
	foreach ($groups as $group) {
		
		$groupUsers = $user->getGroupUsers($group['id']);
		
		// Benutzer parsen
		foreach ($groupUsers as $groupUser) {
			
			$parser->setMultipleParserVars($groupUser);
			$parser->setParservar('alternationFlag', $alternationFlag++%2);
			$contentUsers .= $parser->parse($templateUser);
		}
		
		$parser->setParserVar('contentUsers', $contentUsers);
		$parser->setMultipleParserVars($group);
		
		$contentGroups .= $parser->parse($templateGroup);
		$contentUsers = '';
		$alternationFlag = 0;
		
	}

	$parser->setParserVar('contentGroups', $contentGroups);

	// Inhalte in Variable $contentInclude parsen. Diese wird vom Hauptskript ausgegeben.
	$contentInclude = $parser->parseTemplate($tplPath . 'cmt_rightsmanager_users_overview_frame.tpl');

	
?>