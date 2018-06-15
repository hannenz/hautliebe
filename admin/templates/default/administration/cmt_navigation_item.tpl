<li class="cmtExtendedAccordionItem{IF ({ISSET:itemSelected:VAR})} cmtSelected{ENDIF}">
	<a href="{VAR:appLauncher}launch={VAR:itemId}{IF ({ISSET:queryVars:VAR})}&amp;{VAR:queryVars}{ENDIF}" style="background-image: url('{VAR:itemIcon}')">{VAR:itemName}</a>
</li>