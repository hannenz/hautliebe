<?php
/**
 * class table
 * 
 * Klasse zur Erstellung von HTML-Tabellen
 * 
 * Diese Klasse stellt die Grundwerkzeuge für die Erstellung von Tabellen in HTML zur Verfügung. TODO: Stark überarbeitunsbedürftig!!
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2017-05-14
 */

namespace Contentomat;


class Table {
	
	private $form;
	private $counter;
	private $entry_counter;
	private $alter_color;
	private $user;
	private $function_button;
	private $function_link;
	private $nl;
	
	// Konstruktor
	public function __construct() {
		$this->form = new Form();
		$this->counter = 0;
		$this->entry_counter = 0;
		$this->user = new User(SID);
		$this->function_button = array();
		$this->function_link = array();
		$this->nl = chr(10);
		return;	
	}

    public function TableStart ($width='', $border=0, $cellspacing=0, $cellpadding=0, $add_html='') {
    	if ($add_html) {
    		$add_html = ' '.$add_html;
    	}
    	$html .= '<table ';
    	if ($width) {
    		$html .= 'width="'.$width.'" ';
    	}
    	return $html.$add_html.'>';
    	
    	//$html .= 'border="'.$border.'" cellspacing="'.$cellspacing.'" cellpadding="'.$cellpadding.'"'.$add_html.'>'.$this->nl;
        //return $html;

    }
    
    public function TableEnd () {
        $html = '</table>'.$this->nl;
        return $html;
    }
    
	/**
	 * function bodyStart
	 * 
	 * Erzeugt Start-Tag für einen Tabellenkörper
	 * 
	 * @param $p Array Array mit Attributnamen als Schlüssel und -werten als Wert (z.B.['id'] = 'cmtTableBody1')
	 * @return String Gibt ein formatiertes HTML-Tag zurück
	 */
	public function bodyStart($p=array()) {
		foreach ($p as $attr=>$v) $addHtml .= ' '.$attr.'="'.$v.'"';
		return '<tbody'.$addHtml.'>';	
	}
	
	/**
	 * function bodyEnd
	 * 
	 * Erzeugt End-Tag für einen Tabellenkopf
	 * 
	 * @param Void Erwartet keine Parameter
	 * @return String Gibt ein formatiertes HTML-Tag zurück
	 */	
	public function bodyEnd() {
		return '</tbody>';	
	}

	/**
	 * function headStart
	 * 
	 * Erzeugt Start-Tag für einen Tabellenkopf
	 * 
	 * @param $p Array Array mit Attributnamen als Schlüssel und -werten als Wert (z.B.['id'] = 'cmtTableHead1')
	 * @return String Gibt ein formatiertes HTML-Tag zurück
	 */
	public function headStart($p=array()) {
		foreach ($p as $attr=>$v) $addHtml .= ' '.$attr.'="'.$v.'"';
		return '<thead'.$addHtml.'>';	
	}

	public function makeHead($p=array()) {
		$cols = $p['cols'];
/*		if (isset($col['cmt__id'])) {
			unset ($cols['cmt__id']);
		}
*/
        // Ausgabe beginnen
        $html = '<tr>'.$this->nl;

		foreach ($cols as $key=>$col) {
            $html .= '<td';
            
            // Zelle in Standardformatierung?
            switch ($key) {
            	case 'cmt__select':
            		$html .= ' class="tableSelectHeadCell"';
            		break;
            		
            	case 'cmt__functions':
            		$html .= ' class="tableActionHeadCell"';
            		break;
            		
            	case PRIMARY_KEY:
            		$html .= ' class="tablePrimaryKeyHeadCell"';
            		break;
            		
            	default:
            		$html .= ' class="tableDataHeadCell"';
            		break;
            }
            if ($p['width'][$key]) {
            	$html .=  ' width="'.$p['width'][$key].'"';
            }
            $html .= '>'.$col.'</td>';
        }

        return $html.'</tr>'.$this->nl;
	}
	
	/**
	 * function headEnd
	 * 
	 * Erzeugt End-Tag für einen Tabellenkopf
	 * 
	 * @param Void Erwartet keine Parameter
	 * @return String Gibt ein formatiertes HTML-Tag zurück
	 */		
	public function headEnd() {
		return '</thead>';	
	}

