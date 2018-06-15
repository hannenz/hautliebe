<?php
/**
 * class_gui.inc
 * Kurzbeschreibung
 * 
 * @author J.Hahn
 * @version 2014-04-14
 */

namespace Contentomat;

Class Gui {
	
	protected $templatePath;
	protected $tabsContent;
	protected $tabsPanelContent;
	protected $guiElementCounter;
	protected $tabCounter;
	protected $layerCounter;
	protected $templates;
	protected $templateCache;
	protected $parser;
	protected $pageHasTextEditors;
	
	public function __construct($params = array()) {
		
		$this->guiElementCounter = 0;
		$this->tabCounter = 0;
		$this->hasTextEditors = false;
		$this->templateCache = array();
		$this->tabsContent = '';
		$this->tabsPanelContent = '';
		
		$this->parser = new CMTParser();
		
		$this->templatePath = 'administration/gui/';
		
		if (!is_array($params['templates'])) {
			$params['templates'] = array();
		}
		
		$defaultTemplates = array(
			'tabPanel' => $this->templatePath.'tabs/panel.tpl',
			'tabSet' => $this->templatePath.'tabs/tabset.tpl',
			'tab' => $this->templatePath.'tabs/tab.tpl',
			'layer' => $this->templatePath.'layer/layer.tpl',
			'buttonCalendar' => $this->templatePath.'buttons/button_calendar.tpl',
			'buttonFileSelector' => $this->templatePath.'buttons/button_fileselector.tpl',
			'textEditor' => $this->templatePath.'texteditor/texteditor.tpl',
		);
		
		$this->templates = array_merge($defaultTemplates, $params['templates']);
	}
	
	/**
	 * public function makeTab()
	 * Erzeugt einen Reiter.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte, z.B. 'tabTitle' => 'Reitertitel'
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeTab($params=array()) {

		if (!is_array($params['vars'])) {
			$params['vars'] = array();
		}
		
		$this->guiElementCounter++;
		$this->tabCounter++;
				
		$this->parser->setParserVar('guiElementCounter', $this->guiElementCounter);
		$this->parser->setParserVar('tabCounter', $this->tabCounter);
		
		$this->parser->setMultipleParserVars($params['vars']);

		$tab = $this->parser->parse($this->getTemplate('tab'));
		$this->tabsContent .= $tab;
	
		return $tab;
	}

	/**
	 * public function makeTabPanel()
	 * Erzeugt den Inhaltsbereich eines Reiters.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte, z.B. 'panelContent' => 'Das ist der Inhalt des Reiters'
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeTabPanel($params=array()) {

		if (!is_array($params['vars'])) {
			$params['vars'] = array();
		}
		
		$this->parser->setMultipleParserVars($params['vars']);
		$panel = $this->parser->parse($this->getTemplate('tabPanel'));
//
		$this->tabsPanelContent .= $panel;
		
		return $panel;
	}
	
	/**
	 * public function makeTabSet()
	 * Erzeugt einen kompletten Bereich aus Reitern und deren Inhaltsbereichen.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */	
	public function makeTabSet($params=array()) {

		$this->parser->setParserVar('tabsContent', $this->tabsContent);
		$this->parser->setParserVar('tabsPanelContent', $this->tabsPanelContent);
		$this->parser->setMultipleParserVars($params['vars']);
		$this->tabCounter = 0;
		
		return $this->parser->parse($this->getTemplate('tabSet'));
	}

	/**
	 * public function makeLayer()
	 * Erzeugt einen "Layer", einen auf- und zuklappbaren Inhaltsbereich
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeLayer($params=array()) {
		
		$this->parser->setParserVar('guiElementCounter', $this->guiElementCounter);
		$this->parser->setMultipleParserVars($params['vars']);
		
		return $this->parser->parse($this->getTemplate('layer'));
	}

	/**
	 * public function makeCalendarButton()
	 * Erzeugt einen Knopf zur Einblendung eines Kalenders welches neben einem Datumsfeld steht.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeCalendarButton($params=array()) {

		$this->parser->setMultipleParserVars($params['vars']);
		return $this->parser->parse($this->getTemplate('buttonCalendar'));		
	}
	
	/**
	 * public function makeFileSelectorButton()
	 * Erzeugt einen Knopf zur Einblendung eines Kalenders welches neben einem Datumsfeld steht.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeFileSelectorButton($params=array()) {

		$this->parser->setMultipleParserVars($params['vars']);
		return $this->parser->parse($this->getTemplate('buttonFileSelector'));		
	}

	/**
	 * public function makeTextEditor()
	 * Erzeugt einen Texteditor.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function makeTextEditor($params=array()) {

		$this->parser->setParserVar('pageHasTextEditors', $this->hasTextEditors);
		
		$params['vars']['cmtFieldData'] = $this->parser->protectMacros($params['vars']['cmtFieldData']);
		$this->parser->setMultipleParserVars($params['vars']);

		// Damit das Javascript im Template nicht ein zweites Mal geladen wird
		$this->hasTextEditors = true;
		
		return $this->parser->parse($this->getTemplate('textEditor'));		
	}
	
	
	/**
	 * public function getTemplate()
	 * Liest den Inhalt einer Template-Datei.
	 *
	 * @param array $params Assoziatives Array mit folgenden möglichen Schlüssel/ Wert-Paaren:
	 * 'vars' => Assoziatives Array, enthält alle im Template verwendeten Werte
	 *
	 * @return string Geparstes Template, in der Regel das HTML
	 */
	public function getTemplate($templateName) {
		
		if (!$templateName) {
			return '';
		}
		
		if (!$this->templateCache[$templateName]) {
			$this->templateCache[$templateName] = $this->parser->getTemplate($this->templates[$templateName]);
		}
		
		return $this->templateCache[$templateName];

	}

	/**
	 * public function setTemplate()
	 * Speichert den Pfad einer Template-Datei im Objekt internen Cache.
	 *
	 * @param string $templateName Interner Name des Templates: Kurz halten und ohne Leerzeichen schreiben!
	 * @param string $templatePath Pfad der Template-Datei
	 * @param boolean $addGlobalPath Wenn "true" (default), dann wird dem Pfad der Template-Datei der allgemeine Template-Pfad vorangestellt.
	 *
	 * @return return_type
	 */
	public function setTemplate($templateName, $templatePath, $addGlobalPath = true) {
		
		if (!$addGlobalPath) {
			$this->templates[$templateName] = $templatePath;
		} else {
			$this->templates[$templateName] = $this->templatePath . $templatePath;
		}

		unset($this->templateCache[$templateName]);
	}
	
	
 }

?>