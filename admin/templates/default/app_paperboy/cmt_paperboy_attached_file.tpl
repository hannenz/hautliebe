<div class="">
	<label for="newsletterAttachment{VAR:attachmentNr}">Angehängte Datei {VAR:attachmentNr}:</label>
	<input id="newsletterAttachment{VAR:attachmentNr}" type="text" name="newsletterAttachedFile[{VAR:attachmentNr}]" value="{VAR:attachedFileName}" disabled="disabled" />
	<button class="cmtButton cmtButtonDelete cmtDialog cmtDialogConfirm" data-dialog-content-id="newsletterDialogAttachmentDeletion" data-dialog-title="Anhang entfernen" data-dialog-var="{VAR:attachedFilename}" data-dialog-confirm-url="{SELFURL}&amp;cmtAction=deleteAttachment&amp;attachmentNr={VAR:attachmentNr}"></button> 
	<span class="serviceText">{VAR:fileSize} {VAR:fileSizeUnit}</span>
</div>