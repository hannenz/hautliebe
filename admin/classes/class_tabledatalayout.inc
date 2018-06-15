<?php 

namespace Contentomat;

class TableDataLayout {
	
	protected $tableId;
	protected $entryId;
	
	protected $dataLayoutTable;
	protected $templatesTable;
	
	protected $db;
	
	public function __construct() {
		
		$this->dataLayoutTable = 'cmt_tables_data_layout';
		$this->templatesTable = 'cmt_templates_data_layout';
		
		$this->db = new DBCex();
	}
	
	/**
	 * function get()
	 * Get a data layout record.
	 * 
	 * @param number $tableId	Table's database id
	 * @param number $entryId	Data entry's id
	 * 
	 * @return array	Conmplete dataset of selected data layout
	 */
	public function get($tableId, $entryId) {
		
		$this->db->query("SELECT * FROM " . $this->dataLayoutTable . " WHERE layout_table_id = '" . intval($tableId) . "' AND layout_entry_id = '" . intval($entryId) . "'");
		return $this->db->get();
	}
	
	/**
	 * function save()
	 * Save the template ids for a database record
	 * 
	 * @param number $tableId		Table's database id
	 * @param number $entryId		Data entry's id
	 * @param unknown $templateIds	Array or string (comma separated) of template ids
	 * 
	 * @return bool
	 */
	public function save($tableId, $entryId, $templateIds) {
	
		if (is_array($templateIds)) {
			$templateIds = implode(',', $templateIds);
		}
		
		$layoutExists = $this->get($tableId, $entryId);
		
		if (empty($layoutExists)) {
			$query = "INSERT INTO " . $this->dataLayoutTable . " SET 
					  layout_table_id = '" . intval($tableId) . "',
					  layout_entry_id = '" . intval($entryId) . "',
					  layout_template_ids = '" . $this->db->dbQuote($templateIds) . "'";
		} else {
			$query = "UPDATE " . $this->dataLayoutTable . " SET
					  layout_template_ids = '" . $this->db->dbQuote($templateIds) . "' 
					  WHERE layout_table_id = '" . intval($tableId) . "' AND layout_entry_id = '" . intval($entryId) . "'";
		}
		
		return !(bool)$this->db->query($query);
	}
	
	/**
	 * function activate()
	 * Workaround/ helper: sets the entry id for records with "layout_entry_id" = 0
	 * 
	 * @param number $tableId	Table's database id
	 * @param number $entryId	Data entry's id
	 * 
	 * @return bool
	 */
	public function activate($tableId, $entryId) {
		
		return !(bool)$this->db->query("
			UPDATE " . $this->dataLayoutTable ." 
			SET layout_entry_id = '" . intval($entryId) ."' 
			WHERE layout_table_id = '" . intval($tableId) . "' 
			AND layout_entry_id = '0' LIMIT 1
		");
	}

	/**
	 * function delete()
	 * Delete a layout record by its table and entry ids
	 *
	 * @param number $tableId	Table's database id
	 * @param number $entryId	Data entry's id
	 *
	 * @return bool
	 */
	public function delete($tableId, $entryId) {
		
		return !(bool)$this->db->query("
			DELETE FROM " . $this->dataLayoutTable ."
			WHERE layout_table_id = '" . intval($tableId) . "' 
			AND layout_entry_id = '" . intval($entryId) ."'
		");
	}
	
	/**
	 * function cleanUpDataLayoutTable()
	 * Helper/ workaround for maintenance reasons: delete all entries with layout_entry_id = '0'
	 * 
	 * @param void
	 * @return bool
	 *
	 */
	public function cleanUpDataLayoutTable() {
	
		return !(bool)$this->db->query("
			DELETE FROM " . $this->dataLayoutTable ."
			WHERE layout_entry_id = '0'
		");
	}
	
	/**
	 * public function getWithTemplates()
	 * Return the data layout with its templates (source code) in an array
	 * 
	 * @param number $tableId
	 * @param nubmer $entryId
	 * 
	 * @return array	The layout dataset extended with the templates source code in an array (key: 'templates')
	 */
	public function getWithTemplates($tableId, $entryId) {
		
		$layoutData = $this->get($tableId, $entryId);
		
		$templateIds = explode(',', $layoutData['layout_template_ids']);
		$templates = array();

		foreach($templateIds as $templateId) {
			
			$this->db->query("
				SELECT * FROM " . $this->templatesTable . " 
				WHERE id = '" . intval($templateId) . "'
				AND '" . intval($tableId) ."' IN(cmt_table_ids)
			");
			$r = $this->db->get();

			$templates[] = $r;
		}
		
		$layoutData['templates'] = $templates;
		
		return $layoutData;
		
	}
	
	/**
	 * public function getAvailableTemplates()
	 * Gets all available templates for the selected database table.
	 * 
	 * @param number $tableId
	 * @return array			The available template's datasets in an array.
	 */
	public function getAvailableTemplates($tableId) {
		
		$this->db->query("
			SELECT * FROM " . $this->templatesTable ." 
			WHERE '" . intval($tableId) . "' IN(cmt_table_ids) 
			ORDER BY cmt_position ASC
		");
		
		return $this->db->getAll(false);
	}
	
	
}
