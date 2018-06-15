<div class="serviceContainer">
	<form name="selectSettings" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
		<span class="serviceText">Einstellungen f&uuml;r</span>&nbsp;
		{VAR:selectTable}
		&nbsp;&nbsp;
		<button class="cmtButton cmtButtonEdit" type="submit">bearbeiten</button>
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="sid" value="{SID}" />
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}" />
	</form>
</div>