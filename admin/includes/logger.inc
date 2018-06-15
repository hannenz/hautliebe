<?php
/**
 * logger.inc
 * 
 * Include file for constants specific to class_logger.php
 * Should be included in cmt_constants.inc as well
 * 
 * @author Johannes Braun <j.braun@agentur-halma.de>
 * @package contentomat
 * @version 2016-01-14
 */ 

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
?>
