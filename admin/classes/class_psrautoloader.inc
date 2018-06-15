<?php
namespace Contentomat;

/**
 * PSR4 Autoloader
 * Class to register namespaces and include paths for automatic class file includes. 
 * As seen on: https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader-examples.md
 * See: http://www.php-fig.org/psr/psr-4/
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2014-04-27
 *
 */
class PsrAutoloader {
	/**
	 * An associative array where the key is a namespace prefix and the value
	 * is an array of base directories for classes in that namespace.
	 *
	 * @var array
	 */
	protected $prefixes = array();

	public function __construct() {
		$this->register();
	}
	/**
	 * Register loader with SPL autoloader stack.
	 * 
	 * @return void
	 */
	public function register() {
		spl_autoload_register(array($this, 'loadClass'));
	}

	/**
	 * Adds a base directory for a namespace prefix.
	 *
	 * @param string $prefix The namespace prefix.
	 * @param string $base_dir A base directory for class files in the
	 * namespace.
	 * @param bool $prepend If true, prepend the base directory to the stack
	 * instead of appending it; this causes it to be searched first rather
	 * than last.
	 * @return void
	 */
	public function addNamespace($prefix, $base_dir, $prepend = false) {
		
		// normalize namespace prefix
		$prefix = trim($prefix, '\\') . '\\';

		// normalize the base directory with a trailing separator
		$base_dir = rtrim($base_dir, '/') . DIRECTORY_SEPARATOR;
		$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

		// initialize the namespace prefix array
		if (isset($this->prefixes[$prefix]) === false) {
			$this->prefixes[$prefix] = array();
		}

		// retain the base directory for the namespace prefix
		if ($prepend) {
			array_unshift($this->prefixes[$prefix], $base_dir);
		} else {
			array_push($this->prefixes[$prefix], $base_dir);
		}

	}

	/**
	 * Loads the class file for a given class name.
	 *
	 * @param string $class The fully-qualified class name.
	 * @return mixed The mapped file name on success, or boolean false on
	 * failure.
	 */
	public function loadClass($class) {
		
		// the current namespace prefix
		$prefix = $class;

		// work backwards through the namespace names of the fully-qualified
		// class name to find a mapped file name
		while (false !== $pos = strrpos($prefix, '\\')) {

			// retain the trailing namespace separator in the prefix
			$prefix = substr($class, 0, $pos + 1);

			// the rest is the relative class name
			$relative_class = substr($class, $pos + 1);

			// try to load a mapped file for the prefix and relative class
			$mapped_file = $this->loadMappedFile($prefix, $relative_class);
			if ($mapped_file) {
				return $mapped_file;
			}

			// remove the trailing namespace separator for the next iteration
			// of strrpos()
			$prefix = rtrim($prefix, '\\');   
		}
		
		// if while loop was not called 
		return $this->loadMappedFile('\\', $class);
	}

	/**
	 * Load the mapped file for a namespace prefix and relative class.
	 * 
	 * @param string $prefix The namespace prefix.
	 * @param string $relative_class The relative class name.
	 * @return mixed Boolean false if no mapped file can be loaded, or the
	 * name of the mapped file that was loaded.
	 */
	protected function loadMappedFile($prefix, $relative_class) {

		// are there any base directories for this namespace prefix?
		if (isset($this->prefixes[$prefix]) === false) {
			return false;
		}

		// look through base directories for this namespace prefix
		foreach ($this->prefixes[$prefix] as $base_dir) {

			// class files in contentomat have this filenamstructure: class_{classname}.inc (older versions class_{classname}.php)
			
			$classFileName = 'class_' . strtolower(str_replace('_', '', $relative_class));
			
			// replace the namespace prefix with the base directory,
			// replace namespace separators with directory separators
			// in the relative class name, append with .php
			$file = $base_dir
				  . str_replace('\\', DIRECTORY_SEPARATOR, $classFileName);
			$file = $base_dir
				  . str_replace('\\', '/', $classFileName);

			if (file_exists($file .'.inc')) {
				require_once $file . '.inc';
				return true;
			} else if (file_exists($file .'.php')) {
				require_once $file . '.php';
				return true;
			}
		}

		// never found it
		return false;
	}

	/**
	 * If a file exists, require it from the file system.
	 * 
	 * @param string $file The file to require.
	 * @return bool True if the file exists, false if not.
	 */
	protected function requireFile($file) {
		if (file_exists($file)) {
			require $file;
			return true;
		}
		return false;
	}
}