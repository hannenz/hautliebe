<?php

/**
 * class_Export_csv.inc
 * Content o mat Export XML Class
 *
 * @version 2013-02-04
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */
class XmlExporter extends Exporter {

	protected $xml;

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
		
		if(!$params['tableName']){
			$params['tableName'] = 'queryResults';
		}

		$this->db->query($query);

		$currentFieldInfo = $this->db->getCurrentFieldInfo();


		// create xml dom
		$dom = new DOMDocument("1.0");
		// add table element
		$tableElement = $dom->createElement('table');
		$dom->appendChild($tableElement);

		// add table attribute (name)
		$attrName = $dom->createAttribute("name");
		$tableElement->appendChild($attrName);
		$nameValue = $dom->createTextNode($params['tableName']);
		$attrName->appendChild($nameValue);

		$i = 1;

		while ($res = $this->db->get()) {
			if ($res['id']) {
				$rowId = $res['id'];
			} else {
				$rowId = $i;
			}
			$i++;

			// add row element
			$rowElement = $dom->createElement('row');
			$tableElement->appendChild($rowElement);

			$attrId = $dom->createAttribute("id");
			$rowElement->appendChild($attrId);
			$idValue = $dom->createTextNode($rowId);
			$attrId->appendChild($idValue);


			foreach ($res as $fieldName => $value) {
				$text = $value;
				//$text = preg_replace("/\r/", "\\r", $text);
				//$text = preg_replace("/\n/", "\\n", $text);
				// 
				// add field element
				$fieldElement = $dom->createElement('field');
				$rowElement->appendChild($fieldElement);

				$attrFieldName = $dom->createAttribute("name");
				$fieldElement->appendChild($attrFieldName);
				$fieldNameValue = $dom->createTextNode($fieldName);
				$attrFieldName->appendChild($fieldNameValue);

				if (in_array($currentFieldInfo['type'][$fieldName], array('text', 'blob', 'string'))) {
					$textValue = $dom->createCDATASection($text);
				} else {
					$textValue = $dom->createTextNode($text);
				}
				$fieldElement->appendChild($textValue);
			}
		}
		$tableXml = $dom->saveXML($tableElement);

		return $tableXml;
	}

	/**
	 * protected function prepareData()
	 *
	 * collect & format export data in $this->data to
	 * prepare it to the next step of export (save or download)
	 *
	 * @return string
	 */
	protected function prepareData($params = array()) {

		$defaultParams = array(
			'filePrefix' => '',
			'fileName' => $this->date,
			'fileExtension' => 'xml',
		);

		$params = array_merge($defaultParams, $params);


		$exportTasks = array();

		// tables string
		$tablesXmlString = join("\n\n", $this->data['data']);

		// XML Frame Template
		$exportXml = new SimpleXMLElement('<xml/>');
		$exportXml->addChild('tables', '{{TABLES}}');
		$exportString = $exportXml->asXML();


		$exportString = str_replace('{{TABLES}}', $tablesXmlString, $exportString);


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