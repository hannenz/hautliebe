	<div id="cmtRelationContainer_{VAR:relationNr}" class="cmtFieldTypeRelation fieldTypeRelationOuterContainer">
		<div class="cmtEditEntryRow cmtEditEntryRow{VAR:rowNr}">
			<div class="editEntryRowHead"><span class="cmtContentRelationNr">{VAR:relationNr}</span>. Tabelle f&uuml;r Auswahlliste:</div>
			<div class="editEntryRowField">
				<select id="cmt_option_relation_from_table-{VAR:relationNr}-name" name="cmt_option_relation_from_table[{VAR:relationNr}][name]" size="1" size="1" class="cmtFieldHandlerUpdateSelect" data-update-field-name="cmt_option_relation_from_table[{VAR:relationNr}][value_field]" data-update-target-id="cmtOptionRelationFromTable{VAR:relationNr}ValueContainer">
						<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
					{LOOP TABLE(all)}
						<option value="{FIELD:cmt_tablename}" {IF ("{VAR:relationTableName}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;{FIELD:cmt_showname}{ENDIF}</option>
					{ENDLOOP TABLE}
				</select>
			</div>
			<div class="editEntryRowHead">Wert-Feld</div>
			<div id="cmtOptionRelationFromTable{VAR:relationNr}ValueContainer" class="editEntryRowField">
				<input id="cmt_option_relation_from_table-{VAR:relationNr}-value_field"  type="text" value="{VAR:relationValueField}" name="cmt_option_relation_from_table[{VAR:relationNr}][value_field]" />
			</div>
			<div class="editEntryRowHead">Alias-Feld</div>
			<div class="editEntryRowField">
				<input type="text" value="{VAR:relationAliasField}" name="cmt_option_relation_from_table[{VAR:relationNr}][alias_field]" />
			</div>
			<div class="editEntryRowHead">Zus&auml;tzliche SQL-Anweisung</div>
			<div class="editEntryRowField">
				<input type="text" value="{VAR:relationAddSQL}" name="cmt_option_relation_from_table[{VAR:relationNr}][add_sql]" />
			</div>
			<div class="fieldFunctionsContainer">
				<a class="cmtButton cmtButtonDelete cmtRemoveRelationButton" href="Javascript:void(0)" data-relation-nr="{VAR:relationNr}">diese Relation entfernen</a>
			</div>
		</div>
	</div>
