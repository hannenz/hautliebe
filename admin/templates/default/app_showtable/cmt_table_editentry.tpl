<script type="text/javascript" src="javascript/ace/ace.js"></script>
<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_showtable/css/cmt_showtable_style.css" type="text/css" />
{VAR:cmtAddCode}
<form class="cmtForm" name="editEntryForm" id="cmtEditEntryForm" action="{SELFURL}" method="POST" enctype="{VAR:formEnctype}">

{IF (!{ISSET:cmtDialog})}
<div class="tableHeadlineContainer">
	<div class="tableIcon"><img src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}administration/img/default_table_icon_xlarge.png{ENDIF}" alt="" /></div>
	<h1>{VAR:tableTitle}</h1>
</div>
{ENDIF}

<!-- Meldung -->
{IF ({ISSET:userMessage:VAR})}{VAR:userMessage}{ENDIF}
<!-- Service -->
{VAR:serviceRow}
<!-- Eintrag -->
{VAR:content}
	<div class="serviceFooter">
	{VAR:serviceRow}
	</div>
	<input type="hidden" value="{VAR:action}" name="action" id="cmtAction">
	<input type="hidden" value="1" name="save">
	<input type="hidden" value="{VAR:cmt_dbtable}" name="cmt_dbtable">
	<input type="hidden" value="{VAR:cmt_pos}" name="cmt_pos">
	<input type="hidden" value="{VAR:cmt_ipp}" name="cmt_ipp">
	<input type="hidden" value="{VAR:launch}" name="launch">
	<input type="hidden" value="{VAR:prev_entry}" name="prev_entry">
	<input type="hidden" value="{VAR:prev_id}" name="prev_id">
	<input type="hidden" value="{VAR:next_entry}" name="next_entry">
	<input type="hidden" value="{VAR:next_id}" name="next_id">
	<input type="hidden" value="{VAR:entryID}" name="id[0]">
	<input type="hidden" value="{VAR:cmtDuplicatedID}" name="cmtDuplicatedID">
	<input type="hidden" value="{VAR:entry_nr}" name="entry_nr">
	<input type="hidden" value="{VAR:cmt_slider}" name="cmt_slider">
	<input type="hidden" value="{VAR:cmt_returnto}" name="cmt_returnto">
	<input type="hidden" value="{VAR:cmt_returnto_params}" name="cmt_returnto_params">
</form>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>