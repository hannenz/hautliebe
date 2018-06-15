{IF ({ISSET:imageDeleted})}
<div class="cmtDialogSuccess">
	<p>
		Das Bild wurde erfolgreich gel&ouml;scht.
	</p>
</div>
{ELSE}
<div class="cmtDialogError">
	<p>
		Das Bild konnte nicht gel&ouml;scht werden.
	</p>
</div>
{ENDIF}
<script type="text/javascript">
	Gallery.updateContent('{SELFURL}&galleryAction=showImages&galleryCategoryID={VAR:galleryCategoryID}');

	Gallery.setDialogType({
		type: 'dialog',
		buttonConfirmLabel: 'schlieﬂen'
	});
</script>