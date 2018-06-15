<div class="cmtMediaContainer cmtMediaType{VAR:media_type:ucfirst} ui-corner-all" id="mediaContainer_{VAR:id}" >
	<div class="cmtMediaHandle cmtHandle cmtHandleMove">({VAR:id})</div>
	<div class="cmtMediaContentContainer" data-file-type="{VAR:mediaFileExtension}">
		{IF("{VAR:media_type}"=="image")}
		<figure>
			<img src="{VAR:mediaImagePath}thumbnails/{VAR:media_image_file_internal}" alt="{VAR:media_title}" />
		</figure>
		{ENDIF}
	</div>
	<div class="cmtMediaTitleContainer">{VAR:media_title}</div>
	<div class="cmtMediaServiceContainer">
		<a class="cmtButton cmtButtonDelete cmtDialog cmtDialogConfirm cmtButtonDeleteMedia" data-dialog-confirm-url="Javascript:cmtMedia.deleteMedia('{VAR:id}')" data-dialog-content-id="cmtDialogConfirmMediaDeletion" data-dialog-var="{VAR:id}" title="Medieneintrag l&ouml;schen"></a> 
		<a class="cmtButton cmtButtonEdit cmtDialog cmtDialogLoadContent" href="{SELFURL}&amp;cmtAction=editMedia&amp;cmtMediaID={VAR:id}&amp;cmtMediaType={VAR:media_type}&amp;cmtEntryID={VAR:cmtEntryID}&amp;cmtTableID={VAR:cmtTableID}" title="Medieneintrag bearbeiten">bearbeiten</a>
	</div>
</div>