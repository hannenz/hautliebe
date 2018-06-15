<div class="cmtExtendedAccordionElement{IF ({ISSET:groupSelected:VAR})} cmtExtendedAccordionElementIsOpen{ENDIF}">
	<h4 class="cmtExtendedAccordionHandle">
		<span class="cmtExtendedAccordionIcon" style="background-image: url('{VAR:groupIcon}')"></span>
		<span class="cmtExtendedAccordionTitle">{VAR:groupName}</span>
		<span class="cmtExtendedAccordionPin cmt-font-icon cmt-icon-pin" id="layerPin_{VAR:groupId}"></span>
		<span class="cmtExtendedAccordionStatus cmt-font-icon cmt-icon-open-close" id="layerAction_{VAR:groupId}"></span>
	</h4>
	<ul class="cmtExtendedAccordionContent">
		{VAR:groupHtml}
	</ul>
</div>