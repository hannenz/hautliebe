<div id="mediaFormContainer">
	<form method="POST" action="{SELFURL}" id="editMediaForm"  enctype="multipart/form-data" target="iframe-post-form">
		
		{IF ("{VAR:mediaType}" == "image")}
		<!--    IMAGE    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<!-- <h3 class="cmtSubheadline">Bild hinzufügen</h3> -->
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Bildunterschrift, bzw. -beschreibung genutzt.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Bild</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_file}"/>
					<input type="file" name="media_file_newfile" class="cmtFormCustomFileInput" />
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier ein neues Bild hoch. Erlaubte Formate: <pre>{VAR:allowedMediaTypes}</pre></div>	
			</div>	
		</div>
		{ENDIF}
		
		{IF("{VAR:mediaType}"=="document")}
		<!--    DOCUMENT    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Dokumentbeschreibung genutzt.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Dokumentdatei</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_file}"/>
					<input type="file" name="media_file_newfile" class="cmtFormCustomFileInput" />
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier eine Dokument-Datei hoch. Erlaubte Formate: <pre>{VAR:allowedMediaTypes}</div>	
			</div>	
		</div>
		{ENDIF}
		
		{IF("{VAR:mediaType}"=="audio")}
		<!--    DOCUMENT    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Dateibeschreibung genutzt.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Audiodatei</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_file}"/>
					<input type="file" name="media_file_newfile" class="cmtFormCustomFileInput" />
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier eine Audio-Datei hoch. Erlaubte Formate: <pre>{VAR:allowedMediaTypes}</div>	
			</div>	
		</div>
		{ENDIF}

		{IF("{VAR:mediaType}"=="video")}
		<!--    VIDEO    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Dateibeschreibung genutzt.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Videodatei</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_file}"/>
					<input type="file" name="media_file_newfile" class="cmtFormCustomFileInput" />
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier eine Video-Datei hoch. Erlaubte Formate: <pre>{VAR:allowedMediaTypes}</div>	
			</div>	
		</div>
		{ENDIF}
		
		{IF("{VAR:mediaType}"=="link")}
		<!--    LINK    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird MLog intern als Titel für diesen Link benutzt.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Link-URL</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_url}" name="media_url" />
				</div>
				<div class="cmtEditEntryRowDescription">
					Bitte geben Sie hier den URL des Links ein.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Link-Alias</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_url_alias}" name="media_url_alias">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird auf der Website verlinkt ausgegeben. Fehlt diese Angabe, wird stattdessen 
					die Link-Adresse im Klartext angezeigt.
				</div>
			</div>
		</div>
		{ENDIF}
		
		{IF("{VAR:mediaType}"=="date")}
		<!--    Termin    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Titel des Termins, z.B. ein Veranstaltungstitel.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Datum: Start</div>
				<div class="cmtEditRowField">
					<div class="editEntryRowField">
						<input type="text" class="cmtDateDay" name="media_start_date_day" value="{VAR:media_start_date_day}" placeholder="03" /> .
						<input type="text" class="cmtDateMonth" name="media_start_date_month" value="{VAR:media_start_date_month}" placeholder="12" /> .
						<input type="text" class="cmtDateYear" name="media_start_date_year" value="{VAR:media_start_date_year}" placeholder="2016" />,
						&nbsp;<input type="text" class="cmtDateHour"name="media_start_date_hour" value="{VAR:media_start_date_hour}" placeholder="18" /> :
						<input type="text" class="cmtDateMinute" name="media_start_date_minute" value="{VAR:media_start_date_minute}" placeholder="30" /> 
						<input type="hidden" class="cmtDateSecond" name="media_start_date_second" value="{VAR:media_start_date_second}" />
						Uhr
					</div>
				</div>
				<div class="cmtEditEntryRowDescription">
					Das Startdatum ist obligatorisch.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Datum: Ende</div>
				<div class="cmtEditRowField">
					<div class="editEntryRowField">
						<input type="text" class="cmtDateDay" name="media_end_date_day" value="{VAR:media_end_date_day}" /> .
						<input type="text" class="cmtDateMonth" name="media_end_date_month" value="{VAR:media_end_date_month}" /> .
						<input type="text" class="cmtDateYear"  name="media_end_date_year" value="{VAR:media_end_date_year}" />,
						&nbsp;<input type="text" class="cmtDateHour"  name="media_end_date_hour" value="{VAR:media_end_date_hour}" /> :
						<input type="text" class="cmtDateMinute" name="media_end_date_minute" value="{VAR:media_end_date_minute}" /> 
						<input type="hidden" class="cmtDateSecond" name="media_end_date_second" value="{VAR:media_end_date_second}" />
						Uhr
					</div>
				</div>
				<div class="cmtEditEntryRowDescription">
					Das Enddatum ist optional und kann weggelassen werden.
				</div>
			</div>
		</div>
		{ENDIF}
		
		
		<div class="serviceContainer serviceFooter">
		
			<input type="hidden" name="postId" value="{VAR:postId}" />
			<input type="hidden" name="mediaId" value="{VAR:mediaId}" />
			<input type="hidden" name="mediaTypeId" value="{VAR:mediaTypeId}" />
			<input type="hidden" name="mediaType" value="{VAR:mediaType}" />
<!-- 			<input type="hidden" name="mediaDateFormat" value="dd.mm.yyyy" /> -->
			<input type="hidden" name="action" value="mlogSaveMedia" />

			<a class="cmtButton cmtButtonBack cmtDialogClose" href="Javascript:void(0)">abbrechen</a>
			<button class="cmtButton cmtButtonSave" type="submit">speichern</button>
		</div>
	</form>
</div>
<script type="text/javascript">
	Mlog.initMediaEditForm('#editMediaForm');
</script>
