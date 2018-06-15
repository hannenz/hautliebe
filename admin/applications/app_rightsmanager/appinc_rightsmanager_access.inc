<?php
/**
 * appinc_rightsmanager_access.inv
 * Skript zur Bearbeitung der Zugriffsrechte von Gruppen und Nutzern.
 * 
 * @version 2012-12.18
 */
namespace Contentomat\Rightsmanager;
use \Contentomat\CMTParser;
use \Contentomat\User;
use \Contentomat\Form;
use \Contentomat\ApplicationHandler;
use \Contentomat\SessionHandler;

	$appHandler = new ApplicationHandler();
	$form = new Form();
	$parser = new CMTParser();
	$session = SessionHandler::getSession();
	// The user has to be logged in to the backend to change the access rights!
	$user = new User($session->getSID());	
	
	$userTemplatePath = 'templates/' . $user->getUserCMTTemplate();
	
	$parser->setParserVar('userID', $userID);
	$parser->setParserVar('userGroupID', $userGroupID);

	$userGroupIDs = array();
	$userGroupNames = array();

	// Benutzeraktionen
	switch ($action) {
		
		/*
		 * Gruppenrechte bearbeiten
		 */
		case 'editUserGroupRights':

			if (!$userGroupID) {
				break;
			}
			
			// Gruppenrechte müssen gespeichert werden
			if ($cmtSave) {

				// 1. Gruppenrechte speichern
				if (is_array($cmtRights)) {
					$check = $user->saveGroupRights($userGroupID, $cmtRights);
				}
				
				$parser->setParserVar('userMessageText', 'groupAccessSaved');
				$parser->setParserVar('userMessageType', 'Success');
				
				if ($check) {
					$checkUpdate = $user->updateUserRightsInGroup($userGroupID);
					
					if (!$checkUpdate) {
						$parser->setParserVar('userMessageType', 'Error');
						$parser->setParserVar('userMessageText', 'groupAccessUpdated');
					}
					
				} else {
					$parser->setParserVar('userMessageType', 'Error');
				}
			}
			
			// Rechte der ausgewählten Gruppe anzeigen
			$appGroups = $appHandler->getApplicationGroups();
			$applicationGroupContent = '';
			
			$groupRights = $user->getGroupRights($userGroupID);
			$baseRights = $user->getBaseRights();

			foreach ($appGroups as $appGroup) {

				$alternationFlag = 0;
				
				$applicationContent = '';
				$apps = $appHandler->getApplications(array('groupID' => $appGroup['id']));

				foreach ($apps as $app) {
					
					foreach ($baseRights as $right) {
						if (isset($groupRights[$app['id']][$right])) {
							$parser->setParserVar($right.'Selected', 'checked');
						} else {
							$parser->setParserVar($right.'Selected', '');
						}
					}
					
					$parser->setMultipleParserVars($app);
					$parser->setParserVar('applicationIcon', $userTemplatePath . $appHandler->getApplicationIcon($app['id'], 'medium'));
					
					$parser->setParserVar('alternationFlag', $alternationFlag++%2);
					
					$applicationContent .= $parser->parseTemplate($tplPath . 'cmt_rightsmanager_access_edit_usergroup_row.tpl');
					
				}

				$parser->setMultipleParserVars($appGroup);
				$parser->setParserVar('applicationContent', $applicationContent);
				$parser->setParserVar('groupIcon', $userTemplatePath . $appHandler->getGroupIcon($appGroup['id'], 'medium'));


				$editUserGroupContent .= $parser->parseTemplate($tplPath . 'cmt_rightsmanager_access_edit_usergroup_frame.tpl'); 
				
			}
			
			$parser->setParserVar('editUserGroupContent', $editUserGroupContent);
			
			break;
			
			
		/*
		 * Benutzerrechte bearbeiten
		 */
		case 'editUserRights':

			if (!$userID) {
				break;
			}
			
			// Benutzerrechte müssen gespeichert werden
			if ($cmtSave) {

				if (is_array($cmtRights)) {
					$check = $user->saveUserRights($userID, $cmtRights);
				}
				
				if ($check) {
					$parser->setParserVar('userMessageText', 'userAccessSaved');
					$parser->setParserVar('userMessageType', 'Success');
				} else {
					$parser->setParserVar('userMessageType', 'Error');
				}
			}
			
			// Rechte des ausgewählten Benutzers anzeigen
			$appGroups = $appHandler->getApplicationGroups();
			$applicationGroupContent = '';
			
			$userGroupID = $user->getUserGroupID($userID);
			
			$groupRights = $user->getGroupRights($userGroupID);
			$baseRights = $user->getBaseRights();
			
			foreach ($appGroups as $appGroup) {

				$alternationFlag = 0;
				
				$applicationContent = '';
				$apps = $appHandler->getApplications(array('groupID' => $appGroup['id']));

				foreach ($apps as $app) {

					if (!in_array($app['id'], array_keys($groupRights))) {
						continue;
					}
					
					$userRights = $user->getUserRights($userID, $app['id']);

					foreach ($baseRights as $right) {
						if (isset($groupRights[$app['id']][$right])) {
							
							$parser->setParserVar('showRight' . ucfirst($right), true);
							
							if ($userRights[$right]) {
								$parser->setParserVar($right.'Selected', 'checked="checked"');
							} else {
								$parser->setParserVar($right.'Selected', '');
							}
						} else {
							$parser->setParserVar($right.'Selected', '');
							$parser->setParserVar('showRight' . ucfirst($right), false);
						}
					}
					
					$parser->setMultipleParserVars($app);
					$parser->setParserVar('applicationIcon', $userTemplatePath . $appHandler->getApplicationIcon($app['id'], 'medium'));
					
					$parser->setParserVar('alternationFlag', $alternationFlag++%2);
					
					$applicationContent .= $parser->parseTemplate($tplPath . 'cmt_rightsmanager_access_edit_user_row.tpl');
					
				}

				if ($applicationContent) {
					$parser->setMultipleParserVars($appGroup);
					$parser->setParserVar('applicationContent', $applicationContent);
					$parser->setParserVar('groupIcon', $userTemplatePath . $appHandler->getGroupIcon($appGroup['id'], 'medium'));

					$editUserContent .= $parser->parseTemplate($tplPath . 'cmt_rightsmanager_access_edit_user_frame.tpl');
				} 
				
			}
			
			$parser->setParserVar('editUserContent', $editUserContent);
			break;
	}
	
	
	/*
	 * Auswahllisten und weitere Layoutelemente erzeugen
	 */
	
	$c = 0;
	$userGroups = $user->getAllUserGroups(true);
	
	// Gruppen- und Nutzerauswahllisten
	foreach ($userGroups as $userGroup) {
		
		if ($userGroup['cmt_grouptype'] != 'admin') {
			$userGroupsArray[$c]['label'] = $userGroup['cmt_groupname'];
			
			$userGroupNames[$c] = $userGroup['cmt_groupname'];
			$userGroupIDs[$c] = $userGroup['id'];
			
			$groupUsers = $user->getGroupUsers($userGroup['id']);

			foreach ($groupUsers as $groupUser) {
				$userGroupsArray[$c]['options'][] = array(
					'value' => $groupUser['id'],
					'alias' => $groupUser['cmt_username']
				);
			}
			
			$c++;
		}
	}

	// Nutzerauswahlliste erzeugen
	$parser->setParserVar('cmtUserSelectOptions', $form->select(array(
		'nestedData' => $userGroupsArray,
		'selected' => intval($userID),
		'optionsOnly' => true
	)));
	
	// Gruppenauswahlliste erzeugen
	$parser->setParserVar('cmtGroupSelectOptions', $form->select(array(
		'values' => $userGroupIDs,
		'aliases' => $userGroupNames,
		'selected' => intval($userGroupID),
		'optionsOnly' => true
	)));
	
	
	/*
	 * Inhalte in Include-Variable parsen
	 */
	$contentInclude = $parser->parseTemplate($tplPath . 'cmt_rightsmanager_access_frame.tpl');	
?>