<?php
/*******************************************************
 * 
 * Content-o-mat - Include: Suchfelder
 * 
 * Letzte Änderung: 10.03.2005
 * 
 *******************************************************/
 
$s = $app_vars['search_fields'];

$criteria_select = array ("=" => "=", "!=" => "!=", "<" => "<", ">" => ">", "<=" => "<=", ">=" => ">=", "like" => "like", "not like" => "not like");

$searchlink_checked["and"] = array(1,0);
$searchlink_checked["or"] = array(0,1);

$searchfields_html = $tab->TableStart();
$sf_add[0] = "style=\"padding-right: 12px; padding-bottom: 4px;\" nowrap";

if ($app_vars['search_aliases'] && $cmt_fieldaliases) {
	$searchfield_values = $cmt_fieldnames;
	$searchfield_aliases = $cmt_fieldaliases;
} else {
	$searchfield_values = $cmt_fieldnames;
	$searchfield_aliases = $cmt_fieldnames;
}

array_unshift ($searchfield_values, "");
array_unshift ($searchfield_aliases, "Kein Feld");

// Überschrift
$sf_tab[0] = "<font class=\"service_text\">Suchriterien:</font><br>";
$searchfields_html .= $tab->TableMakeRow($sf_tab);

for ($i = 0; $i < $s; $i++) {
	$n = $i+1;
	if (!$search_link[$n]) {
		$search_link[$n] = "and";
	}
	$sf_tab[0] = $form->FormSelect("search_field[$n]", $searchfield_values, $searchfield_aliases, $search_field[$n])."&nbsp;";
	$sf_tab[0] .= $form->FormSelect("search_criteria[$n]", $criteria_select, "", $search_criteria[$n], 1)."&nbsp;";
	$sf_tab[0] .= $form->FormInput("search_value[$n]", $search_value[$n], 18);
	
	if ($s > 1 && $i<($s-1)) {
		$sf_tab[0] .= "<br><font class=\"service_text\">Verkn&uuml;pfung:</font> ".$form->FormRadio("search_link[$n]", "and", $searchlink_checked[$search_link[$n]][0])."UND&nbsp;&nbsp;";
		$sf_tab[0] .= $form->FormRadio("search_link[$n]", "or", $searchlink_checked[$search_link[$n]][1])."ODER";
	}

	$searchfields_html .= $tab->TableMakeRow($sf_tab, 0, $sf_add);
	
}
$searchfields_html .= $tab->TableEnd();
unset ($sf_tab);
unset ($sf_add);
unset ($show_searchfields);

/* Erzeugte Variable : $searchfields_html */ 
?>