<script type="text/javascript" src="{CMT_TEMPLATE}app_tablebrowser/javascript/cmt_fieldbrowser_functions.js"></script>
<link type="text/css" href="{CMT_TEMPLATE}app_tablebrowser/css/cmt_tablebrowser_style.css" rel="Stylesheet" />

<form id="editForm" method="post" action="{SELFURL}">
	<div class="appHeadlineContainer">
		<div class="appIcon"><img src="{CMT_TEMPLATE}/app_tablebrowser/img/icon.png" alt="" /></div>
		<h1>Feldmanager: {IF ("{VAR:action}" == "new")}<span class="tableHeadlineAddText">Feld hinzuf&uuml;gen</span>{ELSE}
			{IF ("{VAR:action}" == "edit")}<span class="tableHeadlineAddText">Feld bearbeiten: {VAR:cmt_fieldname} (Tabelle: {VAR:cmt_tablename})</span>{ELSE}
			<span class="tableHeadlineAddText">Feld duplizieren</span>{ENDIF}{ENDIF}</h1>
	</div>
	<!-- Meldung -->
	{IF ({ISSET:userMessage:VAR})}
		{IF ("{VAR:userMessageType}" == "error")}
		<div class="cmtMessage cmtMessageError">Wegen eines Fehlers konnte das Feld nicht gespeichert oder angelegt werden: {VAR:userMessage}</div>
		{ENDIF}
	{ENDIF}
	<!-- Service -->
	<div class="serviceContainerButtonRow">
		<a href="{SELFURL}" class="cmtButton cmtButtonBack"></a>
		<button class="cmtButton cmtButtonSave" type="submit">speichern</button>
	</div>
	<div class="serviceContainer">
		<div class="serviceContainerInner">
			<div class="serviceElementContainer">
				<div id="fieldName">
					<label for="cmtFieldname" class="serviceText">Feldname (Datenbank):</label>
					<input type="text" id="cmtFieldname" name="cmt_fieldname" value="{VAR:cmt_fieldname}" />
				</div>
				<div id="fieldAliasName">
					<label for="cmtFieldalias" class="serviceText">Aliasname (System):&nbsp;</label>
					<input type="text" id="cmtFieldalias" name="cmt_fieldalias" value="{VAR:cmt_fieldalias}" />
				</div>
			</div>
			<div class="serviceElementContainer clearfix">
				<label for="cmtSelectFieldtype" class="serviceText">Feld-Typ:&nbsp;</label>
				<select name="cmt_fieldtype" id="cmtSelectFieldtype">
					<option value="string"{IF ("{VAR:cmt_fieldtype}" == "string")} selected="selected"{ENDIF}>String</option>
					<option value="text"{IF ("{VAR:cmt_fieldtype}" == "text")} selected="selected"{ENDIF}>Text</option>
					<option value="float"{IF ("{VAR:cmt_fieldtype}" == "float")} selected="selected"{ENDIF}>Zahl</option>
					<option value="integer"{IF ("{VAR:cmt_fieldtype}" == "integer")} selected="selected"{ENDIF}>Ganzzahl / Integer</option>
					<option value="flag"{IF ("{VAR:cmt_fieldtype}" == "flag")} selected="selected"{ENDIF}>Flag</option>
					<option value="datetime"{IF ("{VAR:cmt_fieldtype}" == "datetime")} selected="selected"{ENDIF}>Datum und Uhrzeit</option>
					<option value="date"{IF ("{VAR:cmt_fieldtype}" == "date")} selected="selected"{ENDIF}>Datum</option>
					<option value="time"{IF ("{VAR:cmt_fieldtype}" == "time")} selected="selected"{ENDIF}>Uhrzeit</option>
					<option value="relation"{IF ("{VAR:cmt_fieldtype}" == "relation")} selected="selected"{ENDIF}>Relation</option>
					<option value="select"{IF ("{VAR:cmt_fieldtype}" == "select")} selected="selected"{ENDIF}>Auswahlliste</option>
					<option value="select_recursive"{IF ("{VAR:cmt_fieldtype}" == "select_recursive")} selected="selected"{ENDIF}>Auswahlliste rekursiv</option>
					<option value="link"{IF ("{VAR:cmt_fieldtype}" == "link")} selected="selected"{ENDIF}>Link</option>
					<option value="html"{IF ("{VAR:cmt_fieldtype}" == "html")} selected="selected"{ENDIF}>HTML</option>
					<option value="position"{IF ("{VAR:cmt_fieldtype}" == "position")} selected="selected"{ENDIF}>Position</option>
					<option value="upload"{IF ("{VAR:cmt_fieldtype}" == "upload")} selected="selected"{ENDIF}>Datei / Upload-Feld</option>
					<option value="system_var"{IF ("{VAR:cmt_fieldtype}" == "system_var")} selected="selected"{ENDIF}>Content-o-mat Variable</option>
				</select>
			</div>
		</div>
	</div>
	<div id="cmtContentAreasContainer">
		<!-- Feldeigenschaften: String -->
		<div id="string_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: String</div>
				<div class="cmtTabs">
					<ul id="stringTabsHead">
						<li><a href="#stringTab1"><span>allgemeine Optionen</span></a></li>
						<li><a href="#stringTab2"><span>Autosuggest aus Liste</span></a></li>
						<li><a href="#stringTab3"><span>Autosuggest aus Tabelle</span></a></li>
						<li><a href="#stringTab4"><span>Autosuggest Optionen</span></a></li>
					</ul>
					<div id="stringTab1">
						<div class="cmtEditEntryRow cmtEditEntryRow0">
							<div class="editEntryRowHead">Default-Wert</div>
							<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_string}" name="cmt_default_string" /></div>
							<div class="editEntryRowDescription">Standard-Wert bei Neueintrag</div>
						</div>
					</div>
					<div id="stringTab2">
						<div class="cmtEditEntryRow cmtEditEntryRow0">
							<div class="editEntryRowHead">Auswahlliste: Werte (&quot;value&quot;)</div>
							<div class="editEntryRowField">
								<textarea name="cmt_option_string_from_list">{VAR:cmt_option_string_from_list}</textarea>
							</div>
							<div class="editEntryRowDescription">Worte oder Daten der Vorschlagsliste. Bitte mit Zeilenschaltung trennen.</div>
						</div>
						<!--
						<div class="cmtEditEntryRow cmtEditEntryRow1">
							<div class="editEntryRowField">
								<input id="stringFromListMultiple" type="checkbox" name="cmt_option_string_from_list_multiple" {IF ({ISSET:cmt_option_string_from_list_multiple})}checked="checked"{ENDIF} />
								<label class="cmtSpecialLabel" for="stringFromListMultiple">Mehrfachauswahl zulassen</label>
							</div>
							<div class="editEntryRowDescription">Es können mehrere Elemente aus der Vorschlagsliste ausgewählt werden.</div>
						</div>
						<div class="cmtEditEntryRow cmtEditEntryRow1">	
							<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
							<div class="editEntryRowField">
								<input type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_string_from_list_multiple_separator}{ENDIF}" name="cmt_option_string_from_list_multiple_separator" class="formFieldTextShort" />
							</div>
							<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
							hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
							<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
						</div>
						-->
					</div>
					<div id="stringTab3">
						<div class="cmtEditEntryRow cmtEditEntryRow0">
							<div class="editEntryRowHead">Daten aus dieser Tabelle beziehen</div>
							<div class="editEntryRowField">
								<select id="cmt_option_string_from_table" name="cmt_option_string_from_table" size="1" class="cmtFieldHandlerUpdateSelect" data-update-field-name="cmt_option_string_from_table_value_field" data-update-target-id="cmt_option_string_from_table_value_field_container">
										<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
									{LOOP TABLE(all)}
										<option value="{FIELD:cmt_tablename}" {IF ("{VAR:cmt_option_string_from_table}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;({FIELD:cmt_showname}){ENDIF}</option>
									{ENDLOOP TABLE}
								</select>
							</div>
							<div>
								<div class="editEntryRowHead">Wert-Feld</div>
								<div class="editEntryRowField" id="cmt_option_string_from_table_value_field_container">
									<input type="text" value="{VAR:cmt_option_string_from_table_value_field}" name="cmt_option_string_from_table_value_field" id="cmt_option_string_from_table_value_field" />
								</div>
							</div>
							<div>
								<div class="editEntryRowHead">Zus&auml;tzliche SQL-Anweisung</div>
								<div class="editEntryRowField">
									<input type="text" value="{VAR:cmt_option_string_from_table_add_sql}" name="cmt_option_string_from_table_add_sql" />
								</div>
								<div class="editEntryRowDescription">
									Mit einer zusätzlichen SQL-Anweisung, die nach dem WHERE-Teil angehängt wird, kann die Auswahl der Daten eingeschränkt oder beeinflusst werden.
								</div>
							</div>
						</div>
						<!--
						<div class="cmtEditEntryRow cmtEditEntryRow1">
							<div class="editEntryRowField">
								<input id="stringFromTableMultiple" type="checkbox" name="cmt_option_string_from_table_multiple" {IF ({ISSET:cmt_option_string_from_table_multiple})}checked="checked"{ENDIF} />
								<label class="cmtSpecialLabel" for="stringFromTableMultiple">Mehrfachauswahl zulassen</label>
							</div>
							<div class="editEntryRowDescription">Es können mehrere Elemente aus der Vorschlagsliste ausgewählt werden.</div>
						</div>
						<div class="cmtEditEntryRow cmtEditEntryRow1">	
							<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
							<div class="editEntryRowField">
								<input type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_string_from_table_multiple_separator}{ENDIF}" name="cmt_option_string_from_table_multiple_separator" class="formFieldTextShort" />
							</div>
							<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
							hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
							<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
						</div>
						-->
					</div>
					<div id="stringTab4">
						<div class="cmtEditEntryRow cmtEditEntryRow0">
							<div class="editEntryRowField">
								<input id="stringFromListMultiple" type="checkbox" name="cmt_option_string_multiple" {IF ({ISSET:cmt_option_string_multiple})}checked="checked"{ENDIF} />
								<label class="cmtSpecialLabel" for="stringFromListMultiple">Mehrfachauswahl zulassen</label>
							</div>
							<div class="editEntryRowDescription">Es können mehrere Elemente aus der Vorschlagsliste ausgewählt werden.</div>
						</div>
						<div class="cmtEditEntryRow cmtEditEntryRow0">	
							<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
							<div class="editEntryRowField">
								<input type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_string_multiple_separator}{ENDIF}" name="cmt_option_string_multiple_separator" class="formFieldTextShort" />
							</div>
							<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
							hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
							<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
						</div>
					</div>
				</div>
		</div>
	
		<!-- Feldeigenschaften: Text -->
		<div id="text_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Text</div>
			<div class="cmtTabs">
				<ul id="textTabsHead">
					<li><a href="#textTab1"><span>allgemeine Optionen</span></a></li>
					<li><a href="#textTab2"><span>Texteditor</span></a></li>
				</ul>
				<div id="textTab1">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Default-Wert</div>
						<div class="editEntryRowField"><textarea name="cmt_default_text" cols="120" rows="5">{VAR:cmt_default_text}</textarea></div>
						<div class="editEntryRowDescription">Standard-Wert bei Neueintrag</div>
					</div>
				</div>
				<div id="textTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="textShowEditor" type="checkbox" name="cmt_option_text_show_editor" {IF ({ISSET:cmt_option_text_show_editor})}checked="checked"{ENDIF} />
							<label for="textShowEditor" class="cmtSpecialLabel">Texteditor anzeigen</label>
						</div>
						<div class="editEntryRowDescription">Anstelle eines normalen Texteingabefeldes wird ein Texteditor angezeigt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowField">
							<select name="cmt_option_text_editor_language" id="cmt_option_text_editor_language">
								<option value="" {IF ("{VAR:cmt_option_text_editor_language}" == "")}selected="selected"{ENDIF}>-- keine Syntax-Hervorhebung --</option>
								<option value="html" {IF ("{VAR:cmt_option_text_editor_language}" == "html")}selected="selected"{ENDIF}>HTML</option>
								<option value="css" {IF ("{VAR:cmt_option_text_editor_language}" == "css")}selected="selected"{ENDIF}>CSS</option>
								<option value="javascript" {IF ("{VAR:cmt_option_text_editor_language}" == "javascript")}selected="selected"{ENDIF}>Javascript</option>
								<option value="php" {IF ("{VAR:cmt_option_text_editor_language}" == "php")}selected="selected"{ENDIF}>PHP</option>
								<option value="javascript" {IF ("{VAR:cmt_option_text_editor_language}" == "sql")}selected="selected"{ENDIF}>SQL</option>
								<option value="javascript" {IF ("{VAR:cmt_option_text_editor_language}" == "json")}selected="selected"{ENDIF}>JSON</option>
								<option value="xml" {IF ("{VAR:cmt_option_text_editor_language}" == "xml")}selected="selected"{ENDIF}>XML</option>
							</select>
						</div>
						<div class="editEntryRowDescription">Für die hier ausgew&aunl;hlte Sprache wird eine Syntax-Hervorhebung im Editor aktiviert.</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Float -->
		<div id="float_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Zahl (Float)</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_float}" name="cmt_default_float" /></div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Option: Runden auf x Dezimalstellen</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_float_round}" name="cmt_option_float_round" class="formFieldTextShort" /></div>
				<div class="editEntryRowDescription">Der in die Datenbanktabelle eingegebene Wert wird vor dem Speichern auf die hier angegebene Anzahl von Dezimalstellen gerundet.</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Ganzzahl/ Integer -->
		<div id="integer_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Ganzzahl (Integer)</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_integer}" name="cmt_default_integer" /></div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Flag -->
		<div id="flag_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Flag</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowField">
					<input id="defaultFlag" type="checkbox" name="cmt_default_flag" {IF ({ISSET:cmt_default_flag})}checked="checked"{ENDIF} />
					<label for="defaultFlag" class="cmtSpecialLabel">Default-Wert</label>
				</div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag. </div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Option: Wert, falls Flag ausgew&auml;hlt</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_flag_value}" name="cmt_option_flag_value" /></div>
				<div class="editEntryRowDescription">Dieser Wert wird anstelle eines "true" oder "1" &uuml;bergeben, falls die Checkbox angeklickt wurde.</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Datetime -->
		<div id="datetime_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Datum und Uhrzeit</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField">
					<input type="text" value="{VAR:cmt_default_datetime_day}" name="cmt_default_datetime_day" class="formFieldTextShort cmtDateDay" />&nbsp;-
					<input type="text" value="{VAR:cmt_default_datetime_month}" name="cmt_default_datetime_month" class="cmtDateMonth" />&nbsp;-
					<input type="text" value="{VAR:cmt_default_datetime_year}" name="cmt_default_datetime_year" class="cmtDateYear" />&nbsp;&nbsp;&nbsp;
					<input type="text" value="{VAR:cmt_default_datetime_hour}" name="cmt_default_datetime_hour" class="cmtDateHour" />&nbsp;:
					<input type="text" value="{VAR:cmt_default_datetime_minute}" name="cmt_default_datetime_minute" class="cmtDateMinute" />&nbsp;:
					<input type="text" value="{VAR:cmt_default_datetime_second}" name="cmt_default_datetime_second" class="cmtDateSecond" />
					<a href="Javascript:void(0);" class="cmtButton cmtButtonShowCalendar cmtShowCalendar" data-dateformat="yyyy-mm-dd" title="Datum ausw&auml;hlen"></a>
				</div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag. Eingabeformat Tag.Monat.Jahr Stunde:Minute:Sekunde </div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input type="checkbox" id="datetimeCurrent" name="cmt_option_datetime_current" {IF ({ISSET:cmt_option_datetime_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="datetimeCurrent">Als Standard-Wert aktuelles Datum und Uhrzeit einf&uuml;gen.</label>
				</div>
				<div class="editEntryRowDescription">Bei einem Neueintrag werden die Felder mit aktuellem Datum und Uhrzeit vorausgef&uuml;llt.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowField">
					<input id="datetimeAlwaysCurrent" type="checkbox" name="cmt_option_datetime_always_current" {IF ({ISSET:cmt_option_datetime_always_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="datetimeAlwaysCurrent">Bei jeder Bearbeitung aktuelles Datum und Uhrzeit speichern.</label>
				</div>
				<div class="editEntryRowDescription">Aktuelles Datum und Uhrzeit werden immer und unabh&auml;ngig von der Eingabe in diesem Feld gespeichert. Die Eingabe wird
				&uuml;berschrieben (Option eignet sich f&uuml;r versteckte oder durch den User nicht bearbeitbare Felder).</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="datetimeCalendar" type="checkbox" name="cmt_option_datetime_show_calendar" {IF ({ISSET:cmt_option_datetime_show_calendar} || "{VAR:action}" == "new")}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="datetimeCalendar">Kalender anzeigen</label>
				</div>
				<div class="editEntryRowDescription">Ein zus&auml;tzlich ausw&auml;hlbarer Kalender erm&ouml;glicht die Datumsauswahl per Mausklick.</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Date -->
		<div id="date_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Datum</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField">
					<input type="text" value="{VAR:cmt_default_date_day}" name="cmt_default_date_day" class="cmtDateYear" />&nbsp;-
					<input type="text" value="{VAR:cmt_default_date_month}" name="cmt_default_date_month" class="cmtDateMonth" />&nbsp;-
					<input type="text" value="{VAR:cmt_default_date_year}" name="cmt_default_date_year" class="cmtDateDay" />
					<a href="Javascript:void(0);" class="cmtButton cmtButtonShowCalendar cmtShowCalendar" data-dateformat="yyyy-mm-dd" title="Datum ausw&auml;hlen"></a>
				</div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag. Eingabeformat Tag.Monat.Jahr</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="dateCurrent" type="checkbox" name="cmt_option_date_current" {IF ({ISSET:cmt_option_date_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="dateCurrent">Als Standard-Wert aktuelles Datum einf&uuml;gen</label>
				</div>
				<div class="editEntryRowDescription">Bei einem Neueintrag werden die Felder mit dem aktuellem Datum vorausgef&uuml;llt.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowField">
					<input id="dateAlwaysCurrent" type="checkbox" name="cmt_option_date_always_current" {IF ({ISSET:cmt_option_date_always_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="dateAlwaysCurrent">Bei jeder Bearbeitung aktuelles Datum speichern</label>
				</div>
				<div class="editEntryRowDescription">Das aktuelle Datum wird immer und unabh&auml;ngig von der Eingabe in diesem Feld gespeichert. Die Eingabe wird
				&uuml;berschrieben (Option eignet sich f&uuml;r versteckte oder durch den User nicht bearbeitbare Felder).</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="dateCalendar" type="checkbox" name="cmt_option_date_show_calendar" {IF ({ISSET:cmt_option_date_show_calendar} || "{VAR:action}" == "new")}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="dateCalendar">Kalender anzeigen</label>
				</div>
				<div class="editEntryRowDescription">Ein zus&auml;tzlich ausw&auml;hlbarer Kalender erm&ouml;glicht die Datumsauswahl per Mausklick.</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Time -->
		<div id="time_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Uhrzeit</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField">
					<input type="text" value="{VAR:cmt_default_time_hour}" name="cmt_default_time_hour" class="formFieldTextShort" />&nbsp;:
					<input type="text" value="{VAR:cmt_default_time_minute}" name="cmt_default_time_minute" class="formFieldTextShort" />&nbsp;:
					<input type="text" value="{VAR:cmt_default_time_second}" name="cmt_default_time_second" class="formFieldTextShort" />
				</div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag. Eingabeformat Stunde:Minute:Sekunde </div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="timeCurrent" type="checkbox" name="cmt_option_time_current" {IF ({ISSET:cmt_option_time_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="timeCurrent">Als Standard-Wert aktuelle Zeit einf&uuml;gen.</label>
				</div>
				<div class="editEntryRowDescription">Bei einem Neueintrag werden die Felder mit der aktuellen Uhrzeit vorausgef&uuml;llt.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowField">
					<input id="timeAlwaysCurrent" type="checkbox" name="cmt_option_time_always_current" {IF ({ISSET:cmt_option_time_allways_current})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="timeAlwaysCurrent">Bei jeder Bearbeitung aktuelle Zeit speichern</label>
				</div>
				<div class="editEntryRowDescription">Die aktuelle Zeit wird immer und unabh&auml;ngig von der Eingabe in diesem Feld gespeichert. Die Eingabe wird
				&uuml;berschrieben (Option eignet sich f&uuml;r versteckte oder durch den User nicht bearbeitbare Felder).</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Relation -->
		<div id="relation_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Relation</div>
			<div class="cmtTabs">
				<ul id="relationTabsHead">
					<li><a href="#relationTab1"><span>Verkn&uuml;pfte Tabellen</span></a></li>
					<li><a href="#relationTab2"><span>Optionen: Mehrfachauswahl</span></a></li>
				</ul>
				<!-- Relation Tabelle 1 -->
		 		<div id="relationTab1">
		 			<div id="relationContainer">
						<!-- Relation Tabelle 1: Immer vorhanden! -->
						<div id="cmtRelationContainer_1" class="cmtFieldTypeRelation fieldTypeRelationOuterContainer">
							<div id="relationInnerContainer_1" class="cmtEditEntryRow cmtEditEntryRow0">
								<div class="editEntryRowHead"><span class="contentRelationNr">1</span>. Tabelle f&uuml;r Auswahlliste:</div>
								<div class="editEntryRowField">
									<select id="cmt_option_relation_from_table-1-name" name="cmt_option_relation_from_table[1][name]" size="1" class="cmtFieldHandlerUpdateSelect" data-update-field-name="cmt_option_relation_from_table[1][value_field]" data-update-target-id="cmtOptionRelationFromTable1ValueContainer">
											<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
										{LOOP TABLE(all)}
											<option value="{FIELD:cmt_tablename}" {IF ("{VAR:relationTableName}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;{FIELD:cmt_showname}{ENDIF}</option>
										{ENDLOOP TABLE}
									</select>
								</div>
								<div class="editEntryRowHead">Wert-Feld</div>
								<div class="editEntryRowField" id="cmtOptionRelationFromTable1ValueContainer">
									<input type="text" id="cmt_option_relation_from_table-1-value_field" value="{VAR:relationValueField}" name="cmt_option_relation_from_table[1][value_field]" />
								</div>
								<div class="editEntryRowHead">Alias-Feld</div>
								<div class="editEntryRowField">
									<input type="text" value="{VAR:relationAliasField}" name="cmt_option_relation_from_table[1][alias_field]" />
								</div>
								<div class="editEntryRowHead">Zus&auml;tzliche SQL-Anweisung</div>
								<div class="editEntryRowField">
									<input type="text" value="{VAR:relationAddSQL}" name="cmt_option_relation_from_table[1][add_sql]" />
								</div>
							</div>
						</div>
						<!--  Weitere, optionale Relationen -->
						{VAR:contentRelation}
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<a class="cmtButton cmtButtonAdd cmtAddRelationButton" href="Javascript:void(0);" data-relations-nr="1">weitere Relation hinzuf&uuml;gen</a>
					</div>
				</div>
				<div id="relationTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="relationMultiple" type="checkbox" name="cmt_option_relation_multiple" {IF ({ISSET:cmt_option_relation_multiple})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="relationMultiple">Mehrfachauswahl zulassen</label>	
						</div>
						<div class="editEntryRowDescription">Erzeugt ein Auwahlfeld, dass eine Mehrfachauswahl der Werte erlaubt..</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
						<div class="editEntryRowField">
							<input type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_relation_multiple_separator}{ENDIF}" name="cmt_option_relation_multiple_separator" class="formFieldTextShort" />
						</div>
						<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
						hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
						<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Select -->
		<div id="select_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Auswahlliste</div>
			<div class="cmtTabs">
				<ul id="selectTabsHead">
					<li><a href="#selectTab1"><span>allgemeine Optionen</span></a></li>
					<li><a href="#selectTab2"><span>einfache Auswahlliste</span></a></li>
					<li><a href="#selectTab3"><span>Auswahlliste aus Datenbanktabelle</span></a></li>
				</ul>
				<div id="selectTab1">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Default-Wert</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_select}" name="cmt_default_select" /></div>
						<div class="editEntryRowDescription">Standard-Wert bei Neueintrag.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Leerauswahl zulassen?</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_select_noselection}" name="cmt_option_select_noselection" /></div>
						<div class="editEntryRowDescription">Alias f&uuml;r eine Leerauswahl an erster Stelle der Auswahlliste (z.B. &quot;--- keine Auswahl ---&quot;). 
						Das Wert-Feld (&quot;value&quot;) bleibt dabei leer.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="selectMultiple" type="checkbox" name="cmt_option_select_multiple" {IF ({ISSET:cmt_option_select_multiple})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="selectMultiple">Mehrfachauswahl zulassen</label>
						</div>
						<div class="editEntryRowDescription">Erzeugt ein Auwahlfeld, dass eine Mehrfachauswahl der Werte erlaubt..</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">	
						<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
						<div class="editEntryRowField">
							<input style="font-family: monospace" type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_select_multiple_separator}{ENDIF}" name="cmt_option_select_multiple_separator" class="formFieldTextShort" />
						</div>
						<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
						hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
						<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
					</div>
				</div>
				<div id="selectTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="clearFloats">
							<div class="editEntryRowCol">
								<div class="editEntryRowHead">Auswahlliste: Werte (&quot;value&quot;)</div>
								<div class="editEntryRowField"><textarea name="cmt_option_select_values" cols="20" rows="8">{VAR:cmt_option_select_values}</textarea></div>
							</div>
							<div class="editEntryRowCol">
								<div class="editEntryRowHead">Auswahlliste: Aliase (optional)</div>
								<div class="editEntryRowField"><textarea name="cmt_option_select_aliases" cols="20" rows="8">{VAR:cmt_option_select_aliases}</textarea></div>
							</div>
						</div>
						<div class="editEntryRowDescription">Werte der Auswahlliste sofern die Auswahlliste nicht aus einer Tabelle erzeugt wird (s.u.). Aliase 
						(Bezeichnungen f&uuml;r die Werte) werden in der Auswahlliste dem genau nebenstehenden Wert zugeordnet angezeigt.</div>
					</div>
				</div>
				<div id="selectTab3">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Auswahlliste aus dieser Tabelle erstellen</div>
						<div class="editEntryRowField">
							<select id="cmt_option_select_from_table" name="cmt_option_select_from_table" size="1" class="cmtFieldHandlerUpdateSelect" data-update-field-name="cmt_option_select_from_table_value_field" data-update-target-id="cmt_option_select_from_table_value_field_container">
									<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
								{LOOP TABLE(all)}
									<option value="{FIELD:cmt_tablename}" {IF ("{VAR:cmt_option_select_from_table}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;({FIELD:cmt_showname}){ENDIF}</option>
								{ENDLOOP TABLE}
							</select>
						</div>
						<div>
							<div class="editEntryRowHead">Wert-Feld</div>
							<div class="editEntryRowField" id="cmt_option_select_from_table_value_field_container">
								<input type="text" value="{VAR:cmt_option_select_from_table_value_field}" name="cmt_option_select_from_table_value_field" id="cmt_option_select_from_table_value_field" />
							</div>
						</div>
						<div>
							<div class="editEntryRowHead">Alias-Feld</div>
							<div class="editEntryRowField">
								<input type="text" value="{VAR:cmt_option_select_from_table_alias_field}" name="cmt_option_select_from_table_alias_field" />
							</div>
						</div>
						<div>
							<div class="editEntryRowHead">Zus&auml;tzliche SQL-Anweisung</div>
							<div class="editEntryRowField">
								<input type="text" value="{VAR:cmt_option_select_from_table_add_sql}" name="cmt_option_select_from_table_add_sql" />
							</div>
						</div>
						<div class="editEntryRowDescription">
							<p>
								Eine Auswahlliste kann auch aus Daten eines Feldes (Wert-Feld) einer anderen Tabelle erzeugt werden. Die Angaben &quot;Alias-Feld&quot; und 
								&quot;SQL Anweisung&quot; sind optional.
							</p>
							<p>
								Das Alias-Feld wird geparst. Es sind alle Felder des Datensatzes vorhanden, der mittels der Query ausgew&auml;hlt wurden. Die Felder werden 
								mit Hilfe der Parser-Makros angezeigt:
							</p>
							<ul>
								<li><code>{<span>VAR</span>:myField1}</code> Zeigt den Inhalt des Feldes &quot;myField1&quot; an</li>
								<li><code>{<span>VAR:name</span>} (geb. am {<span>VAR:birthday:FORMATTED</span>})</code> gibt etwas wie "Horst Hrubesch (geb. am 20.08.1972) aus.</li>
								<li>Die Query im Feld &quot;SQL-Anweisung&quot; wird direkt an die Datenbankabfrage-Query angeh&auml;ngt, so dass mit z.B. <code>ORDER BY myField1</code> 
								auch Sortieranweisungen oder komplexere Queries m&ouml;glich sind.</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Select rekursiv -->
		<div id="select_recursive_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Auswahlliste rekursiv</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_select_recursive}" name="cmt_default_select_recursive" /></div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Leerauswahl zulassen?</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_select_recursive_noselection}" name="cmt_option_select_recursive_noselection" /></div>
				<div class="editEntryRowDescription">Alias f&uuml;r eine Leerauswahl an erster Stelle der Auswahlliste (z.B. &quot;--- keine Auswahl ---&quot;). 
				Das Wert-Feld (&quot;value&quot;) bleibt dabei leer.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Auswahlliste aus Tabelle erstellen</div>
				<div class="editEntryRowField">
					<select id="cmt_option_select_recursive_from_table" name="cmt_option_select_recursive_from_table"  class="cmtFieldHandlerUpdateSelect" size="1" data-update-field-name="cmt_option_select_recursive_parent,cmt_option_select_recursive_parent_value_field,cmt_option_select_recursive_parent_alias_field" data-update-target-id="cmt_option_select_recursive_parent_container,cmt_option_select_recursive_parent_value_field_container,cmt_option_select_recursive_parent_alias_field_container">
						<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
						{LOOP TABLE(all)}
							<option value="{FIELD:cmt_tablename}" {IF ("{VAR:cmt_option_select_recursive_from_table}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;({FIELD:cmt_showname}){ENDIF}</option>
						{ENDLOOP TABLE}
					</select>
					<!-- 									ajaxEmptySelection: '--- Kein Alias-Feld ausgew&auml;hlt ---' -->
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Eltern-Feldname (Parent-ID)</div>
				<div class="editEntryRowField" id="cmt_option_select_recursive_parent_container">
					<input type="text" value="{VAR:cmt_option_select_recursive_parent}" name="cmt_option_select_recursive_parent" id="cmt_option_select_recursive_parent"/>
				</div>
				<div class="editEntryRowDescription">Dieses Feld bezeichnet den &uuml;bergeordneten Eintrag (Eltern-Element) eines Datensatzes (Kind-Element) 
				in der ausgew&auml;hlten Tabelle. &Uuml;blicherweise handelt es sich hier um einen eindeutigen Bezeichner des Eltern-Elements, meist also 
				das Feld <code>id</code>. Dieses Feld bildet in der Auswahlliste den <code>value</code> Wert.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Eltern-ID Feld</div>
				<div class="editEntryRowField" id="cmt_option_select_recursive_parent_value_field_container">
					<input type="text" value="{VAR:cmt_option_select_recursive_parent_value_field}" name="cmt_option_select_recursive_parent_value_field" id="cmt_option_select_recursive_parent_value_field" />
				</div>
				<div class="editEntryRowDescription">In diesem Feld der oben angegebenen Zieltabelle wird die Eltern-ID eines Datensatzes gespeichert.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Optional: Alias-Feld</div>
				<div class="editEntryRowField" id="cmt_option_select_recursive_parent_alias_field_container">
					<input type="text" value="{VAR:cmt_option_select_recursive_parent_alias_field}" name="cmt_option_select_recursive_parent_alias_field" id="cmt_option_select_recursive_parent_alias_field"/>
				</div>
				<div class="editEntryRowDescription">Aus diesem Feld der oben angegebenen Zieltabelle wird in der Auswahlliste der Titel einer jeden Option erzeugt. Wird 
				hier nichts ausgew&auml;hlt, wird der <code>value</code> der Auswahlliste angezeigt.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Optional: Zus&auml;tzliche SQL-Anweisung</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_select_recursive_add_sql}" name="cmt_option_select_recursive_add_sql" /></div>
				<div class="editEntryRowDescription">Hier kann eine SQL-Anweisung (<code>WHERE / ORDER BY</code>) angeben werden, die der eigentlichen SQL-Abfrage angeh&auml;ngt wird.</div>
			</div>
			
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="selectRecursiveMultiple" type="checkbox" name="cmt_option_select_recursive_multiple" {IF ({ISSET:cmt_option_select_recursive_multiple})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="selectRecursiveMultiple">Mehrfachauswahl zulassen</label>
				</div>
				<div class="editEntryRowDescription">Erzeugt ein Auwahlfeld, dass eine Mehrfachauswahl der Werte erlaubt.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">	
				<div class="editEntryRowHead">Mehrfachauswahl Trennzeichen</div>
				<div class="editEntryRowField">
					<input style="font-family: monospace" type="text" value="{IF ("{VAR:action}" == "new")},{ELSE}{VAR:cmt_option_select_recursive_multiple_separator}{ENDIF}" name="cmt_option_select_recursive_multiple_separator" class="formFieldTextShort" />
				</div>
				<div class="editEntryRowDescription">Die ausgew&auml;hlten Werte werden in der Datenbank durch das hier angegebene Zeichen getrennt gespeichert. Wird 
				hier kein Zeichen explizit ausgew&auml;hlt, dann wird das Komma <code>,</code> als Trennzeichen verwendet, da dieses SQL-Abfragen mit
				<code>SELECT * FROM tablename WHERE value IN (fieldname)</code> f&uuml;r die ausgw&auml;hlten Werte erm&ouml;glicht.</div>
			</div>
					
		</div>
	
		<!-- Feldeigenschaften: Link -->
		<div id="link_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Link</div>
			<div class="cmtTabs">
				<ul id="linkTabsHead">
					<li><a href="#linkTab1"><span>allgemeine Linkeinstellungen</span></a></li>
					<li><a href="#linkTab2"><span>erweiterte Optionen</span></a></li>
				</ul>
				<div id="linkTab1">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Default-Wert</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_link}" name="cmt_default_link" /></div>
						<div class="editEntryRowDescription">Standard-Wert bei Neueintrag.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Leerauswahl zulassen?</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_link_noselection}" name="cmt_option_link_noselection" /></div>
						<div class="editEntryRowDescription">Alias f&uuml;r eine Leerauswahl an erster Stelle der Auswahlliste (z.B. &quot;--- keine Datei ---&quot;). 
						Das Wert-Feld (&quot;value&quot;) bleibt dabei leer.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Startpfad</div>
						<div class="editEntryRowField">
							<input type="text" value="{VAR:cmt_option_link_path}" name="cmt_option_link_path" />
							<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmt_option_link_path&amp;cmtOnlyDir=true" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmt_option_link_path">ausw&auml;hlen</a>
						</div>
						<div class="editEntryRowDescription">Startpfad: Es werden nur Dateien und Verzeichnisse in diesem Ordner und unterhalb angezeigt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Verzeichnistiefe</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_link_depth}" name="cmt_option_link_depth" /></div>
						<div class="editEntryRowDescription">Verzeichnistiefe: Zahl, die angibt, ob und wieviele Unterverzeichnisse angezeigt werden sollen:<br />
						<code>1 => </code> zeigt nur das ausgew&auml;hlte Verzeichnis an,<br />
						<code>2 => </code> alle Unterverzeichnisse des ausgew&auml;hlten Verzeichnisses,<br />
						<code>3 => </code> alle Unterordner und die erste Unterebene dieser Ordner an, etc.<br />
						Wird dieses Feld leer gelassen, werden alle Unterverzeichnisse angezeigt.</div>
					</div>
				</div>
				<div id="linkTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Option: Nur Dateien mit folgenden Endungen anzeigen</div>
						<div class="editEntryRowField"><textarea name="cmt_option_link_show" cols="20" rows="8">{VAR:cmt_option_link_show}</textarea></div>
						<div class="editEntryRowDescription">Es werden nur Dateien im Verzeichnis angezeigt, die eine dieser hier angegebenen Dateiendungen aufweisen 
						(z.B. <code>jpeg</code> zeigt nur alle Grafikdateien im JPEG-Format an.<br />
						Die Endungen m&uuml;ssen durch eine Zeilenschaltung (<code>Return/Enter-Taste</code>) voneinander getrennt eingegeben werden.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Option: Dateien mit diesen Endungen nicht anzeigen</div>
						<div class="editEntryRowField"><textarea name="cmt_option_link_dontshow" cols="20" rows="8">{VAR:cmt_option_link_dontshow}</textarea></div>
						<div class="editEntryRowDescription">Dateien im Verzeichnis mit diesen Endungen / diesem Dateityp werden nicht angezeigt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="linkAbsolute" type="checkbox" name="cmt_option_link_absolute_path" {IF ({ISSET:cmt_option_link_absolute_path})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="linkAbsolute">Pfadangabe absolut zu Startordner</label>
						</div>
						<div class="editEntryRowDescription">
							<code>../myFile.png => </code> relative Pfandangabe (voreingestellt, wenn diese Option nicht ausgew&auml;hlt)<br />
							<code>myFolder/myFile.png => </code>absolute Pfadangabe.
						</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowField">
							<input id="linkOnlyDir" type="checkbox" name="cmt_option_link_onlydir" {IF ({ISSET:cmt_option_link_onlydir})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="linkOnlyDir">Nur Verzeichnisse anzeigen.</label>
						</div>
						<div class="editEntryRowDescription">Ist diese Option gew&auml;hlt, werden nur Verzeichnisse aber keine darin befindlichen Dateien angezeigt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="linkDropdown" type="checkbox" name="cmt_option_link_show_as_dropdown" {IF ({ISSET:cmt_option_link_show_as_dropdown})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="linkDropdown">Verzeichnisse als Dropdownmenü anzeigen.</label>
						</div>
						<div class="editEntryRowDescription">Ist diese Option gew&auml;hlt, werden die Verzeichnisse in einem normalen Select-Formularfelder angezeigt. Voreinstellung ist das
						komfortablere Dateiexplorer. Diese Option sollte nur bei wenigen Dateien in einem Verzeichnis gew&auml;hlt werden.</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: HTML -->
		<div id="html_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: HTML</div>
			<div class="cmtTabs">
				<ul id="htmlTabsHead">
					<li><a href="#htmlTab1"><span>allgemeine Optionen</span></a></li>
					<li><a href="#htmlTab2"><span>HTML-Editor</span></a></li>
				</ul>
				<div id="htmlTab1">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Default-Wert</div>
						<div class="editEntryRowField"><textarea name="cmt_default_html" cols="120" rows="5">{VAR:cmt_default_html}</textarea></div>
						<div class="editEntryRowDescription">Standard-Wert bei Neueintrag</div>
					</div>
				</div>
				<div id="htmlTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="htmlEditor" type="checkbox" name="cmt_option_html_editor" {IF ({ISSET:cmt_option_html_editor})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="htmlEditor">HTML-Editor anzeigen</label>
						</div>
						<div class="editEntryRowDescription">Optional kann ein HTML-Editor (Tiny-MCE) für dieses Feld angezeigt werden.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">TinyMCE-Modus</div>
						<div class="editEntryRowField">
							<select size="1" name="cmt_option_html_tinymce_theme">
								<option value="cmtHtmlEditor" {IF ("{VAR:cmt_option_html_tinymce_theme}" == "cmtHtmlEditor")} selected="selected"{ENDIF}>content-o-mat</option>
								<option value="advanced"{IF ("{VAR:cmt_option_html_tinymce_theme}" == "advanced")} selected="selected"{ENDIF}>eingeschr&auml;nkt</option>
								<option value="simple"{IF ("{VAR:cmt_option_html_tinymce_theme}" == "simple")} selected="selected"{ENDIF}>einfach</option>
							</select>
						</div>
						<div class="editEntryRowDescription">Die Funktionsvielfalt des angezeigten HTML-Editors richtet sich nach dem hier ausgew&auml;hlte Modus. Im Modus 
						&quot;content-o-mat&quot; ist die Funktionsvielfalt am h&ouml;chsten.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Bild-Ordner</div>
						<div class="editEntryRowField">
							<input type="text" value="{VAR:cmt_option_html_tinymce_image_dir}" name="cmt_option_html_tinymce_image_dir" />
							<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmt_option_html_tinymce_image_dir&amp;cmtOnlyDir=true" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmt_option_html_tinymce_image_dir">ausw&auml;hlen</a>
						</div>
						<div class="editEntryRowDescription">
							Der hier angegebene Pfad wird im Bildauswahldialog des HTML-Editors als Root-Ordner verwendet. Der Pfad muss absolut zum Webspace-Root angegeben werden 
							allerdings ohne f&uuml;hrendes <code>/</code>, z.B. <code>myImageDir/editorImages/</code>
						</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">CSS-Datei</div>
						<div class="editEntryRowField">
							<input type="text" value="{VAR:cmt_option_html_tinymce_content_css}" name="cmt_option_html_tinymce_content_css" />
							<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmt_option_html_tinymce_content_css" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmt_option_html_tinymce_content_css">ausw&auml;hlen</a>
						</div>
						<div class="editEntryRowDescription">
							Die hier ausgew&auml;hlte CSS-Datei wird im Editor verwendet.
						</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Option: H&ouml;he und Breite des Editors</div>
						<div class="editEntryRowField">H&ouml;he&nbsp;<input type="text" value="{VAR:cmt_option_html_tinymce_height}" name="cmt_option_html_tinymce_height" class="formFieldTextShort" />&nbsp;&nbsp;
							Breite&nbsp;<input type="text" value="{VAR:cmt_option_html_tinymce_width}" name="cmt_option_html_tinymce_width" class="formFieldTextShort" />
						</div>
						<div class="editEntryRowDescription">
							Angaben in Pixeln, z.B. <code>360</code>, oder in Prozentwerten, z.B. <code>80%</code> sind m&ouml;glich
						</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Zus&auml;tzliche Initalisierungsvariablen</div>
						<div class="editEntryRowField"><textarea name="cmt_option_html_tinymce_vars" cols="20" rows="8">{VAR:cmt_option_html_tinymce_vars}</textarea></div>
						<div class="editEntryRowDescription">
							Zus&auml;tzliche Initalisierungsvariablen und -werte k&ouml;nnen hier angegeben werden, z.B. <code>... verify_html : false,
							convert_urls : false ... </code>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Position -->
		<div id="position_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Position</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Default-Wert</div>
				<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_position}" name="cmt_default_position" class="formFieldTextShort" /></div>
				<div class="editEntryRowDescription">Standard-Wert bei Neueintrag.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowHead">Option: Abh&auml;ngig von Feld</div>
				<div class="editEntryRowField">
					<select name="cmt_option_position_parent" size="1">
							<option value="">--- Keine Abh&auml;gigkeit ---</option>
						{LOOP TABLE(cmt_fields:WHERE cmt_tablename='{VAR:cmt_tablename}' AND cmt_fieldname != '{VAR:cmt_fieldname}' ORDER BY cmt_fieldname)}
							<option value="{FIELD:cmt_fieldname}" {IF ("{VAR:cmt_option_position_parent}" == "{FIELD:cmt_fieldname}")} selected="selected"{ENDIF}>{FIELD:cmt_fieldname}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;({FIELD:cmt_fieldalias})</option>
						{ENDLOOP TABLE}
					</select>
				</div>
			</div>
		</div>
	
		<!-- Feldeigenschaften: Upload -->
		<div id="upload_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Datei-Upload</div>
			<div class="cmtTabs">
				<ul id="uploadTabsHead">
					<li><a href="#uploadTab1"><span>allgemeine Optionen</span></a></li>
					<li><a href="#uploadTab2"><span>Verzeichnis aus Tabelle beziehen</span></a></li>
				</ul>
				<div id="uploadTab1">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Default-Wert</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_default_upload}" name="cmt_default_upload" /></div>
						<div class="editEntryRowDescription">Standard-Wert bei Neueintrag.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Upload-Verzeichnis</div>
						<div class="editEntryRowField">
							<input type="text" value="{VAR:cmt_option_upload_dir}" name="cmt_option_upload_dir" />
							<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmt_option_upload_dir&amp;cmtOnlyDir=true" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmt_option_upload_dir">ausw&auml;hlen</a>
						</div>
						<div class="editEntryRowDescription">Startpfad: Es werden nur Dateien und Verzeichnisse in diesem Ordner und unterhalb angezeigt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowField">
							<input id="uploadFileselector" type="checkbox" name="cmt_option_upload_show_fileselector" {IF ({ISSET:cmt_option_upload_show_fileselector:VAR} || "{VAR:action}" == "new")}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="uploadFileselector">Dateiauswahlfenster anzeigen.</label>	
						</div>
						<div class="editEntryRowDescription">Ist diese Option ausgew&auml;hlt, kann der User statt eines Uploads auch eine Datei ausw&auml;hlen, die bereits auf dem Server liegt.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowField">
							<input id="uploadOldfile" type="checkbox" name="cmt_option_upload_deleteoldfile" {IF ({ISSET:cmt_option_upload_deleteoldfile:VAR})}checked="checked"{ENDIF} />
							<label class="cmtSpecialLabel" for="uploadOldfile">Alte Datei überschreiben.</label>
						</div>
						<div class="editEntryRowDescription">Ist diese Option ausgew&auml;hlt, wird die in diesem Feld eingetragene Datei vor dem Upload der neuen Datei gel&ouml;scht</div>
					</div>
				</div>
				<div id="uploadTab2">
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Andere Tabelle</div>
						<div class="editEntryRowField">
							<select id="cmt_option_upload_other_table" name="cmt_option_upload_other_table" size="1" class="cmtFieldHandlerUpdateSelect" data-update-field-name="cmt_option_upload_other_table_field,cmt_option_upload_other_table_field_value" data-update-target-id="cmt_option_upload_other_table_field_container,cmt_option_upload_other_table_field_value_container">
								<option value="">--- Keine Tabelle ausgew&auml;hlt --</option>
								{LOOP TABLE(all)}
									<option value="{FIELD:cmt_tablename}" {IF ("{VAR:cmt_option_upload_other_table}" == "{FIELD:cmt_tablename}")} selected="selected"{ENDIF}>{FIELD:cmt_tablename}{IF ({ISSET:cmt_showname:FIELD})}&nbsp;&nbsp;&nbsp;=&gt;&nbsp;({FIELD:cmt_showname}){ENDIF}</option>
								{ENDLOOP TABLE}
							</select>
						</div>
						<div class="editEntryRowDescription">In der ausgew&auml;hlten, anderen Tabelle befindet sich das Feld mit dem Upload-Verzeichnispfad.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Feld in der anderen Tabelle</div>
						<div class="editEntryRowField" id="cmt_option_upload_other_table_field_container">
							<input type="text" value="{VAR:cmt_option_upload_other_table_field}" name="cmt_option_upload_other_table_field" id="cmt_option_upload_other_table_field" />
						</div>
						<div class="editEntryRowDescription">Geben Sie hier den Namen des Feldes in der ausgew&auml;hlten, anderen Tabelle an, in welchem der Upload-Verzeichnispfad gespeichert ist.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow0">
						<div class="editEntryRowHead">Auswahlbedingung: Feld (Wert) in der anderen Tabelle...</div>
						<div class="editEntryRowField" id="cmt_option_upload_other_table_field_value_container">
							<input type="text" value="{VAR:cmt_option_upload_other_table_field_value}" name="cmt_option_upload_other_table_field_value" id="cmt_option_upload_other_table_field_value" />
						</div>
						<div class="editEntryRowDescription">Geben Sie hier den Namen des Feldes der anderen Tabelle an, deren Wert nachfolgender Bedingung entsprechen soll.</div>
					</div>
					<div class="cmtEditEntryRow cmtEditEntryRow1">
						<div class="editEntryRowHead">Auswahlbedingung: ... entspricht Konstante oder Feld dieser Tabelle</div>
						<div class="editEntryRowField"><input type="text" value="{VAR:cmt_option_upload_this_table_field_value}" name="cmt_option_upload_this_table_field_value" /></div>
						<div class="editEntryRowDescription">Geben Sie hier in Makroschreibweise den Namen des Feldes dieser Tabelle oder einer Konstante an, deren Wert dem Wert des Feldes in 
						der anderen	Tabelle entspreichen soll. Diese Angabe wird geparst! Daher m&uuml;ssen Angaben hier wie folgt eingetragen werden: z.B. <code>{<span>FIELD</span>:myField1}</code>,
						<code>{<span>CONSTANT</span>:CMT_USERID}</code> </div>
					</div>
				</div>
			</div>
		</div>
	
	<!-- Feldeigenschaften: Systemvariable -->
		<div id="system_var_ContentArea" class="cmtContentArea">
			<div class="editEntrySubheadline">Feldtyp: Systemwert</div>
			<div class="cmtEditEntryRow cmtEditEntryRow0">
				<div class="editEntryRowHead">Werttyp</div>
				<div class="editEntryRowField">
					<select name="cmt_option_system_var_type">
						<option value="CMT_USERID" {IF ("{VAR:cmt_option_system_var_type}" == "CMT_USERID")} selected="selected"{ENDIF}>Benutzer: ID</option>
						<option value="CMT_USERNAME"{IF ("{VAR:cmt_option_system_var_type}" == "CMT_USERNAME")} selected="selected"{ENDIF}>Benutzer: Nutzername</option>
						<option value="CMT_USERALIAS"{IF ("{VAR:cmt_option_system_var_type}" == "CMT_USERALIAS")} selected="selected"{ENDIF}>Benutzer: Echter Name</option>
						<option value="CMT_GROUPID"{IF ("{VAR:cmt_option_system_var_type}" == "CMT_GROUPID")} selected="selected"{ENDIF}>Benutzergruppe: ID</option>
						<option value="sys_timestamp"{IF ("{VAR:cmt_option_system_var_type}" == "sys_timestamp")} selected="selected"{ENDIF}>Zeitstempel</option>
						<option value="ref_ip"{IF ("{VAR:cmt_option_system_var_type}" == "ref_ip")} selected="selected"{ENDIF}>Benutzer: IP-Adresse</option>
						<option value="ref_browser"{IF ("{VAR:cmt_option_system_var_type}" == "ref_browser")} selected="selected"{ENDIF}>Benutzer: Browser</option>
					</select>
				</div>
				<div class="editEntryRowDescription">Art des Systemwertes.</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow1">
				<div class="editEntryRowField">
					<input id="systemVarEditable" type="checkbox" name="cmt_option_system_var_editable" {IF ({ISSET:cmt_option_system_var_editable})}checked="checked"{ENDIF} />
					<label class="cmtSpecialLabel" for="systemVarEditable">Wert durch Benutzer bearbeitbar.</label>
				</div>
				<div class="editEntryRowDescription">Auch Benutzer k&ouml;nnen dieses Feld bearbeiten. Ist diese Option nicht gew&auml;hlt, k&ouml;nnen nur Administratoren das Feld
				bearbeiten.</div>
			</div>
		</div>
	</div>
	<!-- Für alle Felder: Feldbeschreibung -->
	<div class="editEntrySubheadline">Feldbeschreibung</div>
	<div class="serviceContainer">
		<div class="serviceContainerInner">
			<div class="editEntryRowField"><textarea name="cmt_fielddesc" cols="120" rows="5">{VAR:cmt_fielddesc}</textarea></div>
			<div class="editEntryRowDescription">Feldbeschreibung (optional)</div>
		</div>
	</div>
	<!-- <div class="editEntrySubheadline">Weitere Optionen</div>
	<div class="serviceContainer">
		<div class="serviceContainerInner">
			<div class="serviceElementContainer clearFloats">
				<div class="serviceElementCheckboxFloat">
					<input type="checkbox" name="cmt_unique" {IF ({ISSET:cmt_unique:VAR})}checked="checked"{ENDIF} />
				</div>
				<div class="serviceElementLabelCheckboxFloat">
					<span class="serviceText">Jeder Wert darf nur einmal vorkommen (UNIQUE)</span>
				</div>
			</div>
		</div>
	</div> -->
	<!-- Service -->
	<div class="serviceFooter serviceContainerButtonRow">
		<a href="{SELFURL}" class="cmtButton cmtButtonBack"></a>
		<button class="cmtButton cmtButtonSave" type="submit">speichern</button>
		
		<!-- Variablen in versteckten Feldern -->
		<input type="hidden" name="cmt_tablename" value="{VAR:cmt_tablename}" />
		<input type="hidden" name="id[0]" value="{VAR:id}" />
		<input type="hidden" name="cmtPage" value="{VAR:cmtPage}" />
		<input type="hidden" name="action" value="{VAR:action}" />
		<input type="hidden" name="save" value="1" />
	</div>
</form>
