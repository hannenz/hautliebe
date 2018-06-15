<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="editEntrySubheadline">Allgemein</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Titel der Galerieanwendung</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_gallery_title" id="cmtsetting_gallery_title" value="{VAR:gallery_title}" />
		</div>
		<div class="cmtEditRowDescription">
			Titel dieser Galerieanwendung.
		</div>
	</div>
	<div class="editEntrySubheadline">Datenbanktabellen</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Tabelle f&uuml;r Bildkategorien</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_table_categories" id="cmtsetting_table_categories" value="{VAR:table_categories}" />
		</div>
		<div class="cmtEditRowDescription">
		Name der Datenbanktabelle in welcher die Bildkategorien gespeichert werden (Standard: gallery_categories).
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Tabelle f&uuml;r Bilder</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_table_images" id="cmtsetting_table_images" value="{VAR:table_images}" />
		</div>
		<div class="cmtEditRowDescription">
		Name der Datenbanktabelle in welcher die Bilddaten gespeichert werden (Standard: gallery_images).
		</div>
	</div>
	<div class="editEntrySubheadline">Thumbnails</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Datenbankfeld f&uuml;r Thumbnailbild</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_thumbnail_from_field" id="cmtsetting_thumbnail_from_field" value="{VAR:thumbnail_from_field}" />
		</div>
		<div class="cmtEditRowDescription">
			Aus diesem Feld wird das Thumbnail generiert (Standard: gallery_image_file).
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Datenbankfeld f&uuml;r Thumbnailname</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_thumbnail_title_from_field" id="cmtsetting_thumbnail_title_from_field" value="{VAR:thumbnail_title_from_field}" />
		</div>
		<div class="cmtEditRowDescription">
			Aus diesem Feld wird der Titel des Thumbnails generiert (Standard: gallery_image_title).
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Thumbnailgr&ouml;&szlig;e (BxH)</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_thumbnail_width" class="small" value="{VAR:thumbnail_width}" /> 
			x <input type="text" name="cmtsetting_thumbnail_height" class="small" value="{VAR:thumbnail_height}" /> Pixel
		</div>
		<div class="cmtEditRowDescription">
		Gr&ouml;&szlig;e des automatisch erzeugten Thumbnails (Breite&nbsp;x&nbsp;H&ouml;he) in Pixeln (idealerweise nicht mehr als 160 x 120 da sonst auch die CSS-Angaben angepasst werden m&uuml;ssen).
		</div>
	</div>
	<div class="editEntrySubheadline">Dateipfade</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Bilderpfad</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_images_base_path" id="cmtsetting_images_base_path" value="{VAR:images_base_path}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_images_base_path" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Pfad wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Dateipfad der hochgeladenen Bilder (Standard: /img/gallery/).
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Thumbnailpfad</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_thumbnails_base_path" id="cmtsetting_thumbnails_base_path" value="{VAR:thumbnails_base_path}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_thumbnails_base_path" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Pfad wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Dateipfad der automatisch erzeugten Thumbnails (Standard: /img/gallery/thumbnails/).
		</div>
	</div>
	<div class="serviceContainer">
		<button class="cmtButton cmtButtonSave" name="action">speichern</button>
		<input type="hidden" name="sid" value="{SID}"/>
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
		<input type="hidden" name="id" value="{VAR:tableId}"/>
		<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
		<input type="hidden" name="action" value="save"/>
	</div>
</form>