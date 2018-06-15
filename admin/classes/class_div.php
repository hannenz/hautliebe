<?php
/**
 * 
 * class_div.php
 * HTML-/DIV-Klasse
 * 
 * Stellt verschiedene Methoden zur Erzeugung von DIV-Container
 * zur Verfügung. Gibt HTML-Tags zurück.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2011-02-16
 * 
 */

namespace Contentomat;

class Div {
	
//	var $form;
	var $counter;
	var $alter_color;
	var $act_layer;
	var $close_layer;
	var $button_img;
	var $nl;
	
	// Konstruktor
	function Div () {
//		$this->form = new Form(); -> wozu das????
		$this->divcounter = 0;
		$this->alternate = 0;
		$this->act_layer = 0;
		$this->close_layer = false;
		$this->nl = chr(10);
		return;	
	}

    function DivStart ($class='', $id='', $name='', $add_html= '') {
    	$html = '<div';
    	$attributes = array ('name', 'class', 'id', 'add_html');
    	foreach ($attributes as $a) {
    		eval ('$attribute = $'.$a.';');
    		if ($attribute) {
    			$html .= ' '.$a.'="'.$attribute.'"';
    		}
    	}
    	$html .= '>'.$this->nl;
        return $html;

    }
    
    function DivEnd () {
        return '</div>'.$this->nl;
    }
	
	function DivMakeDiv ($content='', $class='', $id='', $name='', $add_html='') {
		$this->divcounter++;
		$html = '<div';

		if (preg_match ('/_alternate$/', $class)) {
			$html .= ' class="'.$class.($this->alternate%2).'"';
			$this->alternate++;
		} else if ($class) {
			$html .= ' class="'.$class.'"';
			$this->alternate = 0;
		}
		if ($id) {
			$html .= ' id="'.$id.'"';
		}
/* Weg damit: Ist proprietär und nicht W3C konform!
		if (!$name) {
			$name = 'div'.$this->divcounter;
		}
		$html .=  ' name="'.$name.'"';
*/				
		if ($add_html) {
			$html .= ' '.$add_html;
		}
		$html .= '>'.$content.'</div>'.$this->nl;
		return $html;
	}
		
		
    function DivMakeLayer ($head, $content, $class='layer', $id='', $status=1, $name='', $add_html= '', $button_img='') {
		
		if (!$id) {
			$id = 'layer_'.$this->divcounter;
		}
		
		if (!$button_img) {
			$button_img = $class.'_button';
		}
		
		$change_link = '<a class="'.$class.'_headlink" href="Javascript:void(0);" onClick="change_one_layer(\''.$id.'_content\'); change_layer_icon (\''.$id.'_button\', \''.CMT_TEMPLATE.'img/'.$button_img.'_\'); this.blur();">';
		
		$html = '<div class="'.$class.'_headrow" id="'.$id.'_headrow">';
		$html .= '<table width="100%"><tr><td width="100%" class="'.$class.'_title">'.$change_link.$head.'</a></td>';
		$html .= '<td class="'.$class.'_button">'.$change_link.'<img src="'.CMT_TEMPLATE.'img/'.$button_img.'_close.gif" border="0" id="'.$id.'_button"></a></td></tr></table>';
		$html .= '</div>';
		$html .= '<div class="'.$class.'_content" id="'.$id.'_content">'.$content.'</div>'; 
		
		$this->divcounter++;
        return $html;
    }
	
	/**
	 * function startLayer
	 * Liefert Anfangs-HTML-Quelltext für einen Klapplayer
	 * 
	 * @param $params array Alle folgenden Parameter werden in einem Array übergeben
	 * @param 'head' string Titelzeile des Klapplayers
	 * @param 'class' string CSS-Klasse des Layers
	 * @param 'id' string Layer-ID
	 * @param 'status' number 1 = LAyer anfangs geöffnet, 0 = geschlossen
	 * @param 'addHtml' string Zusätzliches HTML
	 * @param 'buttonImage' string Adresse der Grafik für den Auf-und-Zuklappkknopf
	 */
	function startLayer ($p) {
		$default = array ('class' => 'layer',
						  'status' => 1,
						  'id' => 'layer_'.$this->divcounter,
						  'buttonImage' => 'general/img/icon_layer_close.gif',
						  'status' => 1);
		extract(array_merge($default, $p));
/*
		if (!$id) {
			$id = 'layer_'.$this->divcounter;
		}
		
		if (!$buttonImage) {
			$buttonImage = 'general/img/icon_layer_close.gif';
		}
*/
		if ($status != 1) {
			$this->close_layer = true;
			$this->button_img = $buttonImage;
		}

		
		$changeLink = '<a class="'.$class.'Link" href="Javascript:void(0);" onClick="changeLayer(\''.$id.'_content\', \''.$id.'_button\'); this.blur();">';
		
		$html = '<div class="'.$class.'Handle" id="'.$id.'Handle">';
		$html .= '<div class="'.$class.'Button">'.$changeLink.'<img src="'.CMT_TEMPLATE.$buttonImage.'" class="imageLinked" id="'.$id.'_button"></a></div>';
		$html .= '<div class="'.$class.'Title">'.$changeLink.$head.'</a></div>';
		$html .= '</div>';
		$html .= '<div class="'.$class.'Content" id="'.$id.'_content">'; 
		
		$this->act_layer = $id;
		$this->divcounter++;
        return $html;
		
	}

// outdated
    function DivStartLayer ($head, $class='layer', $id='', $status=1, $name='', $add_html= '', $button_img='') {
		
		if (!$id) {
			$id = 'layer_'.$this->divcounter;
		}
		
		if (!$button_img) {
			$button_img = $class.'_button';
		}

		if ($status != 1) {
			$this->close_layer = true;
			$this->button_img = $button_img;
		}

		
		$change_link = '<a class="'.$class.'_link" href="Javascript:void(0);" onClick="change_one_layer(\''.$id.'_content\'); change_layer_icon (\''.$id.'_button\', \''.CMT_TEMPLATE.'img/'.$button_img.'_\'); this.blur();">';
		
		$html = '<div class="'.$class.'_headrow" id="'.$id.'_headrow">';
		$html .= '<table width="100%"><tr>';
		$html .= '<td class="'.$class.'_button">'.$change_link.'<img src="'.CMT_TEMPLATE.'img/'.$button_img.'_close.gif" border="0" id="'.$id.'_button"></a></td>';
		$html .= '<td width="100%" class="'.$class.'_title">'.$change_link.$head.'</a></td>';
		$html .= '</tr></table>';
		$html .= '</div>';
		$html .= '<div class="'.$class.'_content" id="'.$id.'_content">'; 
		
		$this->act_layer = $id;
		$this->divcounter++;
        return $html;
    }
    
    function DivMakeLayerContent ($content) {
    	return '<div class="'.$class.'_content" id="'.$this->act_layer.'_content">'.$content.'</div>';
    }
    
    function endLayer() {
    	return $this->DivEndLayer();
    }
    
    function DivEndLayer () {
    	$html = '</div>';
    	if ($this->close_layer == true) {
    		$html .= '<script type="text/javascript">changeLayer(\''.$this->act_layer.'_content\', \''.$this->act_layer.'_button\');</script>';
    		$this->act_layer = false;
    		$this->close_layer = false;
    		$this->button_img = false;
    	}
    	return $html;
    }

	function DivStartAlternate ($start_value=0) {
		$this->alternate = $start_value;
	}
	
	function makeSliders ($content, $is_active=1, $add_html='', $type='service') {

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
		
		$html = '<div class="sliderRow">';
		$html = '<div class="sliderBackground">';
		$cells = count($content);
		
		$act_cell = 1;
		
		foreach ($output_html as $cell=>$cell_content) {
			/*
			if ($is_active == $act_cell) {
				$active_html .= 'Active';
			} else {
				$active_html = '';
			}
			*/
			/*
			if ($act_cell == $is_active - 1) {
				$class = 'class="slider'.ucfirst($type).'Pre"';
				$style = '';
			} else if ($act_cell == $is_active){
				$class = 'class="slider'.ucfirst($type).'Active"';
				$style = ' style="background-image: url('.CMT_TEMPLATE.'img/table_slider_seperator_'.$type.'.gif);"';
			} else {
				$class = 'class="slider'.ucfirst($type).'"';
				$style = ' style="background-image: url('.CMT_TEMPLATE.'img/table_slider_seperator_'.$type.'.gif);"';
			}
			*/
			if ($act_cell == $is_active - 1) {
				$class = 'class="sliderPreActive"';
				$linkClass= 'class="sliderNotActiveLink"';
			} else if ($act_cell == $is_active) {
				$class = 'class="sliderActive"';
				$linkClass= 'class="sliderActiveLink"';
			} else{
				$class = 'class="sliderNotActive"';
				$linkClass= 'class="sliderNotActiveLink"';
			}

			//$html .= trim('<div '.$class.' '.$style.' '.$output_add[$cell]);
			$cell_content = str_replace('<a href', '<a '.$linkClass.' href', $cell_content);
			
			$html .= trim('<div '.$class.$output_add[$cell]);
			$html .= '>'.$cell_content.'</div>';
			$act_cell++;
		}
		$html .= '<div class="clear"></div></div>';
		
		return $html;
		
	}

}