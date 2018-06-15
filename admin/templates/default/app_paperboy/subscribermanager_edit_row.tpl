<li>
	<input type="checkbox" id="newsletterID_{VAR:newsletterID}" name="newsletterID[{VAR:newsletterID}]" {IF ({ISSET:isSubscribed:VAR})} checked="checked"{ENDIF} />
	 <label for="newsletterID_{VAR:newsletterID}">{VAR:newsletterName}</label>
</li>