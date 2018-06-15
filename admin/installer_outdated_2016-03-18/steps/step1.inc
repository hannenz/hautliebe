Willkommen zur Installation des content-o-mat. Bitte geben Sie die Pfade zu den genannten Verzeichnissen auf dem Webserver und weitere, allgemeine Einstellungen ein.
<br />&nbsp;<br />
<form name="formStep1" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<h1>Verzeichnisse</h1>
Sollten die Verzeichnisse, die hier angegeben werden noch nicht existieren, dann legen Sie sie bitte mit einem FTP-Programm an. Die Zugriffsrechte auf die Ordner m&uuml;ssen entsprechend gesetzt werden.
<h2>Webroot</h2>
<input type="text" name="webroot" value="<?php echo $webroot; ?>" />
<div class="inputHelp">&Uuml;blicherweise entspricht das Rootverzeichnis der Website dem Rootverzeichnis des Webservers ("/")!</div>
<h2>Pfad zum Administrationsbereich / den Systemdateien</h2>
<input type="text" name="adminpath" value="<?php echo $adminpath; ?>" />
<div class="inputHelp">Hier muss der Pfad des Verzeichnisses eingetragen werden, in welchem die content-o-mat-Dateien liegen.<br>
Die Pfandangabe muss relativ zum Rootverzeichnis eingetragen werden! (Standard ist "admin/");</div>
<h2>Pfad zum zentralen Downloadordner</h2>
<input type="text" name="downloadpath" value="<?php echo $downloadpath; ?>" />
<div class="inputHelp">Pfad relativ zum root-Verzeichnis, in welchem zentrale Downloaddateien liegen (Standard ist "downloads/").<br />
Die Pfandangabe muss relativ zum Rootverzeichnis eingetragen werden!</div>
<h2>Serverpfad für Skript-Includes</h2>
<input type="text" name="cmt_includepath" value="<?php echo $cmt_includepath; ?>" />
<div class="inputHelp">Serverpfad des Webspaces. Wird zum Einbinden von PHP-Skripten genutzt und entspricht in der Regel dem Umgebungswert $_SERVER['DOCUMENT_ROOT']!</div>
<div class="categorySpacer"> </div>

<h1>Website-Einstellungen</h1>
<h2>Website Name</h2>
<input type="text" name="webname" value="<?php echo $webname; ?>" />
<div class="inputHelp">Der Name der Website wird im Administraionsbereich angezeigt (z.B. "Firmenname Web", "Peters Homepage"). 
Bitte verwenden Sie f&uuml;r das <b>'</b> Zeichen die HTML-Entit&auml;t <b>&amp;apos;</b></div>
<h2>Website Sprachk&uuml;rzel</h2>
<input type="text" name="defaultlanguage" value="<?php echo $defaultlanguage; ?>" />
<div class="inputHelp">Internes K&uuml;rzel f&uuml;r die Standardsprachversion der Website (z.B. "de", "en" oder "fr"). Analog zum gew&auml;hlten K&uuml;rzel 
muss auch eine Sprachversion in der Datenbank existieren!</div>
<h2>Website HTML-Standardcodierung</h2>
<input type="text" name="charset" value="<?php echo $charset; ?>" />
<div class="inputHelp">ISO-Schreibweise f&uuml;r den Standardzeichensatz im Adminbereich (z.B. "iso-8859-1" oder "utf-8")</div>
<div class="categorySpacer"> </div>
<h1>Erweiterte Einstellungen</h1>
<h2>MySQL-Fehler speichern?</h2>
<input type="checkbox" value="1" class="inputCheckbox" name="cmt_dberrorlog" id="cmt_dberrorlog" <?php if ($cmt_dberrorlog) echo 'checked="checked"'; ?> /><label for="cmt_dberrorlog">&nbsp;Datenbankfehler speichern</label>
<div class="inputHelp">MySQL-Datenbankfehler, die von PHP-Skripten erzeugt werden, werden in einer Tabelle mit weiteren Informationen gespeichert. Sinnvoll f&uuml;r Entwickler.</div>
<h2>Cookies benutzen?</h2>
<input type="checkbox" value="1" class="inputCheckbox" name="cmt_usecookies" id="cmt_usecookies" <?php if ($cmt_usecookies) echo 'checked="checked"'; ?> /><label for="cmt_usecookies">&nbsp;Cookies nutzen</label>
<div class="inputHelp">Sofern Cookie-Speicherung im Browser des Seitenbesuchers aktiviert ist, wird die Session-ID nicht in der URL sondern als Cookie unsichtbar von Siete zu Seite weitergereicht. Dies hilft, 
die internen Links auf einer Seite f&uuml;r Suchmaschinen lesbarer zu machen.</div>
<h2>Cookienutzung erzwingen?</h2>
<input type="checkbox" value="1" class="inputCheckbox" name="cmt_forcecookies" id="cmt_forcecookies" <?php if ($cmt_forcecookies) echo 'checked="checked"'; ?> /><label for="cmt_forcecookies">&nbsp;Cookies erzwingen</label>
<div class="inputHelp">Selbst wenn Cookie-Speicherung im Browser des Seitenbesuchers nicht aktiviert ist, wird die Session-ID nicht in der URL weitergegeben. In solchen F&auml;llen gehen Session-Variablen verloren, sofern nicht andere 
Mechanismen eingesetzt werden, die Session-ID von Seite zu Seite zu &uuml;bertragen.</div>
<h2>Apache "mod_rewrite" nutzen?</h2>
<input type="checkbox" value="1" class="inputCheckbox" name="cmt_modrewrite" id="cmt_modrewrite" <?php if ($cmt_modrewrite) echo 'checked="checked"'; ?> /><label for="cmt_modrewrite">&nbsp;lesbare URLs mit mod_rewrite verwenden</label>
<div class="inputHelp">"Mod_rewrite" ist eine Erweiterung des Apache-Servers, die lesbare Seitenadressen/ URLs erlaubt.<br />
<strong>W&auml;hlen Sie diese Option nur, wenn der content-o-mat auf einem Apache-Server installiert wird und das Modul "mod_rewrite" aktiviert ist.</strong></div>
<!-- Einstellungen Ende -->
<div id="buttons"><input type="image" src="/installer/img/nextstep.gif" alt="n&auml;chster Schritt"></div>
<input type="hidden" name="step" value="2" />
</form>