<?php
namespace Contentomat\Paperboy;

use \Exception;
use \Contentomat\Debug;
use \Contentomat\Paperboy\SubscriptionHandler;
use \Contentomat\Paperboy\Import;
use \Contentomat\Logger;

class CsvImport extends \Contentomat\Paperboy\Import {

	// CSV format settings
	
	/**
	 * @var string	CSV field delimiter
	 */
	protected $delimiter = ';';

	/**
	 * @var string	CSV text enclosure
	 */
	protected $enclosure = '"';

	/**
	 * @var string	CSV Escape character
	 */
	protected $escape = '\\';

	/**
	 * @var bool	Whether to skip the first line of CSV file (if it contains header data)
	 */
	protected $skipFirstLine = true;


	
	/** 
	 * Field indices in csv data
	 * @var Array
	 */
	protected $csvIndices = array(
		'firstname' => 1,
		'lastname' => 2,
		'zip' => 3,
		'city' => 4,
		'email' => 6
	);


	/**
	 * @var int		Number of records to import
	 */
	protected $subscribersToImport = 0;
	/**
	 * @var int		Number of successfully imported records
	 */
	protected $subscribersImported = 0;



	/**
	 * Getter
	 * @return int
	 */
	public function getSubscribersToImport() {
		return $this->subscribersToImport;
	}

	/**
	 * Getter
	 * @return int
	 */
	public function getSubscribersImported() {
		return $this->subscribersImported;
	}

	
	/**
	 * Constructor
	 * @param string CSV Delimiter, optional, defaults to ;
	 * @param string CSV Enclosure, optional, defaults to '
	 * @param string CSV Escape character, optional, defaults to \
	 * @param bool		Skip first line?, optional, defaults to true
	 * @param mixed		ID or Array of IDs of the newsletter(s) to subscribe each imported subscriber to
	 */
	public function __construct($delimiter, $enclosure, $escape, $skipFirstLine, $newsletterIDs) {

		parent::__construct();

		if (!empty($delimiter)) {
			$this->delimiter = $delimiter;
		}
		if (!empty($enclosure)) {
			$this->enclosure = $enclosure;
		}
		if (!empty($escape)) {
			$this->escape = $escape;
		}
		if (!empty($skipFirstLine)) {
			$this->skipFirstLine = $skipFirstLine;
		}
		// Debugging!

		if (!empty($newsletterIDs)) {
			if (!is_array($newsletterIDs)) {
				$newsletterIDs = (array)$newsletterIDs;
			}
			$this->newsletterIDs = $newsletterIDs;
		}
	}



	/**
	 * @param string $file		Path to CSV file to import data from
	 * @return bool				true if all records have been imported successfully
	 */
	public function import_from_csv($file) {

		if (!file_exists($file)) {
			throw new Exception("File not found: $file");
		}


		$fp = fopen($file, "r");
		if ($fp === false) {
			throw new Exception("Could not open file: $file");
		}


		$line = 0;
		$imports = 0;
		while (($data = fgetcsv($fp, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {

			++$line;

			if ($line == 1 && $this->skipFirstLine) {
				continue;
			}

			$params = array(
				'email' => $data[$this->csvIndices['email']],
				'origin' => 'csvimport',
				'newsletterID' => $this->newsletterIDs
			);
			if (!empty($this->csvIndices['firstname']) && count($data) > $this->csvIndices['firstname']) {
				$params['firstname'] = $data[$this->csvIndices['firstname']];
			}
			if (!empty($this->csvIndices['name']) && count($data) > $this->csvIndices['name']) {
				$params['name'] = $data[$this->csvIndices['lastname']];
			}
			if (!empty($this->csvIndices['zip']) && count($data) > $this->csvIndices['zip']) {
				$params['zip'] = $data[$this->csvIndices['zip']];
			}
			if (!empty($this->csvIndices['city']) && count($data) > $this->csvIndices['city']) {
				$params['city'] = $data[$this->csvIndices['city']];
			}

			try {
				parent::import_subscriber($params);
			}
			catch (Exception $e) {
				continue;
			}

			$imports++;
		}
		fclose($fp);

		$this->subscribersImported = $imports;
		$this->subscribersToImport = $this->skipFirstLine ? $line - 1 : $line;

		return ($this->subscribersImported == $this->subscribersToImport);
	}
}
?>
