<?php
/**
 * globale PHP-Funktionen
 * 
 * Funktionsbibliothek, die Funktionen f�r die Datei index.php zur Verf�gung stellt
 * 
 * Funktionen, die bei der Ausgabe der Website-Seiten verwendet werden sollen, 
 * m�ssen in diese Datei geschrieben werden.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2009-02-10
 */


/**
 * function: mysql_quote
 * 
 * Entwertet "'"-Zeichen in Strings, die in die Datenbank geschrieben werden sollen.
 * 
 * @param string $value String der gesch�tzt werden soll.
 * @return string Gesch�tzter String.
 */

	function mysql_quote($value) {
   		// stripslashes, falls n�tig
   		if (get_magic_quotes_gpc()) {
       		$value = stripslashes($value);
   		}

   		// quotieren, falls kein integer
   		if (!is_int($value)) {
       		$value = mysql_real_escape_string($value);
   		}
		
   		return $value;
	}


	
/**
 * function ob_start_gzipped()
 * 
 * Gzip-Kompression starten sofern Browser das unterst�tzt
 
 * @param void
 * @return void
 * 
 */

	function ob_start_gzipped() {
		$encode = getenv('HTTP_ACCEPT_ENCODING');
		if (stristr($encode, 'gzip')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}
	}
	
/**
 * function safeSerialize
 * 
 * Serialisiert Variablen (Strings und Arrays) damit diese per MySQL-Query
 * in die Datenbank geschrieben werden k�nnen.
 * 
 * @param mixed $var Die zu serialisierende Variable
 * @return mixed Die serialisierte Variable
 * 
 */
	
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
		
/**
 * function safeUnserialize
 * 
 * Entserialisiert Variablen (Strings und Arrays) die zuvor mit 
 * safeSerialize bearbeitet wurden. Es folgen Hilfsfunktionen.
 * 
 * @param mixed $var Die zu entserialisierende Variable
 * @return mixed Die entserialisierte Variable
 * 
 */
	
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

/**
 * function makeNameWebsave
 * Wandelt Sonderzeichen in Namen um, so dass sie als Dateinamen verwendet werden k�nnen
 * 
 * @param string $name Die zu entserialisierende Variable
 * @return string Der umgewandelte Name
 * 
 */
	function makeNameWebSave($name) {
		$search = array(' ', 'ß', 'ä', 'ü', 'ö', 'Ä', 'Ö', 'Ü', '&', '/', "'", ',', '+', '?', '!', '"', 'á', 'à', 'é', 'è', 'ç', 'ô','Á', 'À', 'É', 'È', 'Ç', ':', '%');
		$replace = array('-', 'ss', 'ae', 'ue', 'oe', 'Ae', 'Oe', 'Ue', '-', '-', '', '-', '-', '', '', '', 'a', 'a', 'e', 'e', 'c', 'o', 'A', 'A', 'E', 'E', 'C', '', '');
		return str_replace ($search, $replace, $name);
	}
?>