	// OUTDATED
    public function TableMakeRow ($content, $cols=0, $add_html='', $formatted=0, $edit=0) {

        // Feldvariablen formatieren
        if (gettype($content) != 'array') {
            $output[0] = $content;
        } else {
            $output = $content;
        }

        if (gettype($add_html) != 'array') {
            $output_html[0] = $add_html;
        } else {
            $output_html = $add_html;
        }

        if (gettype($cols) != 'array') {
            $output_cols[0] = $cols;
        } else {
            $output_cols = $cols;
        }
        // -> Feldvariablen formatieren Ende
        
        // NEU MACHEN!
        // Editfeld dazu?
        if ($edit && $output['cmt__id']) {
            $id = $output['cmt__id'];
            $add_query = '&key='.PRIMARY_KEY.'&id='.$id.'&cmt_dbtable='.CMT_DBTABLE.'&cmt_ipp='.CMT_IPP.'&cmt_pos='.CMT_POS;
            $output['cmt__funktionen'] = '<a href="'.SELFURL.'&action=edit'.$add_query.'"><img src="'.CMT_TEMPLATE.'img/icon_edit.gif" border="0" alt="bearbeiten"></a>&nbsp;
                         <a href="'.SELFURL.'&action=duplicate'.$add_query.'"><img src="'.CMT_TEMPLATE.'img/icon_duplicate.gif" border="0" alt="duplizieren"></a>&nbsp;
						<a href="'.SELFURL.'&action=delete'.$add_query.'" onClick="return del_confirm('.$id.')"><img src="'.CMT_TEMPLATE.'img/icon_delete.gif" border="0" alt="l&ouml;schen"></a>&nbsp;';
            unset ($output['cmt__id']);
        }

        // Automatische Formatierung?
        $auto_format = array ('head' => 'table_head',
                              'subhead' => 'table_subhead',
                              'data' => 'table_data',
                              'alternate' => 'table_data_alt',
                              'service' => 'table_service');
        if ($formatted) {
            if (in_array($formatted,array_keys($auto_format))) {
                $formatted = $auto_format[$formatted];
            }
        }
        if ($formatted == 'table_data_alt') {
        	if ($add_html == 'start_alternate') {
        		$output_html[0] = '';
        		$this->alter_color=0;
        	} 
            $alter_color = ($this->alter_color++)%2;
        }
        
        // Vorgeplänkel fertig, Ausgabe beginnen
        $html = '<tr>'.$this->nl;
        
        // Ausgabe
        foreach ($output as $key => $value) {
            $html .= '<td';
            
            // Zelle in Standardformatierung?
            if ($formatted && $key !== 'cmt__funktionen') {
                if ($key === PRIMARY_KEY) {
                    $html .= ' class="'.$formatted.'_pkey';
                } else {
                    $html .= ' class="'.$formatted;
                }

                if (stristr($formatted, 'table_data')) {
                    $html .= $alter_color.'" ';
                } else {
                    $html .= '"';
                }
            }
            
            if ($key === 'cmt__funktionen') {
                $html .= ' nowrap class="'.$formatted.'_func';
                if (stristr($formatted, 'table_data')) {
                    $html .= $alter_color.'" ';
                } else {
                    $html .= '"';

                }
            }
            
            // Spaltenerstreckung ausgeben
            if ($output_cols[$key]) {
                $html .= ' colspan="'.$output_cols[$key].'"';
            }

            // Zusätzliche HTML-Tags ausgeben
            if ($output_html[$key]) {
                $html .= ' '.$output_html[$key];
            }
            
            // Damit im Browser eine leere Zelle angezeigt wird, sofern der Wert nicht existiert
            if (trim($value) == '') {
                $value = '&nbsp;';
            }
            
            //Zellenwert ausgeben
            $html .= '>'.$this->nl.$value.'</td>'.$this->nl;
            $i++;
        }

        $html .= '</tr>'.$this->nl;
        return $html;
    }


