<?php
/**
 * OUTDATED: class_cmtdbtable.inc
 * Kurzkommentar
 *
 * Langkommentar
 * 
 * @version 30.01.2009 / 2009-01-30
 * @author J.Hahn <info@buero-hahn.de>
 */
 
 class cmtDbTable {
 	protected $db;
 	protected $parser;
 	protected $dbTable;
 	protected $condWhere;
 	protected $totalEntries;
 	
 	public function __construct($params=array()) {

 		$this->dbTable = $params['tableName'];
 		$this->condWhere = $params['condWhere'];

 		$this->db = new DBCex();
 		$this->parser = new Parser();
 	}
 	
 	/**
 	 * public function countEntries()
 	 * Z�hlt entweder alle Eintr�ge einer Tabelle oder ausgew�hlte. Erwartet Parameter in einem Array.
 	 * 
 	 * @param string tableName	Name der Datenbanktabelle
 	 * @param string condWhere	OPtional: WHERE-Teile einer Query
 	 * 
 	 * @return mixed Anzahl der Eintr�ge (integer) oder false
 	 */
 	public function countEntries($params=array()) {
 		
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'condWhere' => $this->condWhere
						);
 		$params = array_merge ($defaultParams, $params);

 		if (!$params['tableName']) return false;

 		$query = 'SELECT COUNT(*) AS entriesCount FROM '.$params['tableName'];
 		if ($params['condWhere']) $query .= ' WHERE '.$params['condWhere'];

 		$this->db->query($query);
 		$r = $this->db->get();
 		
 		if (!$this->db->getLastErrorNr()) {
 			return intval($r['entriesCount']);
 		} else {
 			return false;
 		}
 	}
 	
 	
 	public function makePaging($params=array()) {

 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'condWhere' => $this->condWhere,
							'totalEntries' => $this->totalEntries,
							'currentPage' => 1,
							'itemsPerPage' => 10,
							'templatePageLink' => '',
							'templateLinkSeparator' => ' ',
							'templatePrevLink' => '',
							'templateNextLink' => '',
							'showPages' => 0
						);

		// Verhindert '' oder 0 itemsPerPage
		if (!$params['itemsPerPage']) unset($params['itemsPerPage']);
		if (!$params['currentPage']) unset($params['currentPage']);

		$params = array_merge ($defaultParams, $params);

		// Muss Anzahl aller Einträge ermittelt werden?
		if (!$this->totalEntries) $this->totalEntries = $this->countEntries($params);

		// Anzahl der benötigten Paging-Links
		$totalPages = ceil(intval($this->totalEntries) / intval($params['itemsPerPage']));

		// Ist die Seitenauswahl begrenzt auf eine bestimmte Anzahl links?
		if ($params['showPages']) {
			$showLinksDistance = floor($params['showPages']/2);
			
			$firstPage = $params['currentPage'] - $showLinksDistance;
			$lastPage = $params['currentPage'] + $showLinksDistance;
			
			if ($firstPage <= 0) {
				$lastPage += abs($firstPage) + 1;
				$firstPage = 1;
			}
			
			if ($lastPage > $totalPages) $lastPage = $totalPages;
		} else {
			$firstPage = 1;
			$lastPage = $totalPages;
		}

		// Pageing erzeugen
		// Daten an Parser
		$this->parser->setParserVar('tableName', $params['tableName']);
		$this->parser->setParserVar('totalEntries', $this->totalEntries);
		$this->parser->setParserVar('totalPages', $totalPages);
		$this->parser->setParserVar('currentPage', $params['currentPage']);
		$this->parser->setParserVar('itemsPerPage', $params['itemsPerPage']);
		$this->parser->setParserVar('firstPage', $firstPage);
		$this->parser->setParserVar('lastPage', $lastPage);
		
		// Array mit folgenden 3 Elementen wird zur�ckgegeben
		$paging = array ('pagingList' => array(), 'pagingNext', 'pagingPrev');
		
		for ($i = $firstPage; $i <= $lastPage; $i++) {

			$this->parser->setParserVar('page', $i);
			$paging['pagingList'][$i] = $this->parser->parse($params['templatePageLink']);

		}
		$paging['pagingList'] = implode($this->parser->parse($params['templateLinkSeparator']), $paging['pagingList']);
		
		// Ggf. Link vorwärts

		if ($params['currentPage'] < $totalPages) {
			$this->parser->setParserVar('nextPage', true);
		}
		$this->parser->setParserVar('page', intval($params['currentPage']) + 1);
		$paging['pagingNext']= $this->parser->parse($params['templateNextLink']);

		
		// Ggf. Link zurück
		if ($params['currentPage'] > 1) {
			$this->parser->setParserVar('prevPage', true);
		}
		$this->parser->setParserVar('page', intval($params['currentPage']) - 1);
		$paging['pagingPrev']= $this->parser->parse($params['templatePrevLink']);
		
		return $paging;
	
 	}
 	
 	public function setTable($tableName) {
 		$this->dbTable = $tableName;
 	}

	public function validateFormData($params=array()) {

		switch ($params['fieldType']) {

			case 'integer':
				return intval($params['value']);
				break;

			case 'float':
				return floatval($params['value']);
				break;

			case 'datetime':
				return sprintf("%04d-%02d-%02d %02d:%02d:%02d", $params['valueYear'], $params['valueMonth'], $params['valueDay'], $params['valueHour'], $params['valueMinute'], $params['valueSecond']);
				break;

			case 'date':
				return sprintf("%04d-%02d-%02d", $params['valueYear'], $params['valueMonth'], $params['valueDay']);
				break;

			case 'time':
				return sprintf("%02d:%02d:%02d", $params['valueHour'], $params['valueMinute'], $params['valueSecond']);
				break;

		}

	}
 }
?>
