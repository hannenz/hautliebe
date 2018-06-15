<?php
/*******************************************************
 * 
 * Content-o-mat - Include: Sortierrichtung
 * 
 * Letzte Änderung: 14.03.2005
 * 
 *******************************************************/
 
$s = $app_vars['sort_fields'];

$sort_dir_html = $tab->TableStart();
$cell = 0;
$sdr_add[0] = "style=\"padding-right: 12px; padding-bottom: 4px;\"";
$sdr_add[1] = $sdr_add[0];
$sort_checked["asc"] = array(1,0);
$sort_checked["desc"] = array(0,1); 

	
for ($i = 0; $i < $s; $i++) {
	$cell = $i%2;
	$n = $i+1;
	if (!$sort_dir[$n]) {
		$sort_dir[$n] = "asc";
	}

	$sdr_tab[$cell] = "<font class=\"service_text\">$n. Kriterium</font>";
	$sdr_tab[$cell] .= "<br>".$form->FormRadio("sort_dir[$n]", "asc", $sort_checked[$sort_dir[$n]][0])."&nbsp;aufsteigend";
	$sdr_tab[$cell] .= "<br>".$form->FormRadio("sort_dir[$n]", "desc", $sort_checked[$sort_dir[$n]][1])."&nbsp;absteigend";
	
	if ($cell == 1) {
		$sort_dir_html .= $tab->TableMakeRow($sdr_tab, 0, $sdr_add);
		unset ($sdr_tab);
		unset ($sdr_add);
	}
}

// Letze Zelle bei ungerader Sortierfelderanzahl
if ($sdr_tab) {
	$sort_dir_html .= $tab->TableMakeRow($sdr_tab, 0, $sdr_add);
}
$sort_dir_html .= $tab->TableEnd();
unset ($sdr_tab);
unset ($sdr_add);

/* Erzeugte Variable : $sort_dir_html */ 
?>