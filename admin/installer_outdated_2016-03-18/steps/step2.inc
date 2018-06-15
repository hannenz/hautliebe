<form name="formStep2" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<h1>Datenbankzugang</h1>
Bitte geben Sie hier Ihre Datenbankzugangsdaten an.
<h2>Server</h2>
<input type="text" name="server" value="<?php echo $server; ?>">
<div class="inputHelp">URL des Datenbankservers (z.B. "db.meinedomain.de")</div>
<h2>Name der Datenbank</h2>
<input type="text" name="db" value="<?php echo $db; ?>">
<div class="inputHelp">(z.B. "contentomat")</div>
<h2>Benutzername</h2>
<input type="text" name="user" value="<?php echo $user; ?>">
<div class="inputHelp">Name des Datenbankbenutzers (z.B. "customer1")</div>
<h2>Passwort</h2>
<input type="text" name="pw" value="<?php echo $pw; ?>">
<div class="inputHelp">Passwort des Datenbankbenutzers</div>

<div id="buttons"><input type="image" src="/installer/img/nextstep.gif" alt="n&auml;chster Schritt"></div>
<input type="hidden" name="step" value="3">
<input type="hidden" name="webroot" value="<?php echo $webroot; ?>">
<input type="hidden" name="adminpath" value="<?php echo $adminpath; ?>">
</form>