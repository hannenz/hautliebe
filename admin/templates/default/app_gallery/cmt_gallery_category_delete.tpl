{IF ({ISSET:categoryDeleted})}
<div class="cmtDialogSuccess">
	<p>
		Die Kategorie wurde erfolgreich gel&ouml;scht
	</p>
</div>
{ELSE}
<div class="cmtDialogError">
	<p>
		Die Kategorie konnte nicht gel&ouml;scht werden
	</p>
</div>
{ENDIF}
<script type="text/javascript">
	Gallery.updateContent('{SELFURL}&galleryAction=showCategories');

	Gallery.setDialogType({
		type: 'dialog',
		buttonConfirmLabel: 'schlieﬂen'
	});
</script>