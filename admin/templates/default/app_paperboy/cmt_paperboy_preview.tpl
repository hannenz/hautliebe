	{IF ({ISSET:newsletterHTML:VAR})}
	<div class="serviceContainer">
		<div class="serviceText">HTML-Vorschau</div>
		<iframe id="previewHTMLContainer" src="{SELFURL}&amp;cmtAction=previewNewsletterHTML" width="100%" height="480rem">
			Die HTML-Vorschau wird in einem <i>iframe</i> anzeigt
		</iframe>
	</div>
	{ENDIF}
	{IF ({ISSET:newsletterText})}
	<div class="serviceContainer">
		<div class="serviceText">Text-Vorschau</div>		
		<div id="previewTextContainer">
			{VAR:newsletterText}
		</div>
	</div>
	{ENDIF}
