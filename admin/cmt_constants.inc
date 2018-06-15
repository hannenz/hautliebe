<?php
/**
 * Conten-o-mat Einstellungen/Konstanten
 */

// Pfad, in dem sich der ROOT der Homepage befindet (z.B. "/" bei www.meinedomain.de (Standard) oder 
// "myweb/" bei www.meinedomain/myweb/), immer mit führendem und ggf. abschließendem '/' angeben!
define ('WEBROOT', '/');

// Pfad/Ordner zum Admin-Bereich: Standard 'admin/'
define ('ADMINPATH', 'admin/');

// Zentraler Ordner für Downloads: Standard ist 'downloads/'
define ('DOWNLOADPATH', 'downloads/');

// Standardsprache für mehrsprachige Websites
define ('DEFAULTLANGUAGE', 'de');

// veraltet: Zeichensatz, der für die Seite verwendet wird
define ('CHARSET', 'utf-8');

// Zeichensatz, der verwendet wird, falls kein anderer Zeichensatz angegeben wird
define ('CMT_DEFAULTCHARSET', 'utf-8');

// Name des Webs
define ('WEBNAME', 'Hautliebe');

// Datenbankfehler Logging
define ('CMT_DBERRORLOG', '1');


// Sollen Cookies benutzt werden?
define('CMT_USECOOKIES', '1');

// Cookies nutzen, auch wenn keine Cookies aktiviert sind?
define('CMT_FORCECOOKIES', '1');

// Apache Mod-Rewrite nutzen?
define('CMT_MODREWRITE', '1');

// Standardzeitzone für PHP-Funktion 'date'
define('CMT_DEFAULTTIMEZONE', 'Europe/Berlin');

// Set to true, to force cmt_applauncher.inc not to send "no-cache" headers!
// Headers will be sent by default because of security reasons: After logout 
// there is no possibility to "go back" ans view the pages content  
define('CMT_NO_CACHE', false);

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

define ('INCLUDEPATH', $_SERVER['DOCUMENT_ROOT'] . '/');

// Include-Pfad zum Adminbereich
define ('INCLUDEPATHTOADMIN', INCLUDEPATH.ADMINPATH);

define ('PATHTOTMP', INCLUDEPATH . 'tmp' . DIRECTORY_SEPARATOR);

//@require_once(PATHTOADMIN . 'includes' . DIRECTORY_SEPARATOR . 'logger.inc');

/**
 * Available Log Levels
 */
define ('LOG_LEVEL_ALL', 		0);			// Log everything
define ('LOG_LEVEL_INFO', 		1 << 0);	// Neutral messages
define ('LOG_LEVEL_NOTICE',		1 << 1);	// Notification about the system, no action needs to be taken
define ('LOG_LEVEL_DEBUG',		1 << 2);	// Debug messages
define ('LOG_LEVEL_WARNING',	1 << 3);	// Warnings: Please check and fix something
define ('LOG_LEVEL_ERROR',		1 << 4);	// An error occured, some functionality could not be performed but execution continued
define ('LOG_LEVEL_FATAL',		1 << 5);	// A fatal error occured which caused Content-O-Mat to abort execution
define ('LOG_LEVEL_NONE',		1 << 6); 	// Don't log anything

/**
 *  Available targets to log to
 * Multiple targets can be selected by combining with bitwise OR operator
 * @example: LOG_TARGET_FILE | LOG_TARGET_DB  will log to file and database
 * */
define ('LOG_TARGET_FILE', 		1 << 0); 	// Log to a file, set full path in CMT_LOG_FILE
define ('LOG_TARGET_DB', 		1 << 1); 	// Log to database
define ('LOG_TARGET_STDOUT',	1 << 2); 	// Log to "stdout" (e.g. echo'ed to HTML document)
define ('LOG_TARGET_DEBUGGER', 	1 << 3); 	// Log to FirePHP
define ('LOG_TARGET_USER',		1 << 4);	// Pass log message to a user defined callable (function / method)

// Log Level (see admin/classes/class_logger.php for available values and their meanings)
// Only log messages with this or higher log level
define ('CMT_LOG_LEVEL', LOG_LEVEL_DEBUG);

// Log Target(s)
define ('CMT_LOG_TARGET', LOG_TARGET_FILE);

// Path to Log File, if log target LOG_TARGET_FILE is selected (must be writable by web server user)
define ('CMT_LOG_FILE', PATHTOTMP . 'logs' . DIRECTORY_SEPARATOR . 'contentomat.log');
?>
