<?php
/************************************************
*
* Tabellenfunktion: Suchreihenfolge und Sortierung
*
************************************************/

// Felder

	$sort_select = $fields[name];
	$sort_aliases = $sort_select;
	array_unshift($sort_aliases, "Kein Feld");
	array_unshift($sort_select, "noselection");

//	print_r ($sort_select);
//	echo "<br>";
//	print_r ($sort_aliases);
				$head_form[0] = "1. Sortierung<br>";
				$head_form[0] .= $form->FormSelect("sort_by1", $sort_select, $sort_aliases, $sort_field_array[0], 1);
				$head_form[0] .= "<br>2. Sortierung:<br>";
				$head_form[0] .= $form->FormSelect("sort_by2", $sort_select, $sort_aliases, $sort_field_array[1], 1)."";

				// Reihenfolge
				$head_form[1] = "Reihenfolge 1. Kriterium"; //.$form->FormRadio ("sort_dir1", "no", $sort_dir_check[$sort_dir[0]][0])."&nbsp;keine";
				$head_form[1] .= "<br>".$form->FormRadio("sort_dir1", "asc", $sort_dir_check[$sort_dir[0]][0])."&nbsp;aufsteigend";
				$head_form[1] .= "<br>".$form->FormRadio("sort_dir1", "desc", $sort_dir_check[$sort_dir[0]][1])."&nbsp;absteigend";
				$head_form[1] .= "<p>Reihenfolge 2. Kriterium"; //.$form->FormRadio ("sort_dir2", "no", $sort_dir_check[$sort_dir[1]][0])."&nbsp;keine";
				$head_form[1] .= "<br>".$form->FormRadio("sort_dir2", "asc", $sort_dir_check[$sort_dir[1]][0])."&nbsp;aufsteigend";
				$head_form[1] .= "<br>".$form->FormRadio("sort_dir2", "desc", $sort_dir_check[$sort_dir[1]][1])."&nbsp;absteigend";
?>