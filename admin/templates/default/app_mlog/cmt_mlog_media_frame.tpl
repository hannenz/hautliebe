<div id="mlogAddMediaContainer">
	<select id="mlogAddNewMedia" data-url="{SELFURL}">
		{LOOP VAR(availableMediaTypes)}
		<option value="{VAR:mediaType}">{VAR:mediaTypeName}</option>
		{ENDLOOP VAR}
		{VAR:mediaTypeListContent}
	</select>
	<a href="{SELFURL}&amp;id[]={VAR:postId}" title="Medien hinzufügen" id="mlogAddNewMediaButton" class="cmtButton cmtButtonAdd cmtDialog cmtDialogLoadContent">hinzufügen</a>
</div>

<!-- media content container -->
<div id="mlogMediaSortableContainer" class="clearfix">
	{VAR:postMediaContent} 
</div>

<!-- hidden fields container -->
<div>
	<input type="hidden" value="" name="mediaPositions" id="mediaPositions" />
</div>

<!-- dialog window -->
<div id="mlogDialogConfirmMediaDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		Möchten Sie diesen Eintrag wirklich löschen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">löschen</span>
	</div>
</div>
<script type="text/javascript">
	Mlog.selfUrl = "{SELFURL}";
	tinymce.settings.cmtFilePath =  '/media/mlog/static/';
</script>
