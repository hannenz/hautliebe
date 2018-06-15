<link rel="Stylesheet" href="{CMT_TEMPLATE}app_showtable/css/cmt_media_style.css" type="text/css" />
<script src="{CMT_TEMPLATE}app_showtable/javascript/cmt_media_functions.js"></script>
<div id="cmtAddMediaContainer">
	<select id="cmtAddMediaType" data-url="{SELFURL}">
		{VAR:mediaTypeListContent}
	</select>
	<a href="{SELFURL}&amp;cmtEntryID={VAR:cmtEntryID}&amp;cmtTableID={VAR:cmtTableID}" title="Medium hinzufügen" id="cmtAddMediaButton" class="cmtButton cmtButtonAdd cmtDialog cmtDialogLoadContent">hinzuf&uuml;gen</a>
</div>
<div id="cmtMediaSortableContainer" class="clearfix">
	{VAR:mediaContent} 
</div>
<div>
	<input type="hidden" value="" name="cmtMediaPositions" id="cmtMediaPositions" />
	<input type="hidden" value="{VAR:cmtEntryID}" name="cmtEntryID" id="cmtEntryID" />
	<input type="hidden" value="{VAR:cmtTableID}" name="cmtTableID" id="cmtTableID" />
</div>
<div id="cmtDialogConfirmMediaDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>