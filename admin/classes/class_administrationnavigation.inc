<?php
/**
 * class AdministrationNavigation
 * Class provides the methods to create the main navigation.
 * 
 * @version 2016-02-18
 * @author J.Hahn <info@contentomat.de>
 */

namespace Contentomat;

Class AdministrationNavigation {
	
	protected $cmt;
	protected $session;
	protected $applicationHandler;
	protected $user;
	protected $parser;
	protected $db;
	protected $groupTemplate;
	protected $itemTemplate;
	protected $selectedApplicationID;
	
	public function __construct() {

		$this->selectedApplicationID = CMT_NAVAPPID;
		
	    $this->cmt = Contentomat::getContentomat();
		$this->session = $this->cmt->getSession();
		$this->applicationHandler = new applicationHandler();
	       
	    $this->user = new User(SID);
	    $this->parser = new CMTParser();
		$this->db = new DBCex();
	
		$this->groupTemplate = $this->parser->getTemplate('administration/cmt_navigation_group.tpl');
		$this->itemTemplate = $this->parser->getTemplate('administration/cmt_navigation_item.tpl');
		
		// pass global variables to parser
		$this->parser->setParserVar('appLauncher', 'cmt_applauncher.php?sid='.SID.'&amp;');
	}
	
	/**
	 * public function setGroupTemplatePath()
	 * Sets the path for the group template
	 * 
	 * @param string $path Path of the template file
	 * @return boolean
	 */
	public function setGroupTemplatePath($path) {
		
		if (is_file($path)) {
			$this->groupTemplate = file_get_contents($path);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function setApplicationTemplatePath()
	 * Sets the path for a single navigation element template.
	 * 
	 * @param string $path Path of the template file
	 * @return boolean
	 */
	public function setApplicationTemplatePath($path) {
		
		if (is_file($path)) {
			$this->itemTemplate = file_get_contents($path);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * public function createNavigation()
	 * Main method: creates the navigation (HTML)
	 * 
	 * @return Ambigous <string, string, mixed>
	 */
	public function createNavigation() {
	    
	    // 1. Gruppen auslesen
		$groups = array();
		$items = array();
	
	    $items = $this->applicationHandler->getApplications(array(
	    	'groups' => true,
	    	'isVisible' => 1
	    ));

	    // 2. Elemente auslesen und parsen
	    foreach ($items as $g) {
	    	
	    	$groupIsEmpty = true;
	    	$groupSelected = false;

	    	foreach ($g['cmtApplications'] as $r) {
	
	    		// Zugriffsrechte prüfen
		    	if (!$this->user->checkUserAccessRight($r['id'])) {
		    		continue;	
		    	}
		    	
				// Da ist was drin in der Gruppe!
				$groupIsEmpty = false;
				$itemSelected = false;
						
				// Default-Einstellungen holen, sofern es welche gibt.
				if ($r['cmt_type'] == 'table') {
					$settingsPath = 'app_showtable/';
				} else {
					$a = explode('.', $r['cmt_include']);
					$settingsPath = $a[0].'/';
					unset ($a);
				}
				
				$itemSettings = $r['cmt_tablesettings'];
				
				if (!isset($itemSettings['icon'])) {
					$itemSettings['icon'] = 'default';
				}
				
				switch ($itemSettings['icon']) {
					case 'otherIcon':
				    	$itemIcon = CMT_TEMPLATE.$itemSettings['iconPath'];
				    	break;
				
					case 'none':
						$itemIcon = '';
						unset ($itemSettings['iconPath']);
						break;
					default:
						if (isset($itemSettings['iconPath']) && is_file(CMT_TEMPLATE.$settingsPath.$itemSettings['iconPath'])) {
							$itemIcon = CMT_TEMPLATE.$settingsPath.$itemSettings['iconPath'];
						} else {
							$itemIcon = CMT_TEMPLATE.'general/img/'.str_replace('table', $r['cmt_type'], 'cmt_defaulttableicon_16px.png');
						}
						$itemSettings['icon'] = 'default';
						break;
				}
				
				// Das 16px große Icon anzeigen
				if (!strstr($itemIcon, 'cmt_default')) {
					$iconParts = explode('.', (basename($itemIcon)));
					$itemIcon = dirname($itemIcon).'/'.array_shift($iconParts).'_16px.'.implode('.', $iconParts);
				}
				
				if (!is_file($itemIcon)) {
					$itemIcon = CMT_TEMPLATE.'general/img/'.str_replace('table', $r['cmt_type'], 'cmt_defaulttableicon_16px.png');
				}
				
				if ($r['id'] == $this->selectedApplicationID) {
					$itemSelected = true;
					$groupSelected = true;
					
				} else {
					$itemSelected = false;
				}
				
				// generate query variables
				$queryVars = '';
				if ($r['cmt_queryvars']) {
					$queryVars = str_replace(array("\n", "\r"), array('&amp;', ''), $r['cmt_queryvars']);
				}
				
				$parserVars = array(
					'itemIcon' => $itemIcon,
					'itemName' => $r['cmt_showname'],
					'itemId' => $r['id'],
					'itemSelected' => $itemSelected,
					'groupSelected' => $groupSelected,
					'queryVars' => $queryVars
				);
				
				$this->parser->setMultipleParserVars($parserVars);
				$groupHtml .= $this->parser->parse($this->itemTemplate);
	    	}
	    	
	    	// Falls was in der Gruppe drin ist, dann anzeigen
	    	if (!$groupIsEmpty) {
	    		
	    		$groupSettings = $g['cmt_groupsettings'];
	    		
	    		if (!is_array($groupSettings)) {
	    			$groupSettings = array();
	    		}
	    		
				// Icon suchen
			    switch ($groupSettings['icon']) {
			    	case 'otherIcon':
			    		$groupIcon = CMT_TEMPLATE.$groupSettings['iconPath'];
			    		break;
			    		
			    	case 'none':
			    		$groupIcon = '';
			    		break;
			    		
			    	default:
			    		if ($groupSettings['iconPath']) {
			    			$groupIcon = CMT_TEMPLATE.$groupSettings['iconPath'];
			    		} else {
			    			$groupIcon = CMT_TEMPLATE.'general/img/cmt_defaultgroupicon_32px.png';
			    		}
			    		$groupSettings['icon'] = 'default';
			    		break;
			    }
			    
			    // Das 32px große Icon auswählen
			    if (!strstr($groupIcon, 'cmt_defaultgroupicon_32px.png') && $groupIcon != '') {
			    	$iconParts = explode('.', (basename($groupIcon)));
			    	$groupIcon = dirname($groupIcon).'/'.array_shift($iconParts).'_32px.'.implode('.', $iconParts);
			    }
			    
			    if ($groupIcon != '' && !is_file($groupIcon)) {
			    	$groupIcon = CMT_TEMPLATE.'general/img/cmt_defaultgroupicon_32px.png';
			    }
			    
			    // Gruppentemplate parsen
			    $this->parserVars = array(
		    		'groupIcon' => $groupIcon,
					'groupName' => $g['cmt_groupname'],
					'groupId' => $g['id'],
					'groupHtml' => $groupHtml,
		    		'groupSelected' => $groupSelected
			   	);
						
			    $this->parser->SetMultipleParserVars($this->parserVars);
			    $navigation .= $this->parser->parse($this->groupTemplate);
			    
			    unset($groupHtml);
	    	}
	    }
	    $this->parser->setParserVar('cmtNavigationGroups', $navigation);	    
	    return $this->parser->parseTemplate('administration/cmt_navigation.tpl');
	}
}
?>