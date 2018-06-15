<?php
/*******************************************************
 * 
 * Content-o-mat - Kabasound Import-Include
 * 
 * Letzte Änderung: 20.07.2006
 * 
 *******************************************************/

    //////////////////////////////////////
    //
    // Funktionen
    //
    //////////////////////////////////////

	// Datei speichern
	function file_save ($file_data, $file_name) {
         $fp = fopen ($file_name, "w");
         if ($fp) {
            fwrite ($fp, $file_data);
            fclose ($fp);
            return true;
         } else {
             return false;
         }
     }

	// Daten zerlegen
	function xml_split ($xml_data, $split_tag) { // früher noch: $return_data_type
		$regexp = "/<".$split_tag.".*>(.*)<\/".$split_tag.">/Uis";
		preg_match_all ($regexp, $xml_data, $match);
      	return $match[1];
	 }
	
	// Daten zerlegen und zusätzlich Attribute mitgeben 
	function xml_split_attributes ($xml_data, $split_tag) { // früher noch: $return_data_type
		$regexp = "/<".$split_tag."\s{0,}(\w*)\=\"(.*)\".*>(.*)<\/".$split_tag.">/Uis";
		preg_match_all ($regexp, $xml_data, $match);
      	return $match;
	 }
	
	// Ermittelt die Reihen eines Eintrags -> wird nicht mehr gerbaucht
	 function xml_get_rows ($xml_data, $split_tag) {

	 	$regexp = "/<".$split_tag."\>(.*)<\/".$split_tag.">/Uism";	 	
	 	preg_match_all($regexp, $xml_data, $match);
	 	foreach ($match[1] as $field => $value) {
	 		$data[] = $value; 
	 	}
	 	return $data;
	 }

	 // Ermittelt die Tags innerhalb einer Reihe
	 function xml_get_tags ($xml_data) {

	 	$regexp = "/<(.*)\>(.*)<\/\\1>/Uis";
	 	preg_match_all($regexp, $xml_data, $match);
	 	foreach ($match[1] as $field => $value) {
	 		$data[$value] = $match[2][$field]; 
	 	}
	 	$c++;
	 	return $data;		
	 }
	 
	 // Fehler anzeigen
	 function import_error ($text, $cols=0) {
	 	global $tab;
	 	return $tab->TableMakeRow($text, $cols, "", "table_error");
	 }
	 
	 // Meldung anzeigen
	 function import_message ($text, $cols=0) {
	 	global $tab;
	 	return $tab->TableMakeRow($text, $cols, "", "table_ok");
	 }

    //////////////////////////////////////
    //
    // Includes und Anfangsgeraffel
    //
    //////////////////////////////////////

    require ("../cmt_functions.inc");
    require ("../cmt_constants.inc");
    include ("../classes/class_table.php");
    include ("../classes/class_form.php");
    include ("../classes/class_dbcex.php");
    include ("../classes/class_session.php");
    include ("../classes/class_user.php");
    //include ("classes/class_dataformat.php");

    // Session überprüfen
    $session = new Session($check_loggedin = true);
    $db = new DBCex();
    $user = new User(SID);
	$tab = new Table();

	// Übergebene Variablen holen
	$default_vars = array ("sid" => "", "action" => "", "handle_data" => "add", "import_type" => "cmt", "import_file" => "", "import_step" => 1,
						 "import_directory" => "admin/import_export/", "csv_separator" => "", "first_row" => "data", "cmt_dbtable" => "", "csv_type" => "csv",
						 "act_pos" => 1, "total" => 1, "cmt_imptab_structonly" => 0
						 );
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include ("../includes/func_get_vars.php");

	// Ausgabe beginnen
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
	<body>";
	
	//echo "<script>top.cmt_applauncher.resize_progressbar(60, 100);\ntop.cmt_applauncher.change_progressbar_color('#A53D3F', '#FFFFFF');</script>";
	//die();
