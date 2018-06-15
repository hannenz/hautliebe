<form method="post" action="#">
<div id="cmt_relation_searchBar">
	<input type="text" name="searchText" value="{POSTVAR:searchText}">
	{VAR:dropDownField}
	<input type="submit" name="searchform" value="suchen">
</div>

<div id="cmt_relation_Container" class="clearfix">
	<div id="cmt_relation_results">
		{VAR:searchContentResults}
	</div>
	
	<div id="cmt_relation_actions">
		<br/><br/><br/>
		<input type="submit" name="select" value="&gt;&gt;" />
		<br/><br/><br/>
		<input type="submit" name="deselect" value="&lt;&lt;" />
	</div>
	
	<div id="cmt_relation_selected">
		{VAR:searchContentSelectedItems}
	</div>
</div>

<div id="cmt_relation_confirm">
	<input type="submit" name="submitForm" value="[X] schliessen" onClick="windowClose()">
	<input type="submit" name="submitForm" value="&uuml;bernehmen >>" onClick="updateOpener('links')">
</div>
</form>