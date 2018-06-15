<link rel="Stylesheet" href="{CMT_TEMPLATE}app_showtable/css/cmt_datalayout_style.css" type="text/css" />
<script src="{CMT_TEMPLATE}app_showtable/javascript/cmt_datalayout_functions.js"></script>
<div id="cmt-datalayout">
	<div id="cmt-templates-wrapper">
		<h4>Verfügbare Vorlagen</h4>
	
		<div id="cmt-templates">
			{LOOP VAR(cmtTemplates)}
				<div class="cmt-object-template cmt-object-template-{VAR:id}" data-id="{VAR:id}">
					{VAR:cmt_name}<button type="button" class="cmt-delete-layout-object"></button>
				</div>
			{ENDLOOP VAR}
		</div>
	</div>
	<div id="cmt-layout-wrapper">
		<h4>Datenlayout</h4>
		<div id="cmt-layout" class="clearfix">
			{LOOP VAR(cmtSelectedTemplates)}
			<div class="cmt-object-template cmt-object-template-{VAR:id}" data-id="{VAR:id}">
				{VAR:cmt_name}<button type="button" class="cmt-delete-layout-object"></button>
			</div>
			{ENDLOOP VAR}
		</div>
	</div>
</div>
<div>
<!-- 	<input type="hidden" value="{VAR:cmtTemplatePositions}" name="cmtTemplatePositions" id="cmtTemplatePositions" /> -->
	<input type="hidden" value="{VAR:cmtEntryId}" name="cmtEntryId" id="cmtEntryId" />
	<input type="hidden" value="{VAR:cmtTableId}" name="cmtTableId" id="cmtTableId" />
</div>
<div id="cmtDialogConfirmMediaDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>