		<div class="serviceContainer">
			<p class="serviceText">Der Test-Newsletter wird an folgende Empf&auml;nger verschickt:</p>
			<form id="newsletterTestRecipients" name="newsletterTestRecipients" action="{SELFURL}" method="POST">
				{IF ({ISSET:recipientsContent:VAR})}
				<ul id="testRecipientsList">
				{VAR:recipientsContent}
				</ul>{ELSE}
				<div class="cmtMessage cmtMessageWarning">In den Einstellungen sind keine Testempfängeradressen eingetragen.</div>
				{ENDIF}
				<p>
					<input class="cmtButton" type="submit" value="abschicken" />
					<input type="hidden" name="cmtAction" value="sendTestNewsletter" />
				</p>
			</form>
		</div>