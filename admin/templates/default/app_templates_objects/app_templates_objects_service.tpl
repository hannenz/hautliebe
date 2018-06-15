<div class="cmtLayer {IF ({ISSET:cmtShowTemplates:VAR})}cmtLayerActive{ENDIF}">
	<div class="cmtLayerHandle" id="cmtLayerSearchAndSort">
		<div class="cmt-layer-icon cmt-font-icon cmt-icon-filter"></div>
		<div class="cmt-layer-open-close cmt-font-icon cmt-icon-open-close"></div>
		Filtern
	</div>
	<div class="cmtLayerContent">
		<form name="selectTableForm" action="{SELFURL}" method="post">
			<div class="serviceContainer">
				<span class="serviceText">Zeige Vorlagen</span>&nbsp;
				<select name="cmtShowTemplates">
					<option value="" {IF (!{ISSET:cmtShowTemplates:VAR})}selected="selected"{ENDIF}>alle</option>
					<option value="used" {IF ("{VAR:cmtShowTemplates}" == "used")}selected="selected"{ENDIF}>nur genutzte</option>
					<option value="unused" {IF ("{VAR:cmtShowTemplates}" == "unused")}selected="selected"{ENDIF}>nur ungenutzte</option>
				</select>
				&nbsp;<button class="cmtButton" type="submit">anzeigen</button>
			</div>
		</form>
	</div>
</div>