	/**
	 * function makeDataRow
	 * erzeugt eine Tabellendatenzeile
	 * 
	 * Die Funktion erzeugt eine Standard-HTML-Tabellenzeile mit Auswahlfeld als erster Spalte und
	 * Funktionsfeld mit Funktionsbuttons als letzte Spalte, sofern in $params['functionButtons'] HTML
	 * übergeben wird.
	 * @param $params array Array, welches die folgenden Parameter (Name => Wert) beinhaltet:
	 * @param formatted string Formatierung der Zeile: 'alternate' => abwechselnder Zeilenhintergrund wird eingefügt, 'data' => normale Zeile
	 * @param functionButton array Array, welches das HTML für die Knopfaktionen enthält (z.B. $functionButton['edit'] => '<a href="edit.php">edit this</a>')
	 * @param cellClass array Array, welches optionale CSS-Klassennamen enthält, die der Formatierung der jeweiligen Zeile hinzugefügt werden (z.B. $cellClass[cell1] = 'cellWithBorder').
	 * @param cellHtml array Array, welches zusätzliches HTML für die jeweilge Zelle enthält (z.B. $cellHtml['cell1'] = 'style="border: 1px"')
	 * 
	 * @return string HTML-Quelltext der Tabellenzeile ('<tr>Datenzellen hier</tr>')
	 */	
												  
    public function makeDataRow($params) {

		/*
		 * Funktions-Knöpfe erstellen
		 * 
		 * Beginnt ein Knopf mit '<' wird angenommen, dass es sich um einen Link / eine verlinkte Grafik handelt 
		 * (z.B. definiert innerhalb einer onShowRow-Aktion im Codemanager). Eine Rechteprüfung findet hier nicht 
		 * mehr statt. Diese muss in der Applikation geschehen.
		 */ 
		$htmlFunctions = array();

		if (is_array($params['functionButton'])) {

			foreach ($params['functionButton'] as $buttonAction => $buttonValue) {
				$buttonValue = trim($buttonValue);
				$buttonValue = str_replace(array('&amp;', '&'), array('&', '&amp;'), $buttonValue);

				// Handelt es sich bei dem Wert um HTML?

				if (preg_match('/^\<.*\>/', $buttonValue)) {
					$htmlFunctions[] = $buttonValue;
				} 

			}

		}

		/*
		 * Funktionsfelder bearbeiten
		 * 
		 * Im Array $content werden die ID, die Auswahlfeld- und die Funktionsspalte mit den Schlüsseln
		 * 'cmt__id', 'cmmt__select' und 'cmt__functions' mitgegeben
		 */
		
		/*
		 * Datensatz ausgeben
		 */
        $cellClassWrapper = array ('data' => 'tableDataCell',
 								'alternate' => 'tableDataCell',
								'service' => 'tableActionCell');
	
		// 1. Automatische Formatierung?
        if ($params['formatted']) {
            $formatType = $params['formatted'];
            if (in_array($params['formatted'],array_keys($cellClassWrapper))) {
				$params['formatted'] = $cellClassWrapper[$params['formatted']];
            }
        }
		if ($formatType == 'alternate') {
			$alterColor = intval(($this->alter_color++)%2);
		}

		if (!is_array($params['content'])) {
			$params['content'] = array();
		}

		foreach ($params['content'] as $field => $value) {
			
			// Unterscheidung nach Feldtype; Auswahlfeld, Datenfeld, Funktionsknopffeld
			switch ($field) {
				
				// Wird nicht mehr benötigt!
				case 'cmt__select':
					$classHtml = '<td class="tableSelectCell'.$alterColor;
					
					// Buttons und Rest ausegebn
					$valueHtml = '>'.$value.'</td>';
				break;	

				case 'cmt__functions':
					$classHtml = '<td class="cmtTableFunctions cmtAlternate' . $alterColor . ' cmtSelectableDontSelect tableActionCell'.$alterColor;
	
					// Buttons und Rest ausegebn
					$valueHtml = '">'.implode('', $htmlFunctions).'</td>';
				break;

            	case PRIMARY_KEY:
            		$classHtml = '<td class="cmtTablePrimaryKey cmtAlternate' . $alterColor . ' tablePrimaryKeyCell'.$alterColor;
            		
            		// Primary Key und Rest ausgeben
            		$valueHtml = '">'.$value.'</td>';
            		break;
				
				default:			
					$classHtml = '<td class="cmtTableData cmtAlternate' . $alterColor . ' ' . $params['formatted'].$alterColor;
					
					// Wert und Rest ausegebn
					$valueHtml = '">' . $value . '</td>';
//					$valueHtml = '">' . str_replace(array('{', '}'), array('&#123;', '&#125;'), $value) . '</td>';

				break;
			}
			
			// Zellen-HTML zusammenbauen
			// 1. Start-Tag und spezifische CSS-Klasse
			$html .= $classHtml;
		
			// 2. zusätzliche CSS-Formatierung per class?
			if ($params['cellClass'][$field]) {
				$html .= ' '.$params['cellClass'][$field];
			}
			//$html .= '"';
			
			// 3. zusätzliches HTML?
			if ($params['cellHtml'][$field]) {
				$html .= ' '.$params['cellHtml'][$field];
			}
			
			// 4. Abschliend: Wert anzeigen.
			$html .= $valueHtml;
		}
		
		$rowClass = trim('cmtHover ' . $params['rowClass']);

		if ($html) {
			return '<tr class="' .$rowClass . '" id="entry-' . (int)$params['content']['id'] . '">'.$html.'</tr>';
		}
	} 

