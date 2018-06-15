<link type="text/css" href="{CMT_TEMPLATE}app_tablebrowser/css/cmt_table_structure_editor_style.css" rel="Stylesheet" />

<div class="serviceContainer">
	<form id="selectSettings" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
		<span class="serviceText">Tabellenstruktur f&uuml;r</span>&nbsp;
		{VAR:selectTable}
		&nbsp;&nbsp;<button class="cmtButton cmtButtonEdit" type="submit">bearbeiten</button>
		<input type="hidden" name="action" value="edit"/>
		<input type="hidden" name="sid" value="{SID}"/>
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
	</form>
</div>

{IF ({ISSET:errorSave:VAR})}
<div class="cmtMessage cmtMessageError">
	Die Tabellenstruktur für {VAR:cmtTableName} konnte wegen eines Fehlers nicht gespeichert werden (MySQL: {VAR:cmtErrorMySQL}).
</div>
{ENDIF}
{IF ({ISSET:successSave:VAR})}
<div class="cmtMessage cmtMessageSuccess">
	Die Tabellenstruktur für {VAR:cmtTableName} wurde erfolgreich gespeichert.
</div>
{ENDIF}

{IF ({ISSET:showFormFields})}
<div class="cmtTabs">
	<form name="editStructureForm" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
		<ul>
			<li><a href="#tabs-1">Tabellen&uuml;bersicht</a></li>
			<li><a href="#tabs-2">Ansicht: Eintrag bearbeiten</a></li>
		</ul>
		<div id="tabs-1">
			<div class="cmtEditEntryRow cmtEditEntryRow0 clearfix">
				<div id="cmtEditOverviewFieldsContainer">
					<div class="cmtFormFieldContainer">
						<textarea name="cmt_showfields" class="cmtCode">{VAR:structureOverview}</textarea>
					</div>
					<div class="cmtDescription">
						W&auml;hlen Sie die Felder aus, die in der Tabellen&uuml;bersicht angezeigt werden sollen
					</div>
				</div>
				<div class="cmtAvailableFieldsContainer">
					<div class="cmtHelpbox">
						<div class="cmtHelpboxHead">
							Felder in dieser Tabelle
						</div>
						<div class="cmtAvailableFieldsList cmtHelpboxContent">
							<div>{VAR:fieldsCol1}{VAR:fieldsCol2}</div>
						</div>
					</div>
				</div>
			</div>
			<div class="serviceContainer serviceFooter">
				<button class="cmtButton cmtButtonSave" name="action" type="text">speichern</button>
			</div>
		</div>

		<div id="tabs-2">
			<!-- Übersicht: Editmodus -->
			<div class="cmtEditEntryRow cmtEditEntryRow0 clearfix">
				<div id="cmtEditOverviewFieldsContainer">
					<div class="cmtFormFieldContainer">
						<textarea name="cmt_editstruct" class="cmtCode">{VAR:structureEditview}</textarea>
					</div>
					<div class="cmtDescription">
						Hier k&ouml;nnen Sie die Reihenfolgen und die Darstellung der Eingabefelder im Editiermodus eines Tabelleneintrages bearbeiten.
					</div>
				</div>
				<div class="cmtAvailableFieldsContainer">
					<div class="cmtHelpbox">
						<div class="cmtHelpboxHead">
							Felder in dieser Tabelle
						</div>
						<div class="cmtAvailableFieldsList cmtHelpboxContent">
							<div>{VAR:fieldsCol1}{VAR:fieldsCol2}</div>
						</div>
					</div>
				</div>
			</div>
			<div class="serviceContainer serviceFooter">
				<button class="cmtButton cmtButtonSave" type="text">speichern</button>
			</div>
			<input type="hidden" name="action" value="save"/>
			<input type="hidden" name="sid" value="{SID}"/>
			<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
			<input type="hidden" name="cmt_tablename" value="{VAR:tableName}"/>
			<input type="hidden" name="id" value="{VAR:id}"/>
		</div>
	</form>	
</div>
{ELSE}
<div class="cmtMessage cmtMessageInfo">
	Bitte wählen Sie eine Tabelle aus.
</div>
{ENDIF}
