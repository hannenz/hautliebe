<?php

/**
 * class_debug.inc
 * Klasse stellt Methoden und Variablen zum Fehlerbehandlung zur  Verfuegung
 * 
 * Refactored to static class by J. Braun 2015-02-19
 * 
 * @author A.Alkaisi <info@buero-hahn.de>
 * @version 2016-01-13
 * 
 */

namespace Contentomat;

class Debug {

    protected static $initialized  = false;

	protected static $firePhp;
	protected static $firePhpInitDone = FALSE; // Wurde FirePHP initialisiert oder nicht
	protected static $firePHPTypesValue = E_ERROR;	 // folgende gelten folgende Default-Werte, ausser, war: E_ALL
	protected static $firePHPTraceTypesValue = E_ERROR;   // EMail nur bei E_ERROR versenden.
	protected static $errorType = array(
		E_ERROR => 'ERROR',
		E_WARNING => 'WARNING',
		E_PARSE => 'PARSING ERROR',
		E_NOTICE => 'NOTICE',
		E_CORE_ERROR => 'CORE ERROR',
		E_CORE_WARNING => 'CORE WARNING',
		E_COMPILE_ERROR => 'COMPILE ERROR',
		E_COMPILE_WARNING => 'COMPILE WARNING',
		E_USER_ERROR => 'USER ERROR',
		E_USER_WARNING => 'USER WARNING',
		E_USER_NOTICE => 'USER NOTICE',
		E_STRICT => 'STRICT NOTICE',
		E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
	);

    // public static function __construct() {
    //     self::initialize();
    // }

	public static function initialize() {
        if (!self::$initialized) {
            $hlpInclude = PATHTOADMIN . 'classes/FirePHPCore/FirePHP.class.php';

            // Testen, ob die Class-Datei vorhanden ist 
            // Sonst eine E_USER_NOTICE und kein firePHP
            if (file_exists($hlpInclude) === TRUE) {                // FirePHP-Klasse       
                require_once($hlpInclude);

                if (class_exists("FirePHP") === TRUE) {
                    self::$firePhp = \FirePHP::getInstance(true);
                    self::$firePhpInitDone = TRUE;

                    $firePhpOptions = array('maxObjectDepth' => 2,
                        'maxArrayDepth' => 5,
                        'traceOffset' => 0,
                        'maxTraceDepth' => 10,
                    );

                    self::$firePhp->setOptions($firePhpOptions);

                    // $this->firePhp->setObjectFilter('cmtShop', array('myErrorHandler', 'firePHPTrace'));
                }
            }
            
            self::$initialized = true;
        }
    }
    
    
    /**
     * public function firePHPLog()
     * Gibt, wenn FirePHP initialisiert wurde, eine Log-Message per FirePHP aus
     * 
     * @param mixed LogMessage oder WerteArray  
     * @param string Label
     * @return void Kein Rueckgabewert
     */
    public static function log($logmessage, $label="") {
        self::initialize();
        if (self::$firePhpInitDone === TRUE){
            self::$firePhp->log($logmessage, $label);
        }
    }

    /**
     * public static function firePHPInfo()
     * Gibt, wenn FirePHP initialisiert wurde, eine Info-Message per FirePHP aus
     * 
     * @param string InfoMessage
     * @return void Kein Rueckgabewert
     */
    public static function info($infomessage) {
        self::initialize();
        if (self::$firePhpInitDone === TRUE){
            self::$firePhp->info($infomessage);
        }
    }

    /**
     * public static function firePHPWarn()
     * Gibt, wenn FirePHP initialisiert wurde, eine Warnung per FirePHP aus
     * 
     * @param string Warnung
     * @return void Kein Rueckgabewert
     */
    public static function warn($warnmessage) {
        self::initialize();
        if (self::$firePhpInitDone === TRUE){
            self::$firePhp->warn($warnmessage);
        }
    }

    /**
     * public static function firePHPError()
     * Gibt, wenn FirePHP initialisiert wurde, eine Error-Message per FirePHP aus
     * 
     * @param string ErrorMessage
     * @return void Kein Rueckgabewert
     */
    public static function error($errormessage) {
        self::initialize();
        if (self::$firePhpInitDone === TRUE){
            self::$firePhp->error($errormessage);
        }
    }

    /**
     * public static function firePHPTrace()
     * Gibt, wenn FirePHP initialisiert wurde, einen Trace per FirePHP aus
     * 
     * @param string Trace-Label
     * @return void Kein Rueckgabewert
     */
    public static function trace($label) {
        self::initialize();
        if (self::$firePhpInitDone === TRUE){
            self::$firePhp->trace($label);
        }
    }

    /**
     * debug()
     * Outputs debug message with file and line informations
     * 
     * @param $var mixed Subject to debug
     * @return void
     */
    public static function debug($var) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        printf('<div class="debug" style="background-color:#e0e0e0; color:#303030;padding:4px 6px; border:1px solid #808080; border-radius:3px"><div class="debug-title" style="font-family:monospace;font-size:12px"><strong>%s</strong>:%s</div>', $caller['file'], $caller['line']);
        echo "<pre>";
        print_r($var);
        echo "</pre></div>";
    }

}

?>