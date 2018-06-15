{IF ({ISSET:mlogAction:GETVARS})}
	<div class="serviceContainerButtonRow">
		<a href="{SELFURL}"><img title="zurück" alt="zurück" class="imageLinked" src="templates/default/app_showtable/img/icon_back_24px.png"></a>
		<input type="image" value="speichern" id="saveEntry" name="saveEntry" src="templates/default/app_showtable/img/icon_save_24px.png">
		<img src="templates/default/general/img/service_separator_24px.gif" class="serviceSeparator">
		<a href="{SELFURL}&amp;mlogAction=deletePost&amp;postID={VAR:postID}"><img title="löschen und zurück" alt="löschen und zurück" onclick="return confirmDeletion('Wollen Sie diesen Eintrag wirklich löschen?')" class="imageLinked" src="templates/default/app_showtable/img/icon_delete_24px.png"></a>
	</div>
{ELSE}
	<div class="cmtServiceContainer clearfix">
		<div class="newEntryButton">
		<a href="{SELFURL}&amp;mlogAction=newPost">
			<img alt="Neuer Eintrag" title="Neuer Eintrag" class="newEntryButtonImage" src="templates/default/app_showtable/img/icon_new_24px.png">&nbsp;Neuer Eintrag
		</a>
		</div>
	</div>
{ENDIF}