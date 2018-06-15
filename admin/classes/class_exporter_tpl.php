<?php

/**
 * class_Export_tpl.inc
 * Content o mat Export Template Class 
 * 
 * @version 2013-02-07
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */

class TplExporter extends Exporter{

	/**
	 * protected function getTable()
	 *
	 * get the data of given Table as XML string
	 *
	 * @param string $params

	 * @return string
	 */
	protected function getTable($params) {
		if (!$params['customQuery']) {
			$query = $this->buildQuery($params);
		} else {
			$query = $params['customQuery'];
		}

		$this->db->query($query);

		//$currentFieldInfo = $this->db->getCurrentFieldInfo();
		
		$rows = array();
		while($res = $this->db->get()){
			$rows[] = $res;
		}
		
		$params['rows']=$rows;
		
		return $this->parseRows($params);
		
	}
		
	
	/**
	 * protected function arrayToCsv()
	 * convert an array to csv
	 * 
	 * @param array $array, data
	 * @param string $header_row, first row for columens names
	 * @param string $col_sep, columens seperator
	 * @param string $row_sep, row lines seperator
	 * @param string $qut
	 * @return string, csv string
	 */
	protected function parseRows($params) {

		//Data rows.
		if ($params['exportData']) {
			$rowsContent = '';
			foreach ($params['rows'] as $row) {
				$this->parser->setMultipleParserVars($row);
				$rowsContent .= $this->parser->parseTemplate($params['templateRow']);
			}
			
		}
		$this->parser->setParserVar('rowsContent', $rowsContent);
		
		// Frame
		if ($params['exportStructure']) {
			$output = $this->parser->parseTemplate($params['templateFrame']);
		}else{
			$output = $rowsContent;
		}
		
		return $output;
	}
	
	/**
	 * protected function prepareData()
	 *
	 * build export tasks
	 * 
	 * @return array
	 */
	protected function prepareData($params=array()){
		$defaultParams = array(
			'filePrefix' => '',
			'fileName' => $this->date,
			'fileExtension' => 'txt',
		);

		$params = array_merge($defaultParams, $params);

		$exportTasks = array();
		
		$exportString = join("\n\n",$this->data['data']);
	
		if ($params['save']) {
			$exportTasks[] = array(
				'type' => 'save',
				'data' => $exportString,
				'fileName' => $params['filePrefix'] . $params['fileName'] . '.' . $params['fileExtension'],
				'filePath' => ''
			);
		}

		if ($params['download']) {
			$exportTasks[] = array(
				'type' => 'download',
				'data' => $exportString,
				'fileName' => $params['filePrefix'] . $params['fileName'] . '.' . $params['fileExtension']
			);
		}
	
		return $exportTasks;
	}
	
}