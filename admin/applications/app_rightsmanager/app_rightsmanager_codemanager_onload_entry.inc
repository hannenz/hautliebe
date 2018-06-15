<?php
/**
 * app_rightsmanager_codemanager_onload_entry.inc
 * Application include checks user rights against group rights and encodes user's password if a user entry is new.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2015-06-18
 *
 */
namespace Contentomat\Rightsmanager;
use \Contentomat\Contentomat;
use \Contentomat\DBCex;
use \Contentomat\User;

 	$cmt = contentomat::getContentomat();

	$cmtTableDataRaw = $cmt->getVar('cmtTableDataRaw');
	
	$cmt->setVar('oldPass', $cmtTableDataRaw['cmt_pass']);
	
	// TODO: Workaround, all Contentomat vars should be prefixed with 'cmt', so the correct var name is 'cmtAction'!
	if ($_REQUEST['cmt_action'] == "new" || $_REQUEST['cmtAction'] == 'new' || $_REQUEST['action'] == 'new') {
		$user = new User();
		$cmtTableDataRaw['cmt_pass'] = $user->createPassword();
	}

	// pass data to Contentomat
	$cmt->setVar('cmtTableDataRaw', $cmtTableDataRaw);
?>