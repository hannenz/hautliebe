<div class="cmt-object {IF ({ISSET:cmt_visible})}cmt-object-visible{ELSE}cmt-object-not-visible{ENDIF} cmt-clearfix" data-cmt-object-id="{VAR:id}" data-cmt-object-template-id="{VAR:cmt_objecttemplate}">
	<div class="cmt-handle-bar" data-cmt-object-type-name="{VAR:cmtObjectTemplateName:htmlentities}" title="Objekt: {VAR:id} / {VAR:cmtObjectTemplateName:htmlentities}"><span class="cmt-flat-button cmt-button-visibility" data-cmt-tooltip="Sichtbarkeit"></span><span class="cmt-flat-button cmt-button-duplicate" data-cmt-tooltip="Objekt duplizieren"></span><span class="cmt-flat-button cmt-button-delete" data-cmt-tooltip="Objekt l&ouml;schen"></span><span class="cmt-flat-button cmt-button-settings" data-cmt-tooltip="Objekteigenschaften"></span><!-- <span class="cmt-object-id">{VAR:id}</span> --></div>
    <div class="cmt-context-menu">
        <div class="cmt-buttons-main">
            <span class="cmt-flat-button cmt-button-a" data-cmt-tooltip="Link einf&uuml;gen"></span>
            <span class="cmt-flat-button cmt-button-unlink" data-cmt-tooltip="Link entfernen"></span>
            <span class="cmt-flat-button cmt-button-remove-format" data-cmt-tooltip="Formatierung entfernen"></span>
        </div>
		<div class="cmt-buttons-image">
			<span class="cmt-flat-button cmt-button-image" data-cmt-tooltip="Bild einf&uuml;gen / bearbeiten"></span>
            <span class="cmt-flat-button cmt-button-image-a" data-cmt-tooltip="Link einf&uuml;gen"></span>
            <span class="cmt-flat-button cmt-button-image-unlink" data-cmt-tooltip="Link entfernen"></span>
            <span class="cmt-flat-button cmt-button-image-remove-format" data-cmt-tooltip="Originalbildgr&ouml;&szlig;e wiederherstellen"></span>
            <span class="cmt-flat-button cmt-button-image-remove" data-cmt-tooltip="Bild entfernen"></span>
		</div>
        <div class="cmt-buttons-chars">
            <span class="cmt-flat-button cmt-button-b" data-cmt-tooltip="fett"></span>
            <span class="cmt-flat-button cmt-button-i" data-cmt-tooltip="kursiv"></span>
            <span class="cmt-flat-button cmt-button-u" data-cmt-tooltip="unterstrichen"></span>
            <span class="cmt-flat-button cmt-button-strike" data-cmt-tooltip="durchgestrichen"></span>
        </div>
        <div class="cmt-buttons-block">
<!--             <span class="cmt-flat-button cmt-button-p" data-cmt-tooltip="Absatz"></span> -->
            <span class="cmt-flat-button cmt-button-ul" data-cmt-tooltip="Liste"></span>
            <span class="cmt-flat-button cmt-button-ol" data-cmt-tooltip="nummerierte Liste"></span>
        </div>
    </div>
    <div class="cmt-object-content-wrapper cmt-clearfix">
    	{VAR:objectContent}
    </div>
</div>