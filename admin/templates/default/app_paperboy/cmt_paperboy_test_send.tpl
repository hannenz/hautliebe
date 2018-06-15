{IF ({ISSET:failedRecipients:VAR})}
<div class="cmtMessage cmtMessageError">
	<p>Der Testversand ist an {VAR:failedRecipients} Adresse(n) leider gescheitert.</p>
</div>
{ENDIF}
{IF ({ISSET:succeededRecipients:VAR})}
<div class="cmtMessage cmtMessageSuccess">
	<p>Der Testversand war an {VAR:succeededRecipients} Adresse(n)  erfolgreich.</p>
</div>
{ENDIF}