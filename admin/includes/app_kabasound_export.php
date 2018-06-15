<?php
/*******************************************************
 * 
 * Content-o-mat - Kabasound Export-Include
 * 
 * Letzte Änderung: 27.07.2006
 * 
 *******************************************************/

/*
 * TO DO:
 * 1. Wenn Tabellen mit 0 Einträgen gespeichert werden, dann erscheint im Balken
 * "NaN" -> Division durch Null
 * 
 * 2. Export-Files mit mehreren Tabellen ermöglichen
 * 4. CSV-Export: auch tabstopp getrennte Spalten ohne " ermöglichen
 */
    //////////////////////////////////////
    //
    // Funktionen
    //
    //////////////////////////////////////

	function cmtFputcsv($handle, $row, $fd=',', $quot='"') {
	   $str='';
	   foreach ($row as $cell) {
	     $cell = str_replace($quot, $quot.$quot, $cell);
	        
	     if (strchr($cell, $fd) !== FALSE || strchr($cell, $quot) !== FALSE || strchr($cell, "\n") !== FALSE) {
	         // Für Excel
	         //$cell = str_replace ("\r", "", $cell);
	         $str .= $quot.$cell.$quot.$fd;
	     } else {
	         $str .= $cell.$fd;
	     }
	   }
	
	   $check = @fputs($handle, substr($str, 0, -1)."\r\n");
	
	   //return strlen($str);
	   return $check;
	}
	
	// Datenbankspalte zu Tags -> <$row[key]>$value</$row[key];\n
	function row2xml ($row=array()) {
		$export_data = "";
		/*
		if (!$row) {
			$row = array();
		}
		*/
		foreach ($row as $field => $value) {
			$export_data .= "<".$field.">".$value."</".$field.">\n";
		}
		
		return $export_data;
	}

	function export_error ($message) {
		$tab = new Table();
		echo $tab->TableStart("100%");
		echo $tab->TableMakeRow($message, 0, "", "error");
		echo $tab->TableEnd();
		return;
	}
	
	// Zeichen escapen wenn Ausgabeformat UTF-8
	function utf8_htmlspecialchars ($string) {
        $convert_special_chars['"'] = "&quot;";
        $convert_special_chars['<'] = "&lt;";
        $convert_special_chars['>'] = "&gt;";
        $convert_special_chars['&'] = "&amp;";
        $convert_special_chars["'"] = "&apos;";
        $convert_special_chars['€'] = "&#8364;";
		return strtr ($string, $convert_special_chars);
	}	
    //////////////////////////////////////
    //
    // Includes und Anfangsgeraffel
    //
    //////////////////////////////////////

    require ("../cmt_functions.inc");
    require ("../cmt_constants.inc");
    include ("../classes/class_dbcex.php");
    include ("../classes/class_session.php");
    include ("../classes/class_user.php");
    include ("../classes/class_table.php");
    		
    // Session überprüfen
    $session = new Session($check_loggedin = true);
    $db = new DBCex();
    $user = new User(SID);


	// Übergebene Variablen holen
    $default_vars = array ("sid" => "", "cmt_dbtable" => "", "export_type" => "csv", "export_file" => "export.txt",
        				"export_directory" => "import_export/", "export_steps" => 50, "progress" => 0, "csv_separator" => ",",
        				"template_row" => "", "template_frame" => "", "template_encoding" => "", "escape" => "", "cmt_orderstring" => "",
        				"append" => 0
        				);
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include ("../includes/func_get_vars.php");

	$cmt_exptab_name = $session->GetSessionVar("cmt_exptab_name");
	$cmt_exptab_structonly = $session->GetSessionVar("cmt_exptab_structonly");
	
	if (!is_array($cmt_exptab_structonly)) {
		$cmt_exptab_structonly = array();
	}
	if (in_array(trim($cmt_dbtable), $cmt_exptab_structonly)) {
		$structonly = true;
	}

	$act_pos = $progress * $export_steps;
	
	// Anzahl der Einträge ermittlen
	if ($export_type == "freestyle") {
		$query = $session->GetSessionVar("cmt_freestyle_query");
		$db->Query($query);
		$total_entries = $db->CountSelectedRows();
	//die($query.": Total Entries: ".$total_entries);
	} else {
		if (!$cmt_dbtable) {
			export_error("Keine Tabelle angegeben!");
		}
		$query = "SELECT COUNT(*) AS total_entries FROM $cmt_dbtable";
		$db->Query($query);
		$r = $db->Get();
	
		$total_entries = $r['total_entries'];
		unset($r);
	}
	
	if ($structonly) {    
    	$message = "<font class=\"service_text\">Speichere Struktur aus</font> $cmt_dbtable. ";
	} else {
		$message = "<font class=\"service_text\">Speichere</font> $total_entries <font class=\"service_text\">Einträge</font>";
		if ($cmt_dbtable) {
			$message .= " <font class=\"service_text\">aus</font> $cmt_dbtable. ";
		}
	}
    
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html>
	<head>
	  <meta http-equiv=\"content-type\" content=\"text/html; charset=".CHARSET."\">
	  <meta name=\"author\" content=\"www.buero-hahn.de\">
	  <meta http-equiv=\"content-language\" content=\"de\">
	  <meta name=\"robots\" content=\"NOFOLLOW\">
	  <meta http-equiv=\"pragma\" content=\"no-cache\">
	  <title>content-o-mat: kabasound import-export include</title>
	  <link rel=\"Stylesheet\" href=\"../".CMT_TEMPLATE."cmt_style_kabasound_iframe.css\" type=\"text/css\">\n
	</head>
	<body>
	$message";
	unset ($message);
	
	// Variablendefinitionen
	$pos_counter = 0;	
	$exp_file = format_directory(PATHTOADMIN.$export_directory."/").$export_file;
	$new_file = false;
	
	// Export
	if ($export_type != 'excel') {
		if (!$append) {
			$fp = fopen ($exp_file, "w+");
		} else {
			$fp = fopen ($exp_file, "a");
		}
	}
//die ("Datei ist: ".$exp_file." - Typ ist: ".$export_type);	
	switch (trim($export_type)) 
	{
		case "csv":
			// Export eben erst gestartet? Dann Spaltennamen schreiben
			if (!$progress) {
				$new_file = true;
				// Feldinformationen ermitteln
				$fields = $db->GetFieldInfo($cmt_dbtable);
				foreach ($fields[name] as $fn) {
					$fieldnames[] = $fn;
				}
			}
			
			// Erste Zeile schreiben
			if ($new_file) {
				$check = cmtFputcsv ($fp, $fieldnames, $csv_separator);
				if (!$check) {
					export_error("0, Spaltentitel");
				}
			}
			
			// Daten aus DB holen
			$query = "SELECT * FROM $cmt_dbtable LIMIT $act_pos, $export_steps";
			$db->Query($query);
			
			while ($r = $db->Get(MYSQLI_ASSOC)) {
				$check = cmtFputcsv ($fp, $r, $csv_separator);
				if (!$check) {
					export_error($act_pos+$pos_counter);
				}
				$pos_counter++;
			}
		break;
		
		case "cmt":
			if (!$progress) {
				$new_file = true;
				$exp_data = "<cmt_dbtable name=\"".$cmt_dbtable."\">\n";
				
				// Tabellendaten holen				
				$exp_data .= "<tabledefinitions>\n";
				$query = "SELECT * FROM cmt_tables WHERE cmt_tablename = '".$cmt_dbtable."'";
				$db->Query($query);
				
				while ($r = $db->Get(MYSQLI_ASSOC)) {
					//id löschen
					unset($r['id']);					
					$exp_data .= row2xml($r);
				}
				$exp_data .= "</tabledefinitions>\n";
					
				// Felddaten holen aus cmt_fields;
				$exp_data .= "<fielddefinitions>\n";
				$query = "SELECT * FROM cmt_fields WHERE cmt_tablename = '".$cmt_dbtable."'";
				$db->Query($query);
				
				while ($r = $db->Get(MYSQLI_ASSOC)) {
					//id löschen
					unset($r['id']);
					$exp_data .= "<field>\n";
					$exp_data .= row2xml($r);
					$exp_data .= "</field>\n";
				}
				
				$exp_data .= "</fielddefinitions>\n";
			}
			
			if ($structonly == false) {
				$exp_data .= "<tabledata>\n";
				// Variablendefinitionen
				$pos_counter = 0;
				
				// Daten aus DB holen
				$query = "SELECT * FROM $cmt_dbtable LIMIT $act_pos, $export_steps";
				$db->Query($query);
				
				while ($r = $db->Get(MYSQLI_ASSOC)) {
					$exp_data .= "<row>\n";
					$exp_data .= row2xml($r);
					$exp_data .= "</row>\n";
					$pos_counter++;
				}
			} else {
				//$act_pos = $total_entries + $export_steps + 1;
				$act_pos = $total_entries;
			}
			// Speichern
			$check = fputs($fp, $exp_data, strlen($exp_data));
			if (!$check) {
				export_error("Schreibfehler in Zeile: ".$act_pos+$pos_counter);
			}
			
		break;
		
		case "template":
			include ("../classes/class_dataformat.php");
    		include ("../classes/class_parser.php");
    		include ("../classes/class_form.php");    		
    		$parser = new Parser();

			// Daten aus DB holen
			$query = "SELECT * FROM $cmt_dbtable";
			
			if ($cmt_orderstring) {
				$query .= " ORDER BY $cmt_orderstring";
			}
			$query .= " LIMIT $act_pos, $export_steps";
//			die ($query);
			$db->Query($query);
			
			while ($r = $db->Get(MYSQLI_ASSOC)) {
				
				// Stripslashes und Escapen
				foreach ($r as $key => $value) {
					$value = stripslashes($value);
					if ($escape) {
						if ($template_encoding == "utf8") {
							$value = utf8_htmlspecialchars($value);
						} else {
							$value = htmlspecialchars($value);
						}
					}
					$r[$key] = $value;
				}
				
				// Codierung?
				/*
				switch ($template_encoding) {
					case "utf8":
						foreach ($r as $key => $value) {
							$r[$key] = utf8_encode($value);
						}
					break;	
				}
				*/
				//$parser->db_values = $r;
				$parser->vars = $r;
				print_r ($parser->vars);
				$exp_data .= $parser->ParseTemplate($template_row);
				
				$pos_counter++;
			}
				
			// Speichern
			$check = fputs($fp, $exp_data, strlen($exp_data));
			if (!$check) {
				export_error("Schreibfehler in Zeile: ".$act_pos+$pos_counter);
			}
			

		break;
		
		case "freestyle":
			include ("../classes/class_dataformat.php");
    		include ("../classes/class_parser.php");
    		include ("../classes/class_form.php");    		
    		$parser = new Parser();

			// Daten aus DB holen
			$query = $session->GetSessionVar("cmt_freestyle_query");
			$query .= " LIMIT $act_pos, $export_steps";
			$db->Query($query);

			while ($r = $db->Get(MYSQLI_ASSOC)) {
				
				// Stripslashes und Escapen
				foreach ($r as $key => $value) {
					$value = stripslashes($value);
					if ($escape) {
						if ($template_encoding == "utf8") {
							$value = utf8_htmlspecialchars($value);
						} else {
							$value = htmlspecialchars($value);
						}
					}
					$r[$key] = $value;
				}
				
				$parser->vars = $r;
				$exp_data .= $parser->ParseTemplate($template_row);
				
				$pos_counter++;
			}
				
			// Speichern
			$check = fputs($fp, $exp_data, strlen($exp_data));
			if (!$check) {
				export_error("Schreibfehler in Zeile: ".$act_pos+$pos_counter);
			}
		break;		

		case 'excel':
			$exp_file .= '.xls';
			//echo "<br><b>Excel-Export!: $exp_file</b>";
			//$exp_file = '#export_test.xls';
			// Variablen an dieser Stelle:
			// $act_pos -> Eintrags-Position im gesamten Datensatz/ der gesamten Tabelle (ein Vielfaches von $export_steps)
			// $export_steps -> Anzahl der Einträge, die gleichzeitig gelesen/exportiert werden
			// $fp -> File-Pointer
			// $progress -> false = Export startet soeben, true = Export läuft schon
			
			// Die Datei PEAR-Excel-Writer hier includen. (Datei steht im includes-Verzeichnis)
			if (!include('Spreadsheet/Excel/Writer.php'))
			{
				include ('PEAR_Excel_Writer.php');
			}
			
			$xls =& new Spreadsheet_Excel_Writer($exp_file);
  			$sheet =& $xls->addWorksheet('Export');
  			
  			//Formatierung Spaltenüberschrift
  			$format_col =& $xls->addFormat();
  			$format_col->setBold();
			$format_col->setAlign('center');
			$format_col->setSize(10);
			
		 			
  			
			// Export eben erst gestartet? Dann Spaltennamen schreiben
			if (!$progress) 
			{
				//$new_file = true;
				// Feldinformationen ermitteln
				$fields = $db->GetFieldInfo($cmt_dbtable);
				//$col = Spaltenangabe
				$col = 0;
				foreach ($fields['name'] as $key => $fn) 
				{
					$fieldnames[$key] = $fn;
					$fieldtypes[$fn] = $fields['type'][$key];
					$sheet->write(0,$col,$fn,$format_col);
					$col++;
				}
				
				// $fieldnames -> Array mit den Spaltennamen als Werte
			}			
			
			
			// Daten aus DB holen
			$query = "SELECT * FROM $cmt_dbtable";
			//$query .= " LIMIT $act_pos, $export_steps";
			$db->Query($query);
			
			//$row = Zeilenangabe
			//$col = Spaltenangabe
			$row = 1;
			$col = 0;

			//Ermittlung der Spaltenanzahl
			$spalten = count($fieldnames);
			while ($r = $db->Get(MYSQLI_ASSOC))
			{
				//for ($i = 0; $i < $spalten; $i++)
				foreach ($r as $key => $value)
				{
					// Nach Typen unterscheiden
					//echo 'Typ: '.$fieldtypes[$key].'<br>';
					switch ($fieldtypes[$key]) 
					{
						case 'date':
							$value = explode("-",$value);
							$value = $value[2].".".$value[1].".".$value[0];													
							break;
					
						case 'datetime':
							$value = explode("-",$value);
							$value_time = explode(" ",$value[2]);
							$value = $value_time[0].".".$value[1].".".$value[0]." ".$value_time[1];													
							break;
					
						case 'blob':
							$value = str_replace("\r\n", "\n", $value);
							$value = str_replace("\r", "\n",$value);
							break;
						
						default:
							$value = $value;							
													
							break;
					}	
					$sheet->write($row,$col,$value);
					$col++;
				}		
			$row++;
			$col = 0;
			}	
			// Abschließend den internen Datensatzzähler hochzählen
			$pos_counter++;
			$xls->close();
			
			// Reload-Schleife abbrechen
			$act_pos = $total_entries+1;
		break;
	}
	
	
	
	// Fertig oder Redirect einbauen? 
	$iframe_querystring = "?sid=".SID;
	$iframe_querystring .= "&export_type=".$export_type;
	$iframe_querystring .= "&export_file=".urlencode($export_file);
	$iframe_querystring .= "&export_directory=".urlencode($export_directory);
	$iframe_querystring .= "&csv_separator=".urlencode($csv_separator);
	$iframe_querystring .= "&template_frame=".urlencode($template_frame);
	$iframe_querystring .= "&template_row=".urlencode($template_row);
	$iframe_querystring .= "&template_encoding=".urlencode($template_encoding);
	$iframe_querystring .= "&escape=".$escape;
	$iframe_querystring .= "&append=1";
	$iframe_querystring .= "&cmt_orderstring=".urlencode($cmt_orderstring);

    if ($act_pos+$export_steps < $total_entries) {
    	$iframe_querystring .= "&cmt_dbtable=".$cmt_dbtable;
    	$iframe_querystring .= "&export_steps=".urlencode($export_steps);
    	$progress++;
		$iframe_querystring .= "&progress=".$progress;
    	$js_redirect = "this.location.href='".SELF.$iframe_querystring."';";
     } else {
    	switch ($export_type) {
    		case "csv":
    			echo "<font class=\"service_text\"> Alle Daten (".($act_pos+$pos_counter)." von ".$total_entries.") in </font>".basename($exp_file)."<font class=\"service_text\"> erfolgreich exportiert.</font>";
    			fclose($fp);
    			chmod ($exp_file, 0766);
    			$js_redirect = "";
    		break;
    		
    		case "cmt":
    			echo "&nbsp;".($act_pos+$pos_counter)." Einträge<font class=\"service_text\"> erfolgreich in </font>".basename($exp_file)."<font class=\"service_text\"> exportiert.</font>";
    			
    			unset ($exp_data);
    			if (!in_array($cmt_dbtable, $cmt_exptab_structonly)) {
    				$exp_data = "</tabledata>\n";
    			}
    			$exp_data .= "</cmt_dbtable>";
    			$check = @fputs($fp, $exp_data, strlen($exp_data));
    			fclose($fp);
    			chmod ($exp_file, 0766);
    			
    			// Noch eine Tabelle? Das hier noch verbessern! $cmt_exptab_name sollte immer ein Array sein!
    			if (is_array($cmt_exptab_name)) {
    				$cmt_dbtable = trim(array_shift($cmt_exptab_name));
    			}
    			if ($cmt_dbtable) {
    				// weitermachen
			    	$iframe_querystring .= "&cmt_dbtable=".$cmt_dbtable;
    				$js_redirect = "this.location.href='".SELF.$iframe_querystring."';";
    				$session->SetSessionVar("cmt_exptab_name", $cmt_exptab_name);
					//$session->SetSessionVar("cmt_exptab_structonly", $cmt_exptab_structonly);
					$session->SaveSessionVars();
    			} else {
    				$session->DeleteSessionVar("cmt_exptab_name");
    				$session->DeleteSessionVar("cmt_exptab_structonly");
    				$session->SaveSessionVars();
    				unset ($_GET);
    				unset ($_POST);
    				$js_redirect = "";
    			}
    		break;
			
			case "freestyle":
				$session->DeleteSessionVar("cmt_freestyle_query");
    			$session->SaveSessionVars();
    			// ... weiter wie mit einem Template-Export
    							
			case "template":
				@fclose ($fp);
				
				if ($template_frame != "") {
					$content = file_get_contents($exp_file);
					
					// Codierung
					switch ($template_encoding) {
						case "utf8":
							$content = utf8_encode($content);
						break;	
					}
					
					$parser->SetParserVar("content", $content);
					$content = trim($parser->ParseTemplate($template_frame));
					
					if ($content) {
						$fp = @fopen ($exp_file, "w+");
						$check = @fputs($fp, $content, strlen($content));
						if (!$check) {
							export_error("Konnte Rahmenvorlage".str_replace("../", "", $template_frame)." nicht speichern.");
						} else {
							echo " <font class=\"service_text\"> Alle Daten erfolgreich in Datei </font> ".basename($exp_file)."<font class=\"service_text\"> exportiert.</font>";
						}
					}
				} else {
					echo " <font class=\"service_text\"> Alle Daten erfolgreich in Datei </font> ".basename($exp_file)."<font class=\"service_text\"> exportiert.</font>";
				}
				@fclose ($fp);
    			$js_redirect = "";					
			break;
			
			case 'excel':
				$act_pos = $total_entries;
				$pos_counter = 0;
			break;
			
			default:
				$js_redirect = "";
    		break;

    	}
    	
    	// Arbeistschritte für alle Exporttypen
		
		// Falls 0 Einträge, Balken auf 100 Prozent setzten, sonst Javascritp-Fehler
		if (!$total_entries) {
			$act_pos = 1;
			$pos_counter = 0;
			$total_entries = 1;
		}
		
		// Kein Javascript-Redirect
		$js_redirect = '';
    }
    
    
	// Das funktioniert mit Firefox und IE:
    $javascript .= "top.cmt_applauncher.resize_progressbar(".($act_pos+$pos_counter).", ".$total_entries.");";
    $javascript .= $js_redirect;

    //echo "<script language=\"Javascript\">".$javascript."</script>";
	echo "<script language=\"JavaScript\">$javascript</script></body></html>";
    
?>