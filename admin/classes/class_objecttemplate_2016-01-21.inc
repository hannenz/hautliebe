<?php
namespace Contentomat\Layout;

// use \Contentomat\LayoutParser;
use \Contentomat\DBCex;

Class ObjectTemplate {

	protected $db;
//	protected $parser;
	
	public function __construct() {
		
		$this->db = new DBCex();
//		$this->parser = new LayoutParser();
	}
	
	public function getById($templateId) {
	
		return $this->get(array(
			'id' => intval($templateId)
		));
	}
	
	public function getList() {

		return $this->get(array(
			'type' => 'list'
		));
	}
	
	public function getSource($templateId) {
	
		return $this->get(array(
			'type' => 'source',
			'id' => intval($templateId)
		));
	}
	
	public function get($params=array()) {
	
		$defaultParams = array(
			'type' => 'raw',				// 'list' => id/template name pair for selects, 'source' => id/source, 'raw' => id/dataset
			'id' => 0,
			'language' => CMT_LANGUAGE,		// not in use yet
			'pageTemplateId' => null			// not in use yet
		);
		$params = array_merge($defaultParams, $params);
	
		$objectTemplateId = intval($params['id']);
		$pageTemplateId = intval($params['paheTemplateId']);
		
		if ($pageTemplateId) {
			$query = "SELECT ot.* FROM cmt_templates_objects ot 
					  JOIN cmt_templates_pages pt 
					  WHERE ot.id IN(pt.cmt_template_object_ids) ";
			
			$glue = " AND ";
		} else {
			$query = "SELECT ot.* FROM cmt_templates_objects ";
			$glue = " AND ";
		}
		
		$where = array();
	
		// get one template
		if ($objectTemplateId) {
			$where[] = "ot.id = '" . $objectTemplateId . "'";
		}
	
		// build query
		if (!empty($where)) {
			$query .= $glue . implode(' AND ', $where);
		}
	
		// order results
		$query .= " ORDER BY ot.cmt_position ASC";
	
		$this->db->query($query);
		$data = $this->db->getAll();
	
		$result = array();
	
		switch($params['type']) {
				
			case 'source':
				foreach ((array)$data as $id => $value) {
					$result[$id] = $value['cmt_source'];
				}
	
				break;
	
			case 'list':
				foreach ((array)$data as $id => $value) {
					$result[$id] = $value['cmt_title'];
				}
	
				break;
	
			default:
				$result = $data;
				break;
		}
		return $result;
	}
	
}
?>