<div id="galleryCategory_{VAR:id}" class="galleryItemContainer galleryCategoryContainer{IF ("{VAR:id}" != "{VAR:categoryID}")} galleryDroppableItem{ENDIF}{IF ('{VAR:imagesInCategory}' == '0')} galleryCategoryContainerEmpty{ENDIF} {IF ("{VAR:categoryID}" == "{VAR:id}")} galleryCategorySelected{ENDIF}">
	<a href="{SELFURL}&amp;galleryAction=showCategory&amp;galleryCategoryID={VAR:id}" class="galleryCategoryLink"> </a>
	<span class="infoLabel">{VAR:imagesInCategory}</span>
	<p class="galleryCategoryTitle">{VAR:gallery_category_title}</p>
{IF ("{VAR:galleryView}" == "galleryOverview")}
	<div class="galleryEditItemContainer">
		<p class="galleryEditItemInnerContainer">
			<a href="Javascript:void(0);" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;galleryCategoryID={VAR:id}&amp;galleryAction=deleteCategory" data-dialog-var="{VAR:gallery_category_title}" data-dialog-content-id="cmtDialogConfirmDeletion" title="Kategorie l&ouml;schen" class="cmtIcon cmtButtonDelete cmtDialog cmtDialogConfirm galleryButton" id="buttonDelete_{VAR:id}"></a>
			<a href="{SELFURL}&amp;galleryCategoryID={VAR:id}&amp;galleryAction=editCategory" title="Kategorie bearbeiten" class="cmtIcon cmtButtonEdit cmtDialog cmtDialogLoadContent galleryButton" id="buttonEdit_{VAR:id}"></a>
		</p>
	</div>
{ENDIF}
</div>