		{IF (!{ISSET:totalNewsletters:VAR})}
		<div class="cmtMessage cmtMessageInfo">
			Bitte wählen Sie zuerst einen Newsletter aus oder geben Sie eine Empfängeradresse an: <a href="{SELFURL}&amp;action=editNewsletter">Newsletter auswählen</a>
		</div>
		{ELSE}
		{IF ({ISSET:cc:VAR})}
		<div class="cmtMessage cmtMessageWarning">
			Sie möchten die E-Mail an Adressen in CC versenden. Alle Empfänger sehen automatisch auch die E-Mailadressen der anderen Empfänger. Möchten Sie dies nicht, 
			fügen Sie die CC-Adressen ins das BCC ein.   
		</div>
		{ENDIF}
		<div class="serviceContainer">
			<p class="serviceText">
				Bitte klicken Sie auf nachfolgenden Link, um den Newsletterversand zu starten. Es erfolgt keine weitere Abfrage, ob der Versand wirklich gestartet werden soll.
			</p>
			<p>
				<a href="#" id="startDelivery" class="cmtButton cmtButtonSend">Versand starten</a>
			</p>
			<table id="deliveryData" summary="" >
				<tr>
{IF ({ISSET:newsletter_name:VAR})}					<td class="dataName">Newsletter:</td>
					<td class="dataValue" id="newsletterName">{VAR:newsletter_name}</td>
				</tr>{ENDIF}
				<tr>
					<td class="dataName">Empf&auml;nger gesamt:</td>
					<td class="dataValue" id="totalNewsletters">{VAR:totalNewsletters}</td>
				</tr>
{IF ({ISSET:cc:VAR})}				<tr>
					<td class="dataName">CC Empf&auml;nger:</td>
					<td class="dataValue" id="cc">{VAR:cc}</td>
				</tr>{ENDIF}
{IF ({ISSET:bcc:VAR})}				<tr>
					<td class="dataName">BCC Empf&auml;nger:</td>
					<td class="dataValue" id="bcc">{VAR:bcc}</td>
				</tr>{ENDIF}
				<tr>
					<td class="dataName">Versendete Newsletter:</td>
					<td class="dataValue" id="newslettersSent">0</td>
				</tr>
				<tr>
					<td class="dataName">Versandfehler:</td>
					<td class="dataValue" id="newsletterErrors">0</td>
				</tr>
				<tr>
{IF ("{VAR:deliveryInterval}" > "1")}					<td class="dataName">Versandinterval:</td>
					<td class="dataValue" id="deliveryInterval">{VAR:deliveryInterval}</td>
				</tr>{ENDIF}
			</table>
			<div class="serviceText">Fortschritt:</div>		
			<div id="progressBar" class="cmtProgressbar" data-label-value="true" data-label-value-entity="%">
				<div class="cmtLabel"></div>
			</div>
			{ENDIF}
		</div>
		<script type="text/javascript">
			cmtPaperboy.setVar('deliveryTimeOut', '{VAR:maxExecutionTime}');
		</script>