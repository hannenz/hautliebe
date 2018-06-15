<form id="cmtEditNewsletterForm" name="editNewsletter" action="{SELFURL}" method="POST" enctype="multipart/form-data">
{IF ({ISSET:errorMessage:VAR})}
	<div class="cmtMessage cmtMessageError">
		{IF ({ISSET:noAttachments:VAR})}<p>Es wurden keine Dateien zum Hochladen ausgew&auml;hlt.</p>{ENDIF}
		{IF ({ISSET:uploadError:VAR})}<p>Die Datei/en konnte/n nicht hochgeladen werden.</p>{ENDIF}
		{IF ({ISSET:maxAttachmentError:VAR})}<p>Die maximal mögliche Zahl der Anhänge ist erreicht.</p>{ENDIF}
		{IF ({ISSET:duplicateAttachment:VAR})}<p>Mindestens eine Datei wurde nicht hochgeladen, da sie bereits angehängt ist.</p>{ENDIF}
	</div>
{ENDIF}
{IF ({ISSET:successMessage:VAR})}
	<div class="cmtMessage cmtMessageSuccess">
		{IF ({ISSET:uploadSuccess:VAR})}<p>Die Datei/en wurde/n erfolgreich hochgeladen.</p>{ENDIF}
		{IF ({ISSET:attachmentDeleted:VAR})}<p>Die angeh&auml;ngte Datei wurde erfolgreich entfernt.</p>{ENDIF}
	</div>
{ENDIF}
<!-- Tabs: start -->
<div id="cmtTabs" class="cmtTabs ui-tabs clearfix">
	<ul class="" id="selectTabsHead">
		<li class="">
			<a href="#newsletterDataPanel">Newsletterdaten</a>
		</li>
		<li class="">
			<a href="#newsletterHTMLPanel">HTML-Inhalt</a>
		</li>
		<li class="">
			<a href="#newsletterTextPanel">Textinhalt</a>
		</li>
		<li class="">
			<a href="#newsletterAttachmentsPanel">Attachments ({VAR:attachmentsNr})</a>
		</li>
	</ul>
	<!-- Layer 1: Absender und Liste -->
	<div id="newsletterDataPanel" class="cmtPanel">
		<div class="serviceContainer">
			<div class="serviceContainerInner clearfix">
				<div class="serviceElementContainer">
					<div class="serviceText">Newsletter-Vorlage:</div>
					<div class="formField">
						<select id="newsletterTemplateID" name="newsletterTemplateID" class="cmtDialog cmtDialogConfirm" data-dialog-on-action="change" data-dialog-content-id="confirmChangeTemplateID" data-dialog-confirm-url="Javascript:cmtPaperboy.selectTemplate()">
							<option value="0">--- kein Template ausgewählt ---</option>
							{VAR:templateSelect}
						</select>
					</div>
				</div>
				<div class="serviceElementContainer">
					<div class="serviceText">Websiteseite versenden:</div>
					<div class="formField">
						<select name="newsletterPageID" id="newsletterPageID">
							<option value="">--- keine Seite versenden ---</option>
							{VAR:pageSelect}
						</select>
						<a id="refreshWebsitePage" href="Javascript:void(0);" class="cmt-font-icon cmt-icon-refresh" title="Seite neu in Editor laden"></a>
					</div>
				</div>
			</div>
		</div>
		<div class="serviceContainer">
			<div class="serviceContainerInner clearfix">
				<div class="serviceElementContainer">
					<div class="serviceText">Empf&auml;ngerliste:</div>
					<div class="formField">
						<select name="newsletterID" id="newsletterID">
							<option value="0">--- keine Liste ausgew&auml;hlt ---</option>
							{VAR:newsletterSelect}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="serviceContainer">
			<div class="serviceContainerInner clearfix">
				<div class="serviceElementContainer">
					<div class="serviceText">Absendername:</div>
					<div class="formField">
						<input type="text" id="newsletterSenderName" name="newsletterSenderName" value="{VAR:newsletterSenderName}">
					</div>
				</div>
	
				<div class="serviceElementContainer">
					<div class="serviceText">Absender-E-Mail:</div>
					<div class="formField">
						<input type="text" id="newsletterSenderEmail" name="newsletterSenderEmail" value="{VAR:newsletterSenderEmail}">
					</div>
				</div>
			</div>
		</div>
		<div class="serviceContainer">
			<div class="serviceContainerInner clearfix">
				<div class="serviceText">Betreff (Subject):</div>
				<div class="formField">
					<input type="text" id="newsletterSubject" name="newsletterSubject" value="{VAR:newsletterSubject}">
				</div>
			</div>
		</div>
		<div class="cmtLayer {IF ({ISSET:newsletterTo:VAR} || {ISSET:newsletterCC:VAR} || {ISSET:newsletterBCC:VAR})}cmtLayerOpen{ENDIF}">
			<div class="cmtLayerHandle">
<!-- 				<div class="cmt-layer-icon cmt-font-icon"></div> -->
				<div class="cmt-layer-icon cmt-layer-open-close cmt-font-icon cmt-icon-open-close"></div>
				<div class="cmtLayerTitle">An, CC, BCC (optional)</div>
			</div>
			<div class="cmtLayerContent">
				<div class="serviceContainer">
					<div class="serviceContainerInner clearfix">
						<p class="serviceText">
						Optional können die Empfängeradressen auch manuell eingegeben werden. In diesem Fall wird eine 
						ausgewählte Empfängerliste beim Versand ignoriert und stattdessen an die hier angegebenen Empfänger versandt.
						</p>
						<p class="serviceText">Die Empfängeradressen können in den nachfolgenden Feldern mit einer Zeilenschaltung oder einem Semikolon 
						getrennt eingegeben werden.
						</p>
						<div class="serviceText">An:</div>
						<div class="formField">
							<textarea id="newsletterTo" name="newsletterTo">{VAR:newsletterTo}</textarea>
							<p>
								<select name="newsletterToSelect" id="newsletterToSelect">
									<option value="0">--- keine Liste ausgew&auml;hlt ---</option>
									{VAR:newsletterListSelect}
								</select>
								<button id="newsletterToLoad" data-field="newsletterTo" type="button" class="cmtButton cmtButtonAdd">Liste einfügen</button>
								&nbsp;
								<button id="newsletterToEmpty" data-field="newsletterTo" type="button" class="cmtButton cmtButtonDelete">"An" leeren</button>
							</p>
						</div>
					</div>	
				</div>
				<div class="serviceContainer">
					<div class="serviceContainerInner clearfix">
						<div class="serviceText">CC:</div>
						<div class="formField">
							<textarea id="newsletterCC" name="newsletterCC">{VAR:newsletterCC}</textarea>
							<p>
								<select name="newsletterCCSelect" id="newsletterCCSelect">
									<option value="0">--- keine Liste ausgew&auml;hlt ---</option>
									{VAR:newsletterListSelect}
								</select>
								<button id="newsletterCCLoad" data-field="newsletterCC" type="button" class="cmtButton cmtButtonAdd">Liste einfügen</button>
								&nbsp;
								<button id="newsletterCCEmpty" data-field="newsletterCC" type="button" class="cmtButton cmtButtonDelete">"CC" leeren</button>
							</p>
						</div>
					</div>	
				</div>
				<div class="serviceContainer">
					<div class="serviceContainerInner clearfix">
						<div class="serviceText">BCC:</div>
						<div class="formField">
							<textarea id="newsletterBCC" name="newsletterBCC">{VAR:newsletterBCC}</textarea>
							<p>
								<select name="newsletterBCCSelect" id="newsletterBCCSelect">
									<option value="0">--- keine Liste ausgew&auml;hlt ---</option>
									{VAR:newsletterListSelect}
								</select>
								<button id="newsletterBCCLoad" data-field="newsletterBCC" type="button" class="cmtButton cmtButtonAdd">Liste einfügen</button>
								&nbsp;
								<button id="newsletterBCCEmpty" data-field="newsletterBCC" type="button" class="cmtButton cmtButtonDelete">"BCC" leeren</button>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- Layer 2: HTML-Editor -->
	<div  id="newsletterHTMLPanel" class="cmtPanel clearfix">
		<div class="serviceContainer">	
			<span class="serviceText">Bitte editieren Sie hier den HTML Quelltext:</span>
			<div>
				<textarea name="newsletterHTML" id="newsletterHTML">{VAR:newsletterHTML}</textarea>
			</div>
		</div>
	</div>
		
	<!-- Layer 3: Texteingabe -->
	<div  id="newsletterTextPanel" class="cmtPanel">
		<div class="serviceContainer">			
			<div class="formElementContainer">
				<div class="formLabel">Optional: Mailtext (wird angezeigt, wenn beim Empf&auml;nger HTML-Ansicht deaktiviert ist).</div>
				<div class="formField">
					<textarea name="newsletterText" id="newsletterText" cols="76">{VAR:newsletterText}</textarea>
				</div>
			</div>
		</div>
	</div>

	<!-- Layer 4: Attachments -->
	<div  id="newsletterAttachmentsPanel" class="cmtPanel">			
		<div class="serviceContainer">
			{VAR:contentAttachments}
			<p>
				<a href="{SELFURL}&amp;cmtTab={VAR:cmtTab}&amp;cmtAction=uploadAttachments" data-change-action="uploadAttachments">
					<button class="cmtButton cmtButtonUploadFile">ausgew&auml;hlte Dateien hochladen</button>
				</a>
			</p>
		</div>
	</div>
</div>
<input type="hidden" name="cmtAction" id="cmtAction" value="saveEditNewsletter">
<input type="hidden" name="cmtChangeAction" id="cmtChangeAction" value="">
<input type="hidden" name="cmtTab" id="cmtTab" value="{VAR:cmtTab}">
</form>

<div id="confirmChangeTemplateID" class="cmtDialogContentContainer" title="Vorlage wechseln">
	<div class="cmtDialogContent">
		Sind Sie sicher, dass Sie die Newsletter-Vorlage ändern möchten? Alle eingegebenen Daten gehen dabei verloren.
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonConfirm">&auml;ndern</span>
	</div>
</div>

<div id="confirmChangePageID" class="cmtDialogContentContainer" title="Seite wechseln">
	<div class="cmtDialogContent">
		Sind Sie sicher, dass Sie eine neue Seite ausw&auml;hlen m&ouml;chten? Alle eingegebenen Daten gehen dabei verloren.
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonConfirm">&auml;ndern</span>
	</div>
</div>

<!-- Dialog: Attachment deletion -->
<div id="newsletterDialogAttachmentDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		Sind Sie sicher, dass Sie die angeh&auml;ngte Datei <span class="cmtDialogVar"></span> entfernen wollen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">entfernen</span>
	</div>
</div>