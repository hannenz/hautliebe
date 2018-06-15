<div class="mlogMediaContainer mlog-media-type-{VAR:mediaType} ui-corner-all" id="mediaContainer_{VAR:mediaId}" >
	<div class="mlogMediaHandle cmtHandle cmtHandleMove">{VAR:mediaTypeName} <span class="mlog-media-id">({VAR:id})</span></div>
	<div class="mlog-media-content" data-file-type="{VAR:mediaFileType}">
		{SWITCH ("{VAR:mediaType}")}
			{CASE ("image")}
				<figure>
					<img src="{VAR:mediaPath}thumbnails/{VAR:media_internal_filename}" alt="{VAR:media_type_name_de:htmlentities}" />
				</figure>
			{BREAK}
			
		{ENDSWITCH}
	</div>
	<div class="mlogMediaTitleContainer">{VAR:media_title:htmlentities}</div>
	<div class="mlogMediaServiceContainer">
		<a class="cmtButton cmtButtonDelete cmtDialog cmtDialogConfirm mlogButtonDeleteMedia" data-dialog-confirm-url="Javascript:Mlog.deleteMedia('{VAR:mediaId}', '{SELFURL}&action=mlogDeleteMedia')" data-dialog-content-id="mlogDialogConfirmMediaDeletion" data-dialog-var="{VAR:mediaId}" title="Medieneintrag löschen"></a> 
		<a class="cmtButton cmtButtonEdit cmtDialog cmtDialogLoadContent"  href="{SELFURL}&amp;action=mlogEditMedia&amp;mediaId={VAR:mediaId}&amp;mediaType={VAR:mediaType}" title="Medieneintrag bearbeiten (ID: {VAR:mediaId})">bearbeiten</a>
	</div>
</div>