<div class="cmtLayer {IF ("{VAR:layerStatus}" == "1")}cmtLayerOpen{ENDIF}">
	<div id="cmtLayerHandle_{VAR:guiElementCounter}" class="cmtLayerHandle {IF (!{ISSET:cmtLayerIcon:VAR})}cmtLayerHandleNoIcon{ENDIF}">
		<div class="cmt-layer-icon cmt-font-icon {VAR:cmtLayerIcon}"></div>
		<div class="cmt-layer-open-close cmt-font-icon cmt-icon-open-close"></div>
		{VAR:layerTitle}
	</div>
	<div id="cmtLayerContent_{VAR:guiElementCounter}" class="cmtLayerContent">
		{VAR:layerContent}
	</div>
</div>