	// OUTDATED(?)
    public function TableMakeDataRow ($content, $add_html='', $formatted='alternate', $user_functions=array(), $add_class='') {
		
		// Variablen / Arrays
		$output = array();
		$output_html = array();
		$output_cols = array();
		$output_funcs = '';
		
		//unset($this->function_button);
		//$this->function_button = array();		
		unset($this->function_link);
		$this->function_link = array();

		if (!is_array($user_functions)) {
			$user_functions = array();	
		}
		
		if (isset($content['cmt__id'])) {
			$id = $content['cmt__id'];
			unset ($content['cmt__id']);
			
            $functions = $this->TableMakeAllEditButtons($id, $user_functions);

            if (is_array($functions)) {
				$output_funcs = implode ('', $functions);
            }
			
			// Auswahl-Checkbox einfügen, wenn die Reihe bearbeitet werden darf
			if ($output_funcs != '') {
				if ($this->user->checkUserPermission('delete') || $this->user->checkUserPermission('duplicate')) {
					$output['cmt__select'] = $this->form->FormCheckbox('id['.$this->counter.']', 0, $id);
				} else {
					$output['cmt__select'] = '&nbsp;';
				}
				$this->counter++;
				$output = array_merge ($output, $content);
				$output['cmt__functions'] = $output_funcs;
			} else {
				$output['cmt__select'] = '&nbsp;';
				$output = array_merge ($output, $content);
				$output['cmt__functions'] = '&nbsp;';
			}
			$this->entry_counter++;
		}  else {
				$output['cmt__select'] = '&nbsp;';
				$output = array_merge ($output, $content);
		}
		
        if (gettype($add_html) != 'array') {
            $output_html[] = $add_html;
        } else {
        	$output_html = array_merge ($output_html, $add_html);
        }

// ????
        if (gettype($cols) != 'array') {
            $output_cols[] = $cols;
        } else {
        	$output_cols = array_merge ($output_cols, $add_html);
        }
        
        if (!is_array($add_html)) $add_html[] = '';
        if (!is_array($add_html)) $add_html[] = '';
        // -> Feldvariablen formatieren Ende


        // Automatische Formatierung?
        $cellClassWrapper = array ('head' => 'table_head',
                              'subhead' => 'table_subhead',
                              'data' => 'tableDataCell',
                              'alternate' => 'tableDataCell',
                              'service' => 'tableActionCell');
        if ($formatted) {
            $formatType = $formatted;
            if (in_array($formatted,array_keys($cellClassWrapper))) {
                $formatted = $cellClassWrapper[$formatted];
            }
        }
        if ($formatType == 'alternate') {
        	if ($add_html == 'start_alternate') {
        		$output_html[0] = '';
        		$this->alter_color=0;
        	} 
            $alter_color = intval(($this->alter_color++)%2);
        }
        // -> automatische Formatierung fertig ...
        
        // Ausgabe beginnen
        $html = '<tr>'.$this->nl;
    	
        // Ausgabe
        foreach ($output as $key => $value) {
            $html .= '<td';
            
            // Zelle in Standardformatierung?
            switch ($key) {
            	case 'cmt__select':
            		$html .= ' nowrap class="tableSelectCell';
            		break;
            		
            	case 'cmt__functions':
            		$html .= ' nowrap="nowrap" class="tableActionCell';
            		break;
            		
            	case PRIMARY_KEY:
            		$html .= ' class="tablePrimaryKeyCell';
            		break;
            		
            	default:
            		$html .= ' class="'.$formatted;
            		break;
            }
/*
            if ($key && ($key == 'cmt__functions' || $key == 'cmt__select')) {
            	$html .= ' nowrap class="'.$formatted.'_func';
        	} else if ($key && $key == PRIMARY_KEY) {
                $html .= ' class="'.$formatted.'_pkey';
            } else {
                $html .= ' class="'.$formatted;
            }
*/            
            if ($formatType =='alternate') {
                $html .= $alter_color.'" ';
            } else {
                $html .= '"';
            }
         
      
            // Spaltenerstreckung ausgeben
            if ($output_cols[$key]) {
                $html .= ' colspan="'.$output_cols[$key].'"';
            }

            // Zusätzliche HTML-Tags ausgeben
            if ($output_html[$key]) {
                $html .= ' '.$output_html[$key];
            }
            
            // Damit im Browser eine leere Zelle angezeigt wird, sofern der Wert nicht existiert
            if (trim($value) == '') {
                $value = '&nbsp;';
            }
            
            //Zellenwert ausgeben
            $html .= '>'.$this->nl.$value.'</td>'.$this->nl;
            $i++;
        }

        $html .= '</tr>'.$this->nl;
        return $html;
    }

	// OUTDATED Erstellt eine Tabelle mit Reitern
	public function TableMakeSliders ($content, $is_active=1, $add_html='', $type='service') {

        // Feldvariablen formatieren
        if (gettype($content) != 'array') {
            $output_html[0] = $content;
        } else {
            $output_html = $content;
        }

        if (gettype($add_html) != 'array') {
            $output_add[0] = $add_html;
        } else {
            $output_add = $add_html;
        }		
		
		$html = '<table cellspacing="0" cellpadding="0" border="0">'.$this->nl.'<tr>'.$this->nl;
		$cells = count($content);
		
		$act_cell = 1;
		
		foreach ($output_html as $cell=>$cell_content) {
			if ($is_active == $act_cell) {
				$active_html .= '_active';
			} else {
				$active_html = '';
			}
			$html .= '<td class="table_slider_'.$type.$active_html.'" '.$output_add[$cell].' nowrap>'.$cell_content.'</td>'.$this->nl;
			if ($is_active <= $act_cell || $is_active - 1 > $act_cell) {
				$html .= '<td class="table_slider_seperator_'.$type.$active_html.'"><img src="'.CMT_TEMPLATE.'img/table_slider_seperator_'.$type.$active_html.'.gif"></td>'.$this->nl;
			} else {
				$html .= '<td class="table_slider_seperator_left_'.$type.'"><img src="'.CMT_TEMPLATE.'img/table_slider_seperator_left_edge_'.$type.'.gif"></td>'.$this->nl;
			}
			$act_cell++;
		}
		$html .= '<td class="table_slider_'.$type.'" width="100%">&nbsp;</td></tr>'.$this->nl.'</table>'.$this->nl;
		
		return $html;
		
	}
	
	// OUTDATED
	public function TableMakeAllEditButtons ($id, $user_functions) {
		
		$entry_nr = $this->entry_counter + (CMT_POS - 1) * CMT_IPP;
		$add_query = '&key='.PRIMARY_KEY.'&id[]='.$id.'&cmt_dbtable='.CMT_DBTABLE.'&cmt_ipp='.CMT_IPP.'&cmt_pos='.CMT_POS.'&entry_nr='.$entry_nr;		
		
		// Rechte holen
		//global $cmt_userrights;
		//$add_javascript['delete'] =  ' class="cmtDialog cmtDialogConfirm" onClick="return del_confirm('.$id.')"';
		$add_javascript['delete'] =  ' class="cmtDialog cmtDialogConfirm" ';

		// Knöpfe ohne Rechte ausblenden
		foreach ($user_functions as $user_action => $value) {
			if ($this->user->checkUserPermission($user_action)) {
				$this->function_link[$user_action] = SELFURL.'&action='.$user_action.$add_query;
				//$this->function_button[$user_action] = '<a href="'.$this->function_link[$user_action].'"'.$add_javascript[$user_action].'><img src="'.CMT_TEMPLATE.'img/icon_'.$user_action.'.gif" border="0" alt=""></a>';
				$this->function_button[$user_action] = '<a href="'.$this->function_link[$user_action].'"'.$add_javascript[$user_action].'><img src="'.$this->function_button[$user_action].'" class="tableActionCellButton" border="0" alt=""></a>';
				$functions[$user_action] = $this->function_button[$user_action];
			} else {
				unset ($user_functions[$user_action]);
			}
		}
		return $functions;
	}
	
	/**
	 * public function makeAllEditButtons()
	 * OUTDATED! Erzeugt alle Knöpfe zur Bearbeitung eines Eintrags. 
	 *
	 * @param array $params Assoziatives Array mit den nötigen Parametern.
	 * - url => Basis-URL
	 * - id => ID des Eintrags
	 * - cmtPos => Position des Eintrags in der Query-Reihenfolge
	 * - cmtIPP => Anzahl der Einträge pro Seite
	 * - cmtDBTable => Name der aktuellen Datenbanktabelle
	 * - primaryKey => Primärschlüssel (? wofür?)
	 * - buttonTypes => assoziatives Array mit den verschiedenen Knopfarten (war früher $cmt_userfunctions)
	 * 
	 * @return array Assoziatives Array mit den Links
	 */
	public function makeAllEditButtons ($params) {
		
		$defaultParams = array(
			'primaryKey' => 'id',
			'cmtIPP' => 10,
			'buttonTypes' => array('edit', 'duplicate', 'delete')
		);
		
		$params['cmtPos'] = intval($params['cmtPos']);
		$params['cmtIPP'] = intval($params['cmtIPP']);
		
//		if (!isset($params['primary_key'])) {
//			
//			// Konstanten verwenden ist schlecht!
//			if (defined('PRIMARY_KEY')) {
//				$params['primary_key'] = PRIMARY_KEY;
//			} else {
//				$params['primary_key'] = 'id';
//			}
//		}
		
		if (!is_array($params['buttonTypes'])) {
			return array();
		}
		
		$entry_nr = $this->entry_counter + ($params['cmtPos'] - 1) * $params['cmtIPP'];
		$add_query = '&key=' . $params['primary_key'] . '&id[]=' . $params['id'] . '&cmt_dbtable=' . $params['cmtDBTable'] . 
					 '&cmt_ipp=' . $params['cmtIPP'] . '&cmt_pos=' . $params['cmtPos'] . '&entry_nr=' . $entry_nr;		
		
		$add_javascript['delete'] =  ' class="cmtDialog cmtDialogConfirm" ';

		
		// Knöpfe ohne Rechte ausblenden
		foreach ($params['buttonTypes'] as $buttonType => $value) {

			$this->function_link[$buttonType] = $params['url'] . '&action='.$buttonType.$add_query;
			
			$this->function_button[$buttonType] = '<a href="'.$this->function_link[$buttonType] . '"' . $add_javascript[$buttonType] . '><img src="' . 
												  $this->function_button[$buttonType] . '" class="tableActionCellButton" border="0" alt=""></a>';
												  
			$functions[$buttonType] = $this->function_button[$buttonType];
			
		}
		return $functions;
	}
	
	public function TableMakeEditLink ($function) {
		return $this->function_link[$function];	
	} 

	public function TableMakeEditButton ($function) {
		return $this->function_button[$function];	
	}
}