<?php
/**
 * Conten-o-mat Einstellungen/Konstanten
 */

// Pfad, in dem sich der ROOT der Homepage befindet (z.B. "/" bei www.meinedomain.de (Standard) oder 
// "myweb/" bei www.meinedomain/myweb/), immer mit führendem und ggf. abschließendem '/' angeben!
define ('WEBROOT', '{WEBROOT}');

// Pfad/Ordner zum Admin-Bereich: Standard 'admin/'
define ('ADMINPATH', '{ADMINPATH}');

// Zentraler Ordner für Downloads: Standard ist 'downloads/'
define ('DOWNLOADPATH', '{DOWNLOADPATH}');

// Standardsprache für mehrsprachige Websites
define ('DEFAULTLANGUAGE', '{DEFAULTLANGUAGE}');

// veraltet: Zeichensatz, der für die Seite verwendet wird
define ('CHARSET', '{CHARSET}');

// Zeichensatz, der verwendet wird, falls kein anderer Zeichensatz angegeben wird
define ('CMT_DEFAULTCHARSET', '{CHARSET}');

// Name des Webs
define ('WEBNAME', '{WEBNAME}');

// Datenbankfehler Logging
define ('CMT_DBERRORLOG', '{CMT_DBERRORLOG}');

// Sollen Cookies benutzt werden?
define('CMT_USECOOKIES', '{CMT_USECOOKIES}');

// Cookies nutzen, auch wenn keine Cookies aktiviert sind?
define('CMT_FORCECOOKIES', '{CMT_FORCECOOKIES}');

// Apache Mod-Rewrite nutzen?
define('CMT_MODREWRITE', '{CMT_MODREWRITE}');

/* [[UPDATES HERE]] */

// URL des Skriptes
define ("SELF", basename($_SERVER['PHP_SELF']));

// aktueller Pfad minus WEBROOT ergibt den aktuellen ROOT
$actPath = preg_replace('#^'.WEBROOT.'#', '', $_SERVER['PHP_SELF']);

// ROOT berechnen
$depth = substr_count ($actPath, '/');
define ('ROOT', str_pad('', $depth*3, '../'));

// PATHTOADMIN berechnen
define ('PATHTOADMIN', str_replace('//', '/', ROOT.ADMINPATH));

// PATHTODOWNLOADS berechnen
define ('PATHTODOWNLOADS', ROOT.DOWNLOADPATH);

// PATHTOWEBROOT berechnen
define ('PATHTOWEBROOT', ROOT);

// Include-Pfade für PHP-Skripte (im Idealfall immer absolut angeben!):
// Include-Pfad zum Webroot
define ('INCLUDEPATH', '{CMT_INCLUDEPATH}');

// Include-Pfad zum Adminbereich
define ('INCLUDEPATHTOADMIN', INCLUDEPATH.ADMINPATH);
?>