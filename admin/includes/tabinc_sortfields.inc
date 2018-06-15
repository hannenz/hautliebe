<?php
/*******************************************************
 * 
 * Content-o-mat - Include: Sortierfelder
 * 
 * Letzte Änderung: 10.03.2005
 * 
 *******************************************************/
 
$s = $app_vars['sort_fields'];

$sort_fields_html = $tab->TableStart();
$sf_add[0] = "style=\"padding-right: 12px; padding-bottom: 4px;\"";

if ($app_vars['sort_aliases']) {
	$sortfield_values = $cmt_fieldnames;
	$sortfield_aliases = $cmt_fieldaliases;
} else {
	$sortfield_values = $cmt_fieldnames;
	$sortfield_aliases = $cmt_fieldnames;
}

array_unshift ($sortfield_values, "");
array_unshift ($sortfield_aliases, "Kein Feld");

for ($i = 0; $i < $s; $i++) {
	$n = $i+1;
	$sf_tab[0] = "<font class=\"service_text\">$n. Sortierfeld</font><br>";
	$sf_tab[0] .= $form->FormSelect("sort_by[$n]", $sortfield_values, $sortfield_aliases, $sort_by[$n]);

	$sort_fields_html .= $tab->TableMakeRow($sf_tab, 0, $sf_add);
	
}
$sort_fields_html .= $tab->TableEnd();
unset ($sf_tab);
unset ($sf_add);

/* Erzeugte Variable : $sort_fields_html */ 
?>