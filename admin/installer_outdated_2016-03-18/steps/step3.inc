<form name="formStep4" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<h1>Datenbankstruktur erstellen</h1>
Zum Abschluss der Installation wird die Datenbankstruktur (Tabellen, Felder) erstellt. Bitte klicken Sie 
zum Starten des letzten Schrittes auf den gr&uuml;nen Pfeil. 
<input type="hidden" name="step" value="4">
<input type="hidden" name="webroot" value="<?php echo $webroot; ?>">
<input type="hidden" name="adminpath" value="<?php echo $adminpath; ?>">
<div id="buttons"><input type="image" src="/installer/img/nextstep.gif" alt="n&auml;chster Schritt"></div>
</form>