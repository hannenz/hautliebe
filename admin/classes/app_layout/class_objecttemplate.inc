<?php
/**
 * class_objecttemplate.inc
 * Class handles object templates for layout mode.
 *
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-09-21
 */
namespace Contentomat\Layout;

use \Contentomat\DBCex;

Class ObjectTemplate {

	protected $db;
	protected $objectTable = 'cmt_templates_objects';
	
	public function __construct() {
		$this->db = new DBCex();
	}
	
	/**
	 * function getById()
	 * Find an object template by its database id
	 * 
	 * @param number $templateId
	 * @return array
	 */
	public function getById($templateId) {
	
		$t = $this->get(array(
			'id' => intval($templateId)
		));
		
		$template = array_shift($t);
		if (is_array($template)) {
			return $template;
		} else {
			return array();
		}
	}
	
	/**
	 * function getList()
	 * Returns a list of availabel templates
	 * 
	 * @param array $params		Optional parameters for method ObjectTemplate::get()
	 * @return array
	 */
	public function getList($params=array()) {

		if (!is_array($params)) {
			$params = array();
		}
		$params['type'] = 'list';
		return $this->get($params);
	}
	
	/**
	 * function getSource()
	 * Returns the source code of the chosen template.
	 * 
	 * @param number $templateId
	 * @return
	 */
	public function getSource($templateId) {
	
		return array_shift($this->get(array(
			'type' => 'source',
			'id' => intval($templateId)
		)));
	}
	
	/**
	 * function get()
	 * Main method to get object templates
	 * 
	 * @param array $params
	 * @return array
	 */
	public function get($params=array()) {
	
		$defaultParams = array(
			'type' => 'raw',				// 'list' => id/template name pair for selects, 'source' => id/source, 'raw' => id/dataset
			'id' => 0,
//			'language' => CMT_LANGUAGE,		// not in use yet
			'pageTemplateId' => null,
			'where' => array()
		);
		$params = array_merge($defaultParams, $params);
	
		$objectTemplateId = intval($params['id']);
		$pageTemplateId = intval($params['pageTemplateId']);
	
		$this->db->query("SELECT cmt_template_object_ids FROM cmt_templates_pages WHERE id ='" . $pageTemplateId . "'");
		$r = $this->db->get();
		$templateObjectIds = $r['cmt_template_object_ids'];
		
		if ($pageTemplateId && $templateObjectIds) {
			$query = "SELECT ot.* FROM " . $this->objectTable . " ot 
					  WHERE ot.id IN(". $templateObjectIds .") ";
			$glue = " AND ";
		} else {
			$query = "SELECT ot.* FROM " . $this->objectTable . " ot ";
			$glue = " WHERE ";
		}
		
		$where = array();
	
		// get one template
		if ($objectTemplateId) {
			$where[] = "ot.id = '" . $objectTemplateId . "'";
		}
		
		$where = array_merge($where, (array)$params['where']);
	
		// build query
		if (!empty($where)) {
			$query .= $glue . implode(' AND ', $where);
		}
	
		// order results
		$query .= " ORDER BY ot.cmt_position ASC";
	
		$data = array();
		$result = array();
		
		$this->db->query($query);

		while($r = $this->db->get()) {
			
			$r['cmt_source'] = stripslashes($r['cmt_source']);
			$data[$r['id']] = $r;
		}

		switch($params['type']) {
				
			case 'source':
				foreach ((array)$data as $id => $value) {
					$result[$id] = $value['cmt_source'];
				}
	
				break;
	
			case 'list':
				foreach ((array)$data as $id => $value) {
					$result[$id] = $value['cmt_name'];
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