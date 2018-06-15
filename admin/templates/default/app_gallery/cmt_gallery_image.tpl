<div class="galleryItemContainer galleryImageContainer gallerySortableItem" id="imageID_{VAR:id}">
	<a href="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=showImage" id="showImageLink_{VAR:id}" class="galleryImageLink" title="{VAR:gallery_image_title:htmlentities}" rel="({VAR:width}x{VAR:height}, {VAR:fileSizeKiloBytes} KB)">
		<img src="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=showThumbnail" alt="" class="galleryImage" />
	</a>
	<p class="galleryImageCaption">{VAR:imageTitle}</p>
	<div class="galleryEditItemContainer">
		<p class="galleryEditItemInnerContainer">
			<a href="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=editImage&amp;cmtDialog=true" title="Bilddaten bearbeiten" class="cmtIcon cmtButtonEdit cmtDialog cmtDialogLoadContent galleryButton" id="buttonEdit_{VAR:id}"></a>
			<a href="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=showImageInfos&amp;cmtDialog=true" title="Bildinformationen" class="cmtIcon cmtButtonInfo cmtDialog cmtDialogLoadContent galleryButton" id="buttonInfo_{VAR:id}"></a>
			<a href="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=downloadImage&amp;cmtDialog=true" title="Bild herunterladen" class="cmtIcon cmtButtonDownloadFile" id="buttonDownload_{VAR:id}"></a>
			<a href="Javascript:void(0);" title="Bild l&ouml;schen" class="cmtIcon cmtButtonDelete cmtDialog cmtDialogConfirm galleryButton" id="buttonDelete_{VAR:id}" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;galleryImageID={VAR:id}&amp;galleryAction=deleteImage" data-dialog-var="{VAR:gallery_image_title}" data-dialog-content-id="cmtDialogConfirmDeletion"></a>
		</p>
	</div>
</div>