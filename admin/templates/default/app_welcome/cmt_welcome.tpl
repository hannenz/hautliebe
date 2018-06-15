<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_welcome/css/cmt_welcome_style.css" type="text/css">
<script src="{CMT_TEMPLATE}/app_welcome/javascript/cmt_welcome.js"></script>

<div class="appHeadlineContainer">
	<div class="appIcon"><img src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_welcome/img/app_welcome_icon.png{ENDIF}" alt="" /></div>
	<h1>Willkommen</h1>
</div>
<section class="cmt-teaser">
	<p><b>Hallo {VAR:useralias}</b></p>
	<p>Das System ben&ouml;tigt einen aktuellen Browser wie Mozilla Firefox, Google Chrome, Opera, Safari oder Microsoft Edge. Bitte beachten Sie, dass sich &Auml;nderungen im Layout-Modus oder an den in der Datenbank gespeicherten Daten, direkt auch auf den
	&ouml;ffentlich zug&auml;nglichen Seiten bemerkbar machen k&ouml;nnen.
	</p>
</section>
<section class="cmt-dashboard">

	<!-- user -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--user">Benutzer</h3>
		<div class="cmt-dashboard-item__content">
			<dl>
				<dt>ID</dt>
				<dd>{CONSTANT:CMT_USERID}</dd>
				<dt>Username</dt>
				<dd>{VAR:cmtUserName}</dd>
				<dt>Gruppe</dt>
				<dd>{VAR:cmtUserGroup}</dd>
				<dt>Letzter Login</dt>
				<dd>{VAR:cmtUserLastLogin} Uhr</dd>
			</dl>
		</div>
	</article>

	<!-- password -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--password">Passwort</h3>
		<div class="cmt-dashboard-item__content">
			{IF ({ISSET:cmtUserPasswordError:VAR})}
			<p class="error">Sie haben Ihr Passwort sehr lange nicht mehr oder noch nie geändert!</p>
			{ENDIF}
			{IF ({ISSET:cmtUserPasswordWarning:VAR})}
			<p class="warning">
				Sie nutzen seit <em>{VAR:cmtUserLastPasswordChangeDays} Tagen</em> das selbe Passwort! 
			</p>
			{ENDIF}
			<dl>
				<dt>Zuletzt geändert</dt>
				<dd>{VAR:cmtUserLastPasswordChange}</dd>
				<dt>Passwortalter</dt>
				<dd>{VAR:cmtUserLastPasswordChangeDays} Tage</dd>
				<dt>Passwort</dt>
				<dd>
					<a href="{SELFURL}&amp;cmtAction=newPassword" class="cmtDialog cmtDialogLoadContent" title="Passwort ändern" data-width="560px" data-dialog-title="Paswort ändern">jetzt ändern</a>
				</dd>
			</dl>
			
		</div>
	</article>
	
	<!-- CMT -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--cmt">Content-o-mat</h3>
		<div class="cmt-dashboard-item__content">
			<dl>
				<dt>Version</dt>
				<dd>{VAR:version}</dd>
				<dt>Build</dt>
				<dd>{VAR:buildDay}.{VAR:buildMonth}.{VAR:buildYear}</dd>
			</dl>
		</div>
	</article>

{IF ("{VAR:cmtUserType}" == "admin")}
	<!-- database -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--db">Datenbank</h3>
		<div class="cmt-dashboard-item__content">
			<dl>
				<dt>MySQL Version</dt>
				<dd>{VAR:mysqlVersion}</dd>
				<dt>Server</dt>
				<dd>{VAR:cmtDatabaseServerName}</dd>
				<dt>Datenbank</dt>
				<dd>{VAR:cmtDatabaseName}</dd>
				<dt>Benutzer</dt>
				<dd>{VAR:cmtDatabaseUserName}</dd>
			</dl>
		</div>
	</article>
	
	<!-- system -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--system">System</h3>
		<div class="cmt-dashboard-item__content">
			<dl>
				<dt>Server IP:Port</dt>
				<dd>{VAR:serverIp}:{VAR:serverPort}</dd>
				<dt>Root-Pfad</dt>
				<dd>{VAR:documentRoot}</dd>
			</dl>
		</div>
	</article>	

	<!-- php -->
	<article class="cmt-dashboard-item">
		<h3 class="cmt-dashboard-item__head head--php">PHP</h3>
		<div class="cmt-dashboard-item__content">
			<dl>
				<dt>PHP Version</dt>
				<dd>{VAR:phpVersion}</dd>
				<dt>PHP-Extensions</dt>
				<dd>
				<a href="{SELFURL}&amp;cmtAction=showPhpExtensions" class="cmtDialog cmtDialogLoadContent" title="Zeige Extensions" data-width="70%" data-dialog-title="Installierte PHP-Exensions">zeige Extensions</a>
				</dd>
				<dt>PHP-Info</dt>
				<dd>
				<a href="{SELFURL}&amp;cmtAction=phpInfo" class="cmtDialog cmtDialogLoadContent" title="Zeige PHP-Info" data-width="90%" data-dialog-title="Installierte PHP-Exensions">zeige PHP-Info</a>
				</dd>
			</dl>
		</div>
	</article>	
	{ENDIF} 			
</section>