//echo "Step: $import_step - act_pos: $act_pos - total: $total<br>";
	echo $tab->TableStart("100%");
	
	if ($user_message) {
		echo $user_message;
	}

	// Variablendefinitionen
	$pos_counter = 0;	
	
	if (!$import_file) {
		$user_message = import_error("Import abgebrochen: Keine Datei ausgewählt");
		unset($action);
	} else {
		// Import-Typ
		switch ($import_type) {
			case "cmt":
				// Session-Variablen
				$temp_tables = array();
				$temp_files = array(); 
				$temp_files = $session->GetSessionVar("temp_files");
				$temp_tables = $session->GetSessionVar("temp_tables");
				$cmt_dbtable = $temp_tables[0];
				
				// Verabreitungsschritte durchlaufen
				switch ($import_step) {
					
					// Dateien erstellen
					case "1":
						$session->DeleteSessionVar("temp_files_created");
						$session->DeleteSessionVar("temp_files");
						$session->DeleteSessionVar("temp_tables");
						$session->SaveSessionVars();
						unset ($temp_files);
						unset ($temp_tables);
// Schwindelige Änderunge am 2006-09-25						
						$import_filename = format_directory(PATHTOWEBROOT.$import_directory."/").$import_file;
						$filedata = file_get_contents($import_filename);					
						if (!$filedata) {
							$user_message = import_error("Import abgebrochen: Konnte Datei '$import_filename' nicht öffnen oder Datei leer.");
							unset($action);
							break;
						}

						$data = xml_split_attributes ($filedata, "cmt_dbtable");
						foreach ($data[0] as $key => $filedata_part) {
							$temp_filename = "tempfile_".$key.".tmp";
							$check_filesave = file_save ($filedata_part, format_directory(PATHTOADMIN."/temp/").$temp_filename);
							$temp_files[] = $temp_filename;
							$temp_tables[] = $data[2][$key];
						}

						$session->SetSessionVar("temp_files", $temp_files);
						$session->SetSessionVar("temp_files_created", $temp_files);
						$session->SetSessionVar("temp_tables", $temp_tables);
						$session->SaveSessionVars();					
						$cmt_dbtable = $temp_tables[0];
						$total = (count($temp_files))*3+1;
						$act_pos = 1;		
						$import_step++;
					break;
					
					// Tabelle erstellen
					case "2":
						// Import-Ordner holen
						$db->Query("SELECT id FROM cmt_tables_groups WHERE cmt_isimportgroup = '1' ORDER BY cmt_grouppos LIMIT 1");
						$r = $db->Get(MYSQLI_ASSOC);
						$importGroupId = $r['id'];
						
						// höcshte Position innerhalb der Import-Gruppe holen
						$db->Query("SELECT MAX(cmt_itempos) as maxpos FROM cmt_tables WHERE cmt_group = '$importGroupId'");
						$r = $db->Get(MYSQLI_ASSOC);
						$importItemPos = intval($r['maxpos'])+1;
						
						$import_filename = format_directory(PATHTOADMIN."/temp/").$temp_files[0];
						$filedata = file_get_contents($import_filename);
						
						$fielddefinitions = xml_split ($filedata, "fielddefinitions");

						if (trim($fielddefinitions[0]) == "") {
							$user_message = import_error("Import abgebrochen, da die Daten unvollständig sind: In der Import-Datei fehlen die Feldangaben!");
							//$act_pos = $total;
							$error = true;
							break;
						}
						
						$tabledefinitions = xml_split ($filedata, "tabledefinitions");
						
						if (trim($tabledefinitions[0]) == "") {
							$user_message = import_error("In der Import-Datei fehlen die Tabellenangaben!");
							// auskommentiert, weil Fehler noch unkritisch:
							//$act_pos = $total + 1;
							//break;
						}
															
						$data = xml_get_tags($tabledefinitions[0]);
						if (is_array($data)) {
							$data['cmt_group'] = $importGroupId;
							$data['cmt_itempos'] = $importItemPos;
							
							foreach ($data as $field => $value) {
								$query_fields[$field] = $field." = '".addslashes($value)."'";
							}
							
							$query_string = implode(", ", $query_fields);
						} else {
							$query_string = "";
						}
						
						switch ($handle_data) {
							case "add":
							case "update":
								if ($query_string) {
									$query = "SELECT COUNT(*) AS nr_entries FROM cmt_tables WHERE cmt_tablename = '".$data['cmt_tablename']."'";
									unset ($query_fields['id']);
									$db->Query($query);
									$r = $db->Get();
									if (!$r['nr_entries']) {
										$query = "INSERT INTO cmt_tables SET ".implode(", ", $query_fields);
									} else {
										$query = "UPDATE cmt_tables SET ".implode(", ", $query_fields)." WHERE cmt_tablename = '".$data['cmt_tablename']."'";
									}
								}
							break;
								
							case "delete":
								// Tabelle löschen: In cmt_tables...
								$query = "DELETE FROM cmt_tables WHERE cmt_tablename = '".$cmt_dbtable."'";
								$db->Query($query);
									
								// ... in cmt_fields
								$query = "DELETE FROM cmt_fields WHERE cmt_tablename = '".$cmt_dbtable."'";
								$db->Query($query);
									
								// ... und Tabelle
								$query = "DROP TABLE IF EXISTS ".$cmt_dbtable;
								$db->Query($query);
					
								if ($db->last_ErrorNr()) {
									$user_message = import_error("Konnte alte Tabelle '$cmt_dbtable' nicht löschen: ".$db->last_error()." (".$db->last_errorNr().")");
									$act_pos = $total;
									$error = true;
									unset($query);
									break;
								}
								unset ($query);
								
								if ($query_string) {								
									$query = "INSERT INTO cmt_tables SET ".$query_string;
								}
							break;
						}
							
						// Tabelle wirklich erstellen
						$tab_query = "CREATE TABLE IF NOT EXISTS ".$cmt_dbtable." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
						$db->Query($tab_query);
						if ($db->last_errorNr()) {
							$user_message = import_error("Konnte Tabelle '$cmt_dbtable' nicht erstellen: ".$db->last_error()." (".$db->last_errorNr().")");
							$act_pos = $total;
							$error = true;
							$query = "";
							//die ("Tabelle $cmt_dbtable nicht erstellt");
							break;
						} else {
							//die ("Tabelle $cmt_dbtable erstellt");
						}
							
						// Und Felder einfügen
						if (trim($query) != "") {	
							$db->Query($query);
							if ($db->last_ErrorNr()) {
								$user_message = import_error("Fehler beim Speichern der Tabelleninformationen für Tabelle '$cmt_dbtable': ".$db->last_error()." (".$db->last_errorNr().")");
								//$act_pos = $total;
								$error = true;
							}
						}

						$import_step++;
						$act_pos++;
					break;
					
					// Felder erstellen
					case "3":
					//echo $cmt_dbtable;
						$mysql_type[""] = "VARCHAR(255)";
						$mysql_type["string"] = "VARCHAR(255)";
						$mysql_type["text"] = "TEXT";
						$mysql_type["integer"] = "INT";
						$mysql_type["float"] = "FLOAT";
						$mysql_type["flag"] = "TINYINT";
						$mysql_type["date"] = "DATE";
						$mysql_type["datetime"] = "DATETIME";
						$mysql_type["time"] = "TIME";
						$mysql_type["select"] = "VARCHAR(255)";
						$mysql_type["select_recursive"] = "VARCHAR(255)";
						$mysql_type["link"] = "VARCHAR(255)";
						$mysql_type["html"] = "TEXT";
						$mysql_type["position"] = "INT NOT NULL";
						$mysql_type["upload"] = "VARCHAR(255)";
						$mysql_type["system_var"] = "VARCHAR(255)";
						
						$fields = $db->GetFieldInfo($cmt_dbtable);
						$field_names = $fields['name'];
						
						$import_filename = format_directory(PATHTOADMIN."/temp/").$temp_files[0];
						$filedata = file_get_contents($import_filename);
						$fielddefinitions = xml_split ($filedata, "fielddefinitions");

						if (trim($fielddefinitions[0]) == "") {
							$user_message = import_error("In der Import-Datei fehlen die Feldangaben!");
							//$act_pos = $total + 1;
							$error = true;
							break;
						}

						$fields = xml_split ($fielddefinitions[0], "field");
 
						foreach ($fields as $field) {
							
							$data = xml_get_tags($field);
							foreach ($data as $f => $value) {
								$query_fields[$f] = $f." = '".addslashes($value)."'";
							}
							unset ($query_fields['id']);

							switch ($handle_data) {
								case "add":
								case "update":
									$query = "SELECT COUNT(*) AS nr_entries FROM cmt_fields WHERE cmt_tablename = '".$data['cmt_tablename']."' AND cmt_fieldname = '".$data['cmt_fieldname']."'";
									$db->Query($query);
									$r = $db->Get(MYSQLI_ASSOC);
									//echo $query.": ".$r['nr_entries']." - ";
									if (!$r['nr_entries']) {
										$query = "INSERT INTO cmt_fields SET ".implode(", ", $query_fields);
									} else {
										$query = "UPDATE cmt_fields SET ".implode(", ", $query_fields)." WHERE cmt_tablename = '".$data['cmt_tablename']."' AND cmt_fieldname = '".$data['cmt_fieldname']."'";
									}
									//echo $query."<br>";
								break;
								
								case "delete":
									$query = "DELETE FROM cmt_fields WHERE cmt_tablename = '".$data['cmt_tablename']."' AND cmt_fieldname = '".$data['cmt_fieldname']."'";
									$db->Query($query);
									if ($db->last_ErrorNr()) {
										$user_message = import_error("Fehler beim Löschen der Feldinformationen für Feld '".$data['cmt_fieldname']."': ".$db->last_error." (".$db->last_errorNr().")");
										//$act_pos = $total;
										$error = true;
										$query = "";
										break;
									}
									$query = "INSERT INTO cmt_fields SET ".implode(", ", $query_fields);
								break;
							}
							// Felder tatsächlich erstellen
							if (in_array($data['cmt_fieldname'], $field_names)) {
								$db_query = "ALTER TABLE ".$cmt_dbtable." CHANGE ".$data['cmt_fieldname']." ".$data['cmt_fieldname']." ".$mysql_type[$data['cmt_fieldtype']];
							} else {
								$db_query = "ALTER TABLE ".$cmt_dbtable." ADD ".$data['cmt_fieldname']." ".$mysql_type[$data['cmt_fieldtype']];
							}
							
							// Feld erstellen
							$db->Query($db_query);
							if ($db->last_ErrorNr()) {
								$user_message = import_error("Fehler beim Erstellen/ Ändern der Spalte '".$data['cmt_fieldname']."' in der Tabelle '$cmt_dbtable': ".$db->last_error()." (".$db->last_errorNr().")");
								echo $db_query;
								//$act_pos = $total;
								$error = true;
							}
							
							// Feld eintragen in cmt_fields
							$db->Query($query);
							if ($db->last_ErrorNr()) {
								$user_message = import_error("Fehler beim Speichern der Tabelleninformationen für Tabelle '$cmt_dbtable': ".$db->last_error()." (".$db->last_errorNr().")");
								//$act_pos = $total;
								$error = true;
							}
						}
						$import_step++;
						$act_pos++;
					break;
					
					case "4":
						if ($cmt_imptab_structonly) {
							$import_step++;
							$act_pos++;
							
							// Falls letzter Schritt: Meldung anzeigen
							if ($act_pos >= $total) {
								$user_message = import_message("Alle Strukturen erfolgreich importiert.");
							} 
						} else {						
							$import_filename = format_directory(PATHTOADMIN."/temp/").$temp_files[0];
							$filedata = file_get_contents($import_filename);
							$tabledata = xml_split ($filedata, "tabledata");
							$rows = xml_split($tabledata[0], "row");
				
							if ($handle_data == "delete") {
								//$query = "TRUNCATE TABLE ".$cmt_dbtable;
							}
							
							$total_rows = count ($rows);
							$c = 0;
							
							foreach ($rows as $row) {
								$data = xml_get_tags($row);
								foreach ($data as $f => $value) {
									$query_fields[$f] = $f." = '".addslashes($value)."'";
								}
								switch ($handle_data) {
									
									case "update":
										$query = "SELECT COUNT(*) AS nr_entries FROM ".$cmt_dbtable." WHERE id = '".$data['id']."'";
										unset ($query_fields['id']);
										
										$db->Query($query);
										$r = $db->Get();
										if (!$r['nr_entries']) {
											$query = "INSERT INTO ".$cmt_dbtable." SET ".implode(", ", $query_fields);
										} else {
											$query = "UPDATE ".$cmt_dbtable." SET ".implode(", ", $query_fields)." WHERE id = '".$data['id']."'";
										}
									break;
									
									case "add":
										unset ($query_fields['id']);
										$query = "INSERT INTO ".$cmt_dbtable." SET ".implode(", ", $query_fields);
									break;
									
									case "delete":
										$query = "INSERT INTO ".$cmt_dbtable." SET ".implode(", ", $query_fields);
									break;
								}	
								// Zeile speichern
								$c++;
								//echo $query."<br>";
								$db->Query($query);
								if ($db->last_ErrorNr()) {
									$user_message = import_error("Fehler beim Speichern der Daten für Tabelle '$cmt_dbtable', Zeile $c von $total_rows: ".$db->last_error()." (".$db->last_errorNr().")");
									//$act_pos = $total+1;
									$error = true;
									break;
								}
							}
	
							$import_step++;
							$act_pos++;
							
							// Falls letzter Schritt: Meldung anzeigen
							if ($act_pos >= $total && !$user_message) {
								$user_message = import_message("Alle Daten erfolgreich importiert.");
							} 
						}
					break;
				}
			break;
			
			case "csv":
				
				$act_pos = 1;
				$total = 1;
				if ($handle_data == "update" && $first_row != "colnames") {
					$user_message = import_error("Import abgebrochen: Wenn ein Update vorhandener Daten durchgeführt werden soll, muss die Import-Datei Spaltennamen und eine ID-Spalte beinhalten.");
					unset($action);
					break;
				}
				
				$default_seperators = array ("csv" => ",", "excel" => ";");
				
				if ($csv_type != "own") {
					$csv_separator = $default_seperators[$csv_type];
				}

				// Alles ok, Datei öffnen
				$import_filename = format_directory(PATHTOWEBROOT.$import_directory."/").$import_file;
//die($import_filename);
				$fp = @fopen ($import_filename, "r");	
				if (!$fp) {
					$user_message = import_error("Import abgebrochen: Konnte Datei '$import_file' nicht öffnen.");
					unset($action);
					break;
				}

				// erste Zeile Daten oder Spaltennamen?
				if ($first_row == "colnames") {
					$colnames = fgetcsv ($fp, 4096, $csv_separator);	// event. mehr KB -> so darf die erste Zeile max 4 KB groß sein
					$colnames = array_flip($colnames);
					
					// ID groß der klein geschrieben?
					if (is_array($colnames)) {
						foreach ($colnames as $key=>$value) {
							if (strtolower($key) == 'id') {
								unset ($colnames[$key]);
								$colnames['id'] = $value;
								break;
							}
						}
					}
					$id_field = $colnames['id'];
					$colnames = array_flip($colnames);
				} else {
					$fields = $db->GetFieldInfo($cmt_dbtable);
					$fieldnames = $fields[name];
					unset($fieldnames['id']);
					
					foreach ($fieldnames as $fieldname) {
						$colnames[] = $fieldname;
					}
				}
				// Import-Art: Alte Dateien löschen
				if ($handle_data == "delete") {
					$query = "TRUNCATE TABLE $cmt_dbtable";
					$db->Query($query);
					// Geklappt?
					if ($db->Last_ErrorNr()) {
						$user_message = $user_message = import_error("Konnte Tabelle $cmt_dbtable nicht löschen, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr());
						$action = "";
						break;
					}
				}
				
				$import_stats = array ("added" => 0, "updated" => 0, "errors" => 0);
				
				// Import starten
				while ($imported_row = fgetcsv ($fp, 4096, $csv_separator)) {
					
					// SET-Teil der Query erstellen
					foreach ($colnames as $key => $colname) {
						if ($colname) {
							$setfields_query[$key] = $colname." = '".addslashes($imported_row[$key])."'";
						}
					}
					// Statistik: normal ist "added"
					$stat_type = "added";
					
					// Importart: Updaten
					if ($handle_data == "update") {
						// Update oder neu eintragen: Testen, ob es den Eintrag schon gibt
						$query_check = "SELECT COUNT(id) AS check_id FROM $cmt_dbtable WHERE id ='".$imported_row[$id_field]."'";
						$db->Query($query_check);
						$r = $db->Get();
						if ($r['check_id']) {
							// Eintrag bereits vorhanden: updaten
							$query_start = "UPDATE $cmt_dbtable SET ";
							$query_end = " WHERE id = '".$imported_row[$id_field]."'";
							$stat_type = "updated";
						} else {
							// Eintrag hinzufügen
							$query_start = "INSERT INTO $cmt_dbtable SET ";
							$query_end = "";
						}							
					}
					// Importart: neu eintragen, bzw. anfügen
					else {
						$query_start = "INSERT INTO $cmt_dbtable SET ";
						$query_end = "";
						if ($handle_data != "delete") {
							unset ($setfields_query[$id_field]);
						}
					}
					$query_set = implode(", ", $setfields_query);
					$query = $query_start.$query_set.$query_end;
					//echo $query."<br>";
					$db->Query($query);
					
					if ($db->Last_ErrorNr()) {
						$stat_type = "errors";
						
						if ($imported_row[$id_field]) {
							$error = "Eintrag-ID: ".$imported_row[$id_field].", ";
						}
						 
						$error = "Fehler: ".$db->Last_Error()." (".$db->Last_ErrorNr().")";
						$errors[] = $error;
						unset ($error);
					}
					
					// abschließende Definitionen
					$import_stats[$stat_type]++;
					unset ($setfields_query);
					unset ($query);
					unset ($query_start);
					unset ($query_end);
				}
				// Import fertig
				$message_text = "Es wurden ".$import_stats['added']." Einträge hinzugefügt und ".$import_stats['updated']." aktualisiert (Update).";
				if ($import_stats['errors']) {
					$message_text .= "<br>Dabei traten ".$import_stats['errors']." Fehler auf:<p>".implode("<br>", $errors);
				}
				if ($import_stats['errors']) {
					$user_message = $tab->TableMakeRow($message_text, 0, "", "table_error");
				} else {
					$user_message = $tab->TableMakeRow($message_text, 0, "", "table_ok");
				}
				$action = "";
				unset ($csv_separator);
				fclose($fp);
			break;
		}
	}
	
	// Fertig oder Redirect einbauen? 
	$iframe_querystring = "?sid=".SID;
    $iframe_querystring .= "&cmt_dbtable=".$cmt_dbtable;
	$iframe_querystring .= "&import_type=".$import_type;
	$iframe_querystring .= "&import_file=".urlencode($import_file);
	$iframe_querystring .= "&import_directory=".$mport_directory;
	$iframe_querystring .= "&csv_type=".$csv_type;
	$iframe_querystring .= "&first_row=".$first_row;
	$iframe_querystring .= "&handle_data=".$handle_data;
	$iframe_querystring .= "&total=".$total;
	//$iframe_querystring .= "&act_pos=".$act_pos;
	$iframe_querystring .= "&cmt_imptab_structonly=".$cmt_imptab_structonly;
	$iframe_querystring .= "&csv_separator=".urlencode($csv_separator);
	
	
	 // Fehler?
     if ($error)  {
    	switch ($import_type) {
    		case "csv":
    			echo $user_message;
    			$js_redirect = "top.cmt_applauncher.change_progressbar_color(#A53D3F, #FFFFFF);";
    		break;
    		
    		case "cmt":
    			$temp_files_created = $session->GetSessionVar("temp_files_created");
    			
				foreach ($temp_files_created as $temp_file) {
					//unlink (format_directory(PATHTOADMIN."/temp/").$temp_file);
				}

				$session->DeleteSessionVar("temp_files_created");
				$session->DeleteSessionVar("temp_files");
				$session->DeleteSessionVar("temp_tables");
				$session->SaveSessionVars();
				
	   			echo $user_message;
    			$js_redirect = "\ntop.cmt_applauncher.change_progressbar_color('#A53D3F', '#FFFFFF');";
    		break;
    	}
    } else
    
    // Import noch am Laufen?
    if ($act_pos < $total) {
		// Sonderbehandlung für CMT-Format:
    	// Alle Import-Schritte durchlaufen?
    	if ($import_step > 4) {
    		$import_step = 2;
    		
    		// Nächste Tabelle, nächstes File
    		array_shift($temp_tables);
    		array_shift($temp_files);
			
    		// ... kommt noch eine Tabelle? Dies nur zur Sicherheit.
    		if (!$temp_tables[0] || !$temp_files[0]) {
    			$act_pos = $total;
    		}
			
			// ... als Sessionvariablen speichern
			$session->SetSessionVar("temp_files", $temp_files);
			$session->SetSessionVar("temp_tables", $temp_tables);
			$session->SaveSessionVars();	
    	}
		$iframe_querystring .= "&act_pos=".$act_pos;
		$iframe_querystring .= "&import_step=".$import_step;
    	$js_redirect = "this.location.href='".SELF.$iframe_querystring."';";
    	
     } else 
    	
	 // Import Ende?     
     if ($act_pos >= $total)  {
    	switch ($import_type) {
    		case "csv":
    			echo $user_message;
    			$js_redirect = "";
    		break;
    		
    		case "cmt":
    			$temp_files_created = $session->GetSessionVar("temp_files_created");
    			
				foreach ($temp_files_created as $temp_file) {
					unlink (format_directory(PATHTOADMIN."/temp/").$temp_file);
				}

				$session->DeleteSessionVar("temp_files_created");
				$session->DeleteSessionVar("temp_files");
				$session->DeleteSessionVar("temp_tables");
				$session->SaveSessionVars();
				
	   			echo $user_message;
    			$js_redirect = "";
    		break;
    	}
    }
    
    
	// Das funktioniert mit Firefox und IE:
    $javascript .= "top.cmt_applauncher.resize_progressbar(".($act_pos).", ".$total.");";
    $javascript .= $js_redirect;

    //$javascript = "for (a in top.cmt_applauncher.document.getElementById('progress_bar')) {alert(a+', value:'+a.value);};\n";
//    echo "<script language=\"Javascript\">".$javascript."</script>";
	echo $tab->TableEnd();
	echo "<script language=\"JavaScript\">".$javascript."</script></body></html>";
    
?>