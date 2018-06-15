<?php
/************************************************
*
* Tabellenfunktion: Tabellenauswahlfeld
*
************************************************/

$head_form[0] .= "Tabelle:&nbsp;";
$head_form[0] .= $form->FormSelect("cmt_dbtable", $all_dbtables, "", $cmt_dbtable, 1)."&nbsp;&nbsp;";
$head_form[0] .= $form->FormSubmit("neu anzeigen");
$replace .= $tab->TableMakeRow($head_form, 3, $head_addhtml, "service");
?>