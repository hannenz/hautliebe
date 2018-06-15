<script type="text/javascript" src="javascript/windows_js/javascripts/window.js"></script>
<script type="text/javascript" src="{CMT_TEMPLATE}general/appinc_fileselector/javascript/cmt_fileselector_functions.js"></script>

<link type="text/css" href="{CMT_TEMPLATE}general/appinc_fileselector/cmt_fileselector_style.css" rel="Stylesheet"/>

<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data" onSubmit="includeCheckboxes(this)">

<!--

	<div class="editEntrySubheadline">Facebook-Fanpage einbinden</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Page Name</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_facebook_page_name" class="small" value="{VAR:facebook_page_name}" /> 
		</div>
		<div class="editEntryDescription">
		</div>
	</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">App Id</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_facebook_app_id" class="small" value="{VAR:facebook_app_id}" /> 
		</div>
		<div class="editEntryDescription">
		</div>
	</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Api Secret</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_facebook_secret" class="small" value="{VAR:facebook_secret}" /> 
		</div>
		<div class="editEntryDescription">
		</div>
	</div>
-->


	<div class="editEntrySubheadline">Bilder</div>

	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Bilddatei-pfad</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_image_path" id="cmtsetting_media_image_path" value="{VAR:media_image_path}" />&nbsp;
		</div>
		<div class="editEntryDescription">
			Bilddatei-pfad der hochgeladenen Bilder (Standard Pfad: img/mlog/).
		</div>
	</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Unterstützte Bilddatei-Typen:</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_image_types" class="small" value="{VAR:media_image_types}" /> 
		</div>
		<div class="editEntryDescription">
			Unterstützte Bilddatei-Typen, Standard: jpeg,jpg,png,gif.
		</div>
	</div>
	
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Maximale Bilddateigröße:</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_image_maxSize" class="small" value="{VAR:media_image_maxSize}" /> 
		</div>
		<div class="editEntryDescription">
			Maximale Bilddateigröße in MB, Standard: 2. 
		</div>
	</div>
	
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Thumbnail Size:</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_image_thumbnailSize" class="small" value="{VAR:media_image_thumbnailSize}" /> 
		</div>
		<div class="editEntryDescription">
			Thumbnail Size, Standard: 160x120. 
		</div>
	</div>
	
	
	
	
	

	<div class="editEntrySubheadline">Dokumente</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Dokumentpfad</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_document_path" id="cmtsetting_media_document_path" value="{VAR:media_document_path}" />&nbsp;
		</div>
		<div class="editEntryDescription">
			Dateipfad der hochgeladenen Dokumente (Standard Pfad: downloads/mlog/).
		</div>
	</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Dokumentdateitypen</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_document_types" class="small" value="{VAR:media_document_types}" /> 
		</div>
		<div class="editEntryDescription">
			Standard Bilddateitypen: doc,docx,pdf.
		</div>
	</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Maximale Dokumentdateigröße</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_document_maxSize" class="small" value="{VAR:media_document_maxSize}" /> 
		</div>
		<div class="editEntryDescription">
			Maximale Bilddateigröße in MB, Standard: 2. 
		</div>
	</div>
	
	
	
	

	<div class="editEntrySubheadline">Dateien</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Dateienpfad</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_file_path" id="cmtsetting_media_file_path" value="{VAR:media_file_path}" />&nbsp;
		</div>
		<div class="editEntryDescription">
			Dateipfad der hochgeladenen Dateien (Standard Pfad: downloads/mlog/).
		</div>
	</div>
	
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Dateitypen</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_file_types" class="small" value="{VAR:media_file_types}" /> 
		</div>
		<div class="editEntryDescription">
			Standard Bilddateitypen: zip,rar.
		</div>
	</div>
	
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="dataRowHead">Maximale Dateigröße</div>
		<div class="dataRowField">
			<input type="text" name="cmtsetting_media_file_maxSize" class="small" value="{VAR:media_file_maxSize}" /> 
		</div>
		<div class="editEntryDescription">
			Maximale Dateigröße in MB, Standard: 5. 
		</div>
	</div>

	



	<div class="serviceContainer">
		<input src="{CMT_TEMPLATE}general/img/icon_save_24px.png" name="action" value="save" type="image" style="margin: 0px;"/></div>
	<input type="hidden" name="sid" value="{SID}"/>
	<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
	<input type="hidden" name="id" value="{VAR:tableId}"/>
	<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
	<input type="hidden" name="action" value="save"/>
</div>
</form>
