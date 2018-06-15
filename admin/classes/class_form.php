<?php
/**
 * class_form.php - Formularklasse
 * 
 * Bietet Methoden zur Erzeugung von HTML-Formularen und Formularfeldern an.
 * 2009-01-14: iese KLasse ist stark �berarbeitungsbed�rftig!!!!
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2013-11-25
 */

namespace Contentomat;

class Form {

    public $form_nr;
    public $select_nr;
    public $textfield_nr;
    public $textarea_nr;
    public $checkbox_nr;
    public $nl;


	public function __construct() {
	    $this->form_nr = 1;
	    $this->select_nr = 1;
	    $this->textfield_nr = 1;
	    $this->textarea_nr = 1;
	    $this->checkbox_nr = 1;
		$this->nl = chr(10);	
	}

/* -- Start: neue Methoden -- */
	/**
	 * public function select() 
	 * Erzeugt ein Select-Auswahlfeld. 
	 * Erwartet Parameter in einem Array.
	 * 
	 * @param string name			Select-Attribut 'name'
	 * @param string id				Select-Attribut 'id'
	 * @param string size			Select-Attribut 'size'
	 * @param string addHtml		Zusätzliches HTML für Select-Tag
	 * @param string optionsOnly	Nur die Option-Tags werden zur�ckgegeben, ohne umschließendes Select-Tag
	 * @param string values			Werte für Option-Tags
	 * @param string aliases		Aliase für Options Tags. Optional, falls nicht vorhanden, werden die Option-Werte angezeigt
	 * @param mixed selected		Ausgewählter Option-Wert
	 * @param string multiple		Mehrfachauswahl möglich.
	 * @param array nestedData		Statt values und aliases kann mit diesem Parameter ein assoziatives, multidimensionales Array übergeben werden. Es wird eine Auswahlliste mit <optgroups> erstellt.
	 * 
	 * @return string				XHTML
	 */
	public function select($params=array()) {
 		
 		// Default Einstellungen
 		$defaultParams = array (
			'name' => '',
			'id' => '',
			'size' => 1,
			'addHtml' => '',
			'optionsOnly' => false,
			'values' => array(),
			'aliases' => array(),
			'selected' => '',
			'multiple' => false,
			'replaceEntities' => true,
 			'nestedData' => array(),
 			'addRequestDummy' => false,
 			'addRequestDummyValue' => '_cmtRequestEmpty'
 		);
 		$params = array_merge ($defaultParams, $params);

 		if (!is_array($params['values'])) {
 			$params['values'] = array();
 		}
 		
		// Ausgewählte(s) Option(en)
		if (!is_array($params['selected'])) {
			$params['selected'] = array((string)$params['selected']);
		}
 		
 		// ENTWEDER: Erzeugt normale Auswahlliste
		if (empty($params['nestedData'])) {
			// Keine Aliase?
	 		if (empty($params['aliases'])) {
	 			$params['aliases'] = $params['values'];
	 		}
	
			// Bearbeiten
			if ($params['replaceEntities'] !== false) {
				$params['aliases'] = $this->arrayHtmlEntities($params['aliases']);
			}
	
	 		$options = '';
	 		$position = 0;
	 		
	 		// Nur für Mehrfachauswahlen:
	 		$positions = array_flip($params['selected']);
	 		
	 		// Do we need a dummy option to transport field in REQUEST?
	 		if ($params['multiple'] && $params['addRequestDummy']) {
	 			$options = '<option value="' . $params['addRequestDummyValue'] . '" selected="selected" style="display: none"></option>';
	 		}
	 		
	 		foreach ($params['values'] as $key => $value) {
	 			$options .= '<option value="' . htmlspecialchars($value, ENT_COMPAT, CHARSET) . '"';
	 			if (in_array($value, $params['selected'])) {
	 				$options .= ' selected="selected"';
	
	 				if ($params['multiple']) {
	 					$options .= ' data-selected-position="' . $positions[$value] .'"';	
	 				}
	 			}
				
	 			$options .= '>'.$params['aliases'][$key].'</option>';
	 		}
		} else {
		// ODER: Erzeugt Auswahlliste mit <optgroup>

			foreach ($params['nestedData'] as $optGroup) {
				
				//$optGroupName = trim(htmlspecialchars($optGroup['name'], ENT_COMPAT, CHARSET));
				$optGroupLabel = trim(htmlspecialchars($optGroup['label'], ENT_COMPAT, CHARSET));
				
//				if (!$optGroupLabel) {
//					$optGroupLabel = $optGroupName;
//				}
				
				$options .= '<optgroup label="' . $optGroupLabel . '"';
				
				if ($optGroup['disabled']) {
					$options .= ' disabled="disabled"';
				}
				$options .= '>';
				
				if (!is_array($optGroup['options'])) {
					continue;
				}
				foreach ($optGroup['options'] as $optGroupOption) {
		 			
					$value =  htmlspecialchars($optGroupOption['value'], ENT_COMPAT, CHARSET);
					
					if ($optGroupOption['alias']) {
						$alias = htmlspecialchars($optGroupOption['alias'], ENT_COMPAT, CHARSET);
					} else {
						$alias = $value;
					}
					
					$options .= '<option value="' . htmlspecialchars($value, ENT_COMPAT, CHARSET) . '"';
		 			
		 			if (in_array($value, $params['selected'])) {
		 				$options .= ' selected="selected"';
		 			}
					
		 			$options .= '>'.$alias.'</option>';					
				}
			}
		}
 		
 		// Select-Tag bei Bedarf
 		if (!$params['optionsOnly']) {
 			$select = '<select name="'.$params['name'].'"';
 			
 			if ($params['id']) $select .= ' id="'.$params['id'].'"';
 			if ($params['size']) $select .= ' size="'.$params['size'].'"';
 			if ($params['multiple']) $select .= ' multiple="multiple"';
 			if ($params['addHtml']) $select .= ' '.$params['addHtml'];
 			
 			$select .= '>';
 			
 			return $select.$options.'</select>';
 		} else {
 			return $options;
 		}
	}
/* -- Ende: neue Methoden -- */

    ////////////////////////////////////////////
    //
    // Formular Essentials: Start, Kn�pfe, Ende
    //
    ////////////////////////////////////////////
    
    public function FormStart ($name='', $action='', $method='get', $enctype='application/x-www-form-urlencoded', $add_html='') {

		if (trim($enctype) =='') {
			$enctype = 'application/x-www-form-urlencoded';
		}
        if (trim($name) == '') {
            $name = 'form'.$this->form_nr;
        }
        if (trim($action) == '') {
            //$action = SELFURL;
            $action = SELF;
        }
        
        if ($add_html) {
        	$add_html = ' '.$add_html;
        }
        
        $html = '<form name="'.$name.'" action="'.$action.'" method="'.$method.'" enctype="'.$enctype.'"'.$add_html.'>'.$this->nl;
        //$html = "<form name=\"$name\" action=\"$action\" method=\"GET\" enctype=\"$enctype\"$add_html>\n";

        return $html;
    }

    public function FormButtons () {
        $html = $this->FormReset();
        $html .= '&nbsp;&nbsp;';
        $html .= $this->FormSubmit();
        
        return $html;
    }
    
    public function FormEnd () {
    
        $this->form_nr++;
        $this->textfield_nr = 1;
        
        $html = '</form>'.$this->nl;
        return $html;
    }
    
    ////////////////////////////
    //
    // Formularelemente
    //
    ////////////////////////////
    
    // Select-Dropdown
    public function FormSelect ($name='', $values='', $titles='', $selected='', $size=1, $add_html='') {
		
		$multiple = '';
		
        if (trim($name) == '') {
            $name = 'select'.$this->select_nr;
        }

        if ($size > 1) {
            $name .= '[]';
            $multiple = ' multiple';
        } else {
        	$size = 1;
        }
        
        if (gettype($titles) != 'array') {
            $output_titles[0] = $titles;
        } else {
            $output_titles = $titles;
        }

        if (gettype($values) != 'array') {
            $output_values[0] = $values;
        } else {
            $output_values = $values;
        }
        
        // Ausgabe
        $html = '<select name="'.$name.'" size="'.$size.'"'.$multiple.' '.$add_html.'>'.$this->nl;

        foreach ($output_values as $key=>$opt) {
            // Liste erzeugen
            if ($output_titles[$key]) {
            	$html_array[$opt] .= '<option value="'.$opt.'"'.$add_html.'>'.$output_titles[$key].'</option>';
            } else {
            	$html_array[$opt] .= '<option value="'.$opt.'"'.$add_html.'>'.$opt.'</option>';
            }
        }
        
        if (in_array($selected, array_keys($html_array))) {
        	$html_array[$selected] = str_replace ('<option', '<option selected="selected"', $html_array[$selected]);
        }

        $html .= implode($this->nl,$html_array);
        $html .= '</select>'.$this->nl;

        $this->select_nr++;
        return $html;
    }
    
    // Input-Feld
    public function FormInput ($name='', $value='', $size=40, $maxlength='', $add_html='') {

        if (trim($name) == '' || !$name) {
            $name = 'string'.$this->form_nr.$this->textfield_nr++;
        }

        // Erstmal nur Anf�hrungszeichen umwandeln
        $value = $this->cmt_htmlentities($value);
        
        if ($maxlength) {
        	$maxlength = ' maxlength="'.$maxlength.'"';
        } else {
        	$maxlength = '';
        }
        
        if ($size) $size = ' size="'.$size.'"';

        if ($add_html) $add_html = ' '.$add_html;

        $html = '<input type="text" name="'.$name.'" value="'.$value.'"'.$size.$maxlength.$add_html.'/>'.$this->nl;
        return $html;
    }

    // Upload-Feld
    public function FormUpload ($name='', $add_html='') {

        if (trim($name) == '' || !$name) {
            $name = 'upload'.$this->form_nr.$this->textfield_nr++;
        }
        
       if ($add_html) $add_html = ' '.$add_html;
        
        $html = '<input type="file" name="'.$name.'"'.$size.$add_html.'/>'.$this->nl;
        return $html;
    }
    
    // Hidden-Feld
    public function FormHidden ($name='', $value='') {

        if (gettype($name) != 'array') {
            $html = '<input type="hidden" name="'.$name.'" value="'.$value.'"/>';
        } else {
            foreach ($name as $key=>$value) {
                $html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
            }
        }
        return $html;
    }

    // Textarea
    public function FormTextarea ($name='', $value='', $cols=60, $rows=8, $add_html='') {
		
		$cols = ' cols="'.$cols.'"';
		$rows = ' rows="'.$rows.'"';
		
        if (trim($name) == '' || !$name) {
            $name = 'text'.$this->form_nr.$this->textarea_nr++;
        }

        $value = $this->cmt_htmlentities($value);
        
        if ($add_html) {
        	$add_html = ' '.$add_html;
        	
        	if (stristr($add_html, 'width')) {
        		unset($cols);
        	}
        	if (stristr($add_html, 'height')) {
        		unset($rows);
        	}
        }

        $html = '<textarea name="'.$name.'"'.$cols.$rows.$add_html.'>'.$value.'</textarea>'.$this->nl;
        return $html;
    }

    // Checkbox
    public function FormCheckbox ($name='', $checked=0, $value='', $add_html='') {

        if (trim($name) == '' || !$name) {
            $name = 'checkbox'.$this->form_nr.$this->checkbox_nr++;
        }
        
        if ($checked) {
            $html_checked = ' checked="checked"';
        }
        
		$value = trim($value);
		if ($value || $value===0) {
			$value = ' value="'.$value.'"';
		} else $value = ' value="1"';
		
        $html = '<input type="checkbox" name="'.$name.'"'.$value.$html_checked.$add_html.'/>';
        return $html;
    }
    
    // Radio-Button
    public function FormRadio ($name='', $value='' , $checked=0, $addHtml='') {

        if (trim($name) == '' || !$name) {
            $name = 'radio'.$this->form_nr.$this->radio_nr++;
        }

        if ($checked) {
            $html_checked = ' checked="checked"';
        }
        
        if ($addHtml) $addHTML = ' '.$addHtml;

        $html = '<input type="radio" name="'.$name.'" value="'.$value.'"'.$html_checked.$addHtml.'/>';
        return $html;

    }
    
    // Submit-Knopf
    public function FormSubmit ($value='Senden', $name='', $submit_grafik='', $add_html='') {

        if (!$name) {
        	$name = 'submit'.$this->form_nr;
        }
        
        if ($add_html) {
        	$add_html = ' '.$add_html;
        }
        if (!$submit_grafik) {
            $html = '<input type="submit" value="'.$value.'" name="'.$name.'"'.$add_html.'/>';
        } else {
            $html = '<input src="'.$submit_grafik.'" name="'.$name.'" value="'.$value.'" type="image"'.$add_html.'/>';
        }
        return $html;
    }

    // Reset-Knopf
    public function FormReset ($reset='Zur&uuml;cksetzen', $add_html='') {
        if ($add_html) {
        	$add_html = ' '.$add_html;
        }
        $html = '<input type="reset" value="'.$reset.'"'.$add_html.'/>';
        return $html;
    }

    ///////////////////////////////////////////////////////////
    // Zeichenumwandlungsfunktionen
    ///////////////////////////////////////////////////////////
	public function cmt_htmlentities ($string) {
        $convert_special_chars['"'] = '&quot;';
        $convert_special_chars['<'] = '&lt;';
        $convert_special_chars['>'] = '&gt;';
        $convert_special_chars['&'] = '&amp;';	// TODO: Das hier aber nochmal genau testen!!!
        $convert_special_chars['\\'] = '&bsol;';
        // $convert_special_chars['�'] = '&iexcl;';

        return strtr ($string, $convert_special_chars);
	}
    
    ///////////////////////////////////////////////////////////
    // Weitere Funktionen zur Darstellung von Datenbankdaten
    ///////////////////////////////////////////////////////////
    
    public function FormMakeEnum ($name='', $value='', $size=40) {
        $formdata = $this->FormInput($name, $value, $size);
        return $formdata;
    }

    public function FormMakeSet ($name='', $value='', $size=40) {
        $formdata = $this->FormInput($name, $value, $size);
        return $formdata;
    }

	/*
	 * private function arrayHtmlEntities()
	 * Wendet htmlentites() auf ein Array an
	 * 
	 * @param array $a Array mit Werten, die umgewandelt werden sollen
	 * @param string $charset Zeichensatz, optonal
	 */
	private function arrayHtmlEntities($a, $charSet=CHARSET) {
		if (!is_array($a)) return $a;
		foreach ($a as $k => $v) {
			$a[$k] = htmlentities($v, ENT_COMPAT, $charSet);
		}
		return $a;
	}

}
?>
