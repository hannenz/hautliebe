<div id="cmtMediaFormContainer">
	<form method="POST" action="{SELFURL}" id="cmtMediaForm"  enctype="multipart/form-data" target="iframe-post-form">
		<div class="cmtMediaStyle{VAR:cmtMediaType:ucfirst}">
	{SWITCH ("{VAR:cmtMediaType}")}
		
		{CASE ("image")}
			<!--    IMAGE    -->
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Bild</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_image_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_image_file}" />
					<input type="file" name="media_image_file_newfile" class="cmtFormCustomFileInput"/>
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier ein neues Bild im JPG-, PNG- oder GIF-Format hoch.</div>	
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Bildunterschrift, bzw. -beschreibung genutzt.
				</div>
			</div>

		{BREAK}
		
		{CASE ("document")}
			<!--    DOCUMENT    -->
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Dokument</div>
				<div class="cmtEditRowField">
					<input type="text" name="media_document_file" class="cmtFormFile cmtFormFieldMedium" value="{VAR:media_document_file}"/>
					<input type="file" name="media_document_file_newfile" class="cmtFormCustomFileInput" />
				</div>
				<div class="cmtEditEntryRowDescription">Laden Sie hier ein PDF-, Office-Dokument oder eine andere Datei hoch, die zum Download angeboten werden soll.</div>	
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Bildunterschrift, bzw. -beschreibung genutzt.
				</div>
			</div>
			{BREAK}
		
		{CASE ("link")}
		<!--    LINK    -->
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_title}" name="media_title">
				</div>
				<div class="cmtEditEntryRowDescription">
					Der hier eigegebene Text wird als Linktitel auf der Website ausgegeben.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Link-URL</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:media_link_url}" name="media_link_url" />
				</div>
				<div class="cmtEditEntryRowDescription">
					Bitte geben Sie hier die URL, den Link ein.
				</div>
			</div>
		{BREAK}
		
		{CASE ("date")}
		<!--    Termin    -->
		<div class="mlogMediaStyle{VAR:mediaType}">
			<div class="cmtEditRow cmtEditRow0">
				<div class="cmtEditRowHead">Titel</div>
				<div class="cmtEditRowField">
					<input type="text" size="40" value="{VAR:mediaTitle}" name="mediaTitle">
				</div>
				<div class="cmtEditEntryRowDescription">
					Titel des Termins, z.B. ein Veranstaltungstitel.
				</div>
			</div>
			<div class="cmtEditRow cmtEditRow1">
				<div class="cmtEditRowHead">Datum: Start</div>
				<div class="cmtEditRowField">
					<div class="editEntryRowField">
						<input type="text" class="cmtDateDay" id="mediaStartDate_day" name="mediaStartDate_day" value="{VAR:mediaStartDate_day}" /> .
						<input type="text" class="cmtDateMonth" id="mediaStartDate_month" name="mediaStartDate_month" value="{VAR:mediaStartDate_month}" /> .
						<input type="text" class="cmtDateYear" id="mediaStartDate_year" name="mediaStartDate_year" value="{VAR:mediaStartDate_year}" />,
						&nbsp;<input type="text" class="cmtDateHour" id="mediaStartDate_hour" name="mediaStartDate_hour" value="{VAR:mediaStartDate_hour}" /> :
						<input type="text" class="cmtDateMinute" id="mediaStartDate_minute" name="mediaStartDate_minute" value="{VAR:mediaStartDate_minute}" /> 
						<input type="hidden" class="cmtDateSecond" id="mediaStartDate_second" name="mediaStartDate_second" value="{VAR:mediaStartDate_second}" />
						Uhr
						<!-- <a class="cmtButton cmtButtonShowCalendar cmtShowCalendar" data-dateformat="yyyy-mm-dd" href="Javascript: void(0);"></a> -->
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
						<input type="text" class="cmtDateDay" id="mediaEndDate_day" name="mediaEndDate_day" value="{VAR:mediaEndDate_day}" /> .
						<input type="text" class="cmtDateMonth" id="mediaEndDate_month" name="mediaEndDate_month" value="{VAR:mediaEndDate_month}" /> .
						<input type="text" class="cmtDateYear" id="mediaEndDate_year" name="mediaEndDate_year" value="{VAR:mediaEndDate_year}" />,
						&nbsp;<input type="text" class="cmtDateHour" id="mediaEndDate_hour" name="mediaEndDate_hour" value="{VAR:mediaEndDate_hour}" /> :
						<input type="text" class="cmtDateMinute" id="mediaEndDate_minute" name="mediaEndDate_minute" value="{VAR:mediaEndDate_minute}" /> 
						<input type="hidden" class="cmtDateSecond" id="mediaEndDate_second" name="mediaEndDate_second" value="{VAR:mediaEndDate_second}" />
						Uhr
						<!-- <a class="cmtButton cmtButtonShowCalendar cmtShowCalendar" data-dateformat="yyyy-mm-dd" href="Javascript: void(0);"></a> -->
					</div>
				</div>
				<div class="cmtEditEntryRowDescription">
					Das Enddatum ist optional und kann weggelassen werden.
				</div>
			</div>
		</div>
		{BREAK}
		{ENDSWITCH}
		</div>
		<div class="serviceContainer serviceFooter">
			<input type="hidden" name="cmtEntryID" value="{VAR:cmtEntryID}" />
			<input type="hidden" name="cmtTableID" value="{VAR:cmtTableID}" />
			<input type="hidden" name="cmtMediaID" value="{VAR:cmtMediaID}" />
			<input type="hidden" name="cmtMediaType" value="{VAR:cmtMediaType}" />
			<input type="hidden" name="cmtAction" value="saveMedia" />
			<a class="cmtButton cmtButtonBack cmtDialogClose" href="Javascript:void(0)">abbrechen</a>
			<button class="cmtButton cmtButtonSave" type="submit">speichern</button>
		</div>
	</form>
</div>
<script type="text/javascript">
	cmtMedia.initMediaEditForm('#cmtMediaForm');
</script>
</script>