<?php
/**
 * OUTDATED!!!!
 * 
 * globale PHP-Funktionen
 * 
 * Funktionsbibliothek, die Funktionen CMS weit zur Verf�gung stellt
 * 
 * Funktionen, die im gesamten CMS verwendet werden sollen, m�ssen in
 * diese Datei geschrieben werden. Sie wird in cmt_applauncher.php eingebunden
 * und ist somit �berall verf�gbar.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2007-10-08
 */
namespace Contentomat;

	//////////////////////////////////////////////////
	// timestamp2datetime (Zeitstempel)
	// -> macht MySQL-Datetime aus einem Zeitstempel
	//////////////////////////////////////////////////
	
	function timestampToDatetime ($time='') {
		return timestamp2datetime ($time);
	}
	
	function timestamp2datetime ($time='') {
	    if (!$time) {
	    	$time = time();
	    }
	    return date("Y-m-d H:i:s", $time);
	}

	//////////////////////////////////////////////////
	// getMicrotime ()
	// -> holt aktuelle Zeit in Millisekunden
	// (von www.php.net)
	//////////////////////////////////////////////////

	function getMicrotime()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

	//////////////////////////////////////////////////
	// format_directory (Pfadangabe)
	// -> entfernt "./" vom Anfang einer Pfadangabe
	// -> entfernt "//" 
	// -> f�gt ein fehlendes "/" ans Ende des Pfades
	//////////////////////////////////////////////////

	function format_directory ($directory) {
		$directory = preg_replace ("/^\.\//", "", $directory);
		$directory = preg_replace ("/\/{2,}/", "/", $directory);
		if (!preg_match("/\/$/", $directory)) {
			$directory .= "/";
		}
		$directory = preg_replace ("/^\//", "", $directory);
		return $directory;
	}
	//////////////////////////////////////////////////////////////
	// eval_user_code (code)
	// -> $return_vars_here muss im eingebundenen Code erzeugt werden
	//////////////////////////////////////////////////////////////
	function eval_user_code ($code, $vars) {
		$code = preg_replace ("/^(\<\?php|\<\?|\<\%)/", "", $code);
		$code = preg_replace ("/(\%\>|\?\>)$/", "", $code);
		
		if (!is_array($vars)) {
			$vars[0] = $vars;
		}
		extract ($vars);
		eval ($code);
		$vars_here = get_defined_vars();
		unset ($vars_here['code']);
		unset ($vars_here['GLOBALS']); // PHP5 liefert GLOBALS mit!
		
		//$return_data['add_query'] = $add_query;
		//$return_data['ownservice_html'] = $content;
		return $vars_here;
	}		

	//////////////////////////////////////////////////////////////
	// array_trim (array)
	// -> trimmt jedes Element des Arrays
	//////////////////////////////////////////////////////////////
	function array_trim ($array) {
		foreach ($array as $key => $value) {
			$array[$key] = trim($value);
		}
		return $array;
	}

	//////////////////////////////////////////////////////////////
	// -> Datenbankdaten richtig f�r das Schreiben in
	// MySQL-Tabelle vorbereiten, sicherer bei unbekannten
	// Benutzereingaben, von www.php.net
	//////////////////////////////////////////////////////////////

// Achtung: Mittlerweile ersetzt durch die Methode "mysqlQuote" der DBCex-Klasse
	function mysql_quote($value) {
   		if (get_magic_quotes_gpc()) {
       		$value = stripslashes($value);
   		}

   		// quotieren, falls kein integer
   		if (!is_int($value)) {
       		$value = mysql_real_escape_string($value);
   		}
		
   		return $value;
	}

	//////////////////////////////////////////////////////////////
	// -> Benutzermeldungen:
	// error, sucess, warning
	// 
	//////////////////////////////////////////////////////////////
	
	 function user_message ($text, $cols, $type="", $html_type="table") {
	 	
	 	switch ($html_type) {
	 		case "div":
	 			$div = new Div();
	 			return $div->DivMakeDiv($text, 'cmtMessage '. $type);
	 		break;
	 		
	 		default:
	 			$tab = new Table();
	 			return $tab->TableMakeRow($text, $cols, "", 'cmtMessage '.$type);
	 		break;	
	 	}
	 }
	 function success ($text, $cols=0, $html_type='div') {
	 	return user_message($text, $cols, "success", $html_type);
	 }	 
	 
	 function error ($text, $cols=0, $html_type='div') {
	 	return user_message($text, $cols, "error", $html_type);
	 }

	 function message ($text, $cols=0, $html_type='div') {
	 	return user_message($text, $cols, "message", $html_type);
	 }	 

	 function warning ($text, $cols=0, $html_type='div') {
	 	return user_message($text, $cols, "warning", $html_type);
	 }


	//////////////////////////////////////////////////////////////
	// -> Tabellenmanager: Splittet cmt_addvars in Einzelvariablen
	// auf und gibt Array mit den Variablen zur�ck:
	// $key = Varname, $value = Var
	//////////////////////////////////////////////////////////////
	 
 	function get_addvars ($table_addvars) {
		if (trim($table_addvars) != "") {
			$add_vars = explode("\n", trim($table_addvars));
		
			foreach ($add_vars as $add_var) {
				$add_var_temp = explode ("=", $add_var);
				eval ("\$item_var['".trim($add_var_temp[0])."'] = \"".trim($add_var_temp[1])."\";");
			}
		}
		return $item_var;
	}

	
	//////////////////////////////////////////////////////////////
	// -> S�ubert Array mit Datenbanktabellennamen nach Kriterien
	// auf Basis von regul�ren Ausdr�cken
	// gibt gefiltertes Ursprungs-Array zur�ck
	//////////////////////////////////////////////////////////////
	
	function clean_dbtables_array ($dbt, $rgs, $condition=false) {
	 	if (!is_array($dbt)) {
	 		$dbtables[0] = $dbt;
	 	} else {
	 		$dbtables = $dbt;
	 	}
	 	
	 	if (!is_array($rgs)) {
	 		$regexps[0] = $rgs;
	 	} else {
	 		$regexps = $rgs;
	 	}
	 	$dbtables_filtered = array();
	 	
	 	foreach ($dbtables as $dbtable) {
	 		foreach ($regexps as $regexp) {
	 			
	 			// Ausdruck extrahieren und pr�parieren
	 			if (!preg_match("/^\//", $regexp)) {
	 				$regexp = "/".$regexp;
	 			}
	 			if (!preg_match("/\/[imsxUADu]{0,8}$/", $regexp)) {
	 				$regexp .= "/";
	 			}
				$check_name = preg_match($regexp, $dbtable);
	 			if ((!$condition && !$check_name) || ($condition && $check_name))	{
	 				$dbtables_filtered[] = $dbtable;
	 			}
	 		}
	 	}
	 	
	 	return $dbtables_filtered;
	}

	
	//////////////////////////////////////////////////////////////
	// -> Erzeugt strukturiertes Select-Men� der vorhandenen
	// Tabellen und/oder Applikationen
	//
	//////////////////////////////////////////////////////////////
	
	function makeEntrySelection ($name, $value, $show="", $add_html="", $groupSelectable=false) {
		$db = new DBCex();
		$query = "SELECT id, cmt_groupname FROM cmt_tables_groups ORDER BY cmt_grouppos";
		$db->Query ($query);
	
		while ($r = $db->Get(MYSQLI_ASSOC)) {
			$groups[$r['id']] = $r['cmt_groupname'];
		}
		
		$show_restrictions['tables'] = ' WHERE cmt_type = \'table\' ';
		$show_restrictions['applications'] = ' WHERE cmt_type = \'application\' ';
		
		// Dropdown: Tabellen
		$group_old = '';	
		$query = 'SELECT id, cmt_showname, cmt_group FROM cmt_tables'.$show_restrictions[$show].' ORDER BY cmt_group, cmt_itempos ASC';
		$db->Query($query);
		while ($r = $db->Get()) {
			$item_ids[$r['cmt_group']][] = $r['id'];
			$item_names[$r['cmt_group']][] = $r['cmt_showname']; 
		}
		$select_item = '<select name="'.$name.'"';
		if ($add_html) {
			$select_item .= ' '.$add_html;
		}		
		$select_item .= '>';
		
		foreach ($groups as $group_id => $group_name) {
			//$select_item .= '<option value="#" class="select_group">'.$group_name.'</option>';
			if ($groupSelectable) {
				$select_item .= '<option value="group_'.$group_id.'"';
				//echo $value.' -> '.'group_'.$group_id.'<br>';
				if ($value == 'group_'.$group_id) $select_item .= ' selected="selected"';
				$select_item .= '>'.$group_name.'</option>'."\n";
			} else {
				$select_item .= '<optgroup label="'.$group_name.'">'."\n";
			}
			if (is_array($item_ids[$group_id])) {
				foreach ($item_ids[$group_id] as $key => $item_id) {
					$item_name = $item_names[$group_id][$key];
					$select_item .= '<option value="'.$item_id.'"';
					if ($item_id == $value) {
						$select_item .= " selected";
					}
					$select_item .= ">".$item_name."</option>\n";
				}
			}
		}
		$select_item .= "</select>";
		
		$db->Close();
		return $select_item;
	}
	
	//////////////////////////////////////////////////////////////
	// -> Gzip-Kompression starten sofern Browser das 
	// unterst�tzt
	//
	//////////////////////////////////////////////////////////////

	function ob_start_gzipped() {
		$encode = getenv('HTTP_ACCEPT_ENCODING');
		if (stristr($encode, 'gzip')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}
	}
	
	//////////////////////////////////////////////////////////////
	// -> sicheres serialisieren: Auch " und ; gehen
	//
	//////////////////////////////////////////////////////////////
	
	function safeSerialize ($var) {
		
		if (!$var) {
			return;
		}
		
		$search = array('"', ';', '\\');
		$replace = array('__quot__', '__scol__', '__bksl__');
		
		if (is_array($var)) {
			foreach ($var as $key=>$value) {
				if (is_array($value)) {
					$var[$key] = safeSerialize($value);
				} else {
					$var[$key] = str_replace($search, $replace, $value);
				}
			}
		} else {
			$var = str_replace($search, $replace, $var);
		}
		return serialize($var);
	}
		
	//////////////////////////////////////////////////////////////
	// -> sicheres entserialisieren: Auch " und ; gehen
	//
	//////////////////////////////////////////////////////////////
	
	function safeUnserialize ($var) {
		$var = unserialize($var);
		if (is_array($var)) {
			$var = safeUnserializeArray($var);
		} else {
			$var = safeUnserializeCleanUp($var);
		}
		return $var;
	}	

	function safeUnserializeCleanUp ($var) {
		
		$search = array('__quot__', '__scol__', '__bksl__');
		$replace = array('"', ';', '\\');
		
		return str_replace($search, $replace, $var);
	}

	function safeUnserializeArray ($array) {
		foreach ($array as $key=>$value) {
			if (is_array ($value)) {
				$array[$key] = safeUnserializeArray($value);
			} else {
				// unserialize gibt false zur�ck, wenn es nichts zu unserialisierne gibt, nicht den �bergebenen Wert!
				$tmp_value = unserialize($value);
// echo "$key IST: $tmp_value<br />";
				if (is_array($tmp_value)) {
					$array[$key] = safeUnserializeArray($tmp_value);
				} else if ($tmp_value) {
					$array[$key] = safeUnserializeCleanUp($tmp_value);
				} else {
					$array[$key] = safeUnserializeCleanUp($value);
				}
			}
		}
		return $array;
	}

	//////////////////////////////////////////////////////////////
	// -> MySQL-Zeichensatz der Tabelle in HTML �bersetzen
	//
	//////////////////////////////////////////////////////////////

	function wrapCharset($mysql_charset) {
		$html_charsets = array (	'latin1' => 'ISO 8859-1',
									'latin2' => 'ISO 8859-2',
									'latin5' => 'ISO 8859-9',
									'latin7' => 'ISO 8859-13',
									'utf8' => 'UTF-8'
								 );

		if ($html_charsets[$mysql_charset]) {
			define ('CMT_CHARSET', $html_charsets[$mysql_charset]);
		} else {
			define ('CMT_CHARSET', CMT_DEFAULTCHARSET);
		}
		//echo 'mysql: '.$mysql_charset.', Wrapper: '.$html_charsets[$mysql_charset].', HTML: '.CMT_CHARSET;
	}

	
	//////////////////////////////////////////////////////////////
	// -> Macht aus ass. Array einen MySQL-Querystring
	//
	//////////////////////////////////////////////////////////////
	function makeSetQuery ($vars=array()) {
		foreach ($vars as $k=>$v) {
			$query_temp[] = $k."='".mysql_quote($v)."'";
		}
		return implode(', ', $query_temp);
	}
	
	//////////////////////////////////////////////////////////////
	// -> stripslashes f�r Arrays
	//
	//////////////////////////////////////////////////////////////
	function stripslashesArray ($vars=array()) {
		foreach ($vars as $k=>$v) {
			$vars[$k] = stripslashes($v);
		}
		return $vars;
	}

	/**
	 * function utf8EncodeArray()
	 * Wandelt alle Elemente eines Arrays in den UTF8-Zeichensatz um. Die Schl�ssel werden beibehalten.
	 * 
	 * @param $vars Array Die Variablen als Array
	 * @return Array Das Array mit Elementen im UTF8-Zeichensatz
	 */
	function utf8EncodeArray($vars=array()) {
		foreach ($vars as $k=>$v) {
			$vars[$k] = utf8_encode($v);
		}
		return $vars;
	}
	
// TODO: sollte in Klasse DBCex!!!!	
	//////////////////////////////////////////////////////////////
	// -> gibt die Version der MySQL-Datenbank zur�ck
	//
	//////////////////////////////////////////////////////////////
	function getMysqlVersion() {
		$db = new DBCex();
		$db->Query("SHOW VARIABLES LIKE 'version'");
		$r = $db->Get(MYSQLI_ASSOC);
		return $r['Value'];
	}

// TODO: sollte in Klasse DBCex!!!!	
	//////////////////////////////////////////////////////////////
	// -> gibt Variablen der MySQL-Datenbank zur�ck
	//
	//////////////////////////////////////////////////////////////
	function getMysqlVariables($var='%') {
		$db = new DBCex();
		$db->Query("SHOW VARIABLES LIKE '$var'");
		while ($r = $db->Get(MYSQLI_ASSOC)) {
			return $r;
		}
	}

// TODO: sollte in Klasse DBCex!!!!	
	/**
	 * function getAvailableCharsets
	 * 
	 * Liefert die verf�gbaren MySQL-Zeichnes�tze zur�ck, entweder
	 * innerhalb von <option>-Tags einer HTML-<select>-Liste oder
	 * als Rohdaten.
	 * 
	 * @param $raw boolean true gibt die Rohdaten zur�ck, false (default)
	 * eine formatierte <option>-Liste
	 * 
	 * @return array/string Je nach gew�hlter Option wird ein String (HTML)
	 * oder ein Array (Schl�ssel = Zeichensatzname, Wert = Beschreibung)
	 * zur�ckgegeben.
	 */

	function getAvailableCharsets($raw=false) {
		$charsets = array (
			"utf8" => 'UTF-8 Unicode',
			"latin1" => 'ISO 8859-1 Westeuropa',
			"latin2" => 'ISO 8859-2 Mitteleuropa',
			"latin5" => 'ISO 8859-9 T&uuml;rkisch',
			"latin7" => 'ISO 8859-13 Baltikum'
		);
		$db = new DBCEx();
		$db->Query('SHOW CHARSET');
		while ($r = $db->Get(MYSQLI_ASSOC)) {
			$mysql_charsets[$r['Charset']] = $r['Description'];
		}
		unset ($db);
	
		if (!$raw) {
			$select = array_intersect_key($charsets,$mysql_charsets);

			$form = new Form();
			return $form->select(array(
				'values' => array_keys($select),
				'aliases' => array_values($select),
				'optionsOnly' => true	
			));

		} else {
			return $mysql_charsets;
		}
	}
	
	/**
	 * OUTDATED: function makeNameWebsave
	 * Wandelt Sonderzeichen in Namen um, so dass sie als Dateinamen verwendet werden k�nnen
	 * 
	 * @param string $name Die zu entserialisierende Variable
	 * @return string Der umgewandelte Name
	 * 
	 */
	function makeNameWebSave($name) {
		return $name;
	}	
?>