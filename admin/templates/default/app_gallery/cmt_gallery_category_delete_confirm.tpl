<div class="cmtDialogConfirmation">
	<p>
		Soll die Kategorie wirklich gel&ouml;scht werden? Die Bilder innerhalb der Kategorie werden ebenfalls gel&ouml;scht!
	</p>
   	<table summary="category informations">
   		<tr>
   			<td class="propertyName"><span class="cmtServiceTextSpecial">Kategoriename: </span></td>
   			<td class="propertyValue">{VAR:gallery_category_title}</td>
   		</tr>
   		<tr>
   			<td class="propertyName"><span class="cmtServiceTextSpecial">Bilder in Kategorie: </span></td>
   			<td class="propertyValue">{VAR:imagesInCategory}</td>
   		</tr>
   	</table>
	<form name="confirmCategoryDeletion" id="confirmCategoryDeletion" action="{SELFURL}" method="post">
		<input type="hidden" name="galleryCategoryID" value="{VAR:id}" />
		<input type="hidden" name="galleryAction" value="deleteCategoryProceed" />
	</form>
</div>
<script type="text/javascript">
	Gallery.initForm('#confirmCategoryDeletion');

	Gallery.setDialogType({
		type: 'confirm',
		buttonConfirmLabel: 'l' + String.fromCharCode(246) + 'schen',
		buttonConfirmAction: function(ev) {
			Gallery.submitFormInDialog('confirmCategoryDeletion');
		},
		buttonCancelLabel: 'abbrechen',
		buttonCancelAction: null
	});
</script>