	<form enctype="multipart/form-data" method="POST" action="" name="cmtUploadForm" id="cmtUploadForm" style="overflow: hidden">
		{VAR:filesUploadContent}
		<div class="cmtDialogButtons">
			<button class="cmtButton cmtButtonBack" type="button">abbrechen</button>
			<button class="cmtButton cmtButtonUploadFile" type="submit">hochladen</button>
			<input type="hidden" value="upload" name="cmtAction">
			<input type="hidden" value="{VAR:uploadDirectory}" name="cmtDirectory">
		</div>
	</form>