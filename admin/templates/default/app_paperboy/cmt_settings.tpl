<form name="editSettings" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
	
	<div class="cmtTabs">
		<ul>
			<li><a href="#cmtSettingsTab1"><span>Einstellungen</span></a></li>
			<li><a href="#cmtSettingsTab2"><span>HTML-Email</span></a></li>
			<li><a href="#cmtSettingsTab3"><span>Attachments</span></a></li>
			<li><a href="#cmtSettingsTab4"><span>SMTP-Versand</span></a></li>
		</ul>

		<!-- Tab 1 -->
		<div id="cmtSettingsTab1">
		
	<!-- 			<div class="editEntrySubheadline">Allgemein</div> -->
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Empfänger-Emailadresse für Testmails</div>
				<div class="cmtEditRowField">
					<textarea name="cmtsetting_newsletterTestrecipient" cols="60" rows="5">{VAR:newsletterTestrecipient}</textarea>
				</div>
				<div class="cmtEditRowDescription">
					Tragen Sie hier die Empfängeradressen für den Testversand ein. Mehrere Adressen können per Zeilenumbruch oder Semikolon getrennt werden. 
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Versandintervall</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_sendInSteps" value="{VAR:sendInSteps}" size="4">
				</div>
				<div class="cmtEditRowDescription">
					Diese Zahl regelt die Anzahl der pro Versanddurchlauf verschickten Mails. Je höher der Wert, umso schneller läuft der Versand.<br />
					Bitte bedenken Sie aber, dass eine zu große Anzahl auf einmal versandter Mails zu einem Server-Timeout (i.d.R. 30 Sekunden) und einem Abbruch des Versands führen können.
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Mailserver: &quot;Bekannter Benutzer&quot;</div>
				<div class="cmtEditRowField">
					<input id="cmtsetting_knownUserUseOption" name="cmtsetting_knownUserUseOption" type="checkbox" value="1" {IF ({ISSET:knownUserUseOption:VAR})}checked="checked"{ENDIF}/>
					<label for="cmtsetting_knownUserUseOption">-f Option beim Versenden nutzen</label>
					<input name="cmtsetting_knownUserEmail" type="text" value="{VAR:knownUserEmail}" size="40" />
				</div>
				<div class="cmtEditRowDescription">
					Manche Mailserver verlangen aus Sicherheitsgründen, dass PHP Mails mit der &quot;-f&quot;-Option und der E-Mailadresse eines dazugehörigen &quot;bekannten Benutzers&quot; 
					versendet werden.<br />
					Sollte beim Versenden der (Test-)Mails sofort ein Fehler auftreten, aktivieren Sie diese Option und geben eine Mailadresse an, die als Postfach auf diesem Server existiert 
					(z.B. info@meinserver.de).
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Website-Seite versenden: Include-Datei</div>
				<div class="cmtEditRowField">
					<input type="text" name="cmtsetting_includeFile" value="{VAR:includeFile}" />
					<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmtsetting_includeFile&amp;cmtOnlyDir=false" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmtsetting_includeFile">ausw&auml;hlen</a>
				</div>
				<div class="cmtEditRowDescription">
					Die hier ausgewählte Datei wird vor dem Import einer Websiteseite in den HTML- und den TextEditor ausgeführt. Der Inhalt der Websiteseite wird in der bearbeitbaren Variable $mailContent zur Verfügung gestellt.
				</div>
			</div>
		</div>
		<!-- Tab 2 -->
		<div id="cmtSettingsTab2">
	<!-- 			<div class="editEntrySubheadline">HTML-E-Mails</div> -->
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">HTML: Bilder mitsenden</div>
				<div class="cmtEditRowField">
					<input id="cmtsetting_embedHTMLImages" name="cmtsetting_embedHTMLImages" type="checkbox" value="1" {IF ({ISSET:embedHTMLImages:VAR})}checked="checked"{ENDIF}/>
					<label for="cmtsetting_embedHTMLImages">Bilder aus HTML-Teil mitsenden.</label>
				</div>
				<div class="cmtEditRowDescription">
					Bilder im HTML-Teil der E-Mail können mitgesendet werden (größere E-Mail) oder lediglich in der E-Mail verlinkt werden (kleinere E-Mail, aber ggf. keine Darstellung der Bilder )
					im E-Mailprogramm des Empfängers, weil externe Grafiken aus Sicherheitsgründen geblockt werden).
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">HTML: Externer Bilderpfad</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_remoteImagePath" type="text" value="{VAR:remoteImagePath}" />
				</div>
				<div class="cmtEditRowDescription">
					Hier muss eine URL angegeben werden (z.B. <pre>http://www.mydomain.de/</pre>), wenn die Option "Bilder im HTML-Teil der E-Mail mitsenden" nicht ausgewählt ist, 
					wenn also die Grafiken im HTML-Teil der E-Mail von einem externen Server nachgeladen werden sollen. Ist hier kein Pfad angegeben, versucht das System die URL des Servers zu ermitteln.
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Grafikverzeichnis für HTML-Editor</div>
				<div class="cmtEditRowField">
					<input type="text"  name="cmtsetting_imageDir" value="{VAR:imageDir}" />
					<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmtsetting_imageDir&amp;cmtOnlyDir=true" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmtsetting_imageDir">ausw&auml;hlen</a>
				</div>
				<div class="cmtEditRowDescription">
					Hier kann das Grafikverzeichnis für den HTML-Editor angegeben werden.
				</div>
			</div>
		</div>
		
	
		<!-- Tab 3 -->
		<div id="cmtSettingsTab3">
		
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Upload-Verzeichnis für Attachments</div>
				<div class="cmtEditRowField">
					<input type="text"  name="cmtsetting_uploadDirectory" value="{VAR:cmtsetting_uploadDirectory}" />
					<a href="{SELFURL}&amp;cmt_extapp=fileselector&amp;cmt_field=cmtsetting_uploadDirectory&amp;cmtOnlyDir=true" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" data-field="cmtsetting_uploadDirectory">ausw&auml;hlen</a>
				</div>
				<div class="cmtEditRowDescription">
					Hier kann das Upload-Verzeichnis für Attachments angegeben werden (i.d.R. ein temporäres Verzeichnis "/temp").
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Anzahl Dateinanhänge</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_maxAttachments" value="{VAR:maxAttachments}" size="4">
				</div>
				<div class="cmtEditRowDescription">
					Bestimmt die maximale Anzahl von angehängten Dateien, die der User mit einem Newslettter verschicken kann.
				</div>
			</div>
		
		</div>
	
		<!-- Tab 4 -->
		<div id="cmtSettingsTab4">
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Mails per SMTP versenden</div>
				<div class="cmtEditRowField">
					<input id="cmtsetting_transport" name="cmtsetting_transport" type="checkbox" value="smtp" {IF ("{VAR:transport}" == "smtp")}checked="checked"{ENDIF} />
					<label for="cmtsetting_transport">Mails sollen per SMTP versendet werden.</label>
				</div>
				<div class="cmtEditRowDescription">
					Paperboy kann die E-Mails mittels der PHP-Funktion mail() verschicken oder über den Mailserver eines E-Mailkontos. Letztere Methode ist sicherer und performanter, da 
					viele Provider den E-Mailversand per PHP mail() einschränken und es so zu Versandfehlern kommen kann!
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">SMTP-Server</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_smtpHost" type="text" value="{VAR:smtpHost}" />
				</div>
				<div class="cmtEditRowDescription">
					Name des Postausgangservers, z.B. mailout.meinserver.de
				</div>
			</div>
	
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">SMTP-Benutzername</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_smtpUsername" type="text" value="{VAR:smtpUsername}" />
				</div>
				<div class="cmtEditRowDescription">
					Benutzer-, Konto-, bzw. Postfachname für den Postausgangserver.
				</div>
			</div>
			
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">SMTP-Passwort</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_smtpPassword" type="text" value="{VAR:smtpPassword}" />
				</div>
				<div class="cmtEditRowDescription">
					Passwort für das Konto auf dem Postausgangservers.
				</div>
			</div>
			
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Verschlüsselung</div>
				<div class="cmtEditRowField">
					<select name="cmtsetting_smtpSecure">
						<option value="tls" {IF ("{VAR:smtpSecure}" == "tls")}selected="selected"{ENDIF}>STARTTLS</option>
						<option value="ssl" {IF ("{VAR:smtpSecure}" == "ssl")}selected="selected"{ENDIF}>SSL</option>
					</select>
				</div>
				<div class="cmtEditRowDescription">
					Verschlüsselungsverfahren des Postausgangsservers. TLS/SSL sollte vorgezogen werden.
				</div>
			</div>
			
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="cmtEditRowHead">Portnummer</div>
				<div class="cmtEditRowField">
					<input name="cmtsetting_smtpPort" type="text" value="{VAR:smtpPort}" size="4" />
				</div>
				<div class="cmtEditRowDescription">
					Nummer des verwendeten Ports (i.d.R. 465 für SSL, 587 für STARTTLS).
				</div>
			</div>
		</div>
	</div>
	<div class="serviceFooter serviceContainer">
		
		<!-- outdate setting "errorMode" -->
		<input type="hidden" name="cmtsetting_errorMode" value="robust"/>
	
		<button type="submit" class="cmtButton cmtButtonSave">speichern</button>
		<input type="hidden" name="sid" value="{SID}"/>
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
		<input type="hidden" name="id" value="{VAR:tableId}"/>
		<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
		<input type="hidden" name="action" value="save"/>
	</div>
</form>