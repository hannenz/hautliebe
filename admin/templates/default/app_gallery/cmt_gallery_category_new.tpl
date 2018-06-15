<p>Geben Sie hier einen Kategorienamen und eine kurze Beschreibung der Kategorieinhalte ein.</p>
<form id="newCategory" name="newCategory" action="{SELFURL}" method="post">
	<div class="cmtFormRow cmtFormRow0">
		<label for="categoryTitle">Kategorietitel</label>
		<input type="text" name="categoryTitle" id="categoryTitle" value="{VAR:categoryTitle:htmlentities}" />
	</div>
	<div class="cmtFormRow cmtFormRow1">
		<label for="categoryDescription">Kategoriebeschreibung</label>
		<textarea cols="20" rows="10" name="categoryDescription" id="categoryDescription">{VAR:categoryDescription:htmlentities}</textarea>
	</div>
	<div class="cmtDialogButtons">
		<input type="submit" value="anlegen!" />
		<input type="hidden" name="galleryAction" value="newCategoryProceed" />
	</div>
</form>
<script type="text/javascript">
	Gallery.initForm('#newCategory');
</script>