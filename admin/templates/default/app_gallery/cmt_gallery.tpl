	<link rel="Stylesheet" href="{CMT_TEMPLATE}app_gallery/css/cmt_gallery_style.css" type="text/css" />
	<script src="{CMT_TEMPLATE}app_gallery/javascript/cmt_gallery_functions.js" type="text/javascript"></script>

	<div class="appHeadlineContainer">
		<img class="appIcon" src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_gallery/img/app_gallery_icon.png{ENDIF}" alt="" />
		<h1>{IF ({ISSET:gallery_title:VAR})}{VAR:gallery_title}{ELSE}Bildergalerie{ENDIF}</h1>
	</div>

	<div id="cmtMessageContainer"></div>
{IF ("{VAR:galleryView}" == "categoryOverview")}
	<div class="cmtServiceContainer">
		<a href="{SELFURL}" class="cmtButton cmtButtonBack"></a>
		<img src="templates/default/general/img/service_separator_24px.gif" class="serviceSeparator">
		<a href="{SELFURL}&amp;galleryCategoryID={VAR:categoryID}&amp;galleryAction=newImage&amp;cmtDialog=true" class="cmtButton cmtButtonGalleryNewImage cmtDialog cmtDialogLoadContent"  data-width="800" title="Neues Bild">neues Bild</a>
		<a href="#" class="cmtButton cmtButtonSave gallerySaveCategory">Kategorie speichern</a>
		<img src="templates/default/general/img/service_separator_24px.gif" class="serviceSeparator">
		<span class="serviceText serviceTextSpecial">Kategorie: <strong>{VAR:gallery_category_title}</strong></span>
		<img src="templates/default/general/img/service_separator_24px.gif" class="serviceSeparator">
		<span class="serviceText">Bilder: <strong><span id="galleryImagesInCategory">{VAR:imagesInCategory}</span></strong></span>
	</div>
{ELSE}
	<div class="cmtServiceContainer">
		<a class="cmtButton cmtButtonGalleryNewCategory cmtDialog cmtDialogLoadContent" href="{SELFURL}&amp;galleryAction=newCategory&amp;cmtDialog=true" title="neue Kategorie">neue Kategorie</a>
	</div>
{ENDIF}
	<div id="cmtContentContainer">
		<div id="galleryCategoryContent">
			{VAR:galleryContent}
			{IF ({ISSET:categoryList:VAR})}
			<div id="galleryCategoriesList" class="cmtResizeHeight">
				{VAR:categoryList}
			</div>
			{ENDIF}
		</div>
	</div>
	<div id="cmtFooterContainer"></div>
	<div id="cmtModalWin" class="cmtModalWindow" style="display: none;"></div>
	{INCLUDE:PATHTOADMIN.CMT_TEMPLATE.'app_gallery/cmt_gallery_messages.tpl'}

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
	{IF ("{VAR:galleryView}" == "categoryOverview")}
		M&ouml;chten Sie das Bild <span class="cmtDialogVar"></span> wirklich l&ouml;schen?
	{ELSE}
		M&ouml;chten Sie den Ordner <span class="cmtDialogVar"></span> wirklich l&ouml;schen?
	{ENDIF}
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>
<script type="text/javascript">
	cmtGallery.setCurrentCategoryID('{VAR:categoryID}');
</script>
{IF ({ISSET:galleryMessage:GETVAR})}
	<script type="text/javascript">
		cmtPage.showMessage(cmtMessages['{GETVAR:galleryMessageType}']['{GETVAR:galleryMessage}'], '{GETVAR:galleryMessageType}'); 
	</script>
{ENDIF}