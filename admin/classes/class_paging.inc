<?php
/**
 * class_paging.inc
 * Erzeugt ein Paging aus den übergebenen Parametern
 * 
 * @version 2016-09-21
 * @author J.Hahn <info@contentomat.de>
 */

namespace Contentomat;

Class Paging {
	
	public function __construct() {
		
	}
	
	public function makePaging($params) {
		
		$defaultParams = array(
			'pagingLinks' => 10,
			'entriesPerPage' => 10,
			'totalEntries' => 0,
			'currentPage' => 1
		);
		
		$params = array_merge($defaultParams, $params);
		
		$params['pagingLinks'] = intval($params['pagingLinks']);
		$params['totalEntries'] = intval($params['totalEntries']);
		
		$prev = array();
		$next = array();
		$pages = array();
		$allPages = array();
		$parserPages = array();
		
		// Gesamtseiten
		$totalPages = intval(ceil($params['totalEntries'] / $params['entriesPerPage']));
		if (!$totalPages) {
			$totalPages = 1;
		}

		// Aktuelle Seite
		$currentPage = intval($params['currentPage']);
		if (!$currentPage) {
			$currentPage = 1;
		} else if ($currentPage > $totalPages) {
			$currentPage = $totalPages;
		}
		
		// Links für Paging vorwärts berechnen
		$totalNext = $totalPages - $params['currentPage'];

		$range = $params['pagingLinks'];
		$c = 1;
		
		while ($totalNext >= $range) {
			
			$next[] = pow($range, $c++);
			$totalNext = $totalNext / $range;
		}
		
		// Links für Paging zurück berechnen
		$totalPrev = $params['currentPage'];
		$range = $params['pagingLinks'];
		$c = 1;
		
		while ($totalPrev > $range) {
			
			$prev[] = pow($range, $c++);
			$totalPrev = $totalPrev / $range;
		}
		$prev = array_reverse($prev);
		
		// Paginglinks berechnen
		$startPage = floor($params['currentPage'] / $range) * $range;
		if (!$startPage) {
			$startPage = 1;
		}
		
		$endPage = $startPage + $range - 1 ;
	
		if ($endPage > $totalPages) {
			$endPage = $totalPages;
		}
		
		for ($i = $startPage; $i <= $endPage; $i++) {
			$pages[] = intval($i);
			$parserPages[] = array(
				'pageNr' => intval($i)
			);
		}

		// vorherige und nächste Spanne berechnen
		if ($startPage - $range > 0) {
			$prevRange = intval($startPage - $range); 
		} else {
			$prevRange = false;
		}
		
		if ($startPage + $range <= $totalPages) {
			$nextRange = intval($startPage + $range); 
		} else {
			$nextRange = false;
		}
		
		// vorherige und nächste Seite berechnen
		if ($params['currentPage'] < $totalPages) {
			$nextPage = $params['currentPage'] + 1; 
		} else {
			$nextPage = false;
		}
		
		if ($params['currentPage'] > 1) {
			$prevPage = $params['currentPage'] - 1; 
		} else {
			$prevPage = false;
		}
		
		// alle Seiten
		for ($i=1; $i <= $totalPages; $i++) {
			$allPages[] = $i;
		}
		
		$paging = array(
			'prev' => $prev,
			'next' => $next,
			'pages' => $pages,
			'totalPages' => $totalPages,
			'currentPage' => $currentPage,
			'nextPage' => $nextPage,
			'prevPage' => $prevPage,
			'nextRange' => $nextRange,
			'prevRange' => $prevRange,
			'rangeStart' => $startPage,
			'rangeEnd' => $endPage,
			'allPages' => $allPages,
			'parserPages' => $parserPages
		);

		return $paging;
		
	}
	
}
?>