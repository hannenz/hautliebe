<div class="cmtDialogConfirmation">
	<p>
		Soll dieses Bild wirklich gel&ouml;scht werden?
	</p>
   	<table summary="image informations">
   		<tr>
   			<td class="propertyName"><span class="cmtServiceTextSpecial">Bildname: </span></td>
   			<td class="propertyValue">{VAR:gallery_image_title}</td>
   		</tr>
   		<tr>
   			<td class="propertyName"><span class="cmtServiceTextSpecial">Bilddatei: </span></td>
   			<td class="propertyValue">{VAR:imagesBasePath}{VAR:gallery_image_file}</td>
   		</tr>
   	</table>
	<form name="confirmImageDeletion" id="confirmImageDeletion" action="{SELFURL}" method="post">
		<input type="hidden" name="galleryImageID" value="{VAR:id}" />
		<input type="hidden" name="galleryAction" value="deleteImageProceed" />
	</form>
</div>
<script type="text/javascript">
	Gallery.initForm('#confirmImageDeletion');

	Gallery.setDialogType({
		type: 'confirm',
		buttonConfirmLabel: 'Bild l' + String.fromCharCode(246) + 'schen',
		buttonConfirmAction: function(ev) {
			//ev.preventDefault();
			Gallery.submitFormInDialog('confirmImageDeletion');
		},
		buttonCancelLabel: 'abbrechen',
		buttonCancelAction: null
	});
</script>