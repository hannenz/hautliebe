<?php
/**
 * Write log messages to different targets
 * stdout (Browser), File, Database implemented at the moment
 * 
 * Configuration is done in cmt_constants.inc
 * 
 * @author Johannes Braun <j.braun@agentur-halma.de>
 * @package contentomat
 * @version 2016-01-13
 */
namespace Contentomat;

use \Contentomat\DBCex;
use \Contentomat\Debug;

@require_once (PATHTOADMIN . 'includes' . DIRECTORY_SEPARATOR . 'logger.inc');

/**
 * Logger class
 * Implemented as "static class" as described here: http://stackoverflow.com/a/468648
 * but can be instantiated as well (maybe better for performance reasons?)
 * 
 * So, you can calll Logger::log() from everywhere
 * or maybe get an instance at $this->cmt->Logger and call the log-method from there..
 */
class Logger {

	/**
	 * The target(s) to log to
	 * A combination of LOG_TARGET_* values is allowed,
	 * e.g. LOG_TARGET_FILE | LOG_TARGET_DB
	 * @var int
	 * @access protected
	 */
	protected static $target;

	/**
	 * The file to log to
	 * @var string
	 * @access protected
	 */
	protected static $fileName;

	/**
	 * The database table name to log to.
	 * @var string;
	 * @access protected
	 */
	protected static $tableName;

	/**
	 * The db field to log the timestamp to
	 * @var string
	 * @access protected
	 */
	protected static $fieldTimestamp;

	/**
	 * The db field to log the log level to
	 * @var string
	 * @access protected
	 */
	protected static $fieldLevel;

	/**
	 * The db field to log messages to
	 * @var string
	 * @access protected
	 */
	protected static $fieldMessage;

	/**
	 * Format string (strftime) for timestamp output
	 * if target is file or stdout
	 * @var string
	 * @access protected
	 */
	protected static $timestampFormat;

	/**
	 * Log level
	 * @var int
	 * @access protected
	 */
	protected static $level;

	/**
	 * Array of string representations of the log levels
	 * @var array
	 * @access protected
	 */
	protected static $levelStrings;

	/**
	 * A userFunc to pass the log message string to, if target USER is selected
	 * The callable's signature should be boolean fn(string) and return if the
	 * message could successfully be logged or not
	 * @var Callable
	 * @access private
	 */
	protected static $userFunc;

	/**
	 * Handle to the log file
	 * @var Resource
	 * @access private
	 */
	private static $fileHandle;
	
	/**
	 * Database manager instance
	 * @var Object
	 * @access private
	 */
	private static $db;

	/**
	 * Flag to indicate wether the "static" class has been initialized yet
	 * @var boolean
	 * @access private
	 */
	private static $initialized = false;

	/**
	 * Initialize Logger, Pseudo-Constructor for static class
	 */
	public static function initialize() {

		if (self::$initialized === true) {
			return;
		}


		// Set configuration from constants (defined in cmt_constants.inc) or set defaults
		self::$target =			defined('CMT_LOG_TARGET') 			? CMT_LOG_TARGET			: LOG_TARGET_FILE;
		self::$level = 			defined('CMT_LOG_LEVEL')			? CMT_LOG_LEVEL 			: LOG_LEVEL_WARNING;
		self::$fileName = 		defined('CMT_LOG_FILE') 			? CMT_LOG_FILE 				: PATHTOTMP . 'logs' . DIRECTORY_SEPARATOR . 'contentomat.log';
		self::$tableName = 		defined('CMT_LOG_TABLENAME') 		? CMT_LOG_TABLENAME 		: 'cmt_log';
		self::$fieldTimestamp = defined('CMT_LOG_FIELD_TIMESTAMP')	? CMT_LOG_FIELD_TIMESTAMP 	: 'cmt_timestamp';
		self::$fieldLevel = 	defined('CMT_LOG_FIELD_LEVEL')		? CMT_LOG_FIELD_LEVEL 		: 'cmt_level';
		self::$fieldMessage = 	defined('CMT_LOG_FIELD_MESSAGE')	? CMT_LOG_FIELD_MESSAGE 	: 'cmt_message';
		self::$timestampFormat=	defined('CMT_LOG_TIMESTAMP_FMT')	? CMT_LOG_TIMESTAMP_FMT 	: '%Y-%m-%d %H:%M:%S';

		self::$levelStrings = array(
			LOG_LEVEL_ALL => 'All',
			LOG_LEVEL_INFO => 'Info',
			LOG_LEVEL_NOTICE => 'Notice',
			LOG_LEVEL_DEBUG => 'Debug',
			LOG_LEVEL_WARNING => 'Warning',
			LOG_LEVEL_ERROR => 'Error',
			LOG_LEVEL_FATAL => 'Fatal',
		);
		
		self::$fileHandle = null;
		
		// Consider class as initialized at this point
		self::$initialized = true;
		
		if (self::$target & LOG_TARGET_FILE){
			try {
				self::openFile(self::$fileName);
			}
			catch (\Exception $e) {
				// Try to log this error to other targets, if any
				self::$target = LOG_TARGET_DEBUGGER;
				self::error($e->getMessage());
			}
		}

		if (self::$target & LOG_TARGET_DB) {
			self::$db = new DBCex();
		}
	}

	/**
	 * Log a message to the configured targets
	 * 
	 * @param string $message 		The message to log
	 * @param integer $level 		The log-level of the message, default: LOG_LEVEL_INFO
	 * 
	 * @return integer 	A bitwise combined number of targets that have been successfully logged 
	 * 			Should be the same as Logger::target if every target has been successfully logged
	 * 			If 0 is returned, no target has been logged (either because of errors or because
	 * 			the log level of the message was lower than configured)
	 */
	public static function log($message, $level = LOG_LEVEL_INFO) {


		self::initialize();

		$ret = 0;

		if ($level >= self::$level) {

			// Compose the message string
			// TODO: Make configurable, s.th. like self::$messageFormatString = "Confucius said \"%3$s\" at %$1s at a level of %2$s"
			$messageString = sprintf("%s [%s] %s", strftime(self::$timestampFormat), strtoupper(self::$levelStrings[$level]), $message);

			if (self::$target & LOG_TARGET_STDOUT) {
				$r = printf('<pre class="cmt-log-message log-level-%s">%s</pre>',
					strtolower(self::$levelStrings[$level]),
					htmlentities($messageString)
				);
				if ($r > 0) {
					$ret |= LOG_TARGET_STDOUT;
				}
			}

			if (self::$target & LOG_TARGET_FILE) {

				$r = fprintf(self::$fileHandle, "%s\n", $messageString);
				if ($r > 0) {
					$ret |= LOG_TARGET_FILE;
				}
			}

			if (self::$target & LOG_TARGET_DB) {
				$query = sprintf("INSERT INTO %s SET %s=NOW(),%s='%s [%s]',%s='%s'",
					self::$tableName,
					self::$fieldTimestamp,
					self::$fieldLevel,
					$level,
					strtoupper(self::$levelStrings[$level]),
					self::$fieldMessage,
					$message
				);
				if (self::$db->query($query) === 0) {
					$ret |= LOG_TARGET_DB;
				}
			}

			if (self::$target & LOG_TARGET_DEBUGGER) {
				Debug::log($messageString, "<CMT_LOG>");
				$ret |= LOG_TARGET_DEBUGGER;
			}

			if (self::$target & LOG_TARGET_USER) {
				if (is_callable(self::$userFunc)) {
					$ret |= call_user_func(self::$userFunc, $messageString);
				}
			}
		}
		return $ret;
	}

	/**
	 * Convenience function to log a notification
	 * 
	 * @param string $message
	 * @return int 		see Logger::log()
	 */
	public function notice($message) {
		self::log($message, LOG_LEVEL_NOTICE);
	}
	
	/**
	 * Convenience function to log a debug message
	 * 
	 * @param string $message
	 * @return int 		see Logger::log()
	 */
	public function debug($message) {
		self::log($message, LOG_LEVEL_DEBUG);
	}
	
	/**
	 * Convenience function to log a warning message
	 * 
	 * @param string $message
	 * @return int 		see Logger::log()
	 */
	public function warn($message) {
		self::log($message, LOG_LEVEL_WARNING);
	}
	
	/**
	 * Convenience function to log an error message
	 * 
	 * @param string $message
	 * @return int 		see Logger::log()
	 */
	public function error($message) {
		self::log($message, LOG_LEVEL_ERROR);
	}

	/**
	 * Convenience function to log a fatal error
	 * 
	 * @param string $message
	 * @return int 		see Logger::log()
	 */
	public function fatal($message) {
		self::log($message, LOG_LEVEL_FATAL);
	}
	
	/**
	 * Open log file for writing
	 * 
	 * @param string 	Path to file
	 * @return void
	 * @throws \Exception
	 */
	private function openFile($fileName) {
		
		// if (!is_writable($fileName)) {
		// 	throw new \Exception(sprintf("The file \"%s\", specified as log file, is not writable", $fileName));
		// }
		self::$fileName = $fileName;

		if (self::$target & LOG_TARGET_FILE) {
			if (is_resource(self::$fileHandle)) {
				fclose (self::$fileHandle);
			}
			self::$fileHandle = fopen(self::$fileName, 'a');
			if (self::$fileHandle === false) {
				throw new \Exception(sprintf("Could not open log file \"%s\" for writing", $fileName));
			}
		}
	}

	/**
	 * Setter for fileName
	 * @param string		The file name
	 * @return boolean  	Success
	 */
	public function setFileName($fileName) {
		try {
			self::openFile($fileName);
		}
		catch (Exception $e) {
			self::$target -= LOG_TARGET_FILE;
			self::error(sprintf("Could not open log file \"%s\" for writing", $fileName));
		}
	}

	/**
	 * Getter for fileName
	 * @return string
	 */
	public function getFileName() {
		return self::$filename;
	}
	
	/**
	 * Setter for target
	 * @param string
	 * @return void
	 */
	public function setTarget(string $target) {
		self::$target = $target;
	}

	/**
	 * Getter for target
	 * @return string
	 */
	public function getTarget() {
		return self::$target;
	}

	/**
	 * Setter for tableName
	 * @param string
	 * @return void
	 */
	public function setTableName($tableName) {
		self::$tableName = $tableName;
	}

	/**
	 * Getter for tableName
	 * @return string
	 */
	public function getTableName() {
		return self::$tableName;
	}

	/**
	 * Setter for fieldTimestamp
	 * @param string
	 * @return void
	 */
	public function setFieldTimestamp($fieldTimestamp) {
		self::$fieldTimestamp = $fieldTimestamp;
	}

	/**
	 * Getter for fieldTimestamp
	 * @return string
	 */
	public function getFieldTimestamp() {
		return self::$fieldTimestamp;
	}

	/**
	 * Setter for fieldLevel
	 * @param string
	 * @return void
	 */
	public function setFieldLevel($fieldLevel) {
		self::$fieldLevel = $fieldLevel;
	}

	/**
	 * Getter for fieldLevel
	 * @return string
	 */
	public function getFieldLevel() {
		return self::$fieldLevel;
	}

	/**
	 * Setter for fieldMessage
	 * @param string
	 * @return void
	 */
	public function setFieldMessage($fieldMessage) {
		self::$fieldMessage = $fieldMessage;
	}

	/**
	 * Getter for fieldMessage
	 * @return string
	 */
	public function getFieldMessage() {
		return self::$fieldMessage;
	}

	/**
	 * Setter for timestampFormat
	 * @param string
	 * @return void
	 */
	public function setTimestampFormat($timestampFormat) {
		self::$timestampFormat = $timestampFormat;
	}

	/**
	 * Getter for timestampFormat
	 * @return string
	 */
	public function getTimestampFormat() {
		return self::$timestampFormat;
	}

	/**
	 * Setter for level
	 * @param string
	 * @return void
	 */
	public function setLevel($level) {
		self::$level = $level;
	}

	/**
	 * Getter for level
	 * @return string
	 */
	public function getLevel() {
		return self::$level;
	}

	/**
	 * Setter for userFunc
	 * @param string
	 * @return void
	 */
	public function setUserFunc($userFunc) {
		self::$userFunc = $userFunc;
	}
}
?>
