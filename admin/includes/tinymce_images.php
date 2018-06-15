<?php
	namespace Contentomat;

	require ("../cmt_functions.inc");
    require ("../cmt_constants.inc");
	require('../classes/class_filehandler.php');
    require ("../classes/class_session.php");
    require ("../classes/class_dbcex.php");
    require ("../classes/class_contentomat.inc");

    $session = new Session(true);
    	
	$f = new FileHandler();
	$dir = trim($_GET['dir']);
	
	$files = $f->showDirectory(array('directory' => $f->formatDirectory(PATHTOWEBROOT . $dir), 'showOnlyFileTypes1' => 'jpg, jpeg, gif, png'));

	$fileArray = array();
	foreach ((array)$files as $path =>$file) {
		$fileArray[] = array('title' => $file, 'value' => preg_replace ('/^\.\.\//', '', $path));
	}

	echo json_encode($fileArray);
	exit();
?>