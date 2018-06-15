<?php
/*******************************************************
 * 
 * Content-o-mat - Showtable Upload-Include
 * 
 * Letzte Änderung: 25.10.2005
 * 
 *******************************************************/

    //////////////////////////////////////
    //
    // Includes und Anfangsgeraffel
    //
    //////////////////////////////////////

    require ('../cmt_functions.inc');
    require ('../cmt_constants.inc');
    include ('../classes/class_dbcex.php');
    include ('../classes/class_session.php');
    include ('../classes/class_user.php');
    include ('../classes/class_table.php');
    include ('../classes/class_form.php');
    include ('../classes/class_div.php');
    include ('../classes/class_parser.php');

    		
    // Session überprüfen
    $session = new Session($check_loggedin = true);
    $db = new DBCex();
    $user = new User(SID);

	if (!defined("CMT_USERID")) {
		exit();
	}
	
    $div = new Div();
    $form = new Form();
	

	// Übergebene Variablen holen
    $default_vars = array ('sid' => '', 'action' => '', "cmt_value" => "", "cmt_dbtable" => "", "launch" => "",
						   "cmt_fieldname" => "", "cmt_parentname" => "", "cmt_uploaddirectory" => "","cmt_appid" => "");
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include ('../includes/func_get_vars.php');

	$cmt_abort = false;

	// Feldeinstellungen holen
	$query = "SELECT cmt_options FROM cmt_fields WHERE cmt_fieldname = '".$cmt_fieldname."' AND cmt_tablename = '".$cmt_dbtable."'";
	$db->Query($query);
	$r = $db->Get(MYSQLI_ASSOC);

	$makros = explode("\n", trim($r['cmt_options']));
	foreach ($makros as $makro) {
		preg_match ("/^\{(.*)\}(.*)/", $makro, $match);
		$m = $match[1];
		$v = $match[2];
		$app_vars[strtolower($m)] = $v;
	}

	// Code für Upload holen
	$query = "SELECT * FROM cmt_execute_code WHERE cmt_tablename = '$cmt_appid'";
	$db->Query($query);
	while ($r=$db->Get(MYSQLI_ASSOC)) {
		$cmt_executecode[$r['cmt_executiontime']] = stripslashes($r['cmt_code']);
	}

	if ($action == "upload") {
		//Dateien einzeln verabreiten
		foreach ($_FILES as $file_details) {
			// Daten ermitteln
			$file_source = $file_details['tmp_name'];
			$file_name = $file_details['name'];
			$file_size = $file_details['size'];
			$file_type = $file_details['type'];
			
			$f_ext = explode('.', $file_name);
			if (is_array($f_ext)) {
				$file_ext = array_pop($f_ext);
			}
//print_r($file_details);
//die;
			// Leerzeichen entfernen
			$file_name = str_replace (" ", "_", $file_name);
			
			// ANweisungen zur Dateibehandlung
			$cmt_deleteoldfile = $app_vars['deleteoldfile'];		// alte Datei ($cmt_value) im Zielordner nach Upload löschen
			$cmt_copynewfile = true;		// neue Datei in Zielordner kopieren
			
			// Upload-Pfad errechnen
			$cmt_uploaddirectory = format_directory($cmt_uploaddirectory."/");
			
			//////////////////////////////////////////
			//
			// Code ausführen: upload_onupload
			//
			//////////////////////////////////////////
			if ($cmt_executecode['upload_onupload']) {
				$parser = new Parser();	
				$parser->evalvars['cmt_oldfile'] = $cmt_value;
				$parser->evalvars['cmt_filename'] = $file_name;
				$parser->evalvars['cmt_fieldname'] = $cmt_fieldname;
				
				$parser->evalvars['cmt_fileextension'] = $file_ext;
				$parser->evalvars['cmt_filetype'] = $file_type;
				
				
				$parser->evalvars['cmt_uploadpath'] = $cmt_uploaddirectory.$file_name;
				
				$parser->evalvars['cmt_sourcepath'] = $file_source;
				$parser->evalvars['cmt_filesize'] = $file_size;
				$parser->evalvars['cmt_uploaddirectory'] = $cmt_uploaddirectory;
				$parser->evalvars['cmt_deleteoldfile'] = true;
				$parser->evalvars['cmt_copynewfile'] = true;
				$replace .= $parser->parse($cmt_executecode['upload_onupload']);
				$file_name = $parser->evalvars['cmt_filename'];
				
				// macht das Sinn??
				//$file_source = $parser->evalvars['cmt_filesource'];
				//file_size = $parser->evalvars['cmt_filesize'];
				$cmt_uploaddirectory = $parser->evalvars['cmt_uploaddirectory'];
				$cmt_abort = $parser->evalvars['cmt_abort'];
				$cmt_deleteoldfile = $parser->evalvars['cmt_deleteoldfile'];
				$cmt_copynewfile = $parser->evalvars['cmt_copynewfile'];
				$cmt_usermessage = $parser->evalvars['cmt_usermessage'];
			}
			
			if ($file_name && !$cmt_abort) {
				
				//Datei ins Verzeichnis kopieren
				if ($cmt_copynewfile) {
					$new_file = $cmt_uploaddirectory.$file_name;
					$session->setSessionVar("cmt_editentryform_".$cmt_fieldname."_tmp", $file_source);
					$session->setSessionVar("cmt_editentryform_".$cmt_fieldname."_new", $new_file);
					$session->setSessionVar("cmt_editentryform_".$cmt_fieldname."_deleteoldfile", $cmt_deleteoldfile);
					$session->setSessionVar("cmt_editentryform_".$cmt_fieldname."_copynewfile", $cmt_copynewfile);
					$session->setSessionVar("cmt_editentryform_".$cmt_fieldname."_uploaddirectory", $cmt_uploaddirectory);
					$session->saveSessionVars();
					/*
					$file_check = copy($file_source, $new_file);
					
					if (!$file_check) {
						$upload_error = "'$file_name' konnte nicht hochgeladen/gespeichert werden. Bite überprüfen Sie, ob " .
								"Ihre Zugriffsrechte ausreichend gesetzt sind.";
						
						$cmt_usermessage .=  error($upload_error, 0, "div");
						$action = "";
					} else {
						chmod ($new_file, 0755);
}
					*/
				}
				
				// alte Datei löschen
				/*			
				if ($cmt_value && $cmt_deleteoldfile  &&  trim($file_name) != trim($cmt_value)) {
					$cmt_oldfile = $cmt_uploaddirectory.$cmt_value;
					//echo ('lösche '.$cmt_oldfile);
					@unlink($cmt_oldfile);
				}
				*/
			}
				
			if (trim($cmt_usermessage) != "") {
				$user_message .= error($cmt_usermessage, 0, "div");
				//$replace .= $user_message;
				$action = '';
			} else {
				$replace = '<script language="Javascript">';
				$replace .= "\nrefreshRemoteField ('editentryform', '$cmt_fieldname', '$file_name')\n" .
							//"refreshRemoteField ('editentryform', '".$cmt_fieldname."_tmp', '$file_source')\n" .
							//"refreshRemoteField ('editentryform', '".$cmt_fieldname."_new', '$new_file')\n" .
							"closeWindow('upload_window');";
				$replace .= "</script>";
			}
		}
	}		
		// Meldungen erzeugen
/*
		if ($upload_error) {
			$user_message .=  error($upload_errors, 0, "div");
		}
		
		$replace .= $user_message;
	}
*/	
	if ($action != "upload") {
		$replace .= $form->FormStart("uploadform", SELF."?sid=".SID, "POST", "multipart/form-data");
		$replace .= $div->DivMakeDiv("Datei hochladen", "editentry_head");
		
		if ($user_message) {
			$replace .= $user_message;
		}
		
		$replace .= $div->DivMakeDiv($form->FormUpload('cmt_upload_file', "", 30), "editentry_alternate");
		
		$replace .= $form->FormHidden("cmt_dbtable", $cmt_dbtable);
		$replace .= $form->FormHidden("cmt_appid", $cmt_appid);
		$replace .= $form->FormHidden("cmt_fieldname", $cmt_fieldname);
		$replace .= $form->FormHidden("cmt_value", $cmt_value);
		//$replace .= $form->FormHidden("cmt_parentname", $cmt_parentname);
		$replace .= $form->FormHidden("cmt_uploaddirectory", $cmt_uploaddirectory);
		$replace .= $form->FormHidden("action", "upload");
		
		//$service .= $form->FormSubmit("upload", "action", "../img/icon_upload.gif", "onClick=\"set_action(this.form.name, 'upload')\"");
		$service .= "<a href=\"#\" onClick=\"closeWindow('upload_window');\"><img src=\"../".CMT_TEMPLATE."/img/icon_back.gif\" border=\"0\"></a>&nbsp;";
		$service .= $form->FormSubmit("", "", "../".CMT_TEMPLATE."img/icon_upload.gif");
		$replace .= $div->DivMakeDiv($service, "editentry_service");
		$replace .= $form->FormEnd();
	}	 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
  <meta name="author" content="www.buero-hahn.de">
  <meta http-equiv="content-language" content="de">
  <meta name="robots" content="NOFOLLOW">
  <meta http-equiv="pragma" content="no-cache">
  <title>content-o-mat: application launcher</title>
  <link rel="Stylesheet" href="../<?php echo CMT_TEMPLATE; ?>cmt_style.css" type="text/css">
  <script src="../javascript/cmt_functions.js" language="JavaScript"></script>
</head>

<body>
<?php echo $replace ?>
</body>

</html>