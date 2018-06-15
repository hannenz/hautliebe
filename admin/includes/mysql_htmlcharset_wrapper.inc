<?php
	function wrapCharset($mysql_charset) {
		$html_charsets = array (
			'utf8' => 'UTF-8',
			'latin1' => 'ISO 8859-1',
			'latin2' => 'ISO 8859-2',
			'latin5' => 'ISO 8859-9',
			'latin7' => 'ISO 8859-13'
		 );

		if ($html_charsets[$mysql_charset]) {
			define ('CMT_CHARSET', $html_charsets[$mysql_charset]);
		} else {
			define ('CMT_CHARSET', CHARSET);
		}
	}
?>