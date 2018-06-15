<?php
	/*
	function getAvailableCharsets($raw=false) {
		$charsets = array ( "latin1" => 'ISO 8859-1 Westeuropa (Standard)',
							"latin2" => 'ISO 8859-2 Mitteleuropa',
							"latin5" => 'ISO 8859-9 Türkisch',
							"latin7" => 'ISO 8859-13 Baltikum',
							"utf8" => 'UTF-8 Unicode'
							);
		$db = new DBCEx();
		$db->Query('SHOW CHARSET');
		while ($r = $db->Get(MYSQLI_ASSOC)) {
			$mysql_charsets[$r['Charset']] = $r['Description'];
		}
		unset ($db);
		
		if (!$raw) {
			foreach ($charsets as $c=>$d) {
				if ($mysql_charsets[$c]) {
					$select[] = '<option value="'.$c.'">'.$d.'</option>';
				}
			}
			return implode("\n", $select);
		} else {
			return $mysql_charsets;
		}
	}
	*/
?>