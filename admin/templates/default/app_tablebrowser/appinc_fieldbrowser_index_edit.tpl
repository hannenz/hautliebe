<link rel="Stylesheet" href="{CMT_TEMPLATE}app_tablebrowser/css/style.css" type="text/css" />
<form name="editIndexForm" id="editIndexForm" method="post" action="{SELFURL}">
	<div class="tableHeadlineContainer">
		<div id="tableIcon"><img src="{CMT_TEMPLATE}/app_tablebrowser/img/icon_index.png" alt="" /></div>
		<div class="tableHeadline">{IF ("{VAR:action}" == "newIndex")}<span class="tableHeadlineAddText">Index hinzuf&uuml;gen</span>{ELSE}{VAR:cmtIndexName}<span class="tableHeadlineAddText"> - Index bearbeiten</span>{ENDIF}</div>
	</div>
	<div class="tableHeadlineDropshadow"></div>
	<!-- Meldung -->
	{IF ({ISSET:userMessage:VAR})}<div class="{VAR:userMessageType}">{VAR:userMessage}</div>{ENDIF}
	<!-- Service -->
	<div class="serviceContainerButtonRow">
		<a href="{SELFURL}"><img src="{CMT_TEMPLATE}app_showtable/img/icon_back_24px.png" class="imageLinked" alt="zur&uuml;ck" /></a>
		<input src="{CMT_TEMPLATE}app_showtable/img/icon_save_24px.png" name="saveEntry" value="speichern" type="image" />
	</div>
	<div class="serviceContainer">
		<div class="serviceContainerInner">
			<div class="serviceElementContainer">
				<span class="serviceText">Index-Typ:&nbsp;</span>
				<select name="cmtIndexType" id="selectIndexType" size="1">
					<option value="index"{IF ("{VAR:cmtIndexType}" == "index")} selected="selected"{ENDIF}>Index</option>
					<option value="fulltext"{IF ("{VAR:cmtIndexType}" == "fulltext")} selected="selected"{ENDIF}>Volltext</option>
				</select>
				&nbsp;<input type="submit" value="&auml;ndern" name="changeIndexType" />
			</div>
			<div class="serviceElementContainer">
				<span class="serviceText">Index-Name:&nbsp;</span>
				<input type="text" value="{VAR:cmtIndexName}" name="cmtIndexName" />
			</div>
		</div>
	</div>
	<!-- Felderliste -->
	<div class="serviceContainer">
		<p class="serviceText">Um ein oder mehrere Felder eines Indexes zu bearbeiten, ziehen Sie die Felder aus der linken Spalte in den rechten Kasten &quot;Felder im Index&quot;.
		Hier k&ouml;nnen Sie auch die Reihenfolge der Felder im Index &auml;ndern. Feldtypen, die den ausgew&auml;hlten Indextyp nicht unterst&uuml;tzen, werden <strong>nicht zur Auswahl angezeigt!</strong></p>
		<p class="serviceText">Optional kann bei einem zum Index hinzugef&uuml;gten Feld eine Gr&ouml&szlig;e in Byte angegeben werden. <b>ACHTUNG: </b>Bei Tabellen im UTF-8 Format wird 
		der eingegebene Wert (Anzahl der Zeichen) von der Datenbak automatisch auf den 2- bis 3-fachen Wert multipliziert, da UTF-8 mehrere Bytes zur Darstellung eines Zeichens ben&ouml;tigt.</p>
		<div id="selectTableFieldsContainer" class="selectTableFieldsContainer">
			<h1>Verf&uuml;gbare Felder</h1>
			{VAR:contentTableFields}
		</div>
		<!-- Felderziel / Index -->
		<div id="selectTableFieldsTargetContainer" class="selectTableFieldsTargetContainer">
			<h1>Felder im Index</h1>
			{VAR:contentIndexFields}
		</div>
		<div class="clear"> </div>
	</div>
	<!-- Service -->
	<div class="serviceContainerButtonRow">
		<a href="{SELFURL}"><img src="{CMT_TEMPLATE}app_showtable/img/icon_back_24px.png" class="imageLinked" alt="zur&uuml;ck" /></a>
		<input src="{CMT_TEMPLATE}app_showtable/img/icon_save_24px.png" name="saveEntry" value="speichern" type="image" />
		
		<!-- Variablen in versteckten Feldern -->
		<input type="hidden" name="cmtIndexOldName" value="{VAR:cmtIndexName}" />
		<input type="hidden" name="cmtIndexOldType" value="{VAR:cmtIndexType}" />
		<input type="hidden" name="action" value="{VAR:action}" />
		<input type="hidden" name="save" value="1" />
	</div>
	<div class="serviceContainerFooterDropshadow"></div>
</form>
<script type="text/javascript">

	// Sortables erstellen
	Sortable.create("selectTableFieldsContainer", {tag:'div', ghosting:true, dropOnEmpty:true, containment:["selectTableFieldsContainer","selectTableFieldsTargetContainer"],constraint:false});
	Sortable.create("selectTableFieldsTargetContainer", {tag:'div', ghosting:true, dropOnEmpty:true, containment:["selectTableFieldsContainer","selectTableFieldsTargetContainer"],constraint:false});

	// Seite neu laden, wenn Auswahlliste "Indextyp2 geändert wird
	$('selectIndexType').observe('change', function(ev) { window.location = "{SELFURL}&action={VAR:action}&cmtIndexName={VAR:cmtIndexName}&cmtIndexType="+ev.target.value;});
	
	// Feldreihenfolge in Index auslesen, wenn Index gespeichert wird.
	$('editIndexForm').observe('submit', function(ev) {
		
		fieldContainers = $('selectTableFieldsTargetContainer').childElements('div');
		for (i = 0; i < fieldContainers.length; i++) {
			if (fieldContainers[i].tagName.toLowerCase() == 'div') {
				formField = new Element('input', {type: 'hidden', name: 'cmtIndexField[]', value: fieldContainers[i].id});
				$('editIndexForm').insert(formField);
			}
		}
		return false;
	});
	
</script>