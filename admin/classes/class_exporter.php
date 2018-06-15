<?php

/**
 * class_Exporter.inc
 * Content o mat Export Class 
 * 
 * @version 2013-01-24
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */

class Exporter{
	protected $db;				// DB Class Instance
	protected $tablesToExport;	// Tables to be Exported
	protected $customQuery;		// custom export query
	protected $exportPath;		// Path to save exported files
	protected $data;			// An array of tables export in final format (xml, sql ...), each table as one entery 
	protected $date;			// date, to stamp file names with
	protected $settings;		// export settings
	protected $parser;
	/**
	 * public function setExportPath()
	 * 
	 * set the path where export files will saved
	 * 
	 * @param unknown $value
	 */
	public function setExportPath($value){
		$this->exportPath = $value;
	}
	
	/**
	 * public function getData()
	 * 
	 * getter to return the export data
	 * @return array
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * public function __construct()
	 *
	 * Constructor
	 */
	public function __construct() {
		$this->db = new  DBCex();
		$this->parser = new Parser();
		$this->settings = array();
		$this->exportPath = INCLUDEPATH.'export/'; // default export path
		$this->date= date('Y-m-d_His');
	}
	
	/**
	 * public function init()
	 *
	 *
	 * @param array $tables
	 */
	public function init($params){
		$settings = array(
			'exportData'=>true,
			'exportStructure'=>true,
			'exportCmtFields'=>true,
			'exportCmtTables'=>true,
		);
		
		foreach($settings as $option=>$value){
			if(isset($params[$option]) && $params[$option]==false){
				$settings[$option] = false;
			}
		}
		
		$this->settings = array_merge($this->settings,$settings);
		
		if($params['customQuery']){
			$this->settings['customQuery'] = $params['customQuery'];
		}
		
		if($params['templateFrame']){
			$this->settings['templateFrame'] = $params['templateFrame'];
		}
		
		if($params['templateRow']){
			$this->settings['templateRow'] = $params['templateRow'];
		}
		
		// init tables and fields to export
		$this->initTables($params['tables']);
	}
	
	/**
	 * public function init()
	 * 
	 * 
	 * @param array $tables
	 */
	public function initTables($tables=array()){
		if(!$tables || empty($tables)){
			$this->tablesToExport = $this->getAllDatabaseTables();
		}else{
			$this->tablesToExport = $this->getSelectedDatabaseTables($tables);
		}
	}
	
	/**
	 * protected function getAllDatabaseTables()
	 * return the list of all tables in the current DB
	 * 
	 * @return	array	List of all tables in the DB
	 */
	protected function getAllDatabaseTables(){
		$tables = $this->db->getAllTables();
		$exportTables = array();
		foreach($tables as $table){
			$fieldsToExport =  $this->getFieldsToExport($table, '');
			$exportTables[$table]= array('tableName'=>$table,'fields'=>$fieldsToExport);
			$exportTables[$table] = array_merge($this->settings, $exportTables[$table]);
			if($this->settings['customQuery']){
				$exportTables[$table]['customQuery']='';
			}
		}
		
		return $exportTables;
	}
	
	/**
	 * protected function getSelectedDatabaseTables()
	 * return the list of selected tables in the current DB
	 *
	 * @return	array	List of selected tables in the DB
	 */
	protected function getSelectedDatabaseTables($tables){
		$exportTables = array();
		if(!is_array($tables)){
			$tables = explode(',',$tables);
			foreach($tables as $table){
				$fieldsToExport =  $this->getFieldsToExport($table, '');
				$exportTables[$table]= array('tableName'=>$table,'fields'=>$fieldsToExport);
				$exportTables[$table] = array_merge($this->settings, $exportTables[$table]);
				if($this->settings['customQuery']){
					$exportTables[$table]['customQuery']='';
				}
			}
		}else{
			foreach($tables as $table=>$params){
				if(is_numeric($table) && is_string($params)){
					$table = $params;
				}
				if(!is_array($params)){
					$params = array();
				}
				$params['fields']= $this->getFieldsToExport($table, $params['fieldsWrapper']);
				$params['tableName']= $table;
				$exportTables[$table] = array_merge($this->settings, $params);
				if($this->settings['customQuery']){
					$exportTables[$table]['customQuery']='';
				}
			}
		}
		
		return $exportTables;
	}
	
	/**
	 * protected function getFieldsToExport()
	 *
	 * create a list of name=>alias of fields, to create a select
	 * section in export query later
	 *
	 * @param unknown $table
	 * @param unknown $fields
	 * @return multitype:
	 */
	protected function getFieldsToExport($table, $fields=''){
		$fieldsToExport = array();
		if(!$fields){
			$fieldsToExport = array();
			$fieldsInfo = $this->db->getFieldinfo($table);
			$fieldsToExport = $fieldsInfo['name'];
		}else{
			if(!is_array($fields)){
				$fields = explode(',',$fields);
			}
			foreach($fields as $key=>$value){
				if(is_numeric($key)){
					$key = $value;
				}
				$fieldsToExport[$key]=$value;
			}
		}
		return $fieldsToExport;
	}
	
	/**
	 * protected function createExportQuery()
	 *
	 * create the export query
	 *
	 * @param array $params
	 * @return string export query
	 */
	protected function buildQuery($params){
		
		$defaultParams = array(
			'orderBy' => 'id',
			'orderDir'=> 'ASC',
		);
		$params = array_merge($defaultParams,$params);

		$query = '';
	
		$whereClause = array();
	
		if(is_array($params['condWhere'])){
			$whereClause = array_merge($whereClause,$params['condWhere']);
		}
	
		$query .= 'SELECT ';
		$selectFields = array();
		foreach($params['fields'] as $name=>$alias){
			$selectFields[] = $params['tableName'].".".$name." AS ".$alias;
		}
		$query .= join(", ", $selectFields);
		$query .= " FROM ".$params['tableName'];
	
		if (!empty($whereClause)) {
			$query .= ' WHERE ' . join(" AND ", $whereClause);
		}
	
		if($params['orderBy']){
			$query .= " ORDER BY ".$params['tableName'].".".$params['orderBy']." ".$params['orderDir'];
		}
	
		return $query;
	}
	
	/**
	 * public function export()
	 *
	 * The main export funciton, can init tables and fields with params
	 *
	 * @param array $tables
	 * @param string $query, provide a custom query
	 * @return string, csv string
	 */
	public function export($params){

		// call the main method in extended classes to start the exporting process
		$this->work();
		
		$exportTasks = $this->prepareData($params);
		
		foreach($exportTasks as $task){
			if($task['type']=='save'){
				$this->saveFile($task);
			}
			
			if($task['type']=='download'){
				$this->downloadFile($task);
			}
		}
	}
	
	/**
	 * public function work()
	 *
	 * called by export method
	 * used data in $this->tablesToExport to create export content
	 * save created data in $this->data array
	 *
	 * @return boolean
	 */
	public function work(){
		
		$this->data = array();
		
		// custom Query
		if ($this->settings['customQuery']) {
			$this->data['data'][] = $this->getTable($this->settings);
			//  get sql query by creating it
		} else {
			foreach ($this->tablesToExport as $table => $params) {
				$this->data['data'][] = $this->getTable($params);
			}
		}
		
		if (!empty($this->data)) {
			return true;
		} else {
			return false;
		}
	}
	
		
	
	/**
	 * protected function prepareData()
	 *
	 * collect & format export data in $this->data to
	 * prepare it to the next step of export (save or download)
	 *
	 * @return string
	 */
	protected function prepareData($params=array()){
		return array();
	}
	
	
	/**
	 * protected function saveFile()
	 * 
	 * save the output to file (csv, or xml) 
	 * 
	 * @param array $params
	 */
	protected function saveFile($params){
		$fileName = $params['fileName'];
		$data = $params['data'];
		
		if($fileName){
			file_put_contents($this->exportPath.$fileName,$data);
		}
	}
	
	
	/**
	 * protected function saveFile()
	 * 
	 * save the output to file (csv, or xml) 
	 * 
	 * @param array $params
	 */
	protected function appendToFile($params){
		$fileName = $params['fileName'];
		$data = $params['data'];
		
		if($fileName){
			// ToDo: file write instade of file_put_contents function
			file_put_contents($this->exportPath.$fileName,$data);
		}
	}
	
	/**
	 * protected function downloadFile()
	 *
	 * save the output to file (csv, or xml)
	 *
	 * @param array $params
	 */
	protected function downloadFile($params){
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=".$params['fileName']);
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: binary");
		echo $params['data'];
		
	}
	
}


?>
