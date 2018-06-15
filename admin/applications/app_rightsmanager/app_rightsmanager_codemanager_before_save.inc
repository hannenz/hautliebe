<?php
/**
 * app_rightsmanager_codemanager_before_save.inc
 * Application include checks user rights against group rights and encodes user's password if a user entry is new.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2015-06-18
 *
 */
namespace Contentomat\Rightsmanager;
use \Contentomat\Contentomat;
use \Contentomat\DBCex;

	$cmt = contentomat::getContentomat();
	$db = new DBCex();
	
	$cmtTableDataRaw = $cmt->getVar('cmtTableDataRaw');

	$query = "SELECT cmt_grouptype, cmt_showitems FROM cmt_users_groups WHERE id = '" . intval($cmtTableDataRaw['cmt_usergroup']) . "'";
	$db->query($query);
	$r = $db->get();
	
	$cmtTableDataRaw['cmt_usertype'] = $r['cmt_grouptype'];
	
	// save group rights as user rights
	// TODO: Not perfect: If an existing user entry is saved after its rights are changend to less rights than the group has, 
	// this script overwrites here the individual user rights with the group rights.  
	//$cmtTableDataRaw['cmt_showitems'] = $r['cmt_showitems'];

	// encode password if user is new
	if ($cmtTableDataRaw['cmt_pass'] != $_POST['oldPass']) {
	   $cmtTableDataRaw['cmt_pass'] = md5($cmtTableDataRaw['cmt_pass']);
	}

	if ($cmtTableDataRaw['cmt_usergroup'] != $_POST['oldGroup']) {
		$cmtTableDataRaw['cmt_showitems'] = $r['cmt_showitems'];
	}

	// pass data to Contentomat
	$check = $cmt->setVar('cmtTableDataRaw', $cmtTableDataRaw);